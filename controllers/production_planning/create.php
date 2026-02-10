<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/production_planning/');
}

// ================================
// HEADER DATA
// ================================
$productionDate = sanitize($_POST['production_date'] ?? '');
$lineId = (int)($_POST['line_id'] ?? 0);

$productCodes = $_POST['product_code'] ?? [];
$qtys = $_POST['qty'] ?? [];
$jams = $_POST['jam'] ?? [];

// ================================
// BASIC VALIDATION
// ================================
if ($productionDate === '' || $lineId <= 0 || empty($productCodes)) {
    setAlert('error', 'Oops!', 'Data production planning belum lengkap.', 'danger', 'Coba Lagi');
    redirect('pages/production_planning/create.php');
}

try {

    $pdo->beginTransaction();

    // ================================
    // GENERATE PP CODE (GROUP ID)
    // ================================
    $ppCode = 'PP-' . str_replace('-', '', $productionDate) . '-' . strtoupper(substr(uniqid(), -4));

    // ================================
    // PREPARE HEADER INSERT
    // ================================
    $insertPP = $pdo->prepare("
        INSERT INTO tbl_production_planning
        (pp_code, line_id, product_code, shift, production_date, qty, total_qty, status)
        VALUES
        (:pp_code, :line_id, :product_code, :shift, :production_date, :qty, :total_qty, 'planned')
    ");

    // ================================
    // PREPARE DETAIL INSERT
    // ================================
    $insertDetail = $pdo->prepare("
        INSERT INTO tbl_detail_production_planning
        (pp_id, jam, qty, status)
        VALUES
        (:pp_id, :jam, :qty, 'planned')
    ");

    $hasData = false;

    // ================================
    // LOOP SHIFT → PRODUCT
    // ================================
    foreach ($productCodes as $shiftNo => $products) {

        foreach ($products as $pIndex => $productCode) {

            $productCode = sanitize($productCode);
            if ($productCode === '') continue;

            $totalQty = 0;

            foreach ($qtys[$shiftNo][$pIndex] as $q) {
                $totalQty += (int)$q;
            }

            // skip product yg semua qty = 0
            if ($totalQty <= 0) continue;

            $hasData = true;

            // ================================
            // INSERT HEADER
            // ================================
            $insertPP->execute([
                ':pp_code' => $ppCode,
                ':line_id' => $lineId,
                ':product_code' => $productCode,
                ':shift' => $shiftNo,
                ':production_date' => $productionDate,
                ':qty' => $totalQty,
                ':total_qty' => $totalQty
            ]);

            $ppId = $pdo->lastInsertId();

            // ================================
            // INSERT DETAIL JAM + OT
            // ================================
            foreach ($qtys[$shiftNo][$pIndex] as $jIndex => $qty) {

                $qty = (int)$qty;
                $jam = sanitize($jams[$shiftNo][$pIndex][$jIndex] ?? '');

                if ($jam === '') continue;

                $insertDetail->execute([
                    ':pp_id' => $ppId,
                    ':jam' => $jam,   // bisa jam biasa / OT
                    ':qty' => $qty
                ]);
            }
        }
    }

    if (!$hasData) {
        setAlert('error', 'Oops!', 'Semua qty bernilai 0.', 'danger', 'Coba Lagi');
        redirect('pages/production_planning/create.php');
    }

    $pdo->commit();

    setAlert('success', 'Berhasil!', 'Production planning berhasil disimpan.', 'success', 'OK');
    redirect('pages/production_planning/');
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    handlePdoError($e, 'pages/production_planning/create.php');
}
