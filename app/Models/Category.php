<?php
namespace App\Models;

class Category extends Model
{
    protected static string $table = 'property_categories';

    public static function getActive(): array
    {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM properties p WHERE p.category_id = c.id AND p.status = 'approved') as properties_count
                FROM property_categories c WHERE c.is_active = 1 ORDER BY c.sort_order ASC, c.id ASC";
        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }
}
