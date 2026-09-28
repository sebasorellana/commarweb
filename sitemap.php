<?php
require_once __DIR__ . '/includes/site.php';
require_once __DIR__ . '/includes/projects.php';
require_once __DIR__ . '/includes/articles.php';
require_once __DIR__ . '/includes/jobs.php';

$sitemapDate = static function (string $value): string {
    $timestamp = $value !== '' ? strtotime($value) : false;

    return $timestamp ? date('Y-m-d', $timestamp) : '';
};

$urls = [
    ['path' => '', 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['path' => 'el-estudio.php', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'servicios.php', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'servicio-proyectos.php', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'obra-viva.php', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'obras.php', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => 'blog.php', 'changefreq' => 'weekly', 'priority' => '0.8'],
    ['path' => 'contacto.php', 'changefreq' => 'yearly', 'priority' => '0.6'],
];

if (!empty(commar_active_jobs())) {
    $urls[] = ['path' => 'trabaja-con-nosotros.php', 'changefreq' => 'weekly', 'priority' => '0.5'];
}

foreach (commar_projects() as $project) {
    $urls[] = ['path' => commar_work_url($project['slug']), 'changefreq' => 'monthly', 'priority' => '0.7'];
}

foreach (commar_articles() as $article) {
    $urls[] = [
        'path' => commar_article_url($article['slug']),
        'changefreq' => 'monthly',
        'priority' => '0.7',
        'lastmod' => $sitemapDate($article['updated_at'] !== '' ? $article['updated_at'] : $article['published_at']),
    ];
}

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=3600');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?php echo htmlspecialchars(commar_absolute_url($url['path']), ENT_XML1 | ENT_QUOTES, 'UTF-8'); ?></loc>
<?php if (!empty($url['lastmod'])): ?>
    <lastmod><?php echo $url['lastmod']; ?></lastmod>
<?php endif; ?>
    <changefreq><?php echo $url['changefreq']; ?></changefreq>
    <priority><?php echo $url['priority']; ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
