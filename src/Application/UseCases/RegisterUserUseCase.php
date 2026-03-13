<?php

namespace TouristAttractionFinder\Application\UseCases;

use TouristAttractionFinder\Domain\Email;
use TouristAttractionFinder\Domain\Exceptions\UserAlreadyExistsException;
use TouristAttractionFinder\Domain\Password;
use TouristAttractionFinder\Domain\User;
use TouristAttractionFinder\Domain\UserRepository;
use TouristAttractionFinder\Application\DTOs\RegisterUserRequest;
use TouristAttractionFinder\Application\DTOs\RegisterUserResponse;

class RegisterUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(RegisterUserRequest $request): RegisterUserResponse
    {
        // Check if user already exists
        $existingUser = $this->userRepository->findByEmail(Email::fromString($request->email));

        if ($existingUser !== null) {
            throw new UserAlreadyExistsException($request->email);
        }

        // Create new user
        $user = User::create(
            0, // ID will be set by database
            Email::fromString($request->email),
            Password::fromPlain($request->password),
            $request->name
        );

        // Save user to repository
        $this->userRepository->save($user);

        // Return response
        return new RegisterUserResponse(
            $user->getId(),
            $user->getEmail()->toString(),
            $user->getName(),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
            $user->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }
}