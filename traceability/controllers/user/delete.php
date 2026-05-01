<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . "/../../helper/checkPassword.php";
require_once __DIR__ . "/../../helper/redirect.php";
require_once __DIR__ . "/../../helper/sanitize.php";
require_once __DIR__ . "/../../helper/handlePdoError.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = isset($_POST['id']) ? (int) sanitize($_POST['id']) : null;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    // =====================
    // BASIC VALIDATION
    // =====================
    if (empty($id) || empty($password) || empty($username)) {

        setAlert(
            'warning',
            "Oops!",
            'Data tidak lengkap.',
            'warning',
            'Coba Lagi'
        );

        return redirect("pages/user/");
    }

    // =====================
    // CHECK PASSWORD LOGIN USER
    // =====================
    $user = checkLogin($pdo, $username, $password);

    if (!$user) {

        setAlert(
            'error',
            "Oops!",
            'Password salah.',
            'danger',
            'Coba Lagi'
        );

        return redirect("pages/user/");
    }

    try {

        // =====================
        // CHECK DATA EXIST
        // =====================
        $stmt = $pdo->prepare("
            SELECT username, nama 
            FROM tbl_karyawan 
            WHERE karyawan_id = :id
        ");
        $stmt->execute([':id' => $id]);

        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$target) {
            throw new Exception("Karyawan tidak ditemukan.");
        }

        // =====================
        // CEGH HAPUS DIRI SENDIRI
        // =====================
        if ($target['username'] === $username) {
            throw new Exception("Anda tidak dapat menghapus akun sendiri.");
        }

        // =====================
        // TRANSACTION
        // =====================
        $pdo->beginTransaction();

        // Hapus dari tbl_user
        $delUser = $pdo->prepare("
            DELETE FROM tbl_user 
            WHERE username = :username
        ");
        $delUser->execute([
            ':username' => $target['username']
        ]);

        // Hapus dari tbl_karyawan
        $delKaryawan = $pdo->prepare("
            DELETE FROM tbl_karyawan 
            WHERE karyawan_id = :id
        ");
        $delKaryawan->execute([
            ':id' => $id
        ]);

        $pdo->commit();

        setAlert(
            'success',
            "Berhasil!",
            'Karyawan berhasil dihapus.',
            'success',
            'Oke'
        );

        redirect("pages/user/");
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, "pages/user/");
    }
}
