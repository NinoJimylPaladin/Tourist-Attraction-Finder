<?php

namespace TouristAttractionFinder\Domain;

class User
{
    private int $id;
    private Email $email;
    private Password $password;
    private string $name;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        int $id,
        Email $email,
        Password $password,
        string $name,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function create(
        int $id,
        Email $email,
        Password $password,
        string $name
    ): self {
        $now = new \DateTimeImmutable();

        return new self($id, $email, $password, $name, $now, $now);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            Email::fromString($data['email']),
            Password::fromHashed($data['password']),
            $data['name'],
            new \DateTimeImmutable($data['created_at']),
            new \DateTimeImmutable($data['updated_at'])
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updatePassword(Password $password): void
    {
        $this->password = $password;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email->toString(),
            'name' => $this->name,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    public function equals(User $other): bool
    {
        return $this->id === $other->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
