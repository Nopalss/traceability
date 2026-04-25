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
            SELECT COUNT(*) FROM tbl_model WHERE part_code = ?
        ");
        $check->execute([$newAssy]);

        if ($check->fetchColumn() > 0) {
            throw new Exception("Part Code Assy sudah digunakan!");
        }
    }

    /* =========================
       3. UPDATE tbl_model
    ========================== */
    $pdo->prepare("
        UPDATE tbl_model 
        SET name = ?, part_code = ?
        WHERE id = ?
    ")->execute([$model, $newAssy, $id]);

    /* =========================
       4. UPDATE tbl_part (assy)
    ========================== */
    $pdo->prepare("
        UPDATE tbl_part 
        SET part_code = ?, part_name = ?
        WHERE part_code = ?
    ")->execute([$newAssy, $assyName, $oldAssy]);

    /* =========================
       5. DELETE OLD BOM (FIX)
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
        (part_assy, part_code, qty, unit, remark, part_id, subs)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    /* =========================
       INSERT NEW BOM
    ========================== */
    $used = [];
    $mainParts = [];

    foreach ($items as $i) {

        $part_code     = trim($i['part_code'] ?? '');
        $qty           = (int)($i['qty'] ?? 0);
        $unit          = trim($i['unit'] ?? 'Pcs');
        $remark        = (int)($i['remark'] ?? 0);
        $supplier_name = trim($i['supplier'] ?? '');
        $subs_val      = trim($i['subs'] ?? '');

        if (!$part_code) {
            throw new Exception("Ada part kosong");
        }

        if (!$supplier_name) {
            throw new Exception("Supplier kosong di part: $part_code");
        }

        if ($qty <= 0) {
            throw new Exception("Qty tidak valid di part: $part_code");
        }

        // 🔥 DUPLICATE CHECK
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
           HANDLE SUBS
        ========================== */
        $subs_id = 0;

        if ($remark == 0) {
            $mainParts[] = $part_id;
        }

        if ($remark == 1) {

            if (!$subs_val) {
                throw new Exception("SUBSTITUTE harus punya parent");
            }

            $partsSubs = explode("__", $subs_val);

            if (count($partsSubs) !== 2) {
                throw new Exception("Format subs tidak valid");
            }

            [$subs_code, $subs_supplier_name] = $partsSubs;

            // GET SUPPLIER SUBS
            $getSupplier->execute([
                'name' => $subs_supplier_name
            ]);

            $subs_supplier_id = $getSupplier->fetchColumn();

            if (!$subs_supplier_id) {
                throw new Exception("Supplier subs tidak ditemukan: $subs_supplier_name");
            }

            // GET PART SUBS
            $getPart->execute([
                'code' => $subs_code,
                'supplier' => $subs_supplier_id
            ]);

            $subs_id = $getPart->fetchColumn();

            if (!$subs_id) {
                throw new Exception("Subs tidak ditemukan: $subs_code - $subs_supplier_name");
            }

            // VALIDASI SELF
            if ($subs_id == $part_id) {
                throw new Exception("Part tidak boleh menjadi substitute dirinya sendiri");
            }

            // VALIDASI PARENT EXIST
            if (!in_array($subs_id, $mainParts)) {
                throw new Exception("Parent MAIN belum ada sebelum SUBSTITUTE");
            }
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
            $part_id,
            $subs_id
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
