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

if (!commar_admin_verify_csrf_token()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido.']);
    exit;
}

$file = $_FILES['image'] ?? null;
if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Seleccioná una imagen para subir.']);
    exit;
}

if (commar_media_kind((string) ($file['name'] ?? '')) !== 'image') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'El archivo debe ser una imagen.']);
    exit;
}

try {
    $stored = commar_media_save_upload($file);

    echo json_encode([
        'ok' => true,
        'path' => (string) $stored['path'],
        'width' => (int) ($stored['width'] ?? 0),
        'height' => (int) ($stored['height'] ?? 0),
    ]);
} catch (Throwable $exception) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => $exception->getMessage()]);
}
