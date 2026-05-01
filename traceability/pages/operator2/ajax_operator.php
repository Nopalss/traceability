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
    if (!$lot) $lot = '-';
    $qty   = $_POST['Z3'] ?? 0;
    $mode  = $_POST['mode'] ?? null;

    $assy    = $_POST['assy'] ?? '';
    $remarks = $_POST['Z4'] ?? '';
    $ref     = $_POST['Z5'] ?? '';

    if (!$assy) {
        echo json_encode(['error' => true, 'message' => 'ASSY kosong']);
        exit;
    }

    if (!$part || !$qty) {
        echo json_encode(['error' => true, 'message' => 'Data material tidak valid']);
        exit;
    }

    // REF HARUS ADA DI INCOMING
    $cekRef = $pdo->prepare("SELECT 1 FROM tbl_detail_part WHERE ref_number=? LIMIT 1");
    $cekRef->execute([$ref]);

    if (!$cekRef->fetch()) {
        echo json_encode(['error' => true, 'message' => 'Ref material tidak terdaftar di incoming']);
        exit;
    }

    // VALIDASI BOM
    $cekBom = $pdo->prepare("SELECT 1 FROM tbl_part_assy WHERE part_assy=? AND part_code=?");
    $cekBom->execute([$assy, $part]);

    if (!$cekBom->fetch()) {
        echo json_encode(['error' => true, 'message' => 'Material bukan BOM ASSY ini']);
        exit;
    }

    // EXISTING ACTIVE MATERIAL
    $exist = $pdo->prepare("SELECT * FROM tbl_active_material WHERE part_code=?");
    $exist->execute([$part]);
    $existingRow = $exist->fetch(PDO::FETCH_ASSOC);

    if ($existingRow && !$mode) {
        echo json_encode(['needConfirm' => true]);
        exit;
    }

    // ===== ADD MODE =====
    if ($mode === 'add') {

        $pdo->prepare("
   UPDATE tbl_active_material
SET remain=remain+?, ref_number=?
WHERE part_code=?
")->execute([$qty, $ref, $part]);


        echo json_encode(['success' => true]);
        exit;
    }

    // ===== REPLACE MODE =====

    if ($existingRow && $existingRow['remain'] > 0) {

        $pdo->prepare("
           INSERT INTO tbl_material_loss
(part_code,lost_qty,old_remain,operator,reason,assy,ref_number,shift,line_id)
VALUES (?,?,?,?,?,?,?,?,?)
        ")->execute([
            $part,
            $existingRow['remain'],
            $existingRow['remain'],
            $_SESSION['username'] ?? 'operator',
            'Replace material',
            $assy,
            $ref,
            $_POST['shift'] ?? 0,
            $_POST['line'] ?? 0
        ]);
    }

    // TANDAI INCOMING LAMA SEBAGAI REPLACED
    $pdo->prepare("
UPDATE tbl_detail_part
SET status='REPLACED'
WHERE part_code=? AND ref_number=?
")->execute([$part, $ref]);
    // REPLACE SELALU JALAN
    $pdo->prepare("
REPLACE INTO tbl_active_material 
(part_code,lot_no,spq,remain,ref_number)
VALUES (?,?,?,?,?)
")->execute([$part, $lot, $qty, $qty, $ref]);


    echo json_encode(['success' => true]);
    exit;
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
    $actual = 1;
    $remarks  = $_POST['Z4'] ?? '';
    $ref      = $_POST['Z5'] ?? '';
    $z1       = $_POST['Z1'] ?? '';

    if ($z1 !== $product) {
        echo json_encode(['error' => true, 'message' => 'Product tidak sesuai ASSY']);
        exit;
    }

    $cekSerial = $pdo->prepare("SELECT 1 FROM tbl_detail_product WHERE serial_no=?");
    $cekSerial->execute([$serial]);

    if ($cekSerial->fetch()) {
        echo json_encode(['error' => true, 'message' => 'Serial product sudah pernah discan']);
        exit;
    }

    // ============================
    // CEK APAKAH TARGET SUDAH TERCAPAI
    // ============================

    $planningDate = date('Y-m-d');
    if ($shift == 2 && date('H') < 7) {
        $planningDate = date('Y-m-d', strtotime('-1 day'));
    }


    $cekRemainPlanning = $pdo->prepare("
SELECT 
    SUM(dp.qty) AS total_plan,
    SUM(dp.actual) AS total_actual
FROM tbl_production_planning pp
JOIN tbl_detail_production_planning dp ON pp.pp_id = dp.pp_id
WHERE pp.product_code=? 
  AND pp.shift=? 
  AND pp.line_id=? 
  AND pp.production_date=?
");


    $cekRemainPlanning->execute([$product, $shift, $line, $planningDate]);
    $p = $cekRemainPlanning->fetch(PDO::FETCH_ASSOC);

    $totalPlan   = intval($p['total_plan'] ?? 0);
    $totalActual = intval($p['total_actual'] ?? 0);

    if ($totalActual >= $totalPlan) {
        echo json_encode([
            'error' => true,
            'message' => 'Produk ini sudah mencapai target planning'
        ]);
        exit;
    }


    try {

        $pdo->beginTransaction();

        $pdo->prepare("
            INSERT INTO tbl_production_output
            (product_code,serial_no,shift,line_id,operator,qty,created_at)
            VALUES (?,?,?,?,?,?,NOW())
        ")->execute([$product, $serial, $shift, $line, $operator, $qty]);

        $pdo->prepare("
            INSERT INTO tbl_detail_product
            (product_code,serial_no,qty,shift,line_id,operator,ref_number,remarks)
            VALUES (?,?,?,?,?,?,?,?)
        ")->execute([$product, $serial, $qty, $shift, $line, $operator, $ref, $remarks]);

        $bom = $pdo->prepare("SELECT part_code,qty FROM tbl_part_assy WHERE part_assy=?");
        $bom->execute([$product]);

        foreach ($bom as $b) {

            $used = $b['qty'] * $qty;

            $cekRemain = $pdo->prepare("SELECT remain,lot_no,ref_number 
FROM tbl_active_material 
WHERE part_code=?
");
            $cekRemain->execute([$b['part_code']]);
            $r = $cekRemain->fetch(PDO::FETCH_ASSOC);
            $refMaterial = $r['ref_number'];

            if (!$r || $r['remain'] < $used) throw new Exception();

            // TRACE PRODUKSI
            $pdo->prepare("
        INSERT INTO tbl_detail_production
        (product_code,serial_no,part_code,used_qty,lot_no)
        VALUES (?,?,?,?,?)
    ")->execute([$product, $serial, $b['part_code'], $used, $r['lot_no']]);

            // POTONG ACTIVE MATERIAL
            $pdo->prepare("
        UPDATE tbl_active_material 
        SET remain=remain-?
        WHERE part_code=?
    ")->execute([$used, $b['part_code']]);


            // ===============================
            // POTONG INCOMING PER REF
            // ===============================
            // cari ref material berdasarkan lot aktif
            $refMaterial = $r['ref_number'];

            if (!$refMaterial) {
                throw new Exception('Ref material tidak ditemukan di active material');
            }

            // potong remain incoming
            $pdo->prepare("
UPDATE tbl_detail_part
SET remain = remain - ?
WHERE ref_number=?
")->execute([$used, $refMaterial]);

            $pdo->prepare("
UPDATE tbl_detail_part
SET status='USED'
WHERE ref_number=? AND remain<=0
")->execute([$refMaterial]);
        }

        // ============================
        // UPDATE ACTUAL PLANNING FLEXIBLE (SKIP 0 + AUTO NEXT SLOT)
        // ============================

        // ambil planning date (support shift malam)
        $planningDate = date('Y-m-d');
        if ($shift == 2 && date('H') < 7) {
            $planningDate = date('Y-m-d', strtotime('-1 day'));
        }

        // ambil pp_id
        $pp = $pdo->prepare("
    SELECT pp_id
    FROM tbl_production_planning
    WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
    ORDER BY pp_id DESC LIMIT 1
");
        $pp->execute([$product, $shift, $line, $planningDate]);
        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            // ambil shift start dari tbl_shift
            $shiftStartStmt = $pdo->prepare("
    SELECT start 
    FROM tbl_shift 
    WHERE shift=?
    LIMIT 1
");
            $shiftStartStmt->execute([$shift]);
            $shiftStart = $shiftStartStmt->fetchColumn();

            if ($shiftStart === false) {
                throw new Exception('Shift tidak ditemukan');
            }

            // ambil slot planning
            $slots = $pdo->prepare("
    SELECT id, qty, actual, jam
    FROM tbl_detail_production_planning
    WHERE pp_id=?
    ORDER BY
    CASE
        WHEN jam='OT' THEN 99
        WHEN CAST(SUBSTRING(jam,1,2) AS UNSIGNED) < ?
            THEN CAST(SUBSTRING(jam,1,2) AS UNSIGNED) + 24
        ELSE CAST(SUBSTRING(jam,1,2) AS UNSIGNED)
    END
");

            $slots->execute([$ppRow['pp_id'], $shiftStart]);
            $allSlots = $slots->fetchAll(PDO::FETCH_ASSOC);


            foreach ($allSlots as $slot) {

                // skip kalau target 0
                if ($slot['qty'] <= 0) continue;

                // cari slot pertama yang belum penuh
                if ($slot['actual'] < $slot['qty']) {

                    $pdo->prepare("
                UPDATE tbl_detail_production_planning
                SET actual = actual + 1
                WHERE id=?
            ")->execute([$slot['id']]);

                    break; // stop setelah update 1 slot
                }
            }
        }



        // ============================
        // CEK APAKAH BARU SAJA SELESAI TARGET
        // ============================


        $cekFinish = $pdo->prepare("
SELECT 
    SUM(dp.qty) AS total_plan,
    SUM(dp.actual) AS total_actual
FROM tbl_production_planning pp
JOIN tbl_detail_production_planning dp ON pp.pp_id = dp.pp_id
WHERE pp.product_code=? 
  AND pp.shift=? 
  AND pp.line_id=? 
  AND pp.production_date=?
");


        $cekFinish->execute([$product, $shift, $line, $planningDate]);
        $f = $cekFinish->fetch(PDO::FETCH_ASSOC);

        $isFinished = false;

        if ($f && intval($f['total_actual']) >= intval($f['total_plan'])) {
            $isFinished = true;
        }

        $pdo->commit();
        echo json_encode([
            'success' => true,
            'finished' => $isFinished
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        // echo json_encode(['error' => true, 'message' => 'Gagal simpan produksi']);
        echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    }
    exit;
}


/* =====================================================
   OUTPUT
===================================================== */
if ($action == 'output') {

    $line = $_SESSION['line_id'] ?? 7;

    $q = $pdo->prepare("
        SELECT * FROM tbl_production_output
        WHERE line_id=?
        ORDER BY id DESC LIMIT 20
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
   PLANNING
===================================================== */
if ($action == 'planning') {

    $today = $_GET['production_date'];
    $line = $_GET['line'];
    $shift = $_GET['shift'];

    $q = $pdo->prepare("
        SELECT pp.product_code,dp.jam,dp.qty,dp.actual,s.start shift_start
        FROM tbl_production_planning pp
        JOIN tbl_detail_production_planning dp ON pp.pp_id=dp.pp_id
        JOIN tbl_shift s ON s.shift=pp.shift
        WHERE pp.production_date=? AND pp.line_id=? AND pp.shift=?
     ORDER BY
CASE
    WHEN dp.jam='OT' THEN 99
    WHEN CAST(SUBSTRING(dp.jam,1,2) AS UNSIGNED) < s.start
        THEN CAST(SUBSTRING(dp.jam,1,2) AS UNSIGNED) + 24
    ELSE CAST(SUBSTRING(dp.jam,1,2) AS UNSIGNED)
END

    ");
    $q->execute([$today, $line, $shift]);

    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo json_encode(['jam' => [], 'data' => []]);
        exit;
    }

    $jam = [];
    foreach ($rows as $r) {
        if (!in_array($r['jam'], $jam)) $jam[] = $r['jam'];
    }

    $data = [];
    foreach ($rows as $r) {
        $data[$r['product_code']][$r['jam']] = $r;
    }

    echo json_encode(['jam' => $jam, 'data' => $data]);
    exit;
}


/* =====================================================
   LOAD ASSY
===================================================== */
if ($action == 'load_assy') {

    $today = $_GET['production_date'];
    $line = $_GET['line'];
    $shift = $_GET['shift'];

    $q = $pdo->prepare("
        SELECT DISTINCT product_code
        FROM tbl_production_planning
        WHERE production_date=? AND line_id=? AND shift=?
    ");
    $q->execute([$today, $line, $shift]);

    echo json_encode($q->fetchAll(PDO::FETCH_COLUMN));
    exit;
}
