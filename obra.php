<?php require_once __DIR__ . '/includes/site.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';
    require_once __DIR__ . '/includes/projects.php';

    $slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
    $project = $slug !== '' ? commar_project_by_slug($slug) : null;
    $otherProjects = [];

    if (!$project) {
        http_response_code(404);
        $seo = [
            'title' => 'Obra no encontrada',
            'description' => 'La obra solicitada no está disponible en COMMAR GROUP.',
            'path' => 'obra.php',
            'robots' => 'noindex, follow',
        ];
    } else {
        $otherProjects = array_values(array_filter(
            commar_projects(),
            static fn (array $item): bool => $item['slug'] !== $project['slug']
        ));

        $seo = [
            'title' => $project['title'],
            'description' => $project['summary'],
            'path' => 'obra.php?slug=' . $project['slug'],
            'image' => $project['img'],
            'image_alt' => $project['hero_alt'],
            'og_type' => 'article',
            'json_ld' => [
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'CreativeWork',
                    'name' => $project['title'],
                    'description' => $project['summary'],
                    'image' => commar_absolute_url($project['img']),
                    'url' => commar_absolute_url('obra.php?slug=' . $project['slug']),
                    'creator' => [
                        '@type' => 'Organization',
                        'name' => 'COMMAR GROUP',
                    ],
                ],
            ],
        ];
    }

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
        <?php if (!$project): ?>
            <section class="project-detail-empty">
                <div class="site-shell">
                    <span class="project-detail-kicker">Error 404</span>
                    <h1 class="project-detail-empty-title">La obra solicitada no está disponible.</h1>
                    <a href="obras.php" class="studio-overview-link">Volver a obras</a>
                </div>
            </section>
        <?php else: ?>
            <section class="project-detail-hero" aria-labelledby="project-detail-title">
                <div class="project-detail-hero-media">
                    <img src="<?php echo htmlspecialchars($project['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($project['hero_alt'], ENT_QUOTES, 'UTF-8'); ?>" width="2000" height="1333" fetchpriority="high" decoding="async" class="project-detail-hero-image">
                    <div class="project-detail-hero-overlay"></div>
                </div>

                <div class="site-shell-wide project-detail-hero-content">
                    <span class="project-detail-kicker"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($project['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <h1 id="project-detail-title" class="project-detail-title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="project-detail-intro"><?php echo htmlspecialchars($project['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </section>

            <section class="project-detail-body">
                <div class="site-shell-wide project-detail-grid">
                    <div class="project-detail-copy">
                        <span class="project-detail-kicker">Concepto</span>
                        <?php foreach ($project['description'] as $paragraph): ?>
                            <p><?php echo htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endforeach; ?>
                    </div>

                    <aside class="project-detail-meta">
                        <span class="project-detail-kicker">Ficha técnica</span>
                        <dl class="project-detail-metrics">
                            <div>
                                <dt>Año</dt>
                                <dd><?php echo htmlspecialchars($project['year'], ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <?php foreach ($project['metrics'] as $label => $value): ?>
                                <div>
                                    <dt><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></dt>
                                    <dd><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    </aside>
                </div>
            </section>

            <section class="project-detail-cta-section">
                <div class="site-shell-wide project-detail-cta">
                    <a href="obras.php" class="studio-overview-link">Volver a obras</a>
                    <a href="contacto.php?asunto=Consulta" class="projects-showcase-link">Consultar por este proyecto</a>
                </div>
            </section>

            <?php if (!empty($otherProjects)): ?>
                <section class="project-detail-related" aria-labelledby="project-detail-related-title">
                    <div class="site-shell-wide">
                        <div class="project-detail-related-header">
                            <span class="project-detail-kicker">Otras obras</span>
                            <h2 id="project-detail-related-title" class="project-detail-related-title">Seguí explorando otros proyectos del estudio.</h2>
                        </div>

                        <div class="project-detail-related-grid">
                            <?php foreach (array_slice($otherProjects, 0, 3) as $relatedProject): ?>
                                <article class="project-item project-card">
                                    <div class="project-card-media img-reveal-container">
                                        <img src="<?php echo htmlspecialchars($relatedProject['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($relatedProject['title'] . ' - ' . $relatedProject['category'], ENT_QUOTES, 'UTF-8'); ?>" width="2000" height="1333" loading="lazy" decoding="async" class="project-card-image">
                                        <div class="project-card-badge">
                                            <?php echo htmlspecialchars($relatedProject['id'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($relatedProject['category'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </div>

                                    <div class="project-card-copy">
                                        <div class="project-card-meta">
                                            <span><?php echo htmlspecialchars($relatedProject['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span><?php echo htmlspecialchars($relatedProject['year'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <h3 class="project-card-title"><?php echo htmlspecialchars($relatedProject['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <p class="project-card-summary"><?php echo htmlspecialchars($relatedProject['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <a href="<?php echo htmlspecialchars('obra.php?slug=' . $relatedProject['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="project-card-cta">
                                            <div class="project-card-cta-line"></div>
                                            <span class="project-card-cta-text">Explorar proyecto</span>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
