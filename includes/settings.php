<?php
require_once __DIR__ . '/db.php';

if (!function_exists('commar_default_settings')) {
    function commar_default_settings(): array
    {
        return [
            'home_hero_image' => 'img/hero-home.jpg',
            'home_hero_width' => 1200,
            'home_hero_height' => 1800,
            'home_hero_images' => '',
            'home_hero_carousel_speed' => 5000,
            'home_hero_text_mode' => 'animated_static',
            'home_hero_animated_text' => 'arquitectura,pensamiento,estilo,personalización',
            'home_hero_static_text' => 'Radical',
            'home_hero_link_text' => 'Conocer COMMAR GROUP',
            'home_hero_link_url' => 'el-estudio.php',
            'contact_email' => 'info@commargroup.com.ar',
            'contact_address' => "Olazabal 1483, UF 708\nBelgrano, CABA",
            'contact_form_email' => '',
            'instagram_url' => '',
            'linkedin_url' => '',
            'whatsapp_number' => '5491100000000',
        ];
    }
}

if (!function_exists('commar_settings')) {
    function commar_settings(): array
    {
        $settings = commar_default_settings();
        $statement = commar_db()->query('SELECT setting_key, setting_value FROM commar_settings');

        foreach ($statement->fetchAll() as $row) {
            $settings[(string) $row['setting_key']] = (string) $row['setting_value'];
        }

        return $settings;
    }
}

if (!function_exists('commar_setting')) {
    function commar_setting(string $key): mixed
    {
        $settings = commar_settings();

        return $settings[$key] ?? commar_default_settings()[$key] ?? null;
    }
}

if (!function_exists('commar_save_settings')) {
    function commar_save_settings(array $settings): void
    {
        $statement = commar_db()->prepare(
            'INSERT INTO commar_settings (setting_key, setting_value, updated_at)
             VALUES (:setting_key, :setting_value, :updated_at)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)'
        );

        foreach ($settings as $key => $value) {
            $statement->execute([
                'setting_key' => (string) $key,
                'setting_value' => (string) $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
