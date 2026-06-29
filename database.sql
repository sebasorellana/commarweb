CREATE DATABASE IF NOT EXISTS `commar` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `commar`;

CREATE TABLE IF NOT EXISTS `commar_articles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(180) NOT NULL,
    `title` VARCHAR(180) NOT NULL,
    `description` LONGTEXT NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `year` VARCHAR(4) NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `image_width` INT UNSIGNED NOT NULL DEFAULT 1400,
    `image_height` INT UNSIGNED NOT NULL DEFAULT 933,
    `content_html` LONGTEXT NULL,
    `content_json` LONGTEXT NOT NULL,
    `gallery_json` LONGTEXT NULL,
    `youtube_url` VARCHAR(255) NOT NULL DEFAULT '',
    `tags_json` LONGTEXT NULL,
    `status` ENUM('draft', 'published', 'deleted') NOT NULL DEFAULT 'published',
    `published_at` DATETIME NULL,
    `updated_at` DATETIME NOT NULL,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_commar_articles_slug` (`slug`),
    KEY `idx_commar_articles_status_published` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `commar_articles`
    ADD COLUMN IF NOT EXISTS `tags_json` LONGTEXT NULL AFTER `gallery_json`;

ALTER TABLE `commar_articles`
    ADD COLUMN IF NOT EXISTS `youtube_url` VARCHAR(255) NOT NULL DEFAULT '' AFTER `gallery_json`;

ALTER TABLE `commar_articles`
    MODIFY COLUMN `description` LONGTEXT NOT NULL;

