<?php
$commarAdminConfigPath = __DIR__ . '/config.php';
if (is_file($commarAdminConfigPath)) {
    require_once $commarAdminConfigPath;
}
require_once dirname(__DIR__) . '/includes/db.php';

if (!headers_sent()) {
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
    header('X-Frame-Options: DENY', true);
    header('X-Content-Type-Options: nosniff', true);
    header('Referrer-Policy: same-origin', true);
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()', true);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $secureCookie ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

if (!defined('COMMAR_ADMIN_SESSION_TTL')) {
    define('COMMAR_ADMIN_SESSION_TTL', 1800);
}

if (!defined('COMMAR_ADMIN_LOGIN_MAX_ATTEMPTS')) {
    define('COMMAR_ADMIN_LOGIN_MAX_ATTEMPTS', 5);
}

if (!defined('COMMAR_ADMIN_LOGIN_LOCK_SECONDS')) {
    define('COMMAR_ADMIN_LOGIN_LOCK_SECONDS', 900);
}

if (($_SESSION['commar_admin'] ?? false) === true) {
    $lastActivity = (int) ($_SESSION['commar_last_activity'] ?? time());
    if (time() - $lastActivity > COMMAR_ADMIN_SESSION_TTL) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        session_start();
    } else {
        $_SESSION['commar_last_activity'] = time();
    }
}

function commar_admin_is_logged_in(): bool
{
    return ($_SESSION['commar_admin'] ?? false) === true;
}

function commar_admin_require_login(): void
{
    if (!commar_admin_is_logged_in()) {
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php')), '/');
        $adminDir = substr($scriptDir, -6) === '/admin' ? $scriptDir : '/admin';

        header('Location: ' . $adminDir . '/login.php');
        exit;
    }
}

function commar_admin_normalize_role(string $role): string
{
    return $role === 'editor' ? 'editor' : 'admin';
}

function commar_admin_current_role(): string
{
    static $role = null;

    if ($role !== null) {
        return $role;
    }

    $role = commar_admin_normalize_role((string) ($_SESSION['commar_role'] ?? 'admin'));
    if (commar_admin_is_logged_in() && !empty($_SESSION['commar_user_id'])) {
        $currentUser = commar_admin_get_current_user();
        if ($currentUser !== null) {
            $role = commar_admin_normalize_role((string) ($currentUser['role'] ?? 'admin'));
            $_SESSION['commar_username'] = $currentUser['username'];
            $_SESSION['commar_role'] = $role;
        }
    }

    return $role;
}

function commar_admin_is_administrator(): bool
{
    return commar_admin_is_logged_in() && commar_admin_current_role() === 'admin';
}

function commar_admin_require_administrator(): void
{
    commar_admin_require_login();

    if (!commar_admin_is_administrator()) {
        http_response_code(403);
        exit('No tenés permisos para acceder a esta sección.');
    }
}

function commar_admin_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function commar_admin_verify_csrf_token(?string $token = null): bool
{
    $token = $token ?? $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function commar_admin_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function commar_admin_client_ip(): string
{
    $candidates = [
        (string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? ''),
        (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''),
        (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
    ];

    foreach ($candidates as $candidate) {
        $candidate = trim(explode(',', $candidate)[0] ?? '');
        if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_IP)) {
            return $candidate;
        }
    }

    return 'unknown';
}

function commar_admin_rate_limit_path(string $identifier): string
{
    $dir = dirname(__DIR__) . '/data/admin-rate-limit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    return $dir . '/' . hash('sha256', $identifier) . '.json';
}

function commar_admin_login_rate_key(string $username): string
{
    return strtolower(trim($username)) . '|' . commar_admin_client_ip();
}

function commar_admin_login_is_limited(string $username): bool
{
    $path = commar_admin_rate_limit_path(commar_admin_login_rate_key($username));
    if (!is_file($path)) {
        return false;
    }

    $data = json_decode((string) file_get_contents($path), true);
    if (!is_array($data)) {
        return false;
    }

    $lockedUntil = (int) ($data['locked_until'] ?? 0);
    if ($lockedUntil <= time()) {
        @unlink($path);
        return false;
    }

    return true;
}

