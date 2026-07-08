<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/team.php';

commar_admin_require_login();

$members = commar_team_members();
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
                                <article class="admin-team-card" data-team-card>
                                    <div class="admin-team-photo">
                                        <?php if (($member['image'] ?? '') !== ''): ?>
                                            <img src="../<?php echo commar_admin_h((string) $member['image']); ?>" alt="">
                                        <?php else: ?>
                                            <span>Sin foto</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="admin-team-fields">
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
                                                <input type="url" name="members[<?php echo (int) $index; ?>][linkedin]" value="<?php echo commar_admin_h((string) ($member['linkedin'] ?? '#')); ?>" placeholder="https://www.linkedin.com/in/...">
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

                                        <label class="admin-checkbox-row admin-team-remove">
                                            <input type="checkbox" name="members[<?php echo (int) $index; ?>][delete]" value="1">
                                            Eliminar este miembro
                                        </label>
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
                        <input type="url" data-name-template="members[__INDEX__][linkedin]" value="#" placeholder="https://www.linkedin.com/in/...">
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

                <label class="admin-checkbox-row admin-team-remove">
                    <input type="checkbox" data-name-template="members[__INDEX__][delete]" value="1">
                    Eliminar este miembro
                </label>
            </div>
        </article>
    </template>

    <script src="admin.js?v=20260701-media-picker" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var list = document.querySelector('[data-team-list]');
            var addButton = document.querySelector('[data-team-add]');
            var template = document.getElementById('team-card-template');

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

            bindFileInput(document);
            bindDeleteToggle(document);

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
