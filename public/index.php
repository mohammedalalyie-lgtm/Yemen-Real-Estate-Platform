<?php

declare(strict_types=1);

// Set default timezone to Yemen (Asia/Aden, UTC+3)
date_default_timezone_set('Asia/Aden');

// Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize Application Environment
\App\Config\App::init();

if (\App\Config\App::env('APP_DEBUG', 'false') === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Start Secure Session
\App\Helpers\Session::start();

// Load Routes
require_once dirname(__DIR__) . '/routes/web.php';

// Dispatch Request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

Router::dispatch($uri, $method);
