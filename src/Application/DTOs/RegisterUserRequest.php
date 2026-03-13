<?php

namespace TouristAttractionFinder\Application\DTOs;

class RegisterUserRequest
{
    public string $email;
    public string $password;
    public string $name;

    public function __construct(string $email, string $password, string $name)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }
}