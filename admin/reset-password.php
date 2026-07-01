<?php
require_once __DIR__ . '/auth.php';

$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$reset = commar_admin_get_password_reset($token);
$message = '';
$messageType = '';
$passwordChanged = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reset !== null) {
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if (!commar_admin_verify_csrf_token()) {
        $message = 'La sesión expiró. Volvé a intentar.';
        $messageType = 'error';
    } elseif (strlen($password) < 8) {
        $message = 'La clave debe tener al menos 8 caracteres.';
        $messageType = 'error';
    } elseif ($password !== $passwordConfirm) {
        $message = 'Las claves no coinciden.';
        $messageType = 'error';
    } elseif (commar_admin_reset_password($token, $password)) {
        $message = 'Clave actualizada. Ya podés ingresar con tu nueva contraseña.';
        $messageType = 'success';
        $passwordChanged = true;
        $reset = null;
    } else {
        $message = 'No pudimos actualizar la clave. Solicitá un nuevo enlace.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva clave | COMMAR GROUP</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page admin-auth-page">
    <div class="admin-auth-wrap">
        <main class="admin-auth">
            <section class="admin-auth-stage" aria-label="Crear nueva clave">
                <form method="post" class="admin-login-card">
                    <div class="admin-login-head">
                        <img src="../img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578">
                        <div>
                            <span>Seguridad</span>
                            <h2>Nueva contraseña</h2>
                        </div>
                    </div>
                    <?php if ($message): ?>
                        <p class="admin-alert admin-alert-<?php echo $messageType; ?>"><?php echo commar_admin_h($message); ?></p>
                    <?php endif; ?>
                    <?php if ($reset !== null): ?>
                        <input type="hidden" name="token" value="<?php echo commar_admin_h($token); ?>">
                        <label>
                            <span>Nueva clave</span>
                            <input type="password" name="password" autocomplete="new-password" minlength="8" required>
                        </label>
                        <label>
                            <span>Repetir clave</span>
                            <input type="password" name="password_confirm" autocomplete="new-password" minlength="8" required>
                        </label>
                        <button type="submit" class="admin-login-submit">Guardar clave</button>
                    <?php elseif (!$passwordChanged): ?>
                        <p class="admin-alert admin-alert-error">El enlace no es válido o ya venció.</p>
                        <a href="forgot-password.php" class="admin-login-submit admin-login-button-link">Solicitar nuevo enlace</a>
                    <?php endif; ?>
                    <a href="login.php" class="admin-login-link">Volver al login</a>
                </form>
            </section>
        </main>
        <footer class="admin-footer">
            <p><?php echo date('Y'); ?> &copy; MOnkey CMS v1.1 - Diseñado y Desarrollado por <a href="https://monkey-art.net" target="_blank" rel="noopener noreferrer">MOnkey ARt</a>.</p>
        </footer>
    </div>
</body>
</html>
