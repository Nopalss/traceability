<?php
require_once __DIR__ . "/../../../includes/config.php";

try {
    $search = $_POST['query']['generalSearch'] ?? '';
    $role = $_POST['query']['role'] ?? '';



    $sql = "
    SELECT 
        d.ref_number,
        d.part_code,
        d.qty,
        d.incoming_date,
        d.status,
        d.lot_no,
        d.remarks,
        p.part_name,
        p.supplier
    FROM tbl_detail_part d
    JOIN tbl_part p 
        ON p.part_code = d.part_code
    WHERE 1=1
    ORDER BY d.incoming_date DESC

";
    $params = [];

    // if (!empty($search)) {
    //     $sql .= " AND (
    //                     u.username LIKE :search
    //                     OR u.role LIKE :search
    //                     OR COALESCE(t.name, a.name) LIKE :search
    //                     OR COALESCE(t.phone, a.phone) LIKE :search
    //                 )";
    //     $params[':search'] = "%$search%";
    // }

    // if (!empty($role)) {
    //     $sql .= " AND u.role LIKE :role";
    //     $params[':role'] = $role;
    // }


    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "data" => $users
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ]);
}
