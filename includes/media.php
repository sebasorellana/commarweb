<?php
require_once __DIR__ . '/db.php';

if (!function_exists('commar_media_register')) {
    function commar_media_register(string $path, string $type, int $width, int $height, string $alt = ''): void
    {
        $statement = commar_db()->prepare(
            'INSERT INTO commar_media (path, type, alt, width, height, created_at)
             VALUES (:path, :type, :alt, :width, :height, :created_at)
             ON DUPLICATE KEY UPDATE type = VALUES(type), alt = VALUES(alt), width = VALUES(width), height = VALUES(height)'
        );
        $statement->execute([
            'path' => $path,
            'type' => $type,
            'alt' => $alt,
            'width' => $width,
            'height' => $height,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
