<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';
$partAssy = $_POST['part_assy'] ?? '';
$items    = $_POST['items'] ?? [];

if ($partAssy === '' || empty($items)) {
    redirect('pages/part_assy/');
}

try {
    $pdo->beginTransaction();

    // hapus BOM lama
    $pdo->prepare("DELETE FROM tbl_part_assy WHERE part_assy = :assy")
        ->execute([':assy' => $partAssy]);

    $stmt = $pdo->prepare("
        INSERT INTO tbl_part_assy (part_assy, part_code, qty)
        VALUES (:assy, :code, :qty)
    ");

    foreach ($items as $row) {
        if (empty($row['part_code']) || $row['qty'] <= 0) continue;
        if ($row['part_code'] === $partAssy) continue;

        $stmt->execute([
            ':assy' => $partAssy,
            ':code' => $row['part_code'],
            ':qty'  => (int)$row['qty']
        ]);
    }


    $pdo->commit();
    setAlert(
        'success',
        'Berhasil!',
        'Part Assy Berhasil diperbarui.',
        'success',
        'Oke'
    );
    redirect('pages/part_assy/');
} catch (Exception $e) {
    $pdo->rollBack();
    handlePdoError($e, 'pages/part_register/');
}
