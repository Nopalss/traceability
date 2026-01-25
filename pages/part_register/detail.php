<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

$_SESSION['halaman']    = 'part register';
$_SESSION['menu']       = 'part_register';

// --- Ambil ID ---
$id_part = isset($_GET['id_part']) ? (int) $_GET['id_part'] : 0;

if ($id_part <= 0) {
    redirect('pages/part_register/');
}

// --- Ambil data part ---
$stmt = $pdo->prepare(
    "SELECT p.id_part, p.part_code, p.part_name, p.supplier, s.name_supplier
     FROM tbl_part p
     JOIN tbl_supplier s ON p.supplier = s.id_supplier
     WHERE p.id_part = :id_part
     LIMIT 1"
);
$stmt->execute([':id_part' => $id_part]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {
    redirect('pages/part_register/');
}

$_SESSION['subHalaman'] = ' | Detail | ' . $part['part_code'];
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div
    class="content d-flex flex-column flex-column-fluid pt-0"
    id="kt_content">
    <div>
        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 d-flex justify-content-center mb-2">
                        <div class="card col-lg-6">
                            <div class="card-body">
                                <h3>Detail Part <?= htmlspecialchars($part['part_code']); ?></h3>
                                <table class="table table-striped mt-8">
                                    <tr>
                                        <th>Part Code</th>
                                        <td><?= htmlspecialchars($part['part_code']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Part Name</th>
                                        <td><?= htmlspecialchars($part['part_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Supplier</th>
                                        <td><?= htmlspecialchars($part['name_supplier']); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>pages/part_register/" class="btn col-lg-6 btn-primary">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/../../includes/footer.php';
?>