<?php
namespace App\Helpers;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Configure secure cookie parameters
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string
    {
        self::start();
        if (isset($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }

    public static function hasFlash(string $type): bool
    {
        self::start();
        return !empty($_SESSION['flash'][$type]);
    }

    public static function setAuth(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        // Remove password hash from session storage
        unset($user['password_hash']);
        $_SESSION['auth_user'] = $user;
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION['auth_user'] ?? null;
    }

    public static function userId(): ?int
    {
        $user = self::user();
        return $user['id'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function hasRole(array|string $roles): bool
    {
        $currentRole = self::role();
        if (!$currentRole) {
            return false;
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        return in_array($currentRole, $roles, true);
    }

    public static function isSuperAdmin(): bool
    {
        return self::role() === 'super_admin';
    }

    public static function isAdmin(): bool
    {
        return in_array(self::role(), ['super_admin', 'admin'], true);
    }

    public static function isAdvertiser(): bool
    {
        return in_array(self::role(), ['owner', 'broker', 'office', 'admin', 'super_admin'], true);
    }

    public static function logout(): void
    {
        self::start();
        unset($_SESSION['auth_user']);
        session_destroy();
    }
}
