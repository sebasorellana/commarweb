<?php
require_once __DIR__ . '/settings.php';

if (!function_exists('commar_default_team_members')) {
    function commar_default_team_members(): array
    {
        return [
            ['image' => 'img/romina-loconte.jpg', 'width' => 800, 'height' => 800, 'name' => 'Romina Lo Conte', 'role' => 'Presidente / CEO', 'linkedin' => '#'],
            ['image' => 'img/julian-parente.jpg', 'width' => 800, 'height' => 800, 'name' => 'Julian Parente', 'role' => 'Vicepresidente / Representante técnico', 'linkedin' => '#'],
            ['image' => 'img/belen-gomez.jpg', 'width' => 800, 'height' => 765, 'name' => 'Belén Gomez', 'role' => 'Gerente de Obras Nuevas y Habilitaciones', 'linkedin' => '#'],
            ['image' => 'img/geronimo-zoloaga.jpg', 'width' => 800, 'height' => 757, 'name' => 'Gerónimo Zoloaga', 'role' => 'Gerente de Ajustes y Finales', 'linkedin' => '#'],
            ['image' => 'img/juan-pugliese.jpg', 'width' => 800, 'height' => 775, 'name' => 'Juan P Pugliese', 'role' => 'Analista técnico', 'linkedin' => '#'],
            ['image' => 'img/agustina-freire.jpg', 'width' => 800, 'height' => 771, 'name' => 'Agustina Freire', 'role' => 'Jefatura de obra', 'linkedin' => '#'],
            ['image' => 'img/valentin-lobaccaro.jpg', 'width' => 776, 'height' => 775, 'name' => 'Valentin Lobaccaro', 'role' => 'Analista técnico', 'linkedin' => '#'],
            ['image' => 'img/agustina-futej.jpg', 'width' => 780, 'height' => 775, 'name' => 'Agustina Futej', 'role' => 'Analista técnico', 'linkedin' => '#'],
            ['image' => 'img/claudia-gatica.jpg', 'width' => 800, 'height' => 780, 'name' => 'Claudia Gatica', 'role' => 'Analista técnico', 'linkedin' => '#'],
            ['image' => 'img/kiara-battaglia.jpg', 'width' => 800, 'height' => 773, 'name' => 'Kiara Bataglia', 'role' => 'Gerente administrativa', 'linkedin' => '#'],
            ['image' => 'img/carina-gatica.jpg', 'width' => 800, 'height' => 800, 'name' => 'Carina Gatica', 'role' => 'Gerente RRHH', 'linkedin' => '#'],
            ['image' => 'img/nuria-zoloaga.jpg', 'width' => 727, 'height' => 777, 'name' => 'Nuria Zoloaga', 'role' => 'Asistente administrativo', 'linkedin' => '#'],
            ['image' => 'img/camila-lamuta.jpg', 'width' => 800, 'height' => 779, 'name' => 'Camila Lamuta', 'role' => 'Dra. Arquitectura legal', 'linkedin' => '#'],
        ];
    }
}

if (!function_exists('commar_normalize_team_member')) {
    function commar_normalize_team_member(array $member): ?array
    {
        $name = trim((string) ($member['name'] ?? trim((string) (($member['name'] ?? '') . ' ' . ($member['surname'] ?? '')))));
        $role = trim((string) ($member['role'] ?? $member['position'] ?? ''));
        $image = trim((string) ($member['image'] ?? ''));

        if ($name === '' && $role === '' && $image === '') {
            return null;
        }

        return [
            'image' => $image,
            'width' => max(0, (int) ($member['width'] ?? 0)),
            'height' => max(0, (int) ($member['height'] ?? 0)),
            'name' => $name,
            'role' => $role,
            'linkedin' => trim((string) ($member['linkedin'] ?? '#')) ?: '#',
        ];
    }
}

if (!function_exists('commar_team_members')) {
    function commar_team_members(): array
    {
        $stored = json_decode((string) commar_setting('team_members'), true);
        $members = is_array($stored) ? $stored : commar_default_team_members();
        $normalized = [];

        foreach ($members as $member) {
            if (!is_array($member)) {
                continue;
            }
            $normalizedMember = commar_normalize_team_member($member);
            if ($normalizedMember !== null) {
                $normalized[] = $normalizedMember;
            }
        }

        return count($normalized) > 0 ? $normalized : commar_default_team_members();
    }
}

if (!function_exists('commar_save_team_members')) {
    function commar_save_team_members(array $members): void
    {
        $normalized = [];
        foreach ($members as $member) {
            if (!is_array($member)) {
                continue;
            }
            $normalizedMember = commar_normalize_team_member($member);
            if ($normalizedMember !== null) {
                $normalized[] = $normalizedMember;
            }
        }

        commar_save_settings([
            'team_members' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
        ]);
    }
}
