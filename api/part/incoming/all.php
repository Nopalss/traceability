<?php
require_once __DIR__ . "/../../../includes/config.php";

header('Content-Type: application/json');

try {

    // =============================
    // GET PARAM
    // =============================
    $search     = $_POST['query']['generalSearch'] ?? '';
    $supplier   = $_POST['supplier'] ?? '';
    $date_from  = $_POST['date_from'] ?? '';
    $date_to    = $_POST['date_to'] ?? '';

    // =============================
    // BASE QUERY
    // =============================
    $sql = "
        SELECT 
        dp.lot_no,
            dp.ref_number,
            dp.part_code,
            p.part_name,
            dp.qty,
            dp.remain,
            dp.incoming_date,
            p.supplier,
            s.name_supplier AS supplier_name
        FROM tbl_detail_part dp
        LEFT JOIN tbl_part p 
            ON dp.part_code = p.part_code
        LEFT JOIN tbl_supplier s 
            ON p.supplier = s.id_supplier
        WHERE 1=1
    ";

    $params = [];

    // =============================
    // FILTER SUPPLIER
    // =============================
    if (!empty($supplier)) {
        $sql .= " AND p.supplier = :supplier ";
        $params[':supplier'] = $supplier;
    }

    // =============================
    // FILTER DATE FROM
    // =============================
    if (!empty($date_from)) {
        $sql .= " AND DATE(dp.incoming_date) >= :date_from ";
        $params[':date_from'] = $date_from;
    }

    // =============================
    // FILTER DATE TO
    // =============================
    if (!empty($date_to)) {
        $sql .= " AND DATE(dp.incoming_date) <= :date_to ";
        $params[':date_to'] = $date_to;
    }

    // =============================
    // SEARCH GLOBAL
    // =============================
    if (!empty($search)) {
        $sql .= " AND (
            dp.part_code LIKE :search OR
            p.part_name LIKE :search OR
            s.name_supplier LIKE :search
        ) ";
        $params[':search'] = "%$search%";
    }

    // =============================
    // ORDER + LIMIT
    // =============================
    $sql .= " ORDER BY dp.incoming_date DESC LIMIT 100";

    // =============================
    // EXECUTE
    // =============================
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "data" => $data
    ]);
} catch (PDOException $e) {

    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ]);
}
