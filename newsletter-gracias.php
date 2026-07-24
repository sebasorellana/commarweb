<?php require_once __DIR__ . '/includes/site.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    $copy = [
        'es' => [
            'kicker' => 'Newsletter',
            'title' => 'Gracias por suscribirte.',
            'intro' => 'A partir de ahora vas a recibir novedades, ideas y próximos movimientos de COMMAR GROUP en tu correo.',
            'note' => 'Mientras tanto, podés recorrer nuestros artículos o volver al inicio.',
            'home' => 'Volver al inicio',
            'blog' => 'Ver artículos',
            'meta_title' => 'Gracias por suscribirte',
            'meta_description' => 'Confirmación de suscripción al newsletter de COMMAR GROUP.',
        ],
        'en' => [
            'kicker' => 'Newsletter',
            'title' => 'Thanks for subscribing.',
            'intro' => 'From now on, you will receive updates, ideas and upcoming COMMAR GROUP news in your inbox.',
            'note' => 'In the meantime, you can read our articles or return home.',
            'home' => 'Back home',
            'blog' => 'Read articles',
            'meta_title' => 'Thanks for subscribing',
            'meta_description' => 'Newsletter subscription confirmation for COMMAR GROUP.',
        ],
        'pt' => [
            'kicker' => 'Newsletter',
            'title' => 'Obrigado pela inscrição.',
            'intro' => 'A partir de agora, você receberá novidades, ideias e próximos movimentos da COMMAR GROUP no seu e-mail.',
            'note' => 'Enquanto isso, você pode ler nossos artigos ou voltar ao início.',
            'home' => 'Voltar ao início',
            'blog' => 'Ver artigos',
            'meta_title' => 'Obrigado pela inscrição',
            'meta_description' => 'Confirmação de inscrição na newsletter da COMMAR GROUP.',
        ],
    ];
    $pageCopy = $copy[commar_current_lang()] ?? $copy['es'];
    $submittedEmail = trim((string) ($_POST['email'] ?? ''));
    $emailIsValid = $submittedEmail !== '' && filter_var($submittedEmail, FILTER_VALIDATE_EMAIL);

    $seo = [
        'title' => $pageCopy['meta_title'],
        'description' => $pageCopy['meta_description'],
        'path' => 'newsletter-gracias.php',
        'image' => 'img/logo-commar-500.png',
        'image_alt' => 'COMMAR GROUP',
        'og_type' => 'website',
        'robots' => 'noindex, nofollow',
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260724-header-contrast">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'default';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <section class="newsletter-thanks-page" aria-labelledby="newsletter-thanks-title">
            <div class="newsletter-thanks-shell">
                <div class="newsletter-thanks-mark" aria-hidden="true">
                    <span></span>
                </div>
                <div class="newsletter-thanks-copy">
                    <span class="newsletter-thanks-kicker"><?php echo htmlspecialchars($pageCopy['kicker'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <h1 id="newsletter-thanks-title" class="newsletter-thanks-title"><?php echo htmlspecialchars($pageCopy['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="newsletter-thanks-intro"><?php echo htmlspecialchars($pageCopy['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if ($emailIsValid): ?>
                        <p class="newsletter-thanks-email"><?php echo htmlspecialchars($submittedEmail, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <p class="newsletter-thanks-note"><?php echo htmlspecialchars($pageCopy['note'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="newsletter-thanks-actions">
                        <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="newsletter-thanks-button newsletter-thanks-button-primary"><?php echo htmlspecialchars($pageCopy['home'], ENT_QUOTES, 'UTF-8'); ?></a>
                        <a href="<?php echo htmlspecialchars(commar_url('blog.php'), ENT_QUOTES, 'UTF-8'); ?>" class="newsletter-thanks-button"><?php echo htmlspecialchars($pageCopy['blog'], ENT_QUOTES, 'UTF-8'); ?></a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260724-header-contrast" defer></script>
</body>
</html>
