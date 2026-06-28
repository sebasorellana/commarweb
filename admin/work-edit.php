<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/projects.php';

commar_admin_require_login();

$workId = (int) ($_GET['id'] ?? 0);
$isEditing = $workId > 0;
$work = $isEditing ? commar_admin_project_by_id($workId) : null;
if ($isEditing && !$work) {
    http_response_code(404);
}

$description = $work ? json_decode((string) $work['description_json'], true) : [];
$metrics = $work ? json_decode((string) $work['metrics_json'], true) : [];
$descriptionValue = is_array($description) ? implode("\n\n", array_map('strval', $description)) : '';
$metricsValue = '';
if (is_array($metrics)) {
    foreach ($metrics as $label => $value) {
        $metricsValue .= (string) $label . ': ' . (string) $value . "\n";
    }
}

$pageTitle = $isEditing ? 'Editar obra' : 'Nueva obra';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo commar_admin_h($pageTitle); ?> | MOnkey CMS</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('works'); ?>
        <div class="admin-main">
            <?php commar_admin_header($pageTitle); ?>
            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions" style="margin-bottom: 2rem;">
                        <div>
                            <span class="admin-kicker">Directorio de obras</span>
                            <h2><?php echo commar_admin_h($pageTitle); ?></h2>
                        </div>
                        <a href="works.php" class="admin-secondary-link">Volver al listado</a>
                    </div>

                    <?php if ($isEditing && !$work): ?>
                        <p class="admin-alert admin-alert-error">La obra solicitada no existe.</p>
                    <?php else: ?>
                        <div class="admin-form-container">
                            <form action="save-work.php" method="post" enctype="multipart/form-data" class="admin-form">
                                <?php if ($isEditing): ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $work['id']; ?>">
                                <?php endif; ?>

                                <div class="admin-form-grid">
                                    <label>
                                        Título
                                        <input type="text" name="title" value="<?php echo commar_admin_h($work['title'] ?? ''); ?>" required>
                                    </label>
                                    <label>
                                        Slug
                                        <input type="text" name="slug" value="<?php echo commar_admin_h($work['slug'] ?? ''); ?>">
                                        <span class="admin-help">Si lo dejás vacío se genera desde el título.</span>
                                    </label>
                                </div>
                                <div class="admin-form-grid is-three">
                                    <label>
                                        Categoría
                                        <input type="text" name="category" value="<?php echo commar_admin_h($work['category'] ?? ''); ?>" required>
                                    </label>
                                    <label>
                                        Ubicación
                                        <input type="text" name="location" value="<?php echo commar_admin_h($work['location'] ?? ''); ?>">
                                    </label>
                                    <label>
                                        Año
                                        <input type="text" name="year" value="<?php echo commar_admin_h($work['year'] ?? ''); ?>">
                                    </label>
                                </div>
                                <div class="admin-form-grid">
                                    <label>
                                        Resumen
                                        <textarea name="summary" rows="4" required><?php echo commar_admin_h($work['summary'] ?? ''); ?></textarea>
                                    </label>
                                    <label>
                                        Intro del detalle
                                        <textarea name="intro" rows="4" required><?php echo commar_admin_h($work['intro'] ?? ''); ?></textarea>
                                    </label>
                                </div>
                                <label>Descripción<textarea name="description" rows="8" required><?php echo commar_admin_h($descriptionValue); ?></textarea><span class="admin-help">Separá párrafos con una línea vacía.</span></label>
                                <label>Métricas<textarea name="metrics" rows="5"><?php echo commar_admin_h(trim($metricsValue)); ?></textarea><span class="admin-help">Una por línea, formato: Etiqueta: Valor.</span></label>
                                <div class="admin-form-grid">
                                    <label>
                                        Texto alternativo de imagen
                                        <input type="text" name="hero_alt" value="<?php echo commar_admin_h($work['hero_alt'] ?? ''); ?>">
                                    </label>
                                    <label>
                                        Imagen
                                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" <?php echo !$isEditing ? 'required' : ''; ?>>
                                        <?php if ($isEditing && !empty($work['image'])): ?>
                                            <span class="admin-help">Imagen actual: <?php echo commar_admin_h($work['image']); ?>. Dejar vacío para no cambiar.</span>
                                            <img src="../<?php echo commar_admin_h($work['image']); ?>" alt="" style="max-width: 220px; margin-top: 0.5rem; border-radius: 0.3rem;">
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <button type="submit" class="admin-button-primary">Guardar obra</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
