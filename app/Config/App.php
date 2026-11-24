<?php
namespace App\Config;

class App
{
    private static array $env = [];

    public static function init(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$key, $value] = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    $value = trim($value, '"\'');
                    self::$env[$key] = $value;
                    if (!isset($_ENV[$key])) {
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        return self::$env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    public static function baseUrl(string $path = ''): string
    {
        $configured = self::env('APP_URL');
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Dynamically detect subfolder if running on XAMPP Apache
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseFolder = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        
        $base = $protocol . $host . $baseFolder;
        
        // If baseUrl is called with empty or leading slash, format cleanly
        $path = ltrim($path, '/');
        return $path === '' ? $base : $base . '/' . $path;
    }

    public static function asset(string $path): string
    {
        return self::baseUrl('assets/' . ltrim($path, '/'));
    }

    public static function upload(string $path): string
    {
        return self::baseUrl('uploads/' . ltrim($path, '/'));
    }
}
