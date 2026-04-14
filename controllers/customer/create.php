<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';

$response = [
    'success' => false,
    'message' => 'Invalid request'
];

/* =============================
   NORMALIZE (CONSISTENT)
============================= */
function normalize($str)
{
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9]/', '', $str);
    return $str;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        // Ambil JSON
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data) {
            throw new Exception('Data tidak valid.');
        }

        // FIX: field name
        $name_customer = trim(sanitize($data['name_Customer'] ?? ''));

        if ($name_customer === '') {
            throw new Exception('Nama customer wajib diisi.');
        }

        // VALIDASI: harus ada huruf
        if (!preg_match('/[a-zA-Z]/', $name_customer)) {
            throw new Exception('Nama customer tidak valid.');
        }

        $normalized_input = normalize($name_customer);

        /* =============================
           LOAD DB (CONSISTENT)
        ============================= */
        $dbCustomers = $pdo->query("
            SELECT name_supplier 
            FROM tbl_supplier 
            WHERE status='customer'
        ")->fetchAll(PDO::FETCH_COLUMN);

        $dbNormalized = [];
        foreach ($dbCustomers as $c) {
            $dbNormalized[normalize($c)] = true;
        }

        /* =============================
           CEK DUPLICATE (STRONG)
        ============================= */
        if (isset($dbNormalized[$normalized_input])) {
            throw new Exception('Customer sudah ada.');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO tbl_supplier (name_supplier, created_by, status)
            VALUES (:name, :created_by, 'customer')
        ");

        $stmt->execute([
            ':name' => $name_customer,
            ':created_by' => $_SESSION['username'] ?? 'system'
        ]);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = "Customer '{$name_customer}' berhasil ditambahkan.";
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
