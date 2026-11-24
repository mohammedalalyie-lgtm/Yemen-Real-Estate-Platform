<?php
namespace App\Models;

use App\Config\Database;
use PDO;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function db(): PDO
    {
        return Database::connect();
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        $sql = "SELECT * FROM `" . static::$table . "` ORDER BY {$orderBy}";
        $stmt = self::db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function find(int|string $id): ?array
    {
        $sql = "SELECT * FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = :id LIMIT 1";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function where(string $column, mixed $value, string $operator = '='): array
    {
        $sql = "SELECT * FROM `" . static::$table . "` WHERE `{$column}` {$operator} :val";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':val' => $value]);
        return $stmt->fetchAll();
    }

    public static function findWhere(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM `" . static::$table . "` WHERE `{$column}` = :val LIMIT 1";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([':val' => $value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $data): int
    {
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = ':' . implode(', :', $columns);

        $sql = "INSERT INTO `" . static::$table . "` (`{$fields}`) VALUES ({$placeholders})";
        $stmt = self::db()->prepare($sql);

        $binds = [];
        foreach ($data as $col => $val) {
            $binds[':' . $col] = $val;
        }

        $stmt->execute($binds);
        return (int)self::db()->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $setClauses = [];
        $binds = [':primary_key' => $id];

        foreach ($data as $col => $val) {
            $setClauses[] = "`{$col}` = :set_{$col}";
            $binds[':set_' . $col] = $val;
        }

        $setString = implode(', ', $setClauses);
        $sql = "UPDATE `" . static::$table . "` SET {$setString} WHERE `" . static::$primaryKey . "` = :primary_key";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute($binds);
    }

    public static function delete(int|string $id): bool
    {
        $sql = "DELETE FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = :id";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public static function count(string $where = '', array $binds = []): int
    {
        $sql = "SELECT COUNT(*) FROM `" . static::$table . "`" . ($where ? " WHERE {$where}" : "");
        $stmt = self::db()->prepare($sql);
        $stmt->execute($binds);
        return (int)$stmt->fetchColumn();
    }
}
