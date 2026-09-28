<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';
require_once dirname(__DIR__) . '/includes/images.php';
require_once dirname(__DIR__) . '/includes/media.php';

commar_admin_require_administrator();

$settings = commar_settings();
$updated = ($_GET['updated'] ?? '') === '1';
$errors = [];
$textLength = static fn(string $value): int => function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    commar_admin_require_valid_csrf();

    $title = trim((string) ($_POST['home_social_title'] ?? ''));
    $description = trim((string) ($_POST['home_social_description'] ?? ''));
    $imagePath = trim((string) ($settings['home_social_image'] ?? ''));
    $imageWidth = max(0, (int) ($settings['home_social_image_width'] ?? 0));
    $imageHeight = max(0, (int) ($settings['home_social_image_height'] ?? 0));
    $upload = $_FILES['home_social_image'] ?? [];
    $uploadError = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);

    if ($title === '') {
        $errors[] = 'El título de la tarjeta es obligatorio.';
    } elseif ($textLength($title) > 120) {
        $errors[] = 'El título no puede superar los 120 caracteres.';
    }

    if ($description === '') {
        $errors[] = 'El texto de la tarjeta es obligatorio.';
    } elseif ($textLength($description) > 240) {
        $errors[] = 'El texto no puede superar los 240 caracteres.';
    }

    if ($uploadError !== UPLOAD_ERR_NO_FILE && $errors === []) {
        if ($uploadError !== UPLOAD_ERR_OK) {
            $errors[] = 'La imagen no se pudo cargar correctamente.';
        } elseif ((int) ($upload['size'] ?? 0) > 5 * 1024 * 1024) {
            $errors[] = 'La imagen no puede superar los 5 MB.';
        } else {
            try {
                $imageInfo = @getimagesize((string) ($upload['tmp_name'] ?? ''));
                $imageType = is_array($imageInfo) ? (int) ($imageInfo[2] ?? 0) : 0;
                if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                    throw new RuntimeException('Para máxima compatibilidad con WhatsApp usá una imagen JPG o PNG.');
                }

                $image = commar_admin_store_uploaded_image(
                    (string) ($upload['tmp_name'] ?? ''),
                    'img/admin/home-social-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)),
                    'imagen social',
                    false
                );

                $imagePath = (string) $image['path'];
                $imageWidth = (int) $image['width'];
                $imageHeight = (int) $image['height'];
            } catch (RuntimeException $exception) {
                $errors[] = $exception->getMessage();
            }
        }
    }

    if ($errors === []) {
        commar_save_settings([
            'home_social_title' => $title,
            'home_social_description' => $description,
            'home_social_image' => $imagePath,
            'home_social_image_width' => (string) $imageWidth,
            'home_social_image_height' => (string) $imageHeight,
        ]);

        if ($imagePath !== '') {
            commar_media_register($imagePath, 'image', $imageWidth, $imageHeight, $title);
        }

        header('Location: settings-social-home.php?updated=1');
        exit;
    }

    $settings['home_social_title'] = $title;
    $settings['home_social_description'] = $description;
    $settings['home_social_image'] = $imagePath;
    $settings['home_social_image_width'] = (string) $imageWidth;
    $settings['home_social_image_height'] = (string) $imageHeight;
}

$previewImage = trim((string) ($settings['home_social_image'] ?? ''));
if ($previewImage === '') {
    $previewImage = (string) ($settings['home_hero_image'] ?? 'img/hero-home.jpg');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir Home | MOnkey CMS</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon-32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../img/apple-touch-icon.png">
    <link rel="stylesheet" href="admin.css?v=20260724-social-home">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('settings'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Configuraciones'); ?>
            <main class="admin-content">
                <?php commar_admin_settings_nav('social-home'); ?>

                <div class="admin-settings-container">
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Tarjeta social de la home actualizada.</p>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="admin-alert admin-alert-error">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo commar_admin_h($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data" class="admin-form admin-settings-form admin-social-settings-form">
                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Open Graph</span>
                                <h3>Contenido de la tarjeta</h3>
                            </div>

                            <label>
                                Título
                                <input type="text" name="home_social_title" value="<?php echo commar_admin_h((string) ($settings['home_social_title'] ?? '')); ?>" maxlength="120" required>
                                <span class="admin-help">Recomendado: entre 40 y 60 caracteres.</span>
                            </label>

                            <label>
                                Texto
                                <textarea name="home_social_description" rows="5" maxlength="240" required><?php echo commar_admin_h((string) ($settings['home_social_description'] ?? '')); ?></textarea>
                                <span class="admin-help">Recomendado: entre 110 y 160 caracteres.</span>
                            </label>

                            <label class="admin-file-control">
                                Foto
                                <span class="admin-file-input-wrap">
                                    <span class="admin-file-button">Cambiar imagen</span>
                                    <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                                    <input type="file" name="home_social_image" accept="image/jpeg,image/png" data-file-input>
                                </span>
                                <span class="admin-help">JPG o PNG, hasta 5 MB. Tamaño recomendado: 1200 × 630 px.</span>
                            </label>
                        </section>

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Vista previa</span>
                                <h3>WhatsApp y redes</h3>
                            </div>
                            <article class="admin-social-card-preview">
                                <img src="../<?php echo commar_admin_h($previewImage); ?>" alt="">
                                <div>
                                    <span>commargroup.com.ar</span>
                                    <strong><?php echo commar_admin_h((string) ($settings['home_social_title'] ?? '')); ?></strong>
                                    <p><?php echo commar_admin_h((string) ($settings['home_social_description'] ?? '')); ?></p>
                                </div>
                            </article>
                            <?php if (trim((string) ($settings['home_social_image'] ?? '')) === ''): ?>
                                <p class="admin-help">Hasta que subas una foto se utilizará la imagen principal del hero.</p>
                            <?php endif; ?>
                        </section>

                        <div class="admin-settings-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>La actualización se aplica a la URL de la home.</span>
                                <button type="submit" class="admin-button-primary">Guardar tarjeta</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-media-picker" defer></script>
</body>
</html>
