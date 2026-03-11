<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . "/../../helper/checkPassword.php";
require_once __DIR__ . "/../../helper/redirect.php";
require_once __DIR__ . "/../../helper/sanitize.php";
require_once __DIR__ . "/../../helper/handlePdoError.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = isset($_POST['id']) ? sanitize($_POST['id']) : null;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    if (empty($id) || empty($password) || empty($username)) {
        setAlert('warning', "Oops!", 'Data tidak lengkap.', 'warning', 'Coba Lagi');
        return redirect("pages/line_setting/");
    }

    // Validasi password user yg sedang login
    $user = checkLogin($pdo, $username, $password);
    if (!$user) {
        setAlert('error', "Oops!", 'Password salah.', 'danger', 'Coba Lagi');
        return redirect("pages/line_setting/");
    }

    try {

        // Ambil line_name dulu
        $stmt = $pdo->prepare("SELECT line_name FROM tbl_line WHERE line_id = :id");
        $stmt->execute([':id' => $id]);
        $targetLine = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$targetLine) {
            throw new Exception("Line dengan ID tersebut tidak ditemukan.");
        }

        $lineName = $targetLine['line_name'];

        $pdo->beginTransaction();

        /**
         * =========================
         * 1. DELETE tbl_user
         * =========================
         */
        $stmtUser = $pdo->prepare("DELETE FROM tbl_user WHERE username = :username");
        $stmtUser->execute([':username' => $lineName]);

        /**
         * =========================
         * 2. DELETE tbl_line
         * =========================
         */
        $stmtLine = $pdo->prepare("DELETE FROM tbl_line WHERE line_id = :id");
        $stmtLine->execute([':id' => $id]);

        $pdo->commit();

        setAlert(
            'success',
            "Berhasil!",
            'Line dan akun login berhasil dihapus.',
            'success',
            'Oke'
        );

        redirect("pages/line_setting/");
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, "pages/line_setting/");
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        setAlert('error', "Error!", $e->getMessage(), 'danger', 'Oke');
        redirect("pages/line_setting/");
    }
}
