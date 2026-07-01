document.querySelectorAll('[data-file-input]').forEach((input) => {
    const wrap = input.closest('.admin-file-input-wrap');
    const nameTarget = wrap?.querySelector('[data-file-name]');

    input.addEventListener('change', () => {
        if (!nameTarget) {
            return;
        }

        const files = Array.from(input.files || []);
        if (files.length === 0) {
            nameTarget.textContent = input.multiple ? 'Sin archivos seleccionados' : 'Sin archivo seleccionado';
            return;
        }

        nameTarget.textContent = files.length === 1 ? files[0].name : `${files.length} archivos seleccionados`;
    });
});

document.querySelectorAll('[data-menu-sortable]').forEach((list) => {
    const syncOrder = () => {
        Array.from(list.querySelectorAll('.admin-menu-editor-row')).forEach((row, index) => {
            const orderInput = row.querySelector('[data-menu-order]');
            if (orderInput) {
                orderInput.value = String(index + 1);
            }
        });
    };

    let draggedRow = null;

    list.addEventListener('dragstart', (event) => {
        const row = event.target.closest('.admin-menu-editor-row');
        if (!row) {
            return;
        }

        draggedRow = row;
        row.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragend', () => {
        draggedRow?.classList.remove('is-dragging');
        draggedRow = null;
        syncOrder();
    });

    list.addEventListener('dragover', (event) => {
        event.preventDefault();
        const target = event.target.closest('.admin-menu-editor-row');
        if (!draggedRow || !target || target === draggedRow) {
            return;
        }

        const targetBox = target.getBoundingClientRect();
        const shouldInsertAfter = event.clientY > targetBox.top + targetBox.height / 2;
        list.insertBefore(draggedRow, shouldInsertAfter ? target.nextSibling : target);
    });

    syncOrder();
});

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
    const featuredMediaValue = form.querySelector('[data-featured-media-value]');
    const featuredMediaModal = form.querySelector('[data-featured-media-modal]');
    const featuredMediaGrid = form.querySelector('[data-featured-media-grid]');
    const featuredMediaUpload = form.querySelector('[data-featured-media-upload]');
    const featuredMediaUploadButton = form.querySelector('[data-upload-featured-media]');
    const featuredMediaStatus = form.querySelector('[data-featured-media-status]');
    const acceptFeaturedMediaButton = form.querySelector('[data-accept-featured-media]');
    const aiImageButton = form.querySelector('[data-generate-article-image]');
    const aiImageStatus = form.querySelector('[data-ai-image-status]');
    const aiImageProgress = form.querySelector('[data-ai-image-progress]');
    const galleryMediaModal = form.querySelector('[data-gallery-media-modal]');
    const galleryMediaSelected = form.querySelector('[data-gallery-media-selected]');
    const galleryMediaUpload = form.querySelector('[data-gallery-media-upload]');
    const galleryMediaUploadButton = form.querySelector('[data-upload-gallery-media]');
    const galleryMediaStatus = form.querySelector('[data-gallery-media-status]');
    const galleryInput = form.querySelector('[data-gallery-input]');
    const galleryPreview = form.querySelector('[data-gallery-preview]');
    const galleryList = form.querySelector('[data-gallery-list]');

    const setFeaturedPreview = (path) => {
        if (!featuredPreview || !featuredPreviewWrap || !path) {
            return;
        }

        featuredPreview.src = path.startsWith('blob:') ? path : '../' + path;
        featuredPreview.hidden = false;
        featuredPreviewWrap.classList.remove('is-empty');
        featuredPreviewWrap.classList.remove('is-loading');
    };

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

    const syncGalleryMediaOptionsFromHidden = () => {
        const selectedValues = new Set(Array.from(galleryMediaSelected?.querySelectorAll('input[name="media_gallery_images[]"]') || [])
            .map((input) => input.value));

        galleryMediaModal?.querySelectorAll('[data-gallery-media-option]').forEach((option) => {
            option.checked = selectedValues.has(option.value);
        });
    };

    const syncFeaturedMediaOptionsFromHidden = () => {
        const selectedValue = featuredMediaValue?.value || '';

        featuredMediaModal?.querySelectorAll('[data-featured-media-option]').forEach((option) => {
            option.checked = selectedValue !== '' && option.value === selectedValue;
            option.dataset.wasChecked = option.checked ? 'true' : 'false';
        });
    };

    featuredInput?.addEventListener('change', () => {
        const file = featuredInput.files?.[0];

        if (file && featuredPreview) {
            setFeaturedPreview(URL.createObjectURL(file));
            if (generatedImage) {
                generatedImage.value = '';
            }
            if (featuredMediaValue) {
                featuredMediaValue.value = '';
            }
            if (aiImageButton) {
                aiImageButton.textContent = 'Generar con IA';
            }
        }
    });

    form.querySelectorAll('[data-open-featured-media]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!featuredMediaModal) {
                return;
            }

            syncFeaturedMediaOptionsFromHidden();
            featuredMediaModal.hidden = false;
            document.body.classList.add('admin-modal-open');
        });
    });

    form.querySelectorAll('[data-close-featured-media]').forEach((button) => {
        button.addEventListener('click', () => {
            if (featuredMediaModal) {
                featuredMediaModal.hidden = true;
            }
            document.body.classList.remove('admin-modal-open');
        });
    });

    form.querySelectorAll('[data-featured-media-option]').forEach((option) => {
        option.addEventListener('click', () => {
            const wasChecked = option.dataset.wasChecked === 'true';

            form.querySelectorAll('[data-featured-media-option]').forEach((otherOption) => {
                otherOption.checked = false;
                otherOption.dataset.wasChecked = 'false';
            });

            option.checked = !wasChecked;
            option.dataset.wasChecked = option.checked ? 'true' : 'false';
        });
        option.dataset.wasChecked = option.checked ? 'true' : 'false';
    });

    acceptFeaturedMediaButton?.addEventListener('click', () => {
        const selected = featuredMediaModal?.querySelector('[data-featured-media-option]:checked');
        const path = selected?.value || '';

        if (!path) {
            if (featuredMediaStatus) {
                featuredMediaStatus.textContent = 'Seleccioná una imagen para continuar.';
            }
            return;
        }

        if (featuredMediaValue) {
            featuredMediaValue.value = path;
        }
        if (generatedImage) {
            generatedImage.value = '';
        }
        setFeaturedPreview(path);
        if (featuredMediaModal) {
            featuredMediaModal.hidden = true;
        }
        document.body.classList.remove('admin-modal-open');
        if (aiImageStatus) {
            aiImageStatus.textContent = 'Imagen seleccionada desde mediateca. Guardá el artículo para aplicarla.';
        }
    });

    featuredMediaUploadButton?.addEventListener('click', async () => {
        const file = featuredMediaUpload?.files?.[0];
        const csrfToken = form.querySelector('[name="csrf_token"]')?.value || '';

        if (!file) {
            if (featuredMediaStatus) {
                featuredMediaStatus.textContent = 'Seleccioná una imagen para subir.';
            }
            return;
        }

        featuredMediaUploadButton.disabled = true;
        if (featuredMediaStatus) {
            featuredMediaStatus.textContent = 'Subiendo imagen...';
        }

        const body = new FormData();
        body.append('csrf_token', csrfToken);
        body.append('image', file);

        try {
            const response = await fetch('upload-media-image.php', {
                method: 'POST',
                credentials: 'same-origin',
                body,
            });
            const result = await response.json();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo subir la imagen.');
            }

            const choice = document.createElement('label');
            const input = document.createElement('input');
            const image = document.createElement('img');

            choice.className = 'admin-media-choice';
            input.type = 'checkbox';
            input.dataset.featuredMediaOption = '';
            input.dataset.wasChecked = 'true';
            input.value = result.path;
            input.checked = true;
            input.addEventListener('click', () => {
                const wasChecked = input.dataset.wasChecked === 'true';
                form.querySelectorAll('[data-featured-media-option]').forEach((otherOption) => {
                    otherOption.checked = false;
                    otherOption.dataset.wasChecked = 'false';
                });
                input.checked = !wasChecked;
                input.dataset.wasChecked = input.checked ? 'true' : 'false';
            });
            image.src = '../' + result.path + '?v=' + Date.now();
            image.alt = '';
            form.querySelectorAll('[data-featured-media-option]').forEach((otherOption) => {
                otherOption.checked = false;
                otherOption.dataset.wasChecked = 'false';
            });
            choice.append(input, image);
            featuredMediaGrid?.prepend(choice);

            if (featuredMediaStatus) {
                featuredMediaStatus.textContent = 'Imagen subida y seleccionada. Tocá Aceptar para aplicarla.';
            }
        } catch (error) {
            if (featuredMediaStatus) {
                featuredMediaStatus.textContent = error.message || 'No se pudo subir la imagen.';
            }
        } finally {
            featuredMediaUploadButton.disabled = false;
        }
    });

    form.querySelectorAll('[data-open-gallery-media]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!galleryMediaModal) {
                return;
            }

            syncGalleryMediaOptionsFromHidden();
            galleryMediaModal.hidden = false;
            document.body.classList.add('admin-modal-open');
        });
    });

    galleryMediaUploadButton?.addEventListener('click', async () => {
        const files = Array.from(galleryMediaUpload?.files || []);
        const csrfToken = form.querySelector('[name="csrf_token"]')?.value || '';

        if (files.length === 0) {
            if (galleryMediaStatus) {
                galleryMediaStatus.textContent = 'Seleccioná una o más imágenes para subir.';
            }
            return;
        }

        galleryMediaUploadButton.disabled = true;
        if (galleryMediaStatus) {
            galleryMediaStatus.textContent = 'Subiendo imágenes...';
        }

        try {
            for (const file of files) {
                const body = new FormData();
                body.append('csrf_token', csrfToken);
                body.append('image', file);

                const response = await fetch('upload-media-image.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body,
                });
                const result = await response.json();

                if (!response.ok || !result.ok) {
                    throw new Error(result.error || 'No se pudo subir una imagen.');
                }

                const choice = document.createElement('label');
                const input = document.createElement('input');
                const image = document.createElement('img');

                choice.className = 'admin-media-choice';
                input.type = 'checkbox';
                input.dataset.galleryMediaOption = '';
                input.value = result.path;
                input.checked = true;
                image.src = '../' + result.path + '?v=' + Date.now();
                image.alt = '';
                choice.append(input, image);
                galleryMediaModal?.querySelector('[data-gallery-media-grid]')?.prepend(choice);
            }

            if (galleryMediaStatus) {
                galleryMediaStatus.textContent = 'Imágenes subidas y seleccionadas. Tocá Aceptar para aplicarlas.';
            }
        } catch (error) {
            if (galleryMediaStatus) {
                galleryMediaStatus.textContent = error.message || 'No se pudieron subir las imágenes.';
            }
        } finally {
            galleryMediaUploadButton.disabled = false;
        }
    });

    form.querySelectorAll('[data-close-gallery-media]').forEach((button) => {
        button.addEventListener('click', () => {
            if (galleryMediaModal) {
                galleryMediaModal.hidden = true;
            }
            document.body.classList.remove('admin-modal-open');
        });
    });

    form.querySelectorAll('[data-accept-gallery-media]').forEach((button) => {
        button.addEventListener('click', () => {
            const selected = Array.from(galleryMediaModal?.querySelectorAll('[data-gallery-media-option]:checked') || []);

            if (galleryMediaSelected) {
                galleryMediaSelected.innerHTML = '';
                selected.forEach((option) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'media_gallery_images[]';
                    input.value = option.value;
                    galleryMediaSelected.append(input);
                });
            }

            if (galleryPreview) {
                galleryPreview.innerHTML = '';
                selected.slice(0, 10).forEach((option) => {
                    const item = document.createElement('div');
                    const image = document.createElement('img');
                    const label = document.createElement('span');

                    item.className = 'admin-gallery-item';
                    image.src = '../' + option.value;
                    image.alt = '';
                    image.width = 72;
                    image.height = 72;
                    label.textContent = 'Mediateca';
                    item.append(image, label);
                    galleryPreview.append(item);
                });
            }

            if (galleryMediaModal) {
                galleryMediaModal.hidden = true;
            }
            document.body.classList.remove('admin-modal-open');
        });
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
        const csrfToken = form.querySelector('[name="csrf_token"]')?.value || '';

        if (!title || !description || !content) {
            if (aiImageStatus) {
                aiImageStatus.textContent = 'Completá título, descripción y cuerpo antes de generar.';
            }
            return;
        }

        aiImageButton.disabled = true;
        featuredPreviewWrap?.classList.add('is-loading');
        if (aiImageProgress) {
            aiImageProgress.hidden = false;
        }
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
                body: new URLSearchParams({ title, description, content, csrf_token: csrfToken }),
            });
            const contentType = response.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                throw new Error(response.redirected ? 'La sesión del admin expiró. Volvé a ingresar.' : 'El servidor no devolvió una respuesta JSON.');
            }

            const result = await response.json();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo generar la imagen.');
            }

            setFeaturedPreview(result.path + '?v=' + Date.now());
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
            if (featuredMediaValue) {
                featuredMediaValue.value = '';
            }
            aiImageButton.textContent = 'Generar con IA';
            if (aiImageStatus) {
                aiImageStatus.textContent = 'Imagen generada. Hacé click en el thumbnail para ampliarla. Guardá el artículo para aplicarla.';
            }
        } catch (error) {
            featuredPreviewWrap?.classList.remove('is-loading');
            if (!featuredPreview || featuredPreview.hidden || !featuredPreview.getAttribute('src')) {
                featuredPreviewWrap?.classList.add('is-empty');
            }
            if (aiImageStatus) {
                aiImageStatus.textContent = error.message || 'No se pudo generar la imagen.';
            }
        } finally {
            aiImageButton.disabled = false;
            if (aiImageProgress) {
                aiImageProgress.hidden = true;
            }
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
