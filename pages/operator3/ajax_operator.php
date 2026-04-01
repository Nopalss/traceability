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

<td class='line'>

<div class='btnRemoveLot btnstyle'
data-part='{$r['part_code']}'
data-lot='{$r['lot_no']}'>

</div>

<div class='btnAdjustLot btnstyle'
data-part='{$r['part_code']}'
data-lot='{$r['lot_no']}'
data-remain='{$r['remain']}'>

</div>

<div class='btnNgLot btnstyle'
data-part='{$r['part_code']}'
data-lot='{$r['lot_no']}'
data-remain='{$r['remain']}'>

</div>

</td>
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
    $operatorRemark = $_POST['operator_remark'] ?? '';

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
            (product_code,serial_no,shift,line_id,operator,qty,operator_remark,created_at)
            VALUES (?,?,?,?,?,?,?,NOW())
        ")->execute([
            $product,
            $serial,
            $shift,
            $line,
            $operator,
            $qty,
            $operatorRemark
        ]);


        /* =========================
           INSERT BOX
        ========================== */

        $pdo->prepare("
           INSERT INTO tbl_detail_product
(product_code,serial_no,qty,shift,line_id,operator,ref_number,remarks,operator_remark)
VALUES (?,?,?,?,?,?,?,?,?)
        ")->execute([
            $product,
            $serial,
            $qty,
            $shift,
            $line,
            $operator,
            $ref,
            $remarks,
            $operatorRemark
        ]);


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
            SET actual = actual + $qty
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
        SELECT *
        FROM tbl_production_output
        WHERE line_id=?
        AND DATE(created_at)=CURDATE()
        ORDER BY id DESC

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
            <td>{$r['operator_remark']}</td>
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
   GET MATERIAL FOR EXIT MECA (BOX)
===================================================== */

