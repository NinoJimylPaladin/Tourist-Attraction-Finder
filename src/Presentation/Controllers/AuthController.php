<?php

namespace TouristAttractionFinder\Presentation\Controllers;

use TouristAttractionFinder\Application\DTOs\LoginUserRequest;
use TouristAttractionFinder\Application\DTOs\RegisterUserRequest;
use TouristAttractionFinder\Application\UseCases\LoginUserUseCase;
use TouristAttractionFinder\Application\UseCases\RegisterUserUseCase;
use TouristAttractionFinder\Domain\Exceptions\UserAlreadyExistsException;
use TouristAttractionFinder\Domain\Exceptions\InvalidCredentialsException;
use TouristAttractionFinder\Infrastructure\Repositories\MySQLUserRepository;
use TouristAttractionFinder\Infrastructure\Services\JWTService;
use TouristAttractionFinder\Infrastructure\Services\Logger;

class AuthController
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

    public function register(array $data): array
    {
        // Validate required fields
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            http_response_code(400);
            throw new \InvalidArgumentException('Email, password, and name are required');
        }

        $request = new RegisterUserRequest(
            $data['email'],
            $data['password'],
            $data['name']
        );

        try {
            $response = $this->registerUseCase->execute($request);

            http_response_code(201);
            return [
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $response->id,
                    'email' => $response->email,
                    'name' => $response->name,
                    'created_at' => $response->createdAt,
                ]
            ];
        } catch (UserAlreadyExistsException $e) {
            http_response_code(409);
            throw $e;
        }
    }

    public function login(array $data): array
    {
        // Validate required fields
        if (!isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            throw new \InvalidArgumentException('Email and password are required');
        }

        $request = new LoginUserRequest(
            $data['email'],
            $data['password']
        );

        try {
            $response = $this->loginUseCase->execute($request);

            return [
                'message' => 'Login successful',
                'user' => [
                    'id' => $response->id,
                    'email' => $response->email,
                    'name' => $response->name,
                ],
                'token' => $response->token,
                'token_type' => $response->tokenType,
                'expires_in' => $response->expiresIn,
            ];
        } catch (InvalidCredentialsException $e) {
            http_response_code(401);
            throw $e;
        }
    }
}
