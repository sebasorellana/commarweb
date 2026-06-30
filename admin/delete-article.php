<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/articles.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blog.php');
    exit;
}

commar_admin_require_valid_csrf();

$slug = trim((string) ($_POST['slug'] ?? ''));
if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    http_response_code(400);
    exit('Artículo inválido.');
}

$article = commar_find_article_by_slug($slug);
if ($article === null) {
    http_response_code(404);
    exit('El artículo no existe.');
}

$statement = commar_db()->prepare('UPDATE commar_articles SET status = :status, deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id');
$statement->execute([
    'id' => $article['id'],
    'status' => 'deleted',
    'deleted_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
]);

header('Location: blog.php?deleted=1');
exit;
