<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/production_planning/');
}

// ================= DATA =================
$productionDate = sanitize($_POST['production_date'] ?? '');

$lines = $_POST['line'] ?? [];
$productCodes = $_POST['product_code'] ?? [];
$qtys = $_POST['qty'] ?? [];
$materials = $_POST['material'] ?? [];

// ================= VALIDATION =================
if ($productionDate === '' || empty($productCodes)) {
    setAlert('error', 'Oops!', 'Data tidak lengkap', 'danger', 'OK');
    redirect('pages/production_planning/create.php');
}

try {

    $pdo->beginTransaction();

    $ppCode = 'PP-' . str_replace('-', '', $productionDate) . '-' . strtoupper(substr(uniqid(), -4));

    // ================= PREPARE =================
    $insertPP = $pdo->prepare("
        INSERT INTO tbl_production_planning
        (pp_code, line_id, product_code, shift, production_date, qty, total_qty, status)
        VALUES
        (:pp_code, :line_id, :product_code, :shift, :production_date, :qty, :total_qty, 'planned')
    ");

    $insertDetail = $pdo->prepare("
        INSERT INTO tbl_detail_production_planning
        (pp_id, jam, qty, status)
        VALUES
        (:pp_id, :jam, :qty, 'planned')
    ");

    $insertMaterial = $pdo->prepare("
        INSERT INTO tbl_pp_material
        (pp_id, part_id, part_code, type)
        VALUES
        (:pp_id, :part_id, :part_code, :type)
    ");

    $hasData = false;

    // ================= LOOP SHIFT =================
    foreach ($productCodes as $shiftId => $lineData) {

        foreach ($lineData as $lineId => $products) {

            foreach ($products as $index => $productCode) {

                $productCode = sanitize($productCode);
                if (!$productCode) continue;

                // ================= TOTAL QTY =================
                $totalQty = 0;

                foreach ($qtys[$shiftId][$lineId][$index] ?? [] as $q) {
                    $totalQty += (int)$q;
                }

                if ($totalQty <= 0) continue;

                $hasData = true;

                // ================= INSERT HEADER =================
                $insertPP->execute([
                    ':pp_code' => $ppCode,
                    ':line_id' => $lineId,
                    ':product_code' => $productCode,
                    ':shift' => $shiftId,
                    ':production_date' => $productionDate,
                    ':qty' => $totalQty,
                    ':total_qty' => $totalQty
                ]);

                $ppId = $pdo->lastInsertId();

                // ================= INSERT DETAIL =================
                foreach ($qtys[$shiftId][$lineId][$index] as $jam => $qty) {

                    $insertDetail->execute([
                        ':pp_id' => $ppId,
                        ':jam' => $jam,
                        ':qty' => (int)$qty
                    ]);
                }

                // ================= INSERT MATERIAL =================
                $selectedParts = $materials[$shiftId][$lineId][$index] ?? [];

                if (!empty($selectedParts)) {

                    // 🔥 FIX: pakai id_pa bukan part_id
                    $in = implode(',', array_fill(0, count($selectedParts), '?'));

                    $stmt = $pdo->prepare("
                        SELECT pa.id_pa, pa.part_id, pa.part_code, pa.remark
                        FROM tbl_part_assy pa
                        WHERE pa.id_pa IN ($in)
                    ");

                    $stmt->execute($selectedParts);
                    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($parts as $p) {

                        $insertMaterial->execute([
                            ':pp_id' => $ppId,
                            ':part_id' => $p['part_id'], // tetap part_id untuk relasi ke part
                            ':part_code' => $p['part_code'],
                            ':type' => $p['remark'] == 0 ? 'MAIN' : 'SUB'
                        ]);
                    }
                }
            }
        }
    }

    if (!$hasData) {
        throw new Exception('Semua qty 0');
    }

    $pdo->commit();

    setAlert('success', 'Berhasil', 'Production planning tersimpan', 'success', 'OK');
    redirect('pages/production_planning/');
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    handlePdoError($e, 'pages/production_planning/create.php');
}
