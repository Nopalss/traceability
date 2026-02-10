<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

$query      = $_REQUEST['query'] ?? [];
$pagination = $_REQUEST['pagination'] ?? [];

// ================================
// INPUT FILTER
// ================================
$keyword         = trim($query['keyword'] ?? '');
$line_id         = trim($query['line_id'] ?? '');
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
    $where[] = '(pp.pp_code LIKE :keyword OR l.line_name LIKE :keyword)';
    $params[':keyword'] = "%$keyword%";
}

if ($line_id !== '') {
    $where[] = 'pp.line_id = :line_id';
    $params[':line_id'] = $line_id;
}

if ($production_date !== '') {
    $where[] = 'pp.production_date = :production_date';
    $params[':production_date'] = $production_date;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ================================
// COUNT GROUP (pp_code)
// ================================
$countSql = "
SELECT COUNT(*) FROM (
    SELECT pp.pp_code
    FROM tbl_production_planning pp
    JOIN tbl_line l ON l.line_id = pp.line_id
    $whereSql
    GROUP BY pp.pp_code
) x
";

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// ================================
// DATA QUERY (GROUP BY pp_code)
// ================================
$dataSql = "
SELECT
    MIN(pp.pp_id)                 AS pp_id,
    pp.pp_code,
    l.line_name,
    COUNT(DISTINCT pp.shift)     AS jumlah_shift,
    COUNT(pp.pp_id)              AS product_count,
    pp.production_date
FROM tbl_production_planning pp
JOIN tbl_line l ON l.line_id = pp.line_id
$whereSql
GROUP BY pp.pp_code, pp.production_date, l.line_name
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

// require_once __DIR__ . '/../../includes/config.php';

// header('Content-Type: application/json');
// $query      = $_REQUEST['query'] ?? [];
// $pagination = $_REQUEST['pagination'] ?? [];

// // ================================
// // INPUT
// // ================================
// $status = trim($query['status'] ?? '');
// $keyword = trim($query['keyword'] ?? '');
// $line_id = trim($query['line_id'] ?? '');
// $production_date = trim($query['production_date'] ?? '');

// // ================================
// // PAGINATION
// // ================================
// $page   = max(1, (int)($pagination['page'] ?? 1));
// $limit  = max(1, (int)($pagination['perpage'] ?? 10));
// $offset = ($page - 1) * $limit;

// // ================================
// // WHERE BUILDER (OUTER)
// // ================================
// $whereOuter = ['1=1'];
// $whereInner = ['1=1'];
// $params = [];

// if ($status !== '') {
//     $whereOuter[] = 'pp.status = :status';
//     $whereInner[] = 'status = :status';
//     $params[':status'] = $status;
// }

// if ($keyword !== '') {
//     $whereOuter[] = '(
//         pp.product_code LIKE :keyword
//         OR pp.line_id IN (SELECT line_id FROM tbl_line WHERE line_name LIKE :keyword)
//     )';

//     $whereInner[] = '(
//         product_code LIKE :keyword
//         OR line_id IN (SELECT line_id FROM tbl_line WHERE line_name LIKE :keyword)
//     )';

//     $params[':keyword'] = "%$keyword%";
// }

// if ($line_id !== '') {
//     $whereOuter[] = 'pp.line_id = :line_id';
//     $whereInner[] = 'line_id = :line_id';
//     $params[':line_id'] = $line_id;
// }

// if ($production_date !== '') {
//     $whereOuter[] = 'pp.production_date = :production_date';
//     $whereInner[] = 'production_date = :production_date';
//     $params[':production_date'] = $production_date;
// }

// $whereOuterSql = 'WHERE ' . implode(' AND ', $whereOuter);
// $whereInnerSql = 'WHERE ' . implode(' AND ', $whereInner);

// // ================================
// // COUNT
// // ================================
// $countSql = "
// SELECT COUNT(DISTINCT pp_code)
// FROM tbl_production_planning
// $whereInnerSql
// ";

// $stmt = $pdo->prepare($countSql);
// $stmt->execute($params);
// $total = (int)$stmt->fetchColumn();

// // ================================
// // DATA
// // ================================
// $dataSql = "
// SELECT
//     pp.pp_code,
//     pp.line_id,
//     l.line_name,
//     pp.product_code,
//     pp.production_date,
//     pp.total_qty,
//     pp.status
// FROM tbl_production_planning pp
// JOIN tbl_line l ON pp.line_id = l.line_id
// $whereOuterSql
// AND pp.pp_id IN (
//     SELECT MIN(pp_id)
//     FROM tbl_production_planning
//     $whereInnerSql
//     GROUP BY pp_code
// )
// ORDER BY pp.production_date DESC
// LIMIT :limit OFFSET :offset
// ";

// $stmt = $pdo->prepare($dataSql);

// foreach ($params as $k => $v) {
//     $stmt->bindValue($k, $v);
// }

// $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
// $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// $stmt->execute();
// $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // ================================
// // RESPONSE
// // ================================
// echo json_encode([
//     'meta' => [
//         'page'    => $page,
//         'pages'   => ceil($total / $limit),
//         'perpage' => $limit,
//         'total'   => $total
//     ],
//     'data' => $data
// ]);
