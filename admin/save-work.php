<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/projects.php';
require_once dirname(__DIR__) . '/includes/images.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: works.php');
    exit;
}

commar_admin_require_valid_csrf();

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

function commar_admin_work_gallery_from_row(?array $work): array
{
    if (!$work) {
        return [];
    }

    $gallery = json_decode((string) ($work['gallery_json'] ?? '[]'), true);
    $gallery = is_array($gallery) ? array_values(array_filter($gallery, static function ($item): bool {
        return is_array($item) && trim((string) ($item['path'] ?? '')) !== '';
    })) : [];

    if (empty($gallery) && trim((string) ($work['image'] ?? '')) !== '') {
        $gallery[] = [
            'path' => (string) $work['image'],
            'width' => (int) ($work['image_width'] ?? 0),
            'height' => (int) ($work['image_height'] ?? 0),
            'alt' => (string) ($work['hero_alt'] ?? ''),
        ];
    }

    return $gallery;
}

function commar_admin_save_work_gallery_images(string $slug, string $heroAlt, int $availableSlots): array
{
    $gallery = [];
    $files = $_FILES['gallery_images'] ?? null;
    if (!$files || !isset($files['tmp_name']) || $availableSlots <= 0) {
        return $gallery;
    }

    $tmpNames = is_array($files['tmp_name']) ? $files['tmp_name'] : [];

    foreach ($tmpNames as $index => $tmpName) {
        if (count($gallery) >= $availableSlots) {
            break;
        }

        $error = (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($error !== UPLOAD_ERR_OK) {
            http_response_code(422);
            exit('No se pudo subir una de las imágenes.');
        }

        try {
            $image = commar_admin_store_uploaded_image(
                (string) $tmpName,
                'img/obras/' . $slug . '-gallery-' . date('YmdHis') . '-' . ($index + 1),
                'imagen'
            );
        } catch (RuntimeException $exception) {
            http_response_code(500);
            exit($exception->getMessage());
        }

        $gallery[] = [
            'path' => $image['path'],
            'width' => (int) $image['width'],
            'height' => (int) $image['height'],
            'alt' => $heroAlt,
        ];
    }

    return $gallery;
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

$validCategories = array_map(static fn(array $item): string => (string) $item['name'], commar_admin_work_categories());
if (!in_array($category, $validCategories, true)) {
    http_response_code(422);
    exit('La categoría seleccionada no es válida.');
}

$db = commar_db();
$currentWork = null;
if ($isEditing) {
    $currentStatement = $db->prepare("SELECT * FROM commar_works WHERE id = :id AND status <> 'deleted' LIMIT 1");
    $currentStatement->execute(['id' => $id]);
    $currentWork = $currentStatement->fetch();
    if (!$currentWork) {
        http_response_code(404);
        exit('La obra solicitada no existe.');
    }
}

$slug = commar_admin_unique_work_slug($db, commar_admin_work_slug($slugInput !== '' ? $slugInput : $title), $id);
$currentGallery = commar_admin_work_gallery_from_row(is_array($currentWork) ? $currentWork : null);
$existingGallery = [];
$postedExisting = $_POST['gallery_existing'] ?? [];
$postedExisting = is_array($postedExisting) ? array_slice($postedExisting, 0, 10) : [];

foreach ($postedExisting as $galleryPath) {
    $galleryPath = (string) $galleryPath;
    foreach ($currentGallery as $galleryItem) {
        if (($galleryItem['path'] ?? '') === $galleryPath) {
            $galleryItem['alt'] = $heroAlt;
            $existingGallery[] = $galleryItem;
            break;
        }
    }
}

$newGallery = commar_admin_save_work_gallery_images($slug, $heroAlt, 10 - count($existingGallery));
$gallery = array_slice(array_merge($existingGallery, $newGallery), 0, 10);

if (empty($gallery)) {
    http_response_code(422);
    exit('La obra debe tener al menos una imagen.');
}

$primaryImage = $gallery[0];
$now = date('Y-m-d H:i:s');
$descriptionJson = json_encode($description, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$metricsJson = json_encode($metrics, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$galleryJson = json_encode($gallery, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($descriptionJson === false || $metricsJson === false || $galleryJson === false) {
    http_response_code(500);
    exit('No se pudo preparar la información de la obra.');
}

if ($isEditing) {
    $params = [
        'id' => $id,
        'slug' => $slug,
        'title' => $title,
        'category' => $category,
        'location' => $location,
        'year' => $year,
        'summary' => $summary,
        'image' => (string) ($primaryImage['path'] ?? ''),
        'image_width' => (int) ($primaryImage['width'] ?? 0),
        'image_height' => (int) ($primaryImage['height'] ?? 0),
        'gallery_json' => $galleryJson,
        'hero_alt' => $heroAlt,
        'intro' => $intro,
        'description_json' => $descriptionJson,
        'metrics_json' => $metricsJson,
        'updated_at' => $now,
    ];
    $sql = 'UPDATE commar_works SET slug = :slug, title = :title, category = :category, location = :location, year = :year, summary = :summary, image = :image, image_width = :image_width, image_height = :image_height, gallery_json = :gallery_json, hero_alt = :hero_alt, intro = :intro, description_json = :description_json, metrics_json = :metrics_json, updated_at = :updated_at WHERE id = :id';
    $db->prepare($sql)->execute($params);
    header('Location: works.php?updated=1');
    exit;
}

$statement = $db->prepare(
    'INSERT INTO commar_works
     (slug, title, category, location, year, summary, image, image_width, image_height, gallery_json, hero_alt, intro, description_json, metrics_json, status, created_at, updated_at)
     VALUES
     (:slug, :title, :category, :location, :year, :summary, :image, :image_width, :image_height, :gallery_json, :hero_alt, :intro, :description_json, :metrics_json, :status, :created_at, :updated_at)'
);
$statement->execute([
    'slug' => $slug,
    'title' => $title,
    'category' => $category,
    'location' => $location,
    'year' => $year,
    'summary' => $summary,
    'image' => (string) ($primaryImage['path'] ?? ''),
    'image_width' => (int) ($primaryImage['width'] ?? 0),
    'image_height' => (int) ($primaryImage['height'] ?? 0),
    'gallery_json' => $galleryJson,
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
