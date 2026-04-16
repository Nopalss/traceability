<?php
require '../../includes/config.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

/* ==============================
HELPER
============================== */
function json_out($data)
{
    echo json_encode($data);
    exit;
}

/* ==============================
INSERT NG TYPE + PART
============================== */
if ($action == 'insert') {

    $code   = trim($_POST['code'] ?? '');
    $status = $_POST['status'] ?? 'ACTIVE';
    $parts  = $_POST['parts'] ?? [];

    if (!$code) {
        json_out([
            'error' => true,
            'message' => 'Title dan Description wajib diisi'
        ]);
    }

    try {

        $pdo->beginTransaction();

        /* insert ng type */
        $q = $pdo->prepare("
            INSERT INTO tbl_ng_type (ng_code,  status)
            VALUES (?,?)
        ");
        $q->execute([$code, $status]);

        $type_id = $pdo->lastInsertId();

        /* insert detail part */
        if (!empty($parts)) {

            $stmt = $pdo->prepare("
                INSERT INTO tbl_ng_type_detail (type_id, part_id)
                VALUES (?,?)
            ");

            foreach ($parts as $p) {
                if ($p) {
                    $stmt->execute([$type_id, $p]);
                }
            }
        }

        $pdo->commit();

        json_out(['success' => true]);
    } catch (Exception $e) {

        $pdo->rollBack();

        json_out([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}


/* ==============================
UPDATE NG TYPE + PART
============================== */
if ($action == 'update') {

    $id     = $_POST['id'] ?? 0;
    $code   = trim($_POST['code'] ?? '');
    $status = $_POST['status'] ?? 'ACTIVE';
    $parts  = $_POST['parts'] ?? [];

    if (!$id) {
        json_out([
            'error' => true,
            'message' => 'ID tidak valid'
        ]);
    }

    try {

        $pdo->beginTransaction();

        /* update ng */
        $pdo->prepare("
            UPDATE tbl_ng_type
            SET ng_code = ?,  status = ?
            WHERE id = ?
        ")->execute([$code,  $status, $id]);

        /* hapus relasi lama */
        $pdo->prepare("
            DELETE FROM tbl_ng_type_detail
            WHERE type_id = ?
        ")->execute([$id]);

        /* insert ulang */
        if (!empty($parts)) {

            $stmt = $pdo->prepare("
                INSERT INTO tbl_ng_type_detail (type_id, part_id)
                VALUES (?,?)
            ");

            foreach ($parts as $p) {
                if ($p) {
                    $stmt->execute([$id, $p]);
                }
            }
        }

        $pdo->commit();

        json_out(['success' => true]);
    } catch (Exception $e) {

        $pdo->rollBack();

        json_out([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}


/* ==============================
DELETE NG TYPE + RELASI
============================== */
if ($action == 'delete') {

    $id = $_POST['id'] ?? 0;

    if (!$id) {
        json_out([
            'error' => true,
            'message' => 'ID tidak valid'
        ]);
    }

    try {

        $pdo->beginTransaction();

        /* hapus detail dulu */
        $pdo->prepare("
            DELETE FROM tbl_ng_type_detail
            WHERE type_id = ?
        ")->execute([$id]);

        /* hapus main */
        $pdo->prepare("
            DELETE FROM tbl_ng_type
            WHERE id = ?
        ")->execute([$id]);

        $pdo->commit();

        json_out(['success' => true]);
    } catch (Exception $e) {

        $pdo->rollBack();

        json_out([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}


/* ==============================
GET PART BY TYPE (EDIT)
============================== */
if ($action == 'get_parts') {

    $id = $_GET['id'] ?? 0;

    if (!$id) {
        json_out([]);
    }

    $q = $pdo->prepare("
        SELECT part_id
        FROM tbl_ng_type_detail
        WHERE type_id = ?
    ");

    $q->execute([$id]);

    $data = $q->fetchAll(PDO::FETCH_COLUMN);

    json_out($data);
}


/* ==============================
DEFAULT
============================== */
json_out([
    'error' => true,
    'message' => 'Invalid action'
]);
