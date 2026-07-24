<?php
declare(strict_types=1);

define('COMMAR_SKIP_MAINTENANCE', true);
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/integrations.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$email = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$source = trim((string) ($_POST['source'] ?? 'website'));
$source = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $source) ?? 'website';
$source = trim(substr($source, 0, 80), '-') ?: 'website';
$pageUrl = trim((string) ($_POST['page_url'] ?? ''));
$honeypot = trim((string) ($_POST['company_name'] ?? ''));

if (!commar_verify_csrf()) {
    header('Location: ' . commar_url('newsletter-gracias.php?error=1'));
    exit;
}

if ($honeypot !== '') {
    header('Location: ' . commar_url('newsletter-gracias.php'));
    exit;
}

if ($email === false) {
    header('Location: ' . commar_url('newsletter-gracias.php?error=1'));
    exit;
}

if (!commar_recaptcha_verify('newsletter')) {
    header('Location: ' . commar_url('newsletter-gracias.php?error=1'));
    exit;
}

$ipAddress = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
$userAgent = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
$pageUrl = commar_request_path($pageUrl);
$now = date('Y-m-d H:i:s');

$statement = commar_db()->prepare(
    'INSERT INTO commar_newsletter_submissions
        (email, source, page_url, ip_address, user_agent, submitted_at, updated_at)
     VALUES
        (:email, :source, :page_url, :ip_address, :user_agent, :submitted_at, :updated_at)
     ON DUPLICATE KEY UPDATE
        source = VALUES(source),
        page_url = VALUES(page_url),
        ip_address = VALUES(ip_address),
        user_agent = VALUES(user_agent),
        updated_at = VALUES(updated_at)'
);
$statement->execute([
    'email' => strtolower((string) $email),
    'source' => $source,
    'page_url' => $pageUrl,
    'ip_address' => $ipAddress,
    'user_agent' => $userAgent,
    'submitted_at' => $now,
    'updated_at' => $now,
]);

header('Location: ' . commar_url('newsletter-gracias.php?email=' . rawurlencode((string) $email)));
exit;
