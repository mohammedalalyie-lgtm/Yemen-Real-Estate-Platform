<?php
namespace App\Middleware;

use App\Helpers\Csrf;
use App\Helpers\Session;

class CsrfMiddleware
{
    public static function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Csrf::validate($token)) {
                http_response_code(419);
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['error' => 'انتهت صلاحية جلسة الأمان (CSRF Token Mismatch). يرجى تحديث الصفحة.']);
                    exit;
                }
                die("انتهت صلاحية جلسة الأمان أو الرمز غير صالح (CSRF Token Mismatch). يرجى العودة وتحديث الصفحة ثم المحاولة مجدداً.");
            }
        }
    }
}
