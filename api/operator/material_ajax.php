<?php
require_once __DIR__ . '/../../includes/config.php';

$assy = $_GET['assy'] ?? '';

$q = $pdo->prepare("
SELECT pa.part_code,p.part_name,pa.qty
FROM tbl_part_assy pa
JOIN tbl_part p ON pa.part_code=p.part_code
WHERE pa.part_assy=?
");
$q->execute([$assy]);

echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
