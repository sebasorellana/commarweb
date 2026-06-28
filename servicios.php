<?php require_once __DIR__ . '/includes/site.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    $services = [
        [
            'index' => '01',
            'title' => 'Proyecto',
            'description' => 'Anteproyecto, proyecto ejecutivo, documentación técnica, detalles constructivos y coordinación de decisiones para llegar a obra con una base clara.',
            'href' => 'servicio-proyectos.php',
            'image' => 'img/proyecto-01.jpg',
            'image_width' => 1400,
            'image_height' => 933,
        ],
        [
            'index' => '02',
            'title' => 'Gerenciamiento',
            'description' => 'Planificación, coordinación de equipos, seguimiento de avances, control de costos y orden operativo para sostener el proceso de obra.',
            'href' => 'contacto.php?asunto=Gerenciamiento',
            'image' => 'img/reunion.jpg',
            'image_width' => 2000,
            'image_height' => 1333,
        ],
        [
            'index' => '03',
            'title' => 'Demolición',
            'description' => 'Permisos, planificación técnica, seguridad operativa y coordinación de trabajos de demolición con criterio documental y de campo.',
            'href' => 'contacto.php?asunto=Demolición',
            'image' => 'img/proyecto-04.jpg',
            'image_width' => 1400,
            'image_height' => 933,
        ],
        [
            'index' => '04',
            'title' => 'Construcción',
            'description' => 'Ejecución, control de calidad, coordinación de gremios, seguimiento técnico y resolución de obra con foco en tiempo, orden y resultado.',
            'href' => 'contacto.php?asunto=Construcción',
            'image' => 'img/obras/eba-coarco.jpg',
            'image_width' => 1920,
            'image_height' => 976,
        ],
        [
            'index' => '05',
            'title' => 'Habilitaciones',
            'description' => 'Gestión normativa, documentación y acompañamiento técnico para habilitar locales, actividades y espacios comerciales.',
            'href' => 'contacto.php?asunto=Habilitaciones',
            'image' => 'img/obras/alto-palermo',
            'image_width' => 1200,
            'image_height' => 900,
        ],
        [
            'index' => '06',
            'title' => 'Medio ambiente / Seguridad e Higiene',
            'description' => 'Consultoría ambiental, seguridad e higiene, análisis normativo, documentación técnica y acompañamiento preventivo.',
            'href' => 'contacto.php?asunto=Medio ambiente / Seguridad e Higiene',
            'image' => 'img/proyecto-03.jpg',
            'image_width' => 1400,
            'image_height' => 1400,
        ],
    ];

    $seo = [
        'title' => 'Servicios',
        'description' => 'Servicios de COMMAR GROUP: Proyecto, Gerenciamiento, Demolición, Construcción, Habilitaciones y Medio ambiente / Seguridad e Higiene.',
        'path' => 'servicios.php',
        'image' => 'img/proyecto-01.jpg',
        'image_alt' => 'Servicios de arquitectura, construcción y gestión técnica de COMMAR GROUP',
        'image_width' => 1400,
        'image_height' => 933,
        'og_type' => 'website',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => 'Servicios de COMMAR GROUP',
                'itemListElement' => array_map(
                    static fn(array $service, int $index): array => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $service['title'],
                        'url' => commar_absolute_url($service['href']),
                    ],
                    $services,
                    array_keys($services)
                ),
            ],
        ],
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260508-1">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'default';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <section class="services-page-section" aria-labelledby="services-page-title">
            <div class="site-shell-wide">
                <div class="services-page-header">
                    <span class="services-kicker">Servicios</span>
                    <h1 id="services-page-title" class="services-page-title">
                        <span>Soluciones integrales</span>
                        <span>para proyectos que</span>
                        <span>exigen precisión.</span>
                    </h1>
                    <p>Acompañamos cada etapa con dirección técnica, documentación clara y una gestión coordinada entre proyecto, obra, normativa, ambiente y seguridad.</p>
                </div>

                <div class="services-page-grid">
                    <?php foreach ($services as $service): ?>
                        <article class="services-page-card" data-scroll-reveal="up">
                            <a href="<?php echo htmlspecialchars($service['href'], ENT_QUOTES, 'UTF-8'); ?>" class="services-page-card-link" aria-label="<?php echo htmlspecialchars('Abrir servicio ' . $service['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span><?php echo htmlspecialchars($service['index'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </a>
                            <div class="services-page-card-media">
                                <img src="<?php echo htmlspecialchars($service['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) $service['image_width']; ?>" height="<?php echo (int) $service['image_height']; ?>" loading="lazy" decoding="async">
                            </div>
                            <div class="services-page-card-copy">
                                <h2><?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p><?php echo htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <a href="<?php echo htmlspecialchars($service['href'], ENT_QUOTES, 'UTF-8'); ?>">Consultar</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
