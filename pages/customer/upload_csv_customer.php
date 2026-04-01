<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['customers'])) {

    echo json_encode([
        "status" => "error",
        "message" => "Tidak ada data customer yang dikirim"
    ]);
    exit;
}

$customers = $_POST['customers'];

$created_by = $_SESSION['username'] ?? 'system';

/* =============================
   NORMALIZE FUNCTION
============================= */
function normalize($str)
{
    $str = trim($str);
    $str = strtolower($str);

    // rapihin spasi
    $str = preg_replace('/\s+/', ' ', $str);

    // hapus titik & koma
    $str = str_replace(['.', ','], '', $str);

    return $str;
}

/* =============================
   PREPARE INSERT
============================= */
$insertStmt = $pdo->prepare("
    INSERT INTO tbl_supplier
    (name_supplier, status, created_at, created_by)
    VALUES (?, 'customer', NOW(), ?)
");

/* =============================
   LOAD CUSTOMER DARI DB (1x)
============================= */
$dbCustomers = $pdo->query("
    SELECT name_supplier 
    FROM tbl_supplier 
    WHERE status='customer'
")->fetchAll(PDO::FETCH_COLUMN);

/* normalize DB */
$dbNormalized = array_map(function ($c) {
    return normalize($c);
}, $dbCustomers);

/* =============================
   COUNTER
============================= */
$inserted = 0;
$rejected = 0;
$duplicates = [];

$processed = []; // untuk detect duplicate di CSV

/* =============================
   LOOP
============================= */
foreach ($customers as $name) {

    $name_original = trim($name);

    if ($name_original === '') continue;

    $name_normalized = normalize($name_original);

    /* =============================
       DUPLICATE CSV
    ============================= */
    if (in_array($name_normalized, $processed)) {

        $rejected++;
        $duplicates[] = $name_original;
        continue;
    }

    /* =============================
       DUPLICATE DB
    ============================= */
    if (in_array($name_normalized, $dbNormalized)) {

        $rejected++;
        $duplicates[] = $name_original;
        continue;
    }

    /* =============================
       INSERT
    ============================= */
    $insertStmt->execute([
        $name_original,
        $created_by
    ]);

    $inserted++;

    // update cache
    $processed[] = $name_normalized;
    $dbNormalized[] = $name_normalized;
}

/* =============================
   RESPONSE
============================= */
echo json_encode([
    "status" => "success",
    "inserted" => $inserted,
    "rejected" => $rejected,
    "duplicates" => $duplicates
]);
