<?php
// Test API routing
$path = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = dirname($script_name);

echo "REQUEST_URI: $path\n";
echo "SCRIPT_NAME: $script_name\n";
echo "BASE_PATH: $base_path\n";

// Remove base path from request URI
if ($base_path !== '/') {
  $path = str_replace($base_path, '', $path);
}

// Remove query string
$path = parse_url($path, PHP_URL_PATH);

// Remove leading slash
$path = ltrim($path, '/');

// Split path into segments
$segments = explode('/', $path);

echo "PATH after processing: $path\n";
echo "SEGMENTS: " . print_r($segments, true) . "\n";

if (count($segments) >= 2) {
  $controller = $segments[0];
  $action = $segments[1];
  $params = array_slice($segments, 2);

  echo "CONTROLLER: $controller\n";
  echo "ACTION: $action\n";
  echo "PARAMS: " . print_r($params, true) . "\n";

  // Include the appropriate controller
  $controller_file = __DIR__ . "/api/controllers/{$controller}.php";

  echo "CONTROLLER FILE: $controller_file\n";
  echo "FILE EXISTS: " . (file_exists($controller_file) ? 'YES' : 'NO') . "\n";

  if (file_exists($controller_file)) {
    require_once $controller_file;

    // Call the appropriate action
    if (function_exists($action)) {
      echo "ACTION EXISTS: YES\n";
    } else {
      echo "ACTION EXISTS: NO\n";
    }
  }
}
?>