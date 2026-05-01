<?php
require_once __DIR__ . '/../../includes/config.php';

$data = $_POST['data'] ?? [];

if (!$data) {
    echo json_encode(['status' => false]);
    exit;
}

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        UPDATE tbl_detail_production_planning
        SET qty = :qty,
            actual = :actual
        WHERE id = :id
    ");

    foreach ($data as $id => $val) {
        $stmt->execute([
            ':id' => $id,
            ':qty' => (int)$val['qty'],
            ':actual' => (int)$val['actual']
        ]);
    }

    $pdo->commit();

    echo json_encode(['status' => true]);
} catch (Exception $e) {

    $pdo->rollBack();
    echo json_encode(['status' => false]);
}
