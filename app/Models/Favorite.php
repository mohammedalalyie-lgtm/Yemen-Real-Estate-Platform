<?php
namespace App\Models;

class Favorite extends Model
{
    protected static string $table = 'favorites';

    public static function toggle(int $userId, int $propertyId): bool
    {
        $existing = self::findWhereComposite($userId, $propertyId);
        if ($existing) {
            $sql = "DELETE FROM favorites WHERE user_id = :uid AND property_id = :pid";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':uid' => $userId, ':pid' => $propertyId]);
            return false; // Removed
        } else {
            $sql = "INSERT INTO favorites (user_id, property_id) VALUES (:uid, :pid)";
            $stmt = self::db()->prepare($sql);
            $stmt->execute([':uid' => $userId, ':pid' => $propertyId]);
            return true; // Added
        }
    }

    public static function isFavorite(int $userId, int $propertyId): bool
    {
        return self::findWhereComposite($userId, $propertyId) !== null;
    }

    private static function findWhereComposite(int $userId, int $propertyId): ?array
    {
        $sql = "SELECT * FROM favorites WHERE user_id = :uid AND property_id = :pid LIMIT 1";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId, ':pid' => $propertyId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getForUser(int $userId): array
    {
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image,
                       f.created_at AS favorited_at
                FROM favorites f
                JOIN properties p ON f.property_id = p.id
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                WHERE f.user_id = :uid AND p.status = 'approved'
                ORDER BY f.id DESC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
}
