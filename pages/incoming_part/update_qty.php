<?php
require_once __DIR__ . '/../../includes/config.php';

$ref_number = $_POST['ref_number'];
$qty        = $_POST['qty'];
$remain     = $_POST['remain'];

$q = $pdo->prepare("
    UPDATE tbl_detail_part 
    SET qty = ?, remain = ?
    WHERE ref_number = ?
");

$q->execute([$qty, $remain, $ref_number]);

echo json_encode(['status' => 'success']);
