<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/jobs.php';
require_once dirname(__DIR__) . '/includes/images.php';

commar_admin_require_login();

function commar_admin_upload_job_image(int $jobId = 0): array
{
    $file = $_FILES['image'] ?? null;
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo subir la imagen.');
    }

    $uploadDir = dirname(__DIR__) . '/img/jobs';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
        throw new RuntimeException('No se pudo crear la carpeta de imágenes. Verificá que img/jobs exista y tenga permisos de escritura.');
    }

    if (!is_writable($uploadDir)) {
        @chmod($uploadDir, 0777);
    }

    if (!is_writable($uploadDir)) {
        throw new RuntimeException('La carpeta img/jobs no tiene permisos de escritura.');
    }

    $image = commar_admin_store_uploaded_image(
        (string) $file['tmp_name'],
        'img/jobs/job-' . ($jobId > 0 ? $jobId : 'new') . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)),
        'imagen'
    );

    return [
        'path' => $image['path'],
        'width' => (int) $image['width'],
        'height' => (int) $image['height'],
    ];
}

function commar_admin_delete_job_image_file(string $relativePath): void
{
    if (!preg_match('#^img/jobs/[a-zA-Z0-9._-]+\.(jpe?g|png|webp)$#', $relativePath)) {
        return;
    }

    $absolutePath = dirname(__DIR__) . '/' . $relativePath;
    if (is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

$editingId = (int) ($_GET['edit'] ?? 0);
$editingJob = $editingId > 0 ? commar_job_by_id($editingId, false) : null;
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    commar_admin_require_valid_csrf();

    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete') {
        if (commar_delete_job($id)) {
            header('Location: jobs.php?deleted=1');
            exit;
        }
        $message = 'No se pudo eliminar la búsqueda.';
        $messageType = 'error';
    } else {
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $status = (string) ($_POST['status'] ?? 'inactive');
        $currentJob = $id > 0 ? commar_job_by_id($id, false) : null;

        try {
            $image = commar_admin_upload_job_image($id);
            if ($id > 0 && empty($image) && isset($_POST['remove_image'])) {
                $image = ['remove' => true];
            }
        } catch (RuntimeException $exception) {
            $message = $exception->getMessage();
            $messageType = 'error';
            $image = null;
        }

        if (is_array($image) && commar_save_job($title, $description, $status, $image, $id)) {
            $currentImage = (string) ($currentJob['image'] ?? '');
            $newImage = (string) ($image['path'] ?? '');
            if ($currentImage !== '' && (!empty($image['remove']) || ($newImage !== '' && $newImage !== $currentImage))) {
                commar_admin_delete_job_image_file($currentImage);
            }

            header('Location: jobs.php?' . ($id > 0 ? 'updated=1' : 'created=1'));
            exit;
        }
        if ($message === '') {
            $message = 'Completá título y descripción.';
            $messageType = 'error';
        }
    }
}

