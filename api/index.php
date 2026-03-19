<?php
// API Entry Point
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

// Get the requested path
$path = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

// Remove base path from request URI
if ($base_path !== '/') {
  $path = str_replace($base_path, '', $path);
}

// Remove query string
$path = parse_url($path, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Split path into segments
$segments = explode('/', $path);

// Route the request
if (count($segments) >= 2) {
  $controller = $segments[0];
  $action = $segments[1];
  $params = array_slice($segments, 2);

  // Include the appropriate controller
  $controller_file = __DIR__ . "/controllers/{$controller}.php";

  if (file_exists($controller_file)) {
    require_once $controller_file;

    // Call the appropriate action
    if (function_exists($action)) {
      call_user_func_array($action, $params);
    } else {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => 'Action not found']);
    }
  } else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Controller not found']);
  }
} else {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
