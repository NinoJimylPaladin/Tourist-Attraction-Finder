<?php

namespace TouristAttractionFinder\Domain;

use Respect\Validation\Validator as v;

class Email
{
    private string $email;

    private function __construct(string $email)
    {
        $this->email = strtolower(trim($email));
    }

    public static function fromString(string $email): self
    {
        if (!self::isValid($email)) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        return new self($email);
    }

    public static function isValid(string $email): bool
    {
        try {
            return v::email()->validate($email);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function equals(Email $other): bool
    {
        return $this->email === $other->email;
    }
}
