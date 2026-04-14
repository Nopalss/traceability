<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

/* ================================
   PARAM
================================ */
$query      = $_POST['query'] ?? [];
$pagination = $_POST['pagination'] ?? [];

$keyword = trim($query['part_code'] ?? '');

$page   = max(1, (int)($pagination['page'] ?? 1));
$limit  = max(1, (int)($pagination['perpage'] ?? 100));
$offset = ($page - 1) * $limit;

/* ================================
   WHERE
================================ */
$where  = [];
$params = [];

if ($keyword !== '') {
    $where[] = '(
        m.name LIKE :kw OR
        p.part_code LIKE :kw OR
        p.part_name LIKE :kw
    )';
    $params[':kw'] = '%' . $keyword . '%';
}

/* ================================
   COUNT
================================ */
$countSql = "
SELECT COUNT(DISTINCT pa.part_assy)
FROM tbl_part_assy pa
JOIN tbl_part p ON pa.part_assy = p.part_code
LEFT JOIN tbl_model m ON m.part_code = pa.part_assy
";

if ($where) {
    $countSql .= ' WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

/* ================================
   DATA
================================ */
$dataSql = "
SELECT
    m.id,
    pa.part_assy,
    m.name               AS model_name,
    p.part_code          AS part_code,
    p.part_name,
    COUNT(pa.part_code)  AS part_count,
    MIN(pa.id_pa)        AS id_pa
FROM tbl_part_assy pa
JOIN tbl_part p ON pa.part_assy = p.part_code
LEFT JOIN tbl_model m ON m.part_code = pa.part_assy
";

if ($where) {
    $dataSql .= ' WHERE ' . implode(' AND ', $where);
}

$dataSql .= "
GROUP BY 
    pa.part_assy,
    m.name,
    p.part_code,
    p.part_name

ORDER BY pa.part_assy DESC
LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($dataSql);

/* bind search */
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

/* bind pagination */
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================================
   RESPONSE
================================ */
echo json_encode([
    'meta' => [
        'page'    => $page,
        'pages'   => ceil($total / $limit),
        'perpage' => $limit,
        'total'   => $total,
        'sort'    => 'desc',
        'field'   => 'part_assy'
    ],
    'data' => $data
]);
