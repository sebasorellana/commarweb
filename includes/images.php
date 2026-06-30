<?php
require_once __DIR__ . '/settings.php';

if (!function_exists('commar_image_webp_enabled')) {
    function commar_image_webp_enabled(): bool
    {
        return (string) commar_setting('image_webp_enabled') === '1';
    }
}

if (!function_exists('commar_image_lazyload_enabled')) {
    function commar_image_lazyload_enabled(): bool
    {
        return (string) commar_setting('image_lazyload_enabled') !== '0';
    }
}

if (!function_exists('commar_image_loading_attr')) {
    function commar_image_loading_attr(string $default = 'lazy'): string
    {
        $default = $default === 'eager' ? 'eager' : 'lazy';
        return $default === 'lazy' && !commar_image_lazyload_enabled() ? 'eager' : $default;
    }
}

if (!function_exists('commar_image_quality')) {
    function commar_image_quality(): int
    {
        return max(40, min(95, (int) commar_setting('image_webp_quality')));
    }
}

if (!function_exists('commar_image_max_width')) {
    function commar_image_max_width(): int
    {
        return max(0, min(5000, (int) commar_setting('image_max_width')));
    }
}

if (!function_exists('commar_image_extension_from_type')) {
    function commar_image_extension_from_type(int $imageType): ?string
    {
        return [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
        ][$imageType] ?? null;
    }
}

if (!function_exists('commar_image_create_from_file')) {
    function commar_image_create_from_file(string $path, int $imageType)
    {
        if ($imageType === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) {
            return @imagecreatefromjpeg($path);
        }

        if ($imageType === IMAGETYPE_PNG && function_exists('imagecreatefrompng')) {
            return @imagecreatefrompng($path);
        }

        if ($imageType === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($path);
        }

        return false;
    }
}

if (!function_exists('commar_image_normalize_canvas')) {
    function commar_image_normalize_canvas($source, int $sourceWidth, int $sourceHeight, int $targetWidth, int $targetHeight)
    {
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if (!$canvas) {
            return false;
        }

        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        return $canvas;
    }
}

if (!function_exists('commar_admin_store_uploaded_image')) {
    function commar_admin_store_uploaded_image(string $tmpName, string $relativePathWithoutExtension, string $errorContext = 'imagen'): array
    {
        $imageInfo = getimagesize($tmpName);
        if ($imageInfo === false) {
            throw new RuntimeException('La ' . $errorContext . ' no es válida.');
        }

        $extension = commar_image_extension_from_type((int) $imageInfo[2]);
        if ($extension === null) {
            throw new RuntimeException('Formato no soportado. Usá JPG, PNG o WEBP.');
        }

        $root = dirname(__DIR__);
        $targetDir = $root . '/' . dirname($relativePathWithoutExtension);
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
            throw new RuntimeException('No se pudo crear la carpeta de imágenes.');
        }

        $sourceWidth = (int) $imageInfo[0];
        $sourceHeight = (int) $imageInfo[1];
        $maxWidth = commar_image_max_width();
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight;

        if ($maxWidth > 0 && $sourceWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = max(1, (int) round($sourceHeight * ($targetWidth / $sourceWidth)));
        }

        if (commar_image_webp_enabled() && function_exists('imagewebp')) {
            $source = commar_image_create_from_file($tmpName, (int) $imageInfo[2]);
            if ($source) {
                if (!imageistruecolor($source) && function_exists('imagepalettetotruecolor')) {
                    imagepalettetotruecolor($source);
                }

                $canvas = commar_image_normalize_canvas($source, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight);
                imagedestroy($source);

                if ($canvas) {
                    $relativePath = $relativePathWithoutExtension . '.webp';
                    $targetPath = $root . '/' . $relativePath;

                    if (@imagewebp($canvas, $targetPath, commar_image_quality())) {
                        imagedestroy($canvas);
                        return ['path' => $relativePath, 'width' => $targetWidth, 'height' => $targetHeight, 'type' => 'webp'];
                    }

                    imagedestroy($canvas);
                }
            }
        }

        $relativePath = $relativePathWithoutExtension . '.' . $extension;
        $targetPath = $root . '/' . $relativePath;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            throw new RuntimeException('No se pudo guardar la ' . $errorContext . '.');
        }

        return ['path' => $relativePath, 'width' => $sourceWidth, 'height' => $sourceHeight, 'type' => $extension];
    }
}

if (!function_exists('commar_image_webp_sibling')) {
    function commar_image_webp_sibling(string $path): string
    {
        return preg_replace('/\.(jpe?g|png)$/i', '.webp', $path) ?? $path;
    }
}

