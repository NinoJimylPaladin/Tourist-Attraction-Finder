<?php

namespace TouristAttractionFinder\Infrastructure\Repositories;

use TouristAttractionFinder\Domain\Email;
use TouristAttractionFinder\Domain\User;
use TouristAttractionFinder\Domain\UserRepository;
use TouristAttractionFinder\Infrastructure\Config\DatabaseConfig;

class MySQLUserRepository implements UserRepository
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = DatabaseConfig::getConnection();
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email->toString()]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return User::fromArray($result);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return User::fromArray($result);
    }

    public function save(User $user): void
    {
        if ($user->getId() > 0) {
            // Update existing user
            $stmt = $this->connection->prepare('
                UPDATE users
                SET email = :email, password = :password, name = :name, updated_at = :updated_at
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $user->getId(),
                'email' => $user->getEmail()->toString(),
                'password' => $user->getPassword()->getHash(),
                'name' => $user->getName(),
                'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]);
        } else {
            // Insert new user
            $stmt = $this->connection->prepare('
                INSERT INTO users (email, password, name, created_at, updated_at)
                VALUES (:email, :password, :name, :created_at, :updated_at)
            ');
            $stmt->execute([
                'email' => $user->getEmail()->toString(),
                'password' => $user->getPassword()->getHash(),
                'name' => $user->getName(),
                'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]);

            // Set the generated ID back to the user
            $user->setId((int)$this->connection->lastInsertId());
        }
    }
}