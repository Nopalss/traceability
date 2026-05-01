<?php
require_once __DIR__ . "/../../includes/config.php";

try {
    $search = $_POST['query']['generalSearch'] ?? '';
    $role = $_POST['query']['role'] ?? '';

    $sql = "SELECT * FROM tbl_role 
        WHERE role_id != 1
        ORDER BY role_id ASC";

    $params = [];


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
