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
       4.5 CEK DUPLICATE (PART + SUPPLIER)
    ========================== */
    $uniqueCheck = [];

    foreach ($items as $item) {

        $code = trim($item['part_code'] ?? '');
        $supplier = trim($item['supplier'] ?? '');

        if (!$code) continue;

        $key = $code . '__' . $supplier;

        if (isset($uniqueCheck[$key])) {
            throw new Exception("Duplicate part terdeteksi: $key");
        }

        $uniqueCheck[$key] = true;
    }

    /* =========================
       PREPARE QUERY
    ========================== */
    $getSupplier = $pdo->prepare("
        SELECT id_supplier 
        FROM tbl_supplier 
        WHERE name_supplier = :name 
        AND status = 'supplier'
    ");

    $getPart = $pdo->prepare("
        SELECT id_part 
        FROM tbl_part 
        WHERE part_code = :code 
        AND supplier = :supplier
    ");

    $insertBOM = $pdo->prepare("
        INSERT INTO tbl_part_assy 
        (part_assy, part_code, qty, unit, remark, part_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    /* =========================
       5. INSERT BOM DETAIL
    ========================== */
    foreach ($items as $item) {

        $part_code = trim($item['part_code'] ?? '');
        $qty       = (int)($item['qty'] ?? 0);
        $unit      = trim($item['unit'] ?? 'Pcs');
        $remark    = (int)($item['remark'] ?? 0);
        $supplier_name = trim($item['supplier'] ?? '');

        if (!$part_code) {
            throw new Exception("Ada part_code kosong");
        }

        if ($qty <= 0) {
            throw new Exception("Qty tidak valid di part: $part_code");
        }

        if (!$supplier_name) {
            throw new Exception("Supplier kosong di part: $part_code");
        }

        /* =========================
           GET SUPPLIER ID
        ========================== */
        $getSupplier->execute([
            'name' => $supplier_name
        ]);

        $supplier_id = $getSupplier->fetchColumn();

        if (!$supplier_id) {
            throw new Exception("Supplier tidak ditemukan: $supplier_name");
        }

        /* =========================
           GET PART ID
        ========================== */
        $getPart->execute([
            'code' => $part_code,
            'supplier' => $supplier_id
        ]);

        $part_id = $getPart->fetchColumn();

        if (!$part_id) {
            throw new Exception("Part tidak ditemukan: $part_code - $supplier_name");
        }

        /* =========================
           INSERT BOM
        ========================== */
        $insertBOM->execute([
            $assyCode,
            $part_code,
            $qty,
            $unit,
            $remark,
            $part_id
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
