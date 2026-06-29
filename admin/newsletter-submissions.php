<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

$db = commar_db();
$submissions = $db->query(
    'SELECT * FROM commar_newsletter_submissions ORDER BY submitted_at DESC, id DESC'
)->fetchAll();
$total = count($submissions);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripciones | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('newsletter'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Suscripciones'); ?>
            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Newsletter</span>
                            <h2>Submissions</h2>
                        </div>
                        <a href="newsletter-submissions-export.php" class="admin-primary-link">Exportar CSV</a>
                    </div>

                    <p class="admin-help">Total de suscripciones: <?php echo (int) $total; ?></p>

                    <?php if (empty($submissions)): ?>
                        <p class="admin-empty">Todavía no hay emails suscriptos.</p>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-post-table">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Origen</th>
                                        <th>Página</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $submission): ?>
                                        <tr>
                                            <td>
                                                <div class="admin-post-title">
                                                    <div>
                                                        <strong><?php echo commar_admin_h((string) $submission['email']); ?></strong>
                                                        <span><?php echo commar_admin_h((string) ($submission['ip_address'] ?? '')); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo commar_admin_h((string) $submission['source']); ?></td>
                                            <td><?php echo commar_admin_h((string) $submission['page_url']); ?></td>
                                            <td><?php echo commar_admin_h(date('d/m/Y H:i', strtotime((string) $submission['submitted_at']))); ?></td>
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
