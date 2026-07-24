<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/page-heroes.php';
$blogHero = commar_page_hero('blog');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';
    require_once __DIR__ . '/includes/articles.php';

    $articles = commar_articles();
    $seo = [
        'title' => 'Blog',
        'description' => 'Artículos de COMMAR GROUP sobre arquitectura, construcción, documentación técnica, gestión de obra y estrategias ambientales.',
        'path' => 'blog.php',
        'image' => (string) $blogHero['image'],
        'image_alt' => 'COMMAR GROUP',
        'og_type' => 'website',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => 'Blog COMMAR GROUP',
                'description' => 'Artículos sobre arquitectura, construcción, documentación técnica y estrategias ambientales.',
                'url' => commar_absolute_url(commar_url('blog.php')),
                'inLanguage' => commar_lang_attr(),
            ],
        ],
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
        <section class="page-hero-section" aria-labelledby="blog-title">
            <div class="page-hero-media" aria-hidden="true">
                <img src="<?php echo htmlspecialchars((string) $blogHero['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $blogHero['width']; ?>" height="<?php echo (int) $blogHero['height']; ?>" fetchpriority="high" decoding="async" class="page-hero-image">
                <div class="page-hero-overlay"></div>
            </div>
            <div class="site-shell-wide page-hero-content">
                <span class="page-hero-kicker"><?php echo htmlspecialchars((string) $blogHero['kicker'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h1 id="blog-title" class="page-hero-title"><?php echo htmlspecialchars((string) $blogHero['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="page-hero-intro"><?php echo htmlspecialchars((string) $blogHero['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </section>

        <section class="blog-page" aria-labelledby="blog-title">
            <div class="site-shell-wide">
                <div class="blog-list">
                    <?php foreach ($articles as $article): ?>
                        <article class="blog-card">
                            <a href="<?php echo htmlspecialchars(commar_url($article['url']), ENT_QUOTES, 'UTF-8'); ?>" class="blog-card-media<?php echo $article['image'] === '' ? ' is-placeholder' : ''; ?>" aria-label="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="<?php echo htmlspecialchars($article['display_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $article['display_image_width']; ?>" height="<?php echo (int) $article['display_image_height']; ?>" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="blog-card-image">
                            </a>
                            <div class="blog-card-copy">
                                <span class="blog-card-meta"><?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($article['year'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <h2 class="blog-card-title"><a href="<?php echo htmlspecialchars(commar_url($article['url']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></a></h2>
                                <?php if (!empty($article['tags'])): ?>
                                    <div class="article-tag-list">
                                        <?php foreach ($article['tags'] as $tag): ?>
                                            <span><?php echo htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <p class="blog-card-description"><?php echo htmlspecialchars($article['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260724-header-contrast" defer></script>
</body>
</html>
