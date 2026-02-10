<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/production_planning/');
}

// ================================
// HEADER
// ================================
$ppCode = sanitize($_POST['pp_code'] ?? '');
$productionDate = sanitize($_POST['production_date'] ?? '');
$lineId = (int)($_POST['line_id'] ?? 0);

$productCodes = $_POST['product_code'] ?? [];
$qtys = $_POST['qty'] ?? [];
$jams = $_POST['jam'] ?? [];

if ($ppCode == '' || $productionDate == '' || $lineId <= 0) {
    setAlert('error', 'Oops', 'Data tidak lengkap', 'danger', 'OK');
    redirect('pages/production_planning/');
}

try {

    $pdo->beginTransaction();

    // ================================
    // DELETE OLD DETAIL
    // ================================
    $pdo->prepare("
        DELETE FROM tbl_detail_production_planning
        WHERE pp_id IN (
            SELECT pp_id FROM tbl_production_planning WHERE pp_code=?
        )
    ")->execute([$ppCode]);

    // ================================
    // DELETE OLD HEADER
    // ================================
    $pdo->prepare("DELETE FROM tbl_production_planning WHERE pp_code=?")
        ->execute([$ppCode]);

    // ================================
    // PREPARE INSERT
    // ================================
    $insertPP = $pdo->prepare("
        INSERT INTO tbl_production_planning
        (pp_code,line_id,product_code,shift,production_date,qty,total_qty,status)
        VALUES
        (:pp_code,:line_id,:product_code,:shift,:production_date,:qty,:total_qty,'planned')
    ");

    $insertDetail = $pdo->prepare("
        INSERT INTO tbl_detail_production_planning
        (pp_id,jam,qty,status)
        VALUES
        (:pp_id,:jam,:qty,'planned')
    ");

    $hasData = false;

    foreach ($productCodes as $shiftNo => $products) {

        foreach ($products as $pIndex => $product) {

            $product = sanitize($product);
            if ($product == '') continue;

            $total = 0;

            foreach ($qtys[$shiftNo][$pIndex] as $q) {
                $total += (int)$q;
            }

            // ================================
            // TIDAK DISKIP WALAU TOTAL 0
            // ================================
            $hasData = true;

            // ================================
            // INSERT HEADER
            // ================================
            $insertPP->execute([
                ':pp_code' => $ppCode,
                ':line_id' => $lineId,
                ':product_code' => $product,
                ':shift' => $shiftNo,
                ':production_date' => $productionDate,
                ':qty' => $total,
                ':total_qty' => $total
            ]);

            $ppId = $pdo->lastInsertId();

            // ================================
            // INSERT DETAIL JAM + OT
            // ================================
            foreach ($qtys[$shiftNo][$pIndex] as $j => $q) {

                $jam = sanitize($jams[$shiftNo][$pIndex][$j] ?? '');
                $q = (int)$q;

                if ($jam == '') continue;

                $insertDetail->execute([
                    ':pp_id' => $ppId,
                    ':jam' => $jam,
                    ':qty' => $q
                ]);
            }
        }
    }

    if (!$hasData) {
        setAlert('error', 'Oops!', 'Tidak ada product.', 'danger', 'OK');
        redirect('pages/production_planning/');
    }

    $pdo->commit();

    setAlert('success', 'Berhasil', 'Production planning berhasil diupdate', 'success', 'OK');
    redirect('pages/production_planning/');
} catch (Exception $e) {

    if ($pdo->inTransaction()) $pdo->rollBack();

    handlePdoError($e, 'pages/production_planning/');
}
