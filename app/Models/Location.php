<?php
namespace App\Models;

class Location extends Model
{
    public static function getGovernorates(bool $activeOnly = true): array
    {
        $sql = "SELECT g.*, 
                       (SELECT COUNT(*) FROM properties p WHERE p.governorate_id = g.id AND p.status = 'approved') as properties_count
                FROM governorates g" . ($activeOnly ? " WHERE g.is_active = 1" : "") . " ORDER BY g.sort_order ASC, g.id ASC";
        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function getDistricts(int $governorateId, bool $activeOnly = true): array
    {
        $sql = "SELECT d.*,
                       (SELECT COUNT(*) FROM properties p WHERE p.district_id = d.id AND p.status = 'approved') as properties_count
                FROM districts d WHERE d.governorate_id = :gov_id" . ($activeOnly ? " AND d.is_active = 1" : "") . " ORDER BY d.sort_order ASC, d.id ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':gov_id' => $governorateId]);
        return $stmt->fetchAll();
    }

    public static function getAreas(int $districtId, bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM areas WHERE district_id = :dist_id" . ($activeOnly ? " AND is_active = 1" : "") . " ORDER BY name_ar ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':dist_id' => $districtId]);
        return $stmt->fetchAll();
    }
}
