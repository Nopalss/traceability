<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

$data = $_POST['products'] ?? [];

if (!$data) {
    echo json_encode(['status' => 'ok']);
    exit;
}

/* ================================
   AGGREGATE TOTAL PRODUCT
================================ */

$productTotals = [];

foreach ($data as $item) {

    $code = $item['product_code'];
    $qty  = (int)$item['qty'];

    if (!$code || $qty <= 0) continue;

    if (!isset($productTotals[$code])) {
        $productTotals[$code] = 0;
    }

    $productTotals[$code] += $qty;
}

if (!$productTotals) {
    echo json_encode(['status' => 'ok']);
    exit;
}

/* ================================
   CHECK MATERIAL GLOBAL
================================ */

$allDetails = [];
$hasShortage = false;

foreach ($productTotals as $product => $totalQty) {

    // Ambil BOM + nama part
    $stmt = $pdo->prepare("
        SELECT pa.part_code, pa.qty, p.part_name
        FROM tbl_part_assy pa
        JOIN tbl_part p ON p.part_code = pa.part_code
        WHERE pa.part_assy = ?
    ");
    $stmt->execute([$product]);
    $bom = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($bom as $b) {

        $partCode = $b['part_code'];
        $partName = $b['part_name'];
        $need     = $b['qty'] * $totalQty;

        // Total stock gudang
        $stockStmt = $pdo->prepare("
            SELECT COALESCE(SUM(remain),0)
            FROM tbl_detail_part
            WHERE part_code = ?
            AND status = 'IN'
        ");
        $stockStmt->execute([$partCode]);
        $stock = (int)$stockStmt->fetchColumn();

        $shortage = $need - $stock;

        if ($shortage > 0) {
            $hasShortage = true;
        }

        $allDetails[] = [
            'product'   => $product,
            'part_code' => $partCode,
            'part_name' => $partName,
            'needed'    => $need,
            'available' => $stock,
            'shortage'  => $shortage > 0 ? $shortage : 0,
            'status'    => $shortage > 0 ? 'kurang' : 'cukup'
        ];
    }
}

/* ================================
   RESPONSE
================================ */

echo json_encode([
    'status'  => $hasShortage ? 'not_ok' : 'ok',
    'message' => $hasShortage
        ? 'Beberapa material tidak mencukupi.'
        : 'Semua material mencukupi.',
    'details' => $allDetails
]);
