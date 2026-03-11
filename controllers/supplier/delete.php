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

        return redirect("pages/supplier/");
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

        return redirect("pages/supplier/");
    }

    try {

        // =====================
        // CHECK SUPPLIER EXIST
        // =====================
        $stmt = $pdo->prepare("
            SELECT name_supplier 
            FROM tbl_supplier 
            WHERE id_supplier = :id
            AND status = 'supplier'
        ");

        $stmt->execute([':id' => $id]);

        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$target) {
            throw new Exception("Supplier tidak ditemukan.");
        }

        $pdo->beginTransaction();

        /**
         * OPTIONAL RELATION CHECK
         * Cek apakah supplier dipakai di tbl_part
         */
        $checkRel = $pdo->prepare("
            SELECT id_part 
            FROM tbl_part 
            WHERE supplier = :id 
            LIMIT 1
        ");

        $checkRel->execute([':id' => $id]);

        if ($checkRel->fetch()) {
            throw new Exception("Supplier tidak bisa dihapus karena masih digunakan pada data Part.");
        }

        // =====================
        // DELETE SUPPLIER
        // =====================
        $stmt = $pdo->prepare("
            DELETE FROM tbl_supplier 
            WHERE id_supplier = :id
            AND status = 'supplier'
        ");

        $stmt->execute([':id' => $id]);

        $pdo->commit();

        setAlert(
            'success',
            "Berhasil!",
            "Supplier '{$target['name_supplier']}' berhasil dihapus.",
            'success',
            'Oke'
        );

        redirect("pages/supplier/");
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

        redirect("pages/supplier/");
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, "pages/supplier/");
    }
}
