<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: works.php');
    exit;
}

function commar_admin_work_slug(string $value): string
{
    $value = trim($value);
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = $ascii !== false ? $ascii : $value;
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'obra';
}

function commar_admin_unique_work_slug(PDO $db, string $slug, int $excludeId = 0): string
{
    $baseSlug = $slug;
    $currentSlug = $baseSlug;
    $suffix = 2;

    while (true) {
        $statement = $db->prepare('SELECT id FROM commar_works WHERE slug = :slug AND id <> :id LIMIT 1');
        $statement->execute(['slug' => $currentSlug, 'id' => $excludeId]);
        if (!$statement->fetch()) {
            return $currentSlug;
        }

        $currentSlug = $baseSlug . '-' . $suffix;
        $suffix++;
    }
}

function commar_admin_parse_work_description(string $value): array
{
    $parts = preg_split('/\R{2,}/', trim($value)) ?: [];
    return array_values(array_filter(array_map('trim', $parts), static fn(string $part): bool => $part !== ''));
}

function commar_admin_parse_work_metrics(string $value): array
{
    $metrics = [];
    $lines = preg_split('/\R+/', trim($value)) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        [$label, $metricValue] = array_pad(explode(':', $line, 2), 2, '');
        $label = trim($label);
        $metricValue = trim($metricValue);
        if ($label !== '' && $metricValue !== '') {
            $metrics[$label] = $metricValue;
        }
    }

    return $metrics;
}

$id = (int) ($_POST['id'] ?? 0);
$isEditing = $id > 0;
$title = trim((string) ($_POST['title'] ?? ''));
$slugInput = trim((string) ($_POST['slug'] ?? ''));
$category = trim((string) ($_POST['category'] ?? ''));
$location = trim((string) ($_POST['location'] ?? ''));
$year = trim((string) ($_POST['year'] ?? ''));
$summary = trim((string) ($_POST['summary'] ?? ''));
$intro = trim((string) ($_POST['intro'] ?? ''));
$description = commar_admin_parse_work_description((string) ($_POST['description'] ?? ''));
$metrics = commar_admin_parse_work_metrics((string) ($_POST['metrics'] ?? ''));
$heroAlt = trim((string) ($_POST['hero_alt'] ?? ''));

if ($title === '' || $category === '' || $summary === '' || $intro === '' || empty($description)) {
    http_response_code(422);
    exit('Faltan campos obligatorios.');
}

$db = commar_db();
$slug = commar_admin_unique_work_slug($db, commar_admin_work_slug($slugInput !== '' ? $slugInput : $title), $id);
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

    $imagePath = 'img/obras/' . $slug . '-' . date('YmdHis') . '.' . $extension;
    $targetPath = dirname(__DIR__) . '/' . $imagePath;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        http_response_code(500);
        exit('No se pudo guardar la imagen.');
    }

    $imageWidth = (int) $imageInfo[0];
    $imageHeight = (int) $imageInfo[1];
}

$now = date('Y-m-d H:i:s');
$descriptionJson = json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$metricsJson = json_encode($metrics, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($isEditing) {
    $params = [
        'id' => $id,
        'slug' => $slug,
        'title' => $title,
        'category' => $category,
        'location' => $location,
        'year' => $year,
        'summary' => $summary,
        'hero_alt' => $heroAlt,
        'intro' => $intro,
        'description_json' => $descriptionJson,
        'metrics_json' => $metricsJson,
        'updated_at' => $now,
    ];
    $sql = 'UPDATE commar_works SET slug = :slug, title = :title, category = :category, location = :location, year = :year, summary = :summary, hero_alt = :hero_alt, intro = :intro, description_json = :description_json, metrics_json = :metrics_json, updated_at = :updated_at';

    if ($imagePath !== '') {
        $sql .= ', image = :image, image_width = :image_width, image_height = :image_height';
        $params['image'] = $imagePath;
        $params['image_width'] = $imageWidth;
        $params['image_height'] = $imageHeight;
    }

    $sql .= ' WHERE id = :id';
    $db->prepare($sql)->execute($params);
    header('Location: works.php?updated=1');
    exit;
}

if ($imagePath === '') {
    http_response_code(422);
    exit('La imagen es obligatoria para una nueva obra.');
}

$statement = $db->prepare(
    'INSERT INTO commar_works
     (slug, title, category, location, year, summary, image, image_width, image_height, hero_alt, intro, description_json, metrics_json, status, created_at, updated_at)
     VALUES
     (:slug, :title, :category, :location, :year, :summary, :image, :image_width, :image_height, :hero_alt, :intro, :description_json, :metrics_json, :status, :created_at, :updated_at)'
);
$statement->execute([
    'slug' => $slug,
    'title' => $title,
    'category' => $category,
    'location' => $location,
    'year' => $year,
    'summary' => $summary,
    'image' => $imagePath,
    'image_width' => $imageWidth,
    'image_height' => $imageHeight,
    'hero_alt' => $heroAlt,
    'intro' => $intro,
    'description_json' => $descriptionJson,
    'metrics_json' => $metricsJson,
    'status' => 'published',
    'created_at' => $now,
    'updated_at' => $now,
]);

header('Location: works.php?created=1');
exit;
