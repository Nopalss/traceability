<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['parts'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$parts = $_POST['parts'];

/* =============================
   NORMALIZE (optional, masih dipakai buat clean name)
============================= */
function normalize($str)
{
    $str = strtoupper(trim($str));
    $str = preg_replace('/[^A-Z0-9]/', '', $str);
    return $str;
}

/* =============================
   PREPARE INSERT
============================= */
$insert = $pdo->prepare("
INSERT INTO tbl_part (part_code, part_name, supplier)
VALUES (?,?,?)
");

/* =============================
   LOAD DB (COMPOSITE KEY)
============================= */
$dbParts = $pdo->query("
    SELECT part_code, supplier 
    FROM tbl_part
")->fetchAll(PDO::FETCH_ASSOC);

$dbKeys = [];

foreach ($dbParts as $row) {
    $key = strtoupper(trim($row['part_code'])) . '|' . $row['supplier'];
    $dbKeys[$key] = true;
}

/* =============================
   LOAD SUPPLIER VALID
============================= */
$dbSuppliers = $pdo->query("
    SELECT id_supplier 
    FROM tbl_supplier 
    WHERE status='supplier'
")->fetchAll(PDO::FETCH_COLUMN);

$validSuppliers = array_flip($dbSuppliers);

/* =============================
   COUNTER
============================= */
$inserted = 0;
$rejected = 0;

$processedKeys = [];

try {

    $pdo->beginTransaction();

    foreach ($parts as $p) {

        $code = strtoupper(trim($p['part_code']));
        $name_original = trim($p['part_name']);
        $supplier = $p['supplier'];

        $key = $code . '|' . $supplier;

        // =============================
        // VALIDASI
        // =============================

        if ($code === '' || $name_original === '') {
            $rejected++;
            continue;
        }

        // hanya numeric
        if (!preg_match('/^[0-9]+$/', $code)) {
            $rejected++;
            continue;
        }

        // supplier valid
        if (!isset($validSuppliers[$supplier])) {
            $rejected++;
            continue;
        }

        // =============================
        // DUPLICATE CSV (by code + supplier)
        // =============================

        if (isset($processedKeys[$key])) {
            $rejected++;
            continue;
        }

        // =============================
        // DUPLICATE DB (by code + supplier)
        // =============================

        if (isset($dbKeys[$key])) {
            $rejected++;
            continue;
        }

        // =============================
        // INSERT
        // =============================

        $insert->execute([
            $code,
            strtoupper($name_original),
            $supplier
        ]);

        // update cache
        $processedKeys[$key] = true;
        $dbKeys[$key] = true;

        $inserted++;
    }

    $pdo->commit();
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}

/* =============================
   RESPONSE
============================= */
echo json_encode([
    "inserted" => $inserted,
    "rejected" => $rejected
]);
