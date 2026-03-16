<?php

namespace TouristAttractionFinder\Application\UseCases;

use TouristAttractionFinder\Domain\Email;
use TouristAttractionFinder\Domain\Exceptions\InvalidCredentialsException;
use TouristAttractionFinder\Domain\UserRepository;
use TouristAttractionFinder\Application\DTOs\LoginUserRequest;
use TouristAttractionFinder\Application\DTOs\LoginUserResponse;
use TouristAttractionFinder\Infrastructure\Services\JWTService;

class LoginUserUseCase
{
    private UserRepository $userRepository;
    private JWTService $jwtService;

    public function __construct(UserRepository $userRepository, JWTService $jwtService)
    {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    public function execute(LoginUserRequest $request): LoginUserResponse
    {
        // Find user by email
        $user = $this->userRepository->findByEmail(Email::fromString($request->email));

        if ($user === null) {
            throw new InvalidCredentialsException();
        }

        // Verify password
        if (!$user->getPassword()->verify($request->password)) {
            throw new InvalidCredentialsException();
        }

        // Generate JWT token
        $token = $this->jwtService->generateToken($user->getId(), $user->getEmail()->toString());

        // Return response
        return new LoginUserResponse(
            $user->getId(),
            $user->getEmail()->toString(),
            $user->getName(),
            $token,
            $this->jwtService->getTokenExpiration()
        );
    }
}