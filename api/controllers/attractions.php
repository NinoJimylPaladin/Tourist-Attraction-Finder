<?php

/**
 * Attractions Controller
 * 
 * Handles attraction-related API endpoints
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get Top Rated Attractions
 */
function top_rated()
{
  global $pdo;

  // Get limit parameter (default 6)
  $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;

  // Validate limit
  if ($limit < 1 || $limit > 20) {
    $limit = 6;
  }

  try {
    $stmt = $pdo->prepare("
            SELECT id, name, location, description, image_url, rating, created_at 
            FROM attractions 
            WHERE status = 'active' 
            ORDER BY rating DESC, created_at DESC 
            LIMIT ?
        ");
    $stmt->execute([$limit]);
    $attractions = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
      'success' => true,
      'data' => $attractions
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to fetch attractions'
    ]);
  }
}

/**
 * Get All Attractions
 */
function index()
{
  global $pdo;

  try {
    $stmt = $pdo->prepare("
            SELECT id, name, location, description, image_url, rating, created_at 
            FROM attractions 
            WHERE status = 'active' 
            ORDER BY created_at DESC
        ");
    $stmt->execute();
    $attractions = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
      'success' => true,
      'data' => $attractions
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to fetch attractions'
    ]);
  }
}

/**
 * Get Single Attraction
 */
function show($id)
{
  global $pdo;

  if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Invalid attraction ID'
    ]);
    return;
  }

  try {
    $stmt = $pdo->prepare("
            SELECT id, name, location, description, image_url, rating, created_at 
            FROM attractions 
            WHERE id = ? AND status = 'active'
        ");
    $stmt->execute([(int)$id]);
    $attraction = $stmt->fetch();

    if (!$attraction) {
      http_response_code(404);
      echo json_encode([
        'success' => false,
        'message' => 'Attraction not found'
      ]);
      return;
    }

    http_response_code(200);
    echo json_encode([
      'success' => true,
      'data' => $attraction
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to fetch attraction'
    ]);
  }
}

/**
 * Create New Attraction (Admin only)
 */
function create()
{
  global $pdo;

  // Check authentication
  $user_id = authenticate();
  if (!$user_id) {
    http_response_code(401);
    echo json_encode([
      'success' => false,
      'message' => 'Authentication required'
    ]);
    return;
  }

  // Check if user is admin (this would need to be implemented based on your user system)
  // For now, we'll assume any authenticated user can create attractions

  $input = json_decode(file_get_contents('php://input'), true);

  if (!$input) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Invalid JSON input'
    ]);
    return;
  }

  $name = trim($input['name'] ?? '');
  $location = trim($input['location'] ?? '');
  $description = trim($input['description'] ?? '');
  $image_url = trim($input['image_url'] ?? '');
  $rating = (float)($input['rating'] ?? 0);

  // Validation
  if (empty($name) || empty($location)) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Name and location are required'
    ]);
    return;
  }

  if ($rating < 0 || $rating > 5) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => 'Rating must be between 0 and 5'
    ]);
    return;
  }

  $created_at = date('Y-m-d H:i:s');

  try {
    $stmt = $pdo->prepare("
            INSERT INTO attractions (name, location, description, image_url, rating, created_at, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");
    $stmt->execute([$name, $location, $description, $image_url, $rating, $created_at]);

    $attraction_id = $pdo->lastInsertId();

    http_response_code(201);
    echo json_encode([
      'success' => true,
      'message' => 'Attraction created successfully',
      'data' => [
        'id' => $attraction_id,
        'name' => $name,
        'location' => $location,
        'description' => $description,
        'image_url' => $image_url,
        'rating' => $rating
      ]
    ]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'message' => 'Failed to create attraction'
    ]);
  }
}

/**
 * Authenticate User
 */
function authenticate()
{
  $headers = getallheaders();
  $auth_header = $headers['Authorization'] ?? '';

  if (empty($auth_header) || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    return false;
  }

  $token = $matches[1];
  return verifyToken($token);
}

/**
 * Verify Token (copied from auth.php for standalone use)
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
