<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/settings.php';
require_once dirname(__DIR__) . '/includes/images.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php');
    exit;
}

commar_admin_require_valid_csrf();

$settings = commar_settings();
$textMode = (string) ($_POST['home_hero_text_mode'] ?? 'animated_static');
if (!in_array($textMode, ['animated_static', 'static_only', 'link'], true)) {
    $textMode = 'animated_static';
}

$settings['home_hero_text_mode'] = $textMode;
$settings['home_hero_animated_text'] = trim((string) ($_POST['home_hero_animated_text'] ?? ''));
$settings['home_hero_static_text'] = trim((string) ($_POST['home_hero_static_text'] ?? ''));
$settings['home_hero_link_text'] = trim((string) ($_POST['home_hero_link_text'] ?? ''));
$settings['home_hero_link_url'] = trim((string) ($_POST['home_hero_link_url'] ?? ''));
$settings['home_hero_carousel_speed'] = (string) max(1500, min(20000, (int) ($_POST['home_hero_carousel_speed'] ?? 5000)));

$uploadedImages = [];
$files = $_FILES['home_hero_images'] ?? null;

if (is_array($files) && isset($files['tmp_name']) && is_array($files['tmp_name'])) {
    $validIndexes = [];
    foreach ($files['tmp_name'] as $index => $tmpName) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $validIndexes[] = $index;
    }

    if (count($validIndexes) > 3) {
        http_response_code(422);
        exit('Podés subir un máximo de 3 imágenes.');
    }

    if (count($validIndexes) > 0) {
        $uploadDir = dirname(__DIR__) . '/img/admin';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            http_response_code(500);
            exit('No se pudo crear la carpeta de imágenes.');
        }

        foreach ($validIndexes as $position => $index) {
            if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                http_response_code(422);
                exit('Una de las imágenes no se pudo cargar correctamente.');
            }

            try {
                $image = commar_admin_store_uploaded_image(
                    (string) $files['tmp_name'][$index],
                    'img/admin/home-hero-' . date('YmdHis') . '-' . ($position + 1),
                    'imagen'
                );
            } catch (RuntimeException $exception) {
                http_response_code(422);
                exit($exception->getMessage());
            }

            $uploadedImages[] = [
                'path' => $image['path'],
                'width' => (int) $image['width'],
                'height' => (int) $image['height'],
            ];
        }
    }
}

if (count($uploadedImages) > 0) {
    $settings['home_hero_images'] = json_encode($uploadedImages, JSON_UNESCAPED_SLASHES);
    $settings['home_hero_image'] = $uploadedImages[0]['path'];
    $settings['home_hero_width'] = (string) $uploadedImages[0]['width'];
    $settings['home_hero_height'] = (string) $uploadedImages[0]['height'];
}

commar_save_settings($settings);

header('Location: home.php?updated=1');
exit;
