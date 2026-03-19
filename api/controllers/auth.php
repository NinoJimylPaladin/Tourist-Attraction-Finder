<?php

/**
 * Authentication Controller
 * 
 * Handles user registration, login, and authentication
 */

require_once __DIR__ . '/../config/database.php';

/**
 * User Registration
 */
function register()
{
  global $pdo;

  // Get input data
  $input = json_decode(file_get_contents('php://input'), true);

  if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    return;
  }

  $name = trim($input['name'] ?? '');
  $email = trim($input['email'] ?? '');
  $password = $input['password'] ?? '';

  // Validation
  if (empty($name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    return;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    return;
  }

  if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    return;
  }

  // Check if email already exists
  try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
      http_response_code(409);
      echo json_encode(['success' => false, 'message' => 'Email already registered']);
      return;
    }
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    return;
  }

  // Hash password
  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  $created_at = date('Y-m-d H:i:s');

  // Insert user
  try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password_hash, $created_at]);

    $user_id = $pdo->lastInsertId();

    // Generate token
    $token = generateToken($user_id);

    http_response_code(201);
    echo json_encode([
      'success' => true,
      'message' => 'User registered successfully',
      'data' => [
        'user' => [
          'id' => $user_id,
          'name' => $name,
          'email' => $email
        ],
        'token' => $token
      ]
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
  }
}

/**
 * User Login
 */
function login()
{
  global $pdo;

  // Get input data
  $input = json_decode(file_get_contents('php://input'), true);

  if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    return;
  }

  $email = trim($input['email'] ?? '');
  $password = $input['password'] ?? '';

  // Validation
  if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    return;
  }

  // Find user
  try {
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
      return;
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
      http_response_code(401);
      echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
      return;
    }

    // Generate token
    $token = generateToken($user['id']);

    http_response_code(200);
    echo json_encode([
      'success' => true,
      'message' => 'Login successful',
      'data' => [
        'user' => [
          'id' => $user['id'],
          'name' => $user['name'],
          'email' => $user['email']
        ],
        'token' => $token
      ]
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Login failed']);
  }
}

/**
 * User Logout
 */
function logout()
{
  // For JWT tokens, logout is handled client-side by removing the token
  // This endpoint is just for consistency
  http_response_code(200);
  echo json_encode(['success' => true, 'message' => 'Logout successful']);
}

/**
 * Generate JWT Token
 */
function generateToken($user_id)
{
  $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
  $payload = json_encode([
    'user_id' => $user_id,
    'exp' => time() + (60 * 60 * 24) // 24 hours
  ]);

  $secret = 'your-secret-key'; // In production, use a strong secret from environment variables

  $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
  $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

  $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
  $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

  return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

/**
 * Verify Token
 */
function verifyToken($token)
{
  if (empty($token)) {
    return false;
  }

  $secret = 'your-secret-key'; // In production, use a strong secret from environment variables

  $token_parts = explode('.', $token);
  if (count($token_parts) != 3) {
    return false;
  }

  $header = $token_parts[0];
  $payload = $token_parts[1];
  $signature = $token_parts[2];

  // Verify signature
  $valid_signature = hash_hmac('sha256', $header . "." . $payload, $secret, true);
  $valid_signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($valid_signature));

  if ($signature !== $valid_signature) {
    return false;
  }

  // Decode payload
  $payload = json_decode(base64_decode($payload), true);

  // Check expiration
  if (isset($payload['exp']) && $payload['exp'] < time()) {
    return false;
  }

  return $payload['user_id'] ?? false;
}
