<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

$product_code = $_POST['product_code'] ?? '';
$total_qty    = (int)($_POST['total_qty'] ?? 0);

if (!$product_code || $total_qty <= 0) {
    echo json_encode(['status' => 'ok']);
    exit;
}

/* ===============================
   AMBIL BOM
================================ */
$stmt = $pdo->prepare("
    SELECT part_code, qty 
    FROM tbl_part_assy
    WHERE part_assy = ?
");
$stmt->execute([$product_code]);
$bom = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$bom) {
    echo json_encode(['status' => 'ok']);
    exit;
}

$insufficient = [];

foreach ($bom as $b) {

    $part = $b['part_code'];
    $need = $b['qty'] * $total_qty;

    $stockStmt = $pdo->prepare("
        SELECT SUM(remain) as total_stock
        FROM tbl_detail_part
        WHERE part_code = ?
        AND status = 'IN'
    ");
    $stockStmt->execute([$part]);
    $stock = (int) $stockStmt->fetchColumn();

    if ($stock < $need) {
        $insufficient[] = [
            'part_code' => $part,
            'needed' => $need,
            'available' => $stock
        ];
    }
}

if ($insufficient) {
    echo json_encode([
        'status' => 'not_ok',
        'details' => $insufficient
    ]);
} else {
    echo json_encode(['status' => 'ok']);
}
