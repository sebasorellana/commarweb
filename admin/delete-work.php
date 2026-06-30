<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: works.php');
    exit;
}

commar_admin_require_valid_csrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('ID de obra inválido.');
}

$statement = commar_db()->prepare("UPDATE commar_works SET status = 'deleted', deleted_at = NOW(), updated_at = NOW() WHERE id = :id");
$statement->execute(['id' => $id]);

header('Location: works.php?deleted=1');
exit;
