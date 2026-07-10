<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/images.php';
require_once dirname(__DIR__) . '/includes/media.php';
require_once dirname(__DIR__) . '/includes/page-heroes.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: heros.php');
    exit;
}

commar_admin_require_valid_csrf();

$defaults = commar_default_page_heroes();
$postedHeroes = $_POST['heroes'] ?? [];
if (!is_array($postedHeroes)) {
    header('Location: heros.php?error=' . rawurlencode('No se recibieron heros válidos.'));
    exit;
}

$files = $_FILES['hero_images'] ?? [];
$heroes = [];

foreach ($defaults as $key => $defaultHero) {
    $postedHero = $postedHeroes[$key] ?? [];
    if (!is_array($postedHero)) {
        $postedHero = [];
    }

    $imagePath = trim((string) ($postedHero['image'] ?? $defaultHero['image']));
    $imageWidth = max(0, (int) ($postedHero['width'] ?? $defaultHero['width']));
    $imageHeight = max(0, (int) ($postedHero['height'] ?? $defaultHero['height']));

    $uploadError = $files['error'][$key] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_NO_FILE) {
        if ($uploadError !== UPLOAD_ERR_OK) {
            header('Location: heros.php?error=' . rawurlencode('Una de las imágenes no se pudo cargar correctamente.'));
            exit;
        }

        try {
            $image = commar_admin_store_uploaded_image(
                (string) ($files['tmp_name'][$key] ?? ''),
                'img/heros/' . preg_replace('/[^a-z0-9]+/', '-', strtolower((string) $key)) . '-' . date('YmdHis'),
                'imagen'
            );
        } catch (RuntimeException $exception) {
            header('Location: heros.php?error=' . rawurlencode($exception->getMessage()));
            exit;
        }

        $imagePath = (string) $image['path'];
        $imageWidth = (int) $image['width'];
        $imageHeight = (int) $image['height'];
        commar_media_register($imagePath, 'image', $imageWidth, $imageHeight, (string) ($defaultHero['label'] ?? $key));
    }

    $title = trim((string) ($postedHero['title'] ?? ''));
    $intro = trim((string) ($postedHero['intro'] ?? ''));
    if ($title === '' || $intro === '' || $imagePath === '') {
        header('Location: heros.php?error=' . rawurlencode('Cada hero necesita título, texto introductorio e imagen.'));
        exit;
    }

    $heroes[$key] = [
        'image' => $imagePath,
        'width' => $imageWidth,
        'height' => $imageHeight,
        'kicker' => trim((string) ($postedHero['kicker'] ?? '')),
        'title' => $title,
        'intro' => $intro,
    ];
}

commar_save_page_heroes($heroes);

header('Location: heros.php?updated=1');
exit;
