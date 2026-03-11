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
$no_hp    = sanitize($_POST['no_hp'] ?? '');
$role     = sanitize($_POST['role'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// ================================
// Validasi Awal
// ================================
if (
    $nip === '' ||
    $nama === '' ||
    $no_hp === '' ||
    $role === '' ||
    $username === '' ||
    $password === ''
) {
    setAlert(
        'error',
        'Oops!',
        'Semua field wajib diisi.',
        'danger',
        'Coba Lagi'
    );
    redirect('pages/user/create.php');
}

try {

    $pdo->beginTransaction();

    // ================================
    // Cek NIP sudah ada?
    // ================================
    $cekNip = $pdo->prepare(
        "SELECT COUNT(*) FROM tbl_karyawan WHERE nip = :nip"
    );
    $cekNip->execute([':nip' => $nip]);

    if ($cekNip->fetchColumn() > 0) {
        throw new Exception('NIP sudah terdaftar.');
    }

    // ================================
    // Cek Username sudah ada?
    // ================================
    $cekUser = $pdo->prepare(
        "SELECT COUNT(*) FROM tbl_user WHERE username = :username"
    );
    $cekUser->execute([':username' => $username]);

    if ($cekUser->fetchColumn() > 0) {
        throw new Exception('Username sudah digunakan.');
    }

    // ================================
    // Insert ke tbl_karyawan
    // ================================
    $insertKaryawan = $pdo->prepare(
        "INSERT INTO tbl_karyawan 
        (nip, nama, no_hp, role, username)
        VALUES
        (:nip, :nama, :no_hp, :role, :username)"
    );

    $insertKaryawan->execute([
        ':nip'      => $nip,
        ':nama'     => $nama,
        ':no_hp'    => $no_hp,
        ':role'     => $role,
        ':username' => $username
    ]);

    // ================================
    // Hash Password
    // ================================
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // ================================
    // Insert ke tbl_user
    // ================================
    $insertUser = $pdo->prepare(
        "INSERT INTO tbl_user
        (username, password, rule)
        VALUES
        (:username, :password, :rule)"
    );

    $insertUser->execute([
        ':username' => $username,
        ':password' => $hashedPassword,
        ':rule'     => $role
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

    handlePdoError(
        $e,
        'pages/user/create.php'
    );
}
