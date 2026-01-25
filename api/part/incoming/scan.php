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

// ===============================
// HELPER PARSE
// ===============================
function ambil($str, $a, $b = null)
{
    $p1 = strpos($str, $a);
    if ($p1 === false) return null;

    $p1 += strlen($a);
    $p2 = $b ? strpos($str, $b, $p1) : strlen($str);
    if ($p2 === false) $p2 = strlen($str);

    return trim(substr($str, $p1, $p2 - $p1));
}

// ===============================
// PARSE QR
// FORMAT:
// Z1PARTCODEZ2LOTZ3QTYZ4REMARKSZ5REFNO
// ===============================
$data = [
    'part_code' => ambil($raw, 'Z1', 'Z2'),
    'lot_no'    => ambil($raw, 'Z2', 'Z3'),
    'qty'       => ambil($raw, 'Z3', 'Z4'),
    'remarks'   => ambil($raw, 'Z4', 'Z5'),
    'ref_no'    => ambil($raw, 'Z5')
];

// validasi parsing
if (!$data['part_code']) {
    echo json_encode([
        'success' => false,
        'message' => 'Format QR tidak valid',
        'data' => $data
    ]);
    exit;
}

// ===============================
// CEK PART DI DATABASE
// ===============================
$stmt = $pdo->prepare("
    SELECT part_name, supplier 
    FROM tbl_part 
    WHERE part_code = ?
");
$stmt->execute([$data['part_code']]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    echo json_encode([
        'success' => false,
        'message' => 'Part tidak ditemukan di database',
        'data' => $data
    ]);
    exit;
}

// ===============================
// RESPONSE (TIDAK SIMPAN DATA)
// ===============================
echo json_encode([
    'success' => true,
    'message' => 'QR valid, part ditemukan',
    'data' => $data,
    'part' => $part
]);
