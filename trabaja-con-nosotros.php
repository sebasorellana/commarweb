<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/jobs.php';
require_once __DIR__ . '/includes/integrations.php';

$jobs = commar_active_jobs();

if (empty($jobs)) {
    http_response_code(404);
}

$status = (string) ($_GET['status'] ?? '');
$seo = [
    'title' => 'Trabajá con nosotros',
    'description' => 'Búsquedas laborales activas de COMMAR GROUP.',
    'path' => 'trabaja-con-nosotros.php',
    'robots' => empty($jobs) ? 'noindex, follow' : 'index, follow',
];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php include __DIR__ . '/includes/seo.php'; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;900&display=swap">
    <link rel="stylesheet" href="style.css?v=20260629-jobs">
</head>
<body>
    <?php include __DIR__ . '/includes/google-tag-manager-body.php'; ?>
    <?php
    $headerVariant = 'default';
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
        <section class="jobs-page" aria-labelledby="jobs-title">
            <div class="site-shell-wide jobs-shell">
                <div class="jobs-intro">
                    <span class="jobs-kicker">Trabajá con nosotros</span>
                    <h1 id="jobs-title">Búsquedas laborales activas</h1>
                    <p>Conocé las oportunidades abiertas y enviá tu CV para que el equipo de COMMAR GROUP pueda evaluarlo.</p>
                </div>

                <?php if ($status === 'ok'): ?>
                    <div class="jobs-status jobs-status-success" role="status">Recibimos tu postulación correctamente.</div>
                <?php elseif ($status === 'error'): ?>
                    <div class="jobs-status jobs-status-error" role="alert">No pudimos recibir tu postulación. Revisá los datos y el archivo adjunto.</div>
                <?php endif; ?>

                <?php if (empty($jobs)): ?>
                    <div class="jobs-empty">
                        <h2>No hay búsquedas activas en este momento.</h2>
                        <p>Cuando se abra una nueva posición, vas a poder verla desde el footer del sitio.</p>
                    </div>
                <?php else: ?>
                    <div class="jobs-list">
                        <?php foreach ($jobs as $job): ?>
                            <article class="job-card">
                                <div class="job-card-copy">
                                    <?php if (!empty($job['image'])): ?>
                                        <figure class="job-card-media">
                                            <img src="<?php echo htmlspecialchars((string) $job['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $job['title'], ENT_QUOTES, 'UTF-8'); ?>" width="<?php echo (int) ($job['image_width'] ?? 1200); ?>" height="<?php echo (int) ($job['image_height'] ?? 800); ?>" loading="<?php echo commar_image_loading_attr('lazy'); ?>">
                                        </figure>
                                    <?php endif; ?>
                                    <span class="jobs-kicker">Búsqueda activa</span>
                                    <h2><?php echo htmlspecialchars((string) $job['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                    <div class="job-description">
                                        <?php echo commar_job_description_html((string) $job['description']); ?>
                                    </div>
                                </div>

                                <form class="job-apply-form" action="<?php echo htmlspecialchars(commar_url('job-apply.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data"<?php echo commar_recaptcha_form_attributes('job_apply'); ?>>
                                    <input type="hidden" name="job_id" value="<?php echo (int) $job['id']; ?>">
                                    <label>
                                        Nombre y apellido
                                        <input type="text" name="full_name" autocomplete="name" required>
                                    </label>
                                    <label>
                                        Email
                                        <input type="email" name="email" autocomplete="email" required>
                                    </label>
                                    <label>
                                        Teléfono
                                        <input type="tel" name="phone" autocomplete="tel">
                                    </label>
                                    <label>
                                        CV
                                        <input type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                                    </label>
                                    <label class="job-field-full">
                                        Mensaje
                                        <textarea name="message" rows="4"></textarea>
                                    </label>
                                    <input type="text" name="company_name" value="" tabindex="-1" autocomplete="off" class="newsletter-honeypot" aria-hidden="true">
                                    <?php echo commar_recaptcha_field('job_apply'); ?>
                                    <button type="submit">Enviar CV</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
