<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/page-heroes.php';

commar_admin_require_login();

$heroes = commar_page_heroes();
$updated = ($_GET['updated'] ?? '') === '1';
$error = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heros | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css?v=20260709-heros">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('heroes'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Heros'); ?>

            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Contenido del sitio</span>
                            <h2>Heros por sección</h2>
                        </div>
                    </div>

                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Heros actualizados.</p>
                    <?php endif; ?>
                    <?php if ($error !== ''): ?>
                        <p class="admin-alert admin-alert-error"><?php echo commar_admin_h($error); ?></p>
                    <?php endif; ?>

                    <form action="save-heros.php" method="post" enctype="multipart/form-data" class="admin-form admin-page-heroes-form">
                        <div class="admin-page-heroes-list">
                            <?php foreach ($heroes as $key => $hero): ?>
                                <article class="admin-page-hero-card">
                                    <div class="admin-page-hero-preview">
                                        <?php if (($hero['image'] ?? '') !== ''): ?>
                                            <img src="../<?php echo commar_admin_h((string) $hero['image']); ?>" alt="">
                                        <?php else: ?>
                                            <span>Sin imagen</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="admin-page-hero-fields">
                                        <div class="admin-page-hero-head">
                                            <div>
                                                <span class="admin-kicker"><?php echo commar_admin_h((string) ($hero['path'] ?? '')); ?></span>
                                                <h3><?php echo commar_admin_h((string) ($hero['label'] ?? $key)); ?></h3>
                                            </div>
                                            <a href="../<?php echo commar_admin_h((string) ($hero['path'] ?? '')); ?>" target="_blank" rel="noopener" class="admin-secondary-link">Ver sección</a>
                                        </div>

                                        <input type="hidden" name="heroes[<?php echo commar_admin_h((string) $key); ?>][image]" value="<?php echo commar_admin_h((string) ($hero['image'] ?? '')); ?>">
                                        <input type="hidden" name="heroes[<?php echo commar_admin_h((string) $key); ?>][width]" value="<?php echo (int) ($hero['width'] ?? 0); ?>">
                                        <input type="hidden" name="heroes[<?php echo commar_admin_h((string) $key); ?>][height]" value="<?php echo (int) ($hero['height'] ?? 0); ?>">

                                        <div class="admin-form-grid">
                                            <label>
                                                Kicker
                                                <input type="text" name="heroes[<?php echo commar_admin_h((string) $key); ?>][kicker]" value="<?php echo commar_admin_h((string) ($hero['kicker'] ?? '')); ?>">
                                            </label>
                                            <label class="admin-file-control">
                                                Imagen
                                                <span class="admin-file-input-wrap">
                                                    <span class="admin-file-button">Cambiar imagen</span>
                                                    <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                                                    <input type="file" name="hero_images[<?php echo commar_admin_h((string) $key); ?>]" accept="image/jpeg,image/png,image/webp" data-file-input>
                                                </span>
                                            </label>
                                        </div>

                                        <label>
                                            Título
                                            <textarea name="heroes[<?php echo commar_admin_h((string) $key); ?>][title]" rows="3" required><?php echo commar_admin_h((string) ($hero['title'] ?? '')); ?></textarea>
                                        </label>

                                        <label>
                                            Texto introductorio
                                            <textarea name="heroes[<?php echo commar_admin_h((string) $key); ?>][intro]" rows="4" required><?php echo commar_admin_h((string) ($hero['intro'] ?? '')); ?></textarea>
                                        </label>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <div class="admin-settings-savebar admin-page-heroes-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>Los cambios se aplican a los heros públicos del sitio.</span>
                                <button type="submit" class="admin-button-primary">Guardar heros</button>
                            </div>
                        </div>
                    </form>
                </section>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-media-picker" defer></script>
</body>
</html>
