<?php
require_once __DIR__ . "/../../includes/config.php";

header('Content-Type: application/json');

try {

    $search = $_POST['query']['generalSearch'] ?? '';

    $sql = "
        SELECT 
            p.part_code AS product_code,
            p.part_name,
            COALESCE(COUNT(dp.id), 0) AS total_qty
        FROM tbl_part p
        LEFT JOIN tbl_detail_product dp
            ON dp.product_code = p.part_code
            AND dp.status = 'in'
        WHERE p.status_assy = 1
    ";

    $params = [];

    if (!empty($search)) {
        $sql .= " AND (
                        p.part_code LIKE :search
                        OR p.part_name LIKE :search
                  )";
        $params[':search'] = "%$search%";
    }

    $sql .= "
        GROUP BY p.part_code, p.part_name
        ORDER BY p.part_code ASC
    ";

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
