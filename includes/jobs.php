<?php
require_once __DIR__ . '/db.php';

if (!function_exists('commar_job_description_html')) {
    function commar_job_description_html(string $description): string
    {
        $description = trim($description);
        if ($description === '') {
            return '<p><br></p>';
        }

        if ($description === strip_tags($description)) {
            $paragraphs = preg_split('/\R{2,}/', $description) ?: [];
            $html = '';
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if ($paragraph !== '') {
                    $html .= '<p>' . nl2br(htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8')) . '</p>';
                }
            }

            return $html !== '' ? $html : '<p><br></p>';
        }

        return commar_sanitize_job_description_html($description);
    }
}

if (!function_exists('commar_sanitize_job_description_html')) {
    function commar_sanitize_job_description_html(string $html): string
    {
        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = strip_tags($html, '<p><br><strong><b><em><i><ul><ol><li><a>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        $html = preg_replace('/href\s*=\s*([\'"])\s*javascript:[^\'"]*\1/i', 'href="#"', $html) ?? '';

        return trim($html) !== '' ? trim($html) : '<p><br></p>';
    }
}

if (!function_exists('commar_active_jobs')) {
    function commar_active_jobs(): array
    {
        $statement = commar_db()->query(
            "SELECT * FROM commar_jobs WHERE status = 'active' ORDER BY updated_at DESC, id DESC"
        );

        return $statement->fetchAll();
    }
}

if (!function_exists('commar_admin_jobs')) {
    function commar_admin_jobs(): array
    {
        $statement = commar_db()->query(
            "SELECT * FROM commar_jobs WHERE status <> 'deleted' ORDER BY updated_at DESC, id DESC"
        );

        return $statement->fetchAll();
    }
}

if (!function_exists('commar_job_by_id')) {
    function commar_job_by_id(int $id, bool $activeOnly = true): ?array
    {
        $sql = 'SELECT * FROM commar_jobs WHERE id = :id';
        if ($activeOnly) {
            $sql .= " AND status = 'active'";
        } else {
            $sql .= " AND status <> 'deleted'";
        }
        $sql .= ' LIMIT 1';

        $statement = commar_db()->prepare($sql);
        $statement->execute(['id' => $id]);
        $job = $statement->fetch();

        return is_array($job) ? $job : null;
    }
}

if (!function_exists('commar_save_job')) {
    function commar_save_job(string $title, string $description, string $status, array $image = [], int $id = 0): bool
    {
        $title = trim($title);
        $description = commar_sanitize_job_description_html($description);
        $status = $status === 'active' ? 'active' : 'inactive';
        $imagePath = (string) ($image['path'] ?? '');
        $imageWidth = (int) ($image['width'] ?? 0);
        $imageHeight = (int) ($image['height'] ?? 0);
        $removeImage = !empty($image['remove']);

        if ($title === '' || $description === '') {
            return false;
        }

        $db = commar_db();
        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $current = commar_job_by_id($id, false);
            if ($current && $imagePath === '' && !$removeImage) {
                $imagePath = (string) ($current['image'] ?? '');
                $imageWidth = (int) ($current['image_width'] ?? 0);
                $imageHeight = (int) ($current['image_height'] ?? 0);
            }

            $statement = $db->prepare(
                'UPDATE commar_jobs
                 SET title = :title, description = :description, image = :image, image_width = :image_width, image_height = :image_height, status = :status, updated_at = :updated_at
                 WHERE id = :id AND status <> :deleted'
            );

            return $statement->execute([
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'image' => $imagePath,
                'image_width' => $imageWidth,
                'image_height' => $imageHeight,
                'status' => $status,
                'updated_at' => $now,
                'deleted' => 'deleted',
            ]);
        }

        $statement = $db->prepare(
            'INSERT INTO commar_jobs (title, description, image, image_width, image_height, status, created_at, updated_at)
             VALUES (:title, :description, :image, :image_width, :image_height, :status, :created_at, :updated_at)'
        );

        return $statement->execute([
            'title' => $title,
            'description' => $description,
            'image' => $imagePath,
            'image_width' => $imageWidth,
            'image_height' => $imageHeight,
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

if (!function_exists('commar_delete_job')) {
    function commar_delete_job(int $id): bool
    {
        $statement = commar_db()->prepare(
            "UPDATE commar_jobs SET status = 'deleted', deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id"
        );
        $now = date('Y-m-d H:i:s');

        return $statement->execute([
            'id' => $id,
            'deleted_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

if (!function_exists('commar_set_job_status')) {
    function commar_set_job_status(int $id, string $status): bool
    {
        if ($id <= 0 || !in_array($status, ['active', 'inactive'], true)) {
            return false;
        }

        $statement = commar_db()->prepare(
            "UPDATE commar_jobs
             SET status = :status, updated_at = :updated_at
             WHERE id = :id AND status <> 'deleted'"
        );

        return $statement->execute([
            'id' => $id,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
