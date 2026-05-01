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
   NORMALIZE (STRONG - SAMA DENGAN SUPPLIER)
============================= */
function normalize($str)
{
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9]/', '', $str);
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
   LOAD DB (1x)
============================= */
$dbCustomers = $pdo->query("
    SELECT name_supplier 
    FROM tbl_supplier 
    WHERE status='customer'
")->fetchAll(PDO::FETCH_COLUMN);

/* normalize + jadikan hash */
$dbNormalized = [];
foreach ($dbCustomers as $c) {
    $dbNormalized[normalize($c)] = true;
}

/* =============================
   COUNTER
============================= */
$inserted = 0;
$rejected = 0;
$duplicates = [];

/* hash untuk CSV duplicate */
$processed = [];

/* =============================
   LOOP
============================= */
foreach ($customers as $name) {

    $name_original = trim($name);

    if ($name_original === '') continue;

    // VALIDASI: minimal harus ada huruf
    if (!preg_match('/[a-zA-Z]/', $name_original)) {
        $rejected++;
        $duplicates[] = $name_original;
        continue;
    }

    $name_normalized = normalize($name_original);

    /* =============================
       DUPLICATE CSV
    ============================= */
    if (isset($processed[$name_normalized])) {
        $rejected++;
        $duplicates[] = $name_original;
        continue;
    }

    /* =============================
       DUPLICATE DB
    ============================= */
    if (isset($dbNormalized[$name_normalized])) {
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
    $processed[$name_normalized] = true;
    $dbNormalized[$name_normalized] = true;
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
