<?php
namespace App\Models;

class Inquiry extends Model
{
    protected static string $table = 'inquiries';

    public static function getForUser(int $userId): array
    {
        $sql = "SELECT i.*, p.title as property_title, p.slug as property_slug,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) as property_image
                FROM inquiries i
                JOIN properties p ON i.property_id = p.id
                WHERE p.user_id = :uid
                ORDER BY i.id DESC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    public static function markAsRead(int $inquiryId, int $ownerId): bool
    {
        $sql = "UPDATE inquiries i
                JOIN properties p ON i.property_id = p.id
                SET i.is_read = 1
                WHERE i.id = :id AND p.user_id = :uid";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([':id' => $inquiryId, ':uid' => $ownerId]);
    }
}
