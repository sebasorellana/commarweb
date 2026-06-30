<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/media.php';

commar_admin_require_login();

$message = '';
$messageType = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    commar_admin_require_valid_csrf();

    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'upload') {
            $files = $_FILES['media_files'] ?? null;
            if (!$files || !is_array($files['name'] ?? null)) {
                throw new RuntimeException('Seleccioná al menos un archivo.');
            }

            $created = 0;
            foreach ($files['name'] as $index => $name) {
                if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                commar_media_save_upload([
                    'name' => $name,
                    'type' => $files['type'][$index] ?? '',
                    'tmp_name' => $files['tmp_name'][$index] ?? '',
                    'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $files['size'][$index] ?? 0,
                ]);
                $created++;
            }

            header('Location: media.php?uploaded=' . $created);
            exit;
        }

        if ($action === 'delete') {
            $path = (string) ($_POST['path'] ?? '');
            if (commar_media_delete($path)) {
                header('Location: media.php?deleted=1');
                exit;
            }
            throw new RuntimeException('No se pudo eliminar el archivo.');
        }
    } catch (RuntimeException $exception) {
        $message = $exception->getMessage();
        $messageType = 'error';
    }
}

$filter = (string) ($_GET['tipo'] ?? 'all');
$search = trim((string) ($_GET['q'] ?? ''));
$mediaItems = commar_media_scan_files();

if (in_array($filter, ['image', 'document', 'video', 'file'], true)) {
    $mediaItems = array_values(array_filter($mediaItems, static fn(array $item): bool => $item['kind'] === $filter));
}

if ($search !== '') {
    $mediaItems = array_values(array_filter($mediaItems, static function (array $item) use ($search): bool {
        return stripos((string) $item['path'], $search) !== false;
    }));
}

$uploaded = (int) ($_GET['uploaded'] ?? 0);
$deleted = ($_GET['deleted'] ?? '') === '1';
$counts = ['all' => 0, 'image' => 0, 'document' => 0, 'video' => 0, 'file' => 0];
foreach (commar_media_scan_files() as $item) {
    $counts['all']++;
    $counts[$item['kind']] = ($counts[$item['kind']] ?? 0) + 1;
}

