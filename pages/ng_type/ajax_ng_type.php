<?php
require '../../includes/config.php';

$action = $_POST['action'] ?? '';

/* ==============================
INSERT NG TYPE
============================== */

if ($action == 'insert') {

    $code   = trim($_POST['code'] ?? '');
    $name   = trim($_POST['name'] ?? '');
    $status = $_POST['status'] ?? 'ACTIVE';

    if (!$code || !$name) {
        echo json_encode([
            'error' => true,
            'message' => 'Title dan Description wajib diisi'
        ]);
        exit;
    }

    $q = $pdo->prepare("
        INSERT INTO tbl_ng_type
        (ng_code, ng_name, status)
        VALUES (?,?,?)
    ");

    $q->execute([$code, $name, $status]);

    echo json_encode([
        'success' => true
    ]);
    exit;
}


/* ==============================
UPDATE NG TYPE
============================== */

if ($action == 'update') {

    $id     = $_POST['id'] ?? 0;
    $code   = trim($_POST['code'] ?? '');
    $name   = trim($_POST['name'] ?? '');
    $status = $_POST['status'] ?? 'ACTIVE';

    if (!$id) {
        echo json_encode([
            'error' => true,
            'message' => 'ID tidak valid'
        ]);
        exit;
    }

    $q = $pdo->prepare("
        UPDATE tbl_ng_type
        SET 
            ng_code = ?,
            ng_name = ?,
            status  = ?
        WHERE id = ?
    ");

    $q->execute([$code, $name, $status, $id]);

    echo json_encode([
        'success' => true
    ]);
    exit;
}


/* ==============================
DELETE NG TYPE
============================== */

if ($action == 'delete') {

    $id = $_POST['id'] ?? 0;

    if (!$id) {
        echo json_encode([
            'error' => true,
            'message' => 'ID tidak valid'
        ]);
        exit;
    }

    $pdo->prepare("
        DELETE FROM tbl_ng_type
        WHERE id = ?
    ")->execute([$id]);

    echo json_encode([
        'success' => true
    ]);
    exit;
}
