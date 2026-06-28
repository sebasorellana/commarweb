<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/focused-works.php';

commar_admin_require_login();

$worksByLang = commar_get_all_focused_works_by_lang();
$updated = ($_GET['updated'] ?? '') === '1';
$created = ($_GET['created'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';
$reordered = ($_GET['reordered'] ?? '') === '1';
$visibleLang = isset($worksByLang['es']) ? 'es' : (array_key_first($worksByLang) ?? 'es');
$visibleWorks = $worksByLang[$visibleLang] ?? [];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obras en foco | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('home'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Página Home'); ?>
            <main class="admin-content">
                <?php commar_admin_home_nav('focused-works'); ?>

                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Contenido Home</span>
                            <h2>Obras en foco</h2>
                        </div>
                        <a href="focused-work-edit.php" class="admin-primary-link">Nueva obra</a>
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
                     <?php if ($reordered): ?>
                        <p class="admin-alert admin-alert-success">Orden actualizado.</p>
                    <?php endif; ?>

                    <?php if (empty($visibleWorks)): ?>
                        <p class="admin-empty">No hay obras en foco cargadas.</p>
                    <?php else: ?>
                        <div class="admin-focused-works-langs">
                            <div class="admin-focused-works-lang-group">
                                <form action="reorder-focused-works.php" method="post">
                                    <input type="hidden" name="lang" value="<?php echo commar_admin_h($visibleLang); ?>">
                                    <div class="admin-table-wrap">
                                        <table class="admin-post-table">
                                            <thead>
                                                <tr>
                                                    <th>Obra</th>
                                                    <th>Categoría</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($visibleWorks as $work): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="admin-post-title">
                                                                <img src="../<?php echo commar_admin_h($work['image']); ?>" alt="" width="64" height="64">
                                                                <div>
                                                                    <strong><?php echo commar_admin_h($work['title']); ?></strong>
                                                                    <span><?php echo commar_admin_h($work['summary']); ?></span>
                                                                    <input type="hidden" name="work_ids[]" value="<?php echo (int) $work['id']; ?>">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo commar_admin_h($work['category']); ?></td>
                                                        <td>
                                                            <div class="admin-table-actions">
                                                                <a href="focused-work-edit.php?id=<?php echo (int) $work['id']; ?>" class="admin-button-icon" title="Editar obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></a>
                                                                <form action="delete-focused-work.php" method="post" onsubmit="return confirm('¿Eliminar esta obra? Esta acción no se puede deshacer.');" style="display: inline;">
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
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
