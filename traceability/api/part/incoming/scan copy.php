<?php
require_once __DIR__ . '/../../../includes/config.php';

header('Content-Type: application/json');

// ===============================
// AMBIL INPUT
// ===============================
$input = json_decode(file_get_contents("php://input"), true);
$raw = trim($input['qr_raw'] ?? '');

if ($raw === '') {
    echo json_encode([
        'success' => false,
        'message' => 'QR kosong'
    ]);
    exit;
}

// NORMALISASI RAW (hapus spasi & newline)
$raw = preg_replace("/\s+/", "", $raw);

// ===============================
// FUNGSI PARSING
// ===============================
function ambilSatu($str, $tag)
{
    preg_match("/$tag([^Z]+)/", $str, $m);
    return $m[1] ?? null;
}

function ambilSemua($str, $tag)
{
    preg_match_all("/$tag([^Z]+)/", $str, $m);
    return $m[1] ?? [];
}

// ===============================
// PARSING SESUAI RULE TERBARU
// ===============================
$part_code = ambilSatu($raw, 'Z1');                     // part code
$lot_no    = implode('', ambilSemua($raw, 'Z2'));       // lot no (bisa lebih dari 1)
$qty       = ambilSatu($raw, 'Z3');                     // qty
$remarks   = ambilSatu($raw, 'Z4');                     // remarks
$ref_no    = implode('', ambilSemua($raw, 'Z5'));       // ref number (bisa lebih dari 1)

$status = 'IN';

// ===============================
// VALIDASI DATA
// ===============================
if (!$part_code || !$lot_no || !$qty || !$ref_no) {
    echo json_encode([
        'success' => false,
        'message' => 'Format QR tidak valid'
    ]);
    exit;
}

if (!ctype_digit($qty)) {
    echo json_encode([
        'success' => false,
        'message' => 'Qty tidak valid'
    ]);
    exit;
}

$qty = (int)$qty;

// ===============================
// TRANSACTION DATABASE
// ===============================
try {
    $pdo->beginTransaction();

    // ===============================
    // 1️⃣ CEK PART (WAJIB ADA)
    // ===============================
    $stmt = $pdo->prepare(
        "SELECT id_part, qty 
         FROM tbl_part 
         WHERE part_code = ? 
         LIMIT 1"
    );
    $stmt->execute([$part_code]);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$part) {
        // ❌ PART TIDAK ADA → ERROR
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Part code tidak ditemukan di master part'
        ]);
        exit;
    }

    // ===============================
    // 2️⃣ UPDATE QTY MASTER PART
    // ===============================
    $newQty = $part['qty'] + $qty;

    $stmt = $pdo->prepare(
        "UPDATE tbl_part
         SET qty = ?, updated = NOW()
         WHERE id_part = ?"
    );
    $stmt->execute([$newQty, $part['id_part']]);

    // ===============================
    // 3️⃣ INSERT DETAIL PART
    // ===============================
    $stmt = $pdo->prepare(
        "INSERT INTO tbl_detail_part
        (ref_number, part_code, qty, status, lot_no, remarks)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $ref_no,
        $part_code,
        $qty,
        $status,
        $lot_no,
        $remarks
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Incoming part berhasil',
        'data' => [
            'part_code' => $part_code,
            'lot_no'    => $lot_no,
            'qty'       => $qty,
            'ref_no'    => $ref_no,
            'remarks'   => $remarks
        ]
    ]);
} catch (Exception $e) {
    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server',
        'error'   => $e->getMessage()
    ]);
}
