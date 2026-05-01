<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . "/../../helper/checkPassword.php";
require_once __DIR__ . "/../../helper/redirect.php";
require_once __DIR__ . "/../../helper/sanitize.php";
require_once __DIR__ . "/../../helper/handlePdoError.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    // =====================
    // BASIC VALIDATION
    // =====================
    if ($id <= 0 || empty($password) || empty($username)) {

        setAlert(
            'warning',
            "Oops!",
            'Data tidak lengkap.',
            'warning',
            'Coba Lagi'
        );

        return redirect("pages/customer/");
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

        return redirect("pages/customer/");
    }

    try {

        // =====================
        // CHECK CUSTOMER EXIST
        // =====================
        $stmt = $pdo->prepare("
            SELECT name_supplier 
            FROM tbl_supplier 
            WHERE id_supplier = :id
            AND status = 'customer'
        ");

        $stmt->execute([':id' => $id]);

        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$target) {
            throw new Exception("Customer tidak ditemukan.");
        }

        $pdo->beginTransaction();

        // =====================
        // DELETE CUSTOMER
        // =====================
        $stmt = $pdo->prepare("
            DELETE FROM tbl_supplier 
            WHERE id_supplier = :id
            AND status = 'customer'
        ");

        $stmt->execute([':id' => $id]);

        $pdo->commit();

        setAlert(
            'success',
            "Berhasil!",
            "Customer '{$target['name_supplier']}' berhasil dihapus.",
            'success',
            'Oke'
        );

        redirect("pages/customer/");
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        setAlert(
            'error',
            "Gagal!",
            $e->getMessage(),
            'danger',
            'Coba Lagi'
        );

        redirect("pages/customer/");
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, "pages/customer/");
    }
}
