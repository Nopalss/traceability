<?php
require_once __DIR__ . '/../../includes/config.php';

$action = $_GET['action'] ?? '';

/* =====================================================
   LOAD REPORT (TIDAK DIUBAH)
===================================================== */
if ($action == 'load_report') {

    $line = $_GET['line'] ?? '';
    $date = $_GET['date'] ?? '';
    $search = $_GET['search'] ?? '';

    $whereNg = "WHERE 1=1";

    if ($date != '') {
        $whereNg .= " AND DATE(ng.created_at) = '$date'";
    }

    if ($line != '') {
        $whereNg .= " AND am.line_id = '$line'";
    }

    if ($search != '') {
        $whereNg .= " AND (p.part_name LIKE '%$search%' OR s.name_supplier LIKE '%$search%')";
    }

    $ng = $pdo->query("
        SELECT 
            ng.part_code,
            p.part_name,
            s.name_supplier,
            dp.incoming_date,
            ng.lot_no,
            ng.ng_qty,
            ng.ng_type,
            ng.created_at
        FROM tbl_ng_part ng
        LEFT JOIN tbl_part p ON p.part_code = ng.part_code
        LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier
        LEFT JOIN tbl_detail_part dp ON dp.ref_number = ng.ref_part
        LEFT JOIN tbl_active_material am ON am.ref_number = ng.ref_part
        $whereNg
        ORDER BY ng.created_at DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);

    $whereLoss = "WHERE 1=1";

    if ($line != '') {
        $whereLoss .= " AND ml.line_id = '$line'";
    }

    if ($date != '') {
        $whereLoss .= " AND DATE(ml.created_at) = '$date'";
    }

    if ($search != '') {
        $whereLoss .= " AND (p.part_name LIKE '%$search%' OR s.name_supplier LIKE '%$search%')";
    }

    $loss = $pdo->query("
        SELECT 
            ml.part_code,
            p.part_name,
            s.name_supplier,
            dp.incoming_date,
            ml.lost_qty,
            ml.reason,
            ml.line_id,
            ml.created_at
        FROM tbl_material_loss ml
        LEFT JOIN tbl_part p ON p.part_code = ml.part_code
        LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier
        LEFT JOIN tbl_detail_part dp ON dp.ref_number = ml.ref_number
        $whereLoss
        ORDER BY ml.created_at DESC
        LIMIT 100
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['ng' => $ng, 'loss' => $loss]);
}


