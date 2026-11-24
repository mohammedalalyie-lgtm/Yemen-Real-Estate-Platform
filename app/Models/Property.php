<?php
namespace App\Models;

use PDO;

class Property extends Model
{
    protected static string $table = 'properties';

    public static function getFeatured(int $limit = 6): array
    {
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name,
                       d.name_ar AS district_name, a.name_ar AS area_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image,
                       u.full_name AS user_name, u.phone AS user_phone, u.role AS user_role, u.is_verified AS user_verified
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                LEFT JOIN areas a ON p.area_id = a.id
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'approved' AND p.is_featured = 1
                ORDER BY p.id DESC
                LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getLatest(int $limit = 6): array
    {
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name,
                       d.name_ar AS district_name, a.name_ar AS area_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image,
                       u.full_name AS user_name, u.phone AS user_phone, u.role AS user_role, u.is_verified AS user_verified
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                LEFT JOIN areas a ON p.area_id = a.id
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'approved'
                ORDER BY p.id DESC
                LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function search(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = ["p.status = 'approved'"];
        $binds = [];

        if (!empty($filters['purpose']) && in_array($filters['purpose'], ['sale', 'rent'])) {
            $where[] = "p.purpose = :purpose";
            $binds[':purpose'] = $filters['purpose'];
        }

        if (!empty($filters['governorate_id'])) {
            $where[] = "p.governorate_id = :gov_id";
            $binds[':gov_id'] = (int)$filters['governorate_id'];
        }

        if (!empty($filters['district_id'])) {
            $where[] = "p.district_id = :dist_id";
            $binds[':dist_id'] = (int)$filters['district_id'];
        }

        if (!empty($filters['area_id'])) {
            $where[] = "p.area_id = :area_id";
            $binds[':area_id'] = (int)$filters['area_id'];
        }

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = :cat_id";
            $binds[':cat_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = "p.price >= :min_price";
            $binds[':min_price'] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "p.price <= :max_price";
            $binds[':max_price'] = (float)$filters['max_price'];
        }

        if (!empty($filters['currency'])) {
            $where[] = "p.currency = :currency";
            $binds[':currency'] = $filters['currency'];
        }

        if (!empty($filters['bedrooms'])) {
            $where[] = "p.bedrooms >= :bedrooms";
            $binds[':bedrooms'] = (int)$filters['bedrooms'];
        }

        if (!empty($filters['bathrooms'])) {
            $where[] = "p.bathrooms >= :bathrooms";
            $binds[':bathrooms'] = (int)$filters['bathrooms'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = "(p.title LIKE :kw OR p.description LIKE :kw OR p.address_details LIKE :kw)";
            $binds[':kw'] = '%' . trim($filters['keyword']) . '%';
        }

        $whereClause = implode(' AND ', $where);

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        $orderBy = match ($sort) {
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'area_desc' => 'p.area_size DESC',
            'views_desc' => 'p.views_count DESC',
            default => 'p.id DESC',
        };

        // Total count
        $countSql = "SELECT COUNT(*) FROM properties p WHERE {$whereClause}";
        $countStmt = self::db()->prepare($countSql);
        $countStmt->execute($binds);
        $total = (int)$countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name,
                       d.name_ar AS district_name, a.name_ar AS area_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image,
                       u.full_name AS user_name, u.phone AS user_phone, u.role AS user_role, u.is_verified AS user_verified
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                LEFT JOIN areas a ON p.area_id = a.id
                JOIN users u ON p.user_id = u.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT :limit OFFSET :offset";

        $stmt = self::db()->prepare($sql);
        foreach ($binds as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int)ceil($total / $perPage),
        ];
    }

    public static function findWithDetails(int|string $id): ?array
    {
        $sql = "SELECT p.*, c.name_ar AS category_name, c.slug AS category_slug,
                       g.name_ar AS governorate_name, d.name_ar AS district_name, a.name_ar AS area_name,
                       u.full_name AS user_name, u.email AS user_email, u.phone AS user_phone,
                       u.whatsapp AS user_whatsapp, u.role AS user_role, u.is_verified AS user_verified,
                       u.avatar AS user_avatar, u.bio AS user_bio,
                       o.name AS office_name, o.slug AS office_slug, o.logo AS office_logo, o.phone AS office_phone
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                LEFT JOIN areas a ON p.area_id = a.id
                JOIN users u ON p.user_id = u.id
                LEFT JOIN offices o ON p.office_id = o.id
                WHERE p.id = :id OR p.slug = :slug
                LIMIT 1";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => is_numeric($id) ? (int)$id : 0, ':slug' => (string)$id]);
        $property = $stmt->fetch();

        if (!$property) {
            return null;
        }

        // Fetch images
        $imgSql = "SELECT * FROM property_images WHERE property_id = :prop_id ORDER BY is_primary DESC, sort_order ASC, id ASC";
        $imgStmt = self::db()->prepare($imgSql);
        $imgStmt->execute([':prop_id' => $property['id']]);
        $property['images'] = $imgStmt->fetchAll();

        // Fetch features
        $featSql = "SELECT f.* FROM features f
                    JOIN property_features pf ON f.id = pf.feature_id
                    WHERE pf.property_id = :prop_id";
        $featStmt = self::db()->prepare($featSql);
        $featStmt->execute([':prop_id' => $property['id']]);
        $property['features'] = $featStmt->fetchAll();

        return $property;
    }

    public static function incrementViews(int $id): void
    {
        $sql = "UPDATE properties SET views_count = views_count + 1 WHERE id = :id";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public static function changeStatus(int $propertyId, string $newStatus, int $userId, ?string $reason = null): bool
    {
        $property = self::find($propertyId);
        if (!$property) {
            return false;
        }

        $prevStatus = $property['status'];

        $updateData = [
            'status' => $newStatus,
            'rejection_reason' => ($newStatus === 'rejected' || $newStatus === 'revision_required') ? $reason : null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($newStatus === 'approved' && empty($property['published_at'])) {
            $updateData['published_at'] = date('Y-m-d H:i:s');
        }

        $res = self::update($propertyId, $updateData);

        if ($res) {
            // Record status history
            $histSql = "INSERT INTO property_status_history (property_id, user_id, previous_status, new_status, reason)
                        VALUES (:pid, :uid, :prev, :new, :reason)";
            $histStmt = self::db()->prepare($histSql);
            $histStmt->execute([
                ':pid' => $propertyId,
                ':uid' => $userId,
                ':prev' => $prevStatus,
                ':new' => $newStatus,
                ':reason' => $reason
            ]);
        }

        return $res;
    }

    public static function getSimilar(int $propertyId, int $categoryId, int $governorateId, int $limit = 4): array
    {
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name,
                       d.name_ar AS district_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                WHERE p.status = 'approved' AND p.id != :id
                  AND (p.category_id = :cat_id OR p.governorate_id = :gov_id)
                ORDER BY p.id DESC
                LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':id', $propertyId, PDO::PARAM_INT);
        $stmt->bindValue(':cat_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':gov_id', $governorateId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function checkDuplicate(string $title, int $governorateId, int $districtId, int $categoryId, ?int $excludeId = null): ?array
    {
        $sql = "SELECT id, title, user_id, status FROM properties
                WHERE governorate_id = :gov_id AND district_id = :dist_id AND category_id = :cat_id
                AND title = :title";
        if ($excludeId) {
            $sql .= " AND id != :exc_id";
        }
        $sql .= " LIMIT 1";

        $stmt = self::db()->prepare($sql);
        $binds = [
            ':gov_id' => $governorateId,
            ':dist_id' => $districtId,
            ':cat_id' => $categoryId,
            ':title' => trim($title)
        ];
        if ($excludeId) {
            $binds[':exc_id'] = $excludeId;
        }
        $stmt->execute($binds);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getAdminStats(): array
    {
        $stats = [
            'total' => self::count(),
            'approved' => self::count("status = 'approved'"),
            'pending' => self::count("status = 'pending'"),
            'rejected' => self::count("status = 'rejected'"),
            'reserved' => self::count("status = 'reserved'"),
            'sold_rented' => self::count("status IN ('sold', 'rented')"),
            'total_views' => (int)self::db()->query("SELECT SUM(views_count) FROM properties")->fetchColumn(),
            'total_inquiries' => (int)self::db()->query("SELECT COUNT(*) FROM inquiries")->fetchColumn(),
        ];
        return $stats;
    }

    public static function getUserStats(int $userId): array
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved,
                    SUM(CASE WHEN status IN ('sold', 'rented') THEN 1 ELSE 0 END) as completed,
                    COALESCE(SUM(views_count), 0) as total_views,
                    COALESCE(SUM(inquiries_count), 0) as total_inquiries
                FROM properties WHERE user_id = :uid";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetch() ?: [];
    }
}
