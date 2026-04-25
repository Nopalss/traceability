<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';
require_once __DIR__ . '/../../helper/redirect.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/user/');
}

// ================================
// Ambil & Sanitasi Input
// ================================

$nip      = sanitize($_POST['nip'] ?? '');
$nama     = sanitize($_POST['nama'] ?? '');
$role_id = (int) ($_POST['role'] ?? 0);
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// ================================
// Validasi
// ================================
if (
    $nip === '' ||
    $nama === '' ||
    $role_id === '' ||
    $username === '' ||
    $password === ''
) {
    setAlert('error', 'Oops!', 'Semua field wajib diisi.', 'danger', 'Coba Lagi');
    redirect('pages/user/create.php');
}

try {

    $pdo->beginTransaction();

    // ================================
    // Cek NIP
    // ================================
    $cekNip = $pdo->prepare("SELECT COUNT(*) FROM tbl_karyawan WHERE nip = ?");
    $cekNip->execute([$nip]);

    if ($cekNip->fetchColumn() > 0) {
        throw new Exception('NIP sudah terdaftar.');
    }

    // ================================
    // Cek Username
    // ================================
    $cekUser = $pdo->prepare("SELECT COUNT(*) FROM tbl_user WHERE username = ?");
    $cekUser->execute([$username]);

    if ($cekUser->fetchColumn() > 0) {
        throw new Exception('Username sudah digunakan.');
    }

    // ================================
    // Ambil Role
    // ================================
    $stmt = $pdo->prepare("SELECT * FROM tbl_role WHERE role_id = ?");
    $stmt->execute([$role_id]);
    $roleData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$roleData) {
        throw new Exception("Role tidak ditemukan.");
    }

    $role_name = strtolower($roleData['role_name']);

    // ================================
    // Insert tbl_karyawan
    // ================================
    $insertKaryawan = $pdo->prepare("
        INSERT INTO tbl_karyawan 
        (nip, nama, role, username)
        VALUES (?, ?, ?, ?)
    ");

    $insertKaryawan->execute([
        $nip,
        $nama,
        $role_name,
        $username
    ]);

    // ================================
    // Hash Password
    // ================================
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // ================================
    // Insert tbl_user
    // ================================
    $insertUser = $pdo->prepare("
        INSERT INTO tbl_user
        (username, password, role, role_id)
        VALUES (?, ?, ?, ?)
    ");

    $insertUser->execute([
        $username,
        $hashedPassword,
        $role_name,
        $role_id
    ]);

    $pdo->commit();

    setAlert(
        'success',
        'Berhasil!',
        'Karyawan dan akun berhasil ditambahkan.',
        'success',
        'Oke'
    );

    redirect('pages/user/');
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    handlePdoError($e, 'pages/user/create.php');
}
