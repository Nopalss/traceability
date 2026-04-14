<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$raw         = trim($input['qr'] ?? '');
$supplier_id = intval($input['supplier_id'] ?? 0);

if ($raw === '') {
    echo json_encode([
        'success' => false,
        'message' => 'QR code kosong'
    ]);
    exit;
}

if (!$supplier_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Supplier tidak valid'
    ]);
    exit;
}

/*
=============================
 FLEXIBLE QR PARSER Z1–Z7
=============================
*/
function parseQR($raw)
{
    $result = [];
    $parts = explode('|', $raw);

    foreach ($parts as $p) {
        $p = trim($p);
        if (preg_match('/^(Z[1-7])(.*)$/', $p, $m)) {
            $result[$m[1]] = trim($m[2]);
        }
    }

    return $result;
}

$z = parseQR($raw);

$product_code = $z['Z1'] ?? null;
$serial_no    = $z['Z2'] ?? null;
$ref_number   = $z['Z5'] ?? null;

if (!$product_code || !$serial_no) {
    echo json_encode([
        'success' => false,
        'message' => 'Format QR tidak valid (Z1/Z2 tidak ditemukan)'
    ]);
    exit;
}

/*
=============================
 VALIDATE PRODUCT EXIST
=============================
*/
$stmt = $pdo->prepare("
    SELECT dp.*, p.part_name
    FROM tbl_detail_product dp
    LEFT JOIN tbl_part p ON dp.product_code = p.part_code
    WHERE dp.product_code = ?
      AND dp.serial_no = ? AND dp.ref_number=?
    LIMIT 1
");
$stmt->execute([$product_code, $serial_no, $ref_number]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode([
        'success' => false,
        'message' => 'Product tidak ditemukan'
    ]);
    exit;
}

if ($product['status'] === 'out') {
    echo json_encode([
        'success' => false,
        'message' => 'Product sudah di-scan OUT sebelumnya'
    ]);
    exit;
}

/*
=============================
 VALIDATE SUPPLIER
=============================
*/
$stmt = $pdo->prepare("
    SELECT name_supplier
    FROM tbl_supplier
    WHERE id_supplier = ?
");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    echo json_encode([
        'success' => false,
        'message' => 'Supplier tidak ditemukan'
    ]);
    exit;
}

/*
=============================
 UPDATE STATUS → OUT
=============================
*/
try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        UPDATE tbl_detail_product
        SET status   = 'out',
            location = ?,
            out_date = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$supplier_id, $product['id']]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product berhasil di-scan OUT',
        'data' => [
            'serial_no'    => $product['serial_no'],
            'product_code' => $product['product_code'],
            'product_name' => $product['part_name'],
            'created_at'   => $product['created_at'],
            'out_date'     => date('Y-m-d H:i:s'),
            'status'       => 'out',
            'location'     => $supplier['name_supplier']
        ]
    ]);
} catch (PDOException $e) {

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'message' => 'Gagal update product',
        'error'   => $e->getMessage()
    ]);
}
