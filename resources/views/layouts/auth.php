<?php
use App\Config\App;
use App\Helpers\Session;
use App\Helpers\Csrf;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'تسجيل الدخول | عقار اليمن') ?></title>
    
    <meta name="csrf-token" content="<?= Csrf::token() ?>">
    <meta name="base-url" content="<?= App::baseUrl('') ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= App::asset('css/custom.css') ?>">
    <style>
        .auth-container {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(16, 36, 58, 0.96) 0%, rgba(26, 52, 80, 0.94) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .auth-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
            max-width: 520px;
            width: 100%;
            padding: 36px;
            border: 1px solid rgba(200, 169, 107, 0.25);
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <a href="<?= App::baseUrl('') ?>">
                    <img src="<?= App::asset('images/logo.svg') ?>" alt="عقار اليمن" height="56">
                </a>
            </div>

            <!-- Flash alerts -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= App::asset('js/app.js') ?>"></script>
</body>
</html>
