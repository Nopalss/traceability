<?php
require_once __DIR__ . '/../../includes/config.php';

$success = false;
$error = false;
$message = "";

// ==========================
// GET ID KARYAWAN
// ==========================
$id_karyawan = (int) ($_GET['id'] ?? 0);

if (!$id_karyawan) {
    die("ID tidak ditemukan");
}

// ==========================
// GET DATA
// ==========================
$stmt = $pdo->prepare("
    SELECT u.user_id, u.username, u.role_id, k.*
    FROM tbl_karyawan k
    LEFT JOIN tbl_user u ON u.username = k.username
    WHERE k.karyawan_id = ?
");
$stmt->execute([$id_karyawan]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan");
}

$user_id = $data['user_id'];

// ==========================
// HANDLE UPDATE
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $nip      = trim($_POST['nip'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $role_id  = (int) ($_POST['role'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$nip || !$nama || !$role_id || !$username) {
            throw new Exception("Semua field wajib diisi!");
        }

        $pdo->beginTransaction();

        // ==========================
        // CEK NIP
        // ==========================
        $cek = $pdo->prepare("
            SELECT COUNT(*) FROM tbl_karyawan 
            WHERE nip = ? AND karyawan_id != ?
        ");
        $cek->execute([$nip, $id_karyawan]);

        if ($cek->fetchColumn() > 0) {
            throw new Exception("NIP sudah digunakan!");
        }

        // ==========================
        // CEK USERNAME
        // ==========================
        $cek = $pdo->prepare("
            SELECT COUNT(*) FROM tbl_user 
            WHERE username = ? AND user_id != ?
        ");
        $cek->execute([$username, $user_id]);

        if ($cek->fetchColumn() > 0) {
            throw new Exception("Username sudah digunakan!");
        }

        // ==========================
        // AMBIL ROLE
        // ==========================
        $stmt = $pdo->prepare("SELECT * FROM tbl_role WHERE role_id = ?");
        $stmt->execute([$role_id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$role) {
            throw new Exception("Role tidak ditemukan!");
        }

        $role_name = strtolower($role['role_name']);

        // ==========================
        // UPDATE KARYAWAN (PAKAI ID)
        // ==========================
        $stmt = $pdo->prepare("
            UPDATE tbl_karyawan 
            SET nip=?, nama=?, role=?, username=? 
            WHERE karyawan_id=?
        ");
        $stmt->execute([
            $nip,
            $nama,
            $role_name,
            $username,
            $id_karyawan
        ]);

        // ==========================
        // UPDATE USER (PAKAI user_id)
        // ==========================
        if ($password) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("
                UPDATE tbl_user 
                SET username=?, password=?, rule=?, role_id=? 
                WHERE user_id=?
            ");
            $stmt->execute([
                $username,
                $hashed,
                $role_name,
                $role_id,
                $user_id
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE tbl_user 
                SET username=?, rule=?, role_id=? 
                WHERE user_id=?
            ");
            $stmt->execute([
                $username,
                $role_name,
                $role_id,
                $user_id
            ]);
        }

        $pdo->commit();

        $success = true;
        $message = "Data berhasil diupdate!";
    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $error = true;
        $message = $e->getMessage();
    }
}

// ==========================
// GET ROLE
// ==========================
$stmt = $pdo->prepare("SELECT * FROM tbl_role WHERE role_id != 1 ORDER BY role_id ASC");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>


<div class="content d-flex flex-column flex-column-fluid pt-0">
    <div class="container">

        <div class="row">
            <div class="col-lg-10 mx-auto">

                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h3>Edit User</h3>
                    </div>

                    <form method="POST">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>NIP</label>
                                    <input type="text" name="nip" value="<?= $data['nip'] ?>" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="nama" value="<?= $data['nama'] ?>" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Role</label>
                                    <select name="role" class="form-control">
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r['role_id'] ?>" <?= $data['role_id'] == $r['role_id'] ? 'selected' : '' ?>>
                                                <?= $r['role_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Username</label>
                                    <input type="text" name="username" value="<?= $data['username'] ?>" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Password (opsional)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-end">
                            <button class="btn btn-warning">Update</button>
                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '<?= $message ?>'
        });
    <?php endif; ?>

    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '<?= $message ?>'
        });
    <?php endif; ?>
</script>