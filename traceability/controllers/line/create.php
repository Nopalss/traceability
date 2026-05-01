<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';

$response = [
    'success' => false,
    'message' => 'Invalid request.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        $line_name = sanitize($data['line_name'] ?? '');

        if (empty($line_name)) {
            $response['message'] = 'Nama line tidak boleh kosong.';
        } else {

            // password = line_name → HASH
            $hashedPassword = password_hash($line_name, PASSWORD_BCRYPT);

            $pdo->beginTransaction();

            /**
             * =============================
             * 1. INSERT KE tbl_line
             * =============================
             */
            $sqlLine = "INSERT INTO tbl_line (line_name, created_by)
                        VALUES (:line_name, :created_by)";

            $stmtLine = $pdo->prepare($sqlLine);
            $stmtLine->execute([
                ':line_name'  => $line_name,
                ':created_by' => $_SESSION['username']
            ]);

            /**
             * =============================
             * 2. INSERT KE tbl_user
             * =============================
             */
            $sqlUser = "INSERT INTO tbl_user (username, password, rule)
                        VALUES (:username, :password, :rule)";

            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([
                ':username' => $line_name,
                ':password' => $hashedPassword,
                ':rule'     => 'line'
            ]);

            $pdo->commit();

            $response['success'] = true;
            $response['message'] = "Line '$line_name' berhasil dibuat.";
        }
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = 'Database Error: ' . $e->getMessage();
    } catch (Exception $e) {

        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
