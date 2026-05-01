<?php
require_once __DIR__ . '/../../includes/config.php';

$product = $_POST['product'] ?? '';
$target  = (int)($_POST['target'] ?? 0);

$stmt = $pdo->prepare("
SELECT 
    pa.id_pa,              -- ✅ FIX WAJIB
    pa.part_code,
    pa.qty,
    pa.part_id,
    pa.remark,
    p.part_name,
    s.name_supplier,
    (
        SELECT COALESCE(SUM(remain),0)
        FROM tbl_detail_part 
        WHERE part_code = pa.part_code 
        AND part_id = pa.part_id
        AND status='IN'
    ) as stock
FROM tbl_part_assy pa
JOIN tbl_part p ON p.id_part = pa.part_id
LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier
WHERE pa.part_assy = ?
");

$stmt->execute([$product]);

$data = [];

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {

    $need = (int)$r['qty'] * $target;
    $stock = (int)$r['stock'];
    $shortage = $need - $stock;

    $data[] = [
        'id_pa'      => $r['id_pa'],      // ✅ sekarang aman
        'part_id'    => $r['part_id'],
        'part_code'  => $r['part_code'],
        'part_name'  => $r['part_name'],
        'supplier'   => $r['name_supplier'],
        'remark'     => $r['remark'],
        'stock'      => $stock,
        'need'       => $need,
        'shortage'   => $shortage > 0 ? $shortage : 0
    ];
}

echo json_encode($data);
