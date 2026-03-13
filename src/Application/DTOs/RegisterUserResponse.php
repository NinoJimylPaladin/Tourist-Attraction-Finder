<?php

namespace TouristAttractionFinder\Application\DTOs;

class RegisterUserResponse
{
    public int $id;
    public string $email;
    public string $name;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        int $id,
        string $email,
        string $name,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['email'],
            $data['name'],
            $data['created_at'],
            $data['updated_at']
        );
    }
}