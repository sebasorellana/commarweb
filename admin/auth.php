<?php
require_once __DIR__ . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

if (!headers_sent()) {
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function commar_admin_is_logged_in(): bool
{
    return ($_SESSION['commar_admin'] ?? false) === true;
}

function commar_admin_require_login(): void
{
    if (!commar_admin_is_logged_in()) {
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/');
        $adminDir = str_ends_with($scriptDir, '/admin') ? $scriptDir : '/admin';

        header('Location: ' . $adminDir . '/login.php');
        exit;
    }
}

function commar_admin_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function commar_admin_get_user(string $username): ?array
{
    $statement = commar_db()->prepare('SELECT * FROM commar_users WHERE username = :username LIMIT 1');
    $statement->execute(['username' => $username]);
    $user = $statement->fetch();

    return is_array($user) ? $user : null;
}

function commar_admin_get_user_by_login_identifier(string $identifier): ?array
{
    $statement = commar_db()->prepare(
        'SELECT * FROM commar_users WHERE username = :username_identifier OR email = :email_identifier LIMIT 1'
    );
    $statement->execute([
        'username_identifier' => $identifier,
        'email_identifier' => $identifier,
    ]);
    $user = $statement->fetch();

    return is_array($user) ? $user : null;
}

function commar_admin_verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function commar_admin_login(string $username, string $password): bool
{
    $user = commar_admin_get_user($username);
    if ($user === null) {
        return false;
    }

    if (!commar_admin_verify_password($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['commar_admin'] = true;
    $_SESSION['commar_user_id'] = $user['id'];
    $_SESSION['commar_username'] = $user['username'];

    return true;
}

function commar_admin_create_user(string $username, string $password, string $email = ''): bool
{
    if (commar_admin_get_user($username) !== null) {
        return false; // User already exists
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $statement = commar_db()->prepare(
        'INSERT INTO commar_users (username, password_hash, email) VALUES (:username, :password_hash, :email)'
    );

    return $statement->execute([
        'username' => $username,
        'password_hash' => $passwordHash,
        'email' => $email,
    ]);
}

function commar_admin_get_all_users(): array
{
    $statement = commar_db()->query('SELECT id, username, email, created_at, updated_at FROM commar_users ORDER BY created_at DESC');
    return $statement->fetchAll();
}

function commar_admin_delete_user(int $userId): bool
{
    // Don't allow deleting the current user
    if (($userId ?? 0) === ($_SESSION['commar_user_id'] ?? 0)) {
        return false;
    }

    $statement = commar_db()->prepare('DELETE FROM commar_users WHERE id = :id');
    return $statement->execute(['id' => $userId]);
}

function commar_admin_base_url(): string
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/login.php')), '/');

    return $scheme . '://' . $host . ($scriptDir === '' ? '' : $scriptDir);
}

function commar_admin_create_password_reset(int $userId): string
{
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);

    $cleanup = commar_db()->prepare('UPDATE commar_password_resets SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL');
    $cleanup->execute(['user_id' => $userId]);

    $statement = commar_db()->prepare(
        'INSERT INTO commar_password_resets (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)'
    );
    $statement->execute([
        'user_id' => $userId,
        'token_hash' => $tokenHash,
        'expires_at' => $expiresAt,
    ]);

    return commar_admin_base_url() . '/reset-password.php?token=' . urlencode($token);
}

function commar_admin_send_password_reset(string $identifier): bool
{
    $user = commar_admin_get_user_by_login_identifier(trim($identifier));
    if ($user === null || trim((string) $user['email']) === '') {
        return false;
    }

    $resetUrl = commar_admin_create_password_reset((int) $user['id']);
    $body = implode("\n", [
        'Recibimos una solicitud para restablecer la clave del backend de COMMAR GROUP.',
        '',
        'Abrí este enlace para crear una nueva clave:',
        $resetUrl,
        '',
        'El enlace vence en 1 hora. Si no solicitaste este cambio, podés ignorar este email.',
    ]);

    $headers = [
        'From: COMMAR GROUP <no-reply@commar.group>',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return @mail((string) $user['email'], 'Restablecer clave | COMMAR GROUP', $body, implode("\r\n", $headers));
}

function commar_admin_get_password_reset(string $token): ?array
{
    if ($token === '') {
        return null;
    }

    $statement = commar_db()->prepare(
        'SELECT reset.*, users.username, users.email
         FROM commar_password_resets reset
         INNER JOIN commar_users users ON users.id = reset.user_id
         WHERE reset.token_hash = :token_hash
           AND reset.used_at IS NULL
           AND reset.expires_at > NOW()
         LIMIT 1'
    );
    $statement->execute(['token_hash' => hash('sha256', $token)]);
    $reset = $statement->fetch();

    return is_array($reset) ? $reset : null;
}

function commar_admin_reset_password(string $token, string $password): bool
{
    $reset = commar_admin_get_password_reset($token);
    if ($reset === null) {
        return false;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $pdo = commar_db();
    $pdo->beginTransaction();

    try {
        $updateUser = $pdo->prepare('UPDATE commar_users SET password_hash = :password_hash WHERE id = :id');
        $updateUser->execute([
            'password_hash' => $passwordHash,
            'id' => (int) $reset['user_id'],
        ]);

        $updateReset = $pdo->prepare('UPDATE commar_password_resets SET used_at = NOW() WHERE id = :id');
        $updateReset->execute(['id' => (int) $reset['id']]);

        $pdo->commit();
        return true;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        return false;
    }
}

function commar_admin_get_current_user(): ?array
{
    if (!commar_admin_is_logged_in()) {
        return null;
    }

    $userId = $_SESSION['commar_user_id'] ?? null;
    if ($userId === null) {
        return null;
    }

    $statement = commar_db()->prepare('SELECT id, username, email, created_at, updated_at FROM commar_users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $userId]);
    $user = $statement->fetch();

    return is_array($user) ? $user : null;
}
