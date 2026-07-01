<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/articles.php';
require_once dirname(__DIR__) . '/includes/media.php';
require_once dirname(__DIR__) . '/includes/images.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

commar_admin_require_valid_csrf();

function commar_admin_slugify(string $value): string
{
    $value = trim(commar_text_lower($value));
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = $converted !== false ? $converted : $value;
    $value = preg_replace('/[^a-z0-9]+/', '-', strtolower($value)) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'articulo';
}

function commar_admin_get_youtube_embed_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    // Regex to find YouTube video ID from common URL formats.
    $regex = '/(?:youtube(?:-nocookie)?\.com\/(?:.*[?&]v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    if (preg_match($regex, $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    // If user just pasted an ID
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
        return 'https://www.youtube.com/embed/' . $url;
    }

    return ''; // Return empty if not a valid YouTube URL or ID
}

function commar_admin_sanitize_article_html(string $html): string
{
    $html = trim($html);

    if ($html === '') {
        return '';
    }

    $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html) ?? '';
    $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html) ?? '';
    $html = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/is', '', $html) ?? '';
    $html = preg_replace('/\s+(style|class|id|data-[a-z0-9_-]+)\s*=\s*(["\']).*?\2/is', '', $html) ?? '';
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/is', '$1="#"', $html) ?? '';

    return trim(strip_tags($html, '<p><br><strong><b><em><i><u><s><ul><ol><li><h2><h3><blockquote><a><pre><code>'));
}

function commar_admin_unique_slug(string $slug, string $dataDir, ?string $currentSlug = null): string
{
    $candidate = $slug;
    $index = 2;

    while (true) {
        $statement = commar_db()->prepare('SELECT slug FROM commar_articles WHERE slug = :slug LIMIT 1');
        $statement->execute(['slug' => $candidate]);
        $exists = (bool) $statement->fetch();

        if (!$exists) {
            break;
        }

        if ($currentSlug !== null && $candidate === $currentSlug) {
            break;
        }

        $candidate = $slug . '-' . $index;
        $index++;
    }

    return $candidate;
}

