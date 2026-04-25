<?php
require '../../includes/config.php';

$action = $_REQUEST['action'] ?? '';

/* =====================================================
   LOAD BOM
===================================================== */
if ($action == 'load_bom') {

    $assy = $_GET['assy'] ?? '';
    $line = $_GET['line'] ?? '';

    $q = $pdo->prepare("
    SELECT 
        pa.part_id,
        pa.part_code,
        pa.remark,
        pa.subs,
        pa.qty AS used,
        p.part_name,

        -- 🔥 FIX: ambil stock dari subquery (AMAN)
        (
            SELECT COALESCE(SUM(remain),0)
            FROM tbl_active_material am2
            WHERE am2.part_id = pa.part_id
            AND am2.line_id = ?
        ) as remain,

        -- ambil salah satu lot (optional)
        (
            SELECT lot_no 
            FROM tbl_active_material am2
            WHERE am2.part_id = pa.part_id
            AND am2.line_id = ?
            LIMIT 1
        ) as lot_no,

        (
            SELECT spq 
            FROM tbl_active_material am2
            WHERE am2.part_id = pa.part_id
            AND am2.line_id = ?
            LIMIT 1
        ) as spq,

        (
            SELECT ref_number 
            FROM tbl_active_material am2
            WHERE am2.part_id = pa.part_id
            AND am2.line_id = ?
            LIMIT 1
        ) as ref_number

    FROM tbl_part_assy pa
    JOIN tbl_part p ON p.id_part = pa.part_id

    -- 🔥 tetap filter dari planning
    JOIN tbl_pp_material pm 
        ON pm.part_id = pa.part_id

    WHERE pa.part_assy = ?

    GROUP BY 
        pa.part_id,
        pa.part_code,
        pa.remark,
        pa.qty,
        pa.subs,
        p.part_name

    ORDER BY pa.remark ASC
");

    // 🔥 karena subquery pakai ? 4x
    $q->execute([$line, $line, $line, $line, $assy]);

    $rows = $q->fetchAll(PDO::FETCH_ASSOC);

    // ================= MAIN STOCK =================
    $mainStock = [];

    foreach ($rows as $r) {
        if ($r['remark'] == 0) {
            $mainStock[$r['part_id']] = $r['remain'];
        }
    }

    foreach ($rows as $r) {

        $isSub = ($r['remark'] == 1);

        if ($isSub) {

            $parentId = $r['subs'];
            $parentStock = $mainStock[$parentId] ?? 0;

            if ($parentStock > 0) {
                continue;
            }
        }

        $bg = $isSub ? "style='background:#fff3cd'" : "";

        echo "<tr {$bg}>
            <td>{$r['part_code']}</td>
            <td>
                {$r['part_name']} 
                " . ($isSub ? "<span style='color:#856404'>(SUBS)</span>" : "") . "
            </td>
            <td>{$r['used']}</td>
            <td>{$r['lot_no']}</td>
            <td>{$r['spq']}</td>
            <td>{$r['remain']}</td>

<td class='line'>

<div class='btnRemoveLot btnstyle'
data-part='{$r['part_code']}'
data-lot='{$r['lot_no']}'
data-ref='{$r['ref_number']}'>
</div>

<div class='btnAdjustLot btnstyle'
data-part='{$r['part_code']}'
data-lot='{$r['lot_no']}'
data-ref='{$r['ref_number']}'
data-remain='{$r['remain']}'>
</div>

<div class='btnNgLot btnstyle'
data-part='{$r['part_code']}'
data-part-id='{$r['part_id']}'
data-ref='{$r['ref_number']}'
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
        SELECT remain, part_id
        FROM tbl_detail_part
        WHERE ref_number=? AND part_code=?
        LIMIT 1
    ");
    $cekRef->execute([$ref, $part]);
    $refRow = $cekRef->fetch(PDO::FETCH_ASSOC);

    if (!$refRow) {
        echo json_encode(['error' => true, 'message' => 'Ref material tidak terdaftar']);
        exit;
    }
    if (intval($refRow['remain']) == 0) {
        echo json_encode(['error' => true, 'message' => 'Material sudah habis']);
        exit;
    }

    $remainIncoming = intval($refRow['remain']);

    /* =========================
       VALIDASI BOM
    ========================== */

    $cekBom = $pdo->prepare("
        SELECT 1
        FROM tbl_part_assy
        WHERE part_assy=? AND part_code=? AND part_id=?
    ");
    $cekBom->execute([$assy, $part, $refRow['part_id']]);

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
        WHERE part_code=? AND lot_no=? AND ref_number=?
        LIMIT 1
    ");
    $cekLot->execute([$part, $lot, $ref]);
    $lotRow = $cekLot->fetch(PDO::FETCH_ASSOC);
    if ($lotRow && !$mode) {
        echo json_encode(['error' => true, 'message' => 'Material(ref) Sudah di Scan']);
        exit;
    }


    $pdo->prepare("UPDATE tbl_detail_part
                    SET status = 'USED'
                    WHERE ref_number=? AND part_code=? AND part_id=?
                ")->execute([$ref, $part, $refRow['part_id']]);

    /* =========================
       CEK MATERIAL AKTIF DI LINE
    ========================== */

    $cekExist = $pdo->prepare("
    SELECT COUNT(*) as total
    FROM tbl_active_material
    WHERE part_code=? 
    AND line_id=?
    AND part_id=?
");
    $cekExist->execute([$part, $line, $refRow['part_id']]);
    $existCount = $cekExist->fetch(PDO::FETCH_ASSOC)['total'];


    /* =========================
       FIRST SCAN
    ========================== */

    if ($existCount == 0 && !$mode) {

        $pdo->prepare("
            INSERT INTO tbl_active_material
            (part_code,lot_no,spq,remain,ref_number,line_id, part_id)
            VALUES (?,?,?,?,?,?,?)
        ")->execute([
            $part,
            $lot,
            $qty,
            $remainIncoming,
            $ref,
            $line,
            $refRow['part_id']

        ]);

        echo json_encode(['success' => true]);
        exit;
    }

    /* =========================
       LOT BERBEDA → POPUP
    ========================== */

    if ($existCount > 0 && !$mode) {
        echo json_encode(['needConfirm' => true]);
        exit;
    }



    /* =========================
       ADD MATERIAL
    ========================== */

    if ($mode === 'add') {

        $pdo->prepare("
                INSERT INTO tbl_active_material
                (part_code,lot_no,spq,remain,ref_number,line_id, part_id)
                VALUES (?,?,?,?,?,?,?)
            ")->execute([
            $part,
            $lot,
            $qty,
            $remainIncoming,
            $ref,
            $line,
            $refRow['part_id']
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

    $cekSerial = $pdo->prepare("
        SELECT 1 FROM tbl_detail_product 
        WHERE serial_no=? AND ref_number=? AND product_code=?
    ");
    $cekSerial->execute([$serial, $ref, $z1]);

    if ($cekSerial->fetch()) {
        echo json_encode(['error' => true, 'message' => 'Label Product sudah pernah discan']);
        exit;
    }

    $planningDate = date('Y-m-d');
    if ($shift == 2 && date('H') < 7) {
        $planningDate = date('Y-m-d', strtotime('-1 day'));
    }

    try {

        $pdo->beginTransaction();

        /* ================= OUTPUT ================= */
        $pdo->prepare("
            INSERT INTO tbl_production_output
            (product_code,serial_no,shift,line_id,operator,qty,operator_remark,created_at,ref_number)
            VALUES (?,?,?,?,?,?,?,NOW(),?)
        ")->execute([$product, $serial, $shift, $line, $operator, $qty, $operatorRemark, $ref]);

        /* ================= BOX ================= */
        $pdo->prepare("
            INSERT INTO tbl_detail_product
            (product_code,serial_no,qty,shift,line_id,operator,ref_number,remarks,operator_remark)
            VALUES (?,?,?,?,?,?,?,?,?)
        ")->execute([$product, $serial, $qty, $shift, $line, $operator, $ref, $remarks, $operatorRemark]);

        /* ================= UNIT ================= */
        for ($i = 1; $i <= $qty; $i++) {

            $cekUnit = $pdo->prepare("
                SELECT 1 FROM tbl_product_unit 
                WHERE serial_no=? AND unit_no=? AND ref_number=?
            ");
            $cekUnit->execute([$serial, $i, $ref]);

            if (!$cekUnit->fetch()) {
                $pdo->prepare("
                    INSERT INTO tbl_product_unit
                    (serial_no,product_code,unit_no,ref_number)
                    VALUES (?,?,?,?)
                ")->execute([$serial, $product, $i, $ref]);
            }
        }

        /* ================= BOM FIX (NO GROUP BY!) ================= */
        $bom = $pdo->prepare("
            SELECT 
                pa.part_id,
                pa.part_code,
                pa.qty,
                pa.remark,
                pa.subs
            FROM tbl_pp_material pm
            JOIN tbl_part_assy pa ON pa.part_id = pm.part_id
            WHERE pm.pp_id = (
                SELECT pp_id FROM tbl_production_planning
                WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
                LIMIT 1
            )
            ORDER BY pa.remark ASC
        ");

        $bom->execute([$product, $shift, $line, $planningDate]);
        $bomRows = $bom->fetchAll(PDO::FETCH_ASSOC);

        $processed = [];

        foreach ($bomRows as $b) {

            // 🔥 prevent double berdasarkan PART CODE (bukan part_id)
            if (isset($processed[$b['part_code']])) continue;
            $processed[$b['part_code']] = true;

            $mainPartCode = $b['part_code'];
            $need = intval($b['qty']) * $qty;

            $usageAll = [];

            /* ================= MAIN ================= */
            $lots = $pdo->prepare("
                SELECT * FROM tbl_active_material
                WHERE part_code=? AND part_id=? AND line_id=? AND remain>0
                ORDER BY id ASC
            ");
            $lots->execute([$mainPartCode, $b['part_id'], $line]);

            foreach ($lots as $lot) {

                if ($need <= 0) break;

                $take = min($lot['remain'], $need);

                $usageAll[] = [
                    'part_code' => $mainPartCode,
                    'part_id' => $lot['part_id'],
                    'lot_no' => $lot['lot_no'],
                    'ref' => $lot['ref_number'],
                    'qty' => $take,
                    'id' => $lot['id']
                ];

                $need -= $take;
            }

            /* ================= SUBSTITUTE ================= */
            if ($need > 0 && $b['remark'] == 0) {

                $subs = $pdo->prepare("
                    SELECT part_code, part_id 
                    FROM tbl_part_assy
                    WHERE subs=? AND remark=1
                ");
                $subs->execute([$b['part_id']]);

                foreach ($subs as $s) {

                    $lots = $pdo->prepare("
                        SELECT * FROM tbl_active_material
                        WHERE part_code=? AND part_id=? AND line_id=? AND remain>0
                        ORDER BY id ASC
                    ");
                    $lots->execute([$s['part_code'], $s['part_id'], $line]);

                    foreach ($lots as $lot) {

                        if ($need <= 0) break;

                        $take = min($lot['remain'], $need);

                        $usageAll[] = [
                            'part_code' => $s['part_code'],
                            'part_id' => $s['part_id'],
                            'lot_no' => $lot['lot_no'],
                            'ref' => $lot['ref_number'],
                            'qty' => $take,
                            'id' => $lot['id']
                        ];

                        $need -= $take;
                    }
                }
            }

            if ($need > 0) {
                throw new Exception("Material tidak cukup (MAIN + SUB)");
            }

            /* ================= EXECUTE ================= */
            foreach ($usageAll as $u) {

                // TRACE
                $pdo->prepare("
                    INSERT INTO tbl_detail_production
                    (product_code,serial_no,part_code,used_qty,lot_no,ref_number,ref_product)
                    VALUES (?,?,?,?,?,?,?)
                ")->execute([
                    $product,
                    $serial,
                    $u['part_code'],
                    $u['qty'],
                    $u['lot_no'],
                    $u['ref'],
                    $ref
                ]);

                // ACTIVE MATERIAL
                $pdo->prepare("
                    UPDATE tbl_active_material
                    SET remain = remain - ?
                    WHERE id=?
                ")->execute([$u['qty'], $u['id']]);

                // DELETE kalau habis
                $pdo->prepare("
                    DELETE FROM tbl_active_material
                    WHERE id=? AND remain<=0
                ")->execute([$u['id']]);

                // DETAIL PART (FIXED)
                $pdo->prepare("
                    UPDATE tbl_detail_part
                    SET remain = remain - ?
                    WHERE ref_number=? AND part_code=?
                ")->execute([$u['qty'], $u['ref'], $u['part_code']]);

                $pdo->prepare("
                    UPDATE tbl_detail_part
                    SET status='USED'
                    WHERE ref_number=? AND part_code=? AND remain<=0
                ")->execute([$u['ref'], $u['part_code']]);
            }
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

            $remaining = $qty;

            foreach ($slots as $slot) {

                if ($remaining <= 0) break;

                if ($slot['qty'] <= 0) continue;

                $capacity = $slot['qty'] - $slot['actual'];

                if ($capacity <= 0) continue;

                $take = min($capacity, $remaining);

                $pdo->prepare("
        UPDATE tbl_detail_production_planning
        SET actual = actual + ?
        WHERE id=?
    ")->execute([$take, $slot['id']]);

                $remaining -= $take;
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
   OUTPUT
===================================================== */
if ($action == 'output') {

    $line = $_SESSION['line_id'] ?? 7;

    $query = $pdo->prepare("SELECT line_name FROM tbl_line WHERE line_id=?");
    $query->execute([$line]);
    $lineName = $query->fetchColumn();
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
            <td>{$lineName}</td>
            <td>{$r['operator']}</td>
            <td>{$r['qty']}</td>
            <td>{$r['serial_no']}</td>
            <td>{$r['ref_number']}</td>
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
    $ref = $_POST['ref'] ?? '';

    if (!$serial || !$ref) {
        echo json_encode(['error' => true, 'message' => 'Material kosong']);
        exit;
    }

    // ================= VALIDASI =================
    $p = $pdo->prepare("
        SELECT is_ng
        FROM tbl_detail_product
        WHERE serial_no=? AND ref_number=?
    ");
    $p->execute([$serial, $ref]);
    $prod = $p->fetch(PDO::FETCH_ASSOC);

    if ($prod && $prod['is_ng'] == 1) {
        echo json_encode([
            'error' => true,
            'message' => 'Product sudah pernah EXIT MECA'
        ]);
        exit;
    }

    // ================= AMBIL TRACE REAL =================
    $stmt = $pdo->prepare("
        SELECT 
            dp.part_code,
            dp.lot_no,
            dp.ref_number,
            SUM(dp.used_qty) as used_qty
        FROM tbl_detail_production dp
        WHERE dp.serial_no=? AND dp.ref_product=?
        GROUP BY dp.part_code, dp.lot_no, dp.ref_number
    ");
    $stmt->execute([$serial, $ref]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo json_encode([
            'error' => true,
            'message' => 'Trace material tidak ditemukan'
        ]);
        exit;
    }

    // ================= MAP KE PARENT =================
    $grouped = [];

    foreach ($rows as $r) {

        // ambil info BOM
        $bom = $pdo->prepare("
            SELECT part_id, remark, subs
            FROM tbl_part_assy
            WHERE part_code=?
        ");
        $bom->execute([$r['part_code']]);
        $bomRows = $bom->fetchAll(PDO::FETCH_ASSOC);

        if (!$bomRows) continue;

        $parentId = null;
        $isMain = false;

        foreach ($bomRows as $b) {

            if ($b['remark'] == 0) {
                $parentId = $b['part_id'];
                $isMain = true;
            }

            if ($b['remark'] == 1) {
                $parentId = $b['subs'];
            }
        }

        if (!$parentId) continue;

        // grouping by parent
        if (!isset($grouped[$parentId])) {
            $grouped[$parentId] = [
                'main' => [],
                'sub'  => []
            ];
        }

        // 🔥 inject part_id ke data
        $r['part_id'] = $parentId;

        if ($isMain) {
            $grouped[$parentId]['main'][] = $r;
        } else {
            $grouped[$parentId]['sub'][] = $r;
        }
    }

    // ================= FINAL SELECT =================
    $final = [];

    foreach ($grouped as $parentId => $g) {

        // PRIORITAS: MAIN
        if (!empty($g['main'])) {
            foreach ($g['main'] as $m) {
                $final[] = $m;
            }
        } else {
            foreach ($g['sub'] as $s) {
                $final[] = $s;
            }
        }
    }

    // ================= TAMBAH NAMA PART =================
    foreach ($final as &$f) {

        $p = $pdo->prepare("
            SELECT part_name
            FROM tbl_part
            WHERE part_code=?
            LIMIT 1
        ");
        $p->execute([$f['part_code']]);
        $f['part_name'] = $p->fetchColumn();
    }

    echo json_encode([
        'success' => true,
        'parts' => $final
    ]);

    exit;
}

/* =====================================================
   GET UNITS
===================================================== */

if ($action == 'get_units') {

    $serial = $_POST['serial'] ?? '';
    $ref = $_POST['ref'] ?? '';

    $p = $pdo->prepare("
        SELECT is_ng
        FROM tbl_detail_product
        WHERE serial_no=? AND ref_number=?
        ");

    $p->execute([$serial, $ref]);
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
        WHERE serial_no=? AND ref_number=?
        ORDER BY unit_no
    ");

    $q->execute([$serial, $ref]);

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
    $ref = $_POST['ref'] ?? '';
    $units  = $_POST['units'] ?? [];

    if (!$units) {
        echo json_encode(['error' => true, 'message' => 'Unit kosong']);
        exit;
    }


    $in = implode(',', array_fill(0, count($units), '?'));
    $params = array_merge([$serial, $ref], $units);

    $stmt = $pdo->prepare("
        SELECT 
            um.part_code,
            p.part_name,
            um.lot_no,
            SUM(um.used_qty) used_qty
        FROM tbl_product_unit u
        JOIN tbl_unit_material um ON um.unit_id=u.id
        JOIN tbl_part p ON p.part_code=um.part_code
        WHERE u.serial_no=? AND u.ref_number=?
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
    $qty = intval($_POST['qty']) ?? 0;
    $ref_product = $_POST['ref_product'] ?? '';
    $parts  = $_POST['parts'] ?? [];
    $units  = $_POST['units'] ?? [];
    $line   = $_POST['line'] ?? 0;
    $shift  = $_POST['shift'] ?? 0;

    $operator = $_SESSION['username'] ?? 'operator';

    if (!$serial || !$ref_product) {
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
            WHERE serial_no=? AND ref_number=?
        ");
        $p->execute([$serial, $ref_product]);
        $prod = $p->fetch(PDO::FETCH_ASSOC);

        if (!$prod) throw new Exception("Product tidak ditemukan");
        if ($prod['is_ng'] == 1) throw new Exception("Product sudah pernah EXIT MECA");

        $product = $prod['product_code'];

        /* =========================
           INSERT HEADER NG
        ========================= */

        $pdo->prepare("
            INSERT INTO tbl_ng_product
            (serial_no,product_code,line_id,shift,operator,status,ref_number)
            VALUES (?,?,?,?,?,'EXIT_MECA',?)
        ")->execute([
            $serial,
            $product,
            $line,
            $shift,
            $operator,
            $ref_product
        ]);

        $ngId = $pdo->lastInsertId();


        /* =========================
           ROLLBACK PLANNING (TETAP DIPERTAHANKAN)
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

            $remaining = $qty;

            $shiftData = $pdo->prepare("SELECT start FROM tbl_shift WHERE shift=?");
            $shiftData->execute([$shift]);
            $shiftRow = $shiftData->fetch(PDO::FETCH_ASSOC);

            $shiftStart = intval($shiftRow['start'] ?? 0);

            $slots = $pdo->prepare("
                SELECT id, actual
                FROM tbl_detail_production_planning
                WHERE pp_id=? AND actual > 0
                ORDER BY
                CASE
                    WHEN jam='OT' THEN 99
                    WHEN CAST(SUBSTRING(jam,1,2) AS UNSIGNED) < ?
                        THEN CAST(SUBSTRING(jam,1,2) AS UNSIGNED) + 24
                    ELSE CAST(SUBSTRING(jam,1,2) AS UNSIGNED)
                END DESC
            ");
            $slots->execute([$ppRow['pp_id'], $shiftStart]);

            foreach ($slots as $slot) {

                if ($remaining <= 0) break;

                $take = min($slot['actual'], $remaining);

                $pdo->prepare("
                    UPDATE tbl_detail_production_planning
                    SET actual = actual - ?
                    WHERE id=?
                ")->execute([$take, $slot['id']]);

                $remaining -= $take;
            }
        }

        /* =========================
           TRACE MATERIAL
        ========================= */

        if ($units) {

            $in = implode(',', array_fill(0, count($units), '?'));
            $params = array_merge([$serial, $ref_product], $units);

            $stmt = $pdo->prepare("
                SELECT 
                    um.part_code,
                    um.lot_no,
                    SUM(um.used_qty) used_qty,
                    um.ref_number
                FROM tbl_product_unit u
                JOIN tbl_unit_material um ON um.unit_id=u.id
                WHERE u.serial_no=? AND u.ref_number=?
                AND u.unit_no IN ($in)
                GROUP BY um.part_code,um.lot_no,um.ref_number
            ");
            $stmt->execute($params);
            $traceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {

            $stmt = $pdo->prepare("
                SELECT part_code,lot_no,used_qty,ref_number
                FROM tbl_detail_production
                WHERE serial_no=? AND ref_product=?
            ");
            $stmt->execute([$serial, $ref_product]);
            $traceRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /* =========================
           INSERT NG PART (FIX PER PART)
        ========================= */

        foreach ($parts as $p) {

            $part  = $p['part_code'];
            $lot   = $p['lot_no'];
            $ref_part = $p['ref_number'];
            $ngQty = intval($p['ng_qty']);
            $ngType = $p['ng_type'] ?? 'MECA';

            $used = 0;

            foreach ($traceRows as $t) {
                if (
                    $t['part_code'] == $part &&
                    $t['lot_no'] == $lot &&
                    $t['ref_number'] == $ref_part
                ) {
                    $used = $t['used_qty'];
                    break;
                }
            }

            if ($used <= 0) continue;

            if ($ngQty > $used) {
                throw new Exception("NG qty melebihi usage");
            }

            $pdo->prepare("
                INSERT INTO tbl_ng_part
                (ng_id,part_code,lot_no,used_qty,ng_qty,ng_type,ref_part)
                VALUES (?,?,?,?,?,?,?)
            ")->execute([
                $ngId,
                $part,
                $lot,
                $used,
                $ngQty,
                $ngType,
                $ref_part
            ]);
        }

        /* =========================
           RETURN MATERIAL (FIX AKURAT)
        ========================= */

        foreach ($traceRows as $t) {

            $part = $t['part_code'];
            $lot  = $t['lot_no'];
            $used = $t['used_qty'];
            $ref_part = $t['ref_number'];

            $ng = $pdo->prepare("
                SELECT COALESCE(SUM(ng_qty),0)
                FROM tbl_ng_part
                WHERE ng_id=? AND part_code=? AND lot_no=? AND ref_part=?
            ");
            $ng->execute([$ngId, $part, $lot, $ref_part]);
            $ngQty = intval($ng->fetchColumn());

            $return = $used - $ngQty;
            if ($return <= 0) continue;

            /* RETURN KE LINE */

            $cek = $pdo->prepare("
                SELECT id
                FROM tbl_active_material
                WHERE part_code=? AND lot_no=? AND line_id=? AND ref_number=?
            ");
            $cek->execute([$part, $lot, $line, $ref_part]);
            $row = $cek->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $pdo->prepare("
                    UPDATE tbl_active_material
                    SET remain = remain + ?
                    WHERE id=?
                ")->execute([$return, $row['id']]);
            } else {
                // 🔥 ambil part_id dulu
                $getPart = $pdo->prepare("
    SELECT id_part 
    FROM tbl_part 
    WHERE part_code=? 
    LIMIT 1
");
                $getPart->execute([$part]);
                $partId = $getPart->fetchColumn();

                if (!$partId) {
                    throw new Exception("Part tidak ditemukan: " . $part);
                }

                $pdo->prepare("
INSERT INTO tbl_active_material
(part_id, part_code, lot_no, spq, remain, line_id, ref_number)
VALUES (?,?,?,?,?,?,?)
")->execute([
                    $partId,
                    $part,
                    $lot,
                    $return,
                    $return,
                    $line,
                    $ref_part
                ]);
            }

            /* RETURN KE INCOMING (FIX PART_CODE) */

            $pdo->prepare("
                UPDATE tbl_detail_part
                SET remain = remain + ?
                WHERE lot_no=? AND ref_number=? AND part_code=?
            ")->execute([
                $return,
                $lot,
                $ref_part,
                $part
            ]);
        }

        /* =========================
           SET PRODUCT NG
        ========================= */

        $pdo->prepare("
            UPDATE tbl_detail_product
            SET is_ng=1
            WHERE serial_no=? AND ref_number=?
        ")->execute([$serial, $ref_product]);

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
    $ref = trim($_POST['ref'] ?? '');
    $qty_product = intval($_POST['qty'] ?? 1);

    if (!$serial || !$ref) {
        echo json_encode(['error' => true, 'message' => 'Serial kosong']);
        exit;
    }

    try {

        $pdo->beginTransaction();

        /* =========================
           CEK PRODUCT
        ========================= */

        $prod = $pdo->prepare("
            SELECT product_code,qty,shift,line_id,ref_number
            FROM tbl_detail_product
            WHERE serial_no=? AND ref_number=?
        ");
        $prod->execute([$serial, $ref]);
        $p = $prod->fetch(PDO::FETCH_ASSOC);

        if (!$p) {
            throw new Exception("Product tidak ditemukan");
        }

        $product = $p['product_code'];
        $qty     = intval($p['qty']);
        $shift   = $p['shift'];
        $line    = $p['line_id'];

        /* =========================
           PLANNING DATE (FIX)
        ========================= */

        $planningDate = date('Y-m-d');
        if ($shift == 2 && date('H') < 7) {
            $planningDate = date('Y-m-d', strtotime('-1 day'));
        }

        /* =========================
           CEK EXIT MECA
        ========================= */

        $ng = $pdo->prepare("
            SELECT id
            FROM tbl_ng_product
            WHERE serial_no=? AND status='EXIT_MECA' AND ref_number=?
        ");
        $ng->execute([$serial, $ref]);
        $ngRow = $ng->fetch(PDO::FETCH_ASSOC);

        if (!$ngRow) {
            throw new Exception("Product belum EXIT MECA");
        }

        $ngId = $ngRow['id'];

        /* =========================
           HAPUS TRACE LAMA
        ========================= */

        $unitIds = $pdo->prepare("
            SELECT id
            FROM tbl_product_unit
            WHERE serial_no=? AND ref_number=?
        ");
        $unitIds->execute([$serial, $ref]);
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
            WHERE serial_no=? AND ref_product=?
        ")->execute([$serial, $ref]);

        /* =========================
           🔥 BOM (SAMA KAYAK SCAN)
        ========================= */

        $bom = $pdo->prepare("
            SELECT 
                pa.part_id,
                pa.part_code,
                pa.qty,
                pa.remark,
                pa.subs
            FROM tbl_pp_material pm
            JOIN tbl_part_assy pa ON pa.part_id = pm.part_id
            WHERE pm.pp_id = (
                SELECT pp_id FROM tbl_production_planning
                WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
                LIMIT 1
            )
            ORDER BY pa.remark ASC
        ");

        $bom->execute([$product, $shift, $line, $planningDate]);
        $bomRows = $bom->fetchAll(PDO::FETCH_ASSOC);

        $processed = [];

        foreach ($bomRows as $b) {

            // 🔥 prevent double (MAIN + SUBSTITUTE)
            if (isset($processed[$b['part_code']])) continue;
            $processed[$b['part_code']] = true;

            $mainPartCode = $b['part_code'];
            $need = intval($b['qty']) * $qty;

            $usageAll = [];

            /* ================= MAIN ================= */
            $lots = $pdo->prepare("
                SELECT *
                FROM tbl_active_material
                WHERE part_code=? AND line_id=? AND remain>0
                ORDER BY id ASC
            ");
            $lots->execute([$mainPartCode, $line]);

            foreach ($lots as $lot) {

                if ($need <= 0) break;

                $take = min($lot['remain'], $need);

                $usageAll[] = [
                    'part_code' => $mainPartCode,
                    'lot_no' => $lot['lot_no'],
                    'ref' => $lot['ref_number'],
                    'qty' => $take,
                    'id' => $lot['id']
                ];

                $need -= $take;
            }

            /* ================= SUBSTITUTE ================= */
            if ($need > 0 && $b['remark'] == 0) {

                $subs = $pdo->prepare("
                    SELECT part_code, part_id 
                    FROM tbl_part_assy
                    WHERE subs=? AND remark=1
                ");
                $subs->execute([$b['part_id']]);

                foreach ($subs as $s) {

                    $lots = $pdo->prepare("
                        SELECT *
                        FROM tbl_active_material
                        WHERE part_code=? AND line_id=? AND remain>0
                        ORDER BY id ASC
                    ");
                    $lots->execute([$s['part_code'], $line]);

                    foreach ($lots as $lot) {

                        if ($need <= 0) break;

                        $take = min($lot['remain'], $need);

                        $usageAll[] = [
                            'part_code' => $s['part_code'],
                            'lot_no' => $lot['lot_no'],
                            'ref' => $lot['ref_number'],
                            'qty' => $take,
                            'id' => $lot['id']
                        ];

                        $need -= $take;
                    }
                }
            }

            if ($need > 0) {
                throw new Exception("Material tidak cukup (MAIN + SUB)");
            }

            /* ================= EXECUTE ================= */
            foreach ($usageAll as $u) {

                $pdo->prepare("
                    INSERT INTO tbl_detail_production
                    (product_code,serial_no,part_code,used_qty,lot_no,ref_number,ref_product)
                    VALUES (?,?,?,?,?,?,?)
                ")->execute([
                    $product,
                    $serial,
                    $u['part_code'],
                    $u['qty'],
                    $u['lot_no'],
                    $u['ref'],
                    $ref
                ]);

                $pdo->prepare("
                    UPDATE tbl_active_material
                    SET remain = remain - ?
                    WHERE id=?
                ")->execute([$u['qty'], $u['id']]);

                $pdo->prepare("
                    UPDATE tbl_detail_part
                    SET remain = remain - ?
                    WHERE ref_number=? AND part_code=?
                ")->execute([$u['qty'], $u['ref'], $u['part_code']]);
            }
        }

        /* =========================
           UPDATE PLANNING
        ========================= */

        $pp = $pdo->prepare("
            SELECT pp_id
            FROM tbl_production_planning
            WHERE product_code=? AND shift=? AND line_id=? AND production_date=?
        ");
        $pp->execute([$product, $shift, $line, $planningDate]);
        $ppRow = $pp->fetch(PDO::FETCH_ASSOC);

        if ($ppRow) {

            $shiftData = $pdo->prepare("
                SELECT start
                FROM tbl_shift
                WHERE shift=?
            ");
            $shiftData->execute([$shift]);
            $shiftStart = intval($shiftData->fetchColumn());

            $remaining = $qty;

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

                if ($remaining <= 0) break;

                $capacity = $slot['qty'] - $slot['actual'];
                if ($capacity <= 0) continue;

                $take = min($capacity, $remaining);

                $pdo->prepare("
                    UPDATE tbl_detail_production_planning
                    SET actual = actual + ?
                    WHERE id=?
                ")->execute([$take, $slot['id']]);

                $remaining -= $take;
            }
        }

        /* =========================
           UPDATE STATUS
        ========================= */

        $pdo->prepare("
            UPDATE tbl_detail_product
            SET is_ng=0
            WHERE serial_no=? AND ref_number=?
        ")->execute([$serial, $ref]);

        $pdo->prepare("
            UPDATE tbl_ng_product
            SET status='IN_MECA'
            WHERE id=?
        ")->execute([$ngId]);

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
        SELECT id, ng_code,ng_name
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
    $ref = $_POST['ref'] ?? '';
    $line = $_POST['line'] ?? 0;

    $operator = $_SESSION['username'] ?? 'operator';

    try {

        $pdo->beginTransaction();

        $q = $pdo->prepare("
        SELECT *
        FROM tbl_active_material
        WHERE part_code=? AND lot_no=? AND line_id=? AND ref_number=?
        LIMIT 1
        ");

        $q->execute([$part, $lot, $line, $ref]);

        $row = $q->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Material tidak ditemukan");
        }

        $remain = intval($row['remain']);

        $pdo->prepare("UPDATE tbl_detail_part
                    SET status = 'IN'
                    WHERE ref_number=? AND part_code=? 
                ")->execute([$ref, $part]);

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


if ($action == 'remove_active_material_all') {

    $line = $_POST['line'] ?? 0;
    $operator = $_SESSION['username'] ?? 'operator';

    try {

        $pdo->beginTransaction();

        // ambil SEMUA material di line
        $q = $pdo->prepare("
            SELECT *
            FROM tbl_active_material
            WHERE line_id=?
        ");
        $q->execute([$line]);

        $rows = $q->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            throw new Exception("Material tidak ditemukan");
        }

        foreach ($rows as $row) {

            $ref  = $row['ref_number'];
            $part = $row['part_code'];

            // balikin status ke IN
            $pdo->prepare("
                UPDATE tbl_detail_part
                SET status = 'IN'
                WHERE ref_number=? AND part_code=?
            ")->execute([$ref, $part]);

            // hapus dari active
            $pdo->prepare("
                DELETE FROM tbl_active_material
                WHERE line_id=?
            ")->execute([$line]);
        }

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
    $ref  = $_POST['ref'] ?? '';
    $line = $_POST['line'] ?? 0;
    $shift = $_POST['shift'] ?? 0;

    $type = $_POST['type'] ?? '';
    $qty  = intval($_POST['qty'] ?? 0);

    $operator = $_SESSION['username'] ?? 'operator';

    if (!$part || !$lot || !$ref) {
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
        WHERE part_code=? AND lot_no=? AND line_id=? AND ref_number=?
        LIMIT 1
        ");

        $q->execute([$part, $lot, $line, $ref]);
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
            WHERE lot_no=? AND ref_number=?
            ")->execute([$qty, $lot, $ref]);
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
            WHERE lot_no=? AND ref_number=?
            ")->execute([$qty, $lot, $ref]);
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
            $shift,
            $line
        ]);

        $pdo->commit();

        echo json_encode(['success' => true, 'shift' => $shift]);
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
    $ref    = $_POST['ref'] ?? '';
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

        // =========================
        // CEK MATERIAL AKTIF
        // =========================
        $q = $pdo->prepare("
            SELECT *
            FROM tbl_active_material
            WHERE part_code=? AND lot_no=? AND line_id=? AND ref_number=?
            LIMIT 1
        ");

        $q->execute([$part, $lot, $line, $ref]);
        $row = $q->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Material tidak ditemukan");
        }

        if ($qty > $row['remain']) {
            throw new Exception("NG qty melebihi remain");
        }

        // =========================
        // INSERT KE tbl_ng_part
        // =========================
        $pdo->prepare("
            INSERT INTO tbl_ng_part
            (ng_id, part_code, lot_no, used_qty, ng_qty, ng_type, ref_part, created_at)
            VALUES (0, ?, ?, ?, ?, ?, ?, NOW())
        ")->execute([
            $part,
            $lot,
            0,
            $qty,
            $reason,
            $ref
        ]);

        // =========================
        // UPDATE ACTIVE MATERIAL
        // =========================
        $pdo->prepare("
            UPDATE tbl_active_material
            SET remain = remain - ?
            WHERE id=?
        ")->execute([$qty, $row['id']]);

        // =========================
        // UPDATE INCOMING
        // =========================
        $pdo->prepare("
            UPDATE tbl_detail_part
            SET remain = remain - ?
            WHERE ref_number=?
        ")->execute([$qty, $ref]);

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

if ($action == 'get_ng_by_part') {

    $partId = $_GET['part_id'] ?? 0;

    $q = $pdo->prepare("
        SELECT nt.ng_code, nt.id,nt.ng_code AS ng_name
        FROM tbl_ng_type_detail tnd
        JOIN tbl_ng_type nt ON nt.id = tnd.type_id
        WHERE tnd.part_id = ?
    ");

    $q->execute([$partId]);

    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
