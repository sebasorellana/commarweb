<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/projects.php';

commar_admin_require_login();

$works = commar_admin_projects();
$updated = ($_GET['updated'] ?? '') === '1';
$created = ($_GET['created'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obras | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('works'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Obras'); ?>
            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Directorio</span>
                            <h2>Obras</h2>
                        </div>
                        <a href="work-edit.php" class="admin-primary-link">Nueva obra</a>
                    </div>

                    <?php if ($created): ?>
                        <p class="admin-alert admin-alert-success">Obra creada.</p>
                    <?php endif; ?>
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Obra actualizada.</p>
                    <?php endif; ?>
                    <?php if ($deleted): ?>
                        <p class="admin-alert admin-alert-success">Obra eliminada.</p>
                    <?php endif; ?>

                    <?php if (empty($works)): ?>
                        <p class="admin-empty">No hay obras cargadas.</p>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-post-table">
                                <thead>
                                    <tr>
                                        <th>Obra</th>
                                        <th>Categoría</th>
                                        <th>Ubicación</th>
                                        <th>Año</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($works as $work): ?>
                                        <tr>
                                            <td>
                                                <div class="admin-post-title">
                                                    <?php if (!empty($work['image'])): ?>
                                                        <img src="../<?php echo commar_admin_h($work['image']); ?>" alt="" width="64" height="64">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo commar_admin_h($work['title']); ?></strong>
                                                        <span><?php echo commar_admin_h($work['summary']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo commar_admin_h($work['category']); ?></td>
                                            <td><?php echo commar_admin_h($work['location']); ?></td>
                                            <td><?php echo commar_admin_h($work['year']); ?></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <a href="../obras/<?php echo rawurlencode($work['slug']); ?>" target="_blank" rel="noopener" class="admin-button-icon" title="Ver obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg></a>
                                                    <a href="work-edit.php?id=<?php echo (int) $work['id']; ?>" class="admin-button-icon" title="Editar obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></a>
                                                    <form action="delete-work.php" method="post" onsubmit="return confirm('¿Eliminar esta obra? Esta acción no se puede deshacer.');" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo (int) $work['id']; ?>">
                                                        <button type="submit" class="admin-button-icon admin-button-danger" title="Eliminar obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
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
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
