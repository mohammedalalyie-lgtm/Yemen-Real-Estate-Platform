<?php
namespace App\Models;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return self::findWhere('email', trim(strtolower($email)));
    }

    public static function findByPhone(string $phone): ?array
    {
        return self::findWhere('phone', trim($phone));
    }

    public static function register(array $data): int
    {
        $userData = [
            'full_name' => trim($data['full_name']),
            'email' => trim(strtolower($data['email'])),
            'phone' => trim($data['phone']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'customer',
            'is_verified' => 0,
            'is_active' => 1,
            'bio' => $data['bio'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? $data['phone'] ?? null
        ];

        return self::create($userData);
    }

    public static function authenticate(string $login, string $password): ?array
    {
        // Support login by email or phone
        $sql = "SELECT * FROM users WHERE (email = :login_email OR phone = :login_phone) AND is_active = 1 LIMIT 1";
        $stmt = self::db()->prepare($sql);
        $cleanLogin = trim($login);
        $stmt->execute([':login_email' => $cleanLogin, ':login_phone' => $cleanLogin]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return null;
    }

    public static function getBrokers(int $limit = 10): array
    {
        $sql = "SELECT u.*, o.name AS office_name, o.slug AS office_slug,
                       (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id AND p.status = 'approved') AS properties_count
                FROM users u
                LEFT JOIN offices o ON u.office_id = o.id
                WHERE u.role = 'broker' AND u.is_active = 1
                ORDER BY u.is_verified DESC, properties_count DESC
                LIMIT :limit";
        $stmt = self::db()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getCountsByRole(): array
    {
        $sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
        $stmt = self::db()->query($sql);
        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['role']] = (int)$row['count'];
        }
        return $counts;
    }
}
