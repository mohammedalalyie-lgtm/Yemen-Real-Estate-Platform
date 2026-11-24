<?php
declare(strict_types=1);

/**
 * عقار اليمن – Yemen Real Estate Platform
 * Database Setup & Installation Script
 */

require_once __DIR__ . '/app/Config/App.php';
\App\Config\App::init();

$host = \App\Config\App::env('DB_HOST', '127.0.0.1');
$port = \App\Config\App::env('DB_PORT', '3306');
$dbname = \App\Config\App::env('DB_NAME', 'yemen_real_estate');
$user = \App\Config\App::env('DB_USER', 'root');
$pass = \App\Config\App::env('DB_PASS', '');

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>تثبيت قاعدة بيانات عقار اليمن</title>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"></head><body class="bg-light p-5"><div class="container bg-white p-4 rounded shadow-sm" style="max-width: 700px;">';
    echo '<h3 class="fw-bold mb-3 text-primary">تثبيت وتهيئة قاعدة بيانات منصة عقار اليمن</h3>';
}

try {
    // 1. Connect without DB to create database if not exists
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");

    $msg1 = "تم التحقق من وجود قاعدة البيانات: {$dbname}";
    echo $isCli ? "[OK] {$msg1}\n" : "<div class='alert alert-success'>{$msg1}</div>";

    // 2. Run Schema
    $schemaFile = __DIR__ . '/database/schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);
        $msg2 = "تم استيراد وإنشاء جميع الجداول بنجاح (schema.sql)";
        echo $isCli ? "[OK] {$msg2}\n" : "<div class='alert alert-success'>{$msg2}</div>";
    }

    // 3. Run Seed
    $seedFile = __DIR__ . '/database/seed.sql';
    if (file_exists($seedFile)) {
        $sql = file_get_contents($seedFile);
        $pdo->exec($sql);
        $msg3 = "تم استيراد البيانات الأولية والمحافظات والعقارات بنجاح (seed.sql)";
        echo $isCli ? "[OK] {$msg3}\n" : "<div class='alert alert-success'>{$msg3}</div>";
    }

    // 4. Ensure upload directories exist
    $uploadDirs = [
        __DIR__ . '/public/uploads/properties',
        __DIR__ . '/public/uploads/avatars',
        __DIR__ . '/public/uploads/offices',
        __DIR__ . '/public/uploads/verifications',
        __DIR__ . '/storage/logs'
    ];
    foreach ($uploadDirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    $msgDone = "اكتمل التثبيت والتهيئة بنجاح!";
    echo $isCli ? "\n=== {$msgDone} ===\n" : "<hr><h4 class='text-success fw-bold'>{$msgDone}</h4>";

    $credentialsInfo = "
بيانات حساب الإدارة الافتراضي (Super Admin):
- البريد: admin@aqar-yemen.com
- كلمة المرور: Admin@123456

حساب مكتب عقاري (Office):
- البريد: albaraka@aqar-yemen.com
- كلمة المرور: Admin@123456

حساب وسيط عقاري (Broker):
- البريد: sami.broker@aqar-yemen.com
- كلمة المرور: Admin@123456

حساب مالك عقار (Owner):
- البريد: owner.saleh@aqar-yemen.com
- كلمة المرور: Admin@123456
";

    if ($isCli) {
        echo $credentialsInfo . "\n";
    } else {
        echo "<pre class='bg-light p-3 border rounded' dir='ltr'>" . htmlspecialchars($credentialsInfo) . "</pre>";
        echo "<div class='mt-3'><a href='public/' class='btn btn-primary fw-bold'>الانتقال إلى الموقع الآن</a></div></div></body></html>";
    }

} catch (PDOException $e) {
    $err = "خطأ في الاتصال أو التثبيت: " . $e->getMessage();
    echo $isCli ? "[ERROR] {$err}\n" : "<div class='alert alert-danger'>{$err}</div></div></body></html>";
}
