<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';

commar_admin_require_login();

$settings = commar_settings();
$updated = ($_GET['updated'] ?? '') === '1';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['contact_email'] = trim((string) ($_POST['contact_email'] ?? ''));
    $settings['contact_address'] = trim((string) ($_POST['contact_address'] ?? ''));
    $settings['contact_form_email'] = trim((string) ($_POST['contact_form_email'] ?? ''));
    $settings['instagram_url'] = trim((string) ($_POST['instagram_url'] ?? ''));
    $settings['linkedin_url'] = trim((string) ($_POST['linkedin_url'] ?? ''));
    $settings['whatsapp_number'] = preg_replace('/\D+/', '', (string) ($_POST['whatsapp_number'] ?? '')) ?? '';
    $settings['maintenance_enabled'] = isset($_POST['maintenance_enabled']) ? '1' : '0';
    $settings['maintenance_title'] = trim((string) ($_POST['maintenance_title'] ?? ''));
    $settings['maintenance_message'] = trim((string) ($_POST['maintenance_message'] ?? ''));

    if ($settings['contact_email'] !== '' && !filter_var($settings['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email de contacto no es válido.';
    }

    if ($settings['contact_form_email'] !== '' && !filter_var($settings['contact_form_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email de formularios no es válido.';
    }

    foreach (['instagram_url' => 'Instagram', 'linkedin_url' => 'LinkedIn'] as $key => $label) {
        if ($settings[$key] !== '' && !filter_var($settings[$key], FILTER_VALIDATE_URL)) {
            $errors[] = 'La URL de ' . $label . ' no es válida.';
        }
    }

    if ($settings['maintenance_enabled'] === '1' && $settings['maintenance_title'] === '') {
        $errors[] = 'El título de mantenimiento es obligatorio si el sitio está fuera de línea.';
    }

    if ($settings['maintenance_enabled'] === '1' && $settings['maintenance_message'] === '') {
        $errors[] = 'El mensaje de mantenimiento es obligatorio si el sitio está fuera de línea.';
    }

    if ($errors === []) {
        commar_save_settings([
            'contact_email' => $settings['contact_email'],
            'contact_address' => $settings['contact_address'],
            'contact_form_email' => $settings['contact_form_email'],
            'instagram_url' => $settings['instagram_url'],
            'linkedin_url' => $settings['linkedin_url'],
            'whatsapp_number' => $settings['whatsapp_number'],
            'maintenance_enabled' => $settings['maintenance_enabled'],
            'maintenance_title' => $settings['maintenance_title'],
            'maintenance_message' => $settings['maintenance_message'],
        ]);

        header('Location: settings.php?updated=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuraciones | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('settings'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Configuraciones'); ?>
            <main class="admin-content">
                <?php commar_admin_settings_nav('general'); ?>

                <div class="admin-settings-container">
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Configuración actualizada.</p>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="admin-alert admin-alert-error">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo commar_admin_h($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="admin-form">
                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Datos de contacto</span>
                                <h3>Email y dirección</h3>
                            </div>
                            <div class="admin-settings-email-grid">
                                <label>
                                    Email público
                                    <input type="email" name="contact_email" value="<?php echo commar_admin_h((string) $settings['contact_email']); ?>">
                                    <span class="admin-help">Visible en footer y página de contacto.</span>
                                </label>
                                <label>
                                    Email de formularios
                                    <input type="email" name="contact_form_email" value="<?php echo commar_admin_h((string) $settings['contact_form_email']); ?>">
                                    <span class="admin-help">Si se deja vacío, los formularios se envían al email público.</span>
                                </label>
                            </div>
                            <label>
                                Dirección
                                <textarea name="contact_address" rows="4"><?php echo commar_admin_h((string) $settings['contact_address']); ?></textarea>
                            </label>
                        </section>

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Redes y contacto rápido</span>
                                <h3>Footer y botón flotante</h3>
                            </div>
                            <div class="admin-grid">
                                <label>
                                    URL de Instagram
                                    <input type="url" name="instagram_url" value="<?php echo commar_admin_h((string) $settings['instagram_url']); ?>" placeholder="https://instagram.com/...">
                                </label>
                                <label>
                                    URL de LinkedIn
                                    <input type="url" name="linkedin_url" value="<?php echo commar_admin_h((string) $settings['linkedin_url']); ?>" placeholder="https://linkedin.com/company/...">
                                </label>
                            </div>
                            <label>
                                Número de WhatsApp
                                <input type="tel" name="whatsapp_number" value="<?php echo commar_admin_h((string) $settings['whatsapp_number']); ?>" placeholder="5491100000000">
                                <span class="admin-help">Usá código de país y área, sin espacios ni símbolos. Ejemplo: 5491100000000.</span>
                            </label>
                        </section>

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Disponibilidad</span>
                                <h3>Sitio fuera de línea</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="maintenance_enabled" value="1" <?php echo ((string) ($settings['maintenance_enabled'] ?? '0')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Activar página de mantenimiento
                                    <small>Los visitantes verán una pantalla temporal. El backend seguirá disponible.</small>
                                </span>
                            </label>
                            <div class="admin-grid">
                                <label>
                                    Título
                                    <input type="text" name="maintenance_title" value="<?php echo commar_admin_h((string) ($settings['maintenance_title'] ?? 'Sitio en mantenimiento')); ?>" maxlength="140">
                                </label>
                                <label>
                                    Mensaje
                                    <textarea name="maintenance_message" rows="4"><?php echo commar_admin_h((string) ($settings['maintenance_message'] ?? 'Estamos realizando tareas de actualización. Volveremos a estar disponibles en breve.')); ?></textarea>
                                </label>
                            </div>
                            <span class="admin-help">La página también mostrará el email público y el número de WhatsApp configurados arriba.</span>
                        </section>

                        <button type="submit" class="admin-button-primary">Guardar configuración</button>
                    </form>
                </div>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
