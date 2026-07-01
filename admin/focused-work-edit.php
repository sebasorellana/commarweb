<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/focused-works.php';

commar_admin_require_login();

$workId = (int) ($_GET['id'] ?? 0);
$isEditing = $workId > 0;
$work = null;

if ($isEditing) {
    $work = commar_get_focused_work_by_id($workId);
    if (!$work) {
        http_response_code(404);
    }
}

$pageTitle = $isEditing ? 'Editar obra en foco' : 'Nueva obra en foco';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo commar_admin_h($pageTitle); ?> | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
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
                    <div class="admin-page-actions" style="margin-bottom: 2rem;">
                        <div>
                            <span class="admin-kicker">Obras en foco</span>
                            <h2><?php echo commar_admin_h($pageTitle); ?></h2>
                        </div>
                        <a href="focused-works.php" class="admin-secondary-link">Volver al listado</a>
                    </div>

                    <?php if ($isEditing && !$work): ?>
                        <p class="admin-alert admin-alert-error">La obra solicitada no existe.</p>
                    <?php else: ?>
                        <div class="admin-form-container">
                            <form action="save-focused-work.php" method="post" enctype="multipart/form-data" class="admin-form">
                                <?php if ($isEditing): ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $work['id']; ?>">
                                <?php endif; ?>

                                <div class="admin-form-grid">
                                    <label>
                                        Título
                                        <input type="text" name="title" value="<?php echo commar_admin_h($work['title'] ?? ''); ?>" required>
                                    </label>
                                    <label>
                                        Categoría
                                        <input type="text" name="category" value="<?php echo commar_admin_h($work['category'] ?? ''); ?>" required>
                                    </label>
                                </div>
                                <div class="admin-form-grid">
                                    <label>
                                        Idioma
                                        <select name="lang" required>
                                            <option value="es" <?php echo ($work['lang'] ?? 'es') === 'es' ? 'selected' : ''; ?>>Español</option>
                                            <option value="en" <?php echo ($work['lang'] ?? '') === 'en' ? 'selected' : ''; ?>>Inglés</option>
                                            <option value="pt" <?php echo ($work['lang'] ?? '') === 'pt' ? 'selected' : ''; ?>>Portugués</option>
                                        </select>
                                    </label>
                                    <label class="admin-file-control">
                                        Imagen
                                        <span class="admin-file-input-wrap">
                                            <span class="admin-file-button">Subir imagen</span>
                                            <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" <?php echo !$isEditing ? 'required' : ''; ?> data-file-input>
                                        </span>
                                        <?php if ($isEditing && !empty($work['image'])): ?>
                                            <span class="admin-help">Imagen actual: <?php echo commar_admin_h($work['image']); ?>. Dejar vacío para no cambiar.</span>
                                            <img src="../<?php echo commar_admin_h($work['image']); ?>" alt="" style="max-width: 200px; margin-top: 0.5rem; border-radius: 0.3rem;">
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <label>
                                    Resumen
                                    <textarea name="summary" rows="4" required><?php echo commar_admin_h($work['summary'] ?? ''); ?></textarea>
                                </label>
                                <button type="submit" class="admin-button-primary">Guardar obra</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-media-picker" defer></script>
</body>
</html>
