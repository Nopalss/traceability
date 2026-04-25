<?php
require_once __DIR__ . '/../../includes/config.php';

$action = $_GET['action'] ?? '';

if ($action == 'load_report') {

    $line = $_GET['line'] ?? '';
    $date = $_GET['date'] ?? '';
    $search = $_GET['search'] ?? '';

    // ================= NG =================
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

    // ================= LOSS =================
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

    echo json_encode([
        'ng' => $ng,
        'loss' => $loss
    ]);
}

if ($action == 'summary') {

    $page = intval($_GET['page'] ?? 1);
    $search = $_GET['search'] ?? '';

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where = "";

    if ($search != '') {
        $where = "WHERE p.part_name LIKE '%$search%' 
                  OR s.name_supplier LIKE '%$search%'
                  OR p.part_code LIKE '%$search%'";
    }

    $data = $pdo->query("
        SELECT 
            p.part_code,
            p.part_name,
            s.name_supplier,

            COALESCE(ng.total_ng,0) as total_ng,
            COALESCE(ls.total_loss,0) as total_loss

        FROM tbl_part p
        LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier

        LEFT JOIN (
            SELECT part_code, SUM(ng_qty) total_ng
            FROM tbl_ng_part
            GROUP BY part_code
        ) ng ON ng.part_code = p.part_code

        LEFT JOIN (
            SELECT part_code, SUM(lost_qty) total_loss
            FROM tbl_material_loss
            GROUP BY part_code
        ) ls ON ls.part_code = p.part_code

        $where

        ORDER BY (COALESCE(ng.total_ng,0) + COALESCE(ls.total_loss,0)) DESC

        LIMIT $limit OFFSET $offset
    ")->fetchAll(PDO::FETCH_ASSOC);

    $total = $pdo->query("SELECT COUNT(*) as total FROM tbl_part")->fetch()['total'];

    echo json_encode([
        'data' => $data,
        'total' => $total,
        'start' => $offset + 1,
        'end' => $offset + count($data)
    ]);
}
if ($action == 'detail') {

    $part = $_GET['part'];
    $page = intval($_GET['page'] ?? 1);
    $date = $_GET['date'] ?? '';
    $type = $_GET['type'] ?? 'all';
    $search = $_GET['search'] ?? '';

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $data = [];

    // ================= NG =================
    if ($type == 'all' || $type == 'ng') {

        $where = "WHERE dp.part_code = '$part'";

        if ($date != '') {
            $where .= " AND DATE(ng.created_at) = '$date'";
        }

        if ($search != '') {
            $where .= " AND (ng.ref_part LIKE '%$search%' 
                         OR dp.lot_no LIKE '%$search%' 
                         OR ng.ng_type LIKE '%$search%')";
        }

        $ng = $pdo->query("
            SELECT 
                'NG' as type,
                ng.ref_part as ref,
                dp.lot_no as lot,
                dp.incoming_date as income,
                ng.ng_qty as qty,
                ng.ng_type as reason,
                am.line_id as line,
                l.line_name as line_name,
                ng.created_at as date
            FROM tbl_ng_part ng
            LEFT JOIN tbl_detail_part dp 
                ON dp.ref_number = ng.ref_part
            LEFT JOIN tbl_active_material am 
                ON am.ref_number = ng.ref_part
            LEFT JOIN tbl_line l 
                ON l.line_id = am.line_id
            $where
        ")->fetchAll(PDO::FETCH_ASSOC);

        $data = array_merge($data, $ng);
    }

    // ================= LOSS =================
    if ($type == 'all' || $type == 'loss') {

        $where = "WHERE dp.part_code = '$part'";

        if ($date != '') {
            $where .= " AND DATE(ml.created_at) = '$date'";
        }

        if ($search != '') {
            $where .= " AND (ml.ref_number LIKE '%$search%' 
                         OR dp.lot_no LIKE '%$search%' 
                         OR ml.reason LIKE '%$search%')";
        }

        $loss = $pdo->query("
            SELECT 
                'LOSS' as type,
                ml.ref_number as ref,
                dp.lot_no as lot,
                dp.incoming_date as income,
                ml.lost_qty as qty,
                ml.reason,
                l.line_name as line_name,
                ml.line_id as line,
                ml.created_at as date
            FROM tbl_material_loss ml
            LEFT JOIN tbl_detail_part dp 
                ON dp.ref_number = ml.ref_number
            LEFT JOIN tbl_line l 
                ON l.line_id = ml.line_id
            $where
        ")->fetchAll(PDO::FETCH_ASSOC);

        $data = array_merge($data, $loss);
    }

    // SORT
    usort($data, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    $total = count($data);
    $paged = array_slice($data, $offset, $limit);

    echo json_encode([
        'data' => $paged,
        'total' => $total,
        'start' => $offset + 1,
        'end' => $offset + count($paged)
    ]);
}
