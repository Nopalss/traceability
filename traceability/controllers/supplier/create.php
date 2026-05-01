<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';

$response = [
    'success' => false,
    'message' => 'Invalid request'
];

/* =============================
   NORMALIZE FUNCTION (SAMA DENGAN CSV API)
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
        $name_supplier = trim(sanitize($data['name_supplier'] ?? ''));

        if ($name_supplier === '') {
            throw new Exception('Nama supplier wajib diisi.');
        }

        // VALIDASI: minimal harus ada huruf
        if (!preg_match('/[a-zA-Z]/', $name_supplier)) {
            throw new Exception('Nama supplier tidak valid.');
        }

        $normalized_input = normalize($name_supplier);

        /* =============================
           AMBIL SEMUA SUPPLIER (BIAR CONSISTENT)
        ============================= */
        $dbSuppliers = $pdo->query("
            SELECT name_supplier 
            FROM tbl_supplier 
            WHERE status='supplier'
        ")->fetchAll(PDO::FETCH_COLUMN);

        // normalize semua
        $dbNormalized = array_map(function ($s) {
            return normalize($s);
        }, $dbSuppliers);

        // ubah jadi hash (lebih cepat)
        $dbNormalized = array_flip($dbNormalized);

        /* =============================
           CEK DUPLICATE (STRONG)
        ============================= */
        if (isset($dbNormalized[$normalized_input])) {
            throw new Exception('Supplier sudah ada.');
        }

        // Mulai transaction
        $pdo->beginTransaction();

        // Insert supplier
        $stmt = $pdo->prepare("
            INSERT INTO tbl_supplier (name_supplier, created_by, status)
            VALUES (:name, :created_by, 'supplier')
        ");

        $stmt->execute([
            ':name' => $name_supplier,
            ':created_by' => $_SESSION['username'] ?? 'system'
        ]);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = "Supplier '{$name_supplier}' berhasil ditambahkan.";
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
