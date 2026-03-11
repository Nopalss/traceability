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

        $line_id   = $data['line_id'] ?? null;
        $line_name = sanitize($data['line_name'] ?? '');

        if (empty($line_id) || empty($line_name)) {
            throw new Exception('ID Line dan Nama Line tidak boleh kosong.');
        }

        // password baru = line_name → hash
        $hashedPassword = password_hash($line_name, PASSWORD_BCRYPT);

        $pdo->beginTransaction();

        /**
         * ===========================
         * 1. Ambil line_name lama
         * ===========================
         */
        $stmtOld = $pdo->prepare("SELECT line_name FROM tbl_line WHERE line_id = ?");
        $stmtOld->execute([$line_id]);

        $oldLine = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldLine) {
            throw new Exception('Line tidak ditemukan.');
        }

        $oldLineName = $oldLine['line_name'];

        /**
         * ===========================
         * 2. UPDATE tbl_line
         * ===========================
         */
        $sqlLine = "UPDATE tbl_line 
                    SET line_name = :line_name
                    WHERE line_id = :line_id";

        $stmtLine = $pdo->prepare($sqlLine);
        $stmtLine->execute([
            ':line_name' => $line_name,
            ':line_id'   => $line_id
        ]);

        /**
         * ===========================
         * 3. UPDATE tbl_user
         * ===========================
         */
        $sqlUser = "UPDATE tbl_user
                    SET username = :new_username,
                        password = :password
                    WHERE username = :old_username";

        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([
            ':new_username' => $line_name,
            ':password'     => $hashedPassword,
            ':old_username' => $oldLineName
        ]);

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = "Line  berhasil diperbarui.";
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = 'Database Error: ' . $e->getMessage();
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
exit;
