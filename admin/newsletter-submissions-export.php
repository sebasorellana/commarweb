<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

$submissions = commar_db()->query(
    'SELECT email, source, page_url, ip_address, user_agent, submitted_at, updated_at
     FROM commar_newsletter_submissions
     ORDER BY submitted_at DESC, id DESC'
)->fetchAll();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="newsletter-submissions-' . date('Ymd-His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
if ($output === false) {
    exit;
}

fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['Email', 'Origen', 'Página', 'IP', 'User agent', 'Suscripto', 'Actualizado']);

foreach ($submissions as $submission) {
    fputcsv($output, [
        (string) $submission['email'],
        (string) $submission['source'],
        (string) $submission['page_url'],
        (string) $submission['ip_address'],
        (string) $submission['user_agent'],
        (string) $submission['submitted_at'],
        (string) $submission['updated_at'],
    ]);
}

fclose($output);
exit;
