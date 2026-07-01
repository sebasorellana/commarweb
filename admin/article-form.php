<?php
require_once dirname(__DIR__) . '/includes/media.php';
require_once dirname(__DIR__) . '/includes/articles.php';

$article = $article ?? null;
$content = $content ?? '';
$isEditing = $article !== null;
$selectedStatus = (string) ($article['status'] ?? 'published');
$selectedCategory = (string) ($article['category'] ?? 'Arquitectura');
$featuredImage = $isEditing ? (string) ($article['image'] ?? '') : '';
$gallery = is_array($article['gallery'] ?? null) ? $article['gallery'] : [];
$tags = is_array($article['tags'] ?? null) ? $article['tags'] : [];
$tagsValue = implode(', ', array_map('strval', $tags));
$storedHtml = trim((string) ($article['content_html'] ?? ''));
$editorHtml = $storedHtml;
$mediaImages = commar_media_image_items(48);
$availableTags = [];

foreach (commar_dynamic_articles(false) as $existingArticle) {
    foreach (($existingArticle['tags'] ?? []) as $tag) {
        $tag = trim((string) $tag);

        if ($tag !== '') {
            $availableTags[commar_text_lower($tag)] = $tag;
        }
    }
}

ksort($availableTags);

if ($editorHtml === '') {
    foreach (preg_split('/\R{2,}/', trim($content)) ?: [] as $paragraph) {
        $paragraph = trim($paragraph);

        if ($paragraph !== '') {
            $editorHtml .= '<p>' . nl2br(commar_admin_h($paragraph)) . '</p>';
        }
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
                <div class="admin-rich-editor admin-article-rich-editor" contenteditable="true" data-rich-editor data-article-rich-editor><?php echo $editorHtml; ?></div>
                <textarea name="content" class="admin-content-source" required data-content-source><?php echo commar_admin_h($content); ?></textarea>
                <textarea name="content_html" class="admin-content-source" data-content-html><?php echo commar_admin_h((string) ($article['content_html'] ?? $editorHtml)); ?></textarea>
            </div>
        </section>

        <aside class="admin-editor-sidebar">
            <button type="submit" class="admin-save-button">Guardar</button>

            <section class="admin-sidebar-card">
                <h3>Estado</h3>
                <div class="admin-status-options">
                    <label class="admin-radio-row">
                        <input type="radio" name="status" value="draft" <?php echo $selectedStatus === 'draft' ? 'checked' : ''; ?>>
                        <span>Borrador</span>
                    </label>
                    <label class="admin-radio-row">
                        <input type="radio" name="status" value="published" <?php echo $selectedStatus !== 'draft' ? 'checked' : ''; ?>>
                        <span>Publicado</span>
                    </label>
                </div>
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
                    <input type="hidden" name="tags" value="<?php echo commar_admin_h($tagsValue); ?>" data-tags-value>
                    <div class="admin-tags-field" data-tags-field data-suggestions="<?php echo commar_admin_h(json_encode(array_values($availableTags), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]'); ?>">
                        <div class="admin-tags-list" data-tags-list></div>
                        <input type="text" maxlength="40" placeholder="Agregar tag">
                    </div>
                    <div class="admin-tags-suggestions" data-tags-suggestions></div>
                    <span class="admin-help">Separá cada tag con coma.</span>
                </label>
            </section>

            <section class="admin-sidebar-card">
                <label>
                    Video de YouTube (opcional)
                    <input type="url" name="youtube_url" value="<?php echo commar_admin_h((string) ($article['youtube_url'] ?? '')); ?>" placeholder="https://www.youtube.com/watch?v=..." data-youtube-url>
                    <span class="admin-help">Copiá y pegá la URL completa del video.</span>
                </label>
                <div class="admin-youtube-shortcode" hidden data-youtube-shortcode-wrap>
                    <span>Shortcode</span>
                    <code data-youtube-shortcode></code>
                    <div class="admin-youtube-actions">
                        <button type="button" data-copy-youtube-shortcode>Copiar</button>
                        <button type="button" data-insert-youtube-shortcode>Insertar</button>
                    </div>
                </div>
            </section>

            <section class="admin-sidebar-card">
                <h3>Imagen destacada</h3>
                <input type="hidden" name="generated_image" value="<?php echo commar_admin_h($featuredImage); ?>" data-generated-image>
                <input type="hidden" name="generated_image_width" value="<?php echo $featuredImage !== '' ? (int) ($article['image_width'] ?? 0) : 0; ?>" data-generated-image-width>
                <input type="hidden" name="generated_image_height" value="<?php echo $featuredImage !== '' ? (int) ($article['image_height'] ?? 0) : 0; ?>" data-generated-image-height>
                <input type="hidden" name="media_featured_image" value="<?php echo commar_admin_h($featuredImage); ?>" data-featured-media-value>
                <button type="button" class="admin-image-preview <?php echo $featuredImage === '' ? 'is-empty' : ''; ?>" data-featured-preview-wrap data-open-featured-preview>
                    <?php if ($featuredImage !== ''): ?>
                        <img src="../<?php echo commar_admin_h($featuredImage); ?>" alt="" width="120" height="90" data-featured-preview>
                    <?php else: ?>
                        <img src="" alt="" width="120" height="90" hidden data-featured-preview>
                    <?php endif; ?>
                    <span>Sin imagen</span>
                </button>
                <div class="admin-featured-actions">
                    <button type="button" class="admin-featured-button admin-featured-button-add" data-open-featured-media>Agregar imagen</button>
                    <button type="button" class="admin-featured-button admin-featured-button-ai admin-ai-image-button" data-generate-article-image>Generar con IA</button>
                </div>
                <span class="admin-help admin-ai-image-status" data-ai-image-status>Usa título, descripción y cuerpo del artículo.</span>
                <div class="admin-ai-progress" hidden data-ai-image-progress>
                    <span></span>
                </div>
                <div class="admin-featured-media-modal" hidden data-featured-media-modal>
                    <button type="button" class="admin-modal-backdrop" data-close-featured-media></button>
                    <section class="admin-modal-card admin-featured-media-card" role="dialog" aria-modal="true" aria-labelledby="featured-media-title">
                        <div class="admin-modal-head">
                            <div>
                                <span class="admin-kicker">Mediateca</span>
                                <h2 id="featured-media-title">Agregar imagen</h2>
                            </div>
                            <button type="button" class="admin-modal-close" aria-label="Cerrar" data-close-featured-media>&times;</button>
                        </div>
                        <div class="admin-featured-media-upload">
                            <label class="admin-file-control">
                                Agregar medio
                                <span class="admin-file-input-wrap">
                                    <span class="admin-file-button">Subir imagen</span>
                                    <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                                    <input type="file" accept="image/jpeg,image/png,image/webp" data-featured-media-upload data-file-input>
                                </span>
                            </label>
                            <button type="button" class="admin-button-primary" data-upload-featured-media>Subir y seleccionar</button>
                        </div>
                        <p class="admin-help" data-featured-media-status>Elegí una imagen disponible o subí una nueva.</p>
                        <div class="admin-media-picker-grid admin-featured-media-grid" data-featured-media-grid>
                            <?php foreach ($mediaImages as $item): ?>
                                <?php $path = (string) $item['path']; ?>
                                <label class="admin-media-choice">
                                    <input type="checkbox" data-featured-media-option value="<?php echo commar_admin_h($path); ?>" <?php echo $featuredImage === $path ? 'checked' : ''; ?>>
                                    <img src="../<?php echo commar_admin_h($path); ?>" alt="">
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-featured-media-footer">
                            <button type="button" class="admin-secondary-link" data-close-featured-media>Cancelar</button>
                            <button type="button" class="admin-button-primary" data-accept-featured-media>Aceptar</button>
                        </div>
                    </section>
                </div>
            </section>

            <section class="admin-sidebar-card">
                <h3>Galería de imágenes</h3>
                <button type="button" class="admin-featured-button admin-featured-button-add admin-gallery-media-open" data-open-gallery-media>Agregar imágenes</button>
                <div data-gallery-media-selected></div>
                <div class="admin-featured-media-modal" hidden data-gallery-media-modal>
                    <button type="button" class="admin-modal-backdrop" data-close-gallery-media></button>
                    <section class="admin-modal-card admin-featured-media-card" role="dialog" aria-modal="true" aria-labelledby="gallery-media-title">
                        <div class="admin-modal-head">
                            <div>
                                <span class="admin-kicker">Mediateca</span>
                                <h2 id="gallery-media-title">Agregar imágenes</h2>
                            </div>
                            <button type="button" class="admin-modal-close" aria-label="Cerrar" data-close-gallery-media>&times;</button>
                        </div>
                        <div class="admin-featured-media-upload">
                            <label class="admin-file-control">
                                Agregar medio
                                <span class="admin-file-input-wrap">
                                    <span class="admin-file-button">Subir imágenes</span>
                                    <span class="admin-file-name" data-file-name>Sin archivos seleccionados</span>
                                    <input type="file" accept="image/jpeg,image/png,image/webp" multiple data-gallery-media-upload data-file-input>
                                </span>
                            </label>
                            <button type="button" class="admin-button-primary" data-upload-gallery-media>Subir y seleccionar</button>
                        </div>
                        <p class="admin-help" data-gallery-media-status>Podés seleccionar varias imágenes para sumar a la galería.</p>
                        <div class="admin-media-picker-grid admin-featured-media-grid" data-gallery-media-grid>
                            <?php foreach ($mediaImages as $item): ?>
                                <?php $path = (string) $item['path']; ?>
                                <label class="admin-media-choice">
                                    <input type="checkbox" data-gallery-media-option value="<?php echo commar_admin_h($path); ?>">
                                    <img src="../<?php echo commar_admin_h($path); ?>" alt="">
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="admin-featured-media-footer">
                            <button type="button" class="admin-secondary-link" data-close-gallery-media>Cancelar</button>
                            <button type="button" class="admin-button-primary" data-accept-gallery-media>Aceptar</button>
                        </div>
                    </section>
                </div>
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
