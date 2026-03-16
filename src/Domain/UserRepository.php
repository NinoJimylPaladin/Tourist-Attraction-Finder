<?php

namespace TouristAttractionFinder\Domain;

use TouristAttractionFinder\Domain\User;
use TouristAttractionFinder\Domain\Email;

interface UserRepository
{
    public function findByEmail(Email $email): ?User;

    public function findById(int $id): ?User;

    public function save(User $user): void;
}
