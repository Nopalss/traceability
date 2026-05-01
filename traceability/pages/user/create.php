<?php
require_once __DIR__ . '/../../includes/config.php';

$success = false;
$error = false;
$message = "";

// ==========================
// HANDLE SUBMIT
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $nip      = trim($_POST['nip'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $role_id  = (int) ($_POST['role'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$nip || !$nama || !$role_id || !$username || !$password) {
            throw new Exception("Semua field wajib diisi!");
        }

        $pdo->beginTransaction();

        // ==========================
        // CEK NIP
        // ==========================
        $cek = $pdo->prepare("SELECT COUNT(*) FROM tbl_karyawan WHERE nip = ?");
        $cek->execute([$nip]);
        if ($cek->fetchColumn() > 0) {
            throw new Exception("NIP sudah terdaftar!");
        }

        // ==========================
        // CEK USERNAME
        // ==========================
        $cek = $pdo->prepare("SELECT COUNT(*) FROM tbl_user WHERE username = ?");
        $cek->execute([$username]);
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
        // INSERT KARYAWAN
        // ==========================
        $stmt = $pdo->prepare("
            INSERT INTO tbl_karyawan (nip, nama, role, username)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$nip, $nama, $role_name, $username]);

        // ==========================
        // INSERT USER
        // ==========================
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO tbl_user (username, password, rule, role_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$username, $hashed, $role_name, $role_id]);

        $pdo->commit();

        $success = true;
        $message = "Data berhasil ditambahkan!";
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
$sql = "SELECT * FROM tbl_role WHERE role_id != 1 ORDER BY role_id ASC";
$stmt = $pdo->prepare($sql);
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
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Tambah Karyawan & Akun</h3>
                    </div>

                    <form method="POST">
                        <div class="card-body">

                            <h5 class="text-primary">Data Karyawan</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>NIP</label>
                                    <input type="text" name="nip" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Role</label>
                                    <select name="role" class="form-control" required>
                                        <option value="">-- pilih role --</option>
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r['role_id'] ?>">
                                                <?= $r['role_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <h5 class="text-primary">Akun Login</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-end">
                            <button class="btn btn-primary">Simpan</button>
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
            title: 'Berhasil!',
            text: '<?= $message ?>'
        });
    <?php endif; ?>

    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $message ?>'
        });
    <?php endif; ?>
</script>