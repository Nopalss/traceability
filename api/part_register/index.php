<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

// ================================
// Ambil parameter dari KTDatatable
// ================================
$query      = $_POST['query'] ?? [];
$pagination = $_POST['pagination'] ?? [];

$partCode = isset($query['part_code']) ? trim($query['part_code']) : '';
$supplier = isset($query['supplier']) ? trim($query['supplier']) : '';

// Pagination (default)
$page  = isset($pagination['page']) ? (int)$pagination['page'] : 1;
$limit = isset($pagination['perpage']) ? (int)$pagination['perpage'] : 10;
$offset = ($page - 1) * $limit;

// ================================
// Build WHERE clause (opsional)
// ================================
$where  = [];
$params = [];

if ($partCode !== '') {
    $where[] = 'p.part_code LIKE :part_code';
    $params[':part_code'] = '%' . $partCode . '%';
}

if ($supplier !== '') {
    $where[] = 'p.supplier = :supplier';
    $params[':supplier'] = $supplier;
}

// ================================
// Hitung total data
// ================================
$countSql = "
    SELECT COUNT(*) 
    FROM tbl_part p
    JOIN tbl_supplier s ON p.supplier = s.id_supplier
";

if (!empty($where)) {
    $countSql .= ' WHERE ' . implode(' AND ', $where);
}

$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// ================================
// Ambil data utama
// ================================
$dataSql = "
    SELECT 
        p.id_part,
        p.part_code,
        p.part_name,
        p.supplier,
        s.name_supplier
    FROM tbl_part p
    JOIN tbl_supplier s ON p.supplier = s.id_supplier
";

if (!empty($where)) {
    $dataSql .= ' WHERE ' . implode(' AND ', $where);
}

$dataSql .= " ORDER BY p.id_part DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($dataSql);

// Bind parameter search
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}

// Bind pagination
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================================
// Response sesuai format Metronic
// ================================
echo json_encode([
    'meta' => [
        'page'    => $page,
        'pages'   => ceil($total / $limit),
        'perpage' => $limit,
        'total'   => $total,
        'sort'    => 'desc',
        'field'   => 'id_part'
    ],
    'data' => $data
]);
