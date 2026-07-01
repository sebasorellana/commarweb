<?php
require_once __DIR__ . '/integrations.php';

$newsletterTitleId = $newsletterTitleId ?? 'newsletter-title';
?>
<section id="newsletter" class="home-newsletter-section" aria-labelledby="<?php echo htmlspecialchars($newsletterTitleId, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="site-shell-wide">
        <div class="home-newsletter" data-newsletter-reveal>
            <div class="home-newsletter-copy">
                <span class="home-newsletter-kicker">Newsletter</span>
                <h2 id="<?php echo htmlspecialchars($newsletterTitleId, ENT_QUOTES, 'UTF-8'); ?>" class="home-newsletter-title">Recibí nuevas obras, ideas y próximos movimientos de COMMAR GROUP.</h2>
            </div>
            <form class="home-newsletter-form" action="<?php echo htmlspecialchars(commar_url('newsletter-submit.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post"<?php echo commar_recaptcha_form_attributes('newsletter'); ?>>
                <label class="sr-only" for="<?php echo htmlspecialchars($newsletterTitleId, ENT_QUOTES, 'UTF-8'); ?>-email">Email</label>
                <input id="<?php echo htmlspecialchars($newsletterTitleId, ENT_QUOTES, 'UTF-8'); ?>-email" class="home-newsletter-input" type="email" name="email" placeholder="tu@email.com" autocomplete="email" required>
                <input type="hidden" name="source" value="newsletter">
                <input type="hidden" name="page_url" value="<?php echo htmlspecialchars((string) ($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="text" name="company_name" value="" tabindex="-1" autocomplete="off" class="newsletter-honeypot" aria-hidden="true">
                <?php echo commar_recaptcha_field('newsletter'); ?>
                <button class="home-newsletter-button" type="submit">Suscribirme</button>
            </form>
        </div>
    </div>
</section>
