<?php
namespace App\Models;

class Notification extends Model
{
    protected static string $table = 'notifications';

    public static function send(int $userId, string $title, string $message, ?string $link = null, string $type = 'info'): int
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => 0,
            'type' => $type
        ]);
    }

    public static function getUnreadCount(int $userId): int
    {
        return self::count("user_id = :uid AND is_read = 0", [':uid' => $userId]);
    }

    public static function getForUser(int $userId, int $limit = 20): array
    {
        $sql = "SELECT * FROM notifications WHERE user_id = :uid ORDER BY id DESC LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':uid', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function markAllRead(int $userId): bool
    {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :uid";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([':uid' => $userId]);
    }
}
