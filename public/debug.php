<?php
// Debug script to check routing configuration

header('Content-Type: application/json');

$debug = [
    'SERVER_REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    'SERVER_REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'SERVER_SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'SERVER_PHP_SELF' => $_SERVER['PHP_SELF'] ?? 'N/A',
    'PARSED_PATH' => parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH),
    'MOD_REWRITE_ENABLED' => extension_loaded('mod_rewrite') ? 'Module not detectable from PHP' : 'N/A',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'CURRENT_FILE' => __FILE__,
];

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
