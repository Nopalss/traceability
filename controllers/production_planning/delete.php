<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/checkPassword.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/production_planning/');
}

// ================================
// INPUT
// ================================
$ppCode   = sanitize($_POST['id'] ?? '');
$username = $_SESSION['username'] ?? '';
$password = trim($_POST['password'] ?? '');

if ($ppCode === '' || $username === '' || $password === '') {

    setAlert('warning', 'Oops', 'Data tidak lengkap', 'warning', 'OK');
    redirect('pages/production_planning/');
}

// ================================
// VERIFY PASSWORD
// ================================
$user = checkLogin($pdo, $username, $password);

if (!$user) {

    setAlert('error', 'Oops', 'Password salah', 'danger', 'OK');
    redirect('pages/production_planning/');
}

try {

    // ================================
    // CHECK EXIST
    // ================================
    $check = $pdo->prepare("SELECT 1 FROM tbl_production_planning WHERE pp_code=? LIMIT 1");
    $check->execute([$ppCode]);

    if (!$check->fetchColumn()) {
        throw new Exception('Production Planning tidak ditemukan.');
    }

    $pdo->beginTransaction();

    // ================================
    // DELETE DETAIL FIRST
    // ================================
    $pdo->prepare("
        DELETE FROM tbl_detail_production_planning
        WHERE pp_id IN (
            SELECT pp_id FROM tbl_production_planning WHERE pp_code=?
        )
    ")->execute([$ppCode]);

    // ================================
    // DELETE HEADER (ALL SHIFTS)
    // ================================
    $pdo->prepare("
        DELETE FROM tbl_production_planning
        WHERE pp_code=?
    ")->execute([$ppCode]);

    $pdo->commit();

    setAlert(
        'success',
        'Berhasil!',
        'Production Planning berhasil dihapus.',
        'success',
        'OK'
    );

    redirect('pages/production_planning/');
} catch (Exception $e) {

    if ($pdo->inTransaction()) $pdo->rollBack();

    handlePdoError($e, 'pages/production_planning/');
}
