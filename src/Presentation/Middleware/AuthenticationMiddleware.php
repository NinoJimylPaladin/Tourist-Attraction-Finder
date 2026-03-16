<?php

namespace TouristAttractionFinder\Presentation\Middleware;

use TouristAttractionFinder\Infrastructure\Services\JWTService;
use TouristAttractionFinder\Infrastructure\Repositories\MySQLUserRepository;
use TouristAttractionFinder\Domain\User;

class AuthenticationMiddleware
{
    private JWTService $jwtService;
    private MySQLUserRepository $userRepository;

    public function __construct()
    {
        $this->jwtService = new JWTService();
        $this->userRepository = new MySQLUserRepository();
    }

    public function authenticate(): ?User
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            return null;
        }

        $authHeader = $headers['Authorization'];

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return null;
        }

        $token = $matches[1];

        try {
            $decoded = $this->jwtService->validateToken($token);

            if ($this->jwtService->isTokenExpired($token)) {
                return null;
            }

            $user = $this->userRepository->findById($decoded['user_id']);

            if (!$user || $user->getEmail()->toString() !== $decoded['email']) {
                return null;
            }

            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function requireAuth(): User
    {
        $user = $this->authenticate();

        if (!$user) {
            http_response_code(401);
            throw new \Exception(json_encode([
                'success' => false,
                'message' => 'Authentication required',
                'data' => null
            ]));
        }

        return $user;
    }

    public function optionalAuth(): ?User
    {
        return $this->authenticate();
    }
}