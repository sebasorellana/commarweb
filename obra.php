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

    if (!$project && $slug !== '') {
        $legacyProject = commar_project_by_legacy_slug($slug);
        if ($legacyProject) {
            header('Location: ' . commar_absolute_url(commar_work_url($legacyProject['slug'])), true, 301);
            exit;
        }
    }

    if (!$project) {
        http_response_code(404);
        $seo = [
            'title' => 'Obra no encontrada',
            'description' => 'La obra solicitada no está disponible en COMMAR GROUP.',
            'path' => 'obra.php',
            'robots' => 'noindex, follow',
        ];
    } else {
        if (strpos((string) ($_SERVER['REQUEST_URI'] ?? ''), 'obra.php?slug=') !== false) {
            header('Location: ' . commar_url(commar_work_url($project['slug'])), true, 301);
            exit;
        }

        $otherProjects = array_values(array_filter(
            commar_projects(),
            static fn (array $item): bool => $item['slug'] !== $project['slug']
        ));

        $seo = [
            'title' => $project['title'],
            'description' => $project['summary'],
            'path' => commar_work_url($project['slug']),
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
                    'url' => commar_absolute_url(commar_work_url($project['slug'])),
                    'creator' => [
                        '@type' => 'Organization',
                        'name' => 'COMMAR GROUP',
                    ],
                ],
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => commar_absolute_url('')],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Obras', 'item' => commar_absolute_url('obras.php')],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $project['title'], 'item' => commar_absolute_url(commar_work_url($project['slug']))],
                    ],
                ],
            ],
        ];
    }

    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" href="fonts/inter-latin-var.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="style.css?v=20260930-works-grid2">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'home';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <?php if (!$project): ?>
            <section class="project-detail-empty">
                <div class="site-shell">
                    <span class="project-detail-kicker">Error 404</span>
                    <h1 class="project-detail-empty-title">La obra solicitada no está disponible.</h1>
                    <a href="<?php echo htmlspecialchars(commar_url('obras.php'), ENT_QUOTES, 'UTF-8'); ?>" class="studio-overview-link">Volver a obras</a>
                </div>
            </section>
        <?php else: ?>
            <section class="project-detail-hero" aria-labelledby="project-detail-title">
                <div class="project-detail-hero-media">
                    <img src="<?php echo htmlspecialchars($project['img'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo commar_image_srcset_attrs((string) $project['img'], (int) ($project['img_width'] ?: 2000)); ?> alt="<?php echo htmlspecialchars($project['hero_alt'], ENT_QUOTES, 'UTF-8'); ?>" width="2000" height="1333" fetchpriority="high" decoding="async" class="project-detail-hero-image">
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

            <?php
            $projectGallery = array_values(array_filter(
                $project['gallery'] ?? [],
                static fn ($item): bool => is_array($item) && trim((string) ($item['path'] ?? '')) !== ''
            ));
            ?>
            <?php if (count($projectGallery) > 1): ?>
                <section class="project-gallery-section" aria-labelledby="project-gallery-title">
                    <div class="site-shell-wide">
                        <div class="project-gallery-header">
                            <span class="project-detail-kicker">Galería</span>
                            <h2 id="project-gallery-title" class="project-gallery-title"><?php echo count($projectGallery); ?> <?php echo count($projectGallery) === 1 ? 'imagen' : 'imágenes'; ?></h2>
                        </div>

                        <ul class="project-gallery-grid" data-project-gallery>
                            <?php foreach ($projectGallery as $galleryIndex => $galleryItem): ?>
                                <?php
                                $galleryPath = (string) $galleryItem['path'];
                                $galleryAlt = trim((string) ($galleryItem['alt'] ?? '')) ?: $project['title'];
                                $galleryWidth = (int) ($galleryItem['width'] ?? 0) ?: 1400;
                                $galleryHeight = (int) ($galleryItem['height'] ?? 0) ?: 933;
                                ?>
                                <li class="project-gallery-item<?php echo $galleryWidth < $galleryHeight ? ' is-portrait' : ''; ?>">
                                    <button type="button" class="project-gallery-button" data-project-gallery-item data-src="<?php echo htmlspecialchars($galleryPath, ENT_QUOTES, 'UTF-8'); ?>" data-alt="<?php echo htmlspecialchars($galleryAlt, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Ampliar imagen <?php echo $galleryIndex + 1; ?> de <?php echo count($projectGallery); ?>">
                                        <img src="<?php echo htmlspecialchars($galleryPath, ENT_QUOTES, 'UTF-8'); ?>"<?php echo commar_image_srcset_attrs($galleryPath, $galleryWidth, '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw'); ?> alt="<?php echo htmlspecialchars($galleryAlt, ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo $galleryWidth; ?>" height="<?php echo $galleryHeight; ?>" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async">
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="project-lightbox" data-project-lightbox hidden role="dialog" aria-modal="true" aria-label="Galería de <?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="button" class="project-lightbox-close" data-project-lightbox-close aria-label="Cerrar galería">&times;</button>
                        <?php if (count($projectGallery) > 1): ?>
                            <button type="button" class="project-lightbox-nav is-prev" data-project-lightbox-prev aria-label="Imagen anterior">&lsaquo;</button>
                            <button type="button" class="project-lightbox-nav is-next" data-project-lightbox-next aria-label="Imagen siguiente">&rsaquo;</button>
                        <?php endif; ?>
                        <figure class="project-lightbox-figure">
                            <img src="" alt="" class="project-lightbox-image" data-project-lightbox-image>
                            <figcaption class="project-lightbox-caption" data-project-lightbox-caption></figcaption>
                        </figure>
                    </div>
                </section>
            <?php endif; ?>

            <section class="project-detail-cta-section">
                <div class="site-shell-wide project-detail-cta">
                    <a href="<?php echo htmlspecialchars(commar_url('obras.php'), ENT_QUOTES, 'UTF-8'); ?>" class="studio-overview-link">Volver a obras</a>
                    <a href="<?php echo htmlspecialchars(commar_url('contacto.php?asunto=Consulta'), ENT_QUOTES, 'UTF-8'); ?>" class="projects-showcase-link">Consultar por este proyecto</a>
                </div>
            </section>

            <?php if (!empty($otherProjects)): ?>
                <section class="project-detail-related" aria-labelledby="project-detail-related-title">
                    <div class="site-shell-wide">
                        <div class="project-detail-related-header">
                            <span class="project-detail-kicker">Otras obras</span>
                            <h2 id="project-detail-related-title" class="project-detail-related-title">Seguí explorando otros proyectos del estudio.</h2>
                        </div>

                        <ul class="works-grid works-grid-related">
                            <?php foreach (array_slice($otherProjects, 0, 3) as $relatedProject): ?>
                                <?php
                                $relatedMeta = array_values(array_filter([
                                    trim((string) ($relatedProject['location'] ?? '')),
                                    trim((string) ($relatedProject['year'] ?? '')),
                                ]));
                                ?>
                                <li>
                                    <a href="<?php echo htmlspecialchars(commar_url(commar_work_url($relatedProject['slug'])), ENT_QUOTES, 'UTF-8'); ?>" class="works-card">
                                        <div class="works-card-media">
                                            <img src="<?php echo htmlspecialchars($relatedProject['img'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo commar_image_srcset_attrs((string) $relatedProject['img'], (int) ($relatedProject['img_width'] ?: 1400), '(min-width: 1024px) 30vw, (min-width: 640px) 48vw, 100vw'); ?> alt="<?php echo htmlspecialchars($relatedProject['hero_alt'] ?: $relatedProject['title'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) ($relatedProject['img_width'] ?: 1400); ?>" height="<?php echo (int) ($relatedProject['img_height'] ?: 933); ?>" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async" class="works-card-image">
                                            <?php if (trim((string) $relatedProject['category']) !== ''): ?>
                                                <span class="works-card-badge"><?php echo htmlspecialchars($relatedProject['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="works-card-copy">
                                            <h3 class="works-card-title"><?php echo htmlspecialchars($relatedProject['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                            <?php if (!empty($relatedMeta)): ?>
                                                <p class="works-card-meta"><?php echo htmlspecialchars(implode(' · ', $relatedMeta), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                            <span class="works-card-cta" aria-hidden="true">Ver obra</span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260930-works-grid2" defer></script>
</body>
</html>
