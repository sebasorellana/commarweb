<?php
require_once __DIR__ . '/integrations.php';

$googleTagManagerId = commar_google_tag_manager_id();

if ($googleTagManagerId === '') {
    return;
}
?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo htmlspecialchars($googleTagManagerId, ENT_QUOTES, 'UTF-8'); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
