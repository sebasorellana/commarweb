<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: focused-works.php');
    exit;
}

$lang = (string) ($_POST['lang'] ?? '');
$workIds = $_POST['work_ids'] ?? [];

if ($lang === '' || !is_array($workIds)) {
    http_response_code(400);
    exit('Datos inválidos.');
}

$db = commar_db();
$db->beginTransaction();

try {
    $updateStmt = $db->prepare('UPDATE commar_focused_works SET display_order = :display_order WHERE id = :id AND lang = :lang');
    foreach ($workIds as $order => $id) {
        $updateStmt->execute(['display_order' => $order, 'id' => (int) $id, 'lang' => $lang]);
    }
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    exit('Error al reordenar: ' . $e->getMessage());
}

header('Location: focused-works.php?reordered=1');
exit;