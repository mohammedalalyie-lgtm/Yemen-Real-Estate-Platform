<?php
namespace App\Models;

class ActivityLog extends Model
{
    protected static string $table = 'activity_logs';

    public static function log(?int $userId, string $action, string $entityType, ?int $entityId = null, ?string $details = null): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);

        self::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'details' => $details
        ]);
    }

    public static function getRecent(int $limit = 20): array
    {
        $sql = "SELECT l.*, u.full_name as user_name, u.email as user_email
                FROM activity_logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.id DESC LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
