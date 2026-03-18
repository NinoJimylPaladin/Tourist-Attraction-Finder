<?php

namespace TouristAttractionFinder\Domain;

class Password
{
    private string $hash;

    private function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    public static function fromPlain(string $plainPassword): self
    {
        if (!self::isValid($plainPassword)) {
            throw new \InvalidArgumentException('Password must be at least 8 characters');
        }

        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        if ($hash === false) {
            throw new \RuntimeException('Failed to hash password');
        }

        return new self($hash);
    }

    public static function fromHashed(string $hash): self
    {
        if (!preg_match('/^\$2[ayb]\$[0-9]{2}\$[A-Za-z0-9\.\/]{53}$/', $hash)) {
            throw new \InvalidArgumentException('Invalid password hash format');
        }

        return new self($hash);
    }

    public static function isValid(string $password): bool
    {
        return strlen(trim($password)) >= 8;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hash);
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function needsRehash(): bool
    {
        return password_needs_rehash($this->hash, PASSWORD_DEFAULT);
    }
}
