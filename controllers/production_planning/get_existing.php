<?php
require_once '../../includes/config.php';

$date = $_POST['date'];
$line = $_POST['line'];
$shift = $_POST['shift'];

$stmt = $pdo->prepare("
SELECT jam, qty 
FROM tbl_detail_production_planning d
JOIN tbl_production_planning p ON p.id = d.pp_id
WHERE p.production_date = ?
AND p.line_id = ?
AND p.shift = ?
");

$stmt->execute([$date, $line, $shift]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
