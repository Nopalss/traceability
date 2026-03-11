<?php
require_once __DIR__ . '/../../../includes/config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$raw = trim($input['qr_raw'] ?? '');

if ($raw === '') {
    echo json_encode([
        'success' => false,
        'message' => 'QR code kosong'
    ]);
    exit;
}

/*
=============================
 FLEXIBLE QR PARSER Z1–Z7
=============================
*/
function parseQR($raw)
{
    $result = [];

    $parts = explode('|', $raw);

    foreach ($parts as $p) {
        $p = trim($p);
        if (preg_match('/^(Z[1-7])(.*)$/', $p, $m)) {
            $result[$m[1]] = trim($m[2]);
        }
    }

    return $result;
}


$z = parseQR($raw);

$data = [
    'part_code' => $z['Z1'] ?? null,
    'lot_no'    => $z['Z2'] ?? '',
    'qty'       => isset($z['Z3']) ? intval($z['Z3']) : 1,
    'remarks'   => $z['Z4'] ?? '',
    'ref_no'    => $z['Z5'] ?? ('REF-' . time()),
];

if (!$data['part_code']) {
    echo json_encode([
        'success' => false,
        'message' => 'Z1 (Part code) tidak ditemukan'
    ]);
    exit;
}

/*
=============================
 VALIDATE PART
=============================
*/
$stmt = $pdo->prepare("
    SELECT part_name
    FROM tbl_part
    WHERE part_code = ?
");
$stmt->execute([$data['part_code']]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    echo json_encode([
        'success' => false,
        'message' => 'Part tidak ditemukan di database'
    ]);
    exit;
}

/*
=============================
 INSERT INCOMING
=============================
*/
try {

    $stmt = $pdo->prepare("
        INSERT INTO tbl_detail_part
        (ref_number, part_code, qty, remain, incoming_date, status, lot_no, remarks)
        VALUES (?, ?, ?, ?, NOW(), 'IN', ?, ?)
    ");

    $stmt->execute([
        $data['ref_no'],
        $data['part_code'],
        $data['qty'],
        $data['qty'], // remain = qty
        $data['lot_no'],
        $data['remarks'] ?: 'NORMAL_INCOMING'
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Incoming berhasil disimpan',
        'data'    => $data,   // ⬅️ PENTING
        'part'    => $part
    ]);
} catch (PDOException $e) {

    if ($e->errorInfo[1] == 1062) {
        echo json_encode([
            'success' => false,
            'message' => 'Data duplikat (ref_number sudah ada)'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal insert incoming',
            'error'   => $e->getMessage()
        ]);
    }
}
