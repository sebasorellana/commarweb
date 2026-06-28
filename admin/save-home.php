<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/settings.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php');
    exit;
}

$extensions = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG => 'png',
    IMAGETYPE_WEBP => 'webp',
];

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

            $tmpName = (string) $files['tmp_name'][$index];
            $imageInfo = getimagesize($tmpName);
            if ($imageInfo === false) {
                http_response_code(422);
                exit('Una de las imágenes no es válida.');
            }

            $extension = $extensions[$imageInfo[2]] ?? null;
            if ($extension === null) {
                http_response_code(422);
                exit('Formato no soportado. Usá JPG, PNG o WEBP.');
            }

            $relativePath = 'img/admin/home-hero-' . date('YmdHis') . '-' . ($position + 1) . '.' . $extension;
            $targetPath = dirname(__DIR__) . '/' . $relativePath;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                http_response_code(500);
                exit('No se pudo guardar una de las imágenes.');
            }

            $uploadedImages[] = [
                'path' => $relativePath,
                'width' => (int) $imageInfo[0],
                'height' => (int) $imageInfo[1],
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
