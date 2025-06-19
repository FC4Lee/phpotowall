<?php
// Ensure no output before JSON
ob_start();

// Set JSON content type
header('Content-Type: application/json');

// Disable error display, log to file
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_errors.log');

// Use compatible array syntax
error_reporting(E_ALL & ~E_NOTICE);

// Define photo directory path (Windows-compatible)
$photoDir = realpath(dirname(__FILE__) . '/photo');

// Check if directory exists
if (!is_dir($photoDir)) {
    http_response_code(500);
    echo json_encode(array('error' => 'Photo directory does not exist: ' . $photoDir));
    ob_end_flush();
    exit;
}

// Scan directory
try {
    $files = scandir($photoDir);
    if ($files === false) {
        throw new Exception('Unable to read directory');
    }
    // Filter image files
    $images = array_filter($files, function($file) {
        return preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    });
    // Remove . and .. directories
    $images = array_values(array_diff($images, array('.', '..')));
    // Output JSON
    echo json_encode($images);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Unable to read directory: ' . $e->getMessage()));
}

// Flush output
ob_end_flush();
?>