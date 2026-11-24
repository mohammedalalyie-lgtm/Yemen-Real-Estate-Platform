<?php
use App\Config\App;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Models\Property;
use App\Models\Report;

$user = Session::user();
$pendingCount = Property::count("status = 'pending'");
$pendingReportsCount = Report::count("status = 'pending'");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'لوحة الإدارة | عقار اليمن') ?></title>
    
    <meta name="csrf-token" content="<?= Csrf::token() ?>">
    <meta name="base-url" content="<?= App::baseUrl('') ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= App::asset('css/custom.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        .admin-sidebar {
            background-color: #0c1a29;
            color: #94A3B8;
            min-height: 100vh;
        }
        .admin-sidebar .nav-link {
            color: #94A3B8;
            font-weight: 600;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            background-color: var(--blue-primary);
            color: #FFFFFF;
        }
    </style>
</head>
<body>

    <div class="d-flex min-vh-100 flex-column flex-md-row">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar col-md-3 col-lg-2 d-flex flex-column justify-content-between p-3">
            <div>
                <div class="text-center pb-3 mb-3 border-bottom border-secondary">
                    <a href="<?= App::baseUrl('') ?>">
                        <img src="<?= App::asset('images/logo.svg') ?>" alt="عقار اليمن" height="42" style="filter: brightness(0) invert(1);">
                    </a>
                    <div class="badge bg-danger mt-2 px-3 py-1">لوحة الإدارة العليا Admin</div>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link <?= str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/admin') ? 'active' : '' ?>" href="<?= App::baseUrl('admin') ?>">
                        <i class="bi bi-speedometer2"></i> الرئيسية
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/properties/pending') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/properties/pending') ?>">
                        <i class="bi bi-patch-check"></i> مراجعة الإعلانات
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge bg-warning text-dark ms-auto"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/properties') && !str_contains($_SERVER['REQUEST_URI'] ?? '', 'pending') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/properties') ?>">
                        <i class="bi bi-buildings"></i> إدارة كل العقارات
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/users') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/users') ?>">
                        <i class="bi bi-people"></i> المستخدمين والصلاحيات
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/reports') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/reports') ?>">
                        <i class="bi bi-shield-exclamation"></i> البلاغات ومكافحة الاحتيال
                        <?php if ($pendingReportsCount > 0): ?>
                            <span class="badge bg-danger ms-auto"><?= $pendingReportsCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/locations') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/locations') ?>">
                        <i class="bi bi-geo-alt"></i> المحافظات والمواقع
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/categories') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/categories') ?>">
                        <i class="bi bi-grid"></i> تصنيفات العقارات
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/plans') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/plans') ?>">
                        <i class="bi bi-box-seam"></i> باقات الاشتراكات
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/logs') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/logs') ?>">
                        <i class="bi bi-journal-text"></i> سجل العمليات والأمان
                    </a>
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/settings') ? 'active' : '' ?>" href="<?= App::baseUrl('admin/settings') ?>">
                        <i class="bi bi-gear"></i> إعدادات المنصة
                    </a>
                </nav>
            </div>

            <div class="pt-3 border-top border-secondary">
                <a href="<?= App::baseUrl('dashboard') ?>" class="nav-link text-warning d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-arrow-left-square"></i> لوحة المعلن
                </a>
                <a href="<?= App::baseUrl('') ?>" class="nav-link text-info d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-house-door"></i> واجهة الموقع
                </a>
                <a href="<?= App::baseUrl('logout') ?>" class="nav-link text-danger d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> خروج
                </a>
            </div>
        </aside>

        <!-- Main Admin Panel Content Area -->
        <div class="col-md-9 col-lg-10 d-flex flex-column">
            <header class="bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="fw-bold m-0 text-navy"><?= htmlspecialchars($title ?? 'لوحة الإدارة') ?></h5>
                    <?php if (Session::isSuperAdmin()): ?>
                        <span class="badge bg-danger"><i class="bi bi-star-fill"></i> Super Admin</span>
                    <?php else: ?>
                        <span class="badge bg-primary">Admin</span>
                    <?php endif; ?>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" id="darkModeToggle" title="تبديل الوضع الليلي">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>
                    <div class="fw-semibold text-muted small">المسؤول: <?= htmlspecialchars($user['full_name']) ?></div>
                </div>
            </header>

            <div class="p-4">
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
</body>
</html>
