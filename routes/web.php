<?php

use App\Controllers\HomeController;
use App\Controllers\PropertyController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\OfficeController;
use App\Controllers\BrokerController;
use App\Controllers\InquiryController;
use App\Controllers\FavoriteController;
use App\Controllers\ReportController;
use App\Controllers\ChatbotController;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

class Router
{
    private static array $routes = [];

    public static function get(string $path, array $handler, array $middlewares = []): void
    {
        self::$routes['GET'][$path] = ['handler' => $handler, 'middlewares' => $middlewares];
    }

    public static function post(string $path, array $handler, array $middlewares = []): void
    {
        // By default all POST routes require CsrfMiddleware
        if (!in_array(CsrfMiddleware::class, $middlewares, true)) {
            array_unshift($middlewares, CsrfMiddleware::class);
        }
        self::$routes['POST'][$path] = ['handler' => $handler, 'middlewares' => $middlewares];
    }

    public static function dispatch(string $uri, string $method): void
    {
        // Strip query string
        $path = parse_url($uri, PHP_URL_PATH);

        // Normalize base path if in subfolder
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
        }
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        $routesForMethod = self::$routes[$method] ?? [];

        foreach ($routesForMethod as $routePattern => $routeInfo) {
            $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
            $regex = '#^' . $regex . '$#u';

            if (preg_match($regex, $path, $matches)) {
                // Execute Middlewares
                foreach ($routeInfo['middlewares'] as $middleware) {
                    if (is_array($middleware)) {
                        [$mwClass, $mwArg] = $middleware;
                        $mwClass::handle($mwArg);
                    } else {
                        $middleware::handle();
                    }
                }

                // Filter named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerClass, $action] = $routeInfo['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        \App\Helpers\View::render('errors.404', ['title' => 'الصفحة غير موجودة | 404'], 'main');
    }
}

// -------------------------------------------------------------
// Public Routes
// -------------------------------------------------------------
Router::get('/', [HomeController::class, 'index']);
Router::get('/properties', [PropertyController::class, 'index']);
Router::get('/property/{id}', [PropertyController::class, 'show']);
Router::get('/api/search', [PropertyController::class, 'apiSearch']);
Router::get('/api/districts/{id}', [PropertyController::class, 'apiDistricts']);
Router::get('/api/areas/{id}', [PropertyController::class, 'apiAreas']);

Router::get('/offices', [OfficeController::class, 'index']);
Router::get('/office/{id}', [OfficeController::class, 'show']);
Router::get('/brokers', [BrokerController::class, 'index']);
Router::get('/broker/{id}', [BrokerController::class, 'show']);

// Interactions
Router::post('/inquiry/send', [InquiryController::class, 'store']);
Router::post('/favorite/toggle', [FavoriteController::class, 'toggle']);
Router::post('/report/submit', [ReportController::class, 'store']);
Router::post('/api/chatbot', [ChatbotController::class, 'message']);

// Auth Routes
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/register', [AuthController::class, 'showRegister']);
Router::post('/register', [AuthController::class, 'register']);
Router::get('/forgot-password', [AuthController::class, 'showForgot']);
Router::get('/logout', [AuthController::class, 'logout']);

// -------------------------------------------------------------
// Unified Advertiser Dashboard (Owner, Broker, Office, Admin)
// -------------------------------------------------------------
$dashAuth = [AuthMiddleware::class];

Router::get('/dashboard', [DashboardController::class, 'index'], $dashAuth);
Router::get('/dashboard/properties', [DashboardController::class, 'properties'], $dashAuth);
Router::get('/dashboard/properties/create', [DashboardController::class, 'create'], $dashAuth);
Router::post('/dashboard/properties/store', [DashboardController::class, 'store'], $dashAuth);
Router::get('/dashboard/properties/edit/{id}', [DashboardController::class, 'edit'], $dashAuth);
Router::post('/dashboard/properties/update/{id}', [DashboardController::class, 'update'], $dashAuth);
Router::post('/dashboard/properties/status/{id}', [DashboardController::class, 'updateStatus'], $dashAuth);

Router::get('/dashboard/inquiries', [DashboardController::class, 'inquiries'], $dashAuth);
Router::post('/dashboard/inquiries/read/{id}', [DashboardController::class, 'markInquiryRead'], $dashAuth);
Router::get('/dashboard/favorites', [DashboardController::class, 'favorites'], $dashAuth);
Router::get('/dashboard/profile', [DashboardController::class, 'profile'], $dashAuth);
Router::post('/dashboard/profile/update', [DashboardController::class, 'updateProfile'], $dashAuth);
Router::get('/dashboard/notifications', [DashboardController::class, 'notifications'], $dashAuth);
Router::get('/dashboard/office', [DashboardController::class, 'office'], $dashAuth);

// -------------------------------------------------------------
// Admin & Super Admin Management Routes
// -------------------------------------------------------------
$adminAuth = [[RoleMiddleware::class, ['admin', 'super_admin']]];

Router::get('/admin', [AdminController::class, 'index'], $adminAuth);
Router::get('/admin/properties', [AdminController::class, 'properties'], $adminAuth);
Router::get('/admin/properties/pending', [AdminController::class, 'pending'], $adminAuth);
Router::post('/admin/properties/approve/{id}', [AdminController::class, 'approveProperty'], $adminAuth);
Router::post('/admin/properties/reject/{id}', [AdminController::class, 'rejectProperty'], $adminAuth);
Router::post('/admin/properties/revision/{id}', [AdminController::class, 'requestRevision'], $adminAuth);
Router::post('/admin/properties/featured/{id}', [AdminController::class, 'toggleFeatured'], $adminAuth);

Router::get('/admin/users', [AdminController::class, 'users'], $adminAuth);
Router::post('/admin/users/toggle/{id}', [AdminController::class, 'updateUserStatus'], $adminAuth);
Router::post('/admin/users/role/{id}', [AdminController::class, 'updateUserRole'], $adminAuth);

Router::get('/admin/locations', [AdminController::class, 'locations'], $adminAuth);
Router::get('/admin/categories', [AdminController::class, 'categories'], $adminAuth);
Router::get('/admin/reports', [AdminController::class, 'reports'], $adminAuth);
Router::post('/admin/reports/resolve/{id}', [AdminController::class, 'resolveReport'], $adminAuth);
Router::get('/admin/plans', [AdminController::class, 'plans'], $adminAuth);
Router::get('/admin/logs', [AdminController::class, 'logs'], $adminAuth);
Router::get('/admin/settings', [AdminController::class, 'settings'], $adminAuth);
Router::post('/admin/settings/update', [AdminController::class, 'updateSettings'], $adminAuth);
