<?php
namespace App\Models;

class Report extends Model
{
    protected static string $table = 'reports';

    public static function getPendingReports(): array
    {
        $sql = "SELECT r.*, p.title as property_title, p.slug as property_slug,
                       u.full_name as owner_name, u.phone as owner_phone
                FROM reports r
                JOIN properties p ON r.property_id = p.id
                JOIN users u ON p.user_id = u.id
                WHERE r.status = 'pending'
                ORDER BY r.id DESC";
        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }
}
