<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/images.php';

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

if (!function_exists('commar_media_kind')) {
    function commar_media_kind(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)) {
            return 'image';
        }
        if (in_array($extension, ['mp4', 'webm', 'mov'], true)) {
            return 'video';
        }
        if (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'], true)) {
            return 'document';
        }

        return 'file';
    }
}

if (!function_exists('commar_media_label')) {
    function commar_media_label(string $kind): string
    {
        return [
            'image' => 'Imagen',
            'video' => 'Video',
            'document' => 'Documento',
            'file' => 'Archivo',
        ][$kind] ?? 'Archivo';
    }
}

if (!function_exists('commar_media_allowed_extensions')) {
    function commar_media_allowed_extensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4', 'webm', 'mov'];
    }
}

if (!function_exists('commar_media_collect_paths')) {
    function commar_media_collect_paths($value, array &$paths): void
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                commar_media_collect_paths($item, $paths);
            }
            return;
        }

        $value = (string) $value;
        if ($value === '') {
            return;
        }

        $extensions = implode('|', array_map('preg_quote', commar_media_allowed_extensions()));
        if (preg_match_all('#(?:img|uploads)/[a-zA-Z0-9._/\- %]+\.(?:' . $extensions . ')#i', $value, $matches)) {
            foreach ($matches[0] as $path) {
                $paths[] = ltrim($path, '/');
            }
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            commar_media_collect_paths($decoded, $paths);
        }
    }
}

if (!function_exists('commar_media_usage_index')) {
    function commar_media_usage_index(): array
    {
        $usage = [];
        $add = static function (string $path, string $section, string $detail = '') use (&$usage): void {
            $path = ltrim($path, '/');
            if ($path === '') {
                return;
            }
            $label = $detail !== '' ? $section . ': ' . $detail : $section;
            $usage[$path][] = $label;
        };

        try {
            $db = commar_db();

            foreach ($db->query('SELECT setting_key, setting_value FROM commar_settings')->fetchAll() as $row) {
                $paths = [];
                commar_media_collect_paths((string) ($row['setting_value'] ?? ''), $paths);
                foreach ($paths as $path) {
                    $add($path, 'Configuraciones', (string) ($row['setting_key'] ?? ''));
                }
            }

            foreach ($db->query('SELECT title, image, gallery_json, content_json FROM commar_articles')->fetchAll() as $row) {
                $paths = [];
                commar_media_collect_paths([$row['image'] ?? '', $row['gallery_json'] ?? '', $row['content_json'] ?? ''], $paths);
                foreach ($paths as $path) {
                    $add($path, 'Blog', (string) ($row['title'] ?? 'Artículo'));
                }
            }

            foreach ($db->query("SELECT title, image, gallery_json FROM commar_works WHERE status <> 'deleted'")->fetchAll() as $row) {
                $paths = [];
                commar_media_collect_paths([$row['image'] ?? '', $row['gallery_json'] ?? ''], $paths);
                foreach ($paths as $path) {
                    $add($path, 'Obras', (string) ($row['title'] ?? 'Obra'));
                }
            }

            foreach ($db->query('SELECT title, image FROM commar_focused_works')->fetchAll() as $row) {
                $paths = [];
                commar_media_collect_paths((string) ($row['image'] ?? ''), $paths);
                foreach ($paths as $path) {
                    $add($path, 'Página Home / Obras en foco', (string) ($row['title'] ?? 'Obra'));
                }
            }

            foreach ($db->query("SELECT title, image FROM commar_jobs WHERE status <> 'deleted'")->fetchAll() as $row) {
                $paths = [];
                commar_media_collect_paths((string) ($row['image'] ?? ''), $paths);
                foreach ($paths as $path) {
                    $add($path, 'Búsqueda laboral', (string) ($row['title'] ?? 'Búsqueda'));
                }
            }

            foreach ($db->query('SELECT cv_path, cv_original_name FROM commar_job_applications')->fetchAll() as $row) {
                $paths = [];
                commar_media_collect_paths((string) ($row['cv_path'] ?? ''), $paths);
                foreach ($paths as $path) {
                    $add($path, 'Postulaciones laborales', (string) ($row['cv_original_name'] ?? 'CV'));
                }
            }
        } catch (Throwable $exception) {
            // The mediateca can still list files if the DB is temporarily unavailable.
        }

        foreach ($usage as $path => $labels) {
            $usage[$path] = array_values(array_unique($labels));
        }

        return $usage;
    }
}

