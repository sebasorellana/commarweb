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

if (!function_exists('commar_maintenance_enabled')) {
    function commar_maintenance_enabled(): bool
    {
        return (string) commar_setting('maintenance_enabled') === '1';
    }
}

if (!function_exists('commar_is_admin_request')) {
    function commar_is_admin_request(): bool
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $requestUri = str_replace('\\', '/', (string) ($_SERVER['REQUEST_URI'] ?? ''));

        return strpos($scriptName, '/admin/') !== false
            || strpos($requestUri, '/admin/') !== false;
    }
}

if (!function_exists('commar_whatsapp_number_label')) {
    function commar_whatsapp_number_label(): string
    {
        $phone = preg_replace('/\D+/', '', (string) commar_setting('whatsapp_number')) ?: '5491100000000';

        return '+' . $phone;
    }
}

if (!function_exists('commar_render_maintenance_page')) {
    function commar_render_maintenance_page(): void
    {
        $title = trim((string) commar_setting('maintenance_title')) ?: 'Sitio en mantenimiento';
        $message = trim((string) commar_setting('maintenance_message')) ?: 'Estamos realizando tareas de actualización. Volveremos a estar disponibles en breve.';
        $email = commar_contact_email();
        $whatsappLabel = commar_whatsapp_number_label();
        $whatsappUrl = commar_whatsapp_url('Hola COMMAR GROUP, quisiera recibir mas informacion.');

        http_response_code(503);
        header('Retry-After: 3600');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> | COMMAR GROUP</title>
    <link rel="icon" type="image/png" href="img/logo-commar-500.png">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem;
            overflow: hidden;
            background:
                radial-gradient(circle at 12% 18%, rgba(36, 120, 190, 0.95), transparent 36rem),
                radial-gradient(circle at 82% 22%, rgba(164, 211, 55, 0.82), transparent 30rem),
                radial-gradient(circle at 62% 86%, rgba(18, 77, 151, 0.86), transparent 32rem),
                linear-gradient(135deg, #07121f 0%, #101820 48%, #17210d 100%);
            color: #f8fafc;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
        }
        body::before {
            background:
                radial-gradient(ellipse at 28% 72%, rgba(255, 255, 255, 0.12), transparent 28rem),
                radial-gradient(ellipse at 74% 64%, rgba(164, 211, 55, 0.18), transparent 24rem);
            filter: blur(34px);
            transform: scale(1.08);
        }
        body::after {
            background: rgba(0, 0, 0, 0.58);
        }
        .maintenance-page {
            position: relative;
            z-index: 1;
            width: min(100%, 760px);
            display: grid;
            gap: 2.5rem;
        }
        .maintenance-brand {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-size: 0.78rem;
            font-weight: 800;
        }
        .maintenance-brand img {
            width: 42px;
            height: 48px;
            object-fit: contain;
        }
        .maintenance-content {
            display: grid;
            gap: 1.25rem;
        }
        .maintenance-kicker {
            margin: 0;
            color: #9ca3af;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }
        h1 {
            margin: 0;
            max-width: 11ch;
            font-size: clamp(2.5rem, 8vw, 6rem);
            line-height: 0.92;
            letter-spacing: 0;
        }
        .maintenance-message {
            margin: 0;
            max-width: 620px;
            color: #d1d5db;
            font-size: clamp(1rem, 2.2vw, 1.25rem);
            line-height: 1.65;
        }
        .maintenance-contact {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            padding-top: 0.5rem;
        }
        .maintenance-contact a {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding: 0.7rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
        }
        .maintenance-footer {
            color: #6b7280;
            font-size: 0.76rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <main class="maintenance-page">
        <div class="maintenance-brand">
            <img src="img/logo-commar-500.png" alt="" width="500" height="578">
            <span>COMMAR GROUP</span>
        </div>
        <section class="maintenance-content" aria-labelledby="maintenance-title">
            <p class="maintenance-kicker">Sitio fuera de línea</p>
            <h1 id="maintenance-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="maintenance-message"><?php echo nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')); ?></p>
            <div class="maintenance-contact" aria-label="Canales de contacto">
                <a href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></a>
                <a href="<?php echo htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($whatsappLabel, ENT_QUOTES, 'UTF-8'); ?></a>
            </div>
        </section>
        <p class="maintenance-footer">Volveremos pronto</p>
    </main>
</body>
</html>
        <?php
        exit;
    }
}

if (PHP_SAPI !== 'cli' && !commar_is_admin_request() && commar_maintenance_enabled()) {
    commar_render_maintenance_page();
}
