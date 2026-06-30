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
