<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host = App::env('DB_HOST', '127.0.0.1');
            $port = App::env('DB_PORT', '3306');
            $dbname = App::env('DB_NAME', 'yemen_real_estate');
            $user = App::env('DB_USER', 'root');
            $pass = App::env('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Log and show user-friendly message
                error_log("Database Connection Error: " . $e->getMessage());
                if (App::env('APP_DEBUG', 'false') === 'true') {
                    die("خطأ في الاتصال بقاعدة البيانات: " . htmlspecialchars($e->getMessage()));
                } else {
                    die("نعتذر، حدث خطأ أثناء الاتصال بقاعدة البيانات. يرجى المحاولة لاحقاً.");
                }
            }
        }

        return self::$instance;
    }
}
