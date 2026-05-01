<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/checkPassword.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================================
       AMBIL INPUT
    ================================ */
    $id       = isset($_POST['id']) ? sanitize($_POST['id']) : null;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    /* ================================
       VALIDASI
    ================================ */
    if (empty($id) || empty($password) || empty($username)) {
        setAlert('warning', 'Oops!', 'Data tidak lengkap.', 'warning', 'Coba Lagi');
        return redirect('pages/part_assy/');
    }

    /* ================================
       CEK PASSWORD
    ================================ */
    $user = checkLogin($pdo, $username, $password);
    if (!$user) {
        setAlert('error', 'Oops!', 'Password salah.', 'danger', 'Coba Lagi');
        return redirect('pages/part_assy/');
    }

    try {

        /* ================================
           GET DATA MODEL + ASSY
        ================================ */
        $stmt = $pdo->prepare("
            SELECT part_code 
            FROM tbl_model 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $assy = $stmt->fetchColumn();

        if (!$assy) {
            throw new Exception('Data tidak ditemukan.');
        }

        $pdo->beginTransaction();

        /* ================================
           1. DELETE BOM DETAIL
        ================================ */
        $stmt = $pdo->prepare("
            DELETE FROM tbl_part_assy 
            WHERE part_assy = ?
        ");
        $stmt->execute([$assy]);

        /* ================================
           2. DELETE MODEL
        ================================ */
        $stmt = $pdo->prepare("
            DELETE FROM tbl_model 
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        /* ================================
           3. DELETE PART ASSY (tbl_part)
        ================================ */
        $stmt = $pdo->prepare("
            DELETE FROM tbl_part 
            WHERE part_code = ? 
            AND status_assy = 1
        ");
        $stmt->execute([$assy]);

        $pdo->commit();

        setAlert(
            'success',
            'Berhasil!',
            'Data Part Assy berhasil dihapus.',
            'success',
            'Oke'
        );

        redirect('pages/part_assy/');
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, 'pages/part_assy/');
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        setAlert(
            'error',
            'Oops!',
            $e->getMessage(),
            'danger',
            'Kembali'
        );

        redirect('pages/part_assy/');
    }
}