function commar_admin_save_image(string $slug, ?array $currentArticle = null): array
{
    if (empty($_FILES['image']['tmp_name']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $mediaImage = commar_media_image_from_path((string) ($_POST['media_featured_image'] ?? ''));
        if ($mediaImage !== null) {
            return [
                'path' => $mediaImage['path'],
                'width' => $mediaImage['width'],
                'height' => $mediaImage['height'],
            ];
        }

        $generatedImage = trim((string) ($_POST['generated_image'] ?? ''));
        if ($generatedImage !== '' && preg_match('#^img/blog/[a-zA-Z0-9._/-]+$#', $generatedImage)) {
            $generatedPath = dirname(__DIR__) . '/' . $generatedImage;

            if (is_file($generatedPath)) {
                return [
                    'path' => $generatedImage,
                    'width' => (int) ($_POST['generated_image_width'] ?? 0),
                    'height' => (int) ($_POST['generated_image_height'] ?? 0),
                ];
            }
        }

        if ($currentArticle !== null) {
            return [
                'path' => (string) $currentArticle['image'],
                'width' => (int) ($currentArticle['image_width'] ?? 1400),
                'height' => (int) ($currentArticle['image_height'] ?? 933),
            ];
        }

        return ['path' => '', 'width' => 0, 'height' => 0];
    }

    if (($_FILES['image']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo cargar la imagen.');
    }

    $image = commar_admin_store_uploaded_image((string) $_FILES['image']['tmp_name'], 'img/blog/' . $slug);
    commar_media_register($image['path'], 'featured', (int) $image['width'], (int) $image['height'], $slug);

    return ['path' => $image['path'], 'width' => $image['width'], 'height' => $image['height']];
}

function commar_admin_selected_gallery_media(): array
{
    $gallery = [];
    $selected = $_POST['media_gallery_images'] ?? [];
    $selected = is_array($selected) ? $selected : [];

    foreach ($selected as $path) {
        $mediaImage = commar_media_image_from_path((string) $path);
        if ($mediaImage === null) {
            continue;
        }

        $gallery[] = [
            'path' => $mediaImage['path'],
            'width' => $mediaImage['width'],
            'height' => $mediaImage['height'],
        ];
    }

    return $gallery;
}

function commar_admin_save_gallery_images(string $slug): array
{
    $gallery = [];
    $files = $_FILES['gallery_images'] ?? null;

    if (!is_array($files) || !isset($files['tmp_name']) || !is_array($files['tmp_name'])) {
        return $gallery;
    }

    foreach ($files['tmp_name'] as $index => $tmpName) {
        if ($tmpName === '' || ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if (($files['error'][$index] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo cargar una imagen de galería.');
        }

        $image = commar_admin_store_uploaded_image(
            (string) $tmpName,
            'img/blog/' . $slug . '-gallery-' . date('YmdHis') . '-' . ($index + 1),
            'imagen de galería'
        );
        commar_media_register($image['path'], 'gallery', (int) $image['width'], (int) $image['height'], $slug);

        $gallery[] = [
            'path' => $image['path'],
            'width' => $image['width'],
            'height' => $image['height'],
        ];
    }

    return $gallery;
}

$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$category = trim((string) ($_POST['category'] ?? ''));

// To ensure plain text for paragraph splitting, derive it from the HTML version.
$sourceForPlainText = trim((string) ($_POST['content_html'] ?? ''));
if ($sourceForPlainText === '') {
    $sourceForPlainText = trim((string) ($_POST['content'] ?? ''));
}
$plainText = str_ireplace(['<br>', '<br />'], "\n", $sourceForPlainText);
$plainText = str_ireplace(['</p>', '</li>', '</div>', '</h1>', '<h2>', '</h3>', '</h4>', '</h5>', '</h6>', '</blockquote>', '</pre>', '</address>', '</dd>', '</dt>', '</dl>', '</fieldset>', '</form>', '<hr>'], "\n\n", $plainText);
$plainText = strip_tags($plainText);
$plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$rawContent = trim(preg_replace('/(\R\s*){3,}/', "\n\n", $plainText));

$rawContentHtml = trim((string) ($_POST['content_html'] ?? ''));
$originalSlug = trim((string) ($_POST['original_slug'] ?? ''));
$rawTags = trim((string) ($_POST['tags'] ?? ''));
$status = (string) ($_POST['status'] ?? 'published');
$youtubeUrl = commar_admin_get_youtube_embed_url((string) ($_POST['youtube_url'] ?? ''));
$status = in_array($status, ['draft', 'published'], true) ? $status : 'published';

if ($title === '' || $description === '' || $category === '' || $rawContent === '') {
    http_response_code(422);
    exit('Faltan campos obligatorios.');
}

$currentArticle = null;
if ($originalSlug !== '') {
    if (!preg_match('/^[a-z0-9-]+$/', $originalSlug)) {
        http_response_code(400);
        exit('Artículo inválido.');
    }

    $currentArticle = commar_find_article_by_slug($originalSlug);
    if ($currentArticle === null) {
        http_response_code(404);
        exit('El artículo no existe.');
    }
}

$slug = commar_admin_unique_slug(commar_admin_slugify($title), '', $originalSlug !== '' ? $originalSlug : null);
$image = commar_admin_save_image($slug, $currentArticle);
$existingGallery = [];

foreach (($_POST['gallery_existing'] ?? []) as $galleryPath) {
    $galleryPath = (string) $galleryPath;

    foreach (($currentArticle['gallery'] ?? []) as $galleryItem) {
        if (($galleryItem['path'] ?? '') === $galleryPath) {
            $existingGallery[] = $galleryItem;
            break;
        }
    }
}

$galleryByPath = [];
foreach (array_merge($existingGallery, commar_admin_selected_gallery_media(), commar_admin_save_gallery_images($slug)) as $galleryItem) {
    $path = (string) ($galleryItem['path'] ?? '');
    if ($path === '' || isset($galleryByPath[$path])) {
        continue;
    }
    $galleryByPath[$path] = $galleryItem;
}
$gallery = array_values($galleryByPath);
$paragraphs = preg_split('/\R{2,}/', $rawContent) ?: [];
$paragraphs = array_values(array_filter(array_map('trim', $paragraphs), static fn(string $paragraph): bool => $paragraph !== ''));
$contentHtml = commar_admin_sanitize_article_html($rawContentHtml);
$tags = preg_split('/,/', $rawTags) ?: [];
$tags = array_values(array_unique(array_filter(array_map(
    static fn(string $tag): string => commar_text_substr(trim($tag), 0, 40),
    $tags
), static fn(string $tag): bool => $tag !== '')));
$now = date('c');
$dbNow = date('Y-m-d H:i:s');
$publishedAt = (string) ($currentArticle['published_at'] ?? $now);
$dbPublishedAt = $currentArticle !== null && $currentArticle['published_at'] !== ''
    ? date('Y-m-d H:i:s', strtotime((string) $currentArticle['published_at']))
    : $dbNow;

$article = [
    'title' => $title,
    'description' => $description,
    'category' => $category,
    'year' => (string) ($currentArticle['year'] ?? date('Y')),
    'slug' => $slug,
    'image' => $image['path'],
    'image_width' => $image['width'],
    'image_height' => $image['height'],
    'content' => $paragraphs,
    'content_html' => $contentHtml,
    'gallery' => $gallery,
    'youtube_url' => $youtubeUrl,
    'status' => $status,
    'published_at' => $publishedAt,
    'updated_at' => $now,
];

$contentJson = json_encode($paragraphs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$galleryJson = json_encode($gallery, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$tagsJson = json_encode($tags, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($contentJson === false || $galleryJson === false || $tagsJson === false) {
    http_response_code(500);
    exit('No se pudo preparar el artículo.');
}

if ($currentArticle !== null) {
    $statement = commar_db()->prepare(
        'UPDATE commar_articles SET
            slug = :slug,
            title = :title,
            description = :description,
            category = :category,
            year = :year,
            image = :image,
            image_width = :image_width,
            image_height = :image_height,
            content_html = :content_html,
            content_json = :content_json,
            gallery_json = :gallery_json,
            youtube_url = :youtube_url,
            tags_json = :tags_json,
            status = :status,
            published_at = :published_at,
            updated_at = :updated_at,
            deleted_at = NULL
        WHERE id = :id'
    );
    $statement->execute([
        'id' => $currentArticle['id'],
        'slug' => $slug,
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'year' => (string) ($currentArticle['year'] ?? date('Y')),
        'image' => $image['path'],
        'image_width' => $image['width'],
        'image_height' => $image['height'],
        'content_html' => $contentHtml,
        'content_json' => $contentJson,
        'gallery_json' => $galleryJson,
        'youtube_url' => $youtubeUrl,
        'tags_json' => $tagsJson,
        'status' => $status,
        'published_at' => $dbPublishedAt,
        'updated_at' => $dbNow,
    ]);
} else {
    $statement = commar_db()->prepare(
        'INSERT INTO commar_articles
            (slug, title, description, category, year, image, image_width, image_height, content_html, content_json, gallery_json, youtube_url, tags_json, status, published_at, updated_at)
        VALUES
            (:slug, :title, :description, :category, :year, :image, :image_width, :image_height, :content_html, :content_json, :gallery_json, :youtube_url, :tags_json, :status, :published_at, :updated_at)'
    );
    $statement->execute([
        'slug' => $slug,
        'title' => $title,
        'description' => $description,
        'category' => $category,
        'year' => date('Y'),
        'image' => $image['path'],
        'image_width' => $image['width'],
        'image_height' => $image['height'],
        'content_html' => $contentHtml,
        'content_json' => $contentJson,
        'gallery_json' => $galleryJson,
        'youtube_url' => $youtubeUrl,
        'tags_json' => $tagsJson,
        'status' => $status,
        'published_at' => $dbNow,
        'updated_at' => $dbNow,
    ]);
}

header('Location: blog.php?' . ($originalSlug !== '' ? 'updated=1' : 'created=1'));
exit;
