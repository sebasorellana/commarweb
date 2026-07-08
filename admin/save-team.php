<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/images.php';
require_once dirname(__DIR__) . '/includes/media.php';
require_once dirname(__DIR__) . '/includes/team.php';

commar_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: team.php');
    exit;
}

commar_admin_require_valid_csrf();

$postedMembers = $_POST['members'] ?? [];
if (!is_array($postedMembers)) {
    header('Location: team.php?error=' . rawurlencode('No se recibieron miembros válidos.'));
    exit;
}

$members = [];
$files = $_FILES['member_images'] ?? [];

foreach ($postedMembers as $key => $member) {
    if (!is_array($member) || !empty($member['delete'])) {
        continue;
    }

    $name = trim((string) ($member['name'] ?? ''));
    $role = trim((string) ($member['role'] ?? ''));
    $imagePath = trim((string) ($member['image'] ?? ''));
    $imageWidth = max(0, (int) ($member['width'] ?? 0));
    $imageHeight = max(0, (int) ($member['height'] ?? 0));

    $uploadError = $files['error'][$key] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_NO_FILE) {
        if ($uploadError !== UPLOAD_ERR_OK) {
            header('Location: team.php?error=' . rawurlencode('Una de las fotos no se pudo cargar correctamente.'));
            exit;
        }

        $baseName = preg_replace('/[^a-z0-9]+/', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name)) ?: 'miembro';

        try {
            $image = commar_admin_store_uploaded_image(
                (string) ($files['tmp_name'][$key] ?? ''),
                'img/team/' . $baseName . '-' . date('YmdHis'),
                'foto'
            );
        } catch (RuntimeException $exception) {
            header('Location: team.php?error=' . rawurlencode($exception->getMessage()));
            exit;
        }

        $imagePath = (string) $image['path'];
        $imageWidth = (int) $image['width'];
        $imageHeight = (int) $image['height'];
        commar_media_register($imagePath, 'image', $imageWidth, $imageHeight, $name);
    }

    if ($name === '' && $role === '' && $imagePath === '') {
        continue;
    }

    if ($name === '' || $role === '' || $imagePath === '') {
        header('Location: team.php?error=' . rawurlencode('Cada miembro visible necesita nombre, rol y foto.'));
        exit;
    }

    $members[] = [
        'image' => $imagePath,
        'width' => $imageWidth,
        'height' => $imageHeight,
        'name' => $name,
        'role' => $role,
        'linkedin' => trim((string) ($member['linkedin'] ?? '#')) ?: '#',
    ];
}

commar_save_team_members($members);

header('Location: team.php?updated=1');
exit;
