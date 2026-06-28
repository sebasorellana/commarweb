<?php
require_once __DIR__ . '/site.php';

if (!function_exists('commar_static_projects')) {
    function commar_static_projects(): array
    {
        return [
            [
                'id' => '01',
                'slug' => 'casa-atlas',
                'title' => 'Casa Atlas',
                'category' => 'Residencial',
                'location' => 'Buenos Aires',
                'year' => '2024',
                'summary' => 'Volúmenes puros, sombra controlada y una envolvente mineral pensada para vivir la luz como materia.',
                'img' => 'img/proyecto-01.jpg',
                'img_width' => 1400,
                'img_height' => 933,
                'hero_alt' => 'Vista exterior de Casa Atlas, obra residencial de COMMAR GROUP',
                'intro' => 'Casa Atlas explora una arquitectura doméstica de planos rotundos, patios contenidos y una materialidad mineral que filtra la luz como si fuera una segunda piel.',
                'description' => [
                    'La vivienda se organiza como una secuencia de vacíos intermedios, terrazas contenidas y recintos que alternan exposición y refugio. La estructura no se oculta: ordena la experiencia espacial y define la identidad de la obra.',
                    'El proyecto trabaja con sombra profunda, aperturas controladas y una relación precisa entre interior y exterior, buscando que cada desplazamiento dentro de la casa produzca una variación sensible de luz, temperatura y textura.',
                ],
                'metrics' => [
                    'Programa' => 'Vivienda unifamiliar',
                    'Superficie' => '780 m²',
                    'Estado' => 'Construido',
                    'Equipo' => 'Arquitectura, interiorismo y dirección de obra',
                ],
            ],
            [
                'id' => '02',
                'slug' => 'pabellon-delta',
                'title' => 'Pabellón Delta',
                'category' => 'Institucional',
                'location' => 'Montevideo',
                'year' => '2023',
                'summary' => 'Una pieza cívica de hormigón y vidrio que articula circulación, vacío y contemplación urbana.',
                'img' => 'img/proyecto-02.jpg',
                'img_width' => 1000,
                'img_height' => 1400,
                'hero_alt' => 'Pabellón Delta, edificio institucional de COMMAR GROUP',
                'intro' => 'Pabellón Delta funciona como una infraestructura cívica abierta, donde la estructura de hormigón y la envolvente vidriada construyen una relación directa con el espacio público.',
                'description' => [
                    'El edificio se concibe como un sistema de plataformas, núcleos y visuales cruzadas que permite orientar el flujo de usuarios sin sacrificar claridad espacial. Cada gesto formal responde a necesidades de circulación y permanencia.',
                    'La obra enfatiza la condición pública del programa con un gran vacío central, transparencias graduadas y una secuencia de accesos pensada para producir continuidad entre la ciudad y el interior.',
                ],
                'metrics' => [
                    'Programa' => 'Pabellón institucional',
                    'Superficie' => '1.240 m²',
                    'Estado' => 'Construido',
                    'Equipo' => 'Diseño arquitectónico y coordinación técnica',
                ],
            ],
            [
                'id' => '03',
                'slug' => 'torre-prisma',
                'title' => 'Torre Prisma',
                'category' => 'Uso Mixto',
                'location' => 'Madrid',
                'year' => '2025',
                'summary' => 'Fachadas tensas, geometría afilada y un sistema estructural expuesto como manifiesto visual.',
                'img' => 'img/proyecto-03.jpg',
                'img_width' => 1400,
                'img_height' => 1400,
                'hero_alt' => 'Torre Prisma, obra de uso mixto de COMMAR GROUP',
                'intro' => 'Torre Prisma desarrolla una envolvente aguda y precisa, donde la estructura y la piel exterior construyen una presencia vertical deliberadamente intensa.',
                'description' => [
                    'La torre combina usos comerciales, áreas de trabajo y espacios de estancia en una sección vertical de alta densidad. La lectura del edificio cambia con la distancia, pasando de un volumen abstracto a un ensamblaje visible de planos y aristas.',
                    'El proyecto utiliza el ritmo estructural como lenguaje compositivo y evita recursos decorativos superfluos. La identidad se produce desde la lógica constructiva, la profundidad de fachada y la exposición controlada del sistema portante.',
                ],
                'metrics' => [
                    'Programa' => 'Uso mixto',
                    'Superficie' => '9.850 m²',
                    'Estado' => 'En desarrollo',
                    'Equipo' => 'Arquitectura, fachada y consultoría espacial',
                ],
            ],
            [
                'id' => '04',
                'slug' => 'refugio-litoral',
                'title' => 'Refugio Litoral',
                'category' => 'Hospitalidad',
                'location' => 'São Paulo',
                'year' => '2022',
                'summary' => 'Una secuencia de planos horizontales y patios silenciosos que funden paisaje, agua y materia.',
                'img' => 'img/proyecto-04.jpg',
                'img_width' => 1400,
                'img_height' => 933,
                'hero_alt' => 'Refugio Litoral, proyecto de hospitalidad de COMMAR GROUP',
                'intro' => 'Refugio Litoral propone una arquitectura de hospitalidad silenciosa, apoyada en horizontales largas, patios de descanso y una relación directa con el agua y el paisaje cercano.',
                'description' => [
                    'El proyecto organiza sus programas como episodios sucesivos entre jardines, estanques y planos cubiertos. La experiencia está construida desde la pausa, la sombra y la continuidad material entre interior y exterior.',
                    'La intervención prioriza atmósferas contenidas y una lectura casi monástica del lujo: luz indirecta, texturas honestas y una composición que evita el gesto espectacular para trabajar desde la duración de la experiencia.',
                ],
                'metrics' => [
                    'Programa' => 'Hospitalidad',
                    'Superficie' => '2.460 m²',
                    'Estado' => 'Construido',
                    'Equipo' => 'Arquitectura y experiencia interior',
                ],
            ],
            [
                'id' => '05',
                'slug' => 'patio-umbral',
                'title' => 'Patio Umbral',
                'category' => 'Cultural',
                'location' => 'Ciudad de México',
                'year' => '2021',
                'summary' => 'Un sistema de muros, sombras y vacíos intermedios que transforma el recorrido en una experiencia sensorial.',
                'img' => 'img/proyecto-05.jpg',
                'img_width' => 1400,
                'img_height' => 933,
                'hero_alt' => 'Patio Umbral, proyecto cultural de COMMAR GROUP',
                'intro' => 'Patio Umbral convierte el recorrido en la materia principal del proyecto, articulando muros, sombras profundas y vacíos intermedios de distinta escala.',
                'description' => [
                    'La pieza cultural se construye como una secuencia de compresiones y aperturas. Cada transición reordena la percepción del visitante y produce pausas deliberadas antes de los espacios de mayor intensidad programática.',
                    'La arquitectura trabaja sobre la idea de umbral permanente: no hay un único acceso simbólico, sino una cadena de aproximaciones donde materia, temperatura y sonido participan activamente en la experiencia.',
                ],
                'metrics' => [
                    'Programa' => 'Centro cultural',
                    'Superficie' => '1.980 m²',
                    'Estado' => 'Construido',
                    'Equipo' => 'Arquitectura, museografía y espacialidad pública',
                ],
            ],
            [
                'id' => '06',
                'slug' => 'nucleo-basalto',
                'title' => 'Núcleo Basalto',
                'category' => 'Corporativo',
                'location' => 'Santiago',
                'year' => '2026',
                'summary' => 'Una pieza monolítica de vidrio oscuro y piedra volcánica donde la estructura define la identidad del edificio.',
                'img' => 'img/proyecto-06.jpg',
                'img_width' => 1097,
                'img_height' => 1400,
                'hero_alt' => 'Núcleo Basalto, edificio corporativo de COMMAR GROUP',
                'intro' => 'Núcleo Basalto desarrolla una presencia corporativa densa y contenida, basada en piedra volcánica, vidrio oscuro y una expresión estructural deliberadamente monolítica.',
                'description' => [
                    'El edificio se organiza en torno a un núcleo central que concentra servicios, estructura y circulación, liberando perímetros de trabajo abiertos y altamente adaptables. La imagen exterior traduce esa lógica interna sin concesiones.',
                    'La materialidad busca transmitir precisión y permanencia. Lejos de la estética corporativa genérica, el proyecto construye identidad desde la masa, la profundidad y la lectura frontal de su sistema constructivo.',
                ],
                'metrics' => [
                    'Programa' => 'Edificio corporativo',
                    'Superficie' => '6.340 m²',
                    'Estado' => 'En desarrollo',
                    'Equipo' => 'Arquitectura, workplace y documentación ejecutiva',
                ],
            ],
        ];
    }
}

