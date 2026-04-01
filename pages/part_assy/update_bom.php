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
       2. CEK DUPLICATE ASSY (kalau berubah)
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
       5. UPDATE RELASI BOM (IMPORTANT!)
    ========================== */
    $stmt = $pdo->prepare("
        UPDATE tbl_part_assy 
        SET part_assy = ?
        WHERE part_assy = ?
    ");
    $stmt->execute([$newAssy, $oldAssy]);

    /* =========================
       6. DELETE OLD BOM DETAIL
    ========================== */
    $pdo->prepare("
        DELETE FROM tbl_part_assy WHERE part_assy = ?
    ")->execute([$newAssy]);

    /* =========================
       7. INSERT NEW BOM
    ========================== */
    $insert = $pdo->prepare("
        INSERT INTO tbl_part_assy (part_assy, part_code, qty, unit)
        VALUES (?, ?, ?, ?)
    ");

    $used = [];

    foreach ($items as $i) {

        $part_code = trim($i['part_code'] ?? '');
        $qty       = (int)($i['qty'] ?? 0);
        $unit      = trim($i['unit'] ?? 'Pcs');

        if (!$part_code) {
            throw new Exception("Ada part kosong");
        }

        if (in_array($part_code, $used)) {
            throw new Exception("Duplicate part di BOM: $part_code");
        }

        if ($qty <= 0) {
            throw new Exception("Qty tidak valid di part: $part_code");
        }

        $used[] = $part_code;

        $insert->execute([
            $newAssy,
            $part_code,
            $qty,
            $unit
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
