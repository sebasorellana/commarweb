<?php
require_once __DIR__ . '/layout.php';

commar_admin_require_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo artículo | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('blog'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Nuevo artículo'); ?>

            <main class="admin-content">
                <div>
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Blog</span>
                            <h2>Crear artículo</h2>
                        </div>
                        <a href="blog.php" class="admin-secondary-link">Volver al listado</a>
                    </div>

                    <?php if (COMMAR_ADMIN_PASSWORD === 'admin123'): ?>
                        <p class="admin-alert admin-alert-warning">Clave inicial activa. Cambiala en <strong>admin/config.php</strong> antes de publicar el panel.</p>
                    <?php endif; ?>

                    <?php include __DIR__ . '/article-form.php'; ?>
                </div>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-featured-media" defer></script>
</body>
</html>
