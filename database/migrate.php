<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TouristAttractionFinder\Infrastructure\Config\DatabaseConfig;

class DatabaseMigrator
{
  private \PDO $connection;

  public function __construct()
  {
    $this->connection = DatabaseConfig::getConnection();
  }

  public function runMigrations(): void
  {
    echo "Running database migrations...\n";

    // Create users table
    $this->createUsersTable();

    // Create attractions table
    $this->createAttractionsTable();

    echo "All migrations completed successfully!\n";
  }

  private function createUsersTable(): void
  {
    $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $this->connection->exec($sql);
    echo "✓ Users table created\n";
  }

  private function createAttractionsTable(): void
  {
    $sql = "CREATE TABLE IF NOT EXISTS attractions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            location VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(500),
            category VARCHAR(100),
            rating DECIMAL(3,2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_location (location),
            INDEX idx_category (category),
            INDEX idx_rating (rating)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $this->connection->exec($sql);
    echo "✓ Attractions table created\n";
  }
}

// Run migrations if script is executed directly
if (php_sapi_name() === 'cli') {
  try {
    $migrator = new DatabaseMigrator();
    $migrator->runMigrations();
  } catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
  }
}
