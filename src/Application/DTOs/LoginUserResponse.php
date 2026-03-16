<?php

namespace TouristAttractionFinder\Application\DTOs;

class LoginUserResponse
{
    public int $id;
    public string $email;
    public string $name;
    public string $token;
    public string $tokenType = 'Bearer';
    public int $expiresIn;

    public function __construct(
        int $id,
        string $email,
        string $name,
        string $token,
        int $expiresIn
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }
}