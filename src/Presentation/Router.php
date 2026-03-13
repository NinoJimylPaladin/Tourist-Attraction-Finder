<?php

namespace TouristAttractionFinder\Presentation;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    private function addRoute(string $method, string $path, callable $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        // Parse the path and remove query string
        $path = parse_url($uri, PHP_URL_PATH) ?? '';

        // Normalize the path - remove trailing slashes (except for root)
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        // Remove the web root if it exists (for installations in subdirectories)
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        if ($scriptName !== '/' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }

        // Ensure path starts with /
        if (empty($path)) {
            $path = '/';
        }

        // Try to match routes
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($path, $route['path'])) {
                $this->callCallback($route['callback']);
                return;
            }
        }

        // No route found - provide debugging info
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found',
            'requested_path' => $path,
            'requested_method' => $method,
            'available_routes' => array_map(function($route) {
                return $route['method'] . ' ' . $route['path'];
            }, $this->routes),
            'data' => null
        ]);
    }

    private function matchPath(string $requestPath, string $routePath): bool
    {
        // Simple path matching - can be enhanced for parameters
        return $requestPath === $routePath;
    }

    private function callCallback(callable $callback): void
    {
        try {
            // Get request body for POST requests
            $input = file_get_contents('php://input');
            $data = $input ? json_decode($input, true) : [];

            // Set global request data
            $_REQUEST_DATA = $data;

            $result = $callback($data);

            if ($result !== null) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Request successful',
                    'data' => $result
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }
}