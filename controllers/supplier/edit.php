<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';

$response = [
    'success' => false,
    'message' => 'Invalid request'
];

/* =============================
   NORMALIZE FUNCTION (CONSISTENT)
============================= */
function normalize($str)
{
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9]/', '', $str);
    return $str;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Ambil JSON request
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data) {
            throw new Exception('Data tidak valid.');
        }

        // Ambil input
        $id_supplier   = intval($data['id_supplier'] ?? 0);
        $name_supplier = trim(sanitize($data['name_supplier'] ?? ''));

        if ($id_supplier <= 0 || $name_supplier === '') {
            throw new Exception('Data tidak lengkap.');
        }

        // VALIDASI: minimal harus ada huruf
        if (!preg_match('/[a-zA-Z]/', $name_supplier)) {
            throw new Exception('Nama supplier tidak valid.');
        }

        $normalized_input = normalize($name_supplier);

        /* =============================
           CEK EXIST
        ============================= */
        $checkExist = $pdo->prepare("
            SELECT id_supplier 
            FROM tbl_supplier
            WHERE id_supplier = :id
            AND status = 'supplier'
        ");

        $checkExist->execute([
            ':id' => $id_supplier
        ]);

        if (!$checkExist->fetch()) {
            throw new Exception('Supplier tidak ditemukan.');
        }

        /* =============================
           AMBIL SEMUA SUPPLIER (BIAR CONSISTENT)
        ============================= */
        $dbSuppliers = $pdo->query("
            SELECT id_supplier, name_supplier 
            FROM tbl_supplier 
            WHERE status='supplier'
        ")->fetchAll(PDO::FETCH_ASSOC);

        $dbNormalized = [];

        foreach ($dbSuppliers as $row) {
            if ($row['id_supplier'] == $id_supplier) continue;

            $norm = normalize($row['name_supplier']);
            $dbNormalized[$norm] = true;
        }

        /* =============================
           CEK DUPLICATE (STRONG)
        ============================= */
        if (isset($dbNormalized[$normalized_input])) {
            throw new Exception('Nama supplier sudah digunakan.');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE tbl_supplier
            SET name_supplier = :name
            WHERE id_supplier = :id
            AND status = 'supplier'
        ");

        $stmt->execute([
            ':name' => $name_supplier,
            ':id'   => $id_supplier
        ]);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = 'Supplier berhasil diperbarui.';
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
