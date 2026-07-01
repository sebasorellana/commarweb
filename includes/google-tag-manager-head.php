<?php
require_once __DIR__ . '/integrations.php';

$googleTagManagerId = commar_google_tag_manager_id();

if ($googleTagManagerId === '') {
    return;
}
?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo htmlspecialchars($googleTagManagerId, ENT_QUOTES, 'UTF-8'); ?>');</script>
    <!-- End Google Tag Manager -->
