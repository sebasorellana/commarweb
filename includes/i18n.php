<?php
if (!function_exists('commar_supported_languages')) {
    function commar_supported_languages(): array
    {
        return [
            'es' => ['label' => 'Español', 'code' => 'ES', 'locale' => 'es_AR', 'html' => 'es'],
            'en' => ['label' => 'English', 'code' => 'EN', 'locale' => 'en_US', 'html' => 'en'],
            'pt' => ['label' => 'Português', 'code' => 'PT', 'locale' => 'pt_BR', 'html' => 'pt'],
        ];
    }
}

if (!function_exists('commar_current_lang')) {
    function commar_current_lang(): string
    {
        static $lang = null;

        if ($lang !== null) {
            return $lang;
        }

        $requestedLang = strtolower((string) ($_GET['lang'] ?? $_COOKIE['commar_lang'] ?? 'es'));
        $lang = array_key_exists($requestedLang, commar_supported_languages()) ? $requestedLang : 'es';

        return $lang;
    }
}

if (!function_exists('commar_lang_attr')) {
    function commar_lang_attr(): string
    {
        return commar_supported_languages()[commar_current_lang()]['html'];
    }
}

if (!function_exists('commar_locale')) {
    function commar_locale(): string
    {
        return commar_supported_languages()[commar_current_lang()]['locale'];
    }
}

if (!function_exists('commar_translations')) {
    function commar_translations(): array
    {
        return [
            'es' => [
                'nav.home' => 'Inicio',
                'nav.studio' => 'El estudio',
                'nav.services' => 'Servicios',
                'nav.obra_viva' => 'Obra Viva',
                'nav.works' => 'Obras',
                'nav.blog' => 'Blog',
                'nav.contact' => 'Contacto',
                'nav.menu' => 'Menú',
                'nav.language' => 'Seleccionar idioma',
                'footer.map' => 'Mapa',
                'footer.lets_talk' => 'Hablemos',
                'footer.services' => 'Servicios',
                'footer.social' => 'Redes',
                'footer.contact' => 'Contacto',
                'footer.credit' => 'Sitio diseñado y desarrollado por',
                'footer.vanguard' => 'Arquitectura de vanguardia',
                'article.back' => 'Volver al listado de artículos',
                'article.share' => 'Compartir nota',
                'service.projects' => 'Proyecto',
                'service.management' => 'Gerenciamiento',
                'service.demolitions' => 'Demolición',
                'service.construction' => 'Construcción',
                'service.permits' => 'Habilitaciones',
                'service.environment' => 'Medio ambiente / Seguridad e Higiene',
            ],
            'en' => [
                'nav.home' => 'Home',
                'nav.studio' => 'Studio',
                'nav.services' => 'Services',
                'nav.obra_viva' => 'Obra Viva',
                'nav.works' => 'Works',
                'nav.blog' => 'Blog',
                'nav.contact' => 'Contact',
                'nav.menu' => 'Menu',
                'nav.language' => 'Select language',
                'footer.map' => 'Map',
                'footer.lets_talk' => 'Let’s talk',
                'footer.services' => 'Services',
                'footer.social' => 'Social',
                'footer.contact' => 'Contact',
                'footer.credit' => 'Site designed and developed by',
                'footer.vanguard' => 'Forward-looking architecture',
                'article.back' => 'Back to all articles',
                'article.share' => 'Share article',
                'service.projects' => 'Project',
                'service.management' => 'Management',
                'service.demolitions' => 'Demolition',
                'service.construction' => 'Construction',
                'service.permits' => 'Permits',
                'service.environment' => 'Environment / Health and Safety',
            ],
            'pt' => [
                'nav.home' => 'Início',
                'nav.studio' => 'O estúdio',
                'nav.services' => 'Serviços',
                'nav.obra_viva' => 'Obra Viva',
                'nav.works' => 'Obras',
                'nav.blog' => 'Blog',
                'nav.contact' => 'Contato',
                'nav.menu' => 'Menu',
                'nav.language' => 'Selecionar idioma',
                'footer.map' => 'Mapa',
                'footer.lets_talk' => 'Vamos conversar',
                'footer.services' => 'Serviços',
                'footer.social' => 'Redes',
                'footer.contact' => 'Contato',
                'footer.credit' => 'Site desenhado e desenvolvido por',
                'footer.vanguard' => 'Arquitetura de vanguarda',
                'article.back' => 'Voltar à lista de artigos',
                'article.share' => 'Compartilhar artigo',
                'service.projects' => 'Projeto',
                'service.management' => 'Gerenciamento',
                'service.demolitions' => 'Demolição',
                'service.construction' => 'Construção',
                'service.permits' => 'Habilitações',
                'service.environment' => 'Meio ambiente / Segurança e Higiene',
            ],
        ];
    }
}

