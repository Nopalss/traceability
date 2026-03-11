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
        $id_supplier   = intval($data['id_supplier'] ?? 0);
        $name_customer = trim(sanitize($data['name_Customer'] ?? ''));

        if ($id_supplier <= 0 || $name_customer === '') {
            throw new Exception('Data tidak lengkap.');
        }

        // Pastikan customer ada
        $checkExist = $pdo->prepare("
            SELECT id_supplier 
            FROM tbl_supplier
            WHERE id_supplier = :id
            AND status = 'customer'
        ");

        $checkExist->execute([
            ':id' => $id_supplier
        ]);

        if (!$checkExist->fetch()) {
            throw new Exception('Customer tidak ditemukan.');
        }

        // Cek duplicate customer
        $check = $pdo->prepare("
            SELECT id_supplier
            FROM tbl_supplier
            WHERE LOWER(name_supplier) = LOWER(:name)
            AND status = 'customer'
            AND id_supplier != :id
        ");

        $check->execute([
            ':name' => $name_customer,
            ':id'   => $id_supplier
        ]);

        if ($check->fetch()) {
            throw new Exception('Nama customer sudah digunakan.');
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE tbl_supplier
            SET name_supplier = :name
            WHERE id_supplier = :id
            AND status = 'customer'
        ");

        $stmt->execute([
            ':name' => $name_customer,
            ':id'   => $id_supplier
        ]);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = 'Customer berhasil diperbarui.';
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
