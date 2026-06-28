<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/articles.php';

commar_admin_require_login();

$slug = trim((string) ($_GET['slug'] ?? ''));
$article = $slug !== '' ? commar_find_article_by_slug($slug) : null;

if ($article === null) {
    http_response_code(404);
}

$content = $article !== null ? implode("\n\n", $article['content']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar artículo | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('blog'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Editar artículo'); ?>

            <main class="admin-content">
                <div>
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Blog</span>
                            <h2>Editar artículo</h2>
                        </div>
                        <a href="blog.php" class="admin-secondary-link">Volver al listado</a>
                    </div>

                    <?php if ($article === null): ?>
                        <p class="admin-alert admin-alert-error">El artículo solicitado no existe o no se puede editar.</p>
                    <?php else: ?>
                        <?php include __DIR__ . '/article-form.php'; ?>
                    <?php endif; ?>
                </div>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260513-2" defer></script>
</body>
</html>
