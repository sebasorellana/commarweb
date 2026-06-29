<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/auth.php';

commar_admin_require_login();

$users = commar_admin_get_all_users();
$currentUser = commar_admin_get_current_user();
$totalUsers = count($users);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!commar_admin_verify_csrf_token()) {
        $message = 'Error de seguridad. Por favor, intentalo de nuevo.';
        $messageType = 'error';
    } else {
        // Existing POST logic follows
        if (isset($_POST['create_user'])) {
            $username = trim((string) ($_POST['username'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $email = trim((string) ($_POST['email'] ?? ''));

            if ($username === '' || $password === '') {
                $message = 'El nombre de usuario y la contraseña son obligatorios.';
                $messageType = 'error';
            } elseif (commar_admin_create_user($username, $password, $email)) {
                $message = 'Usuario creado exitosamente.';
                $messageType = 'success';
                $users = commar_admin_get_all_users();
                $totalUsers = count($users);
            } else {
                $message = 'Error al crear el usuario. Es posible que el nombre de usuario ya exista.';
                $messageType = 'error';
            }
        } elseif (isset($_POST['delete_user'])) {
            $userId = (int) ($_POST['user_id'] ?? 0);
            if (commar_admin_delete_user($userId)) {
                $message = 'Usuario eliminado exitosamente.';
                $messageType = 'success';
                $users = commar_admin_get_all_users();
                $totalUsers = count($users);
            } else {
                $message = 'Error al eliminar el usuario.';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de usuarios | MOnkey CMS</title>
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
                    <div class="admin-section-head">
                        <span class="admin-kicker">Usuarios</span>
                        <h2>Gestión de usuarios</h2>
                    </div>

                    <div class="admin-users-summary">
                        <article>
                            <span>Usuarios activos</span>
                            <strong><?php echo $totalUsers; ?></strong>
                        </article>
                        <article>
                            <span>Sesión actual</span>
                            <strong><?php echo commar_admin_h((string) ($currentUser['username'] ?? 'Admin')); ?></strong>
                        </article>
                    </div>

                    <div class="admin-users-grid">
                        <div class="admin-users-list admin-panel">
                            <div class="admin-users-card-head">
                                <div>
                                    <span class="admin-kicker">Usuarios (<?php echo $totalUsers; ?>)</span>
                                    <h3>Usuarios actuales</h3>
                                </div>
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
                                                <th>Fecha de creación</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <?php
                                                $isCurrentUser = $currentUser && (int) $user['id'] === (int) $currentUser['id'];
                                                $initial = mb_strtoupper(mb_substr((string) $user['username'], 0, 1, 'UTF-8'), 'UTF-8');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="admin-user-identity">
                                                            <span class="admin-user-avatar"><?php echo commar_admin_h($initial); ?></span>
                                                            <div>
                                                                <strong><?php echo commar_admin_h($user['username']); ?></strong>
                                                                <?php if ($isCurrentUser): ?>
                                                                    <span>Sesión actual</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo commar_admin_h($user['email'] ?: '-'); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                                    <td>
                                                        <?php if (!$isCurrentUser): ?>
                                                            <form method="post" class="admin-user-row-action" onsubmit="return confirm('¿Estás seguro de que querés eliminar este usuario?');">
                                                                <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                                                                <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                                                <button type="submit" name="delete_user" class="admin-button-icon admin-button-danger" title="Eliminar usuario">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="admin-status-pill">Actual</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="admin-users-create admin-panel">
                            <div class="admin-users-card-head">
                                <div>
                                    <span class="admin-kicker">Nuevo acceso</span>
                                    <h3>Crear usuario</h3>
                                </div>
                            </div>
                            <form method="post" class="admin-user-form">
                                <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                                <label>
                                    Nombre de usuario
                                    <input type="text" name="username" required maxlength="50">
                                </label>
                                <label>
                                    Email
                                    <input type="email" name="email" maxlength="255">
                                </label>
                                <label>
                                    Contraseña
                                    <input type="password" name="password" required minlength="6">
                                    <span class="admin-help">Mínimo 6 caracteres.</span>
                                </label>
                                <button type="submit" name="create_user" class="admin-button-primary">Crear usuario</button>
                            </form>
                        </div>
                    </div>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
