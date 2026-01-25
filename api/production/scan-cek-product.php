<?php
require_once __DIR__ . '/../../includes/config.php';
header('Content-Type: application/json');

// ===============================
// AMBIL INPUT
// ===============================
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
// NORMALISASI
// ===============================
$raw = strtoupper($raw);

// ===============================
// HELPER PARSE Z
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
// PARSE QR PRODUCT (Z1 SAJA)
// ===============================
$refProduct = ambil($raw, 'Z1');

if (!$refProduct) {
    echo json_encode([
        'success' => false,
        'message' => 'Format QR tidak valid (Z1 tidak ditemukan)'
    ]);
    exit;
}

try {
    // ===============================
    // 1️⃣ AMBIL SEMUA PRODUCTION (TRACEABILITY)
    // ===============================
    $stmt = $pdo->prepare("
        SELECT 
            dp.id,
            dp.ref_product,
            dp.product_code,
            dp.ref_number,
            dp.status,
            dp.production_at,
            p.product_name
        FROM tbl_detail_production dp
        JOIN tbl_product p 
            ON p.product_code = dp.product_code
        WHERE dp.ref_product = ?
        ORDER BY dp.id ASC
    ");
    $stmt->execute([$refProduct]);
    $productions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$productions) {
        echo json_encode([
            'success' => false,
            'message' => 'Product tidak ditemukan'
        ]);
        exit;
    }

    // ===============================
    // 2️⃣ LOOP TIAP PRODUCTION → AMBIL PART
    // ===============================
    $result = [];

    $stmtPart = $pdo->prepare("
        SELECT 
            d.ref_number,
            d.part_code,
            p.part_name,
            p.supplier,
            d.qty,
            d.lot_no,
            d.remarks,
            d.incoming_date
        FROM tbl_detail_part d
        JOIN tbl_part p 
            ON p.part_code = d.part_code
        WHERE d.ref_number = ?
        ORDER BY d.incoming_date DESC
    ");

    foreach ($productions as $prod) {
        $stmtPart->execute([$prod['ref_number']]);
        $parts = $stmtPart->fetchAll(PDO::FETCH_ASSOC);

        $result[] = [
            'production' => [
                'ref_product'   => $prod['ref_product'],
                'product_code'  => $prod['product_code'],
                'product_name'  => $prod['product_name'],
                'ref_number'    => $prod['ref_number'],
                'status'        => $prod['status'],
                'production_at' => $prod['production_at']
            ],
            'parts' => $parts
        ];
    }

    // ===============================
    // RESPONSE SUCCESS
    // ===============================
    echo json_encode([
        'success' => true,
        'message' => 'Traceability product berhasil',
        'data' => $result
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem'
        // 'error' => $e->getMessage() // aktifkan saat debug
    ]);
}
