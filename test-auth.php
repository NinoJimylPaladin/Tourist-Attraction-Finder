<?php

// Simple test script to verify authentication functionality
require_once 'vendor/autoload.php';

use TouristAttractionFinder\Infrastructure\Config\DatabaseConfig;
use TouristAttractionFinder\Infrastructure\Services\Logger;

class AuthTester
{
  private Logger $logger;
  private \PDO $connection;

  public function __construct()
  {
    $this->logger = new Logger();
    $this->connection = DatabaseConfig::getConnection();
  }

  public function testDatabaseConnection(): bool
  {
    try {
      $stmt = $this->connection->query("SELECT 1");
      $result = $stmt->fetch();
      $this->logger->info("Database connection test passed");
      return true;
    } catch (\Exception $e) {
      $this->logger->error("Database connection failed", ['error' => $e->getMessage()]);
      return false;
    }
  }

  public function testTablesExist(): bool
  {
    try {
      // Check users table
      $stmt = $this->connection->query("SHOW TABLES LIKE 'users'");
      $usersExists = $stmt->fetch();

      // Check attractions table
      $stmt = $this->connection->query("SHOW TABLES LIKE 'attractions'");
      $attractionsExists = $stmt->fetch();

      if ($usersExists && $attractionsExists) {
        $this->logger->info("Required tables exist");
        return true;
      } else {
        $this->logger->warning("Required tables missing", [
          'users_table' => $usersExists ? 'exists' : 'missing',
          'attractions_table' => $attractionsExists ? 'exists' : 'missing'
        ]);
        return false;
      }
    } catch (\Exception $e) {
      $this->logger->error("Table check failed", ['error' => $e->getMessage()]);
      return false;
    }
  }

  public function testJWTService(): bool
  {
    try {
      $jwtService = new \TouristAttractionFinder\Infrastructure\Services\JWTService();
      $token = $jwtService->generateToken(1, 'test@example.com');

      if ($token) {
        $this->logger->info("JWT service test passed");
        return true;
      } else {
        $this->logger->error("JWT service test failed - no token generated");
        return false;
      }
    } catch (\Exception $e) {
      $this->logger->error("JWT service test failed", ['error' => $e->getMessage()]);
      return false;
    }
  }

  public function runAllTests(): void
  {
    echo "Running Authentication System Tests...\n";
    echo "=====================================\n\n";

    $tests = [
      'Database Connection' => $this->testDatabaseConnection(),
      'Required Tables' => $this->testTablesExist(),
      'JWT Service' => $this->testJWTService(),
    ];

    $passed = 0;
    $total = count($tests);

    foreach ($tests as $testName => $result) {
      $status = $result ? 'PASS' : 'FAIL';
      $color = $result ? "\033[32m" : "\033[31m";
      echo "{$color}✓ {$testName}: {$status}\033[0m\n";

      if ($result) {
        $passed++;
      }
    }

    echo "\n=====================================\n";
    echo "Tests passed: {$passed}/{$total}\n";

    if ($passed === $total) {
      echo "\033[32mAll tests passed! Authentication system is ready.\033[0m\n";
    } else {
      echo "\033[31mSome tests failed. Please check the logs for details.\033[0m\n";
    }
  }
}

// Run tests if script is executed directly
if (php_sapi_name() === 'cli') {
  $tester = new AuthTester();
  $tester->runAllTests();
}
