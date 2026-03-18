<?php

namespace TouristAttractionFinder\Infrastructure\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private string $secretKey;
    private int $expirationTime;

    public function __construct()
    {
        // Use environment variable or generate a secure key
        $secret = $_ENV['JWT_SECRET'] ?? null;
        if (!$secret) {
            // Generate a secure random key if not provided
            $secret = bin2hex(random_bytes(64));
            error_log("Warning: JWT_SECRET not set in environment. Using generated key.");
        }
        $this->secretKey = $secret;

        // Set expiration time (default 1 hour)
        $this->expirationTime = (int)($_ENV['JWT_EXPIRATION'] ?? 3600);

        // Validate expiration time
        if ($this->expirationTime < 300) { // Minimum 5 minutes
            $this->expirationTime = 300;
        } elseif ($this->expirationTime > 86400) { // Maximum 24 hours
            $this->expirationTime = 86400;
        }
    }

    public function generateToken(int $userId, string $email): string
    {
        $payload = [
            'iss' => 'tourist-attraction-finder-api',
            'aud' => 'tourist-attraction-finder-client',
            'iat' => time(),
            'exp' => time() + $this->expirationTime,
            'user_id' => $userId,
            'email' => $email,
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid token: ' . $e->getMessage());
        }
    }

    public function getTokenExpiration(): int
    {
        return $this->expirationTime;
    }

    public function isTokenExpired(string $token): bool
    {
        try {
            $decoded = $this->validateToken($token);
            return $decoded['exp'] < time();
        } catch (\Exception $e) {
            return true;
        }
    }
}