if (!function_exists('commar_normalize_work_row')) {
    function commar_normalize_work_row(array $row, int $index = 0): array
    {
        $description = json_decode((string) ($row['description_json'] ?? '[]'), true);
        $metrics = json_decode((string) ($row['metrics_json'] ?? '[]'), true);

        return [
            'id' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'db_id' => (int) ($row['id'] ?? 0),
            'slug' => (string) ($row['slug'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'category' => (string) ($row['category'] ?? ''),
            'location' => (string) ($row['location'] ?? ''),
            'year' => (string) ($row['year'] ?? ''),
            'summary' => (string) ($row['summary'] ?? ''),
            'img' => (string) ($row['image'] ?? ''),
            'img_width' => (int) ($row['image_width'] ?? 0),
            'img_height' => (int) ($row['image_height'] ?? 0),
            'hero_alt' => (string) ($row['hero_alt'] ?? ''),
            'intro' => (string) ($row['intro'] ?? ''),
            'description' => is_array($description) ? array_values(array_filter(array_map('strval', $description))) : [],
            'metrics' => is_array($metrics) ? $metrics : [],
        ];
    }
}

if (!function_exists('commar_seed_static_projects')) {
    function commar_seed_static_projects(): void
    {
        $db = commar_db();
        $count = (int) $db->query('SELECT COUNT(*) FROM commar_works')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $statement = $db->prepare(
            'INSERT INTO commar_works
             (slug, title, category, location, year, summary, image, image_width, image_height, hero_alt, intro, description_json, metrics_json, status, created_at, updated_at)
             VALUES
             (:slug, :title, :category, :location, :year, :summary, :image, :image_width, :image_height, :hero_alt, :intro, :description_json, :metrics_json, :status, :created_at, :updated_at)'
        );
        $now = date('Y-m-d H:i:s');

        foreach (commar_static_projects() as $project) {
            $statement->execute([
                'slug' => $project['slug'],
                'title' => $project['title'],
                'category' => $project['category'],
                'location' => $project['location'],
                'year' => $project['year'],
                'summary' => $project['summary'],
                'image' => $project['img'],
                'image_width' => (int) $project['img_width'],
                'image_height' => (int) $project['img_height'],
                'hero_alt' => $project['hero_alt'],
                'intro' => $project['intro'],
                'description_json' => json_encode($project['description'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'metrics_json' => json_encode($project['metrics'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}

if (!function_exists('commar_projects')) {
    function commar_projects(): array
    {
        commar_seed_static_projects();

        $statement = commar_db()->query(
            "SELECT * FROM commar_works WHERE status = 'published' ORDER BY title ASC"
        );
        $projects = [];

        foreach ($statement->fetchAll() as $index => $row) {
            $projects[] = commar_normalize_work_row($row, $index);
        }

        return $projects;
    }
}

if (!function_exists('commar_admin_projects')) {
    function commar_admin_projects(): array
    {
        commar_seed_static_projects();

        $statement = commar_db()->query(
            "SELECT * FROM commar_works WHERE status <> 'deleted' ORDER BY title ASC"
        );
        return $statement->fetchAll();
    }
}

if (!function_exists('commar_admin_project_by_id')) {
    function commar_admin_project_by_id(int $id): ?array
    {
        commar_seed_static_projects();

        $statement = commar_db()->prepare("SELECT * FROM commar_works WHERE id = :id AND status <> 'deleted' LIMIT 1");
        $statement->execute(['id' => $id]);
        $project = $statement->fetch();

        return is_array($project) ? $project : null;
    }
}

if (!function_exists('commar_project_by_slug')) {
    function commar_project_by_slug(string $slug): ?array
    {
        foreach (commar_projects() as $project) {
            if ($project['slug'] === $slug) {
                return $project;
            }
        }

        return null;
    }
}