CREATE TABLE IF NOT EXISTS `commar_settings` (
    `setting_key` VARCHAR(120) NOT NULL,
    `setting_value` LONGTEXT NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `commar_media` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `path` VARCHAR(255) NOT NULL,
    `type` VARCHAR(40) NOT NULL DEFAULT 'image',
    `alt` VARCHAR(255) NOT NULL DEFAULT '',
    `width` INT UNSIGNED NOT NULL DEFAULT 0,
    `height` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_commar_media_path` (`path`),
    KEY `idx_commar_media_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `commar_newsletter_submissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `source` VARCHAR(80) NOT NULL DEFAULT 'website',
    `page_url` VARCHAR(500) NOT NULL DEFAULT '',
    `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
    `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
    `submitted_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_commar_newsletter_email` (`email`),
    KEY `idx_commar_newsletter_submitted` (`submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `commar_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('home_hero_image', 'img/hero-home.jpg', NOW()),
('home_hero_width', '1200', NOW()),
('home_hero_height', '1800', NOW())
ON DUPLICATE KEY UPDATE `setting_key` = VALUES(`setting_key`);

INSERT INTO `commar_articles` (`slug`, `title`, `description`, `category`, `year`, `image`, `image_width`, `image_height`, `content_json`, `gallery_json`, `tags_json`, `status`, `published_at`, `updated_at`) VALUES
('como-anticipar-decisiones-criticas-antes-de-iniciar-una-obra', 'Cómo anticipar decisiones críticas antes de iniciar una obra', 'Anticipar decisiones críticas antes de construir permite reducir riesgos, evitar costos imprevistos y lograr que una obra avance con mayor orden, seguridad y eficiencia.', 'Arquitectura', '2026', 'img/proyecto-01.jpg', 1400, 933, '["Iniciar una obra no comienza el día en que llegan los materiales o se instala el obrador. En realidad, una obra empieza mucho antes: en la etapa de análisis, planificación y toma de decisiones. Anticipar los aspectos críticos antes de construir permite reducir riesgos, evitar costos imprevistos y lograr que el proyecto avance con mayor orden, seguridad y eficiencia.","Una de las primeras decisiones clave es definir con claridad el alcance del proyecto. Esto implica saber qué se va a construir, con qué objetivos, bajo qué necesidades y con qué nivel de inversión disponible. Muchas obras enfrentan conflictos porque comienzan con ideas generales, pero sin una definición precisa de superficies, usos, etapas, prioridades o limitaciones técnicas. Cuanto más claro sea el punto de partida, más fácil será tomar decisiones coherentes durante todo el proceso.","Otro aspecto fundamental es revisar la viabilidad normativa. Antes de avanzar con planos definitivos o contrataciones, es indispensable conocer qué permite la normativa vigente: capacidad constructiva, retiros, alturas, usos permitidos, restricciones patrimoniales, condiciones de seguridad, accesibilidad e instalaciones. Este análisis evita proyectar soluciones que luego no puedan aprobarse o que generen demoras en la tramitación municipal.","La documentación técnica también cumple un rol central. Contar con planos, cómputos, especificaciones y detalles constructivos bien definidos permite ordenar el trabajo de todos los equipos involucrados. Una documentación incompleta suele derivar en improvisaciones, diferencias de criterio, mayores costos y retrasos. Por eso, antes de iniciar la obra, es recomendable verificar que cada etapa esté correctamente representada y coordinada.","La planificación económica es otra decisión crítica. No alcanza con estimar un presupuesto general: es necesario contemplar honorarios, permisos, derechos de obra, materiales, mano de obra, imprevistos, seguros, alquileres de equipos y posibles modificaciones durante el proceso. Una obra bien planificada debe incluir márgenes de contingencia y una estrategia clara para administrar los recursos.","También resulta clave anticipar la logística. El ingreso de materiales, la ubicación del obrador, los accesos, los horarios permitidos, la convivencia con vecinos, la necesidad de cortes o permisos especiales y la coordinación de gremios pueden afectar directamente el desarrollo de la obra. Resolver estos puntos con anticipación permite evitar interrupciones innecesarias.","Finalmente, toda obra necesita una conducción técnica responsable. El acompañamiento profesional ayuda a ordenar decisiones, interpretar normativas, coordinar documentación, gestionar permisos y detectar problemas antes de que se transformen en costos mayores.","Anticipar decisiones críticas no significa eliminar todos los imprevistos, pero sí construir con mayor control. Una obra bien pensada desde el inicio tiene más posibilidades de avanzar en tiempo, en regla y con mejores resultados."]', '[]', '["planificación","obra","arquitectura"]', 'published', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
('documentacion-ejecutiva-el-mapa-que-evita-errores-en-obra', 'Documentación ejecutiva: el mapa que evita errores en obra', 'Una documentación ejecutiva completa y precisa traduce el proyecto en instrucciones concretas, reduce errores y mejora la coordinación de todos los equipos de obra.', 'Construcción', '2026', 'img/proyecto-02.jpg', 1000, 1400, '["Toda obra necesita una idea, un proyecto y una dirección clara. Pero para que esa idea pueda construirse correctamente, hace falta algo fundamental: una documentación ejecutiva completa y precisa. Este conjunto de planos, detalles, especificaciones y criterios técnicos funciona como un verdadero mapa para todos los equipos que participan en la obra.","La documentación ejecutiva permite traducir el proyecto en instrucciones concretas. No se trata solo de planos generales, sino de información detallada sobre medidas, materiales, encuentros constructivos, instalaciones, estructuras, terminaciones y procesos de ejecución. Cuando esta documentación está bien desarrollada, cada decisión queda más clara y se reducen las interpretaciones libres en obra.","Uno de los principales beneficios es la prevención de errores. Muchas fallas constructivas, demoras o sobrecostos aparecen cuando la obra avanza con información incompleta. Una medida no definida, una instalación no coordinada o un detalle constructivo ausente pueden generar retrabajos, compras innecesarias o soluciones improvisadas. La documentación ejecutiva ayuda a detectar esas situaciones antes de que lleguen al sitio de obra.","También mejora la coordinación entre profesionales, contratistas y gremios. En una obra intervienen muchas partes: arquitectura, estructura, instalaciones sanitarias, eléctricas, incendio, proveedores y mano de obra. Si cada equipo trabaja con criterios distintos o información desactualizada, los conflictos aparecen rápidamente. Una documentación ordenada permite alinear a todos bajo el mismo criterio técnico.","Otro punto clave es el control económico. Cuanto más definida está la información, más preciso puede ser el presupuesto. Los cómputos de materiales, cantidades, sistemas constructivos y terminaciones dependen directamente de la calidad de la documentación. Esto permite comparar presupuestos con mayor claridad, planificar compras y reducir imprevistos.","La documentación ejecutiva también facilita el seguimiento de obra. Sirve como referencia para verificar avances, controlar calidad, resolver consultas y evaluar si lo construido coincide con lo proyectado. En este sentido, no es solo una herramienta previa al inicio de obra, sino un instrumento activo durante todo el proceso constructivo.","Además, permite tomar mejores decisiones cuando aparecen ajustes inevitables. Toda obra puede requerir modificaciones, pero cuando existe una base técnica sólida, esos cambios pueden analizarse con mayor criterio, midiendo impactos en costos, tiempos y normativa.","En definitiva, la documentación ejecutiva es mucho más que un conjunto de planos: es una herramienta de gestión, control y prevención. Una obra con documentación clara tiene menos margen para el error, menos improvisación y más posibilidades de alcanzar un resultado eficiente, seguro y bien ejecutado."]', '[]', '["documentación","construcción","obra"]', 'published', '2026-01-01 00:00:01', '2026-01-01 00:00:01'),
('estrategias-ambientales-que-elevan-el-valor-del-proyecto', 'Estrategias ambientales que elevan el valor del proyecto', 'Incorporar estrategias ambientales desde el diseño mejora el valor, la eficiencia y la proyección futura de cada proyecto.', 'Medio ambiente', '2026', 'img/proyecto-03.jpg', 1400, 1400, '["Incorporar estrategias ambientales en un proyecto ya no es solo una decisión ética o estética: es una forma concreta de mejorar su valor, su eficiencia y su proyección a futuro. Desde las primeras etapas de diseño, las decisiones vinculadas al uso de recursos, la energía, el agua y los materiales pueden impactar directamente en la calidad del espacio y en sus costos operativos.","Una de las primeras estrategias es aprovechar mejor las condiciones naturales del sitio. La orientación, el ingreso de luz solar, la ventilación cruzada y la protección frente al calor permiten reducir la dependencia de sistemas artificiales de climatización e iluminación. Un proyecto que dialoga con su entorno suele ser más eficiente, confortable y sustentable.","La elección de materiales también cumple un rol central. Utilizar materiales durables, de bajo mantenimiento, reciclables o de origen local puede disminuir el impacto ambiental y, al mismo tiempo, mejorar la vida útil de la obra. No se trata únicamente de construir “verde”, sino de tomar decisiones inteligentes que reduzcan desperdicios y optimicen recursos.","Otro aspecto clave es la eficiencia energética. La incorporación de aislaciones adecuadas, carpinterías eficientes, iluminación LED, sensores, equipos de bajo consumo o sistemas de energía renovable puede generar ahorros importantes en el tiempo. Esto no solo mejora el desempeño ambiental, sino que también vuelve al proyecto más atractivo para usuarios, compradores o inversores.","La gestión del agua es otra oportunidad de valor. Sistemas de captación de lluvia, reutilización de aguas grises, artefactos de bajo consumo y paisajismo con especies adaptadas al clima permiten reducir el consumo y mejorar la relación del edificio con su entorno.","Además, las estrategias ambientales pueden fortalecer la imagen institucional de un proyecto. En desarrollos residenciales, comerciales o corporativos, cada vez más personas valoran los espacios responsables, eficientes y alineados con nuevas formas de habitar y trabajar. La sustentabilidad deja de ser un agregado para convertirse en un diferencial competitivo.","Finalmente, incorporar criterios ambientales desde el inicio permite evitar soluciones costosas de último momento. Cuando estas decisiones se integran al diseño, a la documentación y a la planificación de obra, el resultado es más coherente, eficiente y medible.","En definitiva, las estrategias ambientales elevan el valor del proyecto porque mejoran su rendimiento, reducen costos futuros y aportan una visión más responsable. Construir con criterio ambiental es proyectar pensando no solo en el presente, sino también en la vida útil y el impacto real de cada obra."]', '[]', '["medio ambiente","sustentabilidad","proyecto"]', 'published', '2026-01-01 00:00:02', '2026-01-01 00:00:02')
ON DUPLICATE KEY UPDATE `slug` = VALUES(`slug`);