if (!function_exists('commar_image_src')) {
    function commar_image_src(string $path): string
    {
        $path = trim($path);
        if ($path === '' || !commar_image_webp_enabled() || !preg_match('#(^|/)img/.+\.(jpe?g|png)$#i', $path)) {
            return $path;
        }

        $prefix = '';
        $relativePath = $path;
        if (preg_match('#^(https?://[^/]+/)(img/.+)$#i', $path, $matches)) {
            $prefix = $matches[1];
            $relativePath = $matches[2];
        } elseif (str_starts_with($path, '/')) {
            $prefix = '/';
            $relativePath = ltrim($path, '/');
        }

        if (!str_starts_with($relativePath, 'img/')) {
            return $path;
        }

        $webpPath = commar_image_webp_sibling($relativePath);
        if (is_file(dirname(__DIR__) . '/' . $webpPath)) {
            return $prefix . $webpPath;
        }

        return $path;
    }
}

if (!function_exists('commar_image_rewrite_html')) {
    function commar_image_rewrite_html(string $html): string
    {
        if (!commar_image_webp_enabled() || stripos($html, 'img/') === false) {
            return $html;
        }

        return preg_replace_callback(
            '#(?P<prefix>\b(?:src|data-src)\s*=\s*["\']|url\(\s*["\']?)(?P<path>(?:https?://[^"\')\s]+/|/)?img/[^"\')\s]+\.(?:jpe?g|png))#i',
            static function (array $matches): string {
                return $matches['prefix'] . commar_image_src($matches['path']);
            },
            $html
        ) ?? $html;
    }
}

if (!function_exists('commar_image_start_public_rewrite')) {
    function commar_image_start_public_rewrite(): void
    {
        if (PHP_SAPI !== 'cli' && !defined('COMMAR_DISABLE_IMAGE_REWRITE')) {
            ob_start('commar_image_rewrite_html');
        }
    }
}

if (!function_exists('commar_admin_convert_existing_image_to_webp')) {
    function commar_admin_convert_existing_image_to_webp(string $relativePath): array
    {
        $relativePath = ltrim($relativePath, '/');
        if (!preg_match('#^img/[a-zA-Z0-9._/\- %]+\.(jpe?g|png)$#i', $relativePath)) {
            throw new RuntimeException('La imagen no se puede convertir.');
        }

        if (!function_exists('imagewebp')) {
            throw new RuntimeException('El servidor no tiene soporte WebP en GD.');
        }

        $root = dirname(__DIR__);
        $sourcePath = $root . '/' . $relativePath;
        if (!is_file($sourcePath)) {
            throw new RuntimeException('La imagen no existe.');
        }

        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new RuntimeException('La imagen no es válida.');
        }

        $source = commar_image_create_from_file($sourcePath, (int) $imageInfo[2]);
        if (!$source) {
            throw new RuntimeException('No se pudo abrir la imagen.');
        }

        $sourceWidth = (int) $imageInfo[0];
        $sourceHeight = (int) $imageInfo[1];
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight;
        $maxWidth = commar_image_max_width();

        if ($maxWidth > 0 && $sourceWidth > $maxWidth) {
            $targetWidth = $maxWidth;
            $targetHeight = max(1, (int) round($sourceHeight * ($targetWidth / $sourceWidth)));
        }

        if (!imageistruecolor($source) && function_exists('imagepalettetotruecolor')) {
            imagepalettetotruecolor($source);
        }

        $canvas = commar_image_normalize_canvas($source, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight);
        imagedestroy($source);
        if (!$canvas) {
            throw new RuntimeException('No se pudo preparar la imagen.');
        }

        $webpPath = commar_image_webp_sibling($relativePath);
        $targetPath = $root . '/' . $webpPath;
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
            imagedestroy($canvas);
            throw new RuntimeException('No se pudo crear la carpeta de destino.');
        }

        if (!@imagewebp($canvas, $targetPath, commar_image_quality())) {
            imagedestroy($canvas);
            throw new RuntimeException('No se pudo generar el WebP.');
        }

        imagedestroy($canvas);

        return [
            'source' => $relativePath,
            'webp' => $webpPath,
            'width' => $targetWidth,
            'height' => $targetHeight,
            'bytes' => is_file($targetPath) ? (int) filesize($targetPath) : 0,
        ];
    }
}

if (!function_exists('commar_admin_extract_image_paths')) {
    function commar_admin_extract_image_paths($value, array &$paths): void
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                commar_admin_extract_image_paths($item, $paths);
            }
            return;
        }

        $value = (string) $value;
        if ($value === '') {
            return;
        }

        if (preg_match_all('#img/[a-zA-Z0-9._/\- %]+\.(?:jpe?g|png|webp)#i', $value, $matches)) {
            foreach ($matches[0] as $path) {
                $paths[] = $path;
            }
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            commar_admin_extract_image_paths($decoded, $paths);
        }
    }
}

