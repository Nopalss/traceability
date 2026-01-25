<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/checkPassword.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Ambil input ---
    $part_code  = isset($_POST['id']) ? sanitize($_POST['id']) : null;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    // --- Validasi dasar ---
    if (empty($part_code) || empty($password) || empty($username)) {
        setAlert(
            'warning',
            'Oops!',
            'Data tidak lengkap.',
            'warning',
            'Coba Lagi'
        );
        return redirect('pages/part_register/');
    }

    // --- Verifikasi password user ---
    $user = checkLogin($pdo, $username, $password);
    if (!$user) {
        setAlert(
            'error',
            'Oops!',
            'Password salah.',
            'danger',
            'Coba Lagi'
        );
        return redirect('pages/part_register/');
    }

    try {
        // --- Cek part exist ---
        $stmt = $pdo->prepare(
            "SELECT part_code FROM tbl_part WHERE part_code = :part_code LIMIT 1"
        );
        $stmt->execute([':part_code' => $part_code]);
        $part = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$part) {
            throw new Exception('Data part tidak ditemukan.');
        }

        // --- Transaksi delete ---
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "DELETE FROM tbl_part WHERE part_code = :part_code"
        );
        $stmt->execute([':part_code' => $part_code]);

        $pdo->commit();

        setAlert(
            'success',
            'Berhasil!',
            'Data part berhasil dihapus.',
            'success',
            'Oke'
        );

        redirect('pages/part_register/');
    } catch (PDOException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        handlePdoError($e, 'pages/part_register');
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

        redirect('pages/part_register/');
    }
}
