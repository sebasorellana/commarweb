<?php

declare(strict_types=1);

if (!function_exists('commar_cache_directory')) {
    function commar_cache_directory(): string
    {
        return dirname(__DIR__) . '/data/cache';
    }
}

if (!function_exists('commar_cache_path')) {
    function commar_cache_path(string $key): string
    {
        return commar_cache_directory() . '/' . hash('sha256', $key) . '.cache';
    }
}

if (!function_exists('commar_cache_get')) {
    function commar_cache_get(string $key)
    {
        $path = commar_cache_path($key);
        if (!is_file($path)) {
            return null;
        }

        $payload = @file_get_contents($path);
        $data = is_string($payload)
            ? @unserialize($payload, ['allowed_classes' => false])
            : null;

        if (
            !is_array($data)
            || !isset($data['expires_at'])
            || (int) $data['expires_at'] < time()
            || !array_key_exists('value', $data)
        ) {
            @unlink($path);
            return null;
        }

        return $data['value'];
    }
}

if (!function_exists('commar_cache_set')) {
    function commar_cache_set(string $key, $value, int $ttl): bool
    {
        $directory = commar_cache_directory();
        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        $temporaryPath = @tempnam($directory, 'commar-cache-');
        if (!is_string($temporaryPath)) {
            return false;
        }

        $payload = serialize([
            'expires_at' => time() + max(1, $ttl),
            'value' => $value,
        ]);
        $written = @file_put_contents($temporaryPath, $payload, LOCK_EX);
        if ($written === false) {
            @unlink($temporaryPath);
            return false;
        }

        @chmod($temporaryPath, 0664);
        if (!@rename($temporaryPath, commar_cache_path($key))) {
            @unlink($temporaryPath);
            return false;
        }

        return true;
    }
}

if (!function_exists('commar_cache_clear')) {
    function commar_cache_clear(): array
    {
        $files = glob(commar_cache_directory() . '/*.cache') ?: [];
        $removed = 0;
        $bytes = 0;

        foreach ($files as $path) {
            if (!is_file($path)) {
                continue;
            }

            $size = filesize($path);
            if (@unlink($path)) {
                $removed++;
                $bytes += $size !== false ? $size : 0;
            }
        }

        return ['files' => $removed, 'bytes' => $bytes];
    }
}

if (!function_exists('commar_cache_stats')) {
    function commar_cache_stats(): array
    {
        $directory = commar_cache_directory();
        $files = glob($directory . '/*.cache') ?: [];
        $bytes = 0;
        $newest = 0;

        foreach ($files as $path) {
            if (!is_file($path)) {
                continue;
            }
            $size = filesize($path);
            $modifiedAt = filemtime($path);
            $bytes += $size !== false ? $size : 0;
            $newest = max($newest, $modifiedAt !== false ? $modifiedAt : 0);
        }

        $writable = is_dir($directory) ? is_writable($directory) : is_writable(dirname($directory));

        return [
            'files' => count($files),
            'bytes' => $bytes,
            'newest_at' => $newest,
            'writable' => $writable,
            'directory' => $directory,
        ];
    }
}

if (!function_exists('commar_cache_remember')) {
    function commar_cache_remember(string $key, callable $resolver)
    {
        if (!function_exists('commar_cache_enabled') || !commar_cache_enabled()) {
            return $resolver();
        }

        $cached = commar_cache_get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $resolver();
        commar_cache_set($key, $value, commar_cache_ttl());

        return $value;
    }
}
