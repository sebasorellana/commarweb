<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: focused-works.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('ID de obra inválido.');
}

$statement = commar_db()->prepare('DELETE FROM commar_focused_works WHERE id = :id');
$statement->execute(['id' => $id]);

header('Location: focused-works.php?deleted=1');
exit;