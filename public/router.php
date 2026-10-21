<?php

// Let the development server serve existing static files directly.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

// Forward application requests to the front controller.
require __DIR__ . '/index.php';
