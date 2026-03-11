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
        $name_supplier = trim(sanitize($data['name_supplier'] ?? ''));

        if ($id_supplier <= 0 || $name_supplier === '') {
            throw new Exception('Data tidak lengkap.');
        }

        // Pastikan supplier ada
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

        // Cek duplicate (supplier saja)
        $check = $pdo->prepare("
            SELECT id_supplier
            FROM tbl_supplier
            WHERE LOWER(name_supplier) = LOWER(:name)
            AND status = 'supplier'
            AND id_supplier != :id
        ");

        $check->execute([
            ':name' => $name_supplier,
            ':id'   => $id_supplier
        ]);

        if ($check->fetch()) {
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
