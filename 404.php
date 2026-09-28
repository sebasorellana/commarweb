<?php
require_once __DIR__ . '/includes/site.php';

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    $seo = [
        'title' => 'Página no encontrada',
        'description' => 'La página que buscás no existe o cambió de dirección.',
        'path' => '',
        'og_type' => 'website',
        'robots' => 'noindex, follow',
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" href="fonts/inter-latin-var.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="style.css?v=20260928-perf">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'default';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <section class="newsletter-thanks-page" aria-labelledby="not-found-title">
            <div class="newsletter-thanks-shell">
                <div class="newsletter-thanks-mark" aria-hidden="true">
                    <span></span>
                </div>
                <div class="newsletter-thanks-copy">
                    <span class="newsletter-thanks-kicker">Error 404</span>
                    <h1 id="not-found-title" class="newsletter-thanks-title">No encontramos esta página.</h1>
                    <p class="newsletter-thanks-intro">Puede que la dirección haya cambiado o que el contenido ya no esté disponible.</p>
                    <div class="newsletter-thanks-actions">
                        <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="newsletter-thanks-button newsletter-thanks-button-primary">Volver al inicio</a>
                        <a href="<?php echo htmlspecialchars(commar_url('obras.php'), ENT_QUOTES, 'UTF-8'); ?>" class="newsletter-thanks-button">Ver obras</a>
                        <a href="<?php echo htmlspecialchars(commar_url('blog.php'), ENT_QUOTES, 'UTF-8'); ?>" class="newsletter-thanks-button">Ver artículos</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260724-header-contrast" defer></script>
</body>
</html>
