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

    $selectedSlug = trim((string) ($_GET['slug'] ?? ''));
    $selectedProject = null;

    if ($selectedSlug !== '') {
        foreach ($filteredProjects as $project) {
            if ($project['slug'] === $selectedSlug) {
                $selectedProject = $project;
                break;
            }
        }
    }

    if ($selectedProject === null && !empty($filteredProjects)) {
        $selectedProject = $filteredProjects[0];
    }

    $projectsByLetter = [];
    foreach ($filteredProjects as $project) {
        $title = trim((string) ($project['title'] ?? ''));
        $letter = strtoupper(substr($title, 0, 1));
        if ($letter === '' || !preg_match('/[A-Z0-9]/', $letter)) {
            $letter = '#';
        }
        $projectsByLetter[$letter][] = $project;
    }

    $seo = [
        'title' => 'Obras',
        'description' => 'Directorio de obras de COMMAR GROUP ordenadas alfabéticamente.',
        'path' => 'obras.php',
        'image' => $selectedProject['img'] ?? 'img/logo-commar-500.png',
        'image_alt' => 'Obras de COMMAR GROUP',
        'og_type' => 'website',
    ];

    include __DIR__ . '/includes/seo.php';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260724-header-contrast">
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
                    <span class="project-detail-kicker">Directorio</span>
                    <h1 id="works-directory-title" class="works-directory-title">Obras</h1>
                    <p>Explorá las obras de COMMAR GROUP por categoría, con un índice alfabético y una vista rápida de cada proyecto.</p>
                </div>

                <?php if (empty($projects)): ?>
                    <p class="admin-empty">No hay obras cargadas.</p>
                <?php else: ?>
                    <div class="works-directory-layout">
                        <aside class="works-directory-nav" aria-label="Directorio de obras">
                            <div class="works-directory-filter" aria-label="Filtro de categorías">
                                <a href="<?php echo htmlspecialchars(commar_url('obras.php'), ENT_QUOTES, 'UTF-8'); ?>" class="works-directory-filter-link<?php echo $selectedCategory === '' ? ' is-active' : ''; ?>">Todas</a>
                                <?php foreach ($categories as $category): ?>
                                    <a href="<?php echo htmlspecialchars(commar_url('obras.php?categoria=' . rawurlencode($category)), ENT_QUOTES, 'UTF-8'); ?>" class="works-directory-filter-link<?php echo $selectedCategory === $category ? ' is-active' : ''; ?>">
                                        <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>

                            <?php if (empty($filteredProjects)): ?>
                                <p class="works-directory-empty">No hay obras publicadas en esta categoría.</p>
                            <?php else: ?>
                                <div class="works-directory-list">
                                    <?php foreach ($projectsByLetter as $letter => $letterProjects): ?>
                                        <div class="works-directory-group">
                                            <span class="works-directory-letter"><?php echo htmlspecialchars($letter, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <div class="works-directory-group-list">
                                                <?php foreach ($letterProjects as $project): ?>
                                                    <?php
                                                    $isActive = $selectedProject && $project['slug'] === $selectedProject['slug'];
                                                    $projectUrl = 'obras.php?slug=' . rawurlencode($project['slug']);
                                                    if ($selectedCategory !== '') {
                                                        $projectUrl .= '&categoria=' . rawurlencode($selectedCategory);
                                                    }
                                                    $projectUrl = commar_url($projectUrl);
                                                    ?>
                                                    <a href="<?php echo htmlspecialchars($projectUrl, ENT_QUOTES, 'UTF-8'); ?>" class="works-directory-link<?php echo $isActive ? ' is-active' : ''; ?>">
                                                        <strong><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                                        <span><?php echo htmlspecialchars($project['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </aside>

                        <?php if ($selectedProject): ?>
                            <?php $selectedGallery = !empty($selectedProject['gallery']) ? $selectedProject['gallery'] : [[
                                'path' => $selectedProject['img'],
                                'width' => (int) $selectedProject['img_width'],
                                'height' => (int) $selectedProject['img_height'],
                                'alt' => $selectedProject['hero_alt'],
                            ]]; ?>
                            <?php $primaryGalleryImage = $selectedGallery[0]; ?>
                            <article class="works-directory-detail">
                                <div class="works-directory-copy">
                                    <span class="project-detail-kicker"><?php echo htmlspecialchars($selectedProject['category'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($selectedProject['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <h2><?php echo htmlspecialchars($selectedProject['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                    <dl class="works-directory-meta">
                                        <div>
                                            <dt>Año</dt>
                                            <dd><?php echo htmlspecialchars($selectedProject['year'], ENT_QUOTES, 'UTF-8'); ?></dd>
                                        </div>
                                        <?php foreach (array_slice($selectedProject['metrics'], 0, 3, true) as $label => $value): ?>
                                            <div>
                                                <dt><?php echo htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8'); ?></dt>
                                                <dd><?php echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?></dd>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                    <p><?php echo htmlspecialchars($selectedProject['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="works-directory-media" data-work-gallery>
                                    <img src="<?php echo htmlspecialchars((string) ($primaryGalleryImage['path'] ?? $selectedProject['img']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($primaryGalleryImage['alt'] ?? $selectedProject['hero_alt']), ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) ($primaryGalleryImage['width'] ?? $selectedProject['img_width']); ?>" height="<?php echo (int) ($primaryGalleryImage['height'] ?? $selectedProject['img_height']); ?>" loading="eager" decoding="async" class="works-directory-main-image" data-work-gallery-main>
                                    <?php if (count($selectedGallery) > 1): ?>
                                        <div class="works-directory-thumbs" aria-label="Galería de <?php echo htmlspecialchars($selectedProject['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php foreach (array_slice($selectedGallery, 0, 10) as $galleryIndex => $galleryItem): ?>
                                                <?php $galleryPath = (string) ($galleryItem['path'] ?? ''); ?>
                                                <?php if ($galleryPath !== ''): ?>
                                                    <button type="button" class="works-directory-thumb<?php echo $galleryIndex === 0 ? ' is-active' : ''; ?>" data-work-gallery-thumb data-src="<?php echo htmlspecialchars($galleryPath, ENT_QUOTES, 'UTF-8'); ?>" data-alt="<?php echo htmlspecialchars((string) ($galleryItem['alt'] ?? $selectedProject['hero_alt']), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Ver imagen <?php echo $galleryIndex + 1; ?> de <?php echo htmlspecialchars($selectedProject['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <img src="<?php echo htmlspecialchars($galleryPath, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) ($galleryItem['width'] ?? 160); ?>" height="<?php echo (int) ($galleryItem['height'] ?? 120); ?>" loading="<?php echo commar_image_loading_attr('lazy'); ?>" decoding="async">
                                                    </button>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260724-header-contrast" defer></script>
</body>
</html>
