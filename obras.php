<?php require_once __DIR__ . '/includes/site.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/projects.php';

    $projects = commar_projects();
    $categories = array_values(array_unique(array_filter(array_map(
        static fn (array $project): string => trim((string) ($project['category'] ?? '')),
        $projects
    ))));
    natcasesort($categories);
    $categories = array_values($categories);

    $selectedCategory = trim((string) ($_GET['categoria'] ?? ''));
    if ($selectedCategory !== '' && !in_array($selectedCategory, $categories, true)) {
        $selectedCategory = '';
    }

    $filteredProjects = $selectedCategory === ''
        ? $projects
        : array_values(array_filter($projects, static fn (array $project): bool => (string) $project['category'] === $selectedCategory));

    $legacySlug = trim((string) ($_GET['slug'] ?? ''));
    if ($legacySlug !== '' && commar_project_by_slug($legacySlug)) {
        header('Location: ' . commar_url(commar_work_url($legacySlug)), true, 301);
        exit;
    }

    $seo = [
        'title' => 'Obras',
        'description' => 'Obras de COMMAR GROUP: galería de proyectos con imágenes, ficha técnica y detalles de cada obra.',
        'path' => 'obras.php',
        'image' => $filteredProjects[0]['img'] ?? 'img/logo-commar-500.png',
        'image_alt' => 'Obras de COMMAR GROUP',
        'og_type' => 'website',
    ];

    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preload" href="fonts/inter-latin-var.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="style.css?v=20260930-works-grid2">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'default';
    include __DIR__ . '/includes/header.php';
    ?>

    <main>
        <section class="works-directory-section" aria-labelledby="works-directory-title">
            <div class="site-shell-wide">
                <div class="works-directory-heading">
                    <span class="project-detail-kicker">Portfolio</span>
                    <h1 id="works-directory-title" class="works-directory-title">Obras</h1>
                    <p>Explorá las obras de COMMAR GROUP. Ingresá a cada ficha para ver la galería de imágenes y la información del proyecto.</p>
                </div>

                <?php if (empty($projects)): ?>
                    <p class="admin-empty">No hay obras cargadas.</p>
                <?php else: ?>
                    <?php if (count($categories) > 1): ?>
                        <nav class="works-directory-filter" aria-label="Filtro de categorías">
                            <a href="<?php echo htmlspecialchars(commar_url('obras.php'), ENT_QUOTES, 'UTF-8'); ?>" class="works-directory-filter-link<?php echo $selectedCategory === '' ? ' is-active' : ''; ?>"<?php echo $selectedCategory === '' ? ' aria-current="page"' : ''; ?>>Todas</a>
                            <?php foreach ($categories as $category): ?>
                                <a href="<?php echo htmlspecialchars(commar_url('obras.php?categoria=' . rawurlencode($category)), ENT_QUOTES, 'UTF-8'); ?>" class="works-directory-filter-link<?php echo $selectedCategory === $category ? ' is-active' : ''; ?>"<?php echo $selectedCategory === $category ? ' aria-current="page"' : ''; ?>>
                                    <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    <?php endif; ?>

                    <?php if (empty($filteredProjects)): ?>
                        <p class="works-directory-empty">No hay obras publicadas en esta categoría.</p>
                    <?php else: ?>
                        <ul class="works-grid">
                            <?php foreach ($filteredProjects as $index => $project): ?>
                                <?php
                                $galleryCount = count($project['gallery'] ?? []);
                                $cardMeta = array_values(array_filter([
                                    trim((string) ($project['location'] ?? '')),
                                    trim((string) ($project['year'] ?? '')),
                                ]));
                                ?>
                                <li>
                                    <a href="<?php echo htmlspecialchars(commar_url(commar_work_url($project['slug'])), ENT_QUOTES, 'UTF-8'); ?>" class="works-card">
                                        <div class="works-card-media">
                                            <img src="<?php echo htmlspecialchars($project['img'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo commar_image_srcset_attrs((string) $project['img'], (int) ($project['img_width'] ?: 1400), '(min-width: 1024px) 30vw, (min-width: 640px) 48vw, 100vw'); ?> alt="<?php echo htmlspecialchars($project['hero_alt'] ?: $project['title'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) ($project['img_width'] ?: 1400); ?>" height="<?php echo (int) ($project['img_height'] ?: 933); ?>" loading="<?php echo $index < 3 ? 'eager' : commar_image_loading_attr('lazy'); ?>" decoding="async" class="works-card-image">
                                            <?php if (trim((string) $project['category']) !== ''): ?>
                                                <span class="works-card-badge"><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php endif; ?>
                                            <?php if ($galleryCount > 1): ?>
                                                <span class="works-card-count"><?php echo $galleryCount; ?> imágenes</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="works-card-copy">
                                            <h2 class="works-card-title"><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                            <?php if (!empty($cardMeta)): ?>
                                                <p class="works-card-meta"><?php echo htmlspecialchars(implode(' · ', $cardMeta), ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                            <span class="works-card-cta" aria-hidden="true">Ver obra</span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260930-works-grid2" defer></script>
</body>
</html>
