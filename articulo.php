<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/articles.php';

$slug = (string) ($_GET['slug'] ?? '');
$article = commar_find_article_by_slug($slug);

if (!$article || ($article['status'] ?? 'published') !== 'published') {
    http_response_code(404);
    $articleTitle = 'Artículo no encontrado';
    $articleDescription = 'El artículo solicitado no está disponible.';
} else {
    if (strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), 'articulo.php?slug=') !== false) {
        header('Location: ' . commar_url(commar_article_url($article['slug'])), true, 301);
        exit;
    }

    $articleTitle = $article['title'];
    $articleDescription = $article['description'];
}

function commar_render_article_shortcodes(string $html): string
{
    return preg_replace_callback(
        '/(?:<p>\s*)?\[youtube\s+id=(["\']?)([a-zA-Z0-9_-]{11})\1\]\s*(?:<\/p>)?/i',
        static function (array $matches): string {
            $id = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');

            return '<div class="article-video-section article-video-section-inline"><div class="article-video-wrapper"><iframe src="https://www.youtube.com/embed/' . $id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen title="Video de YouTube integrado"></iframe></div></div>';
        },
        $html
    ) ?? $html;
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    $seo = [
        'title' => $articleTitle,
        'description' => $articleDescription,
        'path' => $article ? commar_article_url($article['slug']) : 'blog.php',
        'image' => $article['image'] ?? 'img/logo-commar-500.png',
        'image_alt' => $articleTitle,
        'image_width' => $article['image_width'] ?? null,
        'image_height' => $article['image_height'] ?? null,
        'og_type' => 'article',
        'json_ld' => $article ? [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $articleTitle,
                'description' => $articleDescription,
                'image' => commar_absolute_url($article['image']),
                'datePublished' => $article['published_at'] ?? null,
                'author' => [
                    '@type' => 'Organization',
                    'name' => 'COMMAR GROUP',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'COMMAR GROUP',
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => commar_absolute_url('img/logo-commar-500.png'),
                    ],
                ],
                'mainEntityOfPage' => commar_absolute_url(commar_article_url($article['slug'])),
                'inLanguage' => commar_lang_attr(),
            ],
        ] : [],
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260508-1">
</head>
<body>
    <?php
    $headerVariant = 'default';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <?php if (!$article): ?>
            <section class="article-page">
                <div class="article-body-section">
                    <div class="article-body">
                        <h1><?php echo htmlspecialchars($articleTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p><?php echo htmlspecialchars($articleDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <article class="article-page">
                <header class="article-hero">
                    <div class="article-hero-media" aria-hidden="true">
                        <img src="<?php echo htmlspecialchars($article['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) ($article['image_width'] ?? 1400); ?>" height="<?php echo (int) ($article['image_height'] ?? 933); ?>" loading="eager" decoding="async" class="article-hero-image">
                        <div class="article-hero-overlay"></div>
                    </div>
                    <div class="site-shell-wide article-hero-inner">
                        <span class="article-kicker"><?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($article['year'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h1 class="article-title"><?php echo htmlspecialchars($articleTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p class="article-intro"><?php echo htmlspecialchars($articleDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </header>

                <div class="article-body-section">
                    <div class="article-body">
                        <?php
                        $contentHtml = trim((string) ($article['content_html'] ?? ''));
                        if ($contentHtml !== '') {
                            echo commar_render_article_shortcodes($contentHtml); // Sanitizado al guardar
                        } else {
                            // Fallback para contenido antiguo sin HTML
                            foreach ($article['content'] as $paragraph) {
                                echo '<p>' . htmlspecialchars((string) $paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <?php if (!empty($article['youtube_url'])): ?>
                    <div class="article-video-section">
                        <div class="article-video-wrapper">
                            <iframe
                                src="<?php echo htmlspecialchars($article['youtube_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                                title="Video de YouTube integrado">
                            </iframe>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($article['gallery'])): ?>
                    <div class="article-gallery-section">
                        <div class="article-gallery">
                            <?php foreach ($article['gallery'] as $image): ?>
                                <figure class="article-gallery-item">
                                    <img
                                        src="<?php echo htmlspecialchars((string) ($image['path'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                                        alt=""
                                        width="<?php echo (int) ($image['width'] ?? 1000); ?>"
                                        height="<?php echo (int) ($image['height'] ?? 1000); ?>"
                                        loading="<?php echo commar_image_loading_attr('lazy'); ?>"
                                        decoding="async">
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php
                $articlePath = commar_article_url($article['slug']);
                include __DIR__ . '/includes/article-share.php';
                ?>
            </article>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
