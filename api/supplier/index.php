<?php
require_once __DIR__ . "/../../includes/config.php";

try {
    $search = $_POST['query']['generalSearch'] ?? '';
    $role = $_POST['query']['role'] ?? '';


    $sql = "SELECT * FROM tbl_supplier WHERE 1=1 AND status = 'supplier' ";

    $params = [];

    if (!empty($search)) {
        $sql .= " AND (
                        name_supplier LIKE :search
                    )";
        $params[':search'] = "%$search%";
    }

    $sql .=  "ORDER BY id_supplier ASC";
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
