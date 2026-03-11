<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
$_SESSION['halaman'] = 'user';
$_SESSION['menu'] = 'user';

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <div class="row">
                <div class="col-lg-10 mx-auto">

                    <!-- CARD -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Tambah Karyawan & Buat Akun
                            </h3>
                        </div>

                        <form action="<?= BASE_URL ?>controllers/user/create.php" method="POST">
                            <div class="card-body">

                                <!-- DATA KARYAWAN -->
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-person-badge me-2"></i>
                                        Data Karyawan
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">NIP</label>
                                            <input type="text" name="nip" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">No HP</label>
                                            <input type="text" name="no_hp" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Role Karyawan</label>
                                            <select name="role" class="form-control" required>
                                                <option value="">-- Pilih Role --</option>
                                                <option value="admin">Admin</option>
                                                <option value="operator">Operator</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- DATA AKUN -->
                                <div class="mt-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-shield-lock-fill me-2"></i>
                                        Data Akun Login
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" name="username" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="alert alert-light-info mt-3">
                                        <small>
                                            <i class="bi bi-info-circle"></i>
                                            Pastikan username unik dan role sesuai dengan akses sistem.
                                        </small>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-end">
                                <a href="<?= BASE_URL ?>pages/user/" class="btn btn-light mr-2">
                                    Batal
                                </a>
                                <button type="reset" class="btn btn-light-danger mr-2">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    Simpan Data
                                </button>
                            </div>

                        </form>

                    </div>
                    <!-- END CARD -->

                </div>
            </div>

        </div>
    </div>
</div>


<?php
require __DIR__ . '/../../includes/footer.php';
?>