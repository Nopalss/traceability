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
// ===============================
$data = [
    'part_code' => ambil($raw, 'Z1', 'Z2'),
    'lot_no'    => ambil($raw, 'Z2', 'Z3'),
    'qty'       => (int) ambil($raw, 'Z3', 'Z4'),
    'remarks'   => ambil($raw, 'Z4', 'Z5'),
    'ref_no'    => ambil($raw, 'Z5')
];

// ===============================
// VALIDASI DASAR
// ===============================
if (
    !$data['part_code'] ||
    !$data['lot_no'] ||
    !$data['qty'] ||
    !$data['ref_no']
) {
    echo json_encode([
        'success' => false,
        'message' => 'Data QR tidak lengkap'
    ]);
    exit;
}

try {
    // ===============================
    // TRANSAKSI
    // ===============================
    $pdo->beginTransaction();

    // ===============================
    // 1️⃣ CEK PART
    // ===============================
    $stmt = $pdo->prepare("
        SELECT id_part, qty
        FROM tbl_part
        WHERE part_code = ?
        FOR UPDATE
    ");
    $stmt->execute([$data['part_code']]);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$part) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Part tidak ditemukan'
        ]);
        exit;
    }

    // ===============================
    // 2️⃣ CEK DUPLIKAT REF NUMBER
    // ===============================
    $stmt = $pdo->prepare("
        SELECT 1
        FROM tbl_detail_part
        WHERE ref_number = ?
        LIMIT 1
    ");
    $stmt->execute([$data['ref_no']]);

    if ($stmt->fetch()) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Data incoming dengan Ref No ini sudah pernah ditambahkan'
        ]);
        exit;
    }

    // ===============================
    // 3️⃣ INSERT DETAIL INCOMING
    // ===============================
    $stmt = $pdo->prepare("
        INSERT INTO tbl_detail_part
        (ref_number, part_code, lot_no, qty, remarks, status, incoming_date)
        VALUES
        (?, ?, ?, ?, ?, 'IN', NOW())
    ");
    $stmt->execute([
        $data['ref_no'],
        $data['part_code'],
        $data['lot_no'],
        $data['qty'],
        $data['remarks']
    ]);

    // ===============================
    // 4️⃣ UPDATE STOK PART
    // ===============================
    $stmt = $pdo->prepare("
        UPDATE tbl_part
        SET qty = qty + ?, updated = NOW()
        WHERE part_code = ?
    ");
    $stmt->execute([
        $data['qty'],
        $data['part_code']
    ]);

    // ===============================
    // COMMIT
    // ===============================
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Incoming part berhasil disimpan'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem, silakan hubungi admin'
    ]);
}
