<?php
require_once __DIR__ . '/auth.php';

function commar_admin_nav(string $active): void
{
    $items = [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10M18 20V4M6 20V16"/></svg>'],
        ['id' => 'home', 'label' => 'Página Home', 'href' => 'home.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
        ['id' => 'heroes', 'label' => 'Heros', 'href' => 'heros.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="14" x="3" y="5" rx="2"/><path d="M7 15h6"/><path d="M7 11h10"/><path d="m16 15 2-2 2 2"/></svg>'],
        ['id' => 'team', 'label' => 'Equipo', 'href' => 'team.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'],
        ['id' => 'works', 'label' => 'Obras', 'href' => 'works.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9v.01"/><path d="M9 13v.01"/><path d="M9 17v.01"/></svg>'],
        ['id' => 'blog', 'label' => 'Blog', 'href' => 'blog.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>'],
        ['id' => 'media', 'label' => 'Mediateca', 'href' => 'media.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>'],
        ['id' => 'jobs', 'label' => 'Busqueda laboral', 'href' => 'jobs.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>'],
        ['id' => 'newsletter', 'label' => 'Suscripciones', 'href' => 'newsletter-submissions.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a2 2 0 0 1-2.06 0L2 7"/></svg>'],
        ['id' => 'settings', 'label' => 'Configuraciones', 'href' => 'settings.php', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'],
    ];
    if (!commar_admin_is_administrator()) {
        $items = array_values(array_filter($items, static fn(array $item): bool => $item['id'] !== 'settings'));
    }
    ?>
    <aside class="admin-sidebar" id="admin-sidebar-menu">
        <div class="admin-sidebar-head">
            <a href="index.php" class="admin-sidebar-brand">
                <img src="../img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578">
                <span>MOnkey CMS</span>
            </a>
            <button type="button" class="admin-sidebar-close" aria-label="Cerrar menú" data-admin-menu-close>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <nav class="admin-sidebar-nav" aria-label="Navegación admin">
            <?php foreach ($items as $item): ?>
                <a href="<?php echo commar_admin_h($item['href']); ?>" class="admin-sidebar-link <?php echo $active === $item['id'] ? 'is-active' : ''; ?>">
                    <?php echo $item['icon']; ?>
                    <span><?php echo commar_admin_h($item['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar-about">
            <a href="#about-monkey-cms" class="admin-sidebar-link admin-sidebar-about-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                <span>Acerca de MOnkey CMS</span>
            </a>
        </div>
    </aside>
    <button type="button" class="admin-sidebar-backdrop" aria-label="Cerrar menú" data-admin-menu-close></button>
    <div id="about-monkey-cms" class="admin-modal-target">
        <a href="#" class="admin-modal-backdrop" aria-label="Cerrar"></a>
        <section class="admin-modal-card admin-about-modal" role="dialog" aria-modal="true" aria-labelledby="about-monkey-cms-title">
            <div class="admin-modal-head">
                <div>
                    <span class="admin-kicker">MOnkey ARt</span>
                    <h2 id="about-monkey-cms-title">Acerca de MOnkey CMS</h2>
                </div>
                <a href="#" class="admin-modal-close" aria-label="Cerrar">&times;</a>
            </div>
            <div class="admin-about-body">
                <span class="admin-about-version">v1.1</span>
                <p>MOnkey CMS es una plataforma de gestión creada exclusivamente para COMMAR GROUP por el equipo de diseño y desarrollo de la Agencia MOnkey ARt.</p>
                <p>Su arquitectura fue pensada para acompañar la identidad, el contenido y la evolución digital de la marca, integrando administración de sitio, publicaciones, obras, mediateca y herramientas operativas en un entorno propio, sobrio y eficiente.</p>
                <p class="admin-about-rights">Todos los derechos reservados. MOnkey CMS, su diseño, estructura e implementación forman parte de un desarrollo a medida para COMMAR GROUP.</p>
            </div>
        </section>
    </div>
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
            <button type="button" class="admin-topbar-menu" aria-label="Abrir menú" aria-controls="admin-sidebar-menu" aria-expanded="false" data-admin-menu-toggle>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/></svg>
            </button>
            <a href="../index.php" target="_blank" rel="noopener" class="admin-topbar-action">Ver sitio</a>
            <form action="logout.php" method="post" class="admin-logout-form">
                <input type="hidden" name="csrf_token" value="<?php echo commar_admin_csrf_token(); ?>">
                <button type="submit" class="admin-topbar-action admin-topbar-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Salir</span>
                </button>
            </form>
        </div>
    </header>
    <?php
}

function commar_admin_settings_nav(string $active): void
{
    $items = [
        ['id' => 'general', 'label' => 'General', 'href' => 'settings.php'],
        ['id' => 'users', 'label' => 'Usuarios', 'href' => 'settings-users.php'],
        ['id' => 'menu', 'label' => 'Menú', 'href' => 'settings-menu.php'],
        ['id' => 'integrations', 'label' => 'Integraciones', 'href' => 'settings-integrations.php'],
        ['id' => 'images', 'label' => 'Imágenes', 'href' => 'settings-images.php'],
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
        <p><?php echo date('Y'); ?> &copy; MOnkey CMS v1.1 - Diseñado y Desarrollado por <a href="https://monkey-art.net" target="_blank" rel="noopener noreferrer">MOnkey ARt</a>.</p>
    </footer>
    <script>
        document.querySelectorAll('[data-admin-menu-toggle]').forEach(function (toggle) {
            var closeButtons = document.querySelectorAll('[data-admin-menu-close]');
            var setMenuOpen = function (isOpen) {
                document.body.classList.toggle('is-admin-menu-open', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            toggle.addEventListener('click', function () {
                setMenuOpen(!document.body.classList.contains('is-admin-menu-open'));
            });

            closeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setMenuOpen(false);
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setMenuOpen(false);
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    setMenuOpen(false);
                }
            });
        });
    </script>
    <?php
}
