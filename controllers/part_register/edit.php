<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =============================
    // AMBIL & NORMALIZE INPUT
    // =============================
    $id_part   = isset($_POST['id_part']) ? (int) $_POST['id_part'] : 0;
    $part_code = isset($_POST['part_code']) ? trim(sanitize($_POST['part_code'])) : null;
    $part_name = isset($_POST['part_name']) ? trim(sanitize($_POST['part_name'])) : null;
    $supplier  = isset($_POST['supplier'])  ? trim(sanitize($_POST['supplier']))  : null;

    // normalize part_code (hapus spasi)
    $part_code = preg_replace('/\s+/', '', $part_code);

    // normalize part_name
    $part_name = strtolower($part_name);
    $part_name = preg_replace('/\s*,\s*/', ',', $part_name);
    $part_name = preg_replace('/\s+/', ' ', $part_name);
    $part_name = trim($part_name);

    // =============================
    // VALIDASI ID
    // =============================
    if ($id_part <= 0) {
        setAlert('error', 'Oops!', 'Data part tidak valid.', 'danger', 'Kembali');
        redirect('pages/part_register/');
    }

    // =============================
    // VALIDASI WAJIB
    // =============================
    $required = compact('part_code', 'part_name', 'supplier');
    foreach ($required as $field => $value) {
        if (empty($value)) {
            setAlert('error', 'Oops!', "Field <b>$field</b> tidak boleh kosong.", 'danger', 'Coba Lagi');
            redirect("pages/part_register/edit.php?id=$id_part");
        }
    }

    // =============================
    // VALIDASI FORMAT PART CODE
    // =============================
    if (!preg_match('/^[0-9]+$/', $part_code)) {
        setAlert('error', 'Oops!', 'Part Code harus berupa angka.', 'danger', 'Coba Lagi');
        redirect("pages/part_register/edit.php?id=$id_part");
    }

    // =============================
    // VALIDASI SUPPLIER EXIST
    // =============================
    $checkSupplier = $pdo->prepare("
        SELECT id_supplier 
        FROM tbl_supplier 
        WHERE id_supplier = :supplier 
        AND status = 'supplier'
        LIMIT 1
    ");
    $checkSupplier->execute([':supplier' => $supplier]);

    if (!$checkSupplier->fetch()) {
        setAlert('error', 'Oops!', 'Supplier tidak ditemukan.', 'danger', 'Coba Lagi');
        redirect("pages/part_register/edit.php?id=$id_part");
    }

    // =============================
    // CEK PART EXIST
    // =============================
    $checkPart = $pdo->prepare("
        SELECT id_part 
        FROM tbl_part 
        WHERE id_part = :id_part 
        LIMIT 1
    ");
    $checkPart->execute([':id_part' => $id_part]);

    if (!$checkPart->fetchColumn()) {
        setAlert('error', 'Oops!', 'Data part tidak ditemukan.', 'danger', 'Kembali');
        redirect('pages/part_register/');
    }

    // =============================
    // CEK DUPLICATE (FIXED ✅)
    // UNIQUE = part_code + supplier
    // exclude dirinya sendiri
    // =============================
    $checkDuplicate = $pdo->prepare("
        SELECT 1 
        FROM tbl_part 
        WHERE part_code = :part_code 
          AND supplier  = :supplier
          AND id_part <> :id_part
        LIMIT 1
    ");

    $checkDuplicate->execute([
        ':part_code' => $part_code,
        ':supplier'  => $supplier,
        ':id_part'   => $id_part
    ]);

    if ($checkDuplicate->fetchColumn()) {
        setAlert('error', 'Oops!', 'Part sudah digunakan oleh data lain.', 'danger', 'Coba Lagi');
        redirect("pages/part_register/edit.php?id=$id_part");
    }

    try {

        // =============================
        // UPDATE
        // =============================
        $sql = "UPDATE tbl_part 
                SET part_code = :part_code,
                    part_name = :part_name,
                    supplier  = :supplier
                WHERE id_part = :id_part";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':part_code' => $part_code,
            ':part_name' => strtoupper($part_name),
            ':supplier'  => $supplier,
            ':id_part'   => $id_part
        ]);

        setAlert('success', 'Berhasil!', 'Data part berhasil diperbarui.', 'success', 'Oke');
    } catch (PDOException $e) {

        // fallback kalau kena unique constraint DB
        if ($e->errorInfo[1] == 1062) {
            setAlert('error', 'Oops!', 'Duplicate part.', 'danger', 'Coba Lagi');
        } else {
            handlePdoError($e, 'pages/part_register/');
        }
    }
}

// =============================
redirect('pages/part_register/');
