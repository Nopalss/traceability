<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

$query      = $_REQUEST['query'] ?? [];
$pagination = $_REQUEST['pagination'] ?? [];

// ================================
// INPUT FILTER
// ================================
$keyword         = trim($query['keyword'] ?? '');
$production_date = trim($query['production_date'] ?? '');

// ================================
// PAGINATION
// ================================
$page   = max(1, (int)($pagination['page'] ?? 1));
$limit  = max(1, (int)($pagination['perpage'] ?? 10));
$offset = ($page - 1) * $limit;

// ================================
// WHERE
// ================================
$where = [];
$params = [];

if ($keyword !== '') {
    $where[] = '(pp.pp_code LIKE :keyword)';
    $params[':keyword'] = "%$keyword%";
}

if ($production_date !== '') {
    $where[] = 'pp.production_date = :production_date';
    $params[':production_date'] = $production_date;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ================================
// COUNT GROUP
// ================================
$countSql = "
SELECT COUNT(*) FROM (
    SELECT pp.pp_code
    FROM tbl_production_planning pp
    $whereSql
    GROUP BY pp.pp_code, pp.production_date
) x
";

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// ================================
// DATA QUERY (FINAL FIX)
// ================================
$dataSql = "
SELECT
    pp.pp_code,

    -- FORMAT TANGGAL
    DATE_FORMAT(pp.production_date, '%d %M %Y') AS production_date,

    COUNT(DISTINCT pp.shift)   AS total_shift,
    COUNT(DISTINCT pp.line_id) AS total_line,

    -- 🔥 FIX: JUMLAH BARIS (PRODUCT)
    COUNT(pp.pp_id)            AS total_part_assy

FROM tbl_production_planning pp

$whereSql

GROUP BY pp.pp_code, pp.production_date

ORDER BY pp.production_date DESC

LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($dataSql);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================================
// RESPONSE
// ================================
echo json_encode([
    'meta' => [
        'page'    => $page,
        'pages'   => ceil($total / $limit),
        'perpage' => $limit,
        'total'   => $total,
    ],
    'data' => $data
]);
