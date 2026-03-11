<?php
require_once __DIR__ . "/../../includes/config.php";

header('Content-Type: application/json');

try {

    $search = $_POST['query']['generalSearch'] ?? '';

    $sql = "
        SELECT 
            p.part_code,
            p.part_name,
            COALESCE(SUM(d.remain),0) AS total_qty
        FROM tbl_part p
        LEFT JOIN tbl_detail_part d 
            ON d.part_code = p.part_code
            AND d.status = 'IN'
        WHERE 1=1
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
