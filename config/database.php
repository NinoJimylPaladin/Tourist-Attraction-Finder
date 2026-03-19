<?php

/**
 * Database Configuration
 *
 * This file contains the database connection configuration
 * using PDO for secure database operations.
 */

// Database configuration (using hardcoded values for now)
$host = 'localhost';
$dbname = 'tourist_attraction_finder';
$username = 'root';
$password = '';

// Create PDO connection
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Database connection failed',
    'error' => $e->getMessage()
  ]);
  exit();
}

// Test connection
try {
  $pdo->query("SELECT 1");
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Database is not accessible',
    'error' => $e->getMessage()
  ]);
  exit();
}