if (!function_exists('commar_t')) {
    function commar_t(string $key): string
    {
        $translations = commar_translations();
        $lang = commar_current_lang();

        return $translations[$lang][$key] ?? $translations['es'][$key] ?? $key;
    }
}

if (!function_exists('commar_nav_label')) {
    function commar_nav_label(string $label): string
    {
        $navMap = [
            'Inicio' => 'nav.home',
            'El estudio' => 'nav.studio',
            'Servicios' => 'nav.services',
            'Obra Viva' => 'nav.obra_viva',
            'Obras' => 'nav.works',
            'Blog' => 'nav.blog',
            'Contacto' => 'nav.contact',
        ];

        return isset($navMap[$label]) ? commar_t($navMap[$label]) : $label;
    }
}

if (!function_exists('commar_url')) {
    function commar_url(string $href): string
    {
        if ($href === '' || $href === '#') {
            return $href;
        }

        if (preg_match('#^(https?:|mailto:|tel:|//)#i', $href)) {
            return $href;
        }

        return commar_localized_path($href, commar_current_lang());
    }
}

if (!function_exists('commar_friendly_path')) {
    function commar_friendly_path(string $href): string
    {
        if ($href === '' || $href === '#' || preg_match('#^(https?:|mailto:|tel:|//)#i', $href)) {
            return $href;
        }

        $fragmentParts = explode('#', $href, 2);
        $pathAndQuery = $fragmentParts[0];
        $fragment = isset($fragmentParts[1]) ? '#' . $fragmentParts[1] : '';
        $queryParts = explode('?', $pathAndQuery, 2);
        $path = $queryParts[0];
        $query = isset($queryParts[1]) ? '?' . $queryParts[1] : '';
        $hasLeadingSlash = str_starts_with($path, '/');
        $normalizedPath = ltrim($path, '/');

        $routes = [
            'index.php' => '',
            'el-estudio.php' => 'el-estudio',
            'servicios.php' => 'servicios',
            'servicio-proyectos.php' => 'servicio-proyectos',
            'obra-viva.php' => 'obra-viva',
            'obras.php' => 'obras',
            'blog.php' => 'blog',
            'contacto.php' => 'contacto',
            'trabaja-con-nosotros.php' => 'trabaja-con-nosotros',
            'newsletter-gracias.php' => 'newsletter-gracias',
        ];

        if (!array_key_exists($normalizedPath, $routes)) {
            return $href;
        }

        $route = $routes[$normalizedPath];
        if ($route === '') {
            $route = $hasLeadingSlash ? '/' : './';
        } elseif ($hasLeadingSlash) {
            $route = '/' . $route;
        }

        return $route . $query . $fragment;
    }
}

if (!function_exists('commar_localized_path')) {
    function commar_localized_path(string $href, string $lang): string
    {
        if ($href === '' || $href === '#') {
            return $href;
        }

        if (preg_match('#^(https?:|mailto:|tel:|//)#i', $href)) {
            return $href;
        }

        $href = commar_friendly_path($href);

        if ($lang === 'es') {
            return $href;
        }

        $parts = explode('#', $href, 2);
        $pathAndQuery = $parts[0];
        $fragment = isset($parts[1]) ? '#' . $parts[1] : '';
        $separator = strpos($pathAndQuery, '?') !== false ? '&' : '?';

        return $pathAndQuery . $separator . 'lang=' . rawurlencode($lang) . $fragment;
    }
}

if (!function_exists('commar_language_url')) {
    function commar_language_url(string $lang): string
    {
        if (!array_key_exists($lang, commar_supported_languages())) {
            $lang = 'es';
        }

        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $currentPage = (string) parse_url($requestUri, PHP_URL_PATH);
        if ($currentPage === '') {
            $currentPage = commar_friendly_path(basename($_SERVER['PHP_SELF'] ?? 'index.php'));
        }

        $query = [];
        $requestQuery = parse_url($requestUri, PHP_URL_QUERY);
        if (is_string($requestQuery) && $requestQuery !== '') {
            parse_str($requestQuery, $query);
        }
        $query['lang'] = $lang;

        return $currentPage . '?' . http_build_query($query);
    }
}
