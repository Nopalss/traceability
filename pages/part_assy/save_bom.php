<?php
require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

$model      = trim($_POST['model'] ?? '');
$assyCode   = trim($_POST['assy_code'] ?? '');
$assyName   = trim($_POST['assy_name'] ?? '');
$items      = json_decode($_POST['items'] ?? '[]', true);

if (!$model || !$assyCode || empty($items)) {
    echo json_encode(["status" => "error", "msg" => "Data tidak lengkap"]);
    exit;
}

try {

    $pdo->beginTransaction();

    // ================= MODEL =================
    $checkModel = $pdo->prepare("SELECT id FROM tbl_model WHERE name = ?");
    $checkModel->execute([$model]);

    if ($checkModel->rowCount() > 0) {
        throw new Exception("Model sudah terdaftar!");
    }

    // ================= PART ASSY =================
    $checkPart = $pdo->prepare("SELECT part_code FROM tbl_part WHERE part_code = ?");
    $checkPart->execute([$assyCode]);

    if ($checkPart->rowCount() > 0) {
        throw new Exception("Model sudah terdaftar!");
    }

    // ambil supplier STI
    $sti = $pdo->query("
        SELECT id_supplier FROM tbl_supplier 
        WHERE name_supplier = 'PT. STI' AND status = 'supplier'
    ")->fetchColumn();

    if (!$sti) {
        throw new Exception("Supplier STI tidak ditemukan");
    }


    // ================= INSERT MODEL =================
    $pdo->prepare("
        INSERT INTO tbl_model (name, part_code, created_by)
        VALUES (?, ?, ?)
    ")->execute([$model, $assyCode, $_SESSION['username'] ?? 'system']);

    // ================= PREPARE =================
    $getSupplier = $pdo->prepare("
        SELECT id_supplier FROM tbl_supplier 
        WHERE name_supplier = :name AND status = 'supplier'
    ");

    // kalau assy belum ada → insert
    if ($checkPart->rowCount() == 0) {
        $pdo->prepare("
            INSERT INTO tbl_part (part_code, part_name, supplier, status, status_assy)
            VALUES (?, ?, ?, 'md', 1)
        ")->execute([$assyCode, $assyName, $sti]);
    }

    $getPart = $pdo->prepare("
        SELECT id_part FROM tbl_part 
        WHERE part_code = :code AND supplier = :supplier
    ");

    $checkPartGlobal = $pdo->prepare("
        SELECT id_part FROM tbl_part WHERE part_code = ?
    ");

    $insert = $pdo->prepare("
        INSERT INTO tbl_part_assy 
        (part_assy, part_code, qty, unit, remark, part_id, subs)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // ================= LOOP =================
    foreach ($items as $item) {

        $part_code = trim($item['part_code']);
        $qty       = (int)$item['qty'];
        $unit      = trim($item['unit']);
        $remark    = (int)$item['remark'];
        $supplier_name = trim($item['supplier']);
        $subs_val  = trim($item['subs'] ?? '');

        // 🔥 VALIDASI GLOBAL PART
        $checkPartGlobal->execute([$part_code]);
        if ($checkPartGlobal->rowCount() == 0) {
            throw new Exception("Part belum terdaftar di master: $part_code");
        }

        // --- supplier ---
        $getSupplier->execute(['name' => $supplier_name]);
        $supplier_id = $getSupplier->fetchColumn();

        if (!$supplier_id) {
            throw new Exception("Supplier tidak ditemukan: $supplier_name");
        }

        // --- part (specific supplier) ---
        $getPart->execute([
            'code' => $part_code,
            'supplier' => $supplier_id
        ]);

        $part_id = $getPart->fetchColumn();

        if (!$part_id) {
            throw new Exception("Part tidak ditemukan untuk supplier: $part_code - $supplier_name");
        }

        // ================= SUBSTITUTE =================
        $subs_id = 0;

        if ($remark == 1) {

            if (!$subs_val) {
                throw new Exception("SUBSTITUTE harus punya parent");
            }

            list($subs_code, $subs_supplier_name) = explode("__", $subs_val);

            // cek global subs
            $checkPartGlobal->execute([$subs_code]);
            if ($checkPartGlobal->rowCount() == 0) {
                throw new Exception("Subs part belum terdaftar: $subs_code");
            }

            // supplier subs
            $getSupplier->execute(['name' => $subs_supplier_name]);
            $subs_supplier_id = $getSupplier->fetchColumn();

            if (!$subs_supplier_id) {
                throw new Exception("Supplier subs tidak ditemukan: $subs_supplier_name");
            }

            // part subs
            $getPart->execute([
                'code' => $subs_code,
                'supplier' => $subs_supplier_id
            ]);

            $subs_id = $getPart->fetchColumn();

            if (!$subs_id) {
                throw new Exception("Subs tidak ditemukan: $subs_code - $subs_supplier_name");
            }

            if ($subs_id == $part_id) {
                throw new Exception("Part tidak boleh menjadi substitute dirinya sendiri");
            }
        }

        // ================= INSERT =================
        $insert->execute([
            $assyCode,
            $part_code,
            $qty,
            $unit,
            $remark,
            $part_id,
            $subs_id
        ]);
    }

    $pdo->commit();

    echo json_encode(["status" => "success", "msg" => "Data berhasil disimpan"]);
} catch (Exception $e) {

    if ($pdo->inTransaction()) $pdo->rollBack();

    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
