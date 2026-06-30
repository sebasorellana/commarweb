<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/images.php';

commar_admin_require_administrator();

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!commar_admin_verify_csrf_token()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Token de seguridad inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $inventory = commar_admin_image_inventory();
    $pendingItems = array_values(array_filter(
        $inventory['items'],
        static fn(array $item): bool => !empty($item['needs_optimization'])
    ));

    if (!$inventory['webp_supported']) {
        throw new RuntimeException('El servidor no tiene soporte WebP en GD.');
    }

    if (empty($pendingItems)) {
        echo json_encode([
            'ok' => true,
            'done' => true,
            'processed' => null,
            'pending' => 0,
            'total' => (int) ($_POST['total'] ?? $inventory['pending']),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $item = $pendingItems[0];
    $result = commar_admin_convert_existing_image_to_webp((string) $item['path']);
    $afterInventory = commar_admin_image_inventory();

    echo json_encode([
        'ok' => true,
        'done' => false,
        'processed' => $result,
        'pending' => (int) $afterInventory['pending'],
        'total' => max((int) ($_POST['total'] ?? 0), (int) $inventory['pending']),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
