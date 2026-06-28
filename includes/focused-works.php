<?php
require_once __DIR__ . '/db.php';

if (!function_exists('commar_get_focused_works')) {
    function commar_get_focused_works(string $lang = 'es'): array
    {
        $statement = commar_db()->prepare(
            'SELECT * FROM commar_focused_works WHERE lang = :lang ORDER BY display_order ASC'
        );
        $statement->execute(['lang' => $lang]);
        $results = $statement->fetchAll();

        return array_map(static function (array $work, int $index): array {
            return [
                'id' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'db_id' => (int) $work['id'],
                'title' => (string) $work['title'],
                'category' => (string) $work['category'],
                'summary' => (string) $work['summary'],
                'img' => (string) $work['image'],
                'img_width' => (int) $work['image_width'],
                'img_height' => (int) $work['image_height'],
            ];
        }, $results, array_keys($results));
    }
}

if (!function_exists('commar_get_all_focused_works_by_lang')) {
    function commar_get_all_focused_works_by_lang(): array
    {
        $statement = commar_db()->query(
            'SELECT * FROM commar_focused_works ORDER BY lang, display_order ASC'
        );
        $works = $statement->fetchAll();
        $grouped = [];
        foreach ($works as $work) {
            $grouped[$work['lang']][] = $work;
        }
        return $grouped;
    }
}

if (!function_exists('commar_get_focused_work_by_id')) {
    function commar_get_focused_work_by_id(int $id): ?array
    {
        $statement = commar_db()->prepare(
            'SELECT * FROM commar_focused_works WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $work = $statement->fetch();
        return is_array($work) ? $work : null;
    }
}