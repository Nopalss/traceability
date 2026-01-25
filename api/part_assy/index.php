<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

// ================================
// Ambil parameter dari KTDatatable
// ================================
$query      = $_POST['query'] ?? [];
$pagination = $_POST['pagination'] ?? [];

$keyword = trim($query['part_code'] ?? ''); // 1 input untuk assy

// Pagination
$page   = max(1, (int)($pagination['page'] ?? 1));
$limit  = max(1, (int)($pagination['perpage'] ?? 10));
$offset = ($page - 1) * $limit;

// ================================
// WHERE (search assy: code / name)
// ================================
$where  = [];
$params = [];

if ($keyword !== '') {
    $where[] = '(p.part_code LIKE :kw OR p.part_name LIKE :kw)';
    $params[':kw'] = '%' . $keyword . '%';
}

// ================================
// COUNT assy
// ================================
$countSql = "
    SELECT COUNT(DISTINCT pa.part_assy)
    FROM tbl_part_assy pa
    JOIN tbl_part p ON pa.part_assy = p.part_code
";

if ($where) {
    $countSql .= ' WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// ================================
// DATA utama
// ================================
$dataSql = "
    SELECT
        pa.part_assy,
        p.part_code      AS part_code,
        p.part_name,
        COUNT(pa.part_code) AS part_count,
        MIN(pa.id_pa)    AS id_pa
    FROM tbl_part_assy pa
    JOIN tbl_part p ON pa.part_assy = p.part_code
";

if ($where) {
    $dataSql .= ' WHERE ' . implode(' AND ', $where);
}

$dataSql .= "
    GROUP BY pa.part_assy, p.part_code, p.part_name
    ORDER BY pa.part_assy DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($dataSql);

// bind search
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

// bind pagination
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================================
// Response Metronic
// ================================
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
