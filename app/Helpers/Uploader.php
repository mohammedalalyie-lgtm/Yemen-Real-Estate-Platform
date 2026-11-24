<?php
namespace App\Helpers;

class Uploader
{
    private static array $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];

    private static int $maxSizeBytes = 5242880; // 5 MB

    public static function uploadImage(array $file, string $subfolder = 'properties'): ?string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > self::$maxSizeBytes) {
            return null;
        }

        // Validate MIME type safely using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mime, self::$allowedMimes)) {
            return null;
        }

        $extension = self::$allowedMimes[$mime];
        $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;

        $targetDir = dirname(__DIR__, 2) . '/public/uploads/' . trim($subfolder, '/');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetPath = $targetDir . '/' . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $newFilename;
        }

        return null;
    }

    public static function uploadMultipleImages(array $filesArray, string $subfolder = 'properties'): array
    {
        $uploaded = [];
        if (!isset($filesArray['name']) || !is_array($filesArray['name'])) {
            return $uploaded;
        }

        $fileCount = count($filesArray['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($filesArray['error'][$i] === UPLOAD_ERR_OK) {
                $singleFile = [
                    'name' => $filesArray['name'][$i],
                    'type' => $filesArray['type'][$i],
                    'tmp_name' => $filesArray['tmp_name'][$i],
                    'error' => $filesArray['error'][$i],
                    'size' => $filesArray['size'][$i],
                ];

                $filename = self::uploadImage($singleFile, $subfolder);
                if ($filename) {
                    $uploaded[] = $filename;
                }
            }
        }

        return $uploaded;
    }
}
