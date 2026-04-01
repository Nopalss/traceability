<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

if (!isset($_POST['parts'])) {
    echo json_encode([
        "status" => "error"
    ]);
    exit;
}

$parts = $_POST['parts'];

/* =============================
   NORMALIZE FUNCTION (GLOBAL)
============================= */
function normalize($str)
{
    $str = trim($str);
    $str = strtoupper($str);

    // hapus simbol yang bikin beda format
    $str = str_replace([',', '.'], '', $str);

    // rapihin spasi
    $str = preg_replace('/\s+/', ' ', $str);

    return $str;
}

/* =============================
   PREPARE INSERT
============================= */
$insert = $pdo->prepare("
INSERT INTO tbl_part
(part_code, part_name, supplier)
VALUES (?,?,?)
");

/* =============================
   LOAD DATA DB (1x ONLY)
============================= */
$dbParts = $pdo->query("
    SELECT part_code, part_name 
    FROM tbl_part
")->fetchAll(PDO::FETCH_ASSOC);

/* normalize DB */
$dbCodes = [];
$dbNames = [];

foreach ($dbParts as $row) {
    $dbCodes[] = strtoupper(trim($row['part_code']));
    $dbNames[] = normalize($row['part_name']);
}

/* =============================
   COUNTER
============================= */
$inserted = 0;
$rejected = 0;

$processedCodes = [];
$processedNames = [];

/* =============================
   LOOP INPUT
============================= */
foreach ($parts as $p) {

    // =============================
    // NORMALIZE INPUT
    // =============================

    $code = strtoupper(trim($p['part_code']));
    $name_original = trim($p['part_name']);
    $name = normalize($name_original);

    $supplier = $p['supplier'];

    // =============================
    // VALIDASI KOSONG
    // =============================

    if ($code === '' || $name === '') {
        $rejected++;
        continue;
    }

    // =============================
    // DUPLICATE CSV (CODE)
    // =============================

    if (in_array($code, $processedCodes)) {
        $rejected++;
        continue;
    }

    // =============================
    // DUPLICATE CSV (NAME)
    // =============================

    if (in_array($name, $processedNames)) {
        $rejected++;
        continue;
    }

    // =============================
    // DUPLICATE DB (CODE)
    // =============================

    if (in_array($code, $dbCodes)) {
        $rejected++;
        continue;
    }

    // =============================
    // DUPLICATE DB (NAME)
    // =============================

    if (in_array($name, $dbNames)) {
        $rejected++;
        continue;
    }

    // =============================
    // INSERT
    // =============================

    $insert->execute([
        $code,
        $name_original, // simpan original biar rapih di UI
        $supplier
    ]);

    // update cache
    $processedCodes[] = $code;
    $processedNames[] = $name;

    $dbCodes[] = $code;
    $dbNames[] = $name;

    $inserted++;
}

/* =============================
   RESPONSE
============================= */
echo json_encode([
    "inserted" => $inserted,
    "rejected" => $rejected
]);
