<?php
use App\Config\App;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Models\Notification;

$user = Session::user();
$unreadNotifications = Notification::getUnreadCount($user['id']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'لوحة التحكم | عقار اليمن') ?></title>
    
    <meta name="csrf-token" content="<?= Csrf::token() ?>">
    <meta name="base-url" content="<?= App::baseUrl('') ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= App::asset('css/custom.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
</head>
<body>

    <div class="d-flex min-vh-100 flex-column flex-md-row">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar col-md-3 col-lg-2 d-flex flex-column justify-content-between p-3">
            <div>
                <div class="text-center pb-3 mb-3 border-bottom border-secondary">
                    <a href="<?= App::baseUrl('') ?>">
                        <img src="<?= App::asset('images/logo.svg') ?>" alt="عقار اليمن" height="42" style="filter: brightness(0) invert(1);">
                    </a>
                    <div class="mt-2 text-warning small fw-bold">لوحة تحكم المعلنين</div>
                </div>

                <div class="user-badge-sidebar p-2 mb-3 rounded d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.06);">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;font-size:1.2rem;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white fw-bold text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;">
                            <?= match($user['role']) {
                                'office' => 'مكتب عقاري',
                                'broker' => 'وسيط عقاري',
                                'owner' => 'مالك عقار',
                                'super_admin' => 'المالك الرئيسي',
                                'admin' => 'مدير النظام',
                                default => 'عميل / باحث'
                            } ?>
                        </div>
                    </div>
                </div>

                <nav class="dashboard-nav nav flex-column">
                    <a class="nav-link <?= str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard') ?>">
                        <i class="bi bi-speedometer2"></i> الرئيسية
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard/properties') && !str_contains($_SERVER['REQUEST_URI'] ?? '', 'create') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/properties') ?>">
                        <i class="bi bi-houses"></i> عقاراتي
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'properties/create') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/properties/create') ?>">
                        <i class="bi bi-plus-circle-fill text-warning"></i> إضافة عقار جديد
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard/inquiries') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/inquiries') ?>">
                        <i class="bi bi-chat-left-dots"></i> الاستفسارات الواردة
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard/favorites') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/favorites') ?>">
                        <i class="bi bi-heart"></i> المفضلة
                    </a>
                    <?php if ($user['role'] === 'office'): ?>
                        <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard/office') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/office') ?>">
                            <i class="bi bi-building"></i> إدارة المكتب والوسطاء
                        </a>
                    <?php endif; ?>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard/notifications') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/notifications') ?>">
                        <i class="bi bi-bell"></i> الإشعارات
                        <?php if ($unreadNotifications > 0): ?>
                            <span class="badge bg-danger ms-auto"><?= $unreadNotifications ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard/profile') ? 'active' : '' ?>" href="<?= App::baseUrl('dashboard/profile') ?>">
                        <i class="bi bi-person-gear"></i> الملف الشخصي
                    </a>
                </nav>
            </div>

            <div class="pt-3 border-top border-secondary">
                <a href="<?= App::baseUrl('') ?>" class="nav-link text-info d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-arrow-right-circle"></i> الانتقال للموقع العام
                </a>
                <a href="<?= App::baseUrl('logout') ?>" class="nav-link text-danger d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 d-flex flex-column">
            <!-- Topbar Header -->
            <header class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="fw-bold fs-5 text-navy">
                    <?= htmlspecialchars($title ?? 'لوحة التحكم') ?>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" id="darkModeToggle" title="تبديل الوضع الليلي">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                    <a href="<?= App::baseUrl('dashboard/notifications') ?>" class="btn btn-light position-relative">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if ($unreadNotifications > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $unreadNotifications ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="btn btn-primary btn-sm fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> أضف عقار
                    </a>
                </div>
            </header>

            <!-- Alerts Banner -->
            <div class="p-3">
                <?php foreach (['success', 'danger', 'warning', 'info'] as $msgType): ?>
                    <?php if (Session::hasFlash($msgType)): ?>
                        <div class="alert alert-<?= $msgType ?> alert-dismissible fade show shadow-sm" role="alert">
                            <?= Session::getFlash($msgType) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= App::asset('js/app.js') ?>"></script>
    <script src="<?= App::asset('js/dashboard.js') ?>"></script>
</body>
</html>
