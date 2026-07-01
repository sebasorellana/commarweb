<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/menu.php';

commar_admin_require_administrator();

$message = '';
$messageType = '';

function commar_admin_menu_post_items(string $location): array
{
    $labels = $_POST[$location . '_label'] ?? [];
    $hrefs = $_POST[$location . '_href'] ?? [];
    $orders = $_POST[$location . '_order'] ?? [];
    $enabled = $_POST[$location . '_enabled'] ?? [];
    $requiresActiveJobs = $_POST[$location . '_requires_active_jobs'] ?? [];

    $labels = is_array($labels) ? $labels : [];
    $items = [];
    foreach ($labels as $index => $label) {
        $label = trim((string) $label);
        $href = trim((string) ($hrefs[$index] ?? ''));
        if ($label === '' || $href === '') {
            continue;
        }

        $items[] = [
            'label' => $label,
            'href' => $href,
            'order' => (int) ($orders[$index] ?? ($index + 1)),
            'enabled' => isset($enabled[$index]),
            'requires_active_jobs' => $location === 'footer' && commar_menu_item_is_jobs_link($label, $href) && isset($requiresActiveJobs[$index]),
        ];
    }

    $newLabel = trim((string) ($_POST[$location . '_new_label'] ?? ''));
    $newHref = trim((string) ($_POST[$location . '_new_href'] ?? ''));
    if ($newLabel !== '' && $newHref !== '') {
        $items[] = [
            'label' => $newLabel,
            'href' => $newHref,
            'order' => count($items) + 1,
            'enabled' => true,
            'requires_active_jobs' => $location === 'footer' && commar_menu_item_is_jobs_link($newLabel, $newHref) && isset($_POST[$location . '_new_requires_active_jobs']),
        ];
    }

    return $items;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!commar_admin_verify_csrf_token()) {
        $message = 'Error de seguridad. Por favor, intentalo de nuevo.';
        $messageType = 'error';
    } else {
        commar_save_menu_items('header', commar_admin_menu_post_items('header'));
        commar_save_menu_items('footer', commar_admin_menu_post_items('footer'));
        header('Location: settings-menu.php?updated=1');
        exit;
    }
}

$updated = isset($_GET['updated']);
$headerItems = commar_menu_items('header', false);
$footerItems = commar_menu_items('footer', false);

function commar_admin_menu_section(string $location, string $title, array $items): void
{
    ?>
    <section class="admin-settings-section admin-menu-section">
        <div class="admin-menu-section-head">
            <div>
                <span class="admin-kicker"><?php echo commar_admin_h($location === 'header' ? 'Header' : 'Footer'); ?></span>
                <h3><?php echo commar_admin_h($title); ?></h3>
            </div>
            <span><?php echo count($items); ?> ítems</span>
        </div>
        <div class="admin-menu-editor" data-menu-sortable>
            <div class="admin-menu-editor-head">
                <span>Mover</span>
                <span>Estado</span>
                <span>Texto</span>
                <span>URL</span>
                <span>Regla</span>
            </div>
            <?php foreach ($items as $index => $item): ?>
                <?php
                $labelValue = (string) ($item['label'] ?? '');
                $hrefValue = (string) ($item['href'] ?? '');
                $isJobsLink = $location === 'footer' && commar_menu_item_is_jobs_link($labelValue, $hrefValue);
                ?>
                <div class="admin-menu-editor-row" draggable="true">
                    <button type="button" class="admin-menu-drag-handle" aria-label="Arrastrar ítem" title="Arrastrar para ordenar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
                    </button>
                    <input type="hidden" name="<?php echo $location; ?>_order[<?php echo $index; ?>]" value="<?php echo (int) ($item['order'] ?? ($index + 1)); ?>" data-menu-order>
                    <label class="admin-switch">
                        <input type="checkbox" name="<?php echo $location; ?>_enabled[<?php echo $index; ?>]" value="1" <?php echo !empty($item['enabled']) ? 'checked' : ''; ?>>
                        <span></span>
                        Activo
                    </label>
                    <input type="text" name="<?php echo $location; ?>_label[<?php echo $index; ?>]" value="<?php echo commar_admin_h($labelValue); ?>" maxlength="80" required aria-label="Texto">
                    <input type="text" name="<?php echo $location; ?>_href[<?php echo $index; ?>]" value="<?php echo commar_admin_h($hrefValue); ?>" maxlength="255" required aria-label="URL">
                    <?php if ($isJobsLink): ?>
                        <label class="admin-checkbox-row admin-menu-rule">
                            <input type="checkbox" name="<?php echo $location; ?>_requires_active_jobs[<?php echo $index; ?>]" value="1" <?php echo !empty($item['requires_active_jobs']) ? 'checked' : ''; ?>>
                            Mostrar solo con búsquedas activas
                        </label>
                    <?php else: ?>
                        <span class="admin-menu-rule-empty">Siempre disponible</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="admin-menu-new-item">
            <span>Nuevo ítem</span>
            <input type="text" name="<?php echo $location; ?>_new_label" maxlength="80" placeholder="Texto">
            <input type="text" name="<?php echo $location; ?>_new_href" maxlength="255" placeholder="URL, ej: servicios.php">
        </div>
    </section>
    <?php
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú | MOnkey CMS</title>
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
                <?php commar_admin_settings_nav('menu'); ?>

                <?php if ($updated): ?>
                    <p class="admin-alert admin-alert-success">Menú actualizado.</p>
                <?php endif; ?>
                <?php if ($message): ?>
                    <p class="admin-alert admin-alert-<?php echo $messageType; ?>"><?php echo commar_admin_h($message); ?></p>
                <?php endif; ?>

                <form action="settings-menu.php" method="post" class="admin-settings-container admin-menu-settings-form">
                    <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                    <?php commar_admin_menu_section('header', 'Ítems del header', $headerItems); ?>
                    <?php commar_admin_menu_section('footer', 'Ítems del footer', $footerItems); ?>
                    <div class="admin-settings-savebar admin-menu-savebar">
                        <div class="admin-settings-savebar-inner">
                            <span>Configuración de menú</span>
                            <button type="submit" class="admin-button-primary">Guardar menú</button>
                        </div>
                    </div>
                </form>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
    <script src="admin.js?v=20260701-menu-sortable" defer></script>
</body>
</html>
