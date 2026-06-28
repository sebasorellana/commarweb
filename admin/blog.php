<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/articles.php';

commar_admin_require_login();

$created = ($_GET['created'] ?? '') === '1';
$updated = ($_GET['updated'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$dynamicArticles = commar_dynamic_articles(false);
$allArticles = $dynamicArticles;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('blog'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Blog'); ?>

            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Artículos</span>
                            <h2>Listado de artículos</h2>
                        </div>
                        <a href="article-new.php" class="admin-primary-link">Nuevo artículo</a>
                    </div>

                    <?php if ($created): ?>
                        <p class="admin-alert admin-alert-success">Artículo creado y publicado.</p>
                    <?php endif; ?>
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Artículo actualizado.</p>
                    <?php endif; ?>
                    <?php if ($deleted): ?>
                        <p class="admin-alert admin-alert-success">Artículo eliminado.</p>
                    <?php endif; ?>

                    <?php if (!$allArticles): ?>
                        <p class="admin-empty">Todavía no hay artículos publicados.</p>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-post-table">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Categoría</th>
                                        <th>Año</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allArticles as $article): ?>
                                        <?php
                                        $isAdminArticle = ($article['source'] ?? '') === 'admin';
                                        $slug = (string) ($article['slug'] ?? '');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="admin-post-title">
                                                    <img src="../<?php echo commar_admin_h($article['image']); ?>" alt="" width="64" height="64">
                                                    <div>
                                                        <strong><?php echo commar_admin_h($article['title']); ?></strong>
                                                        <span><?php echo commar_admin_h($article['description']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo commar_admin_h($article['category']); ?></td>
                                            <td><?php echo commar_admin_h($article['year']); ?></td>
                                            <td><span class="admin-status-pill <?php echo ($article['status'] ?? 'published') === 'draft' ? 'is-draft' : 'is-published'; ?>"><?php echo commar_admin_h(($article['status'] ?? 'published') === 'draft' ? 'Borrador' : 'Publicado'); ?></span></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <?php if ($isAdminArticle): ?>
                                                        <a href="../<?php echo commar_admin_h($article['url']); ?>" target="_blank" rel="noopener" class="admin-button-icon" title="Ver artículo">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                        </a>
                                                        <a href="article-edit.php?slug=<?php echo rawurlencode($slug); ?>" class="admin-button-icon" title="Editar artículo">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                                        </a>
                                                        <form action="delete-article.php" method="post" onsubmit="return confirm('¿Eliminar este artículo? Esta acción no se puede deshacer.');">
                                                            <input type="hidden" name="slug" value="<?php echo commar_admin_h($slug); ?>">
                                                            <button type="submit" class="admin-button-icon admin-button-danger" title="Eliminar artículo">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <a href="../<?php echo commar_admin_h($article['url']); ?>" target="_blank" rel="noopener" class="admin-button-icon" title="Ver artículo">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
