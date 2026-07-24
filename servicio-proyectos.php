<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/page-heroes.php';
$projectHero = commar_page_hero('servicio_proyectos');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';

    $seo = [
        'title' => 'Servicio de Proyecto',
        'description' => 'Servicio de proyecto de COMMAR GROUP: anteproyecto, proyecto ejecutivo, documentación técnica y coordinación integral para obras claras desde el inicio.',
        'path' => 'servicio-proyectos.php',
        'image' => 'img/proyecto-01.jpg',
        'image_alt' => 'Proyecto arquitectónico desarrollado por COMMAR GROUP',
        'image_width' => 1400,
        'image_height' => 933,
        'og_type' => 'article',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => 'Proyecto',
                'provider' => [
                    '@type' => 'Organization',
                    'name' => 'COMMAR GROUP',
                    'url' => commar_absolute_url('index.php'),
                ],
                'areaServed' => 'Argentina',
                'serviceType' => 'Proyecto arquitectónico',
                'description' => 'Anteproyecto, proyecto ejecutivo, documentación técnica y coordinación de decisiones para obras claras desde el inicio.',
                'url' => commar_absolute_url('servicio-proyectos.php'),
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Inicio',
                        'item' => commar_absolute_url('index.php'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Servicios',
                        'item' => commar_absolute_url('servicios.php'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => 'Proyecto',
                        'item' => commar_absolute_url('servicio-proyectos.php'),
                    ],
                ],
            ],
        ],
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" as="image" href="img/proyecto-01.jpg" fetchpriority="high">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260724-header-contrast">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'home';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <section class="service-detail-hero" aria-labelledby="service-detail-title">
            <div class="service-detail-hero-media" aria-hidden="true">
                <img src="<?php echo htmlspecialchars((string) $projectHero['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $projectHero['width']; ?>" height="<?php echo (int) $projectHero['height']; ?>" fetchpriority="high" decoding="async" class="service-detail-hero-image">
                <div class="service-detail-hero-overlay"></div>
            </div>

            <div class="site-shell-wide service-detail-hero-content">
                <span class="service-detail-kicker"><?php echo htmlspecialchars((string) $projectHero['kicker'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h1 id="service-detail-title" class="service-detail-title"><?php echo htmlspecialchars((string) $projectHero['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="service-detail-intro"><?php echo htmlspecialchars((string) $projectHero['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </section>

        <section class="service-detail-section service-detail-overview" aria-labelledby="service-overview-title">
            <div class="site-shell-wide service-detail-two-column">
                <div>
                    <span class="service-detail-kicker">Enfoque</span>
                    <h2 id="service-overview-title" class="service-detail-heading">Un proyecto preciso ordena la obra antes de empezar.</h2>
                </div>
                <div class="service-detail-copy">
                    <p>Trabajamos desde el diagnóstico, la definición del programa y el anteproyecto hasta la documentación ejecutiva necesaria para cotizar, tramitar y construir.</p>
                    <p>La plantilla de esta página está preparada para replicarse en los demás servicios: hero, enfoque, alcances, proceso y llamado a consulta.</p>
                </div>
            </div>
        </section>

        <section class="service-detail-section service-detail-scope" aria-labelledby="service-scope-title">
            <div class="site-shell-wide">
                <div class="service-detail-section-header">
                    <span class="service-detail-kicker">Alcance</span>
                    <h2 id="service-scope-title" class="service-detail-heading">Qué incluye</h2>
                </div>

                <div class="service-detail-card-grid">
                    <article class="service-detail-card">
                        <span>01</span>
                        <h3>Relevamiento y programa</h3>
                        <p>Análisis del sitio, necesidades, condicionantes normativos y objetivos del proyecto.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>02</span>
                        <h3>Anteproyecto</h3>
                        <p>Organización espacial, criterios de implantación, lenguaje material y definición preliminar.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>03</span>
                        <h3>Proyecto ejecutivo</h3>
                        <p>Planos, detalles, documentación técnica y coordinación para presupuestar y ejecutar.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>04</span>
                        <h3>Coordinación técnica</h3>
                        <p>Articulación entre arquitectura, estructura, instalaciones, proveedores y criterios de obra.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="service-detail-section service-detail-process" aria-labelledby="service-process-title">
            <div class="site-shell-wide service-detail-process-grid">
                <div class="service-detail-section-header">
                    <span class="service-detail-kicker">Proceso</span>
                    <h2 id="service-process-title" class="service-detail-heading">De la idea a una documentación construible.</h2>
                </div>
                <ol class="service-detail-steps">
                    <li>
                        <span>01</span>
                        <p>Diagnóstico inicial, objetivos, medidas, restricciones y estrategia de proyecto.</p>
                    </li>
                    <li>
                        <span>02</span>
                        <p>Desarrollo de anteproyecto, revisiones y definición del camino técnico.</p>
                    </li>
                    <li>
                        <span>03</span>
                        <p>Documentación ejecutiva, legajos, detalles y coordinación interdisciplinaria.</p>
                    </li>
                    <li>
                        <span>04</span>
                        <p>Acompañamiento para cotización, trámites y transición hacia la etapa de obra.</p>
                    </li>
                </ol>
            </div>
        </section>

        <section class="service-detail-cta-section" aria-labelledby="service-cta-title">
            <div class="site-shell-wide service-detail-cta">
                <div>
                    <span class="service-detail-kicker">Consulta</span>
                    <h2 id="service-cta-title" class="service-detail-cta-title">Hablemos de tu proyecto.</h2>
                </div>
                <a href="<?php echo htmlspecialchars(commar_url('contacto.php?asunto=Proyecto'), ENT_QUOTES, 'UTF-8'); ?>" class="service-detail-cta-link">Escribir al estudio</a>
            </div>
        </section>
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260724-header-contrast" defer></script>
</body>
</html>
