<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/team.php';

commar_admin_require_login();

$members = commar_team_members(true);
$updated = ($_GET['updated'] ?? '') === '1';
$error = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipo | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css?v=20260708-team">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('team'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Equipo'); ?>

            <main class="admin-content">
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">El estudio</span>
                            <h2>Miembros del equipo</h2>
                        </div>
                        <button type="button" class="admin-primary-link" data-team-add>Agregar miembro</button>
                    </div>

                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Equipo actualizado.</p>
                    <?php endif; ?>
                    <?php if ($error !== ''): ?>
                        <p class="admin-alert admin-alert-error"><?php echo commar_admin_h($error); ?></p>
                    <?php endif; ?>

                    <form action="save-team.php" method="post" enctype="multipart/form-data" class="admin-form admin-team-form" data-team-form>
                        <div class="admin-team-list" data-team-list>
                            <?php foreach ($members as $index => $member): ?>
                                <article class="admin-team-card <?php echo !empty($member['hidden']) ? 'is-hidden-member' : ''; ?>" data-team-card>
                                    <div class="admin-team-photo">
                                        <?php if (($member['image'] ?? '') !== ''): ?>
                                            <img src="../<?php echo commar_admin_h((string) $member['image']); ?>" alt="">
                                        <?php else: ?>
                                            <span>Sin foto</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="admin-team-fields">
                                        <div class="admin-team-card-head">
                                            <span>Posición <?php echo (int) $index + 1; ?></span>
                                            <div class="admin-team-order-actions">
                                                <button type="button" class="admin-button-icon" title="Subir miembro" aria-label="Subir miembro" data-team-move="up">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                                                </button>
                                                <button type="button" class="admin-button-icon" title="Bajar miembro" aria-label="Bajar miembro" data-team-move="down">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="members[<?php echo (int) $index; ?>][image]" value="<?php echo commar_admin_h((string) ($member['image'] ?? '')); ?>">
                                        <input type="hidden" name="members[<?php echo (int) $index; ?>][width]" value="<?php echo (int) ($member['width'] ?? 0); ?>">
                                        <input type="hidden" name="members[<?php echo (int) $index; ?>][height]" value="<?php echo (int) ($member['height'] ?? 0); ?>">

                                        <div class="admin-form-grid">
                                            <label>
                                                Nombre
                                                <input type="text" name="members[<?php echo (int) $index; ?>][name]" value="<?php echo commar_admin_h((string) ($member['name'] ?? '')); ?>" required>
                                            </label>
                                            <label>
                                                Rol
                                                <input type="text" name="members[<?php echo (int) $index; ?>][role]" value="<?php echo commar_admin_h((string) ($member['role'] ?? '')); ?>" required>
                                            </label>
                                        </div>

                                        <div class="admin-form-grid">
                                            <label>
                                                LinkedIn
                                                <?php $linkedinValue = (string) ($member['linkedin'] ?? ''); ?>
                                                <input type="url" name="members[<?php echo (int) $index; ?>][linkedin]" value="<?php echo commar_admin_h($linkedinValue === '#' ? '' : $linkedinValue); ?>" placeholder="https://www.linkedin.com/in/...">
                                            </label>
                                            <label class="admin-file-control">
                                                Foto
                                                <span class="admin-file-input-wrap">
                                                    <span class="admin-file-button">Cambiar foto</span>
                                                    <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                                                    <input type="file" name="member_images[<?php echo (int) $index; ?>]" accept="image/jpeg,image/png,image/webp" data-file-input>
                                                </span>
                                            </label>
                                        </div>

                                        <div class="admin-team-options">
                                            <label class="admin-checkbox-row admin-team-hidden">
                                                <input type="checkbox" name="members[<?php echo (int) $index; ?>][hidden]" value="1" <?php echo !empty($member['hidden']) ? 'checked' : ''; ?>>
                                                Ocultar en El estudio
                                            </label>
                                            <label class="admin-checkbox-row admin-team-remove">
                                                <input type="checkbox" name="members[<?php echo (int) $index; ?>][delete]" value="1">
                                                Eliminar definitivamente
                                            </label>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>

                        <div class="admin-settings-savebar admin-team-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>Los cambios se aplican al bloque Equipo de la página El estudio.</span>
                                <button type="submit" class="admin-button-primary">Guardar equipo</button>
                            </div>
                        </div>
                    </form>
                </section>
            </main>

            <?php commar_admin_footer(); ?>
        </div>
    </div>

    <template id="team-card-template">
        <article class="admin-team-card" data-team-card>
            <div class="admin-team-photo"><span>Sin foto</span></div>
            <div class="admin-team-fields">
                <div class="admin-team-card-head">
                    <span>Nuevo miembro</span>
                    <div class="admin-team-order-actions">
                        <button type="button" class="admin-button-icon" title="Subir miembro" aria-label="Subir miembro" data-team-move="up">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m18 15-6-6-6 6"/></svg>
                        </button>
                        <button type="button" class="admin-button-icon" title="Bajar miembro" aria-label="Bajar miembro" data-team-move="down">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    </div>
                </div>
                <input type="hidden" data-name-template="members[__INDEX__][image]" value="">
                <input type="hidden" data-name-template="members[__INDEX__][width]" value="0">
                <input type="hidden" data-name-template="members[__INDEX__][height]" value="0">

                <div class="admin-form-grid">
                    <label>
                        Nombre
                        <input type="text" data-name-template="members[__INDEX__][name]" value="" required>
                    </label>
                    <label>
                        Rol
                        <input type="text" data-name-template="members[__INDEX__][role]" value="" required>
                    </label>
                </div>

                <div class="admin-form-grid">
                    <label>
                        LinkedIn
                        <input type="url" data-name-template="members[__INDEX__][linkedin]" value="" placeholder="https://www.linkedin.com/in/...">
                    </label>
                    <label class="admin-file-control">
                        Foto
                        <span class="admin-file-input-wrap">
                            <span class="admin-file-button">Subir foto</span>
                            <span class="admin-file-name" data-file-name>Sin archivo seleccionado</span>
                            <input type="file" data-name-template="member_images[__INDEX__]" accept="image/jpeg,image/png,image/webp" data-file-input>
                        </span>
                    </label>
                </div>

                <div class="admin-team-options">
                    <label class="admin-checkbox-row admin-team-hidden">
                        <input type="checkbox" data-name-template="members[__INDEX__][hidden]" value="1">
                        Ocultar en El estudio
                    </label>
                    <label class="admin-checkbox-row admin-team-remove">
                        <input type="checkbox" data-name-template="members[__INDEX__][delete]" value="1">
                        Eliminar definitivamente
                    </label>
                </div>
            </div>
        </article>
    </template>

    <script src="admin.js?v=20260701-media-picker" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var list = document.querySelector('[data-team-list]');
            var addButton = document.querySelector('[data-team-add]');
            var template = document.getElementById('team-card-template');

            var updateTeamOrderControls = function () {
                if (!list) {
                    return;
                }

                var cards = Array.prototype.slice.call(list.querySelectorAll('[data-team-card]'));
                cards.forEach(function (card, index) {
                    var label = card.querySelector('.admin-team-card-head > span');
                    var upButton = card.querySelector('[data-team-move="up"]');
                    var downButton = card.querySelector('[data-team-move="down"]');

                    if (label) {
                        label.textContent = 'Posición ' + (index + 1);
                    }
                    if (upButton) {
                        upButton.disabled = index === 0;
                    }
                    if (downButton) {
                        downButton.disabled = index === cards.length - 1;
                    }
                });
            };

            var bindOrderControls = function (scope) {
                scope.querySelectorAll('[data-team-move]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var card = button.closest('[data-team-card]');
                        if (!card || !list) {
                            return;
                        }

                        if (button.getAttribute('data-team-move') === 'up' && card.previousElementSibling) {
                            list.insertBefore(card, card.previousElementSibling);
                        } else if (button.getAttribute('data-team-move') === 'down' && card.nextElementSibling) {
                            list.insertBefore(card.nextElementSibling, card);
                        }

                        updateTeamOrderControls();
                        card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    });
                });
            };

            var bindFileInput = function (scope) {
                scope.querySelectorAll('[data-file-input]').forEach(function (input) {
                    input.addEventListener('change', function () {
                        var label = input.closest('.admin-file-input-wrap').querySelector('[data-file-name]');
                        var file = input.files && input.files[0] ? input.files[0].name : 'Sin archivo seleccionado';
                        if (label) {
                            label.textContent = file;
                        }
                    });
                });
            };

            var bindDeleteToggle = function (scope) {
                scope.querySelectorAll('.admin-team-remove input[type="checkbox"]').forEach(function (checkbox) {
                    var updateCard = function () {
                        var card = checkbox.closest('[data-team-card]');
                        if (!card) {
                            return;
                        }

                        card.classList.toggle('is-marked-delete', checkbox.checked);
                        card.querySelectorAll('input[required]').forEach(function (input) {
                            input.required = !checkbox.checked;
                        });
                    };

                    checkbox.addEventListener('change', updateCard);
                    updateCard();
                });
            };

            var bindHiddenToggle = function (scope) {
                scope.querySelectorAll('.admin-team-hidden input[type="checkbox"]').forEach(function (checkbox) {
                    var updateCard = function () {
                        var card = checkbox.closest('[data-team-card]');
                        if (card) {
                            card.classList.toggle('is-hidden-member', checkbox.checked);
                        }
                    };

                    checkbox.addEventListener('change', updateCard);
                    updateCard();
                });
            };

            bindFileInput(document);
            bindDeleteToggle(document);
            bindHiddenToggle(document);
            bindOrderControls(document);
            updateTeamOrderControls();

            if (addButton && list && template) {
                addButton.addEventListener('click', function () {
                    var index = 'new_' + Date.now();
                    var fragment = template.content.cloneNode(true);

                    fragment.querySelectorAll('[data-name-template]').forEach(function (field) {
                        field.name = field.getAttribute('data-name-template').replace('__INDEX__', index);
                        field.removeAttribute('data-name-template');
                    });

                    list.appendChild(fragment);
                    bindFileInput(list.lastElementChild);
                    bindDeleteToggle(list.lastElementChild);
                    bindHiddenToggle(list.lastElementChild);
                    bindOrderControls(list.lastElementChild);
                    updateTeamOrderControls();
                    list.lastElementChild.scrollIntoView({ block: 'center', behavior: 'smooth' });
                    var firstInput = list.lastElementChild.querySelector('input[type="text"]');
                    if (firstInput) {
                        firstInput.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>
