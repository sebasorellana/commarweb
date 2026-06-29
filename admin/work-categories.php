<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/projects.php';

commar_admin_require_login();

$editingId = (int) ($_GET['edit'] ?? 0);
$editingCategory = $editingId > 0 ? commar_admin_work_category_by_id($editingId) : null;
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    try {
        if ($action === 'delete') {
            if (commar_admin_delete_work_category($id)) {
                header('Location: work-categories.php?deleted=1');
                exit;
            }

            $message = 'No se pudo eliminar la categoría. Verificá que no esté usada por obras existentes.';
            $messageType = 'error';
        } else {
            $name = trim((string) ($_POST['name'] ?? ''));
            $displayOrder = (int) ($_POST['display_order'] ?? 0);

            if ($name === '') {
                $message = 'El nombre de la categoría es obligatorio.';
                $messageType = 'error';
            } elseif (commar_admin_save_work_category($name, $displayOrder, $id)) {
                header('Location: work-categories.php?' . ($id > 0 ? 'updated=1' : 'created=1'));
                exit;
            } else {
                $message = 'No se pudo guardar la categoría.';
                $messageType = 'error';
            }
        }
    } catch (PDOException $exception) {
        $message = 'Ya existe una categoría con ese nombre.';
        $messageType = 'error';
    }
}

$categories = commar_admin_work_categories();
$created = ($_GET['created'] ?? '') === '1';
$updated = ($_GET['updated'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$formTitle = $editingCategory ? 'Editar categoría' : 'Nueva categoría';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías de obras | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css?v=20260629-admin-works-ui">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('works'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Categorías de obras'); ?>
            <main class="admin-content">
                <?php commar_admin_works_nav('categories'); ?>
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Directorio de obras</span>
                            <h2>Categorías</h2>
                        </div>
                        <a href="works.php" class="admin-secondary-link">Volver a obras</a>
                    </div>

                    <?php if ($created): ?>
                        <p class="admin-alert admin-alert-success">Categoría creada.</p>
                    <?php endif; ?>
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Categoría actualizada.</p>
                    <?php endif; ?>
                    <?php if ($deleted): ?>
                        <p class="admin-alert admin-alert-success">Categoría eliminada.</p>
                    <?php endif; ?>
                    <?php if ($message !== ''): ?>
                        <p class="admin-alert admin-alert-<?php echo $messageType === 'error' ? 'error' : 'success'; ?>"><?php echo commar_admin_h($message); ?></p>
                    <?php endif; ?>

                    <div class="admin-categories-grid">
                        <section class="admin-work-section">
                            <div class="admin-work-section-head">
                                <span class="admin-kicker">Catálogo</span>
                                <h3><?php echo commar_admin_h($formTitle); ?></h3>
                            </div>
                            <form action="work-categories.php" method="post" class="admin-form">
                                <?php if ($editingCategory): ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $editingCategory['id']; ?>">
                                <?php endif; ?>
                                <label>
                                    Nombre
                                    <input type="text" name="name" value="<?php echo commar_admin_h((string) ($editingCategory['name'] ?? '')); ?>" required maxlength="120">
                                </label>
                                <label>
                                    Orden
                                    <input type="number" name="display_order" value="<?php echo (int) ($editingCategory['display_order'] ?? ((count($categories) + 1) * 10)); ?>">
                                    <span class="admin-help">Menor número aparece primero en el select.</span>
                                </label>
                                <div class="admin-work-savebar">
                                    <?php if ($editingCategory): ?>
                                        <a href="work-categories.php" class="admin-secondary-link">Cancelar</a>
                                    <?php endif; ?>
                                    <button type="submit" class="admin-button-primary"><?php echo $editingCategory ? 'Guardar cambios' : 'Crear categoría'; ?></button>
                                </div>
                            </form>
                        </section>

                        <section class="admin-work-section">
                            <div class="admin-work-section-head">
                                <span class="admin-kicker">Disponibles</span>
                                <h3>Listado</h3>
                            </div>
                            <?php if (empty($categories)): ?>
                                <p class="admin-empty">No hay categorías cargadas.</p>
                            <?php else: ?>
                                <div class="admin-table-wrap">
                                    <table class="admin-post-table">
                                        <thead>
                                            <tr>
                                                <th>Categoría</th>
                                                <th>Orden</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categories as $category): ?>
                                                <tr>
                                                    <td>
                                                        <div class="admin-post-title">
                                                            <div>
                                                                <strong><?php echo commar_admin_h((string) $category['name']); ?></strong>
                                                                <span><?php echo commar_admin_h((string) $category['slug']); ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo (int) $category['display_order']; ?></td>
                                                    <td>
                                                        <div class="admin-table-actions">
                                                            <a href="work-categories.php?edit=<?php echo (int) $category['id']; ?>" class="admin-button-icon" title="Editar categoría"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></a>
                                                            <form action="work-categories.php" method="post" onsubmit="return confirm('¿Eliminar esta categoría? Solo se eliminará si no está usada por ninguna obra.');" style="display: inline;">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo (int) $category['id']; ?>">
                                                                <button type="submit" class="admin-button-icon admin-button-danger" title="Eliminar categoría"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
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
