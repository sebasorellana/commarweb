<?php
require_once __DIR__ . '/site.php';
require_once __DIR__ . '/db.php';

if (!function_exists('commar_slugify')) {
    function commar_slugify(string $value): string
    {
        $value = trim($value);
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = $normalized !== false ? $normalized : $value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'obra';
    }
}

if (!function_exists('commar_project_gallery_item')) {
    function commar_project_gallery_item(string $path, int $width, int $height, string $alt = ''): array
    {
        return [
            'path' => $path,
            'width' => $width,
            'height' => $height,
            'alt' => $alt,
        ];
    }
}

if (!function_exists('commar_seed_work_categories')) {
    function commar_seed_work_categories(): void
    {
        static $seeded = false;
        if ($seeded) {
            return;
        }

        $db = commar_db();
        $names = [];

        try {
            $existingCategories = $db->query("SELECT DISTINCT category FROM commar_works WHERE category <> ''")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($existingCategories as $categoryName) {
                $names[] = (string) $categoryName;
            }
        } catch (PDOException $exception) {
            // If the works table is not ready yet, static categories below are enough.
        }

        foreach (commar_static_projects() as $project) {
            $names[] = (string) ($project['category'] ?? '');
        }

        $names = array_values(array_unique(array_filter(array_map('trim', $names))));
        sort($names, SORT_NATURAL | SORT_FLAG_CASE);

        $statement = $db->prepare(
            'INSERT IGNORE INTO commar_work_categories (slug, name, display_order, created_at, updated_at)
             VALUES (:slug, :name, :display_order, :created_at, :updated_at)'
        );
        $now = date('Y-m-d H:i:s');

        foreach ($names as $index => $name) {
            $statement->execute([
                'slug' => commar_slugify($name),
                'name' => $name,
                'display_order' => ($index + 1) * 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $seeded = true;
    }
}

if (!function_exists('commar_admin_work_categories')) {
    function commar_admin_work_categories(): array
    {
        commar_seed_work_categories();

        $statement = commar_db()->query(
            'SELECT * FROM commar_work_categories ORDER BY display_order ASC, name ASC'
        );

        return $statement->fetchAll();
    }
}

if (!function_exists('commar_admin_work_category_by_id')) {
    function commar_admin_work_category_by_id(int $id): ?array
    {
        commar_seed_work_categories();

        $statement = commar_db()->prepare('SELECT * FROM commar_work_categories WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $category = $statement->fetch();

        return is_array($category) ? $category : null;
    }
}

if (!function_exists('commar_admin_save_work_category')) {
    function commar_admin_save_work_category(string $name, int $displayOrder = 0, int $id = 0): bool
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $db = commar_db();
        $slug = commar_slugify($name);
        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $existing = commar_admin_work_category_by_id($id);
            if (!$existing) {
                return false;
            }

            $oldName = (string) ($existing['name'] ?? '');
            $db->beginTransaction();

            try {
                $statement = $db->prepare(
                    'UPDATE commar_work_categories
                     SET slug = :slug, name = :name, display_order = :display_order, updated_at = :updated_at
                     WHERE id = :id'
                );
                $statement->execute([
                    'id' => $id,
                    'slug' => $slug,
                    'name' => $name,
                    'display_order' => $displayOrder,
                    'updated_at' => $now,
                ]);

                if ($oldName !== '' && $oldName !== $name) {
                    $workStatement = $db->prepare('UPDATE commar_works SET category = :new_name WHERE category = :old_name');
                    $workStatement->execute([
                        'new_name' => $name,
                        'old_name' => $oldName,
                    ]);
                }

                $db->commit();
                return true;
            } catch (Throwable $exception) {
                $db->rollBack();
                throw $exception;
            }
        }

        $statement = $db->prepare(
            'INSERT INTO commar_work_categories (slug, name, display_order, created_at, updated_at)
             VALUES (:slug, :name, :display_order, :created_at, :updated_at)'
        );

        return $statement->execute([
            'slug' => $slug,
            'name' => $name,
            'display_order' => $displayOrder,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

if (!function_exists('commar_admin_delete_work_category')) {
    function commar_admin_delete_work_category(int $id): bool
    {
        $category = commar_admin_work_category_by_id($id);
        if (!$category) {
            return false;
        }

        $usageStatement = commar_db()->prepare('SELECT COUNT(*) FROM commar_works WHERE category = :category AND status <> :deleted');
        $usageStatement->execute([
            'category' => (string) $category['name'],
            'deleted' => 'deleted',
        ]);
        if ((int) $usageStatement->fetchColumn() > 0) {
            return false;
        }

        $statement = commar_db()->prepare('DELETE FROM commar_work_categories WHERE id = :id');
        return $statement->execute(['id' => $id]);
    }
}

function commar_projects(): array
{
    static $projects = null;
    if ($projects !== null) {
        return $projects;
    }

    $db = commar_db();
    $statement = $db->query("SELECT * FROM commar_works WHERE status = 'published' ORDER BY title ASC");
    $works = $statement->fetchAll(PDO::FETCH_ASSOC);

    $projects = array_map(static function (array $work): array {
        return [
            'id' => str_pad((string) $work['id'], 2, '0', STR_PAD_LEFT),
            'slug' => (string) $work['slug'],
            'title' => (string) $work['title'],
            'category' => (string) $work['category'],
            'location' => (string) $work['location'],
            'year' => (string) $work['year'],
            'summary' => (string) $work['summary'],
            'img' => (string) $work['image'],
            'img_width' => (int) $work['image_width'],
            'img_height' => (int) $work['image_height'],
            'hero_alt' => (string) $work['hero_alt'],
            'intro' => (string) $work['intro'],
            'description' => json_decode((string) ($work['description_json'] ?? '[]'), true) ?: [],
            'metrics' => json_decode((string) ($work['metrics_json'] ?? '[]'), true) ?: [],
            'gallery' => [], // La galería se puede implementar después si es necesario
        ];
    }, $works);

    return $projects;
}

function commar_project_by_slug(string $slug): ?array
{
    foreach (commar_projects() as $project) {
        if ($project['slug'] === $slug) {
            return $project;
        }
    }
    return null;
}

if (!function_exists('commar_static_projects')) {
    function commar_static_projects(): array
    {
        $alphabetWorks = [
            ['Avenida Alvear', 'Residencial', 'Buenos Aires', '2024', 'img/proyecto-01.jpg', 1400, 933],
            ['Ágora Alsina', 'Institucional', 'Buenos Aires', '2025', 'img/proyecto-02.jpg', 1000, 1400],
            ['Altos Amenábar', 'Residencial', 'Belgrano', '2023', 'img/proyecto-03.jpg', 1400, 1400],
            ['Atelier Arevalo', 'Comercial', 'Palermo', '2022', 'img/proyecto-04.jpg', 1400, 933],
            ['Auditorio Anchorena', 'Cultural', 'Recoleta', '2026', 'img/proyecto-05.jpg', 1400, 933],
            ['Base Belgrano', 'Corporativo', 'Buenos Aires', '2023', 'img/proyecto-02.jpg', 1000, 1400],
            ['Centro Cobalto', 'Cultural', 'Córdoba', '2025', 'img/proyecto-03.jpg', 1400, 1400],
            ['Distrito Dorrego', 'Uso Mixto', 'Rosario', '2022', 'img/proyecto-04.jpg', 1400, 933],
            ['Estación Esmeralda', 'Institucional', 'Mendoza', '2026', 'img/proyecto-05.jpg', 1400, 933],
            ['Forum Figueroa', 'Corporativo', 'La Plata', '2024', 'img/proyecto-06.jpg', 1097, 1400],
            ['Galería Gurruchaga', 'Comercial', 'Buenos Aires', '2021', 'img/proyecto-01.jpg', 1400, 933],
            ['Hábitat Humboldt', 'Residencial', 'Mar del Plata', '2025', 'img/proyecto-02.jpg', 1000, 1400],
            ['Instituto Iberá', 'Institucional', 'Corrientes', '2023', 'img/proyecto-03.jpg', 1400, 1400],
            ['Jardín Juncal', 'Residencial', 'San Isidro', '2022', 'img/proyecto-04.jpg', 1400, 933],
            ['Kiosco Kavanagh', 'Comercial', 'Buenos Aires', '2024', 'img/proyecto-05.jpg', 1400, 933],
            ['Laboratorio Luro', 'Industrial', 'Bahía Blanca', '2026', 'img/proyecto-06.jpg', 1097, 1400],
            ['Mercado Malabia', 'Comercial', 'Buenos Aires', '2021', 'img/proyecto-01.jpg', 1400, 933],
            ['Nave Nogoyá', 'Industrial', 'Entre Ríos', '2025', 'img/proyecto-02.jpg', 1000, 1400],
            ['Observatorio Olivos', 'Institucional', 'Vicente López', '2023', 'img/proyecto-03.jpg', 1400, 1400],
            ['Pasaje Palermo', 'Uso Mixto', 'Buenos Aires', '2024', 'img/proyecto-04.jpg', 1400, 933],
            ['Quinta Quirno', 'Residencial', 'San Fernando', '2022', 'img/proyecto-05.jpg', 1400, 933],
            ['Residencia Rivera', 'Residencial', 'Neuquén', '2026', 'img/proyecto-06.jpg', 1097, 1400],
            ['Sede Serrano', 'Corporativo', 'Buenos Aires', '2025', 'img/proyecto-01.jpg', 1400, 933],
            ['Taller Thames', 'Industrial', 'Buenos Aires', '2023', 'img/proyecto-02.jpg', 1000, 1400],
            ['Unidad Uspallata', 'Institucional', 'Mendoza', '2024', 'img/proyecto-03.jpg', 1400, 1400],
            ['Vivienda Vera', 'Residencial', 'Tigre', '2021', 'img/proyecto-04.jpg', 1400, 933],
            ['Warehouse Wilde', 'Industrial', 'Avellaneda', '2026', 'img/proyecto-05.jpg', 1400, 933],
            ['Xiloteca Xumek', 'Cultural', 'San Luis', '2025', 'img/proyecto-06.jpg', 1097, 1400],
            ['Yard Yrigoyen', 'Uso Mixto', 'Lanús', '2022', 'img/proyecto-01.jpg', 1400, 933],
            ['Zócalo Zapiola', 'Comercial', 'Buenos Aires', '2024', 'img/proyecto-02.jpg', 1000, 1400],
        ];

        $projects = [];
        foreach ($alphabetWorks as $index => $work) {
            [$title, $category, $location, $year, $image, $imageWidth, $imageHeight] = $work;
            $projects[] = [
                'id' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'slug' => commar_slugify($title),
                'title' => $title,
                'category' => $category,
                'location' => $location,
                'year' => $year,
                'summary' => 'Obra de referencia para el directorio alfabético, con enfoque en coordinación técnica, materialidad y resolución integral.',
                'img' => $image,
                'img_width' => $imageWidth,
                'img_height' => $imageHeight,
                'hero_alt' => $title . ', obra de COMMAR GROUP',
                'gallery' => [
                    commar_project_gallery_item($image, $imageWidth, $imageHeight, $title . ', obra de COMMAR GROUP'),
                ],
                'intro' => $title . ' integra arquitectura, gestión técnica y ejecución ordenada para resolver un programa de ' . strtolower($category) . ' con una respuesta clara y contemporánea.',
                'description' => [
                    'La obra organiza programa, estructura y materialidad con una estrategia de ejecución pensada para reducir interferencias durante el proceso constructivo.',
                    'El desarrollo prioriza documentación clara, coordinación entre equipos y una lectura espacial consistente desde el acceso hasta las áreas principales.',
                ],
                'metrics' => [
                    'Programa' => $category,
                    'Superficie' => number_format(720 + ($index * 185), 0, ',', '.') . ' m²',
                    'Estado' => $index % 3 === 0 ? 'En desarrollo' : 'Construido',
                    'Equipo' => 'Arquitectura, documentación y dirección de obra',
                ],
            ];
        }

        return array_merge($projects, [
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
                'gallery' => [
                    commar_project_gallery_item('img/proyecto-01.jpg', 1400, 933, 'Vista exterior de Casa Atlas, obra residencial de COMMAR GROUP'),
                    commar_project_gallery_item('img/proyecto-02.jpg', 1000, 1400, 'Interior de Casa Atlas'),
                    commar_project_gallery_item('img/proyecto-03.jpg', 1400, 1400, 'Detalle material de Casa Atlas'),
                ],
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
                'gallery' => [
                    commar_project_gallery_item('img/proyecto-02.jpg', 1000, 1400, 'Pabellón Delta, edificio institucional de COMMAR GROUP'),
                ],
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
                'gallery' => [
                    commar_project_gallery_item('img/proyecto-03.jpg', 1400, 1400, 'Torre Prisma, obra de uso mixto de COMMAR GROUP'),
                ],
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
                'gallery' => [
                    commar_project_gallery_item('img/proyecto-04.jpg', 1400, 933, 'Refugio Litoral, proyecto de hospitalidad de COMMAR GROUP'),
                ],
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
                'gallery' => [
                    commar_project_gallery_item('img/proyecto-05.jpg', 1400, 933, 'Patio Umbral, proyecto cultural de COMMAR GROUP'),
                ],
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
                'gallery' => [
                    commar_project_gallery_item('img/proyecto-06.jpg', 1097, 1400, 'Núcleo Basalto, edificio corporativo de COMMAR GROUP'),
                ],
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
        ]);
    }
}

if (!function_exists('commar_normalize_work_row')) {
    function commar_normalize_work_row(array $row, int $index = 0): array
    {
        $description = json_decode((string) ($row['description_json'] ?? '[]'), true);
        $metrics = json_decode((string) ($row['metrics_json'] ?? '[]'), true);
        $gallery = json_decode((string) ($row['gallery_json'] ?? '[]'), true);
        $image = (string) ($row['image'] ?? '');
        $imageWidth = (int) ($row['image_width'] ?? 0);
        $imageHeight = (int) ($row['image_height'] ?? 0);
        $heroAlt = (string) ($row['hero_alt'] ?? '');
        $gallery = is_array($gallery) ? array_values(array_filter($gallery, static function ($item): bool {
            return is_array($item) && trim((string) ($item['path'] ?? '')) !== '';
        })) : [];

        if (empty($gallery) && $image !== '') {
            $gallery[] = commar_project_gallery_item($image, $imageWidth, $imageHeight, $heroAlt);
        }

        return [
            'id' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
            'db_id' => (int) ($row['id'] ?? 0),
            'slug' => (string) ($row['slug'] ?? ''),
            'title' => (string) ($row['title'] ?? ''),
            'category' => (string) ($row['category'] ?? ''),
            'location' => (string) ($row['location'] ?? ''),
            'year' => (string) ($row['year'] ?? ''),
            'summary' => (string) ($row['summary'] ?? ''),
            'img' => $image,
            'img_width' => $imageWidth,
            'img_height' => $imageHeight,
            'hero_alt' => $heroAlt,
            'gallery' => $gallery,
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
        $existingSlugs = $db->query('SELECT slug FROM commar_works')->fetchAll(PDO::FETCH_COLUMN);
        $existingSlugs = is_array($existingSlugs) ? array_flip(array_map('strval', $existingSlugs)) : [];

        $statement = $db->prepare(
            'INSERT INTO commar_works
             (slug, title, category, location, year, summary, image, image_width, image_height, gallery_json, hero_alt, intro, description_json, metrics_json, status, created_at, updated_at)
             VALUES
             (:slug, :title, :category, :location, :year, :summary, :image, :image_width, :image_height, :gallery_json, :hero_alt, :intro, :description_json, :metrics_json, :status, :created_at, :updated_at)'
        );
        $now = date('Y-m-d H:i:s');

        foreach (commar_static_projects() as $project) {
            if (isset($existingSlugs[$project['slug']])) {
                continue;
            }

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
                'gallery_json' => json_encode($project['gallery'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'hero_alt' => $project['hero_alt'],
                'intro' => $project['intro'],
                'description_json' => json_encode($project['description'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'metrics_json' => json_encode($project['metrics'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $existingSlugs[$project['slug']] = true;
        }
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
