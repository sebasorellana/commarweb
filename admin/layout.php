<?php
require_once __DIR__ . '/auth.php';

function commar_admin_nav(string $active): void
{
    $items = [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10M18 20V4M6 20V16"/></svg>'],
        ['id' => 'home', 'label' => 'Página Home', 'href' => 'home.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
        ['id' => 'works', 'label' => 'Obras', 'href' => 'works.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 13v.01"/><path d="M9 17v.01"/></svg>'],
        ['id' => 'blog', 'label' => 'Blog', 'href' => 'blog.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>'],
        ['id' => 'jobs', 'label' => 'Busqueda laboral', 'href' => 'jobs.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>'],
        ['id' => 'newsletter', 'label' => 'Suscripciones', 'href' => 'newsletter-submissions.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a2 2 0 0 1-2.06 0L2 7"/></svg>'],
        ['id' => 'settings', 'label' => 'Configuraciones', 'href' => 'settings.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'],
    ];
    ?>
    <aside class="admin-sidebar">
        <a href="index.php" class="admin-sidebar-brand">
            <img src="../img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578">
            <span>MOnkey CMS</span>
        </a>
        <nav class="admin-sidebar-nav" aria-label="Navegación admin">
            <?php foreach ($items as $item): ?>
                <a href="<?php echo commar_admin_h($item['href']); ?>" class="admin-sidebar-link <?php echo $active === $item['id'] ? 'is-active' : ''; ?>">
                    <?php echo $item['icon']; ?>
                    <span><?php echo commar_admin_h($item['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <?php
}

function commar_admin_home_nav(string $active): void
{
    $items = [
        ['id' => 'hero', 'label' => 'Hero de la home', 'href' => 'home.php'],
        ['id' => 'focused-works', 'label' => 'Obras en foco', 'href' => 'focused-works.php'],
    ];
    ?>
    <nav class="admin-subnav" aria-label="Submenú de Página Home">
        <?php foreach ($items as $item): ?>
            <a href="<?php echo commar_admin_h($item['href']); ?>" class="<?php echo $active === $item['id'] ? 'is-active' : ''; ?>"><?php echo commar_admin_h($item['label']); ?></a>
        <?php endforeach; ?>
    </nav>
    <?php
}

function commar_admin_works_nav(string $active): void
{
    $items = [
        ['id' => 'works', 'label' => 'Obras', 'href' => 'works.php'],
        ['id' => 'categories', 'label' => 'Categorías', 'href' => 'work-categories.php'],
    ];
    ?>
    <nav class="admin-subnav" aria-label="Submenú de obras">
        <?php foreach ($items as $item): ?>
            <a href="<?php echo commar_admin_h($item['href']); ?>" class="<?php echo $active === $item['id'] ? 'is-active' : ''; ?>"><?php echo commar_admin_h($item['label']); ?></a>
        <?php endforeach; ?>
    </nav>
    <?php
}

function commar_admin_header(string $title, string $kicker = 'COMMAR GROUP'): void
{
    ?>
    <header class="admin-topbar">
        <div>
            <span class="admin-kicker"><?php echo commar_admin_h($kicker); ?></span>
            <h1><?php echo commar_admin_h($title); ?></h1>
        </div>
        <div class="admin-topbar-actions">
            <a href="../index.php" target="_blank" rel="noopener" class="admin-topbar-action">Ver sitio</a>
            <a href="logout.php" class="admin-topbar-action admin-topbar-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Salir</span>
            </a>
        </div>
    </header>
    <?php
}

function commar_admin_settings_nav(string $active): void
{
    $items = [
        ['id' => 'general', 'label' => 'General', 'href' => 'settings.php'],
        ['id' => 'users', 'label' => 'Gestión de usuarios', 'href' => 'settings-users.php'],
    ];
    ?>
    <nav class="admin-subnav" aria-label="Submenú de configuraciones">
        <?php foreach ($items as $item): ?>
            <a href="<?php echo commar_admin_h($item['href']); ?>" class="<?php echo $active === $item['id'] ? 'is-active' : ''; ?>"><?php echo commar_admin_h($item['label']); ?></a>
        <?php endforeach; ?>
    </nav>
    <?php
}

function commar_admin_footer(): void
{
    ?>
    <footer class="admin-footer admin-footer-dashboard">
        <p>&copy; <?php echo date('Y'); ?> MOnkey CMS, diseñado y creado por <a href="https://monkey-art.net" target="_blank" rel="noopener noreferrer">MOnkey ARt</a>.</p>
    </footer>
    <?php
}
