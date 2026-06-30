<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/auth.php';

commar_admin_require_administrator();

$message = '';
$messageType = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!commar_admin_verify_csrf_token()) {
        $message = 'Error de seguridad. Por favor, intentalo de nuevo.';
        $messageType = 'error';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($action === 'create') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $email = trim((string) ($_POST['email'] ?? ''));
            $role = (string) ($_POST['role'] ?? 'editor');

            if ($username === '' || $password === '') {
                $message = 'El nombre de usuario y la contraseña son obligatorios.';
                $messageType = 'error';
            } elseif (strlen($password) < 8) {
                $message = 'La contraseña debe tener al menos 8 caracteres.';
                $messageType = 'error';
            } elseif (commar_admin_create_user($username, $password, $email, $role)) {
                $message = 'Usuario creado exitosamente.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo crear el usuario. Revisá que el nombre no exista.';
                $messageType = 'error';
            }
        } elseif ($action === 'update') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $role = (string) ($_POST['role'] ?? 'editor');

            if ($userId <= 0 || $username === '') {
                $message = 'El usuario es obligatorio.';
                $messageType = 'error';
            } elseif (commar_admin_update_user($userId, $username, $email, $role)) {
                $message = 'Usuario actualizado.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo actualizar. No podés repetir usuarios ni quitar el último administrador.';
                $messageType = 'error';
            }
        } elseif ($action === 'password') {
            $password = (string) ($_POST['password'] ?? '');

            if ($userId <= 0 || strlen($password) < 8) {
                $message = 'La nueva contraseña debe tener al menos 8 caracteres.';
                $messageType = 'error';
            } elseif (commar_admin_update_user_password($userId, $password)) {
                $message = 'Contraseña actualizada.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo actualizar la contraseña.';
                $messageType = 'error';
            }
        } elseif ($action === 'delete') {
            if (commar_admin_delete_user($userId)) {
                $message = 'Usuario eliminado exitosamente.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo eliminar. No podés eliminar tu usuario ni el último administrador.';
                $messageType = 'error';
            }
        }
    }
}

$users = commar_admin_get_all_users();
$currentUser = commar_admin_get_current_user();
$totalUsers = count($users);
$totalAdmins = count(array_filter($users, static fn(array $user): bool => ($user['role'] ?? 'admin') === 'admin'));

