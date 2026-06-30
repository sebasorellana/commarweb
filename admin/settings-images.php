<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/settings.php';
require_once dirname(__DIR__) . '/includes/images.php';

commar_admin_require_administrator();

$settings = commar_settings();
$updated = ($_GET['updated'] ?? '') === '1';
$errors = [];
$webpSupported = function_exists('imagewebp');
$inventory = commar_admin_image_inventory();
$pendingItems = array_values(array_filter(
    $inventory['items'],
    static fn(array $item): bool => !empty($item['needs_optimization'])
));

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    commar_admin_require_valid_csrf();

    $settings['image_webp_enabled'] = isset($_POST['image_webp_enabled']) ? '1' : '0';
    $settings['image_lazyload_enabled'] = isset($_POST['image_lazyload_enabled']) ? '1' : '0';
    $settings['image_webp_quality'] = (string) max(40, min(95, (int) ($_POST['image_webp_quality'] ?? 82)));
    $settings['image_max_width'] = (string) max(0, min(5000, (int) ($_POST['image_max_width'] ?? 2000)));

    if (!$webpSupported && $settings['image_webp_enabled'] === '1') {
        $errors[] = 'El servidor no tiene habilitada la librería GD con soporte WebP. La conversión no podrá aplicarse hasta habilitarla.';
    }

    if ($errors === []) {
        commar_save_settings([
            'image_webp_enabled' => $settings['image_webp_enabled'],
            'image_webp_quality' => $settings['image_webp_quality'],
            'image_max_width' => $settings['image_max_width'],
            'image_lazyload_enabled' => $settings['image_lazyload_enabled'],
        ]);

        header('Location: settings-images.php?updated=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imágenes | MOnkey CMS</title>
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
                <?php commar_admin_settings_nav('images'); ?>

                <div class="admin-settings-container">
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Configuración de imágenes actualizada.</p>
                    <?php endif; ?>
                    <?php if (!$webpSupported): ?>
                        <p class="admin-alert admin-alert-error">Este PHP no tiene soporte WebP en GD. Las imágenes se guardarán en su formato original hasta habilitarlo en el hosting.</p>
                    <?php endif; ?>
                    <?php if ($errors): ?>
                        <div class="admin-alert admin-alert-error">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo commar_admin_h($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="admin-form admin-settings-form">
                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Formato</span>
                                <h3>Conversión WebP</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="image_webp_enabled" value="1" <?php echo ((string) ($settings['image_webp_enabled'] ?? '1')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Convertir nuevas imágenes a WebP
                                    <small>Aplica a imágenes cargadas desde el admin después de guardar esta configuración.</small>
                                </span>
                            </label>
                            <div class="admin-grid">
                                <label>
                                    Calidad WebP
                                    <input type="number" name="image_webp_quality" value="<?php echo commar_admin_h((string) ($settings['image_webp_quality'] ?? '82')); ?>" min="40" max="95" step="1">
                                    <span class="admin-help">Recomendado: 80 a 85. Más calidad aumenta el peso del archivo.</span>
                                </label>
                                <label>
                                    Ancho máximo
                                    <input type="number" name="image_max_width" value="<?php echo commar_admin_h((string) ($settings['image_max_width'] ?? '2000')); ?>" min="0" max="5000" step="100">
                                    <span class="admin-help">Si una imagen supera este ancho se redimensiona. Usá 0 para no redimensionar.</span>
                                </label>
                            </div>
                        </section>

                        <section class="admin-settings-section">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Carga frontend</span>
                                <h3>Lazyload</h3>
                            </div>
                            <label class="admin-toggle-row">
                                <input type="checkbox" name="image_lazyload_enabled" value="1" <?php echo ((string) ($settings['image_lazyload_enabled'] ?? '1')) === '1' ? 'checked' : ''; ?>>
                                <span>
                                    Activar lazyload en imágenes secundarias
                                    <small>Al desactivarlo, las imágenes marcadas como lazy se sirven como eager.</small>
                                </span>
                            </label>
                        </section>

                        <section class="admin-settings-section admin-image-analyzer" data-image-analyzer data-total-pending="<?php echo (int) $inventory['pending']; ?>">
                            <div class="admin-section-head">
                                <span class="admin-kicker">Analizador</span>
                                <h3>Estado de imágenes del sitio</h3>
                            </div>

                            <div class="admin-image-stats">
                                <article>
                                    <span>Total detectadas</span>
                                    <strong><?php echo (int) $inventory['total']; ?></strong>
                                </article>
                                <article>
                                    <span>Optimizadas</span>
                                    <strong data-image-optimized-count><?php echo (int) $inventory['optimized']; ?></strong>
                                </article>
                                <article>
                                    <span>Por optimizar</span>
                                    <strong data-image-pending-count><?php echo (int) $inventory['pending']; ?></strong>
                                </article>
                                <article>
                                    <span>Peso original</span>
                                    <strong><?php echo commar_admin_h(number_format(((int) $inventory['total_bytes']) / 1048576, 2, ',', '.')); ?> MB</strong>
                                </article>
                            </div>

                            <div class="admin-image-progress" aria-live="polite">
                                <div class="admin-image-progress-track">
                                    <span data-image-progress-bar style="width: <?php echo $inventory['pending'] > 0 ? '0' : '100'; ?>%;"></span>
                                </div>
                                <p data-image-progress-label>
                                    <?php echo $inventory['pending'] > 0 ? 'Proceso pendiente.' : 'Todas las imágenes detectadas están optimizadas.'; ?>
                                </p>
                            </div>

                            <div class="admin-image-batch-actions">
                                <button
                                    type="button"
                                    class="admin-button-primary"
                                    data-image-batch-button
                                    <?php echo !$webpSupported || $inventory['pending'] <= 0 ? 'disabled' : ''; ?>>
                                    Optimizar remanente
                                </button>
                                <span class="admin-help">
                                    El proceso genera archivos WebP paralelos y el sitio los sirve automáticamente cuando existen.
                                </span>
                            </div>

                            <?php if (!$webpSupported): ?>
                                <p class="admin-alert admin-alert-error">El proceso en lote requiere GD con soporte WebP habilitado en PHP.</p>
                            <?php elseif ($pendingItems): ?>
                                <div class="admin-table-wrap">
                                    <table class="admin-post-table admin-image-table">
                                        <thead>
                                            <tr>
                                                <th>Imagen</th>
                                                <th>Dimensiones</th>
                                                <th>Peso</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($pendingItems, 0, 12) as $item): ?>
                                                <tr>
                                                    <td>
                                                        <div class="admin-post-title">
                                                            <strong><?php echo commar_admin_h((string) $item['path']); ?></strong>
                                                            <span><?php echo commar_admin_h((string) $item['webp_path']); ?></span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo (int) $item['width']; ?> x <?php echo (int) $item['height']; ?></td>
                                                    <td><?php echo commar_admin_h(number_format(((int) $item['bytes']) / 1024, 1, ',', '.')); ?> KB</td>
                                                    <td><span class="admin-status-pill is-draft">Pendiente</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if (count($pendingItems) > 12): ?>
                                    <p class="admin-help">Se muestran las primeras 12 imágenes pendientes de <?php echo count($pendingItems); ?> detectadas.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="admin-empty">No hay remanente pendiente para optimizar.</p>
                            <?php endif; ?>
                        </section>

                        <div class="admin-settings-savebar">
                            <div class="admin-settings-savebar-inner">
                                <span>Los cambios afectan las próximas cargas y el render del frontend.</span>
                                <button type="submit" class="admin-button-primary">Guardar configuración</button>
                            </div>
                        </div>
                    </form>
                </div>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script>
        (() => {
            const analyzer = document.querySelector('[data-image-analyzer]');
            if (!analyzer) return;

            const button = analyzer.querySelector('[data-image-batch-button]');
            const bar = analyzer.querySelector('[data-image-progress-bar]');
            const label = analyzer.querySelector('[data-image-progress-label]');
            const pendingCounter = analyzer.querySelector('[data-image-pending-count]');
            const optimizedCounter = analyzer.querySelector('[data-image-optimized-count]');
            const csrfToken = '<?php echo commar_admin_h(commar_admin_csrf_token()); ?>';
            let total = parseInt(analyzer.dataset.totalPending || '0', 10);
            let processed = 0;

            const updateProgress = (pending, text) => {
                const denominator = Math.max(total, processed + pending, 1);
                const percent = Math.max(0, Math.min(100, Math.round((processed / denominator) * 100)));
                bar.style.width = percent + '%';
                pendingCounter.textContent = String(pending);
                if (optimizedCounter) {
                    optimizedCounter.textContent = String(parseInt(optimizedCounter.textContent || '0', 10) + (text ? 0 : 1));
                }
                label.textContent = text || `Optimizadas ${processed} de ${denominator}. Restan ${pending}.`;
            };

            const runStep = async () => {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('total', String(total));

                const response = await fetch('optimize-images-batch.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                const data = await response.json();
                if (!response.ok || !data.ok) {
                    throw new Error(data.error || 'No se pudo optimizar la imagen.');
                }

                if (!data.done) {
                    processed += 1;
                }
                const pending = parseInt(data.pending || '0', 10);
                updateProgress(pending);

                if (pending > 0) {
                    window.setTimeout(runStep, 180);
                    return;
                }

                bar.style.width = '100%';
                label.textContent = 'Proceso completo. Recargando análisis...';
                window.setTimeout(() => window.location.reload(), 800);
            };

            button?.addEventListener('click', () => {
                if (button.disabled) return;
                total = parseInt(analyzer.dataset.totalPending || '0', 10);
                processed = 0;
                button.disabled = true;
                label.textContent = 'Iniciando optimización...';
                runStep().catch((error) => {
                    button.disabled = false;
                    label.textContent = error.message;
                });
            });
        })();
    </script>
</body>
</html>
