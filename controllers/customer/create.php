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

        // Ambil JSON dari request
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (!$data) {
            throw new Exception('Data tidak valid.');
        }

        // Ambil dan bersihkan input
        $name_customer = trim(sanitize($data['name_Customer'] ?? ''));

        if ($name_customer === '') {
            throw new Exception('Nama customer wajib diisi.');
        }

        // Cek duplicate customer
        $check = $pdo->prepare("
            SELECT id_supplier 
            FROM tbl_supplier 
            WHERE LOWER(name_supplier) = LOWER(:name)
            AND status = 'customer'
        ");

        $check->execute([
            ':name' => $name_customer
        ]);

        if ($check->fetch()) {
            throw new Exception('Customer sudah ada.');
        }

        // Mulai transaction
        $pdo->beginTransaction();

        // Insert customer
        $stmt = $pdo->prepare("
            INSERT INTO tbl_supplier (name_supplier, created_by, status)
            VALUES (:name, :created_by, 'customer')
        ");

        $stmt->execute([
            ':name' => $name_customer,
            ':created_by' => $_SESSION['username'] ?? 'system'
        ]);

        // Commit
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