/* =====================================================
   SUMMARY (DUAL MODE)
   - TANPA part → LIST PAGE
   - DENGAN part → DETAIL PAGE
===================================================== */
if ($action == 'summary') {

    $part   = $_GET['part'] ?? '';
    $page   = intval($_GET['page'] ?? 1);
    $limit  = 10;
    $offset = ($page - 1) * $limit;
    $search = $_GET['search'] ?? '';
    $from   = $_GET['from'] ?? '';
    $to     = $_GET['to'] ?? '';

    /* =====================================================
       🔥 MODE 1: LIST (material_report)
    ===================================================== */
    if ($part == '') {

        $where = "WHERE 1=1";

        if ($search != '') {
            $where .= " AND (p.part_code LIKE '%$search%' OR p.part_name LIKE '%$search%' OR s.name_supplier LIKE '%$search%')";
        }

        $data = $pdo->query("
            SELECT 
                p.part_code,
                p.part_name,
                s.name_supplier,
                COALESCE(ng.total_ng, 0) as total_ng,
                COALESCE(ls.total_loss, 0) as total_loss
            FROM tbl_part p
            LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier

            LEFT JOIN (
                SELECT part_code, SUM(ng_qty) as total_ng
                FROM tbl_ng_part
                GROUP BY part_code
            ) ng ON ng.part_code = p.part_code

            LEFT JOIN (
                SELECT part_code, SUM(lost_qty) as total_loss
                FROM tbl_material_loss
                GROUP BY part_code
            ) ls ON ls.part_code = p.part_code

            $where
            ORDER BY (COALESCE(ng.total_ng,0) + COALESCE(ls.total_loss,0)) DESC
            LIMIT $offset, $limit
        ")->fetchAll(PDO::FETCH_ASSOC);

        $total = $pdo->query("
            SELECT COUNT(*) 
            FROM tbl_part p
            LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier
            $where
        ")->fetchColumn();

        echo json_encode([
            'data'  => $data,
            'total' => (int)$total,
            'start' => $offset + 1,
            'end'   => $offset + count($data)
        ]);

        exit;
    }

    /* =====================================================
       🔥 MODE 2: DETAIL (NG TYPE BREAKDOWN)
    ===================================================== */

    $whereDateNG = "";
    $whereDateLoss = "";

    if ($from && $to) {
        $whereDateNG = "AND DATE(ng.created_at) BETWEEN '$from' AND '$to'";
        $whereDateLoss = "AND DATE(ml.created_at) BETWEEN '$from' AND '$to'";
    }

    $ng = $pdo->query("
    SELECT 
        COALESCE(t.ng_code, CONCAT('TYPE-', ng.ng_type)) as ng_name,
        SUM(ng.ng_qty) as qty
    FROM tbl_ng_part ng
    LEFT JOIN tbl_ng_type t 
        ON t.id = ng.ng_type
    WHERE ng.part_code = '$part'
    $whereDateNG
    GROUP BY ng.ng_type
    ORDER BY qty DESC
")->fetchAll(PDO::FETCH_ASSOC);

    $loss = $pdo->query("
        SELECT SUM(lost_qty) as total
        FROM tbl_material_loss ml
        WHERE ml.part_code = '$part'
        AND ml.reason LIKE '%SUB%'
        $whereDateLoss
    ")->fetch()['total'] ?? 0;

    $over = $pdo->query("
        SELECT SUM(lost_qty) as total
        FROM tbl_material_loss ml
        WHERE ml.part_code = '$part'
        AND ml.reason LIKE '%ADD%'
        $whereDateLoss
    ")->fetch()['total'] ?? 0;

    echo json_encode([
        'ng_detail' => $ng,
        'total_loss' => (int)$loss,
        'total_over' => (int)$over
    ]);
}


/* =====================================================
   DETAIL TABLE (TIDAK DIUBAH)
===================================================== */
if ($action == 'detail') {

    $part = $_GET['part'];
    $page = intval($_GET['page'] ?? 1);
    $from = $_GET['from'] ?? '';
    $to   = $_GET['to'] ?? '';
    $type = $_GET['type'] ?? 'all';

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $data = [];

    if ($type == 'all' || $type == 'ng') {

        $where = "WHERE dp.part_code = '$part'";

        if ($from && $to) {
            $where .= " AND DATE(ng.created_at) BETWEEN '$from' AND '$to'";
        }

        $ng = $pdo->query("
            SELECT 
                'NG' as type,
                ng.ref_part as ref,
                dp.lot_no as lot,
                dp.incoming_date as income,
                ng.ng_qty as qty,
                t.ng_code as reason,
                l.line_name,
                am.line_id,
                sh.shift as shift,
                ng.created_at as date
            FROM tbl_ng_part ng
            LEFT JOIN tbl_detail_part dp ON dp.ref_number = ng.ref_part
            LEFT JOIN tbl_active_material am ON am.ref_number = ng.ref_part
            LEFT JOIN tbl_line l ON l.line_id = am.line_id
            LEFT JOIN tbl_shift sh ON sh.shift_id = ng.shift
            LEFT JOIN tbl_ng_type t ON t.id = ng.ng_type
            $where
        ")->fetchAll(PDO::FETCH_ASSOC);

        $data = array_merge($data, $ng);
    }

    if ($type == 'all' || $type == 'loss') {

        $where = "WHERE dp.part_code = '$part'";

        if ($from && $to) {
            $where .= " AND DATE(ml.created_at) BETWEEN '$from' AND '$to'";
        }

        $loss = $pdo->query("
            SELECT 
                'LOSS' as type,
                ml.ref_number as ref,
                dp.lot_no as lot,
                dp.incoming_date as income,
                ml.lost_qty as qty,
                ml.reason,
                l.line_name,
                ml.line_id,
                sh.shift as shift,
                ml.created_at as date
            FROM tbl_material_loss ml
            LEFT JOIN tbl_detail_part dp ON dp.ref_number = ml.ref_number
            LEFT JOIN tbl_line l ON l.line_id = ml.line_id
            LEFT JOIN tbl_shift sh ON sh.shift_id = ml.shift
            $where
        ")->fetchAll(PDO::FETCH_ASSOC);

        $data = array_merge($data, $loss);
    }

    usort($data, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

    $total = count($data);
    $paged = array_slice($data, $offset, $limit);

    echo json_encode([
        'data' => $paged,
        'total' => $total,
        'start' => $offset + 1,
        'end' => $offset + count($paged)
    ]);
}
