<?php
namespace App\Helpers;

class Csrf
{
    public static function token(): string
    {
        Session::start();
        if (!Session::has('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::set('csrf_token', $token);
        }
        return Session::get('csrf_token');
    }

    public static function field(): string
    {
        $token = self::token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(?string $token): bool
    {
        Session::start();
        $storedToken = Session::get('csrf_token');
        if (!$storedToken || !$token) {
            return false;
        }
        return hash_equals($storedToken, $token);
    }
}
