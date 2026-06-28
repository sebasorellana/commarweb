<?php require_once __DIR__ . '/includes/site.php'; ?>
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
    <link rel="preload" as="image" href="img/fullteam.jpg" fetchpriority="high">
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
        <section class="about-hero" aria-labelledby="about-hero-title">
            <div class="about-hero-media" aria-hidden="true">
                <img src="img/fullteam.jpg" alt="" width="3020" height="1480" fetchpriority="high" decoding="async" class="about-hero-image">
                <div class="about-hero-overlay"></div>
            </div>
            <div class="site-shell-wide about-hero-content">
                <span class="about-kicker">El estudio</span>
                <h1 id="about-hero-title" class="about-hero-title">Lo que somos</h1>
                <p class="about-hero-intro">Somos una compañía formada por profesionales de distintas extracciones, que componen un grupo de trabajo multidisciplinario, enfocados en el compromiso, el profesionalismo y la pasión por los métodos y las formas.</p>
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

        <section class="about-section about-history" aria-labelledby="about-history-title">
            <div class="site-shell-wide about-history-grid">
                <div class="about-founder-card" data-history-images-reveal>
                    <img src="img/reunion.jpg" alt="Reunión de trabajo del equipo de COMMAR GROUP" width="2000" height="1333" loading="lazy" decoding="async" class="about-founder-image">
                    <img src="img/reunion2.jpg" alt="Segunda reunión de trabajo del equipo de COMMAR GROUP" width="2000" height="1333" loading="lazy" decoding="async" class="about-founder-image">
                </div>

                <div class="about-history-content">
                    <span class="about-kicker">Trayectoria</span>
                    <h2 id="about-history-title" class="about-heading">De la gestión municipal a un grupo integral.</h2>
                    <div class="about-copy">
                        <p>Año 2002, mientras comenzaba mi carrera profesional, incursionando con tramitaciones municipales ante el gobierno de la Ciudad Autónoma de Buenos Aires. Me especialicé en el acompañamiento personal de mis clientes con la gestión y optimización de sus proyectos de propiedad horizontal.</p>
                        <p>Afianzándose mi situación profesional, en 2003 comenzaba mi carrera docente en la UBA en la materia Estructuras, hasta el 2007, año en el que fui incorporando profesionales a mi estudio para dar respuesta a las crecientes demandas. Abrimos juego para resolver todo tipo de obras: demoliciones, construcciones y ejecución llave en mano de edificios de mediana envergadura.</p>
                        <p>Ya por el 2009 mi carrera docente se orientó a la tecnicatura en Instalaciones. Hoy ejerzo el cargo de Profesor Adjunto en Instalaciones II, cátedra Ing. Roscardi. Esto me impulsó a trabajar en el desarrollo de proyectos de instalaciones sanitarias, eléctricas, térmicas y sistemas de prevención contra incendio, propias y para terceros.</p>
                        <p>En el año 2010 creo COMMAR GROUP, con el fin de unificar la incorporación de Commissioning Argentina S.A., dedicada al medio ambiente, brindando servicios de monitoreo de calidad de aire, análisis de afluentes gaseosos, líquidos y ruidos.</p>
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

        <section class="about-section about-team" aria-labelledby="about-team-title">
            <div class="site-shell-wide">
                <div class="about-section-header">
                    <span class="about-kicker">Equipo</span>
                    <h2 id="about-team-title" class="about-heading">Profesionales que integran el estudio</h2>
                    <p class="about-section-intro">Espacio preparado para presentar a los profesionales del equipo, sus roles, áreas de especialidad y trayectoria dentro de Commar Group.</p>
                </div>

                <?php
                $teamMembers = [
                    ['image' => 'img/romina-loconte.jpg', 'width' => 800, 'height' => 800, 'name' => 'Romina', 'surname' => 'Lo Conte', 'position' => 'Presidente / CEO', 'linkedin' => '#'],
                    ['image' => 'img/julian-parente.jpg', 'width' => 800, 'height' => 800, 'name' => 'Julian', 'surname' => 'Parente', 'position' => 'Vicepresidente / Representante técnico', 'linkedin' => '#'],
                    ['image' => 'img/belen-gomez.jpg', 'width' => 800, 'height' => 765, 'name' => 'Belén', 'surname' => 'Gomez', 'position' => 'Gerente de Obras Nuevas y Habilitaciones', 'linkedin' => '#'],
                    ['image' => 'img/geronimo-zoloaga.jpg', 'width' => 800, 'height' => 757, 'name' => 'Gerónimo', 'surname' => 'Zoloaga', 'position' => 'Gerente de Ajustes y Finales', 'linkedin' => '#'],
                    ['image' => 'img/juan-pugliese.jpg', 'width' => 800, 'height' => 775, 'name' => 'Juan P', 'surname' => 'Pugliese', 'position' => 'Analista técnico', 'linkedin' => '#'],
                    ['image' => 'img/agustina-freire.jpg', 'width' => 800, 'height' => 771, 'name' => 'Agustina', 'surname' => 'Freire', 'position' => 'Jefatura de obra', 'linkedin' => '#'],
                    ['image' => 'img/valentin-lobaccaro.jpg', 'width' => 776, 'height' => 775, 'name' => 'Valentin', 'surname' => 'Lobaccaro', 'position' => 'Analista técnico', 'linkedin' => '#'],
                    ['image' => 'img/agustina-futej.jpg', 'width' => 780, 'height' => 775, 'name' => 'Agustina', 'surname' => 'Futej', 'position' => 'Analista técnico', 'linkedin' => '#'],
                    ['image' => 'img/claudia-gatica.jpg', 'width' => 800, 'height' => 780, 'name' => 'Claudia', 'surname' => 'Gatica', 'position' => 'Analista técnico', 'linkedin' => '#'],
                    ['image' => 'img/kiara-battaglia.jpg', 'width' => 800, 'height' => 773, 'name' => 'Kiara', 'surname' => 'Bataglia', 'position' => 'Gerente administrativa', 'linkedin' => '#'],
                    ['image' => 'img/carina-gatica.jpg', 'width' => 800, 'height' => 800, 'name' => 'Carina', 'surname' => 'Gatica', 'position' => 'Gerente RRHH', 'linkedin' => '#'],
                    ['image' => 'img/nuria-zoloaga.jpg', 'width' => 727, 'height' => 777, 'name' => 'Nuria', 'surname' => 'Zoloaga', 'position' => 'Asistente administrativo', 'linkedin' => '#'],
                    ['image' => 'img/camila-lamuta.jpg', 'width' => 800, 'height' => 779, 'name' => 'Camila', 'surname' => 'Lamuta', 'position' => 'Dra. Arquitectura legal', 'linkedin' => '#'],
                ];
                ?>
                <div class="about-team-grid">
                    <?php foreach ($teamMembers as $member): ?>
                        <article class="about-team-card">
                            <img src="<?php echo htmlspecialchars($member['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($member['name'] . ' ' . $member['surname'] . ' - ' . $member['position'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) $member['width']; ?>" height="<?php echo (int) $member['height']; ?>" loading="lazy" decoding="async" class="about-team-image">
                            <div class="about-team-copy">
                                <div>
                                    <h3><?php echo htmlspecialchars($member['name'] . ' ' . $member['surname'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p><?php echo htmlspecialchars($member['position'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <?php if ($member['linkedin'] !== '#'): ?>
                                    <a href="<?php echo htmlspecialchars($member['linkedin'], ENT_QUOTES, 'UTF-8'); ?>" class="about-team-linkedin" aria-label="<?php echo htmlspecialchars('LinkedIn de ' . $member['name'] . ' ' . $member['surname'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">
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
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
