<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

$model      = trim($_POST['model'] ?? '');
$assyCode   = trim($_POST['assy_code'] ?? '');
$assyName   = trim($_POST['assy_name'] ?? '');
$items      = json_decode($_POST['items'] ?? '[]', true);

/* =========================
   VALIDASI AWAL
========================= */
if (!$model || !$assyCode || empty($items)) {
    echo json_encode([
        "status" => "error",
        "msg" => "Data tidak lengkap"
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    /* =========================
       1. CEK DUPLICATE MODEL
    ========================== */
    $checkModel = $pdo->prepare("
        SELECT id FROM tbl_model WHERE name = ?
    ");
    $checkModel->execute([$model]);

    if ($checkModel->rowCount() > 0) {

        $pdo->rollBack();

        echo json_encode([
            "status" => "error",
            "msg" => "Model / Part Assy sudah terdaftar!"
        ]);
        exit;
    }

    /* =========================
       2. INSERT MODEL
    ========================== */
    $stmt = $pdo->prepare("
        INSERT INTO tbl_model (name, part_code, created_by)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $model,
        $assyCode,
        $_SESSION['username'] ?? 'system'
    ]);

    /* =========================
       3. INSERT PART ASSY KE tbl_part
    ========================== */
    $checkPart = $pdo->prepare("
        SELECT part_code FROM tbl_part WHERE part_code = ?
    ");
    $checkPart->execute([$assyCode]);

    $checkSti = $pdo->prepare("
    SELECT id_supplier 
    FROM tbl_supplier 
    WHERE name_supplier = :name 
    AND status = :status
");

    $checkSti->execute([
        'name' => 'PT. STI',
        'status' => 'supplier'
    ]);

    $sti = $checkSti->fetchColumn();

    if ($checkPart->rowCount() == 0) {

        $insertPart = $pdo->prepare("
            INSERT INTO tbl_part (part_code, part_name, supplier, status, status_assy)
            VALUES (?, ?, $sti, 'md', 1)
        ");

        $insertPart->execute([
            $assyCode,
            $assyName
        ]);
    }

    /* =========================
       4. VALIDASI ITEMS
    ========================== */
    if (!is_array($items) || count($items) === 0) {
        throw new Exception("Item BOM kosong");
    }

    /* =========================
   4.5 CEK DUPLICATE PART DI BOM
========================= */
    $uniqueCheck = [];

    foreach ($items as $item) {

        $code = trim($item['part_code'] ?? '');

        if (!$code) continue;

        if (isset($uniqueCheck[$code])) {
            throw new Exception("Duplicate part_code terdeteksi: $code");
        }

        $uniqueCheck[$code] = true;
    }
    /* =========================
       5. INSERT BOM DETAIL
    ========================== */
    $insertBOM = $pdo->prepare("
        INSERT INTO tbl_part_assy (part_assy, part_code, qty, unit)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($items as $item) {

        $part_code = trim($item['part_code'] ?? '');
        $qty       = (int)($item['qty'] ?? 0);
        $unit      = trim($item['unit'] ?? 'Pcs');

        // VALIDASI PER ITEM
        if (!$part_code) {
            throw new Exception("Ada part_code kosong");
        }

        if ($qty <= 0) {
            throw new Exception("Qty tidak valid di part: $part_code");
        }

        $insertBOM->execute([
            $assyCode,
            $part_code,
            $qty,
            $unit
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "status" => "success",
        "msg" => "Data berhasil disimpan"
    ]);
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "status" => "error",
        "msg" => $e->getMessage()
    ]);
}
