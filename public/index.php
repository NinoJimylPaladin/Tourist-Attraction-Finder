<?php

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers for CORS and JSON responses
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

use TouristAttractionFinder\Presentation\Router;
use TouristAttractionFinder\Presentation\Controllers\AuthController;

// Initialize router
$router = new Router();
$authController = new AuthController();

// Diagnostic endpoint to test routing
$router->get('/api/status', function() {
    return [
        'status' => 'API is running',
        'version' => '1.0',
        'endpoints' => [
            'POST /api/auth/register' => 'Register a new user',
            'POST /api/auth/login' => 'Login with credentials'
        ]
    ];
});

// Define routes
$router->post('/api/auth/register', function($data) use ($authController) {
    return $authController->register($data);
});

$router->post('/api/auth/login', function($data) use ($authController) {
    return $authController->login($data);
});

// Handle 404 for other routes
$router->get('/api/auth/register', function() {
    http_response_code(405);
    return ['message' => 'Method not allowed'];
});

$router->get('/api/auth/login', function() {
    http_response_code(405);
    return ['message' => 'Method not allowed'];
});

// Dispatch the request
$router->dispatch();