<?php

namespace TouristAttractionFinder\Domain;

interface AttractionRepository
{
  public function findById(int $id): ?Attraction;
  public function findByLocation(string $location): array;
  public function findByCategory(string $category): array;
  public function findTopRated(int $limit = 6): array;
  public function findAll(): array;
  public function save(Attraction $attraction): void;
  public function delete(int $id): void;
}
