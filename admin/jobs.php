<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/jobs.php';

commar_admin_require_login();

$editingId = (int) ($_GET['edit'] ?? 0);
$editingJob = $editingId > 0 ? commar_job_by_id($editingId, false) : null;
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        if (commar_save_job($title, $description, $status, $id)) {
            header('Location: jobs.php?' . ($id > 0 ? 'updated=1' : 'created=1'));
            exit;
        }
        $message = 'Completá título y descripción.';
        $messageType = 'error';
    }
}

$jobs = commar_admin_jobs();
$created = ($_GET['created'] ?? '') === '1';
$updated = ($_GET['updated'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$formTitle = $editingJob ? 'Editar búsqueda' : 'Nueva búsqueda';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabaja con nosotros | MOnkey CMS</title>
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
                            <form action="jobs.php" method="post" class="admin-form">
                                <?php if ($editingJob): ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $editingJob['id']; ?>">
                                <?php endif; ?>
                                <label>
                                    Título
                                    <input type="text" name="title" value="<?php echo commar_admin_h((string) ($editingJob['title'] ?? '')); ?>" required maxlength="180">
                                </label>
                                <label>
                                    Descripción
                                    <textarea name="description" rows="9" required><?php echo commar_admin_h((string) ($editingJob['description'] ?? '')); ?></textarea>
                                    <span class="admin-help">Se muestra como detalle de la búsqueda en la web.</span>
                                </label>
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
                                                                <span><?php echo commar_admin_h((string) $job['description']); ?></span>
                                                            </div>
                                                        </div>
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
</body>
</html>
