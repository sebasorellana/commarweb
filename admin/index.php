<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/articles.php';

commar_admin_require_login();

$articles = commar_articles();
$dynamicArticles = commar_dynamic_articles();
$totalArticles = count($articles);
$adminArticles = count($dynamicArticles);
$latestArticles = array_slice($articles, 0, 5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('dashboard'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Dashboard'); ?>

            <main class="admin-content">
                <section class="admin-stats">
                    <article class="admin-stat-card">
                        <div class="admin-stat-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        </div>
                        <div>
                            <span>Artículos totales</span>
                            <strong><?php echo $totalArticles; ?></strong>
                        </div>
                    </article>
                    <article class="admin-stat-card">
                        <div class="admin-stat-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        </div>
                        <div>
                            <span>Creados desde admin</span>
                            <strong><?php echo $adminArticles; ?></strong>
                        </div>
                    </article>
                    <article class="admin-stat-card">
                        <div class="admin-stat-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.42 4.58a5.4 5.4 0 0 0-7.65 0l-.7.7a.5.5 0 0 1-.7 0l-.7-.7a5.4 5.4 0 0 0-7.65 0 5.4 5.4 0 0 0 0 7.65l.7.7a.5.5 0 0 1 0 .7l-.7.7a5.4 5.4 0 0 0 0 7.65 5.4 5.4 0 0 0 7.65 0l.7-.7a.5.5 0 0 1 .7 0l.7.7a5.4 5.4 0 0 0 7.65 0 5.4 5.4 0 0 0 0-7.65l-.7-.7a.5.5 0 0 1 0-.7l.7-.7a5.4 5.4 0 0 0 0-7.65Z"/><path d="M12 12v.01"/></svg>
                        </div>
                        <div>
                            <span>Google Tag Manager</span>
                            <strong>Instalado</strong>
                        </div>
                    </article>
                </section>

                <section class="admin-dashboard-grid">
                    <article class="admin-panel">
                        <div class="admin-section-head">
                            <span class="admin-kicker">Analytics</span>
                            <h2>Resumen del sitio</h2>
                        </div>
                        <div class="admin-analytics-empty">
                            <strong>Tag instalado: GTM-P3GC4TVJ</strong>
                            <p>El sitio ya envía eventos a Google Tag Manager. Para mostrar usuarios, sesiones y vistas reales acá hay que conectar la propiedad de GA4 con credenciales de API.</p>
                        </div>
                        <div class="admin-metric-grid">
                            <div><span>Usuarios</span><strong>-</strong></div>
                            <div><span>Sesiones</span><strong>-</strong></div>
                            <div><span>Vistas</span><strong>-</strong></div>
                            <div><span>Engagement</span><strong>-</strong></div>
                        </div>
                    </article>

                    <article class="admin-panel">
                        <div class="admin-section-head">
                            <span class="admin-kicker">Blog</span>
                            <h2>Último ranking de artículos</h2>
                        </div>
                        <div class="admin-ranking">
                            <?php foreach ($latestArticles as $index => $article): ?>
                                <article>
                                    <strong><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></strong>
                                    <div>
                                        <h3><?php echo commar_admin_h($article['title']); ?></h3>
                                        <span><?php echo commar_admin_h($article['category']); ?> // <?php echo commar_admin_h($article['year']); ?></span>
                                    </div>
                                    <a href="../<?php echo commar_admin_h($article['url']); ?>" target="_blank" rel="noopener">Abrir</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </section>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
