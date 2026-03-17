<?php

namespace TouristAttractionFinder\Infrastructure\Repositories;

use TouristAttractionFinder\Domain\Attraction;
use TouristAttractionFinder\Domain\AttractionRepository;
use TouristAttractionFinder\Infrastructure\Config\DatabaseConfig;

class MySQLAttractionRepository implements AttractionRepository
{
  private \PDO $connection;

  public function __construct()
  {
    $this->connection = DatabaseConfig::getConnection();
  }

  public function findById(int $id): ?Attraction
  {
    $stmt = $this->connection->prepare('SELECT * FROM attractions WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();

    if (!$result) {
      return null;
    }

    return Attraction::fromArray($result);
  }

  public function findByLocation(string $location): array
  {
    $stmt = $this->connection->prepare('SELECT * FROM attractions WHERE location LIKE :location ORDER BY rating DESC');
    $stmt->execute(['location' => '%' . $location . '%']);
    $results = $stmt->fetchAll();

    $attractions = [];
    foreach ($results as $result) {
      $attractions[] = Attraction::fromArray($result);
    }

    return $attractions;
  }

  public function findByCategory(string $category): array
  {
    $stmt = $this->connection->prepare('SELECT * FROM attractions WHERE category = :category ORDER BY rating DESC');
    $stmt->execute(['category' => $category]);
    $results = $stmt->fetchAll();

    $attractions = [];
    foreach ($results as $result) {
      $attractions[] = Attraction::fromArray($result);
    }

    return $attractions;
  }

  public function findTopRated(int $limit = 6): array
  {
    $stmt = $this->connection->prepare('SELECT * FROM attractions ORDER BY rating DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();

    $attractions = [];
    foreach ($results as $result) {
      $attractions[] = Attraction::fromArray($result);
    }

    return $attractions;
  }

  public function findAll(): array
  {
    $stmt = $this->connection->query('SELECT * FROM attractions ORDER BY rating DESC');
    $results = $stmt->fetchAll();

    $attractions = [];
    foreach ($results as $result) {
      $attractions[] = Attraction::fromArray($result);
    }

    return $attractions;
  }

  public function save(Attraction $attraction): void
  {
    if ($attraction->getId() > 0) {
      // Update existing attraction
      $stmt = $this->connection->prepare('
                UPDATE attractions
                SET name = :name, location = :location, description = :description, 
                    image_url = :image_url, category = :category, rating = :rating, updated_at = :updated_at
                WHERE id = :id
            ');
      $stmt->execute([
        'id' => $attraction->getId(),
        'name' => $attraction->getName(),
        'location' => $attraction->getLocation(),
        'description' => $attraction->getDescription(),
        'image_url' => $attraction->getImageUrl(),
        'category' => $attraction->getCategory(),
        'rating' => $attraction->getRating(),
        'updated_at' => $attraction->getUpdatedAt()->format('Y-m-d H:i:s'),
      ]);
    } else {
      // Insert new attraction
      $stmt = $this->connection->prepare('
                INSERT INTO attractions (name, location, description, image_url, category, rating, created_at, updated_at)
                VALUES (:name, :location, :description, :image_url, :category, :rating, :created_at, :updated_at)
            ');
      $stmt->execute([
        'name' => $attraction->getName(),
        'location' => $attraction->getLocation(),
        'description' => $attraction->getDescription(),
        'image_url' => $attraction->getImageUrl(),
        'category' => $attraction->getCategory(),
        'rating' => $attraction->getRating(),
        'created_at' => $attraction->getCreatedAt()->format('Y-m-d H:i:s'),
        'updated_at' => $attraction->getUpdatedAt()->format('Y-m-d H:i:s'),
      ]);

      // Set the generated ID back to the attraction
      $attraction->setId((int)$this->connection->lastInsertId());
    }
  }

  public function delete(int $id): void
  {
    $stmt = $this->connection->prepare('DELETE FROM attractions WHERE id = :id');
    $stmt->execute(['id' => $id]);
  }
}
