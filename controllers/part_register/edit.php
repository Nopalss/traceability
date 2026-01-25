<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Ambil & sanitasi input ---
    $id_part   = isset($_POST['id_part']) ? (int) $_POST['id_part'] : 0;
    $part_code = isset($_POST['part_code']) ? sanitize($_POST['part_code']) : null;
    $part_name = isset($_POST['part_name']) ? sanitize($_POST['part_name']) : null;
    $supplier  = isset($_POST['supplier'])  ? sanitize($_POST['supplier'])  : null;

    // --- Validasi ID ---
    if ($id_part <= 0) {
        setAlert(
            'error',
            'Oops!',
            'Data part tidak valid.',
            'danger',
            'Kembali'
        );
        redirect('pages/part_register/');
    }

    // --- Validasi field wajib ---
    $required = compact('part_code', 'part_name', 'supplier');
    foreach ($required as $field => $value) {
        if (empty($value)) {
            setAlert(
                'error',
                'Oops!',
                "Field <b>$field</b> tidak boleh kosong.",
                'danger',
                'Coba Lagi'
            );
            redirect("pages/part_register/edit.php?id=$id_part");
        }
    }

    // --- Cek part exist ---
    $checkPart = $pdo->prepare(
        "SELECT id_part FROM tbl_part WHERE id_part = :id_part LIMIT 1"
    );
    $checkPart->execute([':id_part' => $id_part]);

    if (!$checkPart->fetchColumn()) {
        setAlert(
            'error',
            'Oops!',
            'Data part tidak ditemukan.',
            'danger',
            'Kembali'
        );
        redirect('pages/part_register/');
    }

    // --- Cek duplikasi part_code (kecuali dirinya sendiri) ---
    $checkCode = $pdo->prepare(
        "SELECT 1 FROM tbl_part 
         WHERE part_code = :part_code 
           AND id_part <> :id_part
         LIMIT 1"
    );
    $checkCode->execute([
        ':part_code' => $part_code,
        ':id_part'   => $id_part
    ]);

    if ($checkCode->fetchColumn()) {
        setAlert(
            'error',
            'Oops!',
            'Part Code sudah digunakan oleh part lain.',
            'danger',
            'Coba Lagi'
        );
        redirect("pages/part_register/edit.php?id=$id_part");
    }

    try {
        // --- Update data part ---
        $sql = "UPDATE tbl_part 
                SET part_code = :part_code,
                    part_name = :part_name,
                    supplier  = :supplier
                WHERE id_part = :id_part";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':part_code' => $part_code,
            ':part_name' => $part_name,
            ':supplier'  => $supplier,
            ':id_part'   => $id_part
        ]);

        setAlert(
            'success',
            'Berhasil!',
            'Data part berhasil diperbarui.',
            'success',
            'Oke'
        );
    } catch (PDOException $e) {
        handlePdoError($e, 'pages/part_register/');
    }
}

// --- Redirect akhir ---
redirect('pages/part_register/');
