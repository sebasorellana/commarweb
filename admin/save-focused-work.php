<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: focused-works.php');
    exit;
}

commar_admin_require_valid_csrf();

$id = (int) ($_POST['id'] ?? 0);
$isEditing = $id > 0;

$title = trim((string) ($_POST['title'] ?? ''));
$category = trim((string) ($_POST['category'] ?? ''));
$summary = trim((string) ($_POST['summary'] ?? ''));
$lang = trim((string) ($_POST['lang'] ?? 'es'));

if ($title === '' || $category === '' || $summary === '' || !in_array($lang, ['es', 'en', 'pt'])) {
    http_response_code(422);
    exit('Faltan campos obligatorios o el idioma no es válido.');
}

$imagePath = '';
$imageWidth = 0;
$imageHeight = 0;

if (!empty($_FILES['image']['tmp_name']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
    $tmpName = (string) $_FILES['image']['tmp_name'];
    $imageInfo = getimagesize($tmpName);

    if ($imageInfo === false) {
        http_response_code(422);
        exit('La imagen no es válida.');
    }

    $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    $extension = $extensions[$imageInfo[2]] ?? null;

    if ($extension === null) {
        http_response_code(422);
        exit('Formato no soportado. Usá JPG, PNG o WEBP.');
    }

    $uploadDir = dirname(__DIR__) . '/img/obras';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        http_response_code(500);
        exit('No se pudo crear la carpeta de imágenes.');
    }

    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
    $relativePath = 'img/obras/' . $slug . '-' . date('YmdHis') . '.' . $extension;
    $targetPath = dirname(__DIR__) . '/' . $relativePath;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        http_response_code(500);
        exit('No se pudo guardar la imagen.');
    }

    $imagePath = $relativePath;
    $imageWidth = (int) $imageInfo[0];
    $imageHeight = (int) $imageInfo[1];
}

$db = commar_db();
$now = date('Y-m-d H:i:s');

if ($isEditing) {
    $params = ['id' => $id, 'title' => $title, 'category' => $category, 'summary' => $summary, 'lang' => $lang, 'updated_at' => $now];
    $sql = 'UPDATE commar_focused_works SET title = :title, category = :category, summary = :summary, lang = :lang, updated_at = :updated_at';

    if ($imagePath !== '') {
        $sql .= ', image = :image, image_width = :image_width, image_height = :image_height';
        $params['image'] = $imagePath;
        $params['image_width'] = $imageWidth;
        $params['image_height'] = $imageHeight;
    }

    $sql .= ' WHERE id = :id';
    $statement = $db->prepare($sql);
    $statement->execute($params);
    $redirect = 'focused-works.php?updated=1';
} else {
    if ($imagePath === '') {
        http_response_code(422);
        exit('La imagen es obligatoria para una nueva obra.');
    }

    $orderStmt = $db->prepare('SELECT MAX(display_order) as max_order FROM commar_focused_works WHERE lang = :lang');
    $orderStmt->execute(['lang' => $lang]);
    $maxOrder = (int) $orderStmt->fetchColumn();

    $statement = $db->prepare(
        'INSERT INTO commar_focused_works (lang, title, category, summary, image, image_width, image_height, display_order, created_at, updated_at)
         VALUES (:lang, :title, :category, :summary, :image, :image_width, :image_height, :display_order, :created_at, :updated_at)'
    );
    $statement->execute([
        'lang' => $lang, 'title' => $title, 'category' => $category, 'summary' => $summary, 'image' => $imagePath,
        'image_width' => $imageWidth, 'image_height' => $imageHeight, 'display_order' => $maxOrder + 1,
        'created_at' => $now, 'updated_at' => $now,
    ]);
    $redirect = 'focused-works.php?created=1';
}

header('Location: ' . $redirect);
exit;
