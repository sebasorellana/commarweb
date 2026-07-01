<?php
require_once __DIR__ . '/site.php';
require_once __DIR__ . '/integrations.php';

$seo = $seo ?? [];
$siteName = 'COMMAR GROUP';
$pageTitle = $seo['title'] ?? $siteName;
$metaTitle = $pageTitle . ' | ' . $siteName;
$description = $seo['description'] ?? 'Estudio de arquitectura contemporánea enfocado en diseño radical, manifiestos espaciales y obras de alto impacto.';
$pagePath = $seo['path'] ?? '';
$canonicalUrl = commar_absolute_url(commar_localized_path($pagePath, commar_current_lang()));
$alternateUrls = [
    'es-AR' => commar_absolute_url(commar_localized_path($pagePath, 'es')),
    'en-US' => commar_absolute_url(commar_localized_path($pagePath, 'en')),
    'pt-BR' => commar_absolute_url(commar_localized_path($pagePath, 'pt')),
];
$ogType = $seo['og_type'] ?? 'website';
$ogImage = commar_absolute_url($seo['image'] ?? 'img/logo-commar-500.png');
$ogImageAlt = $seo['image_alt'] ?? $pageTitle;
$ogImageWidth = $seo['image_width'] ?? null;
$ogImageHeight = $seo['image_height'] ?? null;
$twitterCard = $seo['twitter_card'] ?? 'summary_large_image';
$robots = $seo['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
$jsonLd = $seo['json_ld'] ?? [];
$ogLocale = $seo['locale'] ?? commar_locale();
include __DIR__ . '/google-tag-manager-head.php';
$googleAnalyticsId = commar_google_analytics_id();
$recaptchaEnabled = commar_recaptcha_enabled();
$recaptchaSiteKey = commar_recaptcha_site_key();
$recaptchaVersion = commar_recaptcha_version();
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo htmlspecialchars(rtrim(commar_base_url(), '/') . '/', ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="<?php echo htmlspecialchars($robots, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="COMMAR GROUP">
    <meta name="theme-color" content="#0a0a0a">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<?php foreach ($alternateUrls as $hreflang => $alternateUrl): ?>
    <link rel="alternate" hreflang="<?php echo htmlspecialchars($hreflang, ENT_QUOTES, 'UTF-8'); ?>" href="<?php echo htmlspecialchars($alternateUrl, ENT_QUOTES, 'UTF-8'); ?>">
<?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($alternateUrls['es-AR'], ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="icon" type="image/png" href="img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="img/logo-commar-500.png">

    <meta property="og:locale" content="<?php echo htmlspecialchars($ogLocale, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="<?php echo htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image:alt" content="<?php echo htmlspecialchars($ogImageAlt, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($ogImageWidth): ?>
    <meta property="og:image:width" content="<?php echo htmlspecialchars((string) $ogImageWidth, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
<?php if ($ogImageHeight): ?>
    <meta property="og:image:height" content="<?php echo htmlspecialchars((string) $ogImageHeight, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>

    <meta name="twitter:card" content="<?php echo htmlspecialchars($twitterCard, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($ogImageAlt, ENT_QUOTES, 'UTF-8'); ?>">

<?php foreach ($jsonLd as $schema): ?>
    <script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></script>
<?php endforeach; ?>
<?php if ($googleAnalyticsId !== ''): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($googleAnalyticsId, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($googleAnalyticsId, ENT_QUOTES, 'UTF-8'); ?>');
    </script>
    <!-- End Google Analytics -->
<?php endif; ?>
<?php if ($recaptchaEnabled && $recaptchaSiteKey !== ''): ?>
    <?php if ($recaptchaVersion === 'v2'): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php else: ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8'); ?>" defer></script>
    <script>
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form.matches('[data-recaptcha-form]') || form.dataset.recaptchaReady === '1') {
                return;
            }

            var token = form.querySelector('[data-recaptcha-token]');
            if (!token || !window.grecaptcha) {
                return;
            }

            event.preventDefault();
            window.grecaptcha.ready(function () {
                window.grecaptcha.execute('<?php echo htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8'); ?>', { action: form.dataset.recaptchaAction || 'submit' }).then(function (value) {
                    token.value = value;
                    form.dataset.recaptchaReady = '1';
                    form.requestSubmit ? form.requestSubmit() : form.submit();
                });
            });
        }, true);
    </script>
    <?php endif; ?>
<?php endif; ?>
