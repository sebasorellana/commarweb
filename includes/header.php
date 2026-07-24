<?php
require_once __DIR__ . '/menu.php';

$headerVariant = $headerVariant ?? 'home';
$languageOptions = commar_supported_languages();
$languageSwitcherEnabled = (string) commar_setting('language_switcher_enabled') === '1';
$menuItems = $menuItems ?? commar_menu_items('header');
?>

<?php if ($headerVariant === 'home'): ?>
    <nav id="site-header-nav" class="site-header-nav fixed top-0 left-0 w-full z-[100] px-6 md:px-10 py-8 flex justify-between items-center text-white" data-site-header>
        <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="site-logo-link" aria-label="COMMAR GROUP, volver al inicio">
            <img src="img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578" class="site-logo">
            <span class="site-logo-text">Commar Group</span>
        </a>
        <div class="flex items-center gap-4">
            <div class="desktop-nav">
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars(commar_url($item['href']), ENT_QUOTES, 'UTF-8'); ?>" class="desktop-nav-link"><?php echo htmlspecialchars(commar_nav_label($item['label']), ENT_QUOTES, 'UTF-8'); ?></a>
                <?php endforeach; ?>
            </div>
            <?php if ($languageSwitcherEnabled): ?>
            <details class="relative language-switcher">
                <summary class="list-none flex items-center justify-center w-10 h-10 rounded-full border border-white/20 cursor-pointer hover:bg-white/10 transition-colors">
                    <span class="sr-only"><?php echo htmlspecialchars(commar_t('nav.language'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M2 12h20"></path><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                </summary>
                <div class="site-language-menu absolute right-0 mt-3 min-w-[160px] bg-black/90 border border-white/10 rounded-2xl p-2 backdrop-blur-sm">
                    <?php foreach ($languageOptions as $languageKey => $language): ?>
                        <a href="<?php echo htmlspecialchars(commar_language_url($languageKey), ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center justify-between gap-4 px-3 py-2 text-[11px] uppercase tracking-[0.24em] font-bold hover:bg-white/10 rounded-xl transition-colors">
                            <span><?php echo htmlspecialchars($language['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="text-white/50"><?php echo htmlspecialchars($language['code'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <button id="menu-toggle" class="menu-toggle mobile-only flex items-center gap-4 text-[10px] tracking-[0.3em] uppercase font-bold" type="button" aria-controls="menu-content" aria-expanded="false">
                <span><?php echo htmlspecialchars(commar_t('nav.menu'), ENT_QUOTES, 'UTF-8'); ?></span>
                <div class="menu-toggle-lines">
                    <span class="menu-toggle-line"></span>
                    <span class="menu-toggle-line"></span>
                </div>
            </button>
        </div>
    </nav>

    <div id="menu-content" class="menu-overlay fixed inset-0 bg-white text-black z-[200] flex flex-col p-6 md:p-10 justify-between" aria-hidden="true">
        <div class="flex justify-between items-center text-black">
            <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="site-logo-link site-logo-link-overlay" aria-label="COMMAR GROUP, volver al inicio">
                <img src="img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578" class="site-logo">
                <span class="site-logo-text">Commar Group</span>
            </a>
            <button id="menu-close" class="p-4 border border-black/10 rounded-full hover:bg-black hover:text-white transition-colors" type="button" aria-label="Cerrar menú">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="menu-links">
            <?php foreach ($menuItems as $i => $item): ?>
                <a href="<?php echo htmlspecialchars(commar_url($item['href']), ENT_QUOTES, 'UTF-8'); ?>" class="menu-link">
                    <span class="menu-link-index">0<?php echo $i + 1; ?></span>
                    <span class="menu-link-label"><?php echo htmlspecialchars(commar_nav_label($item['label']), ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex flex-col md:flex-row justify-between text-[10px] uppercase tracking-widest font-bold text-gray-400 border-t border-black/5 pt-8">
            <p><?php echo htmlspecialchars(commar_t('footer.vanguard'), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="flex gap-8 mt-4 md:mt-0">
                <span>EST. 2009</span>
                <span>VOL. IV</span>
            </div>
        </div>
    </div>
<?php else: ?>
    <nav id="site-header-nav" class="site-header-nav fixed top-0 left-0 w-full z-[100] px-6 md:px-10 py-8 flex justify-between items-center text-white" data-site-header>
        <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="site-logo-link" aria-label="COMMAR GROUP, volver al inicio">
            <img src="img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578" class="site-logo">
            <span class="site-logo-text">Commar Group</span>
        </a>
        <div class="flex items-center gap-4">
            <div class="desktop-nav">
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?php echo htmlspecialchars(commar_url($item['href']), ENT_QUOTES, 'UTF-8'); ?>" class="desktop-nav-link"><?php echo htmlspecialchars(commar_nav_label($item['label']), ENT_QUOTES, 'UTF-8'); ?></a>
                <?php endforeach; ?>
            </div>
            <?php if ($languageSwitcherEnabled): ?>
            <details class="relative language-switcher">
                <summary class="list-none flex items-center justify-center w-10 h-10 rounded-full border border-white/20 cursor-pointer hover:bg-white/10 transition-colors">
                    <span class="sr-only"><?php echo htmlspecialchars(commar_t('nav.language'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M2 12h20"></path><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                </summary>
                <div class="site-language-menu absolute right-0 mt-3 min-w-[160px] bg-black/90 border border-white/10 rounded-2xl p-2 backdrop-blur-sm shadow-xl">
                    <?php foreach ($languageOptions as $languageKey => $language): ?>
                        <a href="<?php echo htmlspecialchars(commar_language_url($languageKey), ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center justify-between gap-4 px-3 py-2 text-[11px] uppercase tracking-[0.24em] font-bold hover:bg-white/10 rounded-xl transition-colors">
                            <span><?php echo htmlspecialchars($language['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="text-white/50"><?php echo htmlspecialchars($language['code'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <button id="menu-toggle" class="menu-toggle mobile-only flex items-center gap-4 text-[10px] tracking-[0.3em] uppercase font-bold" type="button" aria-controls="menu-content" aria-expanded="false">
                <span><?php echo htmlspecialchars(commar_t('nav.menu'), ENT_QUOTES, 'UTF-8'); ?></span>
                <div class="menu-toggle-lines">
                    <span class="menu-toggle-line"></span>
                    <span class="menu-toggle-line"></span>
                </div>
            </button>
        </div>
    </nav>

    <div id="menu-content" class="menu-overlay fixed inset-0 bg-white text-black z-[200] flex flex-col p-6 md:p-10 justify-between" aria-hidden="true">
        <div class="flex justify-between items-center text-black">
            <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="site-logo-link site-logo-link-overlay" aria-label="COMMAR GROUP, volver al inicio">
                <img src="img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578" class="site-logo">
                <span class="site-logo-text">Commar Group</span>
            </a>
            <button id="menu-close" class="p-4 border border-black/10 rounded-full hover:bg-black hover:text-white transition-colors" type="button" aria-label="Cerrar menú">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <div class="menu-links">
            <?php foreach ($menuItems as $i => $item): ?>
                <a href="<?php echo htmlspecialchars(commar_url($item['href']), ENT_QUOTES, 'UTF-8'); ?>" class="menu-link">
                    <span class="menu-link-index">0<?php echo $i + 1; ?></span>
                    <span class="menu-link-label"><?php echo htmlspecialchars(commar_nav_label($item['label']), ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex flex-col md:flex-row justify-between text-[10px] uppercase tracking-widest font-bold text-gray-400 border-t border-black/5 pt-8">
            <p><?php echo htmlspecialchars(commar_t('footer.vanguard'), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="flex gap-8 mt-4 md:mt-0">
                <span>EST. 2009</span>
                <span>VOL. IV</span>
            </div>
        </div>
    </div>
<?php endif; ?>

<section class="commar-chatbot" data-commar-chatbot data-whatsapp-url="<?php echo htmlspecialchars(commar_whatsapp_url(), ENT_QUOTES, 'UTF-8'); ?>" data-contact-url="<?php echo htmlspecialchars(commar_url('contacto.php'), ENT_QUOTES, 'UTF-8'); ?>" data-contact-email="<?php echo htmlspecialchars(commar_contact_email(), ENT_QUOTES, 'UTF-8'); ?>" aria-label="Asistente de COMMAR GROUP">
    <div class="commar-chatbot-panel" data-chatbot-panel hidden>
        <div class="commar-chatbot-header">
            <div>
                <p class="commar-chatbot-kicker">COMMAR GROUP</p>
                <h2>Asistente virtual</h2>
            </div>
            <button class="commar-chatbot-close" type="button" data-chatbot-close aria-label="Cerrar asistente">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="commar-chatbot-messages" data-chatbot-messages aria-live="polite"></div>
        <div class="commar-chatbot-actions" data-chatbot-actions></div>
        <form class="commar-chatbot-form" data-chatbot-form>
            <label class="sr-only" for="commar-chatbot-input">Escribí tu consulta sobre COMMAR GROUP</label>
            <input id="commar-chatbot-input" data-chatbot-input type="text" autocomplete="off" placeholder="Consultá por servicios u obras">
            <button type="submit">Enviar</button>
        </form>
    </div>
    <button class="commar-chatbot-toggle" type="button" data-chatbot-toggle aria-expanded="false" aria-label="Abrir asistente de COMMAR GROUP">
        <span class="commar-chatbot-toggle-text">Consultas</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="commar-chatbot-icon" aria-hidden="true">
            <path fill="currentColor" d="M19.11 17.21c-.27-.14-1.58-.78-1.82-.87-.24-.09-.42-.14-.6.14-.18.27-.69.87-.85 1.05-.15.18-.31.2-.58.07-.27-.14-1.12-.41-2.14-1.31-.79-.7-1.32-1.57-1.48-1.84-.15-.27-.02-.42.11-.56.12-.12.27-.31.41-.47.14-.16.18-.27.27-.45.09-.18.05-.34-.02-.47-.07-.14-.6-1.45-.82-1.99-.22-.52-.44-.45-.6-.46h-.51c-.18 0-.47.07-.72.34-.24.27-.92.9-.92 2.19 0 1.29.94 2.54 1.07 2.72.14.18 1.86 2.84 4.52 3.98.63.27 1.12.43 1.5.55.63.2 1.2.17 1.66.1.51-.08 1.58-.64 1.81-1.27.22-.63.22-1.16.15-1.27-.07-.11-.25-.18-.52-.32Z"/>
            <path fill="currentColor" d="M16.03 5.33c-5.9 0-10.69 4.77-10.69 10.66 0 1.88.49 3.71 1.42 5.31L5.27 26.7l5.53-1.45a10.74 10.74 0 0 0 5.22 1.34h.01c5.89 0 10.68-4.78 10.68-10.67 0-2.85-1.11-5.53-3.13-7.54a10.6 10.6 0 0 0-7.55-3.05Zm0 19.46h-.01a8.92 8.92 0 0 1-4.55-1.25l-.33-.19-3.28.86.88-3.2-.21-.33a8.84 8.84 0 0 1-1.36-4.7c0-4.9 3.99-8.88 8.89-8.88 2.37 0 4.59.92 6.26 2.59a8.8 8.8 0 0 1 2.6 6.27c0 4.9-3.99 8.88-8.89 8.88Z"/>
        </svg>
    </button>
</section>
