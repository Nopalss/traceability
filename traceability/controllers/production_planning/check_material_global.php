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
   CHECK MATERIAL GLOBAL (FIXED)
================================ */

$allDetails = [];
$hasShortage = false;

foreach ($productTotals as $product => $totalQty) {

    $stmt = $pdo->prepare("
        SELECT 
            pa.part_code,
            pa.qty,
            pa.remark,
            pa.part_id,
            p.part_name,
            s.name_supplier
        FROM tbl_part_assy pa
        JOIN tbl_part p ON p.id_part = pa.part_id
        LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier
        WHERE pa.part_assy = ?
        ORDER BY pa.part_code
    ");
    $stmt->execute([$product]);
    $bom = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ================= GROUP =================
    $grouped = [];

    foreach ($bom as $b) {

        $key = $b['part_code']; // tetap grouping by part_code

        if (!isset($grouped[$key])) {
            $grouped[$key] = [];
        }

        $grouped[$key][] = $b;
    }

    // ================= PROCESS =================
    foreach ($grouped as $parts) {

        // pisahkan main & subs
        $main = null;
        $subs = [];

        foreach ($parts as $p) {
            if ($p['remark'] == 0) $main = $p;
            else $subs[] = $p;
        }

        if (!$main) continue;

        $needed = $main['qty'] * $totalQty;

        // ================= CHECK MAIN =================
        $stockStmt = $pdo->prepare("
            SELECT COALESCE(SUM(remain),0)
            FROM tbl_detail_part
            WHERE part_code = ?
            AND part_id = ?
            AND status = 'IN'
        ");

        $stockStmt->execute([$main['part_code'], $main['part_id']]);
        $stockMain = (int)$stockStmt->fetchColumn();

        $usedPart = $main;
        $available = $stockMain;
        $usedType = 'MAIN';

        // ================= SUBSTITUTE =================
        if ($stockMain < $needed && count($subs) > 0) {

            foreach ($subs as $sub) {

                $stockStmt->execute([$sub['part_code'], $sub['part_id']]);
                $stockSub = (int)$stockStmt->fetchColumn();

                if ($stockSub >= $needed) {
                    $usedPart = $sub;
                    $available = $stockSub;
                    $usedType = 'SUBSTITUTE';
                    break;
                }
            }
        }

        $shortage = $needed - $available;

        if ($shortage > 0) {
            $hasShortage = true;
        }

        $allDetails[] = [
            'product'   => $product,
            'part_code' => $usedPart['part_code'],
            'part_name' => $usedPart['part_name'] . " (" . $usedType . ")",
            'supplier'  => $usedPart['name_supplier'] ?? '-',
            'needed'    => $needed,
            'available' => $available,
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
