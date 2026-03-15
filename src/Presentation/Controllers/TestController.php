<?php

namespace TouristAttractionFinder\Presentation\Controllers;

use TouristAttractionFinder\Application\DTOs\RegisterUserRequest;
use TouristAttractionFinder\Application\DTOs\LoginUserRequest;
use TouristAttractionFinder\Application\UseCases\RegisterUserUseCase;
use TouristAttractionFinder\Application\UseCases\LoginUserUseCase;
use TouristAttractionFinder\Domain\Exceptions\UserAlreadyExistsException;
use TouristAttractionFinder\Domain\Exceptions\InvalidCredentialsException;
use TouristAttractionFinder\Infrastructure\Repositories\MySQLUserRepository;
use TouristAttractionFinder\Infrastructure\Services\JWTService;
use TouristAttractionFinder\Presentation\Middleware\ValidationMiddleware;

class TestController
{
    private RegisterUserUseCase $registerUseCase;
    private LoginUserUseCase $loginUseCase;

    public function __construct()
    {
        $userRepository = new MySQLUserRepository();
        $jwtService = new JWTService();

        $this->registerUseCase = new RegisterUserUseCase($userRepository);
        $this->loginUseCase = new LoginUserUseCase($userRepository, $jwtService);
    }

    public function runFullTest(): array
    {
        $results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'test_suite' => 'Full API Test Suite',
            'version' => '1.0.0',
            'tests' => []
        ];

        // Test 1: Registration
        $results['tests']['registration'] = $this->testRegistration();

        // Test 2: Login
        $results['tests']['login'] = $this->testLogin();

        // Test 3: Duplicate Registration
        $results['tests']['duplicate_registration'] = $this->testDuplicateRegistration();

        // Test 4: Invalid Login
        $results['tests']['invalid_login'] = $this->testInvalidLogin();

        // Test 5: JWT Token Validation
        $results['tests']['jwt_validation'] = $this->testJWTValidation();

        // Calculate overall result
        $passedTests = 0;
        $totalTests = count($results['tests']);

        foreach ($results['tests'] as $test) {
            if ($test['status'] === 'passed') {
                $passedTests++;
            }
        }

        $results['summary'] = [
            'total_tests' => $totalTests,
            'passed' => $passedTests,
            'failed' => $totalTests - $passedTests,
            'success_rate' => round(($passedTests / $totalTests) * 100, 2) . '%',
            'overall_status' => $passedTests === $totalTests ? 'all_passed' : 'some_failed'
        ];