function commar_admin_user_role_label(string $role): string
{
    return $role === 'editor' ? 'Editor' : 'Administrador';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de usuarios | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('settings'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Configuraciones'); ?>
            <main class="admin-content">
                <?php commar_admin_settings_nav('users'); ?>

                <?php if ($message): ?>
                    <div class="admin-alert admin-alert-<?php echo $messageType; ?>">
                        <?php echo commar_admin_h($message); ?>
                    </div>
                <?php endif; ?>

                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <span class="admin-kicker">Usuarios</span>
                            <h2>Gestión de usuarios</h2>
                        </div>
                        <a href="#add-user-modal" class="admin-primary-link">Agregar usuario</a>
                    </div>

                    <div class="admin-users-summary">
                        <article>
                            <span>Usuarios activos</span>
                            <strong><?php echo $totalUsers; ?></strong>
                        </article>
                        <article>
                            <span>Administradores</span>
                            <strong><?php echo $totalAdmins; ?></strong>
                        </article>
                        <article>
                            <span>Sesión actual</span>
                            <strong><?php echo commar_admin_h((string) ($currentUser['username'] ?? 'Admin')); ?></strong>
                        </article>
                    </div>

                    <?php if (empty($users)): ?>
                        <p class="admin-empty">No hay usuarios registrados.</p>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-users-table">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Fecha de creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <?php
                                        $role = commar_admin_normalize_role((string) ($user['role'] ?? 'admin'));
                                        $isCurrentUser = $currentUser && (int) $user['id'] === (int) $currentUser['id'];
                                        $initial = mb_strtoupper(mb_substr((string) $user['username'], 0, 1, 'UTF-8'), 'UTF-8');
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="admin-user-identity">
                                                    <span class="admin-user-avatar"><?php echo commar_admin_h($initial); ?></span>
                                                    <div>
                                                        <strong><?php echo commar_admin_h((string) $user['username']); ?></strong>
                                                        <?php if ($isCurrentUser): ?>
                                                            <span>Sesión actual</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo commar_admin_h((string) ($user['email'] ?: '-')); ?></td>
                                            <td><span class="admin-status-pill <?php echo $role === 'admin' ? 'is-admin-role' : 'is-editor-role'; ?>"><?php echo commar_admin_h(commar_admin_user_role_label($role)); ?></span></td>
                                            <td><?php echo commar_admin_h(date('d/m/Y', strtotime((string) $user['created_at']))); ?></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <a href="#edit-user-<?php echo (int) $user['id']; ?>" class="admin-button-icon" title="Editar usuario">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                                    </a>
                                                    <a href="#password-user-<?php echo (int) $user['id']; ?>" class="admin-button-icon" title="Cambiar contraseña">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                    </a>
                                                    <?php if (!$isCurrentUser): ?>
                                                        <form method="post" class="admin-user-row-action" onsubmit="return confirm('¿Estás seguro de que querés eliminar este usuario?');">
                                                            <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                                            <button type="submit" class="admin-button-icon admin-button-danger" title="Eliminar usuario">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>

    <div id="add-user-modal" class="admin-modal-target">
        <a href="#" class="admin-modal-backdrop" aria-label="Cerrar"></a>
        <section class="admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="add-user-title">
            <div class="admin-modal-head">
                <div>
                    <span class="admin-kicker">Nuevo acceso</span>
                    <h2 id="add-user-title">Agregar usuario</h2>
                </div>
                <a href="#" class="admin-modal-close" aria-label="Cerrar">&times;</a>
            </div>
            <form method="post" class="admin-user-form">
                <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                <input type="hidden" name="action" value="create">
                <label>
                    Nombre de usuario
                    <input type="text" name="username" required maxlength="50" autocomplete="username">
                </label>
                <label>
                    Email
                    <input type="email" name="email" maxlength="255" autocomplete="email">
                </label>
                <label>
                    Tipo de usuario
                    <select name="role" required>
                        <option value="admin">Administrador</option>
                        <option value="editor" selected>Editor</option>
                    </select>
                </label>
                <label>
                    Contraseña
                    <input type="password" name="password" required minlength="8" autocomplete="new-password">
                    <span class="admin-help">Mínimo 8 caracteres.</span>
                </label>
                <button type="submit" class="admin-button-primary">Crear usuario</button>
            </form>
        </section>
    </div>

    <?php foreach ($users as $user): ?>
        <?php $role = commar_admin_normalize_role((string) ($user['role'] ?? 'admin')); ?>
        <div id="edit-user-<?php echo (int) $user['id']; ?>" class="admin-modal-target">
            <a href="#" class="admin-modal-backdrop" aria-label="Cerrar"></a>
            <section class="admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="edit-user-title-<?php echo (int) $user['id']; ?>">
                <div class="admin-modal-head">
                    <div>
                        <span class="admin-kicker">Editar acceso</span>
                        <h2 id="edit-user-title-<?php echo (int) $user['id']; ?>"><?php echo commar_admin_h((string) $user['username']); ?></h2>
                    </div>
                    <a href="#" class="admin-modal-close" aria-label="Cerrar">&times;</a>
                </div>
                <form method="post" class="admin-user-form">
                    <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                    <label>
                        Nombre de usuario
                        <input type="text" name="username" value="<?php echo commar_admin_h((string) $user['username']); ?>" required maxlength="50" autocomplete="username">
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" value="<?php echo commar_admin_h((string) $user['email']); ?>" maxlength="255" autocomplete="email">
                    </label>
                    <label>
                        Tipo de usuario
                        <select name="role" required>
                            <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                            <option value="editor" <?php echo $role === 'editor' ? 'selected' : ''; ?>>Editor</option>
                        </select>
                    </label>
                    <button type="submit" class="admin-button-primary">Guardar cambios</button>
                </form>
            </section>
        </div>

        <div id="password-user-<?php echo (int) $user['id']; ?>" class="admin-modal-target">
            <a href="#" class="admin-modal-backdrop" aria-label="Cerrar"></a>
            <section class="admin-modal-card" role="dialog" aria-modal="true" aria-labelledby="password-user-title-<?php echo (int) $user['id']; ?>">
                <div class="admin-modal-head">
                    <div>
                        <span class="admin-kicker">Seguridad</span>
                        <h2 id="password-user-title-<?php echo (int) $user['id']; ?>">Cambiar contraseña</h2>
                    </div>
                    <a href="#" class="admin-modal-close" aria-label="Cerrar">&times;</a>
                </div>
                <form method="post" class="admin-user-form">
                    <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                    <input type="hidden" name="action" value="password">
                    <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                    <p class="admin-help">Usuario: <strong><?php echo commar_admin_h((string) $user['username']); ?></strong></p>
                    <label>
                        Nueva contraseña
                        <input type="password" name="password" required minlength="8" autocomplete="new-password">
                        <span class="admin-help">Mínimo 8 caracteres.</span>
                    </label>
                    <button type="submit" class="admin-button-primary">Actualizar contraseña</button>
                </form>
            </section>
        </div>
    <?php endforeach; ?>
</body>
</html>
