<?php
require_once __DIR__ . '/auth.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim((string) ($_POST['identifier'] ?? ''));

    if (!commar_admin_verify_csrf_token()) {
        $message = 'La sesión expiró. Volvé a intentar.';
        $messageType = 'error';
    } elseif ($identifier === '') {
        $message = 'Ingresá tu usuario o email.';
        $messageType = 'error';
    } else {
        commar_admin_send_password_reset($identifier);
        $message = 'Si el usuario existe y tiene un email asociado, vas a recibir un enlace para restablecer la clave.';
        $messageType = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar clave | COMMAR GROUP</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page admin-auth-page">
    <div class="admin-auth-wrap">
        <main class="admin-auth">
            <section class="admin-auth-stage" aria-label="Recuperar acceso al panel">
                <form method="post" class="admin-login-card">
                    <div class="admin-login-head">
                        <img src="../img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578">
                        <div>
                            <span>Recuperación</span>
                            <h2>Olvidé contraseña</h2>
                        </div>
                    </div>
                    <?php if ($message): ?>
                        <p class="admin-alert admin-alert-<?php echo $messageType; ?>"><?php echo commar_admin_h($message); ?></p>
                    <?php endif; ?>
                    <label>
                        <span>Usuario o email</span>
                        <input type="text" name="identifier" autocomplete="username" required>
                    </label>
                    <button type="submit" class="admin-login-submit">Enviar enlace</button>
                    <a href="login.php" class="admin-login-link">Volver al login</a>
                </form>
            </section>
        </main>
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> MOnkey CMS, diseñado y creado por <a href="https://monkey-art.net" target="_blank" rel="noopener noreferrer">MOnkey ARt</a>.</p>
        </footer>
    </div>
</body>
</html>
