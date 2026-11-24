<?php
namespace App\Middleware;

use App\Config\App;
use App\Helpers\Session;

class RoleMiddleware
{
    public static function handle(array|string $allowedRoles): void
    {
        AuthMiddleware::handle();

        if (is_string($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }

        if (!Session::hasRole($allowedRoles)) {
            Session::setFlash('danger', 'عذراً، ليس لديك الصلاحية الكافية للوصول إلى هذا القسم.');
            $homeUrl = App::baseUrl('');
            header("Location: {$homeUrl}");
            exit;
        }
    }
}
