<?php
require_once __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/includes/projects.php';

commar_admin_require_login();

if (!function_exists('commar_admin_search_lower')) {
    function commar_admin_search_lower(string $value): string
    {
        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }
}

$allWorks = commar_admin_projects();
$searchQuery = trim((string) ($_GET['q'] ?? ''));
$works = $allWorks;

if ($searchQuery !== '') {
    $normalizedQuery = commar_admin_search_lower($searchQuery);
    $works = array_values(array_filter($allWorks, static function (array $work) use ($normalizedQuery): bool {
        $searchable = implode(' ', [
            (string) ($work['title'] ?? ''),
            (string) ($work['category'] ?? ''),
            (string) ($work['location'] ?? ''),
            (string) ($work['year'] ?? ''),
            (string) ($work['summary'] ?? ''),
            (string) ($work['slug'] ?? ''),
        ]);

        return strpos(commar_admin_search_lower($searchable), $normalizedQuery) !== false;
    }));
}

$totalWorks = count($allWorks);
$visibleWorks = count($works);
$perPage = 20;
$totalPages = max(1, (int) ceil($visibleWorks / $perPage));
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;
$paginatedWorks = array_slice($works, $offset, $perPage);
$firstVisible = $visibleWorks > 0 ? $offset + 1 : 0;
$lastVisible = min($offset + $perPage, $visibleWorks);
$updated = ($_GET['updated'] ?? '') === '1';
$created = ($_GET['created'] ?? '') === '1';
$deleted = ($_GET['deleted'] ?? '') === '1';

if (!function_exists('commar_admin_works_page_url')) {
    function commar_admin_works_page_url(int $page, string $searchQuery): string
    {
        $params = ['page' => $page];
        if ($searchQuery !== '') {
            $params['q'] = $searchQuery;
        }

        return 'works.php?' . http_build_query($params);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obras | MOnkey CMS</title>
    <link rel="icon" type="image/png" href="../img/logo-commar-500.png">
    <link rel="apple-touch-icon" href="../img/logo-commar-500.png">
    <link rel="stylesheet" href="admin.css?v=20260629-admin-works-ui">
</head>
<body class="admin-page">
    <div class="admin-shell">
        <?php commar_admin_nav('works'); ?>
        <div class="admin-main">
            <?php commar_admin_header('Obras'); ?>
            <main class="admin-content">
                <?php commar_admin_works_nav('works'); ?>
                <section class="admin-panel admin-wide-panel">
                    <div class="admin-page-actions">
                        <div>
                            <h2>Obras</h2>
                        </div>
                        <a href="work-edit.php" class="admin-primary-link">Nueva obra</a>
                    </div>

                    <?php if ($created): ?>
                        <p class="admin-alert admin-alert-success">Obra creada.</p>
                    <?php endif; ?>
                    <?php if ($updated): ?>
                        <p class="admin-alert admin-alert-success">Obra actualizada.</p>
                    <?php endif; ?>
                    <?php if ($deleted): ?>
                        <p class="admin-alert admin-alert-success">Obra eliminada.</p>
                    <?php endif; ?>

                    <form action="works.php" method="get" class="admin-list-search" role="search">
                        <label for="works-search">
                            <span>Buscar obras</span>
                            <input id="works-search" type="search" name="q" value="<?php echo commar_admin_h($searchQuery); ?>" placeholder="Buscar por obra, categoría, ubicación o año">
                        </label>
                        <button type="submit" class="admin-search-submit">Buscar</button>
                        <?php if ($searchQuery !== ''): ?>
                            <a href="works.php" class="admin-search-clear">Limpiar</a>
                        <?php endif; ?>
                    </form>

                    <p class="admin-list-count">
                        <?php if ($searchQuery !== ''): ?>
                            Mostrando <?php echo $firstVisible; ?>-<?php echo $lastVisible; ?> de <?php echo $visibleWorks; ?> resultados. Total cargadas: <?php echo $totalWorks; ?>.
                        <?php else: ?>
                            Mostrando <?php echo $firstVisible; ?>-<?php echo $lastVisible; ?> de <?php echo $totalWorks; ?> obras cargadas.
                        <?php endif; ?>
                    </p>

                    <?php if (empty($works)): ?>
                        <p class="admin-empty">No hay obras cargadas.</p>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-post-table">
                                <thead>
                                    <tr>
                                        <th>Obra</th>
                                        <th>Categoría</th>
                                        <th>Ubicación</th>
                                        <th>Año</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paginatedWorks as $work): ?>
                                        <tr>
                                            <td>
                                                <div class="admin-post-title">
                                                    <?php if (!empty($work['image'])): ?>
                                                        <img src="../<?php echo commar_admin_h($work['image']); ?>" alt="" width="64" height="64">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo commar_admin_h($work['title']); ?></strong>
                                                        <span><?php echo commar_admin_h($work['summary']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo commar_admin_h($work['category']); ?></td>
                                            <td><?php echo commar_admin_h($work['location']); ?></td>
                                            <td><?php echo commar_admin_h($work['year']); ?></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <a href="../obras/<?php echo rawurlencode($work['slug']); ?>" target="_blank" rel="noopener" class="admin-button-icon" title="Ver obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg></a>
                                                    <a href="work-edit.php?id=<?php echo (int) $work['id']; ?>" class="admin-button-icon" title="Editar obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></a>
                                                    <form action="delete-work.php" method="post" onsubmit="return confirm('¿Eliminar esta obra? Esta acción no se puede deshacer.');" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo (int) $work['id']; ?>">
                                                        <button type="submit" class="admin-button-icon admin-button-danger" title="Eliminar obra"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <nav class="admin-pagination" aria-label="Paginación de obras">
                                <a class="<?php echo $currentPage <= 1 ? 'is-disabled' : ''; ?>" href="<?php echo $currentPage <= 1 ? '#' : commar_admin_h(commar_admin_works_page_url($currentPage - 1, $searchQuery)); ?>">Anterior</a>
                                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                    <?php
                                    $isEdge = $page === 1 || $page === $totalPages;
                                    $isNear = abs($page - $currentPage) <= 2;
                                    if (!$isEdge && !$isNear) {
                                        if ($page === 2 || $page === $totalPages - 1) {
                                            echo '<span class="admin-pagination-gap">...</span>';
                                        }
                                        continue;
                                    }
                                    ?>
                                    <a class="<?php echo $page === $currentPage ? 'is-active' : ''; ?>" href="<?php echo commar_admin_h(commar_admin_works_page_url($page, $searchQuery)); ?>"><?php echo $page; ?></a>
                                <?php endfor; ?>
                                <a class="<?php echo $currentPage >= $totalPages ? 'is-disabled' : ''; ?>" href="<?php echo $currentPage >= $totalPages ? '#' : commar_admin_h(commar_admin_works_page_url($currentPage + 1, $searchQuery)); ?>">Siguiente</a>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>
            </main>
            <?php commar_admin_footer(); ?>
        </div>
    </div>
</body>
</html>
