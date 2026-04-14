<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['suppliers'])) {

    echo json_encode([
        "status" => "error",
        "message" => "Tidak ada data supplier yang dikirim"
    ]);
    exit;
}

$suppliers = $_POST['suppliers'];

$created_by = $_SESSION['username'] ?? 'system';

/* =============================
   NORMALIZE FUNCTION
============================= */
function normalize($str)
{
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9]/', '', $str);
    return $str;
}
/* =============================
   PREPARE QUERY
============================= */

$insertStmt = $pdo->prepare("
    INSERT INTO tbl_supplier
    (name_supplier, status, created_at, created_by)
    VALUES (?, 'supplier', NOW(), ?)
");

/* 🔥 ambil semua supplier sekali (biar hemat query) */
$dbSuppliers = $pdo->query("
    SELECT name_supplier 
    FROM tbl_supplier 
    WHERE status='supplier'
")->fetchAll(PDO::FETCH_COLUMN);

/* normalize semua supplier DB */
$dbNormalized = array_map(function ($s) {
    return normalize($s);
}, $dbSuppliers);

/*
COUNTER
*/
$inserted = 0;
$rejected = 0;
$duplicateList = [];

/*
LOOP DATA
*/
foreach ($suppliers as $name) {

    $name_original = trim($name);

    if ($name_original == '') continue;

    $name_normalized = normalize($name_original);

    /*
    CEK DUPLICATE (NORMALIZED)
    */
    if (in_array($name_normalized, $dbNormalized)) {

        $rejected++;
        $duplicateList[] = $name_original;
        continue;
    }

    /*
    INSERT
    */
    $insertStmt->execute([
        $name_original,
        $created_by
    ]);

    $inserted++;

    // update cache supaya CSV duplicate juga ketangkep
    $dbNormalized[] = $name_normalized;
}

/*
RESPONSE
*/
echo json_encode([
    "status" => "success",
    "inserted" => $inserted,
    "rejected" => $rejected,
    "duplicates" => $duplicateList
]);
