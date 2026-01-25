<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Ambil & sanitasi input ---
    $part_code = isset($_POST['part_code']) ? sanitize($_POST['part_code']) : null;
    $part_name = isset($_POST['part_name']) ? sanitize($_POST['part_name']) : null;
    $supplier  = isset($_POST['supplier'])  ? sanitize($_POST['supplier'])  : null;

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
            redirect('pages/part_register/create.php');
        }
    }

    // --- Cek Part Code sudah terdaftar ---
    $check = $pdo->prepare(
        "SELECT 1 FROM tbl_part WHERE part_code = :part_code LIMIT 1"
    );
    $check->execute([':part_code' => $part_code]);

    if ($check->fetchColumn()) {
        setAlert(
            'error',
            'Oops!',
            'Part Code sudah terdaftar.',
            'danger',
            'Coba Lagi'
        );
        redirect('pages/part_register/create.php');
    }

    try {
        // --- Insert data part ---
        $sql = "INSERT INTO tbl_part (part_code, part_name, supplier)
                VALUES (:part_code, :part_name, :supplier)";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':part_code' => $part_code,
            ':part_name' => $part_name,
            ':supplier'  => $supplier
        ]);

        setAlert(
            'success',
            'Berhasil!',
            'Part berhasil ditambahkan.',
            'success',
            'Oke'
        );
    } catch (PDOException $e) {
        handlePdoError($e, 'pages/part_register/create.php');
    }
}

// --- Redirect akhir ---
redirect('pages/part_register/create.php');
