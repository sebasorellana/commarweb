<?php
$article = $article ?? null;
$content = $content ?? '';
$isEditing = $article !== null;
$selectedStatus = (string) ($article['status'] ?? 'published');
$selectedCategory = (string) ($article['category'] ?? 'Arquitectura');
$featuredImage = $isEditing ? (string) ($article['image'] ?? '') : '';
$gallery = is_array($article['gallery'] ?? null) ? $article['gallery'] : [];
$tags = is_array($article['tags'] ?? null) ? $article['tags'] : [];
$tagsValue = implode(', ', array_map('strval', $tags));
$editorHtml = '';

foreach (preg_split('/\R{2,}/', trim($content)) ?: [] as $paragraph) {
    $paragraph = trim($paragraph);

    if ($paragraph !== '') {
        $editorHtml .= '<p>' . nl2br(commar_admin_h($paragraph)) . '</p>';
    }
}

if ($editorHtml === '') {
    $editorHtml = '<p><br></p>';
}
?>
<form action="save-article.php" method="post" enctype="multipart/form-data" class="admin-article-form" data-article-form>
    <?php if ($isEditing): ?>
        <input type="hidden" name="original_slug" value="<?php echo commar_admin_h($article['slug']); ?>">
    <?php endif; ?>

    <div class="admin-article-editor">
        <section class="admin-editor-main">
            <label>
                Título
                <input type="text" name="title" maxlength="140" value="<?php echo commar_admin_h((string) ($article['title'] ?? '')); ?>" required>
            </label>
            <label>
                Descripción
                <textarea name="description" rows="6" required><?php echo commar_admin_h((string) ($article['description'] ?? '')); ?></textarea>
            </label>
            <div class="admin-rich-field">
                <span class="admin-field-label">Cuerpo del artículo</span>
                <div class="admin-editor-toolbar" aria-label="Herramientas de texto">
                    <button type="button" data-editor-command="bold">B</button>
                    <button type="button" data-editor-command="italic">I</button>
                    <button type="button" data-editor-command="insertUnorderedList">Lista</button>
                    <button type="button" data-editor-command="formatBlock" data-editor-value="p">P</button>
                </div>
                <div class="admin-rich-editor" contenteditable="true" data-rich-editor><?php echo $editorHtml; ?></div>
                <textarea name="content" class="admin-content-source" required data-content-source><?php echo commar_admin_h($content); ?></textarea>
                <textarea name="content_html" class="admin-content-source" data-content-html><?php echo commar_admin_h((string) ($article['content_html'] ?? $editorHtml)); ?></textarea>
            </div>
        </section>

        <aside class="admin-editor-sidebar">
            <button type="submit" class="admin-save-button">Guardar</button>

            <section class="admin-sidebar-card">
                <h3>Estado</h3>
                <label class="admin-radio-row">
                    <input type="radio" name="status" value="draft" <?php echo $selectedStatus === 'draft' ? 'checked' : ''; ?>>
                    Borrador
                </label>
                <label class="admin-radio-row">
                    <input type="radio" name="status" value="published" <?php echo $selectedStatus !== 'draft' ? 'checked' : ''; ?>>
                    Publicado
                </label>
            </section>

            <section class="admin-sidebar-card">
                <label>
                    Categorías
                    <select name="category" required>
                        <option value="Arquitectura" <?php echo $selectedCategory === 'Arquitectura' ? 'selected' : ''; ?>>Arquitectura</option>
                        <?php if ($selectedCategory !== '' && $selectedCategory !== 'Arquitectura'): ?>
                            <option value="<?php echo commar_admin_h($selectedCategory); ?>" selected><?php echo commar_admin_h($selectedCategory); ?></option>
                        <?php endif; ?>
                    </select>
                </label>
            </section>

            <section class="admin-sidebar-card">
                <label>
                    Tags
                    <input type="text" name="tags" value="<?php echo commar_admin_h($tagsValue); ?>" maxlength="240" placeholder="obra, permisos, documentación">
                    <span class="admin-help">Separá cada tag con coma.</span>
                </label>
            </section>

            <section class="admin-sidebar-card">
                <label>
                    Video de YouTube (opcional)
                    <input type="url" name="youtube_url" value="<?php echo commar_admin_h((string) ($article['youtube_url'] ?? '')); ?>" placeholder="https://www.youtube.com/watch?v=...">
                    <span class="admin-help">Copiá y pegá la URL completa del video.</span>
                </label>
            </section>

            <section class="admin-sidebar-card">
                <label>
                    Imagen destacada
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-featured-input>
                </label>
                <input type="hidden" name="generated_image" value="<?php echo commar_admin_h($featuredImage); ?>" data-generated-image>
                <input type="hidden" name="generated_image_width" value="<?php echo $featuredImage !== '' ? (int) ($article['image_width'] ?? 0) : 0; ?>" data-generated-image-width>
                <input type="hidden" name="generated_image_height" value="<?php echo $featuredImage !== '' ? (int) ($article['image_height'] ?? 0) : 0; ?>" data-generated-image-height>
                <button type="button" class="admin-image-preview <?php echo $featuredImage === '' ? 'is-empty' : ''; ?>" data-featured-preview-wrap data-open-featured-preview>
                    <img src="<?php echo $featuredImage !== '' ? '../' . commar_admin_h($featuredImage) : ''; ?>" alt="" width="120" height="90" data-featured-preview>
                    <span>Sin imagen</span>
                </button>
                <button type="button" class="admin-secondary-link admin-ai-image-button" data-generate-article-image><?php echo $featuredImage !== '' ? 'Volver a generar con IA' : 'Generar con IA'; ?></button>
                <span class="admin-help admin-ai-image-status" data-ai-image-status>Usa título, descripción y cuerpo del artículo.</span>
            </section>

            <section class="admin-sidebar-card">
                <label>
                    Galería de imágenes
                    <input type="file" name="gallery_images[]" accept="image/jpeg,image/png,image/webp" multiple data-gallery-input>
                </label>
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

    <div class="admin-image-modal" hidden data-featured-modal>
        <button type="button" class="admin-image-modal-backdrop" data-close-featured-preview></button>
        <div class="admin-image-modal-dialog" role="dialog" aria-modal="true" aria-label="Vista previa de imagen destacada">
            <button type="button" class="admin-image-modal-close" data-close-featured-preview>Cerrar</button>
            <img src="" alt="Vista previa de imagen destacada" data-featured-modal-image>
        </div>
    </div>
</form>
