<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';
require_once dirname(__DIR__) . '/includes/cache.php';

commar_admin_require_administrator();

$settings = commar_settings();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    commar_admin_require_valid_csrf();
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'clear') {
        $result = commar_cache_clear();
        header('Location: settings-cache.php?cleared=' . (int) $result['files']);
        exit;
    }

    $enabled = isset($_POST['cache_enabled']) ? '1' : '0';
    $ttl = (string) max(60, min(86400, (int) ($_POST['cache_ttl'] ?? 300)));
    commar_save_settings([
        'cache_enabled' => $enabled,
        'cache_ttl' => $ttl,
    ]);

    header('Location: settings-cache.php?updated=1');
    exit;
}

$settings = commar_settings();
$stats = commar_cache_stats();
$updated = ($_GET['updated'] ?? '') === '1';
$cleared = isset($_GET['cleared']) ? max(0, (int) $_GET['cleared']) : null;
$formatBytes = static function (int $bytes): string {
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return number_format($bytes / 1024, 1, ',', '.') . ' KB';
    }
    return number_format($bytes / (1024 * 1024), 1, ',', '.') . ' MB';
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caché | MOnkey CMS</title>
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
                <?php commar_admin_settings_nav('cache'); ?>

                <div class="admin-settings-container">
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Configuración de caché actualizada.</p>
                    <?php endif; ?>
                    <?php if ($cleared !== null): ?>
                        <p class="admin-alert admin-alert-success">Caché vaciada. Se eliminaron <?php echo $cleared; ?> archivos.</p>
                    <?php endif; ?>
                    <?php if (!$stats['writable']): ?>
                        <p class="admin-alert admin-alert-error">El directorio de caché no tiene permisos de escritura.</p>
                    <?php endif; ?>

                    <section class="admin-stats" aria-label="Estado de la caché">
                        <article class="admin-stat-card is-primary">
                            <div class="admin-stat-card-head"><span>Estado</span></div>
                            <strong><?php echo ((string) ($settings['cache_enabled'] ?? '1')) === '1' ? 'Activa' : 'Inactiva'; ?></strong>
                            <small>Caché segura de datos públicos</small>
                        </article>
                        <article class="admin-stat-card">
                            <div class="admin-stat-card-head"><span>Archivos</span></div>
                            <strong><?php echo (int) $stats['files']; ?></strong>
                            <small>Entradas almacenadas</small>
                        </article>
                        <article class="admin-stat-card">
                            <div class="admin-stat-card-head"><span>Espacio</span></div>
                            <strong><?php echo commar_admin_h($formatBytes((int) $stats['bytes'])); ?></strong>
                            <small>Peso total en disco</small>
                        </article>
                        <article class="admin-stat-card">
                            <div class="admin-stat-card-head"><span>TTL</span></div>
                            <strong><?php echo (int) ($settings['cache_ttl'] ?? 300); ?> s</strong>
                            <small>Vigencia de cada entrada</small>
                        </article>
                    </section>

                    <form method="post" class="admin-form admin-settings-form">
                        <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                        <input type="hidden" name="action" value="save">

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Rendimiento</span>
                                <h3>Caché de datos del sitio</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="cache_enabled" value="1" <?php echo ((string) ($settings['cache_enabled'] ?? '1')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Activar caché
                                    <small>Reduce consultas repetidas de configuración, artículos y obras. No almacena formularios ni tokens de seguridad.</small>
                                </span>
                            </label>
                            <label>
                                Tiempo de vida (segundos)
                                <input type="number" name="cache_ttl" value="<?php echo (int) ($settings['cache_ttl'] ?? 300); ?>" min="60" max="86400" step="60" required>
                                <span class="admin-help">Valor recomendado: 300 segundos. Rango permitido: 60 a 86.400 segundos.</span>
                            </label>
                        </section>

                        <div class="admin-settings-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>Los cambios de contenido invalidan la caché automáticamente.</span>
                                <button type="submit" class="admin-button-primary">Guardar caché</button>
                            </div>
                        </div>
                    </form>

                    <form method="post" class="admin-settings-section">
                        <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                        <input type="hidden" name="action" value="clear">
                        <div class="admin-section-head">
                            <span class="admin-kicker">Mantenimiento</span>
                            <h3>Vaciar caché ahora</h3>
                        </div>
                        <p class="admin-help">El sitio regenerará las entradas necesarias en las próximas visitas. Esta acción no elimina contenido ni imágenes.</p>
                        <button type="submit" class="admin-button-primary">Vaciar caché</button>
                    </form>
                </div>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-mobile-drawer" defer></script>
</body>
</html>
