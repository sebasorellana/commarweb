<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/focused-works.php';

$currentLang = commar_current_lang();
$homeHeroImage = (string) commar_setting('home_hero_image');
$homeHeroWidth = (int) commar_setting('home_hero_width');
$homeHeroHeight = (int) commar_setting('home_hero_height');
$homeHeroImages = json_decode((string) commar_setting('home_hero_images'), true);
if (!is_array($homeHeroImages) || count($homeHeroImages) === 0) {
    $homeHeroImages = [[
        'path' => $homeHeroImage,
        'width' => $homeHeroWidth,
        'height' => $homeHeroHeight,
    ]];
}
$homeHeroImages = array_slice(array_values(array_filter($homeHeroImages, static fn($image): bool => is_array($image) && !empty($image['path']))), 0, 3);
if (count($homeHeroImages) === 0) {
    $homeHeroImages = [[
        'path' => $homeHeroImage,
        'width' => $homeHeroWidth,
        'height' => $homeHeroHeight,
    ]];
}
$homeHeroFirstImage = $homeHeroImages[0] ?? ['path' => $homeHeroImage, 'width' => $homeHeroWidth, 'height' => $homeHeroHeight];
$homeHeroTextMode = (string) commar_setting('home_hero_text_mode');
$homeHeroAnimatedWords = array_values(array_filter(array_map('trim', explode(',', (string) commar_setting('home_hero_animated_text')))));
$homeHeroStaticText = trim((string) commar_setting('home_hero_static_text'));
$homeHeroLinkText = trim((string) commar_setting('home_hero_link_text'));
$homeHeroLinkUrl = trim((string) commar_setting('home_hero_link_url'));
$homeHeroCarouselSpeed = max(1500, min(20000, (int) commar_setting('home_hero_carousel_speed')));
$contactEmail = commar_contact_email();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';
    require_once __DIR__ . '/includes/projects.php';
    require_once __DIR__ . '/includes/articles.php';

    $projects = commar_projects();
    $articles = commar_articles();
    $focusedWorksCopy = [
        'es' => [
            'kicker' => 'Obras en foco',
            'list_label' => 'Obras destacadas',
            'nav_label' => 'Controles del carrusel',
            'prev_label' => 'Anterior',
            'next_label' => 'Siguiente',
        ],
        'en' => [
            'kicker' => 'Focused works',
            'list_label' => 'Featured works',
            'nav_label' => 'Carousel controls',
            'prev_label' => 'Previous',
            'next_label' => 'Next',
        ],
        'pt' => [
            'kicker' => 'Obras em destaque',
            'list_label' => 'Obras destacadas',
            'nav_label' => 'Controles do carrossel',
            'prev_label' => 'Anterior',
            'next_label' => 'Próximo',
        ],
    ];
    $focusedWorks = $focusedWorksCopy[$currentLang];
    $featuredProjects = commar_get_focused_works($currentLang);
    $seo = [
        'title' => 'Estudio de arquitectura contemporánea',
        'description' => 'COMMAR GROUP es un estudio de arquitectura contemporánea que diseña espacios de alto impacto, obras radicales y manifiestos espaciales para clientes globales.',
        'path' => '',
        'image' => (string) ($homeHeroFirstImage['path'] ?? $homeHeroImage),
        'image_alt' => 'Visual arquitectónico de COMMAR GROUP',
        'image_width' => (int) ($homeHeroFirstImage['width'] ?? $homeHeroWidth),
        'image_height' => (int) ($homeHeroFirstImage['height'] ?? $homeHeroHeight),
        'og_type' => 'website',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => 'COMMAR GROUP',
                'url' => commar_absolute_url(''),
                'logo' => commar_absolute_url('img/logo-commar-500.png'),
                'email' => $contactEmail,
                'description' => 'Estudio de arquitectura contemporánea con base creativa en Buenos Aires y presencia internacional.',
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer support',
                    'email' => $contactEmail,
                    'availableLanguage' => ['es', 'en', 'pt'],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'COMMAR GROUP',
                'url' => commar_absolute_url(''),
                'inLanguage' => commar_lang_attr(),
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'COMMAR GROUP | Estudio de arquitectura contemporánea',
                'url' => commar_absolute_url(''),
                'description' => 'COMMAR GROUP es un estudio de arquitectura contemporánea que diseña espacios de alto impacto, obras radicales y manifiestos espaciales para clientes globales.',
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => 'COMMAR GROUP',
                    'url' => commar_absolute_url(''),
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Obras seleccionadas de COMMAR GROUP',
                'itemListElement' => array_map(
                    static fn (array $project, int $index): array => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => commar_absolute_url('obra.php?slug=' . $project['slug']),
                        'name' => $project['title'],
                    ],
                    $projects,
                    array_keys($projects)
                ),
            ],
        ],
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" as="image" href="<?php echo htmlspecialchars($homeHeroImage, ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260508-1">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'home';
    $menuItems = [
        ['label' => 'Inicio', 'href' => 'index.php'],
        ['label' => 'El estudio', 'href' => 'el-estudio.php'],
        ['label' => 'Servicios', 'href' => 'servicios.php'],
        ['label' => 'Obra Viva', 'href' => 'obra-viva.php'],
        ['label' => 'Obras', 'href' => 'obras.php'],
        ['label' => 'Blog', 'href' => 'blog.php'],
        ['label' => 'Contacto', 'href' => 'contacto.php'],
    ];
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
    <section id="hero-home" class="hero-reveal-section" aria-labelledby="hero-home-title">
        <h1 id="hero-home-title" class="sr-only">COMMAR GROUP, estudio de arquitectura contemporánea y diseño espacial</h1>

        <div class="hero-reveal-container">
            <div class="hero-reveal-media" aria-hidden="true" data-hero-carousel data-hero-carousel-speed="<?php echo $homeHeroCarouselSpeed; ?>">
                <?php foreach ($homeHeroImages as $index => $image): ?>
                    <img src="<?php echo htmlspecialchars((string) $image['path'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) ($image['width'] ?? $homeHeroWidth); ?>" height="<?php echo (int) ($image['height'] ?? $homeHeroHeight); ?>" fetchpriority="<?php echo $index === 0 ? 'high' : 'auto'; ?>" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>" decoding="async" class="hero-reveal-image<?php echo $index === 0 ? ' is-active' : ''; ?>">
                <?php endforeach; ?>
                <div class="hero-reveal-overlay" aria-hidden="true"></div>
            </div>

            <div class="hero-reveal-content">
                <?php if ($homeHeroTextMode === 'link' && $homeHeroLinkText !== '' && $homeHeroLinkUrl !== ''): ?>
                    <a href="<?php echo htmlspecialchars($homeHeroLinkUrl, ENT_QUOTES, 'UTF-8'); ?>" class="hero-reveal-title hero-reveal-title-link">
                        <span><?php echo htmlspecialchars($homeHeroLinkText, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                <?php else: ?>
                    <p class="hero-reveal-title" aria-hidden="true">
                        <?php if ($homeHeroTextMode === 'animated_static' && count($homeHeroAnimatedWords) > 0): ?>
                            <span class="hero-reveal-title-thin hero-typewriter-line"><span class="hero-typewriter"><span class="hero-typewriter-cursor" aria-hidden="true"></span><span data-hero-typewriter data-hero-typewriter-words="<?php echo htmlspecialchars(json_encode($homeHeroAnimatedWords, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($homeHeroAnimatedWords[0], ENT_QUOTES, 'UTF-8'); ?></span></span></span>
                        <?php endif; ?>
                        <?php if ($homeHeroStaticText !== ''): ?>
                            <span><?php echo htmlspecialchars($homeHeroStaticText, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <p class="hero-reveal-description">Diseñamos manifiestos espaciales para clientes globales, con una mirada precisa, material y decididamente contemporánea.</p>
            </div>
        </div>
    </section>

    <!-- ACERCA DEL ESTUDIO -->
    <section id="acerca-del-estudio" class="studio-overview-section" aria-labelledby="acerca-del-estudio-title">
        <div class="site-shell-wide">
            <div class="studio-overview-grid">
                <div class="studio-overview-media" data-scroll-reveal="left">
                    <picture>
                        <source srcset="img/romina-lo-conte-mobile.jpg" media="(max-width: 767px)">
                        <img src="img/romina-lo-conte.jpg" alt="Retrato de la arquitecta Romina Lo Conte, fundadora y directora creativa de COMMAR GROUP" width="852" height="1280" loading="lazy" decoding="async" class="studio-overview-image">
                    </picture>
                </div>

                <div class="studio-overview-copy" data-scroll-reveal="up" style="--reveal-delay: 0.12s;">
                    <span class="studio-overview-kicker">Acerca del estudio</span>
                    <h2 id="acerca-del-estudio-title" class="studio-overview-title">Arquitectura, Construcción y Medio Ambiente en un equipo con experiencia y trayectoria.</h2>
                    <div class="studio-overview-text">
                        <p>Somos un estudio multidisciplinario que integra arquitectura, construcción y soluciones ambientales, acompañando proyectos de punta a punta con profesionalismo, experiencia y foco en la calidad.</p>
                    </div>
                    <blockquote class="studio-overview-quote">
                        <p>"Nuestro valor no está solo en lo que construimos, sino en todo lo que evitamos que falle."</p>
                        <footer>Romina Lo Conte</footer>
                    </blockquote>
                    <a href="el-estudio.php" class="studio-overview-link">Conocer el estudio</a>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="services-section" aria-labelledby="servicios-title">
        <div class="site-shell-wide">
            <div class="services-header" data-scroll-reveal="up">
                <p class="services-watermark" aria-hidden="true">SERVICIOS</p>
                <div class="services-heading">
                    <span class="services-kicker">Servicios</span>
                    <h2 id="servicios-title" class="services-title">Soluciones integrales para proyectos que exigen precisión.</h2>
                </div>
                <p class="services-intro">Acompañamos cada etapa con dirección técnica, documentación clara y una gestión coordinada entre proyecto, obra, normativa y ambiente.</p>
            </div>

            <div class="services-grid">
                <article class="service-card" data-scroll-reveal="up" style="--reveal-delay: 0.04s;">
                    <span class="service-card-index">01</span>
                    <h3 class="service-card-title"><a href="servicio-proyectos.php" class="service-card-title-link">Proyecto</a></h3>
                    <p class="service-card-text">Anteproyecto, proyecto ejecutivo, documentación técnica y coordinación de decisiones para obras claras desde el inicio.</p>
                </article>
                <article class="service-card" data-scroll-reveal="up" style="--reveal-delay: 0.1s;">
                    <span class="service-card-index">02</span>
                    <h3 class="service-card-title">Gerenciamiento</h3>
                    <p class="service-card-text">Planificación, coordinación de equipos, control de avances y seguimiento técnico para ordenar cada etapa de obra.</p>
                </article>
                <article class="service-card" data-scroll-reveal="up" style="--reveal-delay: 0.16s;">
                    <span class="service-card-index">03</span>
                    <h3 class="service-card-title">Demolición</h3>
                    <p class="service-card-text">Planificación, permisos, control técnico y coordinación operativa para intervenciones seguras y ordenadas.</p>
                </article>
                <article class="service-card" data-scroll-reveal="up" style="--reveal-delay: 0.22s;">
                    <span class="service-card-index">04</span>
                    <h3 class="service-card-title">Construcción</h3>
                    <p class="service-card-text">Ejecución, seguimiento de avances, control de calidad y coordinación de gremios en campo.</p>
                </article>
                <article class="service-card" data-scroll-reveal="up" style="--reveal-delay: 0.28s;">
                    <span class="service-card-index">05</span>
                    <h3 class="service-card-title">Habilitaciones</h3>
                    <p class="service-card-text">Documentación, gestión normativa y acompañamiento técnico para habilitar locales, actividades y espacios comerciales.</p>
                </article>
                <article class="service-card" data-scroll-reveal="up" style="--reveal-delay: 0.34s;">
                    <span class="service-card-index">06</span>
                    <h3 class="service-card-title">Medio ambiente / Seguridad e Higiene</h3>
                    <p class="service-card-text">Consultoría ambiental, seguridad e higiene, análisis normativo y documentación para proyectos preparados para su contexto.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER -->
    <?php include __DIR__ . '/includes/newsletter.php'; ?>

    <!-- EXPERIENCIA MATERIAL -->
    <section id="experiencia-material" class="shader-carousel-section" aria-labelledby="experiencia-material-title">
        <div class="shader-carousel" data-project-carousel>
            <div class="shader-carousel-media" aria-hidden="true">
                <?php foreach ($featuredProjects as $projectIndex => $project): ?>
                    <img src="<?php echo htmlspecialchars($project['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $project['img_width']; ?>" height="<?php echo (int) $project['img_height']; ?>" loading="<?php echo $projectIndex === 0 ? 'eager' : 'lazy'; ?>" decoding="async" class="shader-carousel-image<?php echo $projectIndex === 0 ? ' is-active' : ''; ?>">
                <?php endforeach; ?>
            </div>
            <div class="shader-carousel-scrim" aria-hidden="true"></div>

            <div class="shader-carousel-content" data-scroll-reveal="right">
                <span class="shader-carousel-kicker"><?php echo htmlspecialchars($focusedWorks['kicker'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h2 id="experiencia-material-title" class="shader-carousel-title"><?php echo htmlspecialchars($featuredProjects[0]['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="shader-carousel-description"><?php echo htmlspecialchars($featuredProjects[0]['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <ul class="shader-carousel-list" aria-label="<?php echo htmlspecialchars($focusedWorks['list_label'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php foreach ($featuredProjects as $projectIndex => $project): ?>
                    <li class="shader-carousel-slide<?php echo $projectIndex === 0 ? ' is-active' : ''; ?>" data-title="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>" data-description="<?php echo htmlspecialchars($project['summary'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="button">
                            <span><?php echo htmlspecialchars($project['id'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <strong><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="shader-carousel-nav" aria-label="<?php echo htmlspecialchars($focusedWorks['nav_label'], ENT_QUOTES, 'UTF-8'); ?>" data-scroll-reveal="up" style="--reveal-delay: 0.28s;">
                <button class="shader-carousel-arrow" type="button" data-project-prev aria-label="<?php echo htmlspecialchars($focusedWorks['prev_label'], ENT_QUOTES, 'UTF-8'); ?>">←</button>
                <button class="shader-carousel-arrow" type="button" data-project-next aria-label="<?php echo htmlspecialchars($focusedWorks['next_label'], ENT_QUOTES, 'UTF-8'); ?>">→</button>
            </div>
        </div>
    </section>

    <!-- INSIGHTS -->
    <section class="insights-section" aria-labelledby="insights-title">
        <div class="site-shell-wide">
            <div class="insights-module">
                <div class="insights-copy" data-scroll-reveal="up">
                    <span class="insights-kicker">Insights</span>
                    <h2 id="insights-title" class="insights-title">Últimos artículos del blog de COMMAR GROUP.</h2>
                </div>
                <div class="insights-list">
                    <?php foreach (array_slice($articles, 0, 3) as $articleIndex => $article): ?>
                        <article class="insight-card" data-scroll-reveal="up" style="--reveal-delay: <?php echo htmlspecialchars(number_format($articleIndex * 0.08, 2, '.', ''), ENT_QUOTES, 'UTF-8'); ?>s;">
                            <a href="<?php echo htmlspecialchars($article['url'], ENT_QUOTES, 'UTF-8'); ?>" class="insight-card-media<?php echo $article['image'] === '' ? ' is-placeholder' : ''; ?>" aria-label="<?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="<?php echo htmlspecialchars($article['display_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $article['display_image_width']; ?>" height="<?php echo (int) $article['display_image_height']; ?>" loading="lazy" decoding="async" class="insight-card-image">
                            </a>
                            <div class="insight-card-copy">
                                <span class="insight-card-meta"><?php echo htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($article['year'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <h3 class="insight-card-title"><a href="<?php echo htmlspecialchars($article['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                                <?php if (!empty($article['tags'])): ?>
                                    <div class="article-tag-list">
                                        <?php foreach (array_slice($article['tags'], 0, 3) as $tag): ?>
                                            <span><?php echo htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