function commar_admin_record_login_failure(string $username): void
{
    $path = commar_admin_rate_limit_path(commar_admin_login_rate_key($username));
    $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : [];
    $data = is_array($data) ? $data : [];
    $attempts = (int) ($data['attempts'] ?? 0) + 1;

    $payload = [
        'attempts' => $attempts,
        'updated_at' => time(),
        'locked_until' => $attempts >= COMMAR_ADMIN_LOGIN_MAX_ATTEMPTS ? time() + COMMAR_ADMIN_LOGIN_LOCK_SECONDS : 0,
    ];

    @file_put_contents($path, json_encode($payload, JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function commar_admin_clear_login_failures(string $username): void
{
    $path = commar_admin_rate_limit_path(commar_admin_login_rate_key($username));
    if (is_file($path)) {
        @unlink($path);
    }
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
    if (commar_admin_login_is_limited($username)) {
        return false;
    }

    $user = commar_admin_get_user($username);
    if ($user === null) {
        commar_admin_record_login_failure($username);
        return false;
    }

    if (!commar_admin_verify_password($password, $user['password_hash'])) {
        commar_admin_record_login_failure($username);
        return false;
    }

    commar_admin_clear_login_failures($username);
    $_SESSION['commar_admin'] = true;
    $_SESSION['commar_user_id'] = $user['id'];
    $_SESSION['commar_username'] = $user['username'];
    $_SESSION['commar_role'] = commar_admin_normalize_role((string) ($user['role'] ?? 'admin'));
    $_SESSION['commar_last_activity'] = time();

    return true;
}

function commar_admin_create_user(string $username, string $password, string $email = '', string $role = 'editor', string $avatar = ''): bool
{
    if (commar_admin_get_user($username) !== null) {
        return false; // User already exists
    }

    $role = commar_admin_normalize_role($role);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $statement = commar_db()->prepare(
        'INSERT INTO commar_users (username, password_hash, email, avatar, role) VALUES (:username, :password_hash, :email, :avatar, :role)'
    );

    return $statement->execute([
        'username' => $username,
        'password_hash' => $passwordHash,
        'email' => $email,
        'avatar' => $avatar,
        'role' => $role,
    ]);
}

function commar_admin_get_all_users(): array
{
    $statement = commar_db()->query('SELECT id, username, email, avatar, role, created_at, updated_at FROM commar_users ORDER BY created_at DESC');
    return $statement->fetchAll();
}

function commar_admin_get_user_by_id(int $userId): ?array
{
    $statement = commar_db()->prepare('SELECT id, username, email, avatar, role, created_at, updated_at FROM commar_users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $userId]);
    $user = $statement->fetch();

    return is_array($user) ? $user : null;
}

function commar_admin_count_administrators(): int
{
    $statement = commar_db()->query("SELECT COUNT(*) FROM commar_users WHERE role = 'admin'");
    return (int) $statement->fetchColumn();
}

function commar_admin_update_user(int $userId, string $username, string $email, string $role, string $avatar = ''): bool
{
    $currentUser = commar_admin_get_user_by_id($userId);
    if ($currentUser === null) {
        return false;
    }

    $existingUser = commar_admin_get_user($username);
    if ($existingUser !== null && (int) $existingUser['id'] !== $userId) {
        return false;
    }

    $role = commar_admin_normalize_role($role);
    if ((string) ($currentUser['role'] ?? 'admin') === 'admin' && $role !== 'admin' && commar_admin_count_administrators() <= 1) {
        return false;
    }

    $statement = commar_db()->prepare(
        'UPDATE commar_users SET username = :username, email = :email, avatar = :avatar, role = :role WHERE id = :id'
    );

    $updated = $statement->execute([
        'username' => $username,
        'email' => $email,
        'avatar' => $avatar,
        'role' => $role,
        'id' => $userId,
    ]);

    if ($updated && (int) ($_SESSION['commar_user_id'] ?? 0) === $userId) {
        $_SESSION['commar_username'] = $username;
        $_SESSION['commar_role'] = $role;
    }

    return $updated;
}

function commar_admin_update_user_password(int $userId, string $password): bool
{
    if (commar_admin_get_user_by_id($userId) === null) {
        return false;
    }

    $statement = commar_db()->prepare('UPDATE commar_users SET password_hash = :password_hash WHERE id = :id');
    return $statement->execute([
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'id' => $userId,
    ]);
}

function commar_admin_delete_user(int $userId): bool
{
    // Don't allow deleting the current user
    if (($userId ?? 0) === ($_SESSION['commar_user_id'] ?? 0)) {
        return false;
    }

    $user = commar_admin_get_user_by_id($userId);
    if ($user === null) {
        return false;
    }

    if ((string) ($user['role'] ?? 'admin') === 'admin' && commar_admin_count_administrators() <= 1) {
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

    $statement = commar_db()->prepare('SELECT id, username, email, avatar, role, created_at, updated_at FROM commar_users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $userId]);
    $user = $statement->fetch();

    return is_array($user) ? $user : null;
}

function commar_admin_require_valid_csrf(): void
{
    if (!commar_admin_verify_csrf_token()) {
        http_response_code(403);
        exit('Token de seguridad inválido.');
    }
}

function commar_admin_inject_csrf_fields(string $html): string
{
    if (stripos($html, '<form') === false || stripos($html, 'method="post"') === false && stripos($html, "method='post'") === false) {
        return $html;
    }

    $tokenField = '<input type="hidden" name="csrf_token" value="' . commar_admin_h(commar_admin_csrf_token()) . '">';

    return preg_replace_callback(
        '#<form\b([^>]*)>#i',
        static function (array $matches) use ($tokenField): string {
            $form = $matches[0];
            $attributes = $matches[1] ?? '';

            if (!preg_match('/\bmethod\s*=\s*([\'"])post\1/i', $attributes)) {
                return $form;
            }

            return $form . "\n                    " . $tokenField;
        },
        $html
    ) ?? $html;
}

if (PHP_SAPI !== 'cli' && !defined('COMMAR_ADMIN_DISABLE_CSRF_INJECTION')) {
    ob_start('commar_admin_inject_csrf_fields');
}