if (!function_exists('commar_media_scan_files')) {
    function commar_media_scan_files(): array
    {
        $root = dirname(__DIR__);
        $directories = [$root . '/img', $root . '/uploads'];
        $files = [];
        $allowed = commar_media_allowed_extensions();
        $usage = commar_media_usage_index();

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $extension = strtolower($file->getExtension());
                if (!in_array($extension, $allowed, true)) {
                    continue;
                }

                $relativePath = ltrim(str_replace($root . '/', '', $file->getPathname()), '/');
                $imageInfo = commar_media_kind($relativePath) === 'image' ? @getimagesize($file->getPathname()) : false;
                $files[] = [
                    'path' => $relativePath,
                    'name' => basename($relativePath),
                    'kind' => commar_media_kind($relativePath),
                    'mime' => mime_content_type($file->getPathname()) ?: '',
                    'bytes' => $file->getSize(),
                    'modified_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'width' => is_array($imageInfo) ? (int) $imageInfo[0] : 0,
                    'height' => is_array($imageInfo) ? (int) $imageInfo[1] : 0,
                    'usage' => $usage[$relativePath] ?? [],
                ];
            }
        }

        usort($files, static fn(array $a, array $b): int => strcmp((string) $b['modified_at'], (string) $a['modified_at']));

        return $files;
    }
}

if (!function_exists('commar_media_save_upload')) {
    function commar_media_save_upload(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo subir el archivo.');
        }

        $originalName = (string) ($file['name'] ?? 'archivo');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, commar_media_allowed_extensions(), true)) {
            throw new RuntimeException('Formato no permitido.');
        }

        $kind = commar_media_kind($originalName);
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $baseName) ?: $baseName)) ?: 'archivo';
        $relativeDir = $kind === 'image' ? 'img/media' : 'uploads/media';
        $targetDir = dirname(__DIR__) . '/' . $relativeDir;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
            throw new RuntimeException('No se pudo crear la carpeta de mediateca.');
        }

        if ($kind === 'image') {
            $stored = commar_admin_store_uploaded_image(
                (string) ($file['tmp_name'] ?? ''),
                $relativeDir . '/' . $baseName . '-' . date('YmdHis'),
                'imagen'
            );
            $relativePath = (string) $stored['path'];
            $imageInfo = [(int) $stored['width'], (int) $stored['height']];
        } else {
            $relativePath = $relativeDir . '/' . $baseName . '-' . date('YmdHis') . '.' . $extension;
            $targetPath = dirname(__DIR__) . '/' . $relativePath;
            if (!move_uploaded_file((string) ($file['tmp_name'] ?? ''), $targetPath)) {
                throw new RuntimeException('No se pudo guardar el archivo.');
            }
            $imageInfo = false;
        }

        commar_media_register(
            $relativePath,
            $kind,
            is_array($imageInfo) ? (int) $imageInfo[0] : 0,
            is_array($imageInfo) ? (int) $imageInfo[1] : 0,
            $originalName
        );

        return ['path' => $relativePath, 'kind' => $kind];
    }
}

if (!function_exists('commar_media_delete')) {
    function commar_media_delete(string $path): bool
    {
        $path = ltrim($path, '/');
        if (!preg_match('#^(img|uploads)/[a-zA-Z0-9._/\- %]+$#', $path)) {
            return false;
        }

        $absolutePath = dirname(__DIR__) . '/' . $path;
        if (!is_file($absolutePath)) {
            return false;
        }

        $deleted = @unlink($absolutePath);
        if ($deleted) {
            try {
                $statement = commar_db()->prepare('DELETE FROM commar_media WHERE path = :path');
                $statement->execute(['path' => $path]);
            } catch (Throwable $exception) {
            }
        }

        return $deleted;
    }
}
