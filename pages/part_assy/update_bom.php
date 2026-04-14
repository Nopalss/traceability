<?php
require '../../includes/config.php';
header('Content-Type: application/json');

$id         = $_POST['id'] ?? '';
$model      = trim($_POST['model'] ?? '');
$newAssy    = trim($_POST['assy_code'] ?? '');
$assyName   = trim($_POST['assy_name'] ?? '');
$items      = json_decode($_POST['items'] ?? '[]', true);

if (!$id || !$model || !$newAssy || empty($items)) {
    echo json_encode([
        "status" => "error",
        "msg" => "Data tidak lengkap"
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    /* =========================
       1. GET ASSY LAMA
    ========================== */
    $stmt = $pdo->prepare("SELECT part_code FROM tbl_model WHERE id=?");
    $stmt->execute([$id]);
    $oldAssy = $stmt->fetchColumn();

    if (!$oldAssy) {
        throw new Exception("Data tidak ditemukan");
    }

    /* =========================
       2. CEK DUPLICATE ASSY
    ========================== */
    if ($oldAssy !== $newAssy) {

        $check = $pdo->prepare("
            SELECT COUNT(*) FROM tbl_part WHERE part_code = ?
        ");
        $check->execute([$newAssy]);

        if ($check->fetchColumn() > 0) {
            throw new Exception("Part Code Assy sudah digunakan!");
        }
    }

    /* =========================
       3. UPDATE tbl_model
    ========================== */
    $stmt = $pdo->prepare("
        UPDATE tbl_model 
        SET name = ?, part_code = ?
        WHERE id = ?
    ");
    $stmt->execute([$model, $newAssy, $id]);

    /* =========================
       4. UPDATE tbl_part (assy)
    ========================== */
    $stmt = $pdo->prepare("
        UPDATE tbl_part 
        SET part_code = ?, part_name = ?
        WHERE part_code = ?
    ");
    $stmt->execute([$newAssy, $assyName, $oldAssy]);

    /* =========================
       5. UPDATE RELASI BOM
    ========================== */
    $stmt = $pdo->prepare("
        UPDATE tbl_part_assy 
        SET part_assy = ?
        WHERE part_assy = ?
    ");
    $stmt->execute([$newAssy, $oldAssy]);

    /* =========================
       6. DELETE OLD BOM
    ========================== */
    $pdo->prepare("
        DELETE FROM tbl_part_assy WHERE part_assy = ?
    ")->execute([$newAssy]);

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

    $insert = $pdo->prepare("
        INSERT INTO tbl_part_assy 
        (part_assy, part_code, qty, unit, remark, part_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    /* =========================
       7. INSERT NEW BOM
    ========================== */
    $used = [];

    foreach ($items as $i) {

        $part_code = trim($i['part_code'] ?? '');
        $qty       = (int)($i['qty'] ?? 0);
        $unit      = trim($i['unit'] ?? 'Pcs');
        $remark    = (int)($i['remark'] ?? 0);
        $supplier_name = trim($i['supplier'] ?? '');

        if (!$part_code) {
            throw new Exception("Ada part kosong");
        }

        if (!$supplier_name) {
            throw new Exception("Supplier kosong di part: $part_code");
        }

        if ($qty <= 0) {
            throw new Exception("Qty tidak valid di part: $part_code");
        }

        // 🔥 DUPLICATE CHECK (part + supplier)
        $key = $part_code . '__' . $supplier_name;

        if (in_array($key, $used)) {
            throw new Exception("Duplicate part: $key");
        }

        $used[] = $key;

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
           INSERT
        ========================== */
        $insert->execute([
            $newAssy,
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
        "msg" => "Data berhasil diupdate"
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
