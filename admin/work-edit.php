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
$gallery = $work ? json_decode((string) ($work['gallery_json'] ?? '[]'), true) : [];
$gallery = is_array($gallery) ? array_values(array_filter($gallery, static function ($item): bool {
    return is_array($item) && trim((string) ($item['path'] ?? '')) !== '';
})) : [];
if ($work && empty($gallery) && !empty($work['image'])) {
    $gallery[] = [
        'path' => (string) $work['image'],
        'width' => (int) ($work['image_width'] ?? 0),
        'height' => (int) ($work['image_height'] ?? 0),
        'alt' => (string) ($work['hero_alt'] ?? ''),
    ];
}
$descriptionValue = is_array($description) ? implode("\n\n", array_map('strval', $description)) : '';
$metricsValue = '';
if (is_array($metrics)) {
    foreach ($metrics as $label => $value) {
        $metricsValue .= (string) $label . ': ' . (string) $value . "\n";
    }
}

$categories = commar_admin_work_categories();
$selectedCategory = (string) ($work['category'] ?? '');
$categoryNames = array_map(static fn(array $category): string => (string) $category['name'], $categories);
if ($selectedCategory !== '' && !in_array($selectedCategory, $categoryNames, true)) {
    array_unshift($categories, [
        'id' => 0,
        'name' => $selectedCategory,
        'display_order' => 0,
    ]);
}

$pageTitle = $isEditing ? 'Editar obra' : 'Nueva obra';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo commar_admin_h($pageTitle); ?> | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css?v=20260629-admin-works-ui">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('works'); ?>
        <div class="admin-main">
            <?php commar_admin_header($pageTitle); ?>
            <main class="admin-content">
                <?php commar_admin_works_nav('works'); ?>
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions admin-work-edit-head">
                        <div>
                            <span class="admin-kicker">Directorio de obras</span>
                            <h2><?php echo commar_admin_h($pageTitle); ?></h2>
                        </div>
                        <a href="works.php" class="admin-secondary-link">Volver al listado</a>
                    </div>

                    <?php if ($isEditing && !$work): ?>
                        <p class="admin-alert admin-alert-error">La obra solicitada no existe.</p>
                    <?php else: ?>
                        <div class="admin-form-container admin-work-form-container">
                            <form action="save-work.php" method="post" enctype="multipart/form-data" class="admin-form admin-work-form" data-article-form>
                                <?php if ($isEditing): ?>
                                    <input type="hidden" name="id" value="<?php echo (int) $work['id']; ?>">
                                <?php endif; ?>

                                <div class="admin-work-editor-grid">
                                    <div class="admin-work-editor-main">
                                        <section class="admin-work-section">
                                            <div class="admin-work-section-head">
                                                <span class="admin-kicker">Identificación</span>
                                                <h3>Datos principales</h3>
                                            </div>
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
                                                    <select name="category" required>
                                                        <option value="">Seleccionar categoría</option>
                                                        <?php foreach ($categories as $category): ?>
                                                            <?php $categoryName = (string) ($category['name'] ?? ''); ?>
                                                            <option value="<?php echo commar_admin_h($categoryName); ?>" <?php echo $selectedCategory === $categoryName ? 'selected' : ''; ?>>
                                                                <?php echo commar_admin_h($categoryName); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
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
                                        </section>

                                        <section class="admin-work-section">
                                            <div class="admin-work-section-head">
                                                <span class="admin-kicker">Contenido</span>
                                                <h3>Texto de la obra</h3>
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
                                        </section>
                                    </div>

                                    <aside class="admin-work-editor-side">
                                        <section class="admin-work-section admin-work-media-section">
                                            <div class="admin-work-section-head">
                                                <span class="admin-kicker">Imágenes</span>
                                                <h3>Galería</h3>
                                            </div>
                                            <label>
                                                Texto alternativo
                                                <input type="text" name="hero_alt" value="<?php echo commar_admin_h($work['hero_alt'] ?? ''); ?>">
                                            </label>
                                            <label>
                                                Cargar imágenes
                                                <input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple <?php echo !$isEditing ? 'required' : ''; ?> data-gallery-input>
                                            </label>
                                            <span class="admin-help">Subí de 1 a 10 imágenes. La primera imagen del orden será la principal. Arrastrá para ordenar las existentes.</span>
                                            <div class="admin-gallery-list" data-gallery-list>
                                                <?php foreach ($gallery as $galleryItem): ?>
                                                    <?php $galleryPath = (string) ($galleryItem['path'] ?? ''); ?>
                                                    <?php if ($galleryPath !== ''): ?>
                                                        <div class="admin-gallery-item" draggable="true">
                                                            <img src="../<?php echo commar_admin_h($galleryPath); ?>" alt="" width="72" height="72">
                                                            <span>Arrastrar</span>
                                                            <input type="hidden" name="gallery_existing[]" value="<?php echo commar_admin_h($galleryPath); ?>">
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="admin-gallery-list admin-gallery-list-new" data-gallery-preview></div>
                                        </section>
                                    </aside>
                                </div>
                                <div class="admin-work-savebar">
                                    <a href="works.php" class="admin-secondary-link">Cancelar</a>
                                    <button type="submit" class="admin-button-primary">Guardar obra</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260628-works-gallery" defer></script>
</body>
</html>
