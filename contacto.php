<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/integrations.php';
require_once __DIR__ . '/includes/page-heroes.php';

$contactHero = commar_page_hero('contacto');
$contactEmail = commar_contact_email();
$contactFormEmail = commar_contact_form_email();
$contactAddressLines = commar_contact_address_lines();
$contactSubjects = [
    'Consulta',
    'Obra Viva',
    'Proyecto',
    'Gerenciamiento',
    'Demolición',
    'Construcción',
    'Habilitaciones',
    'Medio ambiente / Seguridad e Higiene',
];
$selectedSubject = $_GET['asunto'] ?? 'Consulta';
$selectedSubject = in_array($selectedSubject, $contactSubjects, true) ? $selectedSubject : 'Consulta';
$formData = [
    'subject' => $_POST['subject'] ?? $selectedSubject,
    'full_name' => $_POST['full_name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'company' => $_POST['company'] ?? '',
    'message' => $_POST['message'] ?? '',
];
$formErrors = [];
$formSent = false;
$mailAccepted = false;
$fallbackMailto = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $formData = array_map(static fn ($value): string => trim((string) $value), $formData);
    $replyToName = str_replace(["\r", "\n"], ' ', $formData['full_name']);

    if (!in_array($formData['subject'], $contactSubjects, true)) {
        $formErrors[] = 'Seleccioná un asunto válido.';
    }

    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $formErrors[] = 'Ingresá un email válido.';
    }

    if (!commar_recaptcha_verify('contact')) {
        $formErrors[] = 'No pudimos validar el captcha. Intentá nuevamente.';
    }

    $emailBody = implode("\n", [
        'Asunto: ' . $formData['subject'],
        'Nombre y apellido: ' . ($formData['full_name'] !== '' ? $formData['full_name'] : '-'),
        'Email: ' . $formData['email'],
        'Teléfono: ' . ($formData['phone'] !== '' ? $formData['phone'] : '-'),
        'Empresa: ' . ($formData['company'] !== '' ? $formData['company'] : '-'),
        '',
        'Mensaje:',
        $formData['message'] !== '' ? $formData['message'] : '-',
    ]);
    $fallbackMailto = 'mailto:' . rawurlencode($contactFormEmail) . '?subject=' . rawurlencode($formData['subject']) . '&body=' . rawurlencode($emailBody);

    if ($formErrors === []) {
        $headers = [
            'From: COMMAR GROUP <' . $contactEmail . '>',
            'Reply-To: ' . ($replyToName !== '' ? $replyToName . ' ' : '') . '<' . $formData['email'] . '>',
            'Content-Type: text/plain; charset=UTF-8',
        ];
        $mailAccepted = @mail($contactFormEmail, 'Contacto web - ' . $formData['subject'], $emailBody, implode("\r\n", $headers));
        $formSent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(commar_lang_attr(), ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <?php
    require_once __DIR__ . '/includes/site.php';

    $seo = [
        'title' => 'Contacto',
        'description' => 'Contactá a COMMAR GROUP para consultas sobre proyectos, construcción, demoliciones, habilitaciones, gerenciamiento municipal y medioambiente.',
        'path' => 'contacto.php',
        'image' => (string) $contactHero['image'],
        'image_alt' => 'COMMAR GROUP',
        'og_type' => 'website',
        'json_ld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Contacto | COMMAR GROUP',
                'url' => commar_absolute_url('contacto.php'),
                'inLanguage' => commar_lang_attr(),
            ],
        ],
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
        <section class="page-hero-section" aria-labelledby="contact-title">
            <div class="page-hero-media" aria-hidden="true">
                <img src="<?php echo htmlspecialchars((string) $contactHero['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" width="<?php echo (int) $contactHero['width']; ?>" height="<?php echo (int) $contactHero['height']; ?>" fetchpriority="high" decoding="async" class="page-hero-image">
                <div class="page-hero-overlay"></div>
            </div>
            <div class="site-shell-wide page-hero-content">
                <span class="page-hero-kicker"><?php echo htmlspecialchars((string) $contactHero['kicker'], ENT_QUOTES, 'UTF-8'); ?></span>
                <h1 id="contact-title" class="page-hero-title"><?php echo htmlspecialchars((string) $contactHero['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="page-hero-intro"><?php echo htmlspecialchars((string) $contactHero['intro'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </section>

        <section class="contact-page" aria-labelledby="contact-title">
            <div class="site-shell-wide contact-grid">
                <div class="contact-copy">
                    <div class="contact-details">
                        <a href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php foreach ($contactAddressLines as $line): ?>
                            <span><?php echo htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="contact-form-panel">
                    <?php if ($formSent): ?>
                        <div class="contact-form-status" role="status">
                            <strong><?php echo $mailAccepted ? 'Consulta enviada.' : 'Tu consulta quedó lista para enviar.'; ?></strong>
                            <p><?php echo $mailAccepted ? 'Gracias por escribirnos. Te responderemos a la brevedad.' : 'El servidor local no confirmó el envío automático. Podés enviarla desde tu cliente de correo con el botón de abajo.'; ?></p>
                            <?php if (!$mailAccepted): ?>
                                <a href="<?php echo htmlspecialchars($fallbackMailto, ENT_QUOTES, 'UTF-8'); ?>" class="contact-submit-link">Enviar por email</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($formErrors !== []): ?>
                        <div class="contact-form-errors" role="alert">
                            <?php foreach ($formErrors as $error): ?>
                                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form class="contact-form" action="<?php echo htmlspecialchars(commar_url('contacto.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post"<?php echo commar_recaptcha_form_attributes('contact'); ?>>
                        <div class="contact-field">
                            <label for="contact-subject">Asunto</label>
                            <select id="contact-subject" name="subject">
                                <?php foreach ($contactSubjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $formData['subject'] === $subject ? ' selected' : ''; ?>><?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="contact-field">
                            <label for="contact-full-name">Nombre y apellido</label>
                            <input id="contact-full-name" name="full_name" type="text" value="<?php echo htmlspecialchars($formData['full_name'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="name">
                        </div>

                        <div class="contact-field">
                            <label for="contact-email">Email</label>
                            <input id="contact-email" name="email" type="email" value="<?php echo htmlspecialchars($formData['email'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email" required>
                        </div>

                        <div class="contact-field">
                            <label for="contact-phone">Teléfono</label>
                            <input id="contact-phone" name="phone" type="tel" value="<?php echo htmlspecialchars($formData['phone'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="tel">
                        </div>

                        <div class="contact-field">
                            <label for="contact-company">Empresa</label>
                            <input id="contact-company" name="company" type="text" value="<?php echo htmlspecialchars($formData['company'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="organization">
                        </div>

                        <div class="contact-field contact-field-full">
                            <label for="contact-message">Mensaje</label>
                            <textarea id="contact-message" name="message" rows="7"><?php echo htmlspecialchars($formData['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <?php echo commar_recaptcha_field('contact'); ?>
                        <button type="submit" class="contact-submit">Enviar consulta</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script src="script.js?v=20260508-1" defer></script>
</body>
</html>
