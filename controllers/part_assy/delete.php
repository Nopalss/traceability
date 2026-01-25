<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/checkPassword.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ================================
    // Ambil input
    // ================================
    $partAssy = isset($_POST['id']) ? sanitize($_POST['id']) : null;
    $username = $_SESSION['username'] ?? null;
    $password = trim($_POST['password'] ?? '');

    // ================================
    // Validasi dasar
    // ================================
    if (empty($partAssy) || empty($password) || empty($username)) {
        setAlert(
            'warning',
            'Oops!',
            'Data tidak lengkap.',
            'warning',
            'Coba Lagi'
        );
        return redirect('pages/part_assy/');
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
        return redirect('pages/part_assy/');
    }

    try {
        // ================================
        // Cek Part Assy exist
        // ================================
        $stmt = $pdo->prepare(
            "SELECT 1 FROM tbl_part_assy WHERE part_assy = :part_assy LIMIT 1"
        );
        $stmt->execute([
            ':part_assy' => $partAssy
        ]);

        if (!$stmt->fetchColumn()) {
            throw new Exception('Data Part Assy tidak ditemukan.');
        }

        // ================================
        // Transaksi delete (hapus seluruh BOM)
        // ================================
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "DELETE FROM tbl_part_assy WHERE part_assy = :part_assy"
        );
        $stmt->execute([
            ':part_assy' => $partAssy
        ]);

        $pdo->commit();

        setAlert(
            'success',
            'Berhasil!',
            'Part Assy berhasil dihapus.',
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
