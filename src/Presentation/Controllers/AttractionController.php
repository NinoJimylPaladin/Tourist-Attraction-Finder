<?php

namespace TouristAttractionFinder\Presentation\Controllers;

use TouristAttractionFinder\Domain\Attraction;
use TouristAttractionFinder\Domain\AttractionRepository;
use TouristAttractionFinder\Infrastructure\Repositories\MySQLAttractionRepository;
use TouristAttractionFinder\Presentation\Middleware\AuthenticationMiddleware;
use TouristAttractionFinder\Presentation\Middleware\ValidationMiddleware;

class AttractionController
{
  private AttractionRepository $attractionRepository;

  public function __construct()
  {
    $this->attractionRepository = new MySQLAttractionRepository();
  }

  public function getAll(array $data): array
  {
    $attractions = $this->attractionRepository->findAll();

    return array_map(function ($attraction) {
      return $attraction->toArray();
    }, $attractions);
  }

  public function getTopRated(array $data): array
  {
    $limit = isset($data['limit']) ? (int)$data['limit'] : 6;
    $attractions = $this->attractionRepository->findTopRated($limit);

    return array_map(function ($attraction) {
      return $attraction->toArray();
    }, $attractions);
  }

  public function getByCategory(array $data): array
  {
    if (!isset($data['category'])) {
      http_response_code(400);
      throw new \InvalidArgumentException('Category parameter is required');
    }

    $attractions = $this->attractionRepository->findByCategory($data['category']);

    return array_map(function ($attraction) {
      return $attraction->toArray();
    }, $attractions);
  }

  public function getByLocation(array $data): array
  {
    if (!isset($data['location'])) {
      http_response_code(400);
      throw new \InvalidArgumentException('Location parameter is required');
    }

    $attractions = $this->attractionRepository->findByLocation($data['location']);

    return array_map(function ($attraction) {
      return $attraction->toArray();
    }, $attractions);
  }

  public function getById(array $data): array
  {
    if (!isset($data['id'])) {
      http_response_code(400);
      throw new \InvalidArgumentException('ID parameter is required');
    }

    $attraction = $this->attractionRepository->findById((int)$data['id']);

    if (!$attraction) {
      http_response_code(404);
      throw new \Exception('Attraction not found');
    }

    return $attraction->toArray();
  }

  public function create(array $data): array
  {
    // Check if user is authenticated
    $middleware = new AuthenticationMiddleware();
    $middleware->requireAuth();

    // Validate input
    ValidationMiddleware::validate($data, [
      'name' => \Respect\Validation\Validator::stringType()->length(2, 255),
      'location' => \Respect\Validation\Validator::stringType()->length(2, 255),
      'description' => \Respect\Validation\Validator::stringType()->length(10, null),
      'image_url' => \Respect\Validation\Validator::url(),
      'category' => \Respect\Validation\Validator::stringType()->length(2, 100),
      'rating' => \Respect\Validation\Validator::floatVal()->min(0)->max(5)
    ]);

    $attraction = Attraction::create(
      0, // ID will be set by database
      $data['name'],
      $data['location'],
      $data['description'],
      $data['image_url'],
      $data['category'],
      (float)$data['rating']
    );

    $this->attractionRepository->save($attraction);

    return [
      'message' => 'Attraction created successfully',
      'attraction' => $attraction->toArray()
    ];
  }

  public function update(array $data): array
  {
    // Check if user is authenticated
    $middleware = new AuthenticationMiddleware();
    $middleware->requireAuth();

    if (!isset($data['id'])) {
      http_response_code(400);
      throw new \InvalidArgumentException('ID parameter is required');
    }

    $attraction = $this->attractionRepository->findById((int)$data['id']);

    if (!$attraction) {
      http_response_code(404);
      throw new \Exception('Attraction not found');
    }

    // Validate input
    ValidationMiddleware::validate($data, [
      'name' => \Respect\Validation\Validator::stringType()->length(2, 255),
      'location' => \Respect\Validation\Validator::stringType()->length(2, 255),
      'description' => \Respect\Validation\Validator::stringType()->length(10, null),
      'image_url' => \Respect\Validation\Validator::url(),
      'category' => \Respect\Validation\Validator::stringType()->length(2, 100),
      'rating' => \Respect\Validation\Validator::floatVal()->min(0)->max(5)
    ]);

    $attraction->update(
      $data['name'],
      $data['location'],
      $data['description'],
      $data['image_url'],
      $data['category'],
      (float)$data['rating']
    );

    $this->attractionRepository->save($attraction);

    return [
      'message' => 'Attraction updated successfully',
      'attraction' => $attraction->toArray()
    ];
  }

  public function delete(array $data): array
  {
    // Check if user is authenticated
    $middleware = new AuthenticationMiddleware();
    $middleware->requireAuth();

    if (!isset($data['id'])) {
      http_response_code(400);
      throw new \InvalidArgumentException('ID parameter is required');
    }

    $attraction = $this->attractionRepository->findById((int)$data['id']);

    if (!$attraction) {
      http_response_code(404);
      throw new \Exception('Attraction not found');
    }

    $this->attractionRepository->delete($attraction->getId());

    return [
      'message' => 'Attraction deleted successfully'
    ];
  }
}
