<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';

$response = [
    'success' => false,
    'message' => 'Invalid request'
];

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

        // Cek duplicate supplier
        $check = $pdo->prepare("
            SELECT id_supplier 
            FROM tbl_supplier 
            WHERE LOWER(name_supplier) = LOWER(:name)
            AND status = 'supplier'
        ");

        $check->execute([
            ':name' => $name_supplier
        ]);

        if ($check->fetch()) {
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
