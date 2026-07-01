<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';

commar_admin_require_administrator();

$settings = commar_settings();
$updated = ($_GET['updated'] ?? '') === '1';
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    commar_admin_require_valid_csrf();

    $settings['google_tag_manager_id'] = strtoupper(trim((string) ($_POST['google_tag_manager_id'] ?? '')));
    $settings['google_analytics_id'] = strtoupper(trim((string) ($_POST['google_analytics_id'] ?? '')));
    $settings['recaptcha_enabled'] = isset($_POST['recaptcha_enabled']) ? '1' : '0';
    $settings['recaptcha_version'] = (string) ($_POST['recaptcha_version'] ?? 'v3') === 'v2' ? 'v2' : 'v3';
    $settings['recaptcha_site_key'] = trim((string) ($_POST['recaptcha_site_key'] ?? ''));
    $settings['recaptcha_secret_key'] = trim((string) ($_POST['recaptcha_secret_key'] ?? ''));
    $settings['recaptcha_v3_score'] = (string) max(0, min(1, (float) ($_POST['recaptcha_v3_score'] ?? 0.5)));

    if ($settings['google_tag_manager_id'] !== '' && !preg_match('/^GTM-[A-Z0-9_-]+$/', $settings['google_tag_manager_id'])) {
        $errors[] = 'El ID de Google Tag Manager debe tener formato GTM-XXXX.';
    }

    if ($settings['google_analytics_id'] !== '' && !preg_match('/^G-[A-Z0-9]+$/', $settings['google_analytics_id'])) {
        $errors[] = 'El ID de Google Analytics debe tener formato G-XXXX.';
    }

    if ($settings['recaptcha_enabled'] === '1' && ($settings['recaptcha_site_key'] === '' || $settings['recaptcha_secret_key'] === '')) {
        $errors[] = 'Para activar reCAPTCHA tenés que cargar Site key y Secret key.';
    }

    if ($errors === []) {
        commar_save_settings([
            'google_tag_manager_id' => $settings['google_tag_manager_id'],
            'google_analytics_id' => $settings['google_analytics_id'],
            'recaptcha_enabled' => $settings['recaptcha_enabled'],
            'recaptcha_version' => $settings['recaptcha_version'],
            'recaptcha_site_key' => $settings['recaptcha_site_key'],
            'recaptcha_secret_key' => $settings['recaptcha_secret_key'],
            'recaptcha_v3_score' => $settings['recaptcha_v3_score'],
        ]);

        header('Location: settings-integrations.php?updated=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integraciones | MOnkey CMS</title>
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
                <?php commar_admin_settings_nav('integrations'); ?>

                <div class="admin-settings-container">
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Integraciones actualizadas.</p>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="admin-alert admin-alert-error">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo commar_admin_h($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="admin-form admin-settings-form">
                        <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Google</span>
                                <h3>Medición y etiquetas</h3>
                            </div>
                            <div class="admin-grid">
                                <label>
                                    Google Tag Manager
                                    <input type="text" name="google_tag_manager_id" value="<?php echo commar_admin_h((string) ($settings['google_tag_manager_id'] ?? '')); ?>" placeholder="GTM-XXXXXXX">
                                    <span class="admin-help">Se inyecta en el head y el noscript del body.</span>
                                </label>
                                <label>
                                    Google Analytics GA4
                                    <input type="text" name="google_analytics_id" value="<?php echo commar_admin_h((string) ($settings['google_analytics_id'] ?? '')); ?>" placeholder="G-XXXXXXXXXX">
                                    <span class="admin-help">Usá el ID de medición de GA4. Si usás GA desde GTM, podés dejarlo vacío.</span>
                                </label>
                            </div>
                        </section>

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Seguridad</span>
                                <h3>Google reCAPTCHA</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="recaptcha_enabled" value="1" <?php echo ((string) ($settings['recaptcha_enabled'] ?? '0')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Activar reCAPTCHA en formularios públicos
                                    <small>Aplica en contacto, newsletter y postulaciones laborales.</small>
                                </span>
                            </label>
                            <div class="admin-grid">
                                <label>
                                    Versión
                                    <select name="recaptcha_version">
                                        <option value="v3" <?php echo ((string) ($settings['recaptcha_version'] ?? 'v3')) !== 'v2' ? 'selected' : ''; ?>>reCAPTCHA v3 invisible</option>
                                        <option value="v2" <?php echo ((string) ($settings['recaptcha_version'] ?? 'v3')) === 'v2' ? 'selected' : ''; ?>>reCAPTCHA v2 checkbox</option>
                                    </select>
                                </label>
                                <label>
                                    Score mínimo v3
                                    <input type="number" name="recaptcha_v3_score" value="<?php echo commar_admin_h((string) ($settings['recaptcha_v3_score'] ?? '0.5')); ?>" min="0" max="1" step="0.1">
                                    <span class="admin-help">0 permite más envíos; 1 es más estricto.</span>
                                </label>
                            </div>
                            <div class="admin-grid">
                                <label>
                                    Site key
                                    <input type="text" name="recaptcha_site_key" value="<?php echo commar_admin_h((string) ($settings['recaptcha_site_key'] ?? '')); ?>">
                                </label>
                                <label>
                                    Secret key
                                    <input type="password" name="recaptcha_secret_key" value="<?php echo commar_admin_h((string) ($settings['recaptcha_secret_key'] ?? '')); ?>" autocomplete="new-password">
                                </label>
                            </div>
                        </section>

                        <div class="admin-settings-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>Integraciones</span>
                                <button type="submit" class="admin-button-primary">Guardar integraciones</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-mobile-drawer" defer></script>
</body>
</html>
