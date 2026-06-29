<?php
require_once __DIR__ . '/db.php';

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
    function commar_save_job(string $title, string $description, string $status, int $id = 0): bool
    {
        $title = trim($title);
        $description = trim($description);
        $status = $status === 'active' ? 'active' : 'inactive';

        if ($title === '' || $description === '') {
            return false;
        }

        $db = commar_db();
        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $statement = $db->prepare(
                'UPDATE commar_jobs
                 SET title = :title, description = :description, status = :status, updated_at = :updated_at
                 WHERE id = :id AND status <> :deleted'
            );

            return $statement->execute([
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'updated_at' => $now,
                'deleted' => 'deleted',
            ]);
        }

        $statement = $db->prepare(
            'INSERT INTO commar_jobs (title, description, status, created_at, updated_at)
             VALUES (:title, :description, :status, :created_at, :updated_at)'
        );

        return $statement->execute([
            'title' => $title,
            'description' => $description,
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
