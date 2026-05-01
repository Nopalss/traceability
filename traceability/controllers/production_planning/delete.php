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
    // GET PP_ID LIST
    // ================================
    $stmt = $pdo->prepare("
        SELECT pp_id 
        FROM tbl_production_planning 
        WHERE pp_code = ?
    ");
    $stmt->execute([$ppCode]);
    $ppIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($ppIds)) {
        throw new Exception('Production Planning tidak ditemukan.');
    }

    $pdo->beginTransaction();

    // ================================
    // PREPARE IN CLAUSE
    // ================================
    $placeholders = implode(',', array_fill(0, count($ppIds), '?'));

    // ================================
    // DELETE DETAIL
    // ================================
    $pdo->prepare("
        DELETE FROM tbl_detail_production_planning
        WHERE pp_id IN ($placeholders)
    ")->execute($ppIds);

    // ================================
    // DELETE MATERIAL (WAJIB!)
    // ================================
    $pdo->prepare("
        DELETE FROM tbl_pp_material
        WHERE pp_id IN ($placeholders)
    ")->execute($ppIds);

    // ================================
    // DELETE HEADER
    // ================================
    $pdo->prepare("
        DELETE FROM tbl_production_planning
        WHERE pp_code = ?
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

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    handlePdoError($e, 'pages/production_planning/');
}
