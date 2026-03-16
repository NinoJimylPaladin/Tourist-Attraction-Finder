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
use TouristAttractionFinder\Presentation\Controllers\HealthController;
use TouristAttractionFinder\Presentation\Controllers\TestController;

// Initialize router and controllers
$router = new Router();
$authController = new AuthController();
$healthController = new HealthController();
$testController = new TestController();

// API Information and Health Endpoints
$router->get('/api/status', function() use ($healthController) {
    return $healthController->info();
});

$router->get('/api/health', function() use ($healthController) {
    return $healthController->check();
});

$router->get('/api/info', function() use ($healthController) {
    return $healthController->info();
});

$router->get('/api/metrics', function() use ($healthController) {
    return $healthController->metrics();
});

// Authentication Endpoints
$router->post('/api/auth/register', function($data) use ($authController) {
    return $authController->register($data);
});

$router->post('/api/auth/login', function($data) use ($authController) {
    return $authController->login($data);
});

// Test Endpoints
$router->get('/api/test/full', function() use ($testController) {
    return $testController->runFullTest();
});

$router->post('/api/test/endpoint', function($data) use ($testController) {
    if (!isset($data['method']) || !isset($data['path'])) {
        http_response_code(400);
        return [
            'success' => false,
            'message' => 'Method and path are required',
            'data' => null
        ];
    }

    return $testController->testEndpoint($data['method'], $data['path'], $data['data'] ?? []);
});

// API Documentation Endpoint
$router->get('/api/docs', function() {
    return [
        'api_name' => 'Tourist Attraction Finder API',
        'version' => '1.0.0',
        'description' => 'Clean Architecture PHP API for managing tourist attractions',
        'base_url' => 'http://localhost/api',
        'endpoints' => [
            'Health & Information' => [
                'GET /api/status' => [
                    'description' => 'API status and basic information',
                    'response' => ['status' => 'string', 'version' => 'string', 'endpoints' => 'array']
                ],
                'GET /api/health' => [
                    'description' => 'Comprehensive health check',
                    'response' => ['status' => 'string', 'checks' => 'array', 'timestamp' => 'string']
                ],
                'GET /api/info' => [
                    'description' => 'Detailed API information',
                    'response' => ['api_name' => 'string', 'version' => 'string', 'architecture' => 'string']
                ],
                'GET /api/metrics' => [
                    'description' => 'Server metrics and performance data',
                    'response' => ['server_info' => 'array', 'request_info' => 'array', 'timestamp' => 'string']
                ]
            ],
            'Authentication' => [
                'POST /api/auth/register' => [
                    'description' => 'Register a new user',
                    'request' => ['email' => 'string', 'password' => 'string', 'name' => 'string'],
                    'response' => ['message' => 'string', 'user' => 'object'],
                    'validation' => [
                        'email' => 'valid email format',
                        'password' => 'minimum 8 characters',
                        'name' => '2-100 characters'
                    ]
                ],
                'POST /api/auth/login' => [
                    'description' => 'Authenticate user and get JWT token',
                    'request' => ['email' => 'string', 'password' => 'string'],
                    'response' => ['message' => 'string', 'user' => 'object', 'token' => 'string', 'expires_in' => 'int'],
                    'errors' => [
                        400 => 'Invalid email or password format',
                        401 => 'Invalid credentials'
                    ]
                ]
            ],
            'Testing' => [
                'GET /api/test/full' => [
                    'description' => 'Run comprehensive API test suite',
                    'response' => ['tests' => 'array', 'summary' => 'array', 'timestamp' => 'string']
                ],
                'POST /api/test/endpoint' => [
                    'description' => 'Test specific endpoint',
                    'request' => ['method' => 'string', 'path' => 'string', 'data' => 'object'],
                    'response' => ['method' => 'string', 'path' => 'string', 'status_code' => 'int', 'execution_time' => 'string']
                ]
            ]
        ],
        'authentication' => [
            'type' => 'JWT Bearer Token',
            'header' => 'Authorization: Bearer <token>',
            'token_expiration' => '3600 seconds (1 hour)',
            'protected_endpoints' => 'All endpoints except health and auth'
        ],
        'error_handling' => [
            'format' => 'JSON',
            'structure' => ['success' => 'bool', 'message' => 'string', 'data' => 'mixed'],
            'common_codes' => [
                200 => 'Success',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                500 => 'Internal Server Error',
                503 => 'Service Unavailable'
            ]
        ],
        'security' => [
            'cors' => 'Enabled for all origins',
            'input_validation' => 'Respect\Validation library',
            'password_hashing' => 'bcrypt algorithm',
            'jwt_algorithm' => 'HS256'
        ],
        'dependencies' => [
            'vlucas/phpdotenv' => 'Environment configuration',
            'firebase/php-jwt' => 'JWT token handling',
            'respect/validation' => 'Input validation'
        ]
    ];
});

// Handle 405 for GET requests to POST-only endpoints
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