<?php

namespace TouristAttractionFinder\Domain;

class Attraction
{
  private int $id;
  private string $name;
  private string $location;
  private string $description;
  private string $imageUrl;
  private string $category;
  private float $rating;
  private \DateTimeImmutable $createdAt;
  private \DateTimeImmutable $updatedAt;

  private function __construct(
    int $id,
    string $name,
    string $location,
    string $description,
    string $imageUrl,
    string $category,
    float $rating,
    \DateTimeImmutable $createdAt,
    \DateTimeImmutable $updatedAt
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->location = $location;
    $this->description = $description;
    $this->imageUrl = $imageUrl;
    $this->category = $category;
    $this->rating = $rating;
    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  public static function create(
    int $id,
    string $name,
    string $location,
    string $description,
    string $imageUrl,
    string $category,
    float $rating
  ): self {
    $now = new \DateTimeImmutable();

    return new self($id, $name, $location, $description, $imageUrl, $category, $rating, $now, $now);
  }

  public static function fromArray(array $data): self
  {
    return new self(
      $data['id'],
      $data['name'],
      $data['location'],
      $data['description'],
      $data['image_url'],
      $data['category'],
      (float)$data['rating'],
      new \DateTimeImmutable($data['created_at']),
      new \DateTimeImmutable($data['updated_at'])
    );
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getLocation(): string
  {
    return $this->location;
  }

  public function getDescription(): string
  {
    return $this->description;
  }

  public function getImageUrl(): string
  {
    return $this->imageUrl;
  }

  public function getCategory(): string
  {
    return $this->category;
  }

  public function getRating(): float
  {
    return $this->rating;
  }

  public function getCreatedAt(): \DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): \DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function update(
    string $name,
    string $location,
    string $description,
    string $imageUrl,
    string $category,
    float $rating
  ): void {
    $this->name = $name;
    $this->location = $location;
    $this->description = $description;
    $this->imageUrl = $imageUrl;
    $this->category = $category;
    $this->rating = $rating;
    $this->updatedAt = new \DateTimeImmutable();
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'location' => $this->location,
      'description' => $this->description,
      'image_url' => $this->imageUrl,
      'category' => $this->category,
      'rating' => $this->rating,
      'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
      'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
    ];
  }

  public function equals(Attraction $other): bool
  {
    return $this->id === $other->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }
}
