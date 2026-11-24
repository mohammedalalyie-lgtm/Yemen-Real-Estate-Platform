<?php
namespace App\Models;

class Office extends Model
{
    protected static string $table = 'offices';

    public static function getOffices(int $limit = 12): array
    {
        $sql = "SELECT o.*, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       (SELECT COUNT(*) FROM properties p WHERE p.office_id = o.id AND p.status = 'approved') as properties_count,
                       (SELECT COUNT(*) FROM office_members om WHERE om.office_id = o.id AND om.status = 'active') as brokers_count
                FROM offices o
                LEFT JOIN governorates g ON o.governorate_id = g.id
                LEFT JOIN districts d ON o.district_id = d.id
                WHERE o.is_active = 1
                ORDER BY o.is_verified DESC, properties_count DESC
                LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findWithStats(int|string $id): ?array
    {
        $sql = "SELECT o.*, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       u.full_name AS owner_name, u.email AS owner_email, u.phone AS owner_phone
                FROM offices o
                LEFT JOIN governorates g ON o.governorate_id = g.id
                LEFT JOIN districts d ON o.district_id = d.id
                JOIN users u ON o.owner_id = u.id
                WHERE (o.id = :id OR o.slug = :slug) AND o.is_active = 1
                LIMIT 1";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => is_numeric($id) ? (int)$id : 0, ':slug' => (string)$id]);
        $office = $stmt->fetch();

        if (!$office) {
            return null;
        }

        // Brokers
        $bSql = "SELECT u.*, om.role_title, om.created_at as joined_at,
                        (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id AND p.status = 'approved') as properties_count
                 FROM office_members om
                 JOIN users u ON om.broker_id = u.id
                 WHERE om.office_id = :oid AND om.status = 'active'";
        $bStmt = self::db()->prepare($bSql);
        $bStmt->execute([':oid' => $office['id']]);
        $office['brokers'] = $bStmt->fetchAll();

        return $office;
    }
}
