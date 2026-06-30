<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';

commar_admin_require_administrator();

$settings = commar_settings();
$updated = ($_GET['updated'] ?? '') === '1';
$errors = [];
$webpSupported = function_exists('imagewebp');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    commar_admin_require_valid_csrf();

    $settings['image_webp_enabled'] = isset($_POST['image_webp_enabled']) ? '1' : '0';
    $settings['image_lazyload_enabled'] = isset($_POST['image_lazyload_enabled']) ? '1' : '0';
    $settings['image_webp_quality'] = (string) max(40, min(95, (int) ($_POST['image_webp_quality'] ?? 82)));
    $settings['image_max_width'] = (string) max(0, min(5000, (int) ($_POST['image_max_width'] ?? 2000)));

    if (!$webpSupported && $settings['image_webp_enabled'] === '1') {
        $errors[] = 'El servidor no tiene habilitada la librería GD con soporte WebP. La conversión no podrá aplicarse hasta habilitarla.';
    }

    if ($errors === []) {
        commar_save_settings([
            'image_webp_enabled' => $settings['image_webp_enabled'],
            'image_webp_quality' => $settings['image_webp_quality'],
            'image_max_width' => $settings['image_max_width'],
            'image_lazyload_enabled' => $settings['image_lazyload_enabled'],
        ]);

        header('Location: settings-images.php?updated=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimización de imágenes | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('settings'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Configuraciones'); ?>
            <main class="admin-content">
                <?php commar_admin_settings_nav('images'); ?>

                <div class="admin-settings-container">
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Configuración de imágenes actualizada.</p>
                    <?php endif; ?>
                    <?php if (!$webpSupported): ?>
                        <p class="admin-alert admin-alert-error">Este PHP no tiene soporte WebP en GD. Las imágenes se guardarán en su formato original hasta habilitarlo en el hosting.</p>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="admin-alert admin-alert-error">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo commar_admin_h($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="admin-form admin-settings-form">
                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Formato</span>
                                <h3>Conversión WebP</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="image_webp_enabled" value="1" <?php echo ((string) ($settings['image_webp_enabled'] ?? '1')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Convertir nuevas imágenes a WebP
                                    <small>Aplica a imágenes cargadas desde el admin después de guardar esta configuración.</small>
                                </span>
                            </label>
                            <div class="admin-grid">
                                <label>
                                    Calidad WebP
                                    <input type="number" name="image_webp_quality" value="<?php echo commar_admin_h((string) ($settings['image_webp_quality'] ?? '82')); ?>" min="40" max="95" step="1">
                                    <span class="admin-help">Recomendado: 80 a 85. Más calidad aumenta el peso del archivo.</span>
                                </label>
                                <label>
                                    Ancho máximo
                                    <input type="number" name="image_max_width" value="<?php echo commar_admin_h((string) ($settings['image_max_width'] ?? '2000')); ?>" min="0" max="5000" step="100">
                                    <span class="admin-help">Si una imagen supera este ancho se redimensiona. Usá 0 para no redimensionar.</span>
                                </label>
                            </div>
                        </section>

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Carga frontend</span>
                                <h3>Lazyload</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="image_lazyload_enabled" value="1" <?php echo ((string) ($settings['image_lazyload_enabled'] ?? '1')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Activar lazyload en imágenes secundarias
                                    <small>Al desactivarlo, las imágenes marcadas como lazy se sirven como eager.</small>
                                </span>
                            </label>
                        </section>

                        <div class="admin-settings-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>Los cambios afectan las próximas cargas y el render del frontend.</span>
                                <button type="submit" class="admin-button-primary">Guardar configuración</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
