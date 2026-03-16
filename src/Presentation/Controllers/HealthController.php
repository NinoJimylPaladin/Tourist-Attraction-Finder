<?php

namespace TouristAttractionFinder\Presentation\Controllers;

use TouristAttractionFinder\Infrastructure\Config\DatabaseConfig;
use TouristAttractionFinder\Infrastructure\Services\JWTService;

class HealthController
{
    public function check(): array
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'checks' => []
        ];

        // Check database connection
        try {
            $db = DatabaseConfig::getConnection();
            $db->query('SELECT 1');
            $status['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $status['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
            $status['status'] = 'degraded';
        }

        // Check JWT service
        try {
            $jwtService = new JWTService();
            $testToken = $jwtService->generateToken(1, 'test@example.com');
            $jwtService->validateToken($testToken);
            $status['checks']['jwt'] = [
                'status' => 'ok',
                'message' => 'JWT service working'
            ];
        } catch (\Exception $e) {
            $status['checks']['jwt'] = [
                'status' => 'error',
                'message' => 'JWT service failed: ' . $e->getMessage()
            ];
            $status['status'] = 'degraded';
        }

        // Check environment configuration
        $envChecks = [
            'DB_HOST' => $_ENV['DB_HOST'] ?? 'not set',
            'DB_NAME' => $_ENV['DB_NAME'] ?? 'not set',
            'JWT_SECRET' => !empty($_ENV['JWT_SECRET']) ? 'set' : 'not set'
        ];

        $status['checks']['environment'] = [
            'status' => 'ok',
            'message' => 'Environment variables configured',
            'details' => $envChecks
        ];

        // Set appropriate HTTP status code
        if ($status['status'] === 'healthy') {
            http_response_code(200);
        } else {
            http_response_code(503);
        }

        return $status;
    }

    public function info(): array
    {
        return [
            'api_name' => 'Tourist Attraction Finder API',
            'version' => '1.0.0',
            'description' => 'API for managing tourist attractions in Zamboanga Del Norte',
            'author' => 'Tourist Attraction Finder Team',
            'architecture' => 'Clean Architecture',
            'framework' => 'Custom PHP',
            'database' => 'MySQL',
            'authentication' => 'JWT',
            'endpoints' => [
                'GET /api/health' => 'Health check endpoint',
                'GET /api/info' => 'API information',
                'POST /api/auth/register' => 'User registration',
                'POST /api/auth/login' => 'User authentication',
                'GET /api/status' => 'API status'
            ],
            'dependencies' => [
                'vlucas/phpdotenv' => 'Environment management',
                'firebase/php-jwt' => 'JWT authentication',
                'respect/validation' => 'Input validation'
            ],
            'documentation' => [
                'OpenAPI' => 'Available at /api/docs',
                'Postman' => 'Collection available on request'
            ]
        ];
    }

    public function metrics(): array
    {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => [
                'php_version' => phpversion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
                'memory_usage' => [
                    'current' => memory_get_usage(true),
                    'peak' => memory_get_peak_usage(true),
                    'limit' => ini_get('memory_limit')
                ],
                'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
            ],
            'request_info' => [
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ],
            'environment' => [
                'mode' => $_ENV['APP_ENV'] ?? 'development',
                'debug' => (bool)$_ENV['APP_DEBUG'] ?? true
            ]
        ];
    }
}