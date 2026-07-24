<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/team.php';
require_once __DIR__ . '/includes/page-heroes.php';
$aboutHero = commar_page_hero('el_estudio');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';
    $seo = [
        'title' => 'El Estudio',
        'description' => 'COMMAR GROUP es una compañía multidisciplinaria enfocada en arquitectura, construcción, medio ambiente y gestión integral de proyectos.',
        'path' => 'el-estudio.php',
        'image' => 'img/romina-lo-conte.jpg',
        'image_alt' => 'Retrato editorial de la fundadora de COMMAR GROUP',
        'image_width' => 797,
        'image_height' => 1200,
        'og_type' => 'article',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'El Estudio | COMMAR GROUP',
                'url' => commar_absolute_url('el-estudio.php'),
                'description' => 'Compañía multidisciplinaria enfocada en arquitectura, construcción, medio ambiente y gestión integral de proyectos.',
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => 'COMMAR GROUP',
                    'url' => commar_absolute_url('index.php'),
                ],
                'about' => [
                    '@type' => 'Organization',
                    'name' => 'COMMAR GROUP',
                    'url' => commar_absolute_url('index.php'),
                ],
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
                        'name' => 'El Estudio',
                        'item' => commar_absolute_url('el-estudio.php'),
                    ],
                ],
            ],
        ],
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" as="image" href="<?php echo htmlspecialchars((string) $aboutHero['image'], ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260508-1">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'home';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <section class="about-hero" aria-labelledby="about-hero-title">
            <div class="about-hero-media" aria-hidden="true">
                <img src="<?php echo htmlspecialchars((string) $aboutHero['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $aboutHero['width']; ?>" height="<?php echo (int) $aboutHero['height']; ?>" fetchpriority="high" decoding="async" class="about-hero-image">
                <div class="about-hero-overlay"></div>
            </div>
            <div class="site-shell-wide about-hero-content">
                <span class="about-kicker"><?php echo htmlspecialchars((string) $aboutHero['kicker'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h1 id="about-hero-title" class="about-hero-title"><?php echo htmlspecialchars((string) $aboutHero['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="about-hero-intro"><?php echo htmlspecialchars((string) $aboutHero['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </section>

        <section class="about-section about-identity" aria-labelledby="about-identity-title">
            <div class="site-shell-wide about-two-column about-identity-stack">
                <div>
                    <span class="about-kicker">Commar Group</span>
                    <h2 id="about-identity-title" class="about-heading">Un equipo multidisciplinario para resolver necesidades concretas.</h2>
                </div>
                <div class="about-copy">
                    <p>Commar es un grupo multidisciplinario, y nuestro compromiso es enfocarnos en solucionar tu necesidad, para que puedas seguir adelante y crecer.</p>
                    <p>La sinergia con las otras empresas del Grupo Commar permite contar con personal multidisciplinario y tener acceso a tecnologías avanzadas para la resolución de problemáticas proyectuales, constructivas y ambientales.</p>
                </div>
            </div>
        </section>

        <section class="about-section about-team" aria-labelledby="about-team-title">
            <div class="site-shell-wide">
                <div class="about-section-header">
                    <span class="about-kicker">Equipo</span>
                    <h2 id="about-team-title" class="about-heading">Profesionales que integran el estudio</h2>
                    <p class="about-section-intro">Espacio preparado para presentar a los profesionales del equipo, sus roles, áreas de especialidad y trayectoria dentro de Commar Group.</p>
                </div>

                <?php $teamMembers = commar_team_members(); ?>
                <div class="about-team-grid">
                    <?php foreach ($teamMembers as $member): ?>
                        <article class="about-team-card">
                            <img src="<?php echo htmlspecialchars((string) $member['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($member['name'] . ' - ' . $member['role'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) $member['width']; ?>" height="<?php echo (int) $member['height']; ?>" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="about-team-image">
                            <div class="about-team-copy">
                                <div>
                                    <h3><?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p><?php echo htmlspecialchars($member['role'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <?php if ($member['linkedin'] !== '#'): ?>
                                    <a href="<?php echo htmlspecialchars($member['linkedin'], ENT_QUOTES, 'UTF-8'); ?>" class="about-team-linkedin" aria-label="<?php echo htmlspecialchars('LinkedIn de ' . $member['name'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
                                        <span class="sr-only">LinkedIn</span>
                                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M6.94 8.98H3.76V20h3.18V8.98ZM5.35 4a1.84 1.84 0 1 0 0 3.68A1.84 1.84 0 0 0 5.35 4Zm6.67 4.98H8.98V20h3.04v-5.78c0-1.52.29-2.99 2.17-2.99 1.86 0 1.88 1.74 1.88 3.09V20h3.05v-6.41c0-3.15-.68-5.57-4.36-5.57-1.77 0-2.96.97-3.45 1.89h-.04V8.98Z"></path>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="about-section about-history" aria-labelledby="about-history-title">
            <div class="site-shell-wide about-history-grid">
                <div class="about-founder-card" data-history-images-reveal>
                    <img src="img/reunion.jpg" alt="Reunión de trabajo del equipo de COMMAR GROUP" width="2000" height="1333" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="about-founder-image">
                    <img src="img/reunion2.jpg" alt="Segunda reunión de trabajo del equipo de COMMAR GROUP" width="2000" height="1333" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="about-founder-image">
                </div>

                <div class="about-history-content">
                    <span class="about-kicker">Trayectoria</span>
                    <h2 id="about-history-title" class="about-heading">De la gestión municipal a un grupo integral.</h2>
                    <div class="about-copy">
                        <p>La historia de COMMAR GROUP nace en 2002, cuando Romina Lo Conte comenzó a abrirse camino en un territorio donde cada expediente, cada permiso y cada decisión técnica exigían precisión, criterio y una mirada capaz de anticiparse a los obstáculos. Desde la gestión municipal ante el Gobierno de la Ciudad de Buenos Aires, entendió que detrás de cada trámite había un proyecto esperando avanzar.</p>
                        <p>Con esa convicción, transformó la experiencia administrativa en una forma de acompañamiento estratégico. Lo que empezó como una respuesta cercana para clientes de propiedad horizontal fue creciendo hasta convertirse en una estructura profesional capaz de ordenar procesos, destrabar complejidades y darle dirección concreta a obras cada vez más ambiciosas.</p>
                        <p>Su recorrido académico y docente en la Universidad de Buenos Aires fortaleció esa visión. La enseñanza de estructuras primero, y luego el desarrollo en instalaciones, consolidaron una manera de trabajar donde la técnica no es un dato aislado, sino una herramienta para construir con responsabilidad, coordinación y futuro.</p>
                        <p>En 2010, Romina Lo Conte dio forma a COMMAR GROUP: un grupo integral nacido para reunir arquitectura, construcción, gestión técnica y medio ambiente bajo una misma lógica de trabajo. Una evolución natural para una trayectoria marcada por la decisión de convertir problemas complejos en soluciones posibles.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section about-services" aria-labelledby="about-services-title">
            <div class="site-shell-wide">
                <div class="about-section-header">
                    <span class="about-kicker">Divisiones</span>
                    <h2 id="about-services-title" class="about-heading">Servicios integrados</h2>
                </div>

                <div class="about-services-grid">
                    <article class="about-service-panel">
                        <span>01</span>
                        <h3>Servicios de Arquitectura</h3>
                        <p>ARQCONSULT es la división de Commar que cuenta con un staff de profesionales en grado de brindar soluciones integradoras para el desarrollo de proyectos habitacionales, comerciales e industriales.</p>
                        <p>Acompañamos desde el estudio de factibilidad y presupuesto hasta la dirección de obra, documentación y habilitaciones correspondientes.</p>
                    </article>
                    <article class="about-service-panel">
                        <span>02</span>
                        <h3>Servicios de Construcción</h3>
                        <p>Contamos con más de 60 personas en todos los rubros de obra, realizando demoliciones, obra nueva, remodelaciones y ampliaciones.</p>
                        <p>Tenemos amplia experiencia en garages comerciales, oficinas y depósitos industriales, con sistemas racionalizados livianos y hormigón armado.</p>
                    </article>
                    <article class="about-service-panel">
                        <span>03</span>
                        <h3>Servicios de Medio Ambiente</h3>
                        <p>Brindamos servicios de monitoreo y desarrollamos proyectos llave en mano para sistemas de muestreo y análisis continuo de efluentes gaseosos, líquidos, fluidos de proceso y mitigación de olores.</p>
                        <p>También trabajamos en el desarrollo de cabinas y redes de monitoreo de calidad del aire.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="about-section about-environment" aria-labelledby="about-environment-title">
            <div class="site-shell-wide about-environment-stack">
                <div>
                    <span class="about-kicker">Medio ambiente</span>
                    <h2 id="about-environment-title" class="about-heading">Experiencia técnica en control ambiental.</h2>
                </div>
                <div class="about-copy">
                    <p>Fuimos filial del Grupo SOFINTER de Italia, la consultora ambiental de mayor experiencia local en el control de emisiones y calidad del aire, hasta el año 2010, cuando se independizó formando parte de Commar Group.</p>
                    <p>Ese recorrido consolidó una forma de trabajo basada en precisión técnica, tecnología aplicada y equipos interdisciplinarios preparados para resolver desafíos ambientales complejos.</p>
                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
