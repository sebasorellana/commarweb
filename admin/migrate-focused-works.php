<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/db.php';

commar_admin_require_login();

$featuredProjectsByLang = [
    'es' => [
        [
            'title' => 'Hospital Alemán',
            'category' => 'Salud',
            'summary' => 'Gestión de plano municipal de modificación y ampliación con demolición parcial, incluyendo presentación vía TAD, encomiendas, documentación, liquidación de derechos de obra, seguimiento de expediente, solicitudes de inspección y asesoramiento técnico normativo.',
            'img' => 'img/obras/hospital-aleman.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Alto Palermo',
            'category' => 'Comercial',
            'summary' => 'Gestión de permisos especiales ante organismos públicos: ingreso de bomba de hormigón a contramano y habilitación municipal de torre grúa, incluyendo armado de expediente y seguimiento hasta la aprobación.',
            'img' => 'img/obras/alto-palermo.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Barrio Rodrigo Bueno',
            'category' => 'Urbano',
            'summary' => 'Gestión integral de planos municipales, firmas profesionales, encomiendas, presentación vía TAD, seguimiento de expedientes e inspecciones AVO. Incluye obra nueva, modificaciones y relevamiento del playón gastronómico.',
            'img' => 'img/obras/barrio-rodrigo-bueno.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'EBA Coarco',
            'category' => 'Institucional',
            'summary' => 'Gestión integral de planos y trámites ante DGROC y DGFYCO: armado, presentación y seguimiento de planos de obra nueva, planos conforme a obra, documentación complementaria, encomiendas profesionales y solicitudes de AVOS e inspecciones.',
            'img' => 'img/obras/eba-coarco.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Movistar Arena',
            'category' => 'Equipamiento',
            'summary' => 'Intervención en un equipamiento urbano preparado para alto flujo y precisión operativa.',
            'img' => 'img/obras/movistar-arena.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
    ],
    'en' => [
        [
            'title' => 'Hospital Alemán',
            'category' => 'Healthcare',
            'summary' => 'Management of municipal plans for modification and expansion with partial demolition, including TAD submission, professional work orders, documentation, building fee settlement, file follow-up, inspection requests, and technical regulatory advice.',
            'img' => 'img/obras/hospital-aleman.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Alto Palermo',
            'category' => 'Commercial',
            'summary' => 'Management of special permits before public agencies: concrete pump entry against traffic flow and municipal authorization for a tower crane, including file preparation and follow-up through approval.',
            'img' => 'img/obras/alto-palermo.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Barrio Rodrigo Bueno',
            'category' => 'Urban',
            'summary' => 'Comprehensive management of municipal plans, professional signatures, work orders, TAD submission, file follow-up, and AVO inspections. Includes new construction, modifications, and survey of the gastronomic plaza.',
            'img' => 'img/obras/barrio-rodrigo-bueno.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'EBA Coarco',
            'category' => 'Institutional',
            'summary' => 'Comprehensive management of plans and procedures before DGROC and DGFYCO: preparation, submission, and follow-up of new construction plans, as-built plans, supplementary documentation, professional work orders, and AVOS and inspection requests.',
            'img' => 'img/obras/eba-coarco.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Movistar Arena',
            'category' => 'Venue',
            'summary' => 'Intervention in an urban venue designed for high traffic flow and operational precision.',
            'img' => 'img/obras/movistar-arena.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
    ],
    'pt' => [
        [
            'title' => 'Hospital Alemán',
            'category' => 'Saúde',
            'summary' => 'Gestão de planta municipal de modificação e ampliação com demolição parcial, incluindo apresentação via TAD, encomendas profissionais, documentação, liquidação de direitos de obra, acompanhamento do expediente, solicitações de inspeção e assessoria técnica normativa.',
            'img' => 'img/obras/hospital-aleman.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Alto Palermo',
            'category' => 'Comercial',
            'summary' => 'Gestão de permissões especiais junto a órgãos públicos: entrada de bomba de concreto na contramão e habilitação municipal de grua torre, incluindo montagem do expediente e acompanhamento até a aprovação.',
            'img' => 'img/obras/alto-palermo.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Barrio Rodrigo Bueno',
            'category' => 'Urbano',
            'summary' => 'Gestão integral de plantas municipais, assinaturas profissionais, encomendas, apresentação via TAD, acompanhamento de expedientes e inspeções AVO. Inclui obra nova, modificações e levantamento da praça gastronômica.',
            'img' => 'img/obras/barrio-rodrigo-bueno.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'EBA Coarco',
            'category' => 'Institucional',
            'summary' => 'Gestão integral de plantas e trâmites junto à DGROC e DGFYCO: preparação, apresentação e acompanhamento de plantas de obra nova, plantas conforme obra, documentação complementar, encomendas profissionais e solicitações de AVOS e inspeções.',
            'img' => 'img/obras/eba-coarco.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
        [
            'title' => 'Movistar Arena',
            'category' => 'Equipamento',
            'summary' => 'Intervenção em um equipamento urbano preparado para alto fluxo e precisão operacional.',
            'img' => 'img/obras/movistar-arena.jpg',
            'img_width' => 1920,
            'img_height' => 976,
        ],
    ],
];

$db = commar_db();
$db->beginTransaction();

try {
    $insertStatement = $db->prepare(
        'INSERT INTO commar_focused_works
            (lang, title, category, summary, image, image_width, image_height, display_order, created_at, updated_at)
        VALUES
            (:lang, :title, :category, :summary, :image, :image_width, :image_height, :display_order, :created_at, :updated_at)'
    );

    foreach ($featuredProjectsByLang as $lang => $projects) {
        $order = 0;
        foreach ($projects as $project) {
            $checkStmt = $db->prepare('SELECT id FROM commar_focused_works WHERE lang = :lang AND title = :title');
            $checkStmt->execute(['lang' => $lang, 'title' => $project['title']]);
            if ($checkStmt->fetch()) {
                echo "Obra '{$project['title']}' ({$lang}) ya existe. Omitiendo.<br>";
                continue;
            }

            $now = date('Y-m-d H:i:s');
            $insertStatement->execute([
                'lang' => $lang,
                'title' => $project['title'],
                'category' => $project['category'],
                'summary' => $project['summary'],
                'image' => $project['img'],
                'image_width' => $project['img_width'],
                'image_height' => $project['img_height'],
                'display_order' => $order,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $order++;
            echo "Obra migrada: '{$project['title']}' ({$lang}).<br>";
        }
    }

    $db->commit();
    echo "¡Migración de obras en foco completada con éxito!";

} catch (Exception $e) {
    $db->rollBack();
    echo "Ocurrió un error durante la migración: " . $e->getMessage();
}