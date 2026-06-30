<?php
$commarDbConfig = [];
$commarDbConfigPath = __DIR__ . '/db-config.php';

if (is_file($commarDbConfigPath)) {
    $commarDbConfig = require $commarDbConfigPath;
}

if (!is_array($commarDbConfig)) {
    $commarDbConfig = [];
}

defined('COMMAR_DB_HOST') || define('COMMAR_DB_HOST', (string) ($commarDbConfig['host'] ?? '127.0.0.1'));
defined('COMMAR_DB_NAME') || define('COMMAR_DB_NAME', (string) ($commarDbConfig['name'] ?? 'commar'));
defined('COMMAR_DB_USER') || define('COMMAR_DB_USER', (string) ($commarDbConfig['user'] ?? 'root'));
defined('COMMAR_DB_PASSWORD') || define('COMMAR_DB_PASSWORD', (string) ($commarDbConfig['password'] ?? ''));
defined('COMMAR_DB_CHARSET') || define('COMMAR_DB_CHARSET', (string) ($commarDbConfig['charset'] ?? 'utf8mb4'));

if (!function_exists('commar_db')) {
    function commar_db(): PDO
    {
        static $pdo = null;
        static $schemaChecked = false;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            COMMAR_DB_HOST,
            COMMAR_DB_NAME,
            COMMAR_DB_CHARSET
        );

        try {
            $pdo = new PDO($dsn, COMMAR_DB_USER, COMMAR_DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            if ((string) $exception->getCode() !== '1049') {
                throw $exception;
            }

            $serverDsn = sprintf('mysql:host=%s;charset=%s', COMMAR_DB_HOST, COMMAR_DB_CHARSET);
            $serverPdo = new PDO($serverDsn, COMMAR_DB_USER, COMMAR_DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $serverPdo->exec('CREATE DATABASE IF NOT EXISTS `' . COMMAR_DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            $pdo = new PDO($dsn, COMMAR_DB_USER, COMMAR_DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        if (!$schemaChecked) {
            commar_db_ensure_schema($pdo);
            $schemaChecked = true;
        }

        return $pdo;
    }
}

if (!function_exists('commar_db_ensure_schema')) {
    function commar_db_ensure_schema(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_articles` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `slug` VARCHAR(180) NOT NULL,
                `title` VARCHAR(180) NOT NULL,
                `description` LONGTEXT NOT NULL,
                `category` VARCHAR(100) NOT NULL,
                `year` VARCHAR(4) NOT NULL,
                `image` VARCHAR(255) NOT NULL DEFAULT '',
                `image_width` INT UNSIGNED NOT NULL DEFAULT 0,
                `image_height` INT UNSIGNED NOT NULL DEFAULT 0,
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_media` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_settings` (
                `setting_key` VARCHAR(120) NOT NULL,
                `setting_value` LONGTEXT NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`setting_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_newsletter_submissions` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(50) NOT NULL,
                `password_hash` VARCHAR(255) NOT NULL,
                `email` VARCHAR(255) NOT NULL DEFAULT '',
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_commar_users_username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_password_resets` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `token_hash` CHAR(64) NOT NULL,
                `expires_at` DATETIME NOT NULL,
                `used_at` DATETIME NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_commar_password_resets_token` (`token_hash`),
                KEY `idx_commar_password_resets_user` (`user_id`),
                KEY `idx_commar_password_resets_expires` (`expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_focused_works` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `lang` VARCHAR(5) NOT NULL DEFAULT 'es',
                `title` VARCHAR(255) NOT NULL,
                `category` VARCHAR(100) NOT NULL,
                `summary` TEXT NOT NULL,
                `image` VARCHAR(255) NOT NULL,
                `image_width` INT UNSIGNED NOT NULL DEFAULT 0,
                `image_height` INT UNSIGNED NOT NULL DEFAULT 0,
                `display_order` INT NOT NULL DEFAULT 0,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_commar_focused_works_lang_order` (`lang`, `display_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_works` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `slug` VARCHAR(180) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `category` VARCHAR(100) NOT NULL DEFAULT '',
                `location` VARCHAR(120) NOT NULL DEFAULT '',
                `year` VARCHAR(20) NOT NULL DEFAULT '',
                `summary` TEXT NOT NULL,
                `image` VARCHAR(255) NOT NULL DEFAULT '',
                `image_width` INT UNSIGNED NOT NULL DEFAULT 0,
                `image_height` INT UNSIGNED NOT NULL DEFAULT 0,
                `gallery_json` LONGTEXT NULL,
                `hero_alt` VARCHAR(255) NOT NULL DEFAULT '',
                `intro` TEXT NOT NULL,
                `description_json` LONGTEXT NOT NULL,
                `metrics_json` LONGTEXT NOT NULL,
                `status` ENUM('published', 'draft', 'deleted') NOT NULL DEFAULT 'published',
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `deleted_at` DATETIME NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_commar_works_slug` (`slug`),
                KEY `idx_commar_works_status_title` (`status`, `title`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_work_categories` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `slug` VARCHAR(180) NOT NULL,
                `name` VARCHAR(120) NOT NULL,
                `display_order` INT NOT NULL DEFAULT 0,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_commar_work_categories_slug` (`slug`),
                UNIQUE KEY `uniq_commar_work_categories_name` (`name`),
                KEY `idx_commar_work_categories_order` (`display_order`, `name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_jobs` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(180) NOT NULL,
                `description` LONGTEXT NOT NULL,
                `image` VARCHAR(255) NOT NULL DEFAULT '',
                `image_width` INT UNSIGNED NOT NULL DEFAULT 0,
                `image_height` INT UNSIGNED NOT NULL DEFAULT 0,
                `status` ENUM('active', 'inactive', 'deleted') NOT NULL DEFAULT 'active',
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `deleted_at` DATETIME NULL,
                PRIMARY KEY (`id`),
                KEY `idx_commar_jobs_status_updated` (`status`, `updated_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS `commar_job_applications` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `job_id` INT UNSIGNED NOT NULL,
                `full_name` VARCHAR(160) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `phone` VARCHAR(80) NOT NULL DEFAULT '',
                `message` TEXT NOT NULL,
                `cv_path` VARCHAR(255) NOT NULL,
                `cv_original_name` VARCHAR(255) NOT NULL,
                `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
                `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
                `submitted_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_commar_job_applications_job` (`job_id`, `submitted_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ([
            "ALTER TABLE `commar_articles` ADD COLUMN IF NOT EXISTS `tags_json` LONGTEXT NULL AFTER `gallery_json`",
            "ALTER TABLE `commar_articles` ADD COLUMN IF NOT EXISTS `youtube_url` VARCHAR(255) NOT NULL DEFAULT '' AFTER `gallery_json`",
            "ALTER TABLE `commar_articles` MODIFY COLUMN `description` LONGTEXT NOT NULL",
            "ALTER TABLE `commar_articles` MODIFY COLUMN `image` VARCHAR(255) NOT NULL DEFAULT ''",
            "ALTER TABLE `commar_articles` MODIFY COLUMN `image_width` INT UNSIGNED NOT NULL DEFAULT 0",
            "ALTER TABLE `commar_articles` MODIFY COLUMN `image_height` INT UNSIGNED NOT NULL DEFAULT 0",
            "ALTER TABLE `commar_works` ADD COLUMN IF NOT EXISTS `gallery_json` LONGTEXT NULL AFTER `image_height`",
            "ALTER TABLE `commar_jobs` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) NOT NULL DEFAULT '' AFTER `description`",
            "ALTER TABLE `commar_jobs` ADD COLUMN IF NOT EXISTS `image_width` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `image`",
            "ALTER TABLE `commar_jobs` ADD COLUMN IF NOT EXISTS `image_height` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `image_width`",
        ] as $sql) {
            try {
                $pdo->exec($sql);
            } catch (PDOException $exception) {
                // Older local schemas may already be compatible enough to continue.
            }
        }

        try {
            $columnCheck = $pdo->query("SHOW COLUMNS FROM `commar_articles` LIKE 'youtube_url'");
            if ($columnCheck && !$columnCheck->fetch()) {
                $pdo->exec("ALTER TABLE `commar_articles` ADD COLUMN `youtube_url` VARCHAR(255) NOT NULL DEFAULT '' AFTER `gallery_json`");
            }
        } catch (PDOException $exception) {
            // Save screens will surface DB issues if the schema is not writable.
        }

        try {
            $columnCheck = $pdo->query("SHOW COLUMNS FROM `commar_works` LIKE 'gallery_json'");
            if ($columnCheck && !$columnCheck->fetch()) {
                $pdo->exec("ALTER TABLE `commar_works` ADD COLUMN `gallery_json` LONGTEXT NULL AFTER `image_height`");
            }
        } catch (PDOException $exception) {
            // The site can continue; save screens will surface DB issues if the schema is not writable.
        }
    }
}
