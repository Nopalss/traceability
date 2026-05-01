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
$id       = (int)($_POST['id'] ?? 0);
$nip      = sanitize($_POST['nip'] ?? '');
$nama     = sanitize($_POST['nama'] ?? '');
$no_hp    = sanitize($_POST['no_hp'] ?? '');
$role     = sanitize($_POST['role'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$pengguna = $_POST['pengguna'] ?? '';


// ================================
// Validasi Awal
// ================================
if (
    $id <= 0 ||
    $nip === '' ||
    $nama === '' ||
    $no_hp === '' ||
    $username === ''
) {
    setAlert(
        'error',
        'Oops!',
        'Semua field wajib diisi (kecuali password).',
        'danger',
        'Coba Lagi'
    );
    if (isset($pengguna)) {
        redirect('pages/user/operator.php');
    }
    redirect('pages/user/edit.php?id=' . $id);
}

try {

    $pdo->beginTransaction();

    // ================================
    // Ambil username lama
    // ================================
    $old = $pdo->prepare("
        SELECT username FROM tbl_karyawan 
        WHERE karyawan_id = :id
    ");
    $old->execute([':id' => $id]);
    $oldData = $old->fetch(PDO::FETCH_ASSOC);

    if (!$oldData) {
        throw new Exception('Data karyawan tidak ditemukan.');
    }

    $oldUsername = $oldData['username'];

    // ================================
    // Cek NIP unik (kecuali dirinya)
    // ================================
    $cekNip = $pdo->prepare("
        SELECT COUNT(*) FROM tbl_karyawan 
        WHERE nip = :nip AND karyawan_id != :id
    ");
    $cekNip->execute([
        ':nip' => $nip,
        ':id'  => $id
    ]);

    if ($cekNip->fetchColumn() > 0) {
        throw new Exception('NIP sudah digunakan.');
    }

    // ================================
    // Cek Username unik (kecuali dirinya)
    // ================================
    $cekUser = $pdo->prepare("
        SELECT COUNT(*) FROM tbl_user 
        WHERE username = :username AND username != :old_username
    ");
    $cekUser->execute([
        ':username'     => $username,
        ':old_username' => $oldUsername
    ]);

    if ($cekUser->fetchColumn() > 0) {
        throw new Exception('Username sudah digunakan.');
    }

    // ================================
    // Update tbl_karyawan
    // ================================
    $updateKaryawan = $pdo->prepare("
        UPDATE tbl_karyawan
        SET nip = :nip,
            nama = :nama,
            no_hp = :no_hp,
            role = :role,
            username = :username
        WHERE karyawan_id = :id
    ");

    $updateKaryawan->execute([
        ':nip'      => $nip,
        ':nama'     => $nama,
        ':no_hp'    => $no_hp,
        ':role'     => $role,
        ':username' => $username,
        ':id'       => $id
    ]);

    // ================================
    // Update tbl_user
    // ================================
    if ($password !== '') {

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $updateUser = $pdo->prepare("
            UPDATE tbl_user
            SET username = :username,
                password = :password,
                rule = :rule
            WHERE username = :old_username
        ");

        $updateUser->execute([
            ':username'     => $username,
            ':password'     => $hashedPassword,
            ':rule'         => $role,
            ':old_username' => $oldUsername
        ]);
    } else {

        $updateUser = $pdo->prepare("
            UPDATE tbl_user
            SET username = :username,
                rule = :rule
            WHERE username = :old_username
        ");

        $updateUser->execute([
            ':username'     => $username,
            ':rule'         => $role,
            ':old_username' => $oldUsername
        ]);
    }

    $pdo->commit();

    setAlert(
        'success',
        'Berhasil!',
        'Data karyawan berhasil diperbarui.',
        'success',
        'Oke'
    );

    if (isset($pengguna)) {
        redirect('pages/user/operator.php');
    }
    redirect('pages/user/');
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if (isset($pengguna)) {
        handlePdoError(
            $e,
            'pages/user/operator.php'
        );
    }
    handlePdoError(
        $e,
        'pages/user/edit.php?id=' . $id
    );
}
