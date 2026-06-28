<?php
require_once __DIR__ . '/db.php';

if (!function_exists('commar_normalize_article_row')) {
    function commar_normalize_article_row(array $article): array
    {
        $content = json_decode((string) ($article['content_json'] ?? '[]'), true);
        $gallery = json_decode((string) ($article['gallery_json'] ?? '[]'), true);
        $tags = json_decode((string) ($article['tags_json'] ?? '[]'), true);
        $image = (string) $article['image'];

        return [
            'id' => (int) $article['id'],
            'slug' => (string) $article['slug'],
            'title' => (string) $article['title'],
            'description' => (string) $article['description'],
            'category' => (string) $article['category'],
            'year' => (string) $article['year'],
            'url' => 'articulo.php?slug=' . rawurlencode((string) $article['slug']),
            'image' => $image,
            'image_width' => (int) ($article['image_width'] ?? 0),
            'image_height' => (int) ($article['image_height'] ?? 0),
            'display_image' => $image !== '' ? $image : 'img/logo-commar-500.png',
            'display_image_width' => $image !== '' ? (int) ($article['image_width'] ?? 1400) : 466,
            'display_image_height' => $image !== '' ? (int) ($article['image_height'] ?? 933) : 495,
            'content' => is_array($content) ? $content : [],
            'content_html' => (string) ($article['content_html'] ?? ''),
            'gallery' => is_array($gallery) ? $gallery : [],
            'youtube_url' => (string) ($article['youtube_url'] ?? ''),
            'tags' => is_array($tags) ? $tags : [],
            'status' => (string) ($article['status'] ?? 'published'),
            'published_at' => (string) ($article['published_at'] ?? ''),
            'updated_at' => (string) ($article['updated_at'] ?? ''),
            'source' => 'admin',
        ];
    }
}

if (!function_exists('commar_articles')) {
    function commar_articles(): array
    {
        return commar_dynamic_articles(true);
    }
}

if (!function_exists('commar_dynamic_articles')) {
    function commar_dynamic_articles(bool $publishedOnly = true): array
    {
        $sql = 'SELECT * FROM commar_articles WHERE status <> :deleted';
        $params = ['deleted' => 'deleted'];

        if ($publishedOnly) {
            $sql .= ' AND status = :published';
            $params['published'] = 'published';
        }

        $sql .= ' ORDER BY published_at DESC, updated_at DESC, id DESC';

        $statement = commar_db()->prepare($sql);
        $statement->execute($params);

        return array_map('commar_normalize_article_row', $statement->fetchAll());
    }
}

if (!function_exists('commar_find_article_by_slug')) {
    function commar_find_article_by_slug(string $slug): ?array
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return null;
        }

        $statement = commar_db()->prepare('SELECT * FROM commar_articles WHERE slug = :slug LIMIT 1');
        $statement->execute(['slug' => $slug]);
        $article = $statement->fetch();

        return is_array($article) ? commar_normalize_article_row($article) : null;
    }
}
