<?php
require_once __DIR__ . '/jobs.php';

$footerEmail = commar_contact_email();
$footerAddressLines = commar_contact_address_lines();
$instagramUrl = trim((string) commar_setting('instagram_url'));
$linkedinUrl = trim((string) commar_setting('linkedin_url'));
$footerScript = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$shouldRenderNewsletter = !in_array($footerScript, ['index.php'], true);
$footerActiveJobs = commar_active_jobs();
?>
    <?php if ($shouldRenderNewsletter): ?>
        <?php $newsletterTitleId = 'newsletter-title-footer'; ?>
        <?php include __DIR__ . '/newsletter.php'; ?>
    <?php endif; ?>
    <footer class="site-footer">
        <div class="site-shell-wide footer-shell">
            <div class="footer-top">
                <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-logo-link" aria-label="COMMAR GROUP - Inicio">
                    <img src="img/logo-commar-500.png" alt="COMMAR GROUP" width="500" height="578" loading="lazy" decoding="async" class="footer-logo-image">
                </a>

                <div class="footer-contact-card">
                    <span class="footer-cta-label"><?php echo htmlspecialchars(commar_t('footer.lets_talk'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <a href="mailto:<?php echo htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8'); ?>" class="footer-email-link"><?php echo htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php if ($footerAddressLines): ?>
                        <p><?php echo implode('<br>', array_map(static fn(string $line): string => htmlspecialchars($line, ENT_QUOTES, 'UTF-8'), $footerAddressLines)); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-grid">
                <nav class="footer-column" aria-label="Navegación del footer">
                    <p class="footer-column-label"><?php echo htmlspecialchars(commar_t('footer.map'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="<?php echo htmlspecialchars(commar_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.home'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="<?php echo htmlspecialchars(commar_url('el-estudio.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.studio'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="<?php echo htmlspecialchars(commar_url('servicios.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.services'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="<?php echo htmlspecialchars(commar_url('obra-viva.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.obra_viva'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="<?php echo htmlspecialchars(commar_url('obras.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.works'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="<?php echo htmlspecialchars(commar_url('blog.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.blog'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php if (!empty($footerActiveJobs)): ?>
                        <a href="<?php echo htmlspecialchars(commar_url('trabaja-con-nosotros.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link">Trabajá con nosotros</a>
                    <?php endif; ?>
                    <a href="<?php echo htmlspecialchars(commar_url('contacto.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('nav.contact'), ENT_QUOTES, 'UTF-8'); ?></a>
                </nav>
                <div class="footer-column">
                    <p class="footer-column-label"><?php echo htmlspecialchars(commar_t('footer.services'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="<?php echo htmlspecialchars(commar_url('servicio-proyectos.php'), ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars(commar_t('service.projects'), ENT_QUOTES, 'UTF-8'); ?></a>
                    <span><?php echo htmlspecialchars(commar_t('service.management'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php echo htmlspecialchars(commar_t('service.demolitions'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php echo htmlspecialchars(commar_t('service.construction'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php echo htmlspecialchars(commar_t('service.permits'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php echo htmlspecialchars(commar_t('service.environment'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="footer-column">
                    <p class="footer-column-label"><?php echo htmlspecialchars(commar_t('footer.social'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if ($instagramUrl !== ''): ?>
                        <a href="<?php echo htmlspecialchars($instagramUrl, ENT_QUOTES, 'UTF-8'); ?>" class="footer-link" target="_blank" rel="noopener noreferrer">Instagram</a>
                    <?php else: ?>
                        <span>Instagram</span>
                    <?php endif; ?>
                    <?php if ($linkedinUrl !== ''): ?>
                        <a href="<?php echo htmlspecialchars($linkedinUrl, ENT_QUOTES, 'UTF-8'); ?>" class="footer-link" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                    <?php else: ?>
                        <span>LinkedIn</span>
                    <?php endif; ?>
                </div>
                <div class="footer-column">
                    <p class="footer-column-label"><?php echo htmlspecialchars(commar_t('footer.contact'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="mailto:<?php echo htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8'); ?>" class="footer-link"><?php echo htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8'); ?></a>
                    <?php foreach ($footerAddressLines as $line): ?>
                        <span><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="footer-bottom">
                <p>COMMAR GROUP© 2026</p>
                <p><?php echo htmlspecialchars(commar_t('footer.credit'), ENT_QUOTES, 'UTF-8'); ?> <a href="https://monkey-art.net" target="_blank" rel="noopener noreferrer">MOnkey ARt</a>.</p>
            </div>
        </div>
    </footer>
