<?php
require_once __DIR__ . '/settings.php';

if (!function_exists('commar_default_page_heroes')) {
    function commar_default_page_heroes(): array
    {
        return [
            'el_estudio' => [
                'label' => 'El estudio',
                'path' => 'el-estudio.php',
                'image' => 'img/fullteam.jpg',
                'width' => 3020,
                'height' => 1480,
                'kicker' => 'El estudio',
                'title' => 'Lo que somos',
                'intro' => 'Somos una compañía formada por profesionales de distintas extracciones, que componen un grupo de trabajo multidisciplinario, enfocados en el compromiso, el profesionalismo y la pasión por los métodos y las formas.',
            ],
            'servicios' => [
                'label' => 'Servicios',
                'path' => 'servicios.php',
                'image' => 'img/proyecto-01.jpg',
                'width' => 1400,
                'height' => 933,
                'kicker' => 'Servicios',
                'title' => "Soluciones integrales\npara proyectos que\nexigen precisión.",
                'intro' => 'Acompañamos cada etapa con dirección técnica, documentación clara y una gestión coordinada entre proyecto, obra, normativa, ambiente y seguridad.',
            ],
            'servicio_proyectos' => [
                'label' => 'Servicio Proyecto',
                'path' => 'servicio-proyectos.php',
                'image' => 'img/proyecto-01.jpg',
                'width' => 1400,
                'height' => 933,
                'kicker' => 'Servicio // 01',
                'title' => 'Proyecto',
                'intro' => 'Convertimos una necesidad inicial en documentación clara, decisiones coordinadas y una base técnica sólida para avanzar hacia obra con menos incertidumbre.',
            ],
            'obra_viva' => [
                'label' => 'Obra Viva',
                'path' => 'obra-viva.php',
                'image' => 'img/obras/eba-coarco.jpg',
                'width' => 1920,
                'height' => 976,
                'kicker' => 'Solución 360° para obras',
                'title' => 'Obra Viva',
                'intro' => 'Gestión técnico-administrativa de obras en CABA. Integramos trámites, documentación y cumplimiento normativo para que tu obra avance sin interrupciones.',
            ],
            'blog' => [
                'label' => 'Blog',
                'path' => 'blog.php',
                'image' => 'img/reunion2.jpg',
                'width' => 2000,
                'height' => 1333,
                'kicker' => 'Blog',
                'title' => 'Artículos de COMMAR GROUP',
                'intro' => 'Ideas y criterios prácticos sobre arquitectura, construcción, gestión de obra, documentación técnica y medio ambiente.',
            ],
            'contacto' => [
                'label' => 'Contacto',
                'path' => 'contacto.php',
                'image' => 'img/reunion.jpg',
                'width' => 2000,
                'height' => 1333,
                'kicker' => 'Contacto',
                'title' => 'Hablemos de tu próximo proyecto.',
                'intro' => 'Completá el formulario y seleccioná el área de consulta para que podamos derivarla al equipo correspondiente.',
            ],
            'trabaja' => [
                'label' => 'Trabajá con nosotros',
                'path' => 'trabaja-con-nosotros.php',
                'image' => 'img/fullteam.jpg',
                'width' => 3020,
                'height' => 1480,
                'kicker' => 'Trabajá con nosotros',
                'title' => 'Búsquedas laborales activas',
                'intro' => 'Conocé las oportunidades abiertas y enviá tu CV para que el equipo de COMMAR GROUP pueda evaluarlo.',
            ],
        ];
    }
}

if (!function_exists('commar_normalize_page_hero')) {
    function commar_normalize_page_hero(array $hero, array $defaults): array
    {
        return [
            'label' => trim((string) ($defaults['label'] ?? $hero['label'] ?? '')),
            'path' => trim((string) ($defaults['path'] ?? $hero['path'] ?? '')),
            'image' => trim((string) ($hero['image'] ?? $defaults['image'] ?? '')),
            'width' => max(0, (int) ($hero['width'] ?? $defaults['width'] ?? 0)),
            'height' => max(0, (int) ($hero['height'] ?? $defaults['height'] ?? 0)),
            'kicker' => trim((string) ($hero['kicker'] ?? $defaults['kicker'] ?? '')),
            'title' => trim((string) ($hero['title'] ?? $defaults['title'] ?? '')),
            'intro' => trim((string) ($hero['intro'] ?? $defaults['intro'] ?? '')),
        ];
    }
}

if (!function_exists('commar_page_heroes')) {
    function commar_page_heroes(): array
    {
        $defaults = commar_default_page_heroes();
        $stored = json_decode((string) commar_setting('page_heroes'), true);
        $stored = is_array($stored) ? $stored : [];
        $heroes = [];

        foreach ($defaults as $key => $defaultHero) {
            $storedHero = $stored[$key] ?? [];
            $heroes[$key] = commar_normalize_page_hero(is_array($storedHero) ? $storedHero : [], $defaultHero);
        }

        return $heroes;
    }
}

if (!function_exists('commar_page_hero')) {
    function commar_page_hero(string $key): array
    {
        $heroes = commar_page_heroes();
        $defaults = commar_default_page_heroes();

        return $heroes[$key] ?? commar_normalize_page_hero([], $defaults[$key] ?? []);
    }
}

if (!function_exists('commar_save_page_heroes')) {
    function commar_save_page_heroes(array $heroes): void
    {
        $defaults = commar_default_page_heroes();
        $normalized = [];

        foreach ($defaults as $key => $defaultHero) {
            $hero = $heroes[$key] ?? [];
            $normalized[$key] = commar_normalize_page_hero(is_array($hero) ? $hero : [], $defaultHero);
        }

        commar_save_settings([
            'page_heroes' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
        ]);
    }
}
