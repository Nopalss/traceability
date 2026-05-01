<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$serial = trim($input['serial_no'] ?? '');

if ($serial === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Serial number tidak boleh kosong'
    ]);
    exit;
}

/*
=====================================
  FIND PRODUCT BY SERIAL
=====================================
*/
$stmt = $pdo->prepare("
    SELECT 
        dp.id,
        dp.product_code,
        dp.serial_no,
        dp.status,
        dp.created_at,
        dp.location,
        p.part_name,
        s.name_supplier
    FROM tbl_detail_product dp
    LEFT JOIN tbl_part p 
        ON dp.product_code = p.part_code
    LEFT JOIN tbl_supplier s 
        ON dp.location = s.id_supplier
    WHERE dp.serial_no = ?
    LIMIT 1
");
$stmt->execute([$serial]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Serial tidak ditemukan di sistem'
    ]);
    exit;
}

/*
=====================================
  FORMAT LOCATION
=====================================
*/
$location = '-';

if ($data['status'] === 'out') {
    $location = $data['name_supplier'] ?? 'Unknown Supplier';
} else {
    $location = 'Warehouse / Production Area';
}

/*
=====================================
  RETURN RESPONSE
=====================================
*/
echo json_encode([
    'success' => true,
    'data' => [
        'product_code' => $data['product_code'],
        'product_name' => $data['part_name'],
        'serial_no'    => $data['serial_no'],
        'status'       => strtoupper($data['status']),
        'location'     => $location,
        'created_at'   => $data['created_at']
    ]
]);
