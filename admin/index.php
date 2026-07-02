<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/articles.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/integrations.php';

commar_admin_require_login();

function commar_admin_dashboard_count(string $sql, array $params = []): int
{
    try {
        $statement = commar_db()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    } catch (Throwable $exception) {
        return 0;
    }
}

$articles = commar_articles();
$dynamicArticles = commar_dynamic_articles();
$totalArticles = count($articles);
$adminArticles = count($dynamicArticles);
$latestArticles = array_slice($articles, 0, 5);
$publishedWorks = commar_admin_dashboard_count("SELECT COUNT(*) FROM commar_works WHERE status = 'published'");
$activeJobs = commar_admin_dashboard_count("SELECT COUNT(*) FROM commar_jobs WHERE status = 'active'");
$newsletterSubmissions = commar_admin_dashboard_count('SELECT COUNT(*) FROM commar_newsletter_submissions');
$ga4Id = commar_google_analytics_id();
$gtmId = commar_google_tag_manager_id();
$analyticsLabel = $ga4Id !== '' ? $ga4Id : ($gtmId !== '' ? $gtmId : 'Pendiente');
$integrationStatus = $ga4Id !== '' ? 'GA4 activo' : ($gtmId !== '' ? 'GTM activo' : 'Sin configurar');
$lastArticleDate = !empty($latestArticles[0]['published_at'])
    ? date('d/m/Y', strtotime((string) $latestArticles[0]['published_at']))
    : 'Sin fecha';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('dashboard'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Dashboard'); ?>

            <main class="admin-content">
                <section class="admin-dashboard-hero">
                    <div>
                        <span class="admin-kicker">Centro operativo</span>
                        <h2>Vista general del sitio</h2>
                        <p>Contenido, captación y estado de integraciones en una lectura compacta.</p>
                    </div>
                    <a href="article-new.php" class="admin-dashboard-action">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        <span>Nuevo artículo</span>
                    </a>
                </section>

                <section class="admin-stats" aria-label="KPIs principales">
                    <article class="admin-stat-card is-primary">
                        <div class="admin-stat-card-head">
                            <span>Artículos publicados</span>
                            <div class="admin-stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </div>
                        </div>
                        <strong><?php echo $totalArticles; ?></strong>
                        <small><?php echo $adminArticles; ?> creados desde el admin</small>
                    </article>
                    <article class="admin-stat-card">
                        <div class="admin-stat-card-head">
                            <span>Obras visibles</span>
                            <div class="admin-stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 13v.01"/><path d="M9 17v.01"/></svg>
                            </div>
                        </div>
                        <strong><?php echo $publishedWorks; ?></strong>
                        <small>Publicadas en el sitio</small>
                    </article>
                    <article class="admin-stat-card">
                        <div class="admin-stat-card-head">
                            <span>Suscripciones</span>
                            <div class="admin-stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a2 2 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                        </div>
                        <strong><?php echo $newsletterSubmissions; ?></strong>
                        <small>Leads captados por newsletter</small>
                    </article>
                    <article class="admin-stat-card">
                        <div class="admin-stat-card-head">
                            <span>Analytics</span>
                            <div class="admin-stat-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                            </div>
                        </div>
                        <strong><?php echo commar_admin_h($integrationStatus); ?></strong>
                        <small><?php echo commar_admin_h($analyticsLabel); ?></small>
                    </article>
                </section>

                <section class="admin-dashboard-grid">
                    <article class="admin-panel admin-dashboard-panel admin-dashboard-panel-wide">
                        <div class="admin-section-head">
                            <span class="admin-kicker">Analytics</span>
                            <h2>Resumen de medición</h2>
                        </div>
                        <div class="admin-analytics-overview">
                            <div>
                                <strong><?php echo commar_admin_h($analyticsLabel); ?></strong>
                                <span><?php echo $ga4Id !== '' ? 'ID de GA4 configurado en producción.' : 'Para ver usuarios y sesiones reales acá falta conectar GA4 por API.'; ?></span>
                            </div>
                            <a href="settings-integrations.php">Configurar</a>
                        </div>
                        <div class="admin-metric-grid">
                            <div><span>Usuarios</span><strong>-</strong><small>GA4 API</small></div>
                            <div><span>Sesiones</span><strong>-</strong><small>GA4 API</small></div>
                            <div><span>Vistas</span><strong>-</strong><small>GA4 API</small></div>
                            <div><span>Engagement</span><strong>-</strong><small>GA4 API</small></div>
                        </div>
                    </article>

                    <article class="admin-panel admin-dashboard-panel admin-quick-panel">
                        <div class="admin-section-head">
                            <span class="admin-kicker">Operación</span>
                            <h2>Estado rápido</h2>
                        </div>
                        <div class="admin-dashboard-checklist">
                            <div>
                                <span>Último artículo</span>
                                <strong><?php echo commar_admin_h($lastArticleDate); ?></strong>
                            </div>
                            <div>
                                <span>Búsquedas activas</span>
                                <strong><?php echo $activeJobs; ?></strong>
                            </div>
                            <div>
                                <span>Integración principal</span>
                                <strong><?php echo commar_admin_h($integrationStatus); ?></strong>
                            </div>
                        </div>
                    </article>

                    <article class="admin-panel admin-dashboard-panel admin-dashboard-panel-wide">
                        <div class="admin-section-head">
                            <span class="admin-kicker">Blog</span>
                            <h2>Últimos artículos publicados</h2>
                        </div>
                        <div class="admin-ranking">
                            <?php foreach ($latestArticles as $index => $article): ?>
                                <article>
                                    <strong><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></strong>
                                    <div>
                                        <h3><?php echo commar_admin_h($article['title']); ?></h3>
                                        <span><?php echo commar_admin_h($article['category']); ?> / <?php echo commar_admin_h($article['year']); ?></span>
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
