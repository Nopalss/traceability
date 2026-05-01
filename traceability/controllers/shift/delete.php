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

        return redirect("pages/shift/");
    }

    // =====================
    // CHECK PASSWORD USER
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

        return redirect("pages/shift/");
    }

    try {

        // =====================
        // CHECK SHIFT EXIST
        // =====================
        $stmt = $pdo->prepare("SELECT shift FROM tbl_shift WHERE shift_id = :id");
        $stmt->execute([':id' => $id]);

        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$target) {
            throw new Exception("Shift tidak ditemukan.");
        }

        // =====================
        // TRANSACTION
        // =====================
        $pdo->beginTransaction();

        // kalau nanti ada relasi:
        // DELETE FROM tbl_xxx WHERE shift_id = :id

        $stmt = $pdo->prepare("DELETE FROM tbl_shift WHERE shift_id = :id");
        $stmt->execute([':id' => $id]);

        $pdo->commit();

        setAlert(
            'success',
            "Berhasil!",
            'Shift berhasil dihapus.',
            'success',
            'Oke'
        );

        redirect("pages/shift/");
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, "pages/shift/");
    }
}
