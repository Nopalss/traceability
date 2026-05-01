<?php
require_once __DIR__ . '/../../../includes/config.php';

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

/*
=============================
 GET INPUT (SAFE)
=============================
*/
$rawBody = file_get_contents("php://input");
$input = json_decode($rawBody, true);

if (!is_array($input)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON'
    ]);
    exit;
}

$raw = trim($input['qr_raw'] ?? '');
$supplier = trim($input['supplier'] ?? '');

if ($raw === '') {
    echo json_encode([
        'success' => false,
        'message' => 'QR code kosong'
    ]);
    exit;
}

if ($supplier === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Supplier wajib diisi'
    ]);
    exit;
}

/*
=============================
 FAST PARSER (NO REGEX)
=============================
*/
function parseQR($raw)
{
    $result = [];
    $parts = explode('|', $raw);

    foreach ($parts as $p) {
        if (strlen($p) < 3) continue;

        $key = substr($p, 0, 2);
        $val = substr($p, 2);

        $result[$key] = trim($val);
    }

    return $result;
}

$z = parseQR($raw);

/*
=============================
 MAPPING
=============================
*/
$data = [
    'part_code' => $z['Z1'] ?? null,
    'lot_no'    => $z['Z2'] ?? '',
    'qty'       => isset($z['Z3']) ? (int)$z['Z3'] : 0,
    'remarks'   => $z['Z4'] ?? '',
    'ref_no'    => $z['Z5'] ?? null,
];

/*
=============================
 VALIDATION
=============================
*/
if (!$data['part_code']) {
    echo json_encode(['success' => false, 'message' => 'Z1 tidak ada']);
    exit;
}

if (!$data['lot_no']) {
    echo json_encode(['success' => false, 'message' => 'Lot number kosong']);
    exit;
}

if (!$data['ref_no']) {
    echo json_encode(['success' => false, 'message' => 'Ref number wajib ada']);
    exit;
}

if ($data['qty'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Qty tidak valid']);
    exit;
}

/*
=============================
 VALIDATE PART (SMART)
=============================
*/
try {

    // ambil semua part berdasarkan part_code + join supplier
    $stmt = $pdo->prepare("
        SELECT p.id_part, p.part_code, p.part_name, s.id_supplier, s.name_supplier
        FROM tbl_part p
        JOIN tbl_supplier s ON p.supplier = s.id_supplier
        WHERE p.part_code = ?
    ");
    $stmt->execute([$data['part_code']]);
    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // kalau part_code tidak ada sama sekali
    if (!$parts) {
        echo json_encode([
            'success' => false,
            'message' => 'Part code tidak ditemukan'
        ]);
        exit;
    }

    $matchedPart = null;
    $supplierNames = [];

    foreach ($parts as $p) {

        $supplierNames[] = $p['name_supplier'];

        if ($p['id_supplier'] == $supplier) {
            $matchedPart = $p;
        }
    }

    // kalau supplier tidak cocok
    if (!$matchedPart) {

        $supplierList = implode(', ', array_unique($supplierNames));

        echo json_encode([
            'success' => false,
            'message' => "Part code ini milik supplier: $supplierList"
        ]);
        exit;
    }

    $part = $matchedPart;
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
    exit;
}

/*
=============================
 PRE-CHECK DUPLICATE
=============================
*/
$check = $pdo->prepare("
    SELECT 1 FROM tbl_detail_part 
    WHERE ref_number = ? 
    LIMIT 1
");
$check->execute([$data['ref_no']]);

if ($check->fetchColumn()) {
    echo json_encode([
        'success' => false,
        'message' => 'QR sudah pernah di scan'
    ]);
    exit;
}

/*
=============================
 INSERT
=============================
*/
try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO tbl_detail_part
        (ref_number, part_code, qty, remain, incoming_date, status, lot_no, remarks, part_id)
        VALUES (?, ?, ?, ?, NOW(), 'IN', ?, ?, ?)
    ");

    $stmt->execute([
        $data['ref_no'],
        $data['part_code'],
        $data['qty'],
        $data['qty'],
        $data['lot_no'],
        $data['remarks'] ?: 'NORMAL_INCOMING',
        $part["id_part"]
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Incoming berhasil',
        'data'    => $data,
        'part'    => $part
    ]);
    exit;
} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Insert gagal',
        'error'   => $e->getMessage()
    ]);
    exit;
}
