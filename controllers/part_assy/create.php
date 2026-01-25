<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/part_assy/');
}

$partAssy = sanitize($_POST['part_assy'] ?? '');
$items    = $_POST['items'] ?? [];

// ================================
// Validasi awal
// ================================
if ($partAssy === '' || empty($items)) {
    setAlert(
        'error',
        'Oops!',
        'Part Assy dan Part Yang Digunakan wajib diisi.',
        'danger',
        'Coba Lagi'
    );
    redirect('pages/part_assy/create.php');
}

try {
    $pdo->beginTransaction();

    // ================================
    // HAPUS BOM LAMA (REPLACE TOTAL)
    // ================================
    $delete = $pdo->prepare(
        "DELETE FROM tbl_part_assy WHERE part_assy = :part_assy"
    );
    $delete->execute([
        ':part_assy' => $partAssy
    ]);

    // ================================
    // PREPARE INSERT BARU
    // ================================
    $insert = $pdo->prepare(
        "INSERT INTO tbl_part_assy (part_assy, part_code, qty)
         VALUES (:part_assy, :part_code, :qty)"
    );

    $usedParts = [];

    foreach ($items as $row) {
        $component = sanitize($row['part_code'] ?? '');
        $qty       = (int)($row['qty'] ?? 0);

        // skip baris kosong
        if ($component === '' || $qty <= 0) {
            continue;
        }

        // ❌ assy tidak boleh pakai dirinya sendiri
        if ($component === $partAssy) {
            throw new Exception('Part Assy tidak boleh menggunakan dirinya sendiri.');
        }

        // ❌ cegah duplikat komponen
        if (in_array($component, $usedParts, true)) {
            throw new Exception('Part komponen tidak boleh duplikat.');
        }

        $usedParts[] = $component;

        $insert->execute([
            ':part_assy' => $partAssy,
            ':part_code' => $component,
            ':qty'       => $qty
        ]);
    }

    // ❌ tidak ada komponen valid
    if (empty($usedParts)) {
        throw new Exception('Tidak ada part komponen yang valid.');
    }

    $pdo->commit();

    setAlert(
        'success',
        'Berhasil!',
        'Part Assy berhasil diperbarui.',
        'success',
        'Oke'
    );

    redirect('pages/part_assy/');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    handlePdoError(
        $e,
        'pages/part_assy/edit.php?part_assy=' . urlencode($partAssy)
    );
}
