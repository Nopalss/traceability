<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =============================
    // AMBIL & NORMALIZE INPUT
    // =============================
    $part_code = isset($_POST['part_code']) ? trim(sanitize($_POST['part_code'])) : null;
    $part_name = isset($_POST['part_name']) ? trim(sanitize($_POST['part_name'])) : null;
    $supplier  = isset($_POST['supplier'])  ? trim(sanitize($_POST['supplier']))  : null;

    // normalize part_code (hapus spasi)
    $part_code = preg_replace('/\s+/', '', $part_code);

    // normalize part_name (rapihin spasi & koma)
    $part_name = strtolower($part_name);
    $part_name = preg_replace('/\s*,\s*/', ',', $part_name);
    $part_name = preg_replace('/\s+/', ' ', $part_name);
    $part_name = trim($part_name);

    // =============================
    // VALIDASI WAJIB
    // =============================
    $required = compact('part_code', 'part_name', 'supplier');
    foreach ($required as $field => $value) {
        if (empty($value)) {
            setAlert('error', 'Oops!', "Field <b>$field</b> tidak boleh kosong.", 'danger', 'Coba Lagi');
            redirect('pages/part_register/create.php');
        }
    }

    // =============================
    // VALIDASI FORMAT PART CODE
    // =============================
    if (!preg_match('/^[0-9]+$/', $part_code)) {
        setAlert('error', 'Oops!', 'Part Code harus berupa angka.', 'danger', 'Coba Lagi');
        redirect('pages/part_register/create.php');
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
        redirect('pages/part_register/create.php');
    }

    // =============================
    // CEK DUPLICATE (FIXED ✅)
    // UNIQUE = part_code + supplier
    // =============================
    $check = $pdo->prepare("
        SELECT 1 
        FROM tbl_part 
        WHERE part_code = :part_code 
        AND supplier = :supplier
        LIMIT 1
    ");

    $check->execute([
        ':part_code' => $part_code,
        ':supplier'  => $supplier
    ]);

    if ($check->fetchColumn()) {
        setAlert('error', 'Oops!', 'Part sudah terdaftar.', 'danger', 'Coba Lagi');
        redirect('pages/part_register/create.php');
    }

    try {

        // =============================
        // INSERT
        // =============================
        $sql = "INSERT INTO tbl_part (part_code, part_name, supplier)
                VALUES (:part_code, :part_name, :supplier)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':part_code' => $part_code,
            ':part_name' => strtoupper($part_name), // konsisten uppercase
            ':supplier'  => $supplier
        ]);

        setAlert('success', 'Berhasil!', 'Part berhasil ditambahkan.', 'success', 'Oke');
    } catch (PDOException $e) {

        // fallback kalau kena UNIQUE constraint dari DB
        if ($e->errorInfo[1] == 1062) {
            setAlert('error', 'Oops!', 'Data sudah ada.', 'danger', 'Coba Lagi');
        } else {
            handlePdoError($e, 'pages/part_register/create.php');
        }
    }
}

// =============================
redirect('pages/part_register/create.php');
