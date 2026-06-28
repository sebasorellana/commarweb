<?php
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/settings.php';

if (!function_exists('commar_base_url')) {
    function commar_base_url(): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $basePath = ($scriptDir === '/' || $scriptDir === '.') ? '' : $scriptDir;

        if ($host === 'commar.group' || $host === 'www.commar.group') {
            $basePath = '';
            $scheme = 'https';
        }

        return rtrim($scheme . '://' . $host . $basePath, '/');
    }
}

if (!function_exists('commar_absolute_url')) {
    function commar_absolute_url(string $path = ''): string
    {
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        return rtrim(commar_base_url(), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('commar_whatsapp_url')) {
    function commar_whatsapp_url(?string $customMessage = null): string
    {
        $phone = preg_replace('/\D+/', '', (string) commar_setting('whatsapp_number')) ?: '5491100000000';
        $message = trim((string) ($customMessage ?? 'Hola COMMAR GROUP, quisiera recibir mas informacion.'));

        if ($message === '') {
            $message = 'Hola COMMAR GROUP, quisiera recibir mas informacion.';
        }

        return 'https://wa.me/' . rawurlencode($phone) . '?text=' . rawurlencode($message);
    }
}

if (!function_exists('commar_contact_email')) {
    function commar_contact_email(): string
    {
        return trim((string) commar_setting('contact_email')) ?: 'info@commargroup.com.ar';
    }
}

if (!function_exists('commar_contact_form_email')) {
    function commar_contact_form_email(): string
    {
        return trim((string) commar_setting('contact_form_email')) ?: commar_contact_email();
    }
}

if (!function_exists('commar_contact_address_lines')) {
    function commar_contact_address_lines(): array
    {
        $address = trim((string) commar_setting('contact_address'));
        $lines = preg_split('/\R+/', $address) ?: [];

        return array_values(array_filter(array_map('trim', $lines), static fn(string $line): bool => $line !== ''));
    }
}
