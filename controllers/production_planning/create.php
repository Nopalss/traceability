<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/production_planning/');
}

// ================================
// Ambil & sanitize data utama
// ================================
$productionDate = sanitize($_POST['production_date'] ?? '');
$lineId         = (int)($_POST['line_id'] ?? 0);
$productCode    = sanitize($_POST['part_assy'] ?? '');
$totalQty       = (int)($_POST['total_qty'] ?? 0);

$shifts = $_POST['shift'] ?? [];
$qtys   = $_POST['qty'] ?? [];
$starts = $_POST['start'] ?? [];
$ends   = $_POST['end'] ?? [];

// ================================
// Validasi awal
// ================================
if (
    $productionDate === '' ||
    $lineId <= 0 ||
    $productCode === '' ||
    empty($shifts) ||
    $totalQty <= 0
) {
    setAlert(
        'error',
        'Oops!',
        'Data production planning belum lengkap.',
        'danger',
        'Coba Lagi'
    );
    redirect('pages/production_planning/create.php');
}

try {
    $pdo->beginTransaction();

    // ================================
    // GENERATE PP CODE (GROUP ID)
    // ================================
    // contoh: PP-20260202-AB12
    $ppCode = 'PP-' . str_replace('-', '', $productionDate) . '-' . strtoupper(substr(uniqid(), -4));

    // ================================
    // PREPARE INSERT
    // ================================
    $insert = $pdo->prepare(
        "INSERT INTO tbl_production_planning
        (pp_code, line_id, product_code, shift, production_date, qty, total_qty, status, start, end)
        VALUES
        (:pp_code, :line_id, :product_code, :shift, :production_date, :qty, :total_qty, :status, :start, :end)"
    );

    $validRow = false;

    foreach ($shifts as $i => $shiftNo) {

        $shiftNo = (int)$shiftNo;
        $qty     = (int)($qtys[$i] ?? 0);
        $start   = sanitize($starts[$i] ?? '');
        $end     = sanitize($ends[$i] ?? '');

        // skip baris kosong
        if ($shiftNo <= 0 || $qty <= 0 || $start === '' || $end === '') {
            continue;
        }

        $validRow = true;

        $insert->execute([
            ':pp_code'         => $ppCode,
            ':line_id'         => $lineId,
            ':product_code'    => $productCode,
            ':shift'           => $shiftNo,
            ':production_date' => $productionDate,
            ':qty'             => $qty,
            ':total_qty'       => $totalQty,
            ':status'          => 'planned',
            ':start'           => $start,
            ':end'             => $end,
        ]);
    }

    if (!$validRow) {
        throw new Exception('Tidak ada data shift yang valid.');
    }

    $pdo->commit();

    setAlert(
        'success',
        'Berhasil!',
        'Production planning berhasil disimpan.',
        'success',
        'Oke'
    );

    redirect('pages/production_planning/');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    handlePdoError(
        $e,
        'pages/production_planning/create.php'
    );
}
