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
$sql = "SELECT * FROM tbl_supplier";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$supplier = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$part) {
    redirect('pages/part_register/');
}

$_SESSION['subHalaman'] = ' | edit part | ' . $id_part;
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
                    <div class="card col-lg-6">
                        <div class="card-body">
                            <h3>Form Edit Part</h3>
                            <form
                                action="<?= BASE_URL ?>controllers/part_register/edit.php"
                                method="post"
                                class="form">

                                <!-- ID tersembunyi -->
                                <input type="hidden" name="id_part" value="<?= $part['id_part']; ?>">

                                <div class="col input-group-sm mt-5">
                                    <label for="part_code" class="form-label small font-weight-bolder">
                                        Part Code
                                    </label>
                                    <input
                                        id="part_code"
                                        name="part_code"
                                        class="form-control"
                                        value="<?= htmlspecialchars($part['part_code']); ?>"
                                        required>
                                </div>

                                <div class="col input-group-sm mt-5">
                                    <label for="part_name" class="form-label small font-weight-bolder">
                                        Part Name
                                    </label>
                                    <input
                                        id="part_name"
                                        name="part_name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($part['part_name']); ?>"
                                        required>
                                </div>

                                <div class="col input-group-sm mt-5">
                                    <label for="supplier" class="form-label small font-weight-bolder">
                                        Supplier
                                    </label>
                                    <select class="form-control" id="supplier" name="supplier" required>
                                        <option value="">Select</option>
                                        <?php foreach ($supplier as $s): ?>
                                            <?php $selected = $s['id_supplier'] === $part['supplier'] ? "selected" : ""; ?>
                                            <option value="<?= $s['id_supplier'] ?>" <?= $selected ?>><?= $s["name_supplier"] ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                                <div class="col mt-5 text-right">
                                    <a href="<?= BASE_URL ?>pages/part_register/" class="btn btn-outline-danger">
                                        Batal
                                    </a>
                                    <button type="submit" name="submit" class="btn btn-success">
                                        Update
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/../../includes/footer.php';
?>