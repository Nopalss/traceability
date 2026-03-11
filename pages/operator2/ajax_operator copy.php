<?php
require '../../includes/config.php';

$action = $_REQUEST['action'] ?? '';

/* =====================================================
   LOAD BOM
===================================================== */
if ($action == 'load_bom') {

    $assy = $_GET['assy'] ?? '';

    $q = $pdo->prepare("
        SELECT pa.part_code,
               p.part_name,
               pa.qty,
               am.lot_no,
               am.spq,
               am.remain
        FROM tbl_part_assy pa
        JOIN tbl_part p ON p.part_code = pa.part_code
        LEFT JOIN tbl_active_material am ON am.part_code = pa.part_code
        WHERE pa.part_assy = ?
    ");

    $q->execute([$assy]);

    foreach ($q as $r) {

        echo "<tr>
            <td>{$r['part_code']}</td>
            <td>{$r['part_name']}</td>
            <td>{$r['qty']}</td>
            <td>" . ($r['lot_no'] ?? '-') . "</td>
            <td>" . ($r['spq'] ?? '-') . "</td>
            <td>" . ($r['remain'] ?? '-') . "</td>
        </tr>";
    }

    exit;
}


/* =====================================================
   SCAN MATERIAL
===================================================== */
if ($action == 'scan_material') {



    $part  = $_POST['Z1'] ?? '';
    $lot   = $_POST['Z2'] ?? '';
    $qty   = $_POST['Z3'] ?? 0;
    $mode  = $_POST['mode'] ?? null;

    $assy = $_POST['assy'] ?? '';
    $remarks = $_POST['Z4'] ?? '';
    $ref = $_POST['Z5'] ?? '';

    if (!$assy) {
        echo json_encode(['error' => true, 'message' => 'ASSY kosong']);
        exit;
    }

    if (!$part || !$qty) {
        echo json_encode(['error' => true, 'message' => 'Data material tidak valid']);
        exit;
    }
    // CEK REF SUDAH PERNAH DIPAKAI
    $cekRef = $pdo->prepare("
    SELECT 1 FROM tbl_detail_part WHERE ref_number = ? LIMIT 1
");
    $cekRef->execute([$ref]);

    if (!$cekRef->fetch()) {
        echo json_encode([
            'error' => true,
            'message' => 'Ref material tidak terdaftar di incoming'
        ]);
        exit;
    }

    // VALIDASI KE tbl_detail_part
    $cekIncoming = $pdo->prepare("
    SELECT *
    FROM tbl_detail_part
    WHERE part_code = ?
      AND lot_no = ?
      AND qty = ?
      AND remarks = ?
      AND ref_number = ?
      AND status = 'IN'
");

    $cekIncoming->execute([$part, $lot, $qty, $remarks, $ref]);

    $incomingRow = $cekIncoming->fetch(PDO::FETCH_ASSOC);

    if (!$incomingRow) {
        echo json_encode([
            'error' => true,
            'message' => 'Material tidak terdaftar di Incoming'
        ]);
        exit;
    }
    $cekBom = $pdo->prepare("
    SELECT 1
    FROM tbl_part_assy
    WHERE part_assy = ?
      AND part_code = ?
");

    $cekBom->execute([$assy, $part]);

    if (!$cekBom->fetch()) {
        echo json_encode([
            'error' => true,
            'message' => 'Material bukan BOM ASSY ini'
        ]);
        exit;
    }

    // cek apakah sudah ada material aktif
    $exist = $pdo->prepare("SELECT * FROM tbl_active_material WHERE part_code = ?");
    $exist->execute([$part]);
    $existingRow = $exist->fetch(PDO::FETCH_ASSOC);

    // kalau sudah ada dan belum pilih mode
    if ($existingRow && !$mode) {
        echo json_encode(['needConfirm' => true]);
        exit;
    }
    if ($mode === 'add') {

        $pdo->prepare("
        UPDATE tbl_active_material
        SET
            lot_no = ?,
            spq    = ?,
            remain = remain + ?
        WHERE part_code = ?
    ")->execute([$lot, $qty, $qty, $part]);
    } else {

        if ($existingRow && $existingRow['remain'] > 0) {

            $pdo->prepare("
            INSERT INTO tbl_material_loss
            (part_code,lost_qty,old_remain,operator,reason,assy,ref_number)
            VALUES (?,?,?,?,?,?,?)
        ")->execute([
                $part,
                $existingRow['remain'],
                $existingRow['remain'],
                $_SESSION['username'] ?? 'operator',
                'Replace material',
                $assy,
                $ref
            ]);
        }

        // 🔥 REPLACE HARUS DI LUAR IF
        $pdo->prepare("
        REPLACE INTO tbl_active_material (part_code, lot_no, spq, remain)
        VALUES (?, ?, ?, ?)
    ")->execute([$part, $lot, $qty, $qty]);
        echo json_encode(['success' => true]);
        exit;
    }
}


/* =====================================================
   SCAN PRODUCT
===================================================== */
if ($action == 'scan_product') {

    $product  = $_POST['assy'] ?? '';
    $serial   = $_POST['Z2'] ?? '';
    $shift    = $_POST['shift'] ?? 0;
    $line     = $_POST['line'] ?? 0;
    $operator = $_POST['operator'] ?? '';
    $qty      = $_POST['Z3'] ?? 1;
    $remarks = $_POST['Z4'] ?? '';
    $ref = $_POST['Z5'] ?? '';

    $cekSerial = $pdo->prepare("
    SELECT 1 FROM tbl_detail_product WHERE serial_no = ?
");
    $cekSerial->execute([$serial]);

    if ($cekSerial->fetch()) {
        echo json_encode([
            'error' => true,
            'message' => 'Serial product sudah pernah discan'
        ]);
        exit;
    }

    $z1 = $_POST['Z1'] ?? '';

    if ($z1 !== $product) {
        echo json_encode([
            'error' => true,
            'message' => 'Product tidak sesuai ASSY'
        ]);
        exit;
    }

    if (!$product || !$serial) {
        echo json_encode(['error' => true, 'message' => 'Data product tidak valid']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        // INSERT OUTPUT
        $pdo->prepare("
            INSERT INTO tbl_production_output
            (product_code, serial_no, shift, line_id, operator, qty, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ")->execute([$product, $serial, $shift, $line, $operator, $qty]);

        // SIMPAN DETAIL PRODUCT
        $pdo->prepare("
    INSERT INTO tbl_detail_product
    (product_code, serial_no, qty, shift, line_id, operator, ref_number, remarks)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
")->execute([
            $product,
            $serial,
            $qty,
            $shift,
            $line,
            $operator,
            $ref,
            $remarks
        ]);

        // KURANGI MATERIAL SESUAI BOM
        $bom = $pdo->prepare("
            SELECT part_code, qty
            FROM tbl_part_assy
            WHERE part_assy = ?
        ");
        $bom->execute([$product]);

        foreach ($bom as $b) {

            $used = $b['qty'] * $qty;

            // CEK REMAIN DULU (ANTI MINUS)
            $cekRemain = $pdo->prepare("
        SELECT remain, lot_no
        FROM tbl_active_material
        WHERE part_code = ?
    ");
            $cekRemain->execute([$b['part_code']]);
            $r = $cekRemain->fetch(PDO::FETCH_ASSOC);

            if (!$r) {
                throw new Exception("Material {$b['part_code']} belum discan");
            }

            if ($r['remain'] < $used) {
                throw new Exception("Material {$b['part_code']} tidak cukup");
            }

            // SIMPAN RELASI PRODUCT → MATERIAL
            $pdo->prepare("
        INSERT INTO tbl_detail_production
        (product_code, serial_no, part_code, used_qty, lot_no)
        VALUES (?, ?, ?, ?, ?)
    ")->execute([
                $product,
                $serial,
                $b['part_code'],
                $used,
                $r['lot_no']
            ]);

            // POTONG MATERIAL
            $pdo->prepare("
        UPDATE tbl_active_material
        SET remain = remain - ?
        WHERE part_code = ?
    ")->execute([$used, $b['part_code']]);
        }


        $pdo->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {

        $pdo->rollBack();
        echo json_encode(['error' => true, 'message' => 'Gagal simpan produksi']);
    }

    exit;
}


/* =====================================================
   OUTPUT TABLE
===================================================== */
if ($action == 'output') {

    $line = $_SESSION['line_id'] ?? 7;

    $q = $pdo->prepare("
    SELECT *
    FROM tbl_production_output
    WHERE line_id = ?
    ORDER BY id DESC
    LIMIT 20
");

    $q->execute([$line]);

    foreach ($q as $r) {

        echo "<tr>
            <td>" . date('d/m/Y', strtotime($r['created_at'])) . "</td>
            <td>" . date('H:i:s', strtotime($r['created_at'])) . "</td>
            <td>{$r['shift']}</td>
            <td>{$r['line_id']}</td>
            <td>{$r['operator']}</td>
            <td>{$r['qty']}</td>
            <td>{$r['serial_no']}</td>
            <td></td>
        </tr>";
    }

    exit;
}


/* =====================================================
   PLANNING (TIDAK DIUBAH)
===================================================== */
if ($action == 'planning') {

    $today = $_GET['production_date'] ?? date('Y-m-d');
    $line  = $_GET['line'] ?? ($_SESSION['line_id'] ?? 7);
    $shift = $_GET['shift'];



    $q = $pdo->prepare("
    SELECT pp.product_code,
       dp.jam,
       dp.qty,
       dp.actual,
       s.start AS shift_start
FROM tbl_production_planning pp
JOIN tbl_detail_production_planning dp ON pp.pp_id = dp.pp_id
JOIN tbl_shift s ON s.shift = pp.shift
WHERE pp.production_date = ?
  AND pp.line_id = ?
  AND pp.shift = ?
ORDER BY dp.jam

");


    $q->execute([$today, $line, $shift]);

    $rows = $q->fetchAll(PDO::FETCH_ASSOC);


    if (!$rows) {
        echo json_encode([
            'jam' => [],
            'data' => []
        ]);
        exit;
    }


    $shiftStart = (int)$rows[0]['shift_start'];

    $jamList = [];


    foreach ($rows as $r) {
        if (!in_array($r['jam'], $jamList)) {
            $jamList[] = $r['jam'];
        }
    }
    /* ======================
   SORT JAM BY SHIFT START
====================== */
    usort($jamList, function ($a, $b) use ($shiftStart) {

        if ($a === 'OT') return 1;
        if ($b === 'OT') return -1;

        [$ha] = explode('-', $a);
        [$hb] = explode('-', $b);

        $ha = (int)$ha;
        $hb = (int)$hb;

        // convert ke timeline shift
        $ha = ($ha - $shiftStart + 24) % 24;
        $hb = ($hb - $shiftStart + 24) % 24;

        return $ha <=> $hb;
    });

    $data = [];

    foreach ($rows as $r) {
        $data[$r['product_code']][$r['jam']] = $r;
    }

    echo json_encode([
        'jam' => $jamList,
        'data' => $data
    ]);
    exit;
}

if ($action == 'load_assy') {

    $today = $_GET['production_date'] ?? date('Y-m-d');
    $line  = $_GET['line'] ?? ($_SESSION['line_id'] ?? 7);
    $shift = $_GET['shift'];

    $q = $pdo->prepare("
        SELECT DISTINCT product_code
        FROM tbl_production_planning
        WHERE production_date = ?
          AND line_id = ?
          AND shift = ?
    ");

    $q->execute([$today, $line, $shift]);

    echo json_encode($q->fetchAll(PDO::FETCH_COLUMN));
    exit;
}
