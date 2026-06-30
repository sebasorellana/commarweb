<?php
require_once __DIR__ . '/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string) ($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (commar_admin_login($username, $password)) {
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    }

    $error = 'Usuario o clave incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | COMMAR GROUP</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-page admin-auth-page">
    <div class="admin-auth-wrap">
        <main class="admin-auth">
            <section class="admin-auth-stage" aria-label="Acceso al panel de administracion">
                <form method="post" class="admin-login-card">
                    <div class="admin-login-head">
                        <img src="../img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578">
                        <div>
                            <span>Backend</span>
                            <h2>Ingresar al panel</h2>
                        </div>
                    </div>
                    <?php if ($error): ?>
                        <p class="admin-alert admin-alert-error"><?php echo commar_admin_h($error); ?></p>
                    <?php endif; ?>
                    <label>
                        <span>Usuario</span>
                        <input type="text" name="username" autocomplete="username" required>
                    </label>
                    <label>
                        <span>Clave</span>
                        <input type="password" name="password" autocomplete="current-password" required>
                    </label>
                    <button type="submit" class="admin-login-submit">Ingresar</button>
                    <a href="forgot-password.php" class="admin-login-link">Olvidé contraseña</a>
                </form>
            </section>
        </main>
        <footer class="admin-footer">
            <p>&copy; <?php echo date('Y'); ?> MOnkey CMS, diseñado y creado por <a href="https://monkey-art.net" target="_blank" rel="noopener noreferrer">MOnkey ARt</a>.</p>
        </footer>
    </div>
</body>
</html>
