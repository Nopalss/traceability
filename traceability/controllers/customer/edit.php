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

        // FIX field name
        $id_supplier   = intval($data['id_Customer'] ?? 0);
        $name_customer = trim(sanitize($data['name_Customer'] ?? ''));

        if ($id_supplier <= 0 || $name_customer === '') {
            throw new Exception('Data tidak lengkap.');
        }

        // VALIDASI
        if (!preg_match('/[a-zA-Z]/', $name_customer)) {
            throw new Exception('Nama customer tidak valid.');
        }

        $normalized_input = normalize($name_customer);

        /* =============================
           CEK EXIST
        ============================= */
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

        /* =============================
           LOAD DB (CONSISTENT)
        ============================= */
        $dbCustomers = $pdo->query("
            SELECT id_supplier, name_supplier 
            FROM tbl_supplier 
            WHERE status='customer'
        ")->fetchAll(PDO::FETCH_ASSOC);

        $dbNormalized = [];

        foreach ($dbCustomers as $row) {
            if ($row['id_supplier'] == $id_supplier) continue;

            $norm = normalize($row['name_supplier']);
            $dbNormalized[$norm] = true;
        }

        /* =============================
           CEK DUPLICATE (STRONG)
        ============================= */
        if (isset($dbNormalized[$normalized_input])) {
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
