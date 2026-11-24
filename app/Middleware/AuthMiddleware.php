<?php
namespace App\Middleware;

use App\Config\App;
use App\Helpers\Session;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Session::check()) {
            Session::setFlash('warning', 'يرجى تسجيل الدخول أولاً للوصول إلى هذه الصفحة.');
            $loginUrl = App::baseUrl('login');
            header("Location: {$loginUrl}");
            exit;
        }
    }
}