if (!function_exists('commar_admin_used_image_paths')) {
    function commar_admin_used_image_paths(): array
    {
        $paths = [];
        $root = dirname(__DIR__);
        $scanFiles = array_merge(
            glob($root . '/*.php') ?: [],
            glob($root . '/includes/*.php') ?: [],
            [$root . '/style.css']
        );

        foreach ($scanFiles as $file) {
            if (is_file($file)) {
                commar_admin_extract_image_paths((string) file_get_contents($file), $paths);
            }
        }

        try {
            $db = commar_db();
            $settings = $db->query('SELECT setting_value FROM commar_settings')->fetchAll();
            foreach ($settings as $row) {
                commar_admin_extract_image_paths((string) ($row['setting_value'] ?? ''), $paths);
            }

            foreach ([
                ['commar_articles', ['image', 'gallery_json', 'content_json']],
                ['commar_works', ['image', 'gallery_json']],
                ['commar_focused_works', ['image']],
                ['commar_jobs', ['image']],
            ] as [$table, $columns]) {
                $columnSql = implode(', ', array_map(static fn(string $column): string => '`' . $column . '`', $columns));
                try {
                    $rows = $db->query('SELECT ' . $columnSql . ' FROM `' . $table . '`')->fetchAll();
                    foreach ($rows as $row) {
                        foreach ($columns as $column) {
                            commar_admin_extract_image_paths((string) ($row[$column] ?? ''), $paths);
                        }
                    }
                } catch (Throwable $exception) {
                    // Some installs may not have every optional table yet.
                }
            }
        } catch (Throwable $exception) {
            // The static scan still gives a useful inventory if DB is unavailable.
        }

        $paths = array_values(array_unique(array_map(static fn(string $path): string => ltrim($path, '/'), $paths)));
        $paths = array_values(array_filter($paths, static fn(string $path): bool => is_file(dirname(__DIR__) . '/' . $path)));
        natcasesort($paths);

        return array_values($paths);
    }
}

if (!function_exists('commar_admin_image_inventory')) {
    function commar_admin_image_inventory(): array
    {
        $items = [];
        $totalBytes = 0;
        $optimized = 0;
        $pending = 0;
        $webpSupported = function_exists('imagewebp');
        $maxWidth = commar_image_max_width();

        foreach (commar_admin_used_image_paths() as $path) {
            $absolutePath = dirname(__DIR__) . '/' . $path;
            $info = getimagesize($absolutePath);
            if ($info === false) {
                continue;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $webpPath = preg_match('/\.(jpe?g|png)$/i', $path) ? commar_image_webp_sibling($path) : $path;
            $webpExists = $webpPath !== $path && is_file(dirname(__DIR__) . '/' . $webpPath);
            $servedPath = commar_image_src($path);
            $servedInfo = is_file(dirname(__DIR__) . '/' . ltrim(parse_url($servedPath, PHP_URL_PATH) ?: $servedPath, '/'))
                ? getimagesize(dirname(__DIR__) . '/' . ltrim(parse_url($servedPath, PHP_URL_PATH) ?: $servedPath, '/'))
                : false;
            $servedWidth = is_array($servedInfo) ? (int) $servedInfo[0] : (int) $info[0];
            $isWebpServed = strtolower(pathinfo($servedPath, PATHINFO_EXTENSION)) === 'webp';
            $isTooWide = $maxWidth > 0 && $servedWidth > $maxWidth;
            $needsOptimization = in_array($extension, ['jpg', 'jpeg', 'png'], true) && (!$isWebpServed || $isTooWide);
            $bytes = (int) filesize($absolutePath);
            $webpBytes = $webpExists ? (int) filesize(dirname(__DIR__) . '/' . $webpPath) : 0;
            $totalBytes += $bytes;

            if ($needsOptimization) {
                $pending++;
            } else {
                $optimized++;
            }

            $items[] = [
                'path' => $path,
                'served_path' => $servedPath,
                'webp_path' => $webpPath,
                'webp_exists' => $webpExists,
                'extension' => $extension,
                'width' => (int) $info[0],
                'height' => (int) $info[1],
                'bytes' => $bytes,
                'webp_bytes' => $webpBytes,
                'needs_optimization' => $needsOptimization,
            ];
        }

        return [
            'items' => $items,
            'total' => count($items),
            'optimized' => $optimized,
            'pending' => $pending,
            'total_bytes' => $totalBytes,
            'webp_supported' => $webpSupported,
        ];
    }
}
