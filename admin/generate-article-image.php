<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/media.php';

commar_admin_require_login();

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

function commar_admin_ai_image_response(int $status, string $error): never
{
    http_response_code($status);
    echo json_encode(['ok' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
    exit;
}

function commar_admin_ai_slug(string $value): string
{
    $value = trim(mb_strtolower($value, 'UTF-8'));
    $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    $value = $converted !== false ? $converted : $value;
    $value = preg_replace('/[^a-z0-9]+/', '-', strtolower($value)) ?? '';
    $value = trim($value, '-');

    return $value !== '' ? $value : 'articulo';
}

function commar_admin_fetch_remote_image(string $url): string
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_USERAGENT => 'COMMAR CMS/1.0',
        ]);
        $contents = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if (is_string($contents) && $contents !== '' && $status >= 200 && $status < 300) {
            return $contents;
        }

        throw new RuntimeException($error !== '' ? $error : 'El servicio de IA no respondió correctamente.');
    }

    $contents = @file_get_contents($url);
    if (is_string($contents) && $contents !== '') {
        return $contents;
    }

    throw new RuntimeException('No se pudo descargar la imagen generada.');
}

$title = trim((string) ($_POST['title'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$content = trim((string) ($_POST['content'] ?? ''));

if ($title === '' || $description === '' || $content === '') {
    commar_admin_ai_image_response(422, 'Completá título, descripción y cuerpo antes de generar.');
}

$promptSource = mb_substr($title . '. ' . $description . '. ' . $content, 0, 650, 'UTF-8');
$prompt = 'Editorial architectural feature image for a professional article. '
    . 'Subject: ' . $promptSource . '. '
    . 'Style: contemporary architecture, premium real estate editorial photography, natural light, refined materials, no text, no logos, no people, horizontal composition.';
$url = 'https://image.pollinations.ai/prompt/' . rawurlencode($prompt)
    . '?width=1400&height=933&model=flux&nologo=true&seed=' . random_int(1, 999999);

try {
    $imageBytes = commar_admin_fetch_remote_image($url);
    $imageInfo = getimagesizefromstring($imageBytes);

    if ($imageInfo === false) {
        commar_admin_ai_image_response(502, 'La respuesta del servicio no fue una imagen válida.');
    }

    $extensions = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];
    $extension = $extensions[$imageInfo[2]] ?? 'jpg';
    $slug = commar_admin_ai_slug($title);
    $relativePath = 'img/blog/ai-' . $slug . '-' . date('YmdHis') . '.' . $extension;
    $targetPath = dirname(__DIR__) . '/' . $relativePath;
    $targetDir = dirname($targetPath);

    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
        commar_admin_ai_image_response(500, 'No se pudo crear la carpeta de imágenes.');
    }

    if (file_put_contents($targetPath, $imageBytes, LOCK_EX) === false) {
        commar_admin_ai_image_response(500, 'No se pudo guardar la imagen generada.');
    }

    commar_media_register($relativePath, 'ai-featured', (int) $imageInfo[0], (int) $imageInfo[1], $title);

    echo json_encode([
        'ok' => true,
        'path' => $relativePath,
        'width' => (int) $imageInfo[0],
        'height' => (int) $imageInfo[1],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    commar_admin_ai_image_response(502, 'No se pudo generar la imagen. ' . $exception->getMessage());
}
