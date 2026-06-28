<?php require_once __DIR__ . '/includes/site.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/projects.php';

    $projects = commar_projects();
    $selectedSlug = trim((string) ($_GET['slug'] ?? ''));
    $selectedProject = $selectedSlug !== '' ? commar_project_by_slug($selectedSlug) : ($projects[0] ?? null);

    if ($selectedProject === null && !empty($projects)) {
        $selectedProject = $projects[0];
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
    <link rel="stylesheet" href="style.css?v=20260508-1">
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
                    <p>Explorá las obras de COMMAR GROUP ordenadas alfabéticamente.</p>
                </div>

                <?php if (empty($projects)): ?>
                    <p class="admin-empty">No hay obras cargadas.</p>
                <?php else: ?>
                    <div class="works-directory-layout">
                        <aside class="works-directory-nav" aria-label="Directorio de obras">
                            <?php foreach ($projects as $project): ?>
                                <?php $isActive = $selectedProject && $project['slug'] === $selectedProject['slug']; ?>
                                <a href="obras.php?slug=<?php echo urlencode($project['slug']); ?>" class="works-directory-link<?php echo $isActive ? ' is-active' : ''; ?>">
                                    <span><?php echo htmlspecialchars($project['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <strong><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                </a>
                            <?php endforeach; ?>
                        </aside>

                        <?php if ($selectedProject): ?>
                            <article class="works-directory-detail">
                                <div class="works-directory-media">
                                    <img src="<?php echo htmlspecialchars($selectedProject['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($selectedProject['hero_alt'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) $selectedProject['img_width']; ?>" height="<?php echo (int) $selectedProject['img_height']; ?>" loading="eager" decoding="async">
                                </div>
                                <div class="works-directory-copy">
                                    <span class="project-detail-kicker"><?php echo htmlspecialchars($selectedProject['category'], ENT_QUOTES, 'UTF-8'); ?> // <?php echo htmlspecialchars($selectedProject['location'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <h2><?php echo htmlspecialchars($selectedProject['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                    <p><?php echo htmlspecialchars($selectedProject['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
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
                                    <a href="obra.php?slug=<?php echo urlencode($selectedProject['slug']); ?>" class="projects-showcase-link">Ver ficha completa</a>
                                </div>
                            </article>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php include __DIR__ . '/includes/footer.php'; ?>
    </main>

    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
