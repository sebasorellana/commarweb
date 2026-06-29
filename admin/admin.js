document.querySelectorAll('[data-article-form]').forEach((form) => {
    const editor = form.querySelector('[data-rich-editor]');
    const source = form.querySelector('[data-content-source]');
    const htmlSource = form.querySelector('[data-content-html]');
    const featuredInput = form.querySelector('[data-featured-input]');
    const featuredPreview = form.querySelector('[data-featured-preview]');
    const featuredPreviewWrap = form.querySelector('[data-featured-preview-wrap]');
    const featuredModal = form.querySelector('[data-featured-modal]');
    const featuredModalImage = form.querySelector('[data-featured-modal-image]');
    const generatedImage = form.querySelector('[data-generated-image]');
    const generatedImageWidth = form.querySelector('[data-generated-image-width]');
    const generatedImageHeight = form.querySelector('[data-generated-image-height]');
    const aiImageButton = form.querySelector('[data-generate-article-image]');
    const aiImageStatus = form.querySelector('[data-ai-image-status]');
    const galleryInput = form.querySelector('[data-gallery-input]');
    const galleryPreview = form.querySelector('[data-gallery-preview]');
    const galleryList = form.querySelector('[data-gallery-list]');

    const syncEditor = () => {
        if (!editor || !source) {
            return;
        }

        const blocks = Array.from(editor.querySelectorAll('p, li'))
            .map((block) => block.innerText.trim())
            .filter(Boolean);
        const text = blocks.length > 0 ? blocks.join('\n\n') : editor.innerText.trim();
        source.value = text;

        if (htmlSource) {
            htmlSource.value = editor.innerHTML.trim();
        }
    };

    form.querySelectorAll('[data-editor-command]').forEach((button) => {
        button.addEventListener('click', () => {
            const command = button.dataset.editorCommand;
            const value = button.dataset.editorValue || null;

            editor?.focus();
            document.execCommand(command, false, value);
            syncEditor();
        });
    });

    editor?.addEventListener('input', syncEditor);
    form.addEventListener('submit', syncEditor);

    featuredInput?.addEventListener('change', () => {
        const file = featuredInput.files?.[0];

        if (file && featuredPreview) {
            featuredPreview.src = URL.createObjectURL(file);
            featuredPreviewWrap?.classList.remove('is-empty');
            featuredPreviewWrap?.classList.remove('is-loading');
            if (generatedImage) {
                generatedImage.value = '';
            }
            if (aiImageButton) {
                aiImageButton.textContent = 'Generar con IA';
            }
        }
    });

    featuredPreviewWrap?.addEventListener('click', () => {
        if (featuredPreviewWrap.classList.contains('is-empty') || !featuredPreview?.src || !featuredModal || !featuredModalImage) {
            return;
        }

        featuredModalImage.src = featuredPreview.src;
        featuredModal.hidden = false;
        document.body.classList.add('admin-modal-open');
    });

    form.querySelectorAll('[data-close-featured-preview]').forEach((button) => {
        button.addEventListener('click', () => {
            if (featuredModal) {
                featuredModal.hidden = true;
            }
            document.body.classList.remove('admin-modal-open');
        });
    });

    aiImageButton?.addEventListener('click', async () => {
        syncEditor();

        const title = form.querySelector('[name="title"]')?.value.trim() || '';
        const description = form.querySelector('[name="description"]')?.value.trim() || '';
        const content = source?.value.trim() || '';

        if (!title || !description || !content) {
            if (aiImageStatus) {
                aiImageStatus.textContent = 'Completá título, descripción y cuerpo antes de generar.';
            }
            return;
        }

        aiImageButton.disabled = true;
        featuredPreviewWrap?.classList.remove('is-empty');
        featuredPreviewWrap?.classList.add('is-loading');
        if (aiImageStatus) {
            aiImageStatus.textContent = 'Generando imagen... puede tardar hasta 90 segundos.';
        }

        try {
            const response = await fetch('generate-article-image.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ title, description, content }),
            });
            const contentType = response.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                throw new Error(response.redirected ? 'La sesión del admin expiró. Volvé a ingresar.' : 'El servidor no devolvió una respuesta JSON.');
            }

            const result = await response.json();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo generar la imagen.');
            }

            if (featuredPreview) {
                featuredPreview.src = '../' + result.path + '?v=' + Date.now();
            }
            featuredPreviewWrap?.classList.remove('is-empty');
            featuredPreviewWrap?.classList.remove('is-loading');

            if (generatedImage) {
                generatedImage.value = result.path;
            }
            if (generatedImageWidth) {
                generatedImageWidth.value = result.width || 0;
            }
            if (generatedImageHeight) {
                generatedImageHeight.value = result.height || 0;
            }
            if (featuredInput) {
                featuredInput.value = '';
            }
            aiImageButton.textContent = 'Volver a generar con IA';
            if (aiImageStatus) {
                aiImageStatus.textContent = 'Imagen generada. Hacé click en el thumbnail para ampliarla. Guardá el artículo para aplicarla.';
            }
        } catch (error) {
            featuredPreviewWrap?.classList.remove('is-loading');
            if (!featuredPreview?.src) {
                featuredPreviewWrap?.classList.add('is-empty');
            }
            if (aiImageStatus) {
                aiImageStatus.textContent = error.message || 'No se pudo generar la imagen.';
            }
        } finally {
            aiImageButton.disabled = false;
        }
    });

    galleryInput?.addEventListener('change', () => {
        if (!galleryPreview) {
            return;
        }

        galleryPreview.innerHTML = '';
        const existingCount = galleryList ? galleryList.querySelectorAll('.admin-gallery-item').length : 0;
        const availableSlots = Math.max(0, 10 - existingCount);
        Array.from(galleryInput.files || []).slice(0, availableSlots).forEach((file) => {
            const item = document.createElement('div');
            const image = document.createElement('img');
            const label = document.createElement('span');

            item.className = 'admin-gallery-item';
            image.src = URL.createObjectURL(file);
            image.alt = '';
            image.width = 72;
            image.height = 72;
            label.textContent = 'Nueva';

            item.append(image, label);
            galleryPreview.append(item);
        });

        if ((galleryInput.files?.length || 0) > availableSlots) {
            const item = document.createElement('div');
            item.className = 'admin-gallery-limit';
            item.textContent = 'Máximo 10 imágenes por obra.';
            galleryPreview.append(item);
        }
    });

    if (!galleryList) {
        return;
    }

    let draggedItem = null;

    galleryList.addEventListener('dragstart', (event) => {
        const item = event.target.closest('.admin-gallery-item');

        if (!item) {
            return;
        }

        draggedItem = item;
        item.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
    });

    galleryList.addEventListener('dragend', () => {
        draggedItem?.classList.remove('is-dragging');
        draggedItem = null;
    });

    galleryList.addEventListener('dragover', (event) => {
        event.preventDefault();

        const target = event.target.closest('.admin-gallery-item');
        if (!draggedItem || !target || target === draggedItem) {
            return;
        }

        const targetBox = target.getBoundingClientRect();
        const shouldInsertAfter = event.clientY > targetBox.top + targetBox.height / 2;

        galleryList.insertBefore(draggedItem, shouldInsertAfter ? target.nextSibling : target);
    });
});
