<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';

commar_admin_require_login();

$settings = commar_settings();
$updated = ($_GET['updated'] ?? '') === '1';
$heroImages = json_decode((string) ($settings['home_hero_images'] ?? ''), true);
if (!is_array($heroImages) || count($heroImages) === 0) {
    $heroImages = [[
        'path' => (string) $settings['home_hero_image'],
        'width' => (int) $settings['home_hero_width'],
        'height' => (int) $settings['home_hero_height'],
    ]];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Home | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('home'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Página Home'); ?>

            <main class="admin-content">
                <?php commar_admin_home_nav('hero'); ?>

                <div class="admin-dashboard-grid">
                    <section class="admin-panel">
                        <h2>Hero de la home</h2>
                        <?php if ($updated): ?>
                            <p class="admin-alert admin-alert-success">Hero actualizado.</p>
                        <?php endif; ?>
                        <form action="save-home.php" method="post" enctype="multipart/form-data" class="admin-form">
                        <label>
                            Modo del texto principal
                            <select name="home_hero_text_mode">
                                <option value="animated_static" <?php echo ($settings['home_hero_text_mode'] ?? '') === 'animated_static' ? 'selected' : ''; ?>>Texto animado + texto estático</option>
                                <option value="static_only" <?php echo ($settings['home_hero_text_mode'] ?? '') === 'static_only' ? 'selected' : ''; ?>>Solo texto estático</option>
                                <option value="link" <?php echo ($settings['home_hero_text_mode'] ?? '') === 'link' ? 'selected' : ''; ?>>Enlace</option>
                            </select>
                        </label>

                        <label>
                            Texto animado
                            <input type="text" name="home_hero_animated_text" value="<?php echo commar_admin_h((string) ($settings['home_hero_animated_text'] ?? '')); ?>">
                            <span class="admin-help">Separá cada palabra o frase con coma. Ejemplo: arquitectura, pensamiento, estilo.</span>
                        </label>

                        <label>
                            Texto estático
                            <input type="text" name="home_hero_static_text" value="<?php echo commar_admin_h((string) ($settings['home_hero_static_text'] ?? '')); ?>">
                        </label>

                        <div class="admin-grid admin-home-link-grid">
                            <label>
                                Texto del enlace
                                <input type="text" name="home_hero_link_text" value="<?php echo commar_admin_h((string) ($settings['home_hero_link_text'] ?? '')); ?>">
                            </label>
                            <label>
                                URL del enlace
                                <input type="text" name="home_hero_link_url" value="<?php echo commar_admin_h((string) ($settings['home_hero_link_url'] ?? '')); ?>" placeholder="el-estudio.php">
                            </label>
                        </div>

                        <label>
                            Imágenes de fondo
                            <input type="file" name="home_hero_images[]" accept="image/jpeg,image/png,image/webp" multiple>
                            <span class="admin-help">Podés subir hasta 3 imágenes. Si no cargás nuevas, se conservan las actuales.</span>
                        </label>

                        <label>
                            Velocidad del carrusel
                            <input type="number" name="home_hero_carousel_speed" min="1500" max="20000" step="500" value="<?php echo (int) ($settings['home_hero_carousel_speed'] ?? 5000); ?>">
                            <span class="admin-help">Tiempo entre imágenes en milisegundos. Ejemplo: 5000 = 5 segundos.</span>
                        </label>

                            <button type="submit">Actualizar hero</button>
                        </form>
                    </section>

                    <aside class="admin-panel">
                        <h2>Imágenes actuales</h2>
                        <div class="admin-hero-preview-list">
                            <?php foreach (array_slice($heroImages, 0, 3) as $image): ?>
                                <figure class="admin-hero-preview">
                                    <img src="../<?php echo commar_admin_h((string) ($image['path'] ?? '')); ?>" alt="Imagen actual del hero home">
                                    <figcaption><?php echo commar_admin_h((string) ($image['path'] ?? '')); ?></figcaption>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                </div>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
