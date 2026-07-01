<?php
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/settings.php';

if (!function_exists('commar_default_menu_items')) {
    function commar_default_menu_items(string $location): array
    {
        $items = [
            'header' => [
                ['label' => commar_t('nav.home'), 'href' => 'index.php', 'enabled' => true],
                ['label' => commar_t('nav.studio'), 'href' => 'el-estudio.php', 'enabled' => true],
                ['label' => commar_t('nav.services'), 'href' => 'servicios.php', 'enabled' => true],
                ['label' => commar_t('nav.obra_viva'), 'href' => 'obra-viva.php', 'enabled' => true],
                ['label' => commar_t('nav.works'), 'href' => 'obras.php', 'enabled' => true],
                ['label' => commar_t('nav.blog'), 'href' => 'blog.php', 'enabled' => true],
                ['label' => commar_t('nav.contact'), 'href' => 'contacto.php', 'enabled' => true],
            ],
            'footer' => [
                ['label' => commar_t('nav.home'), 'href' => 'index.php', 'enabled' => true],
                ['label' => commar_t('nav.studio'), 'href' => 'el-estudio.php', 'enabled' => true],
                ['label' => commar_t('nav.services'), 'href' => 'servicios.php', 'enabled' => true],
                ['label' => commar_t('nav.obra_viva'), 'href' => 'obra-viva.php', 'enabled' => true],
                ['label' => commar_t('nav.works'), 'href' => 'obras.php', 'enabled' => true],
                ['label' => commar_t('nav.blog'), 'href' => 'blog.php', 'enabled' => true],
                ['label' => 'Trabajá con nosotros', 'href' => 'trabaja-con-nosotros.php', 'enabled' => true, 'requires_active_jobs' => true],
                ['label' => commar_t('nav.contact'), 'href' => 'contacto.php', 'enabled' => true],
            ],
        ];

        return $items[$location] ?? [];
    }
}

if (!function_exists('commar_normalize_menu_items')) {
    function commar_menu_item_is_jobs_link(string $label, string $href): bool
    {
        $value = commar_text_lower($label . ' ' . $href);

        return str_contains($value, 'trabaja-con-nosotros')
            || str_contains($value, 'trabajá con nosotros')
            || str_contains($value, 'trabaja con nosotros');
    }

    function commar_normalize_menu_items(array $items, string $location): array
    {
        $normalized = [];
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = trim((string) ($item['label'] ?? ''));
            $href = trim((string) ($item['href'] ?? ''));
            if ($label === '' || $href === '') {
                continue;
            }

            $normalized[] = [
                'label' => commar_text_substr($label, 0, 80),
                'href' => commar_text_substr($href, 0, 255),
                'enabled' => !empty($item['enabled']),
                'order' => (int) ($item['order'] ?? ($index + 1)),
                'requires_active_jobs' => $location === 'footer' && commar_menu_item_is_jobs_link($label, $href) && !empty($item['requires_active_jobs']),
            ];
        }

        usort($normalized, static fn(array $a, array $b): int => ($a['order'] <=> $b['order']) ?: strcmp($a['label'], $b['label']));

        return array_map(static function (array $item, int $index): array {
            $item['order'] = $index + 1;
            return $item;
        }, $normalized, array_keys($normalized));
    }
}

if (!function_exists('commar_menu_items')) {
    function commar_menu_items(string $location, bool $onlyEnabled = true): array
    {
        $settingKey = $location . '_menu_items';
        $stored = json_decode((string) commar_setting($settingKey), true);
        $items = is_array($stored) ? $stored : commar_default_menu_items($location);
        $items = commar_normalize_menu_items($items, $location);

        if ($onlyEnabled) {
            $items = array_values(array_filter($items, static fn(array $item): bool => !empty($item['enabled'])));
        }

        return $items;
    }
}

if (!function_exists('commar_save_menu_items')) {
    function commar_save_menu_items(string $location, array $items): void
    {
        $items = commar_normalize_menu_items($items, $location);
        commar_save_settings([
            $location . '_menu_items' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
        ]);
    }
}