        return $results;
    }

    private function testRegistration(): array
    {
        try {
            $testEmail = 'test-' . time() . '@example.com';
            $testData = [
                'email' => $testEmail,
                'password' => 'testpassword123',
                'name' => 'Test User'
            ];

            // Validate input
            ValidationMiddleware::validate($testData, [
                'email' => \Respect\Validation\Validator::email(),
                'password' => \Respect\Validation\Validator::stringType()->length(8, null),
                'name' => \Respect\Validation\Validator::stringType()->length(2, 100)
            ]);

            $request = new RegisterUserRequest(
                $testData['email'],
                $testData['password'],
                $testData['name']
            );

            $response = $this->registerUseCase->execute($request);

            return [
                'status' => 'passed',
                'message' => 'User registration successful',
                'details' => [
                    'user_id' => $response->id,
                    'email' => $response->email,
                    'name' => $response->name
                ]
            ];
        } catch (UserAlreadyExistsException $e) {
            return [
                'status' => 'failed',
                'message' => 'User already exists',
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Registration test failed',
                'error' => $e->getMessage()
            ];
        }
    }

    private function testLogin(): array
    {
        try {
            $testEmail = 'test-' . time() . '@example.com';
            $testData = [
                'email' => $testEmail,
                'password' => 'testpassword123'
            ];

            // First register the user
            $registerRequest = new RegisterUserRequest(
                $testData['email'],
                $testData['password'],
                'Test User for Login'
            );

            $this->registerUseCase->execute($registerRequest);

            // Then try to login
            $loginRequest = new LoginUserRequest(
                $testData['email'],
                $testData['password']
            );

            $response = $this->loginUseCase->execute($loginRequest);

            return [
                'status' => 'passed',
                'message' => 'User login successful',
                'details' => [
                    'user_id' => $response->id,
                    'email' => $response->email,
                    'name' => $response->name,
                    'token_length' => strlen($response->token),
                    'expires_in' => $response->expiresIn
                ]
            ];
        } catch (InvalidCredentialsException $e) {
            return [
                'status' => 'failed',
                'message' => 'Invalid credentials',
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Login test failed',
                'error' => $e->getMessage()
            ];
        }
    }

    private function testDuplicateRegistration(): array
    {
        try {
            $testEmail = 'duplicate-test@example.com';

            // First registration
            $request1 = new RegisterUserRequest(
                $testEmail,
                'password123',
                'Duplicate User 1'
            );
            $this->registerUseCase->execute($request1);

            // Second registration with same email (should fail)
            $request2 = new RegisterUserRequest(
                $testEmail,
                'password456',
                'Duplicate User 2'
            );
            $this->registerUseCase->execute($request2);

            return [
                'status' => 'failed',
                'message' => 'Duplicate registration should have failed',
                'error' => 'Expected UserAlreadyExistsException was not thrown'
            ];
        } catch (UserAlreadyExistsException $e) {
            return [
                'status' => 'passed',
                'message' => 'Duplicate registration correctly rejected',
                'details' => [
                    'email' => $testEmail,
                    'error_message' => $e->getMessage()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Duplicate registration test failed unexpectedly',
                'error' => $e->getMessage()
            ];
        }
    }

    private function testInvalidLogin(): array
    {
        try {
            $loginRequest = new LoginUserRequest(
                'nonexistent@example.com',
                'wrongpassword'
            );

            $this->loginUseCase->execute($loginRequest);

            return [
                'status' => 'failed',
                'message' => 'Invalid login should have failed',
                'error' => 'Expected InvalidCredentialsException was not thrown'
            ];
        } catch (InvalidCredentialsException $e) {
            return [
                'status' => 'passed',
                'message' => 'Invalid login correctly rejected',
                'details' => [
                    'error_message' => $e->getMessage()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Invalid login test failed unexpectedly',
                'error' => $e->getMessage()
            ];
        }
    }

    private function testJWTValidation(): array
    {
        try {
            $jwtService = new JWTService();

            // Generate a test token
            $testToken = $jwtService->generateToken(1, 'test@example.com');

            // Validate the token
            $decoded = $jwtService->validateToken($testToken);

            // Check if token is expired
            $isExpired = $jwtService->isTokenExpired($testToken);

            return [
                'status' => 'passed',
                'message' => 'JWT validation successful',
                'details' => [
                    'token_length' => strlen($testToken),
                    'decoded_user_id' => $decoded['user_id'],
                    'decoded_email' => $decoded['email'],
                    'is_expired' => $isExpired,
                    'expiration_time' => $jwtService->getTokenExpiration()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'JWT validation failed',
                'error' => $e->getMessage()
            ];
        }
    }

    public function testEndpoint(string $method, string $path, array $data = []): array
    {
        $startTime = microtime(true);

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://localhost$path");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);

            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $executionTime = microtime(true) - $startTime;

            curl_close($ch);

            return [
                'method' => $method,
                'path' => $path,
                'status_code' => $httpCode,
                'execution_time' => round($executionTime * 1000, 2) . 'ms',
                'response' => json_decode($response, true) ?: $response,
                'success' => $httpCode >= 200 && $httpCode < 300
            ];
        } catch (\Exception $e) {
            return [
                'method' => $method,
                'path' => $path,
                'status_code' => 0,
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
                'error' => $e->getMessage(),
                'success' => false
            ];
        }
    }
}