$jobs = commar_admin_jobs();
$created = ($_GET['created'] ?? '') === '1';
$updated = ($_GET['updated'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$formTitle = $editingJob ? 'Editar búsqueda' : 'Nueva búsqueda';
$descriptionHtml = commar_job_description_html((string) ($editingJob['description'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabaja con nosotros | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css?v=20260629-jobs">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('jobs'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Trabaja con nosotros'); ?>
            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Búsquedas laborales</span>
                            <h2>ABM</h2>
                        </div>
                    </div>

                    <?php if ($created): ?><p class="admin-alert admin-alert-success">Búsqueda creada.</p><?php endif; ?>
                    <?php if ($updated): ?><p class="admin-alert admin-alert-success">Búsqueda actualizada.</p><?php endif; ?>
                    <?php if ($deleted): ?><p class="admin-alert admin-alert-success">Búsqueda eliminada.</p><?php endif; ?>
                    <?php if ($message !== ''): ?><p class="admin-alert admin-alert-<?php echo $messageType === 'error' ? 'error' : 'success'; ?>"><?php echo commar_admin_h($message); ?></p><?php endif; ?>

                    <div class="admin-categories-grid">
                        <section class="admin-work-section">
                            <div class="admin-work-section-head">
                                <span class="admin-kicker">Formulario</span>
                                <h3><?php echo commar_admin_h($formTitle); ?></h3>
                            </div>
                            <form action="jobs.php" method="post" enctype="multipart/form-data" class="admin-form" data-article-form>
                                <?php if ($editingJob): ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $editingJob['id']; ?>">
                                <?php endif; ?>
                                <label>
                                    Título
                                    <input type="text" name="title" value="<?php echo commar_admin_h((string) ($editingJob['title'] ?? '')); ?>" required maxlength="180">
                                </label>
                                <div class="admin-rich-field">
                                    <span class="admin-field-label">Descripción</span>
                                    <div class="admin-editor-toolbar" aria-label="Herramientas de texto">
                                        <button type="button" data-editor-command="bold">B</button>
                                        <button type="button" data-editor-command="italic">I</button>
                                        <button type="button" data-editor-command="insertUnorderedList">Lista</button>
                                        <button type="button" data-editor-command="formatBlock" data-editor-value="p">P</button>
                                    </div>
                                    <div class="admin-rich-editor admin-rich-editor-compact" contenteditable="true" data-rich-editor><?php echo $descriptionHtml; ?></div>
                                    <textarea class="admin-content-source" data-content-source><?php echo commar_admin_h(strip_tags(str_replace(['</p>', '<br>', '<br />'], ["\n\n", "\n", "\n"], $descriptionHtml))); ?></textarea>
                                    <textarea name="description" class="admin-content-source" data-content-html><?php echo commar_admin_h($descriptionHtml); ?></textarea>
                                </div>
                                <span class="admin-help">Se muestra como detalle de la búsqueda en la web. Podés usar negritas, itálicas y listas.</span>
                                <label class="admin-file-control">
                                    Imagen
                                    <span class="admin-file-input-wrap">
                                        <span class="admin-file-button">Subir imagen</span>
                                        <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-file-input>
                                    </span>
                                    <span class="admin-help">Formato JPG, PNG o WEBP. Si editás y no subís una nueva, se conserva la actual.</span>
                                </label>
                                <?php if (!empty($editingJob['image'])): ?>
                                    <figure class="admin-hero-preview">
                                        <img src="../<?php echo commar_admin_h((string) $editingJob['image']); ?>" alt="" loading="lazy">
                                        <figcaption>Imagen actual</figcaption>
                                    </figure>
                                    <label class="admin-checkbox-row">
                                        <input type="checkbox" name="remove_image" value="1">
                                        <span>Eliminar imagen actual</span>
                                    </label>
                                <?php endif; ?>
                                <label>
                                    Estado
                                    <select name="status">
                                        <?php $selectedStatus = (string) ($editingJob['status'] ?? 'active'); ?>
                                        <option value="active" <?php echo $selectedStatus === 'active' ? 'selected' : ''; ?>>Activa</option>
                                        <option value="inactive" <?php echo $selectedStatus !== 'active' ? 'selected' : ''; ?>>Inactiva</option>
                                    </select>
                                </label>
                                <div class="admin-work-savebar">
                                    <?php if ($editingJob): ?>
                                        <a href="jobs.php" class="admin-secondary-link">Cancelar</a>
                                    <?php endif; ?>
                                    <button type="submit" class="admin-button-primary"><?php echo $editingJob ? 'Guardar cambios' : 'Crear búsqueda'; ?></button>
                                </div>
                            </form>
                        </section>

                        <section class="admin-work-section">
                            <div class="admin-work-section-head">
                                <span class="admin-kicker">Listado</span>
                                <h3>Búsquedas</h3>
                            </div>
                            <?php if (empty($jobs)): ?>
                                <p class="admin-empty">No hay búsquedas cargadas.</p>
                            <?php else: ?>
                                <div class="admin-table-wrap">
                                    <table class="admin-post-table">
                                        <thead>
                                            <tr>
                                                <th>Búsqueda</th>
                                                <th>Imagen</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jobs as $job): ?>
                                                <tr>
                                                    <td>
                                                        <div class="admin-post-title">
                                                            <div>
                                                                <strong><?php echo commar_admin_h((string) $job['title']); ?></strong>
                                                                <span><?php echo commar_admin_h(trim(strip_tags((string) $job['description']))); ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($job['image'])): ?>
                                                            <img src="../<?php echo commar_admin_h((string) $job['image']); ?>" alt="" width="72" height="48" class="admin-table-thumb">
                                                        <?php else: ?>
                                                            <span class="admin-empty-inline">Sin imagen</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><span class="admin-status-pill <?php echo $job['status'] === 'active' ? 'is-published' : 'is-draft'; ?>"><?php echo $job['status'] === 'active' ? 'Activa' : 'Inactiva'; ?></span></td>
                                                    <td>
                                                        <div class="admin-table-actions">
                                                            <a href="jobs.php?edit=<?php echo (int) $job['id']; ?>" class="admin-button-icon" title="Editar búsqueda"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></a>
                                                            <form action="jobs.php" method="post" onsubmit="return confirm('¿Eliminar esta búsqueda?');" style="display: inline;">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $job['id']; ?>">
                                                                <button type="submit" class="admin-button-icon admin-button-danger" title="Eliminar búsqueda"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </section>
                    </div>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260629-jobs-editor" defer></script>
</body>
</html>
