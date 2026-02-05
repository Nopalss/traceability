<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/checkPassword.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ================================
    // Ambil input (id = pp_code)
    // ================================
    $ppCode   = isset($_POST['id']) ? sanitize($_POST['id']) : null;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    // ================================
    // Validasi dasar
    // ================================
    if (empty($ppCode) || empty($password) || empty($username)) {
        setAlert(
            'warning',
            'Oops!',
            'Data tidak lengkap.',
            'warning',
            'Coba Lagi'
        );
        return redirect('pages/production_planning/');
    }

    // ================================
    // Verifikasi password user
    // ================================
    $user = checkLogin($pdo, $username, $password);
    if (!$user) {
        setAlert(
            'error',
            'Oops!',
            'Password salah.',
            'danger',
            'Coba Lagi'
        );
        return redirect('pages/production_planning/');
    }

    try {
        // ================================
        // Cek production planning exist
        // ================================
        $stmt = $pdo->prepare(
            "SELECT 1
             FROM tbl_production_planning
             WHERE pp_code = :pp_code
             LIMIT 1"
        );
        $stmt->execute([
            ':pp_code' => $ppCode
        ]);

        if (!$stmt->fetchColumn()) {
            throw new Exception('Data Production Planning tidak ditemukan.');
        }

        // ================================
        // Transaksi delete (hapus semua shift)
        // ================================
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "DELETE FROM tbl_production_planning
             WHERE pp_code = :pp_code"
        );
        $stmt->execute([
            ':pp_code' => $ppCode
        ]);

        $pdo->commit();

        setAlert(
            'success',
            'Berhasil!',
            'Production Planning berhasil dihapus.',
            'success',
            'Oke'
        );

        redirect('pages/production_planning/');
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError(
            $e,
            'pages/production_planning/'
        );
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

        redirect('pages/production_planning/');
    }
}
