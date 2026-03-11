<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/sanitize.php';

$_SESSION['halaman'] = 'user';
$_SESSION['menu'] = 'user';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

// ================================
// Ambil ID
// ================================
$id = $_GET['id'] ?? '';

if ($id == '') {
    header("Location: " . BASE_URL . 'pages/user/');
}

// ================================
// Ambil Data Karyawan
// ================================
$stmt = $pdo->prepare("
    SELECT k.*, u.rule 
    FROM tbl_karyawan k
    LEFT JOIN tbl_user u ON k.username = u.username
    WHERE k.karyawan_id = :id
");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header("Location: " . BASE_URL . 'pages/user/');
}
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <div class="row">
                <div class="col-lg-10 mx-auto">

                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-white">
                            <h3 class="card-title mb-0">
                                <i class="bi bi-pencil-square me-2"></i>
                                Update Karyawan & Akun
                            </h3>
                        </div>

                        <form action="<?= BASE_URL ?>controllers/user/update.php" method="POST">
                            <input type="hidden" name="id" value="<?= $data['karyawan_id'] ?>">

                            <div class="card-body">

                                <!-- DATA KARYAWAN -->
                                <div class="mb-4">
                                    <h5 class="text-warning mb-3">
                                        <i class="bi bi-person-badge me-2"></i>
                                        Data Karyawan
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">NIP</label>
                                            <input type="text" name="nip"
                                                value="<?= htmlspecialchars($data['nip']) ?>"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama"
                                                value="<?= htmlspecialchars($data['nama']) ?>"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">No HP</label>
                                            <input type="text" name="no_hp"
                                                value="<?= htmlspecialchars($data['no_hp']) ?>"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Role</label>
                                            <select name="role" class="form-control" required>
                                                <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                <option value="operator" <?= $data['role'] == 'operator' ? 'selected' : '' ?>>Operator</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- DATA AKUN -->
                                <div class="mt-4">
                                    <h5 class="text-warning mb-3">
                                        <i class="bi bi-shield-lock-fill me-2"></i>
                                        Data Akun Login
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" name="username"
                                                value="<?= htmlspecialchars($data['username']) ?>"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Password Baru</label>
                                            <input type="password" name="password"
                                                class="form-control">
                                            <small class="text-muted">
                                                Kosongkan jika tidak ingin mengubah password.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="alert alert-light-warning mt-3">
                                        <small>
                                            <i class="bi bi-info-circle"></i>
                                            Perubahan role akan mempengaruhi hak akses sistem.
                                        </small>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-end">
                                <a href="<?= BASE_URL ?>pages/user/" class="btn btn-light mr-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-1"></i>
                                    Update Data
                                </button>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php
require __DIR__ . '/../../includes/footer.php';
?>