if ($action == 'get_exit_material') {

    $serial = $_POST['serial'] ?? '';

    if (!$serial) {
        echo json_encode(['error' => true, 'message' => 'Serial kosong']);
        exit;
    }

    $p = $pdo->prepare("
        SELECT is_ng
        FROM tbl_detail_product
        WHERE serial_no=?
        ");

    $p->execute([$serial]);
    $prod = $p->fetch(PDO::FETCH_ASSOC);

    if ($prod && $prod['is_ng'] == 1) {
        echo json_encode([
            'error' => true,
            'message' => 'Product sudah pernah EXIT MECA'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            dp.part_code,
            p.part_name,
            dp.lot_no,
            dp.used_qty
        FROM tbl_detail_production dp
        JOIN tbl_part p ON p.part_code = dp.part_code
        WHERE dp.serial_no=?
    ");

    $stmt->execute([$serial]);

    $parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$parts) {
        echo json_encode([
            'error' => true,
            'message' => 'Trace material tidak ditemukan'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'parts' => $parts
    ]);

    exit;
}

/* =====================================================
   GET UNITS
===================================================== */

if ($action == 'get_units') {

    $serial = $_POST['serial'] ?? '';

    $p = $pdo->prepare("
        SELECT is_ng
        FROM tbl_detail_product
        WHERE serial_no=?
        ");

    $p->execute([$serial]);
    $prod = $p->fetch(PDO::FETCH_ASSOC);

    if ($prod && $prod['is_ng'] == 1) {
        echo json_encode([
            'error' => true,
            'message' => 'Product sudah pernah EXIT MECA'
        ]);
        exit;
    }

    $q = $pdo->prepare("
        SELECT unit_no
        FROM tbl_product_unit
        WHERE serial_no=?
        ORDER BY unit_no
    ");

    $q->execute([$serial]);

    $units = $q->fetchAll(PDO::FETCH_COLUMN);

    if (!$units) {
        echo json_encode([
            'error' => true,
            'message' => 'Unit tidak ditemukan'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'units' => $units
    ]);

    exit;
}

/* =====================================================
   GET UNIT PARTS
===================================================== */

if ($action == 'get_unit_parts') {

    $serial = $_POST['serial'] ?? '';
    $units  = $_POST['units'] ?? [];

    if (!$units) {
        echo json_encode(['error' => true, 'message' => 'Unit kosong']);
        exit;
    }


    $in = implode(',', array_fill(0, count($units), '?'));
    $params = array_merge([$serial], $units);

    $stmt = $pdo->prepare("
        SELECT 
            um.part_code,
            p.part_name,
            um.lot_no,
            SUM(um.used_qty) used_qty
        FROM tbl_product_unit u
        JOIN tbl_unit_material um ON um.unit_id=u.id
        JOIN tbl_part p ON p.part_code=um.part_code
        WHERE u.serial_no=? 
        AND u.unit_no IN ($in)
        GROUP BY um.part_code,um.lot_no
    ");

    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'parts' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

    exit;
}

/* =====================================================
   EXIT MECA
===================================================== */
if ($action == 'exit_meca') {

    $serial = $_POST['serial'] ?? '';
    $parts  = $_POST['parts'] ?? [];
    $units  = $_POST['units'] ?? [];
    $line   = $_POST['line'] ?? 0;
    $shift  = $_POST['shift'] ?? 0;
    $ngType = $_POST['ng_type'] ?? 'MECA';

    $operator = $_SESSION['username'] ?? 'operator';

    if (!$serial) {
        echo json_encode(['error' => true, 'message' => 'Serial kosong']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        /* =========================
           AMBIL PRODUCT
        ========================= */

        $p = $pdo->prepare("
        SELECT product_code, is_ng
        FROM tbl_detail_product
        WHERE serial_no=?
        ");

        $p->execute([$serial]);
        $prod = $p->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            throw new Exception("Product tidak ditemukan");
        }

        if ($prod && $prod['is_ng'] == 1) {
            echo json_encode([
                'error' => true,
                'message' => 'Product sudah pernah EXIT MECA'
            ]);
            exit;
        }

        $product = $prod['product_code'];

        /* =========================
           INSERT HEADER NG
        ========================= */

        $pdo->prepare("
        INSERT INTO tbl_ng_product
        (serial_no,product_code,line_id,shift,operator,status)
        VALUES (?,?,?,?,?,'EXIT_MECA')
        ")->execute([
            $serial,
            $product,
            $line,
            $shift,
            $operator
        ]);

        $ngId = $pdo->lastInsertId();


        /* =========================
           ROLLBACK PLANNING ACTUAL
        ========================= */

        $pp = $pdo->prepare("
        SELECT pp_id
        FROM tbl_production_planning
        WHERE product_code=? AND shift=? AND line_id=?
        ORDER BY pp_id DESC
        LIMIT 1
        ");

        $pp->execute([$product, $shift, $line]);
        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            $pdo->prepare("
            UPDATE tbl_detail_production_planning
            SET actual = actual - 1
            WHERE pp_id=? AND actual>0
            ORDER BY id DESC
            LIMIT 1
            ")->execute([$ppRow['pp_id']]);
        }


        /* =========================
           TRACE MATERIAL
        ========================= */

        if ($units) {

            $in = implode(',', array_fill(0, count($units), '?'));
            $params = array_merge([$serial], $units);

            $stmt = $pdo->prepare("
            SELECT 
                um.part_code,
                um.lot_no,
                SUM(um.used_qty) used_qty
            FROM tbl_product_unit u
            JOIN tbl_unit_material um ON um.unit_id=u.id
            WHERE u.serial_no=? 
            AND u.unit_no IN ($in)
            GROUP BY um.part_code,um.lot_no
            ");

            $stmt->execute($params);

            $traceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {

            $stmt = $pdo->prepare("
            SELECT part_code,lot_no,used_qty
            FROM tbl_detail_production
            WHERE serial_no=?
            ");

            $stmt->execute([$serial]);

            $traceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        /* =========================
           INSERT NG PART
        ========================= */

        foreach ($parts as $p) {

            $part  = $p['part_code'];
            $lot   = $p['lot_no'];
            $ngQty = intval($p['ng_qty']);

            $used = 0;

            foreach ($traceRows as $t) {
                if ($t['part_code'] == $part && $t['lot_no'] == $lot) {
                    $used = $t['used_qty'];
                    break;
                }
            }

            if ($used == 0) continue;

            if ($ngQty > $used) $ngQty = $used;

            $pdo->prepare("
            INSERT INTO tbl_ng_part
            (ng_id,part_code,lot_no,used_qty,ng_qty,ng_type)
            VALUES (?,?,?,?,?,?)
            ")->execute([
                $ngId,
                $part,
                $lot,
                $used,
                $ngQty,
                $ngType
            ]);
        }


        /* =========================
           RETURN MATERIAL
        ========================= */

        foreach ($traceRows as $t) {

            $part = $t['part_code'];
            $lot  = $t['lot_no'];
            $used = $t['used_qty'];

            $ng = $pdo->prepare("
            SELECT SUM(ng_qty)
            FROM tbl_ng_part
            WHERE ng_id=? AND part_code=? AND lot_no=?
            ");

            $ng->execute([$ngId, $part, $lot]);
            $ngQty = intval($ng->fetchColumn());

            $return = $used - $ngQty;

            if ($return <= 0) continue;

            /* RETURN KE LINE */

            $cek = $pdo->prepare("
            SELECT id
            FROM tbl_active_material
            WHERE part_code=? AND lot_no=? AND line_id=?
            ");

            $cek->execute([$part, $lot, $line]);
            $row = $cek->fetch(PDO::FETCH_ASSOC);

            if ($row) {

                $pdo->prepare("
                UPDATE tbl_active_material
                SET remain = remain + ?
                WHERE id=?
                ")->execute([$return, $row['id']]);
            } else {

                $pdo->prepare("
                INSERT INTO tbl_active_material
                (part_code,lot_no,spq,remain,line_id)
                VALUES (?,?,?,?,?)
                ")->execute([
                    $part,
                    $lot,
                    $return,
                    $return,
                    $line
                ]);
            }


            /* RETURN KE INCOMING */

            $pdo->prepare("
            UPDATE tbl_detail_part
            SET remain = remain + ?
            WHERE lot_no=?
            ")->execute([
                $return,
                $lot
            ]);
        }


        /* SET PRODUCT NG */

        $pdo->prepare("
        UPDATE tbl_detail_product
        SET is_ng=1
        WHERE serial_no=?
        ")->execute([$serial]);


        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Exit Meca berhasil'
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


if ($action == 'in_meca') {

    $serial = trim($_POST['serial'] ?? '');

    if (!$serial) {
        echo json_encode(['error' => true, 'message' => 'Serial kosong']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        /* =========================
           CEK PRODUCT
        ========================= */

        $prod = $pdo->prepare("
            SELECT product_code,qty,shift,line_id
            FROM tbl_detail_product
            WHERE serial_no=?
        ");
        $prod->execute([$serial]);
        $p = $prod->fetch(PDO::FETCH_ASSOC);

        if (!$p) {
            throw new Exception("Product tidak ditemukan");
        }

        $product = $p['product_code'];
        $qty     = intval($p['qty']);
        $shift   = $p['shift'];
        $line    = $p['line_id'];

        /* =========================
           CEK EXIT MECA
        ========================= */

        $ng = $pdo->prepare("
            SELECT id
            FROM tbl_ng_product
            WHERE serial_no=? AND status='EXIT_MECA'
        ");
        $ng->execute([$serial]);

        if (!$ng->fetch()) {
            throw new Exception("Product belum EXIT MECA");
        }

        /* =========================
           HAPUS TRACE LAMA
        ========================= */

        $unitIds = $pdo->prepare("
            SELECT id
            FROM tbl_product_unit
            WHERE serial_no=?
        ");
        $unitIds->execute([$serial]);
        $units = $unitIds->fetchAll(PDO::FETCH_COLUMN);

        if ($units) {

            $in = implode(',', array_fill(0, count($units), '?'));

            $pdo->prepare("
                DELETE FROM tbl_unit_material
                WHERE unit_id IN ($in)
            ")->execute($units);
        }

        $pdo->prepare("
            DELETE FROM tbl_detail_production
            WHERE serial_no=?
        ")->execute([$serial]);

        /* =========================
           AMBIL BOM
        ========================= */

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
               FIFO MATERIAL
            ========================= */

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
               INSERT TRACE BARU
            ========================= */

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

                $pdo->prepare("
                    UPDATE tbl_active_material
                    SET remain = remain - ?
                    WHERE id=?
                ")->execute([
                    $lu['qty'],
                    $lu['id']
                ]);

                $pdo->prepare("
                    UPDATE tbl_detail_part
                    SET remain = remain - ?
                    WHERE ref_number=?
                ")->execute([
                    $lu['qty'],
                    $lu['ref']
                ]);
            }
        }

        /* =========================
           UPDATE PLANNING +1
        ========================= */

        $planningDate = date('Y-m-d');
        if ($shift == 2 && date('H') < 7) {
            $planningDate = date('Y-m-d', strtotime('-1 day'));
        }

        $pp = $pdo->prepare("
            SELECT pp_id
            FROM tbl_production_planning
            WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
        ");
        $pp->execute([$product, $shift, $line, $planningDate]);
        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            $pdo->prepare("
                UPDATE tbl_detail_production_planning
                SET actual = actual + 1
                WHERE pp_id=? AND actual < qty
                ORDER BY id
                LIMIT 1
            ")->execute([$ppRow['pp_id']]);
        }

        /* =========================
           UPDATE STATUS
        ========================= */

        $pdo->prepare("
            UPDATE tbl_detail_product
            SET is_ng=0
            WHERE serial_no=?
        ")->execute([$serial]);

        $pdo->prepare("
            UPDATE tbl_ng_product
            SET status='IN_MECA'
            WHERE serial_no=?
        ")->execute([$serial]);

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

/* =====================================
   LOAD NG TYPE
===================================== */

if ($action == 'get_ng_type') {

    $q = $pdo->query("
        SELECT ng_code,ng_name
        FROM tbl_ng_type
        WHERE status='ACTIVE'
        ORDER BY ng_name
    ");

    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

/* =====================================================
   REMOVE ACTIVE MATERIAL
===================================================== */

if ($action == 'remove_active_material') {

    $part = $_POST['part'] ?? '';
    $lot  = $_POST['lot'] ?? '';
    $line = $_POST['line'] ?? 0;

    $operator = $_SESSION['username'] ?? 'operator';

    try {

        $pdo->beginTransaction();

        $q = $pdo->prepare("
        SELECT *
        FROM tbl_active_material
        WHERE part_code=? AND lot_no=? AND line_id=?
        LIMIT 1
        ");

        $q->execute([$part, $lot, $line]);

        $row = $q->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Material tidak ditemukan");
        }

        $remain = intval($row['remain']);

        if ($remain > 0) {

            $pdo->prepare("
            INSERT INTO tbl_material_loss
            (part_code,lost_qty,old_remain,operator,reason,assy,ref_number,shift,line_id)
            VALUES (?,?,?,?,?,?,?,?,?)
            ")->execute([

                $part,
                $remain,
                $remain,
                $operator,
                'CLOSE LOT',
                '',
                $row['ref_number'],
                0,
                $line

            ]);
        }

        $pdo->prepare("
        DELETE FROM tbl_active_material
        WHERE id=?
        ")->execute([$row['id']]);

        $pdo->commit();

        echo json_encode(['success' => true]);
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
   ADJUST MATERIAL
===================================================== */

if ($action == 'adjust_material') {

    $part = $_POST['part'] ?? '';
    $lot  = $_POST['lot'] ?? '';
    $line = $_POST['line'] ?? 0;

    $type = $_POST['type'] ?? '';
    $qty  = intval($_POST['qty'] ?? 0);

    $operator = $_SESSION['username'] ?? 'operator';

    if (!$part || !$lot) {
        echo json_encode(['error' => true, 'message' => 'Part atau Lot kosong']);
        exit;
    }

    if ($qty <= 0) {
        echo json_encode(['error' => true, 'message' => 'Qty tidak valid']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        $q = $pdo->prepare("
        SELECT *
        FROM tbl_active_material
        WHERE part_code=? AND lot_no=? AND line_id=?
        LIMIT 1
        ");

        $q->execute([$part, $lot, $line]);
        $row = $q->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Material tidak ditemukan");
        }

        $remain = intval($row['remain']);

        if ($type == 'ADD') {

            $pdo->prepare("
            UPDATE tbl_active_material
            SET remain = remain + ?
            WHERE id=?
            ")->execute([$qty, $row['id']]);

            /* INCOMING IKUT NAIK */

            $pdo->prepare("
            UPDATE tbl_detail_part
            SET remain = remain + ?
            WHERE lot_no=?
            ")->execute([$qty, $lot]);
        }

        if ($type == 'SUB') {

            if ($qty > $remain) {
                throw new Exception("Qty melebihi remain material");
            }

            $pdo->prepare("
            UPDATE tbl_active_material
            SET remain = remain - ?
            WHERE id=?
            ")->execute([$qty, $row['id']]);

            /* INCOMING IKUT TURUN */

            $pdo->prepare("
            UPDATE tbl_detail_part
            SET remain = remain - ?
            WHERE lot_no=?
            ")->execute([$qty, $lot]);
        }

        $pdo->prepare("
        INSERT INTO tbl_material_loss
        (part_code,lost_qty,old_remain,operator,reason,assy,ref_number,shift,line_id)
        VALUES (?,?,?,?,?,?,?,?,?)
        ")->execute([
            $part,
            $qty,
            $remain,
            $operator,
            'ADJUST ' . $type,
            '',
            $row['ref_number'],
            0,
            $line
        ]);

        $pdo->commit();

        echo json_encode(['success' => true]);
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
   MATERIAL NG
===================================================== */

if ($action == 'material_ng') {

    $part   = $_POST['part'] ?? '';
    $lot    = $_POST['lot'] ?? '';
    $line   = $_POST['line'] ?? 0;
    $shift  = $_POST['shift'] ?? 0;

    $qty    = intval($_POST['qty'] ?? 0);
    $reason = $_POST['reason'] ?? 'NG';

    $operator = $_SESSION['username'] ?? 'operator';

    if ($qty <= 0) {
        echo json_encode(['error' => true, 'message' => 'Qty tidak valid']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        $q = $pdo->prepare("
        SELECT *
        FROM tbl_active_material
        WHERE part_code=? AND lot_no=? AND line_id=?
        LIMIT 1
        ");

        $q->execute([$part, $lot, $line]);

        $row = $q->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Material tidak ditemukan");
        }

        if ($qty > $row['remain']) {
            throw new Exception("NG qty melebihi remain");
        }

        $pdo->prepare("
            UPDATE tbl_active_material
            SET remain = remain - ?
            WHERE id=?
        ")->execute([$qty, $row['id']]);


        /* UPDATE INCOMING */

        $pdo->prepare("
            UPDATE tbl_detail_part
            SET remain = remain - ?
            WHERE lot_no=?
        ")->execute([$qty, $lot]);

        $pdo->prepare("
        INSERT INTO tbl_material_loss
        (part_code,lost_qty,old_remain,operator,reason,assy,ref_number,shift,line_id)
        VALUES (?,?,?,?,?,?,?,?,?)
        ")->execute([

            $part,
            $qty,
            $row['remain'],
            $operator,
            $reason,
            '',
            $row['ref_number'],
            $shift,
            $line

        ]);

        $pdo->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {

        $pdo->rollBack();

        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
