<?php require_once __DIR__ . '/includes/site.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';

    $seo = [
        'title' => 'Obra Viva',
        'description' => 'Obra Viva es la solución 360° de COMMAR GROUP para la gestión técnico-administrativa de obras en CABA.',
        'path' => 'obra-viva.php',
        'image' => 'img/obras/eba-coarco.jpg',
        'image_alt' => 'Gestión técnico-administrativa de obras en CABA',
        'image_width' => 1920,
        'image_height' => 976,
        'og_type' => 'article',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => 'Obra Viva',
                'provider' => [
                    '@type' => 'Organization',
                    'name' => 'COMMAR GROUP',
                    'url' => commar_absolute_url(''),
                ],
                'areaServed' => 'Ciudad Autónoma de Buenos Aires',
                'serviceType' => 'Gestión técnico-administrativa de obras',
                'description' => 'Solución 360° para trámites, documentación y cumplimiento normativo de obras en CABA.',
                'url' => commar_absolute_url('obra-viva.php'),
            ],
        ],
    ];
    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" as="image" href="img/obras/eba-coarco.jpg" fetchpriority="high">
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
        <section class="service-detail-hero obra-viva-hero" aria-labelledby="obra-viva-title">
            <div class="service-detail-hero-media" aria-hidden="true">
                <img src="img/obras/eba-coarco.jpg" alt="" width="1920" height="976" fetchpriority="high" decoding="async" class="service-detail-hero-image">
                <div class="service-detail-hero-overlay"></div>
            </div>

            <div class="site-shell-wide service-detail-hero-content">
                <span class="service-detail-kicker">Solución 360° para obras</span>
                <h1 id="obra-viva-title" class="service-detail-title">Obra Viva</h1>
                <p class="service-detail-intro">Gestión técnico-administrativa de obras en CABA. Integramos trámites, documentación y cumplimiento normativo para que tu obra avance sin interrupciones.</p>
            </div>
        </section>

        <section class="service-detail-section" aria-labelledby="obra-viva-what-title">
            <div class="site-shell-wide obra-viva-what-layout">
                <div class="obra-viva-what-heading">
                    <img src="img/obra-viva-logo1.png" alt="Obra Viva" width="835" height="729" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="obra-viva-section-logo">
                    <div>
                        <span class="service-detail-kicker">¿Qué es Obra Viva?</span>
                        <h2 id="obra-viva-what-title" class="service-detail-heading">El backbone administrativo de la obra.</h2>
                    </div>
                </div>
                <div class="service-detail-copy">
                    <p>Obra Viva es el área administrativa externa de oficinas técnicas de empresas constructoras, estudios de arquitectura y profesionales independientes.</p>
                    <p>Nos integramos como la oficina administrativa de la obra, asegurando la gestión y el cumplimiento en cada etapa.</p>
                    <p>Integra la gestión técnica y administrativa de obra a través de un sistema propio que garantiza continuidad, cumplimiento y eficiencia.</p>
                </div>
            </div>
        </section>

        <section class="service-detail-section service-detail-scope" aria-labelledby="obra-viva-offer-title">
            <div class="site-shell-wide">
                <div class="service-detail-section-header">
                    <span class="service-detail-kicker">Lo que ofrecemos</span>
                    <h2 id="obra-viva-offer-title" class="service-detail-heading">Gestión integral para sostener el avance de obra.</h2>
                </div>

                <div class="service-detail-card-grid obra-viva-card-grid">
                    <article class="service-detail-card">
                        <span>01</span>
                        <h3>Gestión integral</h3>
                        <p>Acompañamos todas las instancias administrativas, desde el inicio hasta la finalización de obra, asegurando cumplimiento normativo vigente.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>02</span>
                        <h3>Trámites y normativa</h3>
                        <p>Gestionamos ante organismos de control para evitar demoras, observaciones y sanciones que afecten el desarrollo de la obra.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>03</span>
                        <h3>Documentación de obra</h3>
                        <p>Carga, seguimiento y control de documentación técnica y administrativa requerida en cada etapa del proyecto.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>04</span>
                        <h3>Arquitectura legal</h3>
                        <p>Asesoramiento técnico-normativo y respuesta a actas, intimaciones y requerimientos administrativos.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>05</span>
                        <h3>Asistencia continua</h3>
                        <p>Acompañamiento a estudios, profesionales y empresas en organización y control documental, incluyendo seguridad e higiene.</p>
                    </article>
                    <article class="service-detail-card">
                        <span>06</span>
                        <h3>Seguimiento en TAD</h3>
                        <p>Coordinamos trámites de demolición, excavación, obra civil y avisos de obra, garantizando presentación y avance.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="service-detail-section service-detail-process" aria-labelledby="obra-viva-why-title">
            <div class="site-shell-wide service-detail-process-grid">
                <div class="service-detail-section-header">
                    <span class="service-detail-kicker">¿Por qué Obra Viva?</span>
                    <h2 id="obra-viva-why-title" class="service-detail-heading">Continuidad, control y normativa CABA.</h2>
                </div>
                <ul class="obra-viva-benefits">
                    <li>Evitás demoras y sanciones.</li>
                    <li>Delegás la gestión administrativa.</li>
                    <li>Asegurás continuidad en tus obras.</li>
                    <li>Trabajás con especialistas en normativa de CABA.</li>
                </ul>
            </div>
        </section>

        <section class="service-detail-section obra-viva-lines" aria-labelledby="obra-viva-lines-title">
            <div class="site-shell-wide">
                <div class="service-detail-section-header">
                    <span class="service-detail-kicker">Servicios</span>
                    <h2 id="obra-viva-lines-title" class="service-detail-heading">Cuatro líneas de trabajo.</h2>
                </div>

                <div class="obra-viva-line-grid" data-obra-viva-timeline>
                    <article class="obra-viva-line">
                        <span>01</span>
                        <h3>Gestión de obras</h3>
                        <ul>
                            <li>Portal DO - Obras.</li>
                            <li>Alta de obra e inicio de demolición / obra civil.</li>
                            <li>Pedidos de inspección: demolición, obra civil, excavación y AVOS 1, 2 y 3.</li>
                            <li>Subsanación de intimaciones SIFER.</li>
                            <li>Desligue profesional o desligue de empresas.</li>
                            <li>Extensión de horario en obra en ejecución.</li>
                        </ul>
                    </article>

                    <article class="obra-viva-line">
                        <span>02</span>
                        <h3>Gestión de empresas</h3>
                        <ul>
                            <li>Registro y renovación de empresa demoledora, excavadora o constructora.</li>
                            <li>Modificación de tipo de empresa.</li>
                            <li>Vinculación y desvinculación de obra.</li>
                            <li>Desligue y ligue de RT principal.</li>
                            <li>Declaración de RT secundarios.</li>
                            <li>Carga de informes digitales de obras.</li>
                        </ul>
                    </article>

                    <article class="obra-viva-line">
                        <span>03</span>
                        <h3>Asesoría en línea</h3>
                        <ul>
                            <li>Asesoría legal.</li>
                            <li>Asesoría técnica.</li>
                            <li>Interpretación de inspecciones, intimaciones y actas de comprobación.</li>
                            <li>Levantamiento de clausuras.</li>
                        </ul>
                    </article>

                    <article class="obra-viva-line">
                        <span>04</span>
                        <h3>Formación profesional</h3>
                        <p>Programa de capacitación en plataformas de gestión de obra, con entrenamiento técnico, práctico y aplicado a situaciones reales.</p>
                        <ul>
                            <li>Uso del Portal Director de Obra.</li>
                            <li>Pedidos de AVOS: requisitos, criterios y documentación necesaria.</li>
                            <li>Subsanaciones en SIFER.</li>
                            <li>Portal de Empresas.</li>
                            <li>RT: carga de informes digitales.</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section class="service-detail-cta-section" aria-labelledby="obra-viva-cta-title">
            <div class="site-shell-wide service-detail-cta">
                <div class="obra-viva-cta-heading">
                    <img src="img/obra-viva-logo2.png" alt="Obra Viva" width="814" height="782" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="obra-viva-cta-logo">
                    <div>
                        <span class="service-detail-kicker">Consulta</span>
                        <h2 id="obra-viva-cta-title" class="service-detail-cta-title">Delegá la gestión administrativa de tu obra.</h2>
                    </div>
                </div>
                <a href="contacto.php?asunto=Obra%20Viva" class="service-detail-cta-link">Consultar por Obra Viva</a>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
