<?php
namespace App\Models;

class Setting extends Model
{
    protected static string $table = 'site_settings';

    private static array $cache = [];

    public static function get(string $key, ?string $default = null): ?string
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $row = self::findWhere('setting_key', $key);
        if ($row) {
            self::$cache[$key] = $row['setting_value'];
            return $row['setting_value'];
        }

        return $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general'): bool
    {
        self::$cache[$key] = $value;
        $row = self::findWhere('setting_key', $key);
        if ($row) {
            return self::update($row['id'], ['setting_value' => $value]);
        } else {
            return (bool)self::create([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group
            ]);
        }
    }

    public static function getAllGrouped(): array
    {
        $all = self::all('setting_group ASC, id ASC');
        $grouped = [];
        foreach ($all as $item) {
            $grouped[$item['setting_group']][$item['setting_key']] = $item['setting_value'];
        }
        return $grouped;
    }
}
