<?php

declare(strict_types=1);

if (!function_exists('commar_is_https')) {
    function commar_is_https(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443') {
            return true;
        }

        return getenv('COMMAR_TRUST_PROXY_HEADERS') === '1'
            && strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    }
}

if (!function_exists('commar_start_secure_session')) {
    function commar_start_secure_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', commar_is_https() ? '1' : '0');
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

if (!function_exists('commar_csrf_token')) {
    function commar_csrf_token(): string
    {
        commar_start_secure_session();

        if (!isset($_SESSION['commar_public_csrf']) || !is_string($_SESSION['commar_public_csrf'])) {
            $_SESSION['commar_public_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['commar_public_csrf'];
    }
}

if (!function_exists('commar_verify_csrf')) {
    function commar_verify_csrf(?string $token = null): bool
    {
        commar_start_secure_session();
        $token = $token ?? (string) ($_POST['csrf_token'] ?? '');
        $storedToken = $_SESSION['commar_public_csrf'] ?? null;

        return is_string($storedToken)
            && $storedToken !== ''
            && $token !== ''
            && hash_equals($storedToken, $token);
    }
}

if (!function_exists('commar_request_path')) {
    function commar_request_path(string $value): string
    {
        $parts = parse_url($value);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
            return '/';
        }

        $path = '/' . ltrim((string) ($parts['path'] ?? '/'), '/');
        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';

        return substr($path . $query, 0, 500);
    }
}

if (!function_exists('commar_public_security_headers')) {
    function commar_public_security_headers(): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
        header('X-Frame-Options: SAMEORIGIN');
    }
}