function commar_admin_media_size(int $bytes): string
{
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
    }

    return number_format($bytes / 1024, 1, ',', '.') . ' KB';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediateca | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('media'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Mediateca'); ?>
            <main class="admin-content">
                <?php if ($uploaded > 0): ?>
                    <p class="admin-alert admin-alert-success"><?php echo $uploaded; ?> archivo<?php echo $uploaded === 1 ? '' : 's'; ?> agregado<?php echo $uploaded === 1 ? '' : 's'; ?>.</p>
                <?php endif; ?>
                <?php if ($deleted): ?>
                    <p class="admin-alert admin-alert-success">Archivo eliminado.</p>
                <?php endif; ?>
                <?php if ($message): ?>
                    <p class="admin-alert admin-alert-<?php echo $messageType; ?>"><?php echo commar_admin_h($message); ?></p>
                <?php endif; ?>

                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Archivos</span>
                            <h2>Biblioteca de medios</h2>
                        </div>
                        <a href="#upload-media-modal" class="admin-primary-link">Agregar medio</a>
                    </div>

                    <form action="media.php" method="get" class="admin-media-toolbar">
                        <div class="admin-media-tabs" aria-label="Filtros de mediateca">
                            <?php foreach (['all' => 'Todos', 'image' => 'Imágenes', 'document' => 'Documentos', 'video' => 'Videos', 'file' => 'Otros'] as $key => $label): ?>
                                <a href="media.php?tipo=<?php echo commar_admin_h($key); ?>" class="<?php echo $filter === $key || ($key === 'all' && $filter === 'all') ? 'is-active' : ''; ?>">
                                    <?php echo commar_admin_h($label); ?> <span><?php echo (int) ($counts[$key] ?? 0); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <label>
                            <span class="sr-only">Buscar</span>
                            <input type="search" name="q" value="<?php echo commar_admin_h($search); ?>" placeholder="Buscar por nombre o ruta">
                        </label>
                        <input type="hidden" name="tipo" value="<?php echo commar_admin_h($filter); ?>">
                    </form>

                    <?php if (empty($mediaItems)): ?>
                        <p class="admin-empty">No hay medios para mostrar.</p>
                    <?php else: ?>
                        <div class="admin-media-grid">
                            <?php foreach ($mediaItems as $index => $item): ?>
                                <?php $modalId = 'media-item-' . $index; ?>
                                <a href="#<?php echo $modalId; ?>" class="admin-media-card">
                                    <span class="admin-media-thumb">
                                        <?php if ($item['kind'] === 'image'): ?>
                                            <img src="../<?php echo commar_admin_h((string) $item['path']); ?>" alt="">
                                        <?php else: ?>
                                            <span><?php echo commar_admin_h(strtoupper(pathinfo((string) $item['path'], PATHINFO_EXTENSION))); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <strong><?php echo commar_admin_h((string) $item['name']); ?></strong>
                                    <small><?php echo commar_admin_h(commar_media_label((string) $item['kind'])); ?> · <?php echo commar_admin_media_size((int) $item['bytes']); ?></small>
                                    <?php if (!empty($item['usage'])): ?>
                                        <em>En uso</em>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>

    <div id="upload-media-modal" class="admin-modal-target">
        <a href="#" class="admin-modal-backdrop" aria-label="Cerrar"></a>
        <section class="admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="upload-media-title">
            <div class="admin-modal-head">
                <div>
                    <span class="admin-kicker">Carga</span>
                    <h2 id="upload-media-title">Agregar medio</h2>
                </div>
                <a href="#" class="admin-modal-close" aria-label="Cerrar">&times;</a>
            </div>
            <form action="media.php" method="post" enctype="multipart/form-data" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                <input type="hidden" name="action" value="upload">
                <label>
                    Archivos
                    <input type="file" name="media_files[]" multiple accept="image/*,video/mp4,video/webm,video/quicktime,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                    <span class="admin-help">Podés subir imágenes, videos y documentos.</span>
                </label>
                <button type="submit" class="admin-button-primary">Subir archivos</button>
            </form>
        </section>
    </div>

    <?php foreach ($mediaItems as $index => $item): ?>
        <?php $modalId = 'media-item-' . $index; ?>
        <div id="<?php echo $modalId; ?>" class="admin-modal-target">
            <a href="#" class="admin-modal-backdrop" aria-label="Cerrar"></a>
            <section class="admin-modal-card admin-media-detail" role="dialog" aria-modal="true" aria-labelledby="<?php echo $modalId; ?>-title">
                <div class="admin-modal-head">
                    <div>
                        <span class="admin-kicker"><?php echo commar_admin_h(commar_media_label((string) $item['kind'])); ?></span>
                        <h2 id="<?php echo $modalId; ?>-title"><?php echo commar_admin_h((string) $item['name']); ?></h2>
                    </div>
                    <a href="#" class="admin-modal-close" aria-label="Cerrar">&times;</a>
                </div>
                <div class="admin-media-detail-grid">
                    <div class="admin-media-preview">
                        <?php if ($item['kind'] === 'image'): ?>
                            <img src="../<?php echo commar_admin_h((string) $item['path']); ?>" alt="">
                        <?php elseif ($item['kind'] === 'video'): ?>
                            <video src="../<?php echo commar_admin_h((string) $item['path']); ?>" controls></video>
                        <?php else: ?>
                            <span><?php echo commar_admin_h(strtoupper(pathinfo((string) $item['path'], PATHINFO_EXTENSION))); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="admin-media-meta">
                        <p><strong>Ruta</strong><code><?php echo commar_admin_h((string) $item['path']); ?></code></p>
                        <p><strong>Tamaño</strong><?php echo commar_admin_media_size((int) $item['bytes']); ?></p>
                        <?php if ((int) $item['width'] > 0): ?>
                            <p><strong>Dimensiones</strong><?php echo (int) $item['width']; ?> x <?php echo (int) $item['height']; ?></p>
                        <?php endif; ?>
                        <p><strong>Actualizado</strong><?php echo commar_admin_h(date('d/m/Y H:i', strtotime((string) $item['modified_at']))); ?></p>

                        <div class="admin-media-usage">
                            <strong>Uso</strong>
                            <?php if (!empty($item['usage'])): ?>
                                <ul>
                                    <?php foreach ($item['usage'] as $usage): ?>
                                        <li><?php echo commar_admin_h((string) $usage); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No se detecta uso en el sitio.</p>
                            <?php endif; ?>
                        </div>

                        <form action="media.php" method="post" onsubmit="return confirm('¿Eliminar este archivo? Si está en uso puede romper una sección del sitio.');">
                            <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="path" value="<?php echo commar_admin_h((string) $item['path']); ?>">
                            <button type="submit" class="admin-button-primary admin-media-delete">Eliminar medio</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    <?php endforeach; ?>
</body>
</html>
