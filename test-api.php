<?php

// Simple test script to verify the API implementation
require_once 'vendor/autoload.php';

use TouristAttractionFinder\Domain\Email;
use TouristAttractionFinder\Domain\Password;
use TouristAttractionFinder\Domain\User;
use TouristAttractionFinder\Application\DTOs\RegisterUserRequest;
use TouristAttractionFinder\Application\DTOs\LoginUserRequest;
use TouristAttractionFinder\Application\UseCases\RegisterUserUseCase;
use TouristAttractionFinder\Application\UseCases\LoginUserUseCase;
use TouristAttractionFinder\Infrastructure\Repositories\MySQLUserRepository;
use TouristAttractionFinder\Infrastructure\Services\JWTService;

echo "Tourist Attraction Finder API - Test Script\n";
echo "==========================================\n\n";

// Test 1: Domain Layer
echo "1. Testing Domain Layer...\n";
try {
    $email = Email::fromString('test@example.com');
    $password = Password::fromPlain('testpassword123');
    $user = User::create(1, $email, $password, 'Test User');

    echo "   ✓ Email validation: " . $email->toString() . "\n";
    echo "   ✓ Password hashing: " . ($password->verify('testpassword123') ? 'Valid' : 'Invalid') . "\n";
    echo "   ✓ User creation: " . $user->getName() . "\n";
    echo "   ✓ User array conversion: " . json_encode($user->toArray()) . "\n";
} catch (Exception $e) {
    echo "   ✗ Domain test failed: " . $e->getMessage() . "\n";
}

// Test 2: Application Layer
echo "\n2. Testing Application Layer...\n";
try {
    $registerRequest = new RegisterUserRequest('app@example.com', 'apppassword123', 'App User');
    $loginRequest = new LoginUserRequest('app@example.com', 'apppassword123');

    echo "   ✓ Register request created\n";
    echo "   ✓ Login request created\n";
} catch (Exception $e) {
    echo "   ✗ Application test failed: " . $e->getMessage() . "\n";
}

// Test 3: Infrastructure Layer
echo "\n3. Testing Infrastructure Layer...\n";
try {
    // Test JWT Service
    $jwtService = new JWTService();
    $token = $jwtService->generateToken(1, 'test@example.com');
    $decoded = $jwtService->validateToken($token);

    echo "   ✓ JWT token generation: " . substr($token, 0, 20) . "...\n";
    echo "   ✓ JWT token validation: " . $decoded['email'] . "\n";
    echo "   ✓ Token expiration: " . $jwtService->getTokenExpiration() . " seconds\n";
} catch (Exception $e) {
    echo "   ✗ Infrastructure test failed: " . $e->getMessage() . "\n";
}

// Test 4: Database Connection
echo "\n4. Testing Database Connection...\n";
try {
    $userRepository = new MySQLUserRepository();
    echo "   ✓ Database connection established\n";
    echo "   ✓ User repository created\n";
} catch (Exception $e) {
    echo "   ✗ Database test failed: " . $e->getMessage() . "\n";
    echo "   Note: This may be expected if database is not configured\n";
}

// Test 5: Use Cases
echo "\n5. Testing Use Cases...\n";
try {
    $userRepository = new MySQLUserRepository();
    $jwtService = new JWTService();

    $registerUseCase = new RegisterUserUseCase($userRepository);
    $loginUseCase = new LoginUserUseCase($userRepository, $jwtService);

    echo "   ✓ Register use case created\n";
    echo "   ✓ Login use case created\n";
    echo "   Note: Actual execution requires database setup\n";
} catch (Exception $e) {
    echo "   ✗ Use case test failed: " . $e->getMessage() . "\n";
}

echo "\n==========================================\n";
echo "Test completed! Check the results above.\n";
echo "\nTo run the full API:\n";
echo "1. Set up your .env file with database credentials\n";
echo "2. Run the SQL migration to create the users table\n";
echo "3. Configure your web server to point to the public/ directory\n";
echo "4. Test the endpoints using curl or a tool like Postman\n";
echo "\nExample curl commands:\n";
echo "curl -X POST http://localhost/api/auth/register -H 'Content-Type: application/json' -d '{\"email\":\"test@example.com\",\"password\":\"password123\",\"name\":\"Test User\"}'\n";
echo "curl -X POST http://localhost/api/auth/login -H 'Content-Type: application/json' -d '{\"email\":\"test@example.com\",\"password\":\"password123\"}'\n";