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

    $qty   = intval($_POST['Z3'] ?? 0);
    $mode  = $_POST['mode'] ?? null;

    $assy  = $_POST['assy'] ?? '';
    $ref   = $_POST['Z5'] ?? '';

    $shift = $_POST['shift'] ?? 0;
    $line  = $_POST['line'] ?? 0;

    $operator = $_SESSION['username'] ?? 'operator';

    if (!$assy) {
        echo json_encode(['error' => true, 'message' => 'ASSY kosong']);
        exit;
    }

    if (!$part || $qty <= 0) {
        echo json_encode(['error' => true, 'message' => 'Data material tidak valid']);
        exit;
    }

    /* =========================
       VALIDASI REF INCOMING
    ========================== */

    $cekRef = $pdo->prepare("
        SELECT remain
        FROM tbl_detail_part
        WHERE ref_number=?
        LIMIT 1
    ");
    $cekRef->execute([$ref]);
    $refRow = $cekRef->fetch(PDO::FETCH_ASSOC);

    if (!$refRow) {
        echo json_encode(['error' => true, 'message' => 'Ref material tidak terdaftar']);
        exit;
    }

    $remainIncoming = intval($refRow['remain']);

    /* =========================
       VALIDASI BOM
    ========================== */

    $cekBom = $pdo->prepare("
        SELECT 1
        FROM tbl_part_assy
        WHERE part_assy=? AND part_code=?
    ");
    $cekBom->execute([$assy, $part]);

    if (!$cekBom->fetch()) {
        echo json_encode(['error' => true, 'message' => 'Material bukan BOM ASSY ini']);
        exit;
    }

    /* =========================
       CEK LOT SAMA DI LINE
    ========================== */

    $cekLot = $pdo->prepare("
        SELECT id
        FROM tbl_active_material
        WHERE part_code=? AND lot_no=? AND line_id=?
        LIMIT 1
    ");
    $cekLot->execute([$part, $lot, $line]);
    $lotRow = $cekLot->fetch(PDO::FETCH_ASSOC);
    if ($lotRow && !$mode) {
        echo json_encode(['error' => true, 'message' => 'Material Sudah di Scan']);
        exit;
    }


    /* =========================
       CEK MATERIAL AKTIF DI LINE
    ========================== */

    $cekExist = $pdo->prepare("
      SELECT lot_no
FROM tbl_active_material
WHERE part_code=? AND line_id=?
LIMIT 1
    ");
    $cekExist->execute([$part, $line]);
    $existRow = $cekExist->fetch(PDO::FETCH_ASSOC);


    /* =========================
       FIRST SCAN
    ========================== */

    if (!$existRow && !$mode) {

        $pdo->prepare("
            INSERT INTO tbl_active_material
            (part_code,lot_no,spq,remain,ref_number,line_id)
            VALUES (?,?,?,?,?,?)
        ")->execute([
            $part,
            $lot,
            $qty,
            $remainIncoming,
            $ref,
            $line
        ]);

        echo json_encode(['success' => true]);
        exit;
    }

    /* =========================
       LOT BERBEDA → POPUP
    ========================== */

    if ($existRow && $existRow['lot_no'] != $lot && !$mode) {

        echo json_encode(['needConfirm' => true]);
        exit;
    }


    /* =========================
       ADD MATERIAL
    ========================== */

    if ($mode === 'add') {

        $pdo->prepare("
                INSERT INTO tbl_active_material
                (part_code,lot_no,spq,remain,ref_number,line_id)
                VALUES (?,?,?,?,?,?)
            ")->execute([
            $part,
            $lot,
            $qty,
            $remainIncoming,
            $ref,
            $line
        ]);


        echo json_encode(['success' => true]);
        exit;
    }


    /* =========================
       REPLACE MATERIAL
    ========================== */

    if ($mode === 'remove') {

        $pdo->beginTransaction();

        try {

            $old = $pdo->prepare("
                SELECT *
                FROM tbl_active_material
                WHERE part_code=? AND line_id=?
            ");
            $old->execute([$part, $line]);

            foreach ($old as $o) {

                if ($o['remain'] > 0) {

                    $pdo->prepare("
                        INSERT INTO tbl_material_loss
                        (part_code,lost_qty,old_remain,operator,reason,assy,ref_number,shift,line_id)
                        VALUES (?,?,?,?,?,?,?,?,?)
                    ")->execute([
                        $part,
                        $o['remain'],
                        $o['remain'],
                        $operator,
                        'Replace material',
                        $assy,
                        $o['ref_number'],
                        $shift,
                        $line
                    ]);
                }
            }

            $pdo->prepare("
                DELETE FROM tbl_active_material
                WHERE part_code=? AND line_id=?
            ")->execute([$part, $line]);


            $pdo->prepare("
                INSERT INTO tbl_active_material
                (part_code,lot_no,spq,remain,ref_number,line_id)
                VALUES (?,?,?,?,?,?)
            ")->execute([
                $part,
                $lot,
                $qty,
                $remainIncoming,
                $ref,
                $line
            ]);

            $pdo->commit();

            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {

            $pdo->rollBack();

            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}


/* =====================================================
   SCAN PRODUCT (FIFO + UNIT TRACE)
===================================================== */
if ($action == 'scan_product') {

    $product  = $_POST['assy'] ?? '';
    $serial   = $_POST['Z2'] ?? '';
    $shift    = $_POST['shift'] ?? 0;
    $line     = $_POST['line'] ?? 0;
    $operator = $_POST['operator'] ?? '';
    $qty      = intval($_POST['Z3'] ?? 1);
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
        echo json_encode(['error' => true, 'message' => 'Lot Product sudah pernah discan']);
        exit;
    }

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

        /* =========================
           INSERT OUTPUT
        ========================== */

        $pdo->prepare("
            INSERT INTO tbl_production_output
            (product_code,serial_no,shift,line_id,operator,qty,created_at)
            VALUES (?,?,?,?,?,?,NOW())
        ")->execute([$product, $serial, $shift, $line, $operator, $qty]);


        /* =========================
           INSERT BOX
        ========================== */

        $pdo->prepare("
            INSERT INTO tbl_detail_product
            (product_code,serial_no,qty,shift,line_id,operator,ref_number,remarks)
            VALUES (?,?,?,?,?,?,?,?)
        ")->execute([$product, $serial, $qty, $shift, $line, $operator, $ref, $remarks]);


        /* =========================
           GENERATE UNIT
        ========================== */

        $unitIds = [];

        for ($i = 1; $i <= $qty; $i++) {

            $pdo->prepare("
                INSERT INTO tbl_product_unit
                (serial_no,product_code,unit_no)
                VALUES (?,?,?)
            ")->execute([$serial, $product, $i]);

            $unitIds[$i] = $pdo->lastInsertId();
        }


        /* =========================
           AMBIL BOM
        ========================== */

        $bom = $pdo->prepare("
            SELECT part_code,qty 
            FROM tbl_part_assy
            WHERE part_assy=?
        ");

        $bom->execute([$product]);


        foreach ($bom as $b) {

            $part = $b['part_code'];
            $perUnit = intval($b['qty']);

            $totalNeed = $perUnit * $qty;

            /* =========================
               FIFO ACTIVE MATERIAL
            ========================== */

            $lots = $pdo->prepare("
                SELECT *
                FROM tbl_active_material
                WHERE part_code=? AND remain>0
                ORDER BY id ASC
            ");

            $lots->execute([$part]);

            $lotRows = $lots->fetchAll(PDO::FETCH_ASSOC);

            if (!$lotRows) {
                throw new Exception("Material $part tidak tersedia");
            }

            $need = $totalNeed;

            $lotUsage = [];

            foreach ($lotRows as $lot) {

                if ($need <= 0) break;

                $take = min($lot['remain'], $need);

                $lotUsage[] = [
                    'lot_no' => $lot['lot_no'],
                    'ref' => $lot['ref_number'],
                    'qty' => $take,
                    'id' => $lot['id']
                ];

                $need -= $take;
            }

            if ($need > 0) {
                throw new Exception("Material $part tidak cukup");
            }


            /* =========================
               TRACE PER UNIT
            ========================== */

            $lotIndex = 0;
            $lotRemain = $lotUsage[0]['qty'];

            foreach ($unitIds as $unitNo => $unitId) {

                $needUnit = $perUnit;

                while ($needUnit > 0) {

                    if ($lotRemain == 0) {

                        $lotIndex++;
                        $lotRemain = $lotUsage[$lotIndex]['qty'];
                    }

                    $take = min($lotRemain, $needUnit);

                    $pdo->prepare("
                        INSERT INTO tbl_unit_material
                        (unit_id,part_code,lot_no,used_qty)
                        VALUES (?,?,?,?)
                    ")->execute([
                        $unitId,
                        $part,
                        $lotUsage[$lotIndex]['lot_no'],
                        $take
                    ]);

                    $lotRemain -= $take;
                    $needUnit -= $take;
                }
            }


            /* =========================
               INSERT SUMMARY TRACE
            ========================== */

            foreach ($lotUsage as $lu) {

                $pdo->prepare("
                    INSERT INTO tbl_detail_production
                    (product_code,serial_no,part_code,used_qty,lot_no)
                    VALUES (?,?,?,?,?)
                ")->execute([
                    $product,
                    $serial,
                    $part,
                    $lu['qty'],
                    $lu['lot_no']
                ]);


                /* =========================
                   UPDATE ACTIVE MATERIAL
                ========================== */

                $pdo->prepare("
                    UPDATE tbl_active_material
                    SET remain = remain - ?
                    WHERE id=?
                ")->execute([$lu['qty'], $lu['id']]);


                /* =========================
                   UPDATE INCOMING
                ========================== */

                $pdo->prepare("
                    UPDATE tbl_detail_part
                    SET remain = remain - ?
                    WHERE ref_number=?
                ")->execute([$lu['qty'], $lu['ref']]);


                $pdo->prepare("
                    UPDATE tbl_detail_part
                    SET status='USED'
                    WHERE ref_number=? AND remain<=0
                ")->execute([$lu['ref']]);
            }


            /* =========================
               HAPUS LOT HABIS
            ========================== */

            $pdo->prepare("
                DELETE FROM tbl_active_material
                WHERE remain<=0
            ")->execute();
        }


        /* =========================
           UPDATE PLANNING
        ========================== */

        $pp = $pdo->prepare("
            SELECT pp_id
            FROM tbl_production_planning
            WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
            LIMIT 1
        ");

        $pp->execute([$product, $shift, $line, $planningDate]);

        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            $shiftStartStmt = $pdo->prepare("
    SELECT start 
    FROM tbl_shift 
    WHERE shift=? 
    LIMIT 1
");
            $shiftStartStmt->execute([$shift]);
            $shiftStart = $shiftStartStmt->fetchColumn();

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

            foreach ($slots as $slot) {

                if ($slot['qty'] <= 0) continue;

                if ($slot['actual'] < $slot['qty']) {

                    $pdo->prepare("
            UPDATE tbl_detail_production_planning
            SET actual = actual + 1
            WHERE id=?
        ")->execute([$slot['id']]);

                    break;
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

        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
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


/* =====================================================
   EXIT MECA
===================================================== */
if ($action == 'exit_meca') {

    $serial   = $_POST['serial'] ?? '';
    $line     = $_POST['line'] ?? 0;
    $shift    = $_POST['shift'] ?? 0;
    $parts    = $_POST['parts'] ?? [];
    $operator = $_SESSION['username'] ?? 'operator';

    if (!$serial) {
        echo json_encode(['error' => true, 'message' => 'Serial kosong']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        /* =========================
           CEK PRODUK
        ========================== */

        $cek = $pdo->prepare("
            SELECT *
            FROM tbl_detail_product
            WHERE serial_no=?
            LIMIT 1
        ");
        $cek->execute([$serial]);
        $product = $cek->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Serial tidak ditemukan");
        }

        if ($product['is_ng']) {
            throw new Exception("Produk sudah pernah EXIT MECA");
        }

        $productCode = $product['product_code'];

        /* =========================
           UPDATE STATUS NG
        ========================== */

        $pdo->prepare("
            UPDATE tbl_detail_product
            SET is_ng=1
            WHERE serial_no=?
        ")->execute([$serial]);


        /* =========================
           INSERT NG PRODUCT
        ========================== */

        $pdo->prepare("
            INSERT INTO tbl_ng_product
            (serial_no,product_code,line_id,shift,operator,status)
            VALUES (?,?,?,?,?,'EXIT_MECA')
        ")->execute([
            $serial,
            $productCode,
            $line,
            $shift,
            $operator
        ]);

        $ngId = $pdo->lastInsertId();


        /* =========================
           PROCESS PART
        ========================== */

        foreach ($parts as $p) {

            $part_code = $p['part_code'];
            $lot_no    = $p['lot_no'];
            $used_qty  = $p['used_qty'];
            $ng_qty    = $p['ng_qty'];
            $checked   = $p['checked'];

            /* =========================
               INSERT NG PART
            ========================== */

            $pdo->prepare("
                INSERT INTO tbl_ng_part
                (ng_id,part_code,lot_no,used_qty,ng_qty)
                VALUES (?,?,?,?,?)
            ")->execute([
                $ngId,
                $part_code,
                $lot_no,
                $used_qty,
                $ng_qty
            ]);


            /* =========================
               KEMBALIKAN MATERIAL GOOD
            ========================== */

            if (!$checked) {

                $returnQty = $used_qty;

                $pdo->prepare("
                    UPDATE tbl_active_material
                    SET remain = remain + ?
                    WHERE part_code=? AND lot_no=?
                ")->execute([
                    $returnQty,
                    $part_code,
                    $lot_no
                ]);
            }

            /* =========================
               UPDATE TRACE PRODUKSI
            ========================== */

            $pdo->prepare("
                UPDATE tbl_detail_production
                SET exit_meca=1
                WHERE serial_no=? AND part_code=?
            ")->execute([
                $serial,
                $part_code
            ]);
        }


        /* =========================
           KOREKSI PLANNING (-1)
        ========================== */

        $planningDate = date('Y-m-d');
        if ($shift == 2 && date('H') < 7) {
            $planningDate = date('Y-m-d', strtotime('-1 day'));
        }

        $pp = $pdo->prepare("
            SELECT pp_id
            FROM tbl_production_planning
            WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
            LIMIT 1
        ");

        $pp->execute([$productCode, $shift, $line, $planningDate]);
        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            $slot = $pdo->prepare("
                SELECT id
                FROM tbl_detail_production_planning
                WHERE pp_id=? AND actual>0
                ORDER BY id DESC
                LIMIT 1
            ");

            $slot->execute([$ppRow['pp_id']]);
            $s = $slot->fetch(PDO::FETCH_ASSOC);

            if ($s) {
                $pdo->prepare("
                    UPDATE tbl_detail_production_planning
                    SET actual = actual - 1
                    WHERE id=?
                ")->execute([$s['id']]);
            }
        }


        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'EXIT MECA berhasil'
        ]);
    } catch (Exception $e) {

        $pdo->rollBack();

        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

/* =====================================================
   IN MECA
===================================================== */
if ($action == 'in_meca') {

    $serial = $_POST['serial'] ?? '';
    $line   = $_POST['line'] ?? 0;
    $shift  = $_POST['shift'] ?? 0;
    $operator = $_SESSION['username'] ?? 'operator';

    if (!$serial) {
        echo json_encode(['error' => true, 'message' => 'Serial kosong']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        /* =========================
           CEK NG PRODUCT
        ========================== */

        $ng = $pdo->prepare("
            SELECT *
            FROM tbl_ng_product
            WHERE serial_no=? AND status='EXIT_MECA'
            LIMIT 1
        ");
        $ng->execute([$serial]);
        $ngRow = $ng->fetch(PDO::FETCH_ASSOC);

        if (!$ngRow) {
            throw new Exception("Produk belum EXIT MECA");
        }

        $product = $ngRow['product_code'];


        /* =========================
           AMBIL BOM
        ========================== */

        $bom = $pdo->prepare("
            SELECT part_code, qty
            FROM tbl_part_assy
            WHERE part_assy=?
        ");
        $bom->execute([$product]);


        foreach ($bom as $b) {

            $used = $b['qty'];

            /* =========================
               CEK ACTIVE MATERIAL
            ========================== */

            $mat = $pdo->prepare("
                SELECT remain, lot_no, ref_number
                FROM tbl_active_material
                WHERE part_code=?
            ");
            $mat->execute([$b['part_code']]);
            $m = $mat->fetch(PDO::FETCH_ASSOC);

            if (!$m || $m['remain'] < $used) {
                throw new Exception("Material tidak cukup untuk repair");
            }

            /* =========================
               UPDATE TRACE PRODUKSI
            ========================== */

            $pdo->prepare("
                UPDATE tbl_detail_production
                SET lot_no=?
                WHERE serial_no=? AND part_code=?
            ")->execute([
                $m['lot_no'],
                $serial,
                $b['part_code']
            ]);

            /* =========================
               POTONG ACTIVE MATERIAL
            ========================== */

            $pdo->prepare("
                UPDATE tbl_active_material
                SET remain=remain-?
                WHERE part_code=?
            ")->execute([
                $used,
                $b['part_code']
            ]);

            /* =========================
               UPDATE INCOMING
            ========================== */

            $pdo->prepare("
                UPDATE tbl_detail_part
                SET remain = remain - ?
                WHERE ref_number=?
            ")->execute([
                $used,
                $m['ref_number']
            ]);
        }


        /* =========================
           UPDATE STATUS PRODUCT
        ========================== */

        $pdo->prepare("
            UPDATE tbl_detail_product
            SET is_ng=0
            WHERE serial_no=?
        ")->execute([$serial]);


        /* =========================
           UPDATE NG PRODUCT
        ========================== */

        $pdo->prepare("
            UPDATE tbl_ng_product
            SET status='IN_MECA'
            WHERE serial_no=?
        ")->execute([$serial]);


        /* =========================
           UPDATE PLANNING ACTUAL +1
        ========================== */

        $planningDate = date('Y-m-d');
        if ($shift == 2 && date('H') < 7) {
            $planningDate = date('Y-m-d', strtotime('-1 day'));
        }

        $pp = $pdo->prepare("
            SELECT pp_id
            FROM tbl_production_planning
            WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
            LIMIT 1
        ");

        $pp->execute([$product, $shift, $line, $planningDate]);
        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            $slot = $pdo->prepare("
                SELECT id
                FROM tbl_detail_production_planning
                WHERE pp_id=? AND actual < qty
                LIMIT 1
            ");
            $slot->execute([$ppRow['pp_id']]);
            $s = $slot->fetch(PDO::FETCH_ASSOC);

            if ($s) {
                $pdo->prepare("
                    UPDATE tbl_detail_production_planning
                    SET actual = actual + 1
                    WHERE id=?
                ")->execute([$s['id']]);
            }
        }


        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'IN MECA berhasil'
        ]);
    } catch (Exception $e) {

        $pdo->rollBack();

        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
