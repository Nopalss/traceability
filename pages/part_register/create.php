<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['halaman'] = 'part register';
$_SESSION['menu'] = 'part_register';
$_SESSION['subHalaman'] = ' | new part';

$sql = "SELECT * FROM tbl_supplier";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$supplier = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <h3>Form New Part</h3>
                            <form action="<?= BASE_URL ?>controllers/part_register/create.php" method="post" class="form">
                                <div class="col input-group-sm mt-5">
                                    <label for="part_code" class="form-label small font-weight-bolder">
                                        Part Code
                                    </label>
                                    <input id="part_code" name="part_code" class="form-control" required>
                                </div>
                                <div class="col input-group-sm mt-5">
                                    <label for="part_name" class="form-label small font-weight-bolder">
                                        Part Name
                                    </label>
                                    <input id="part_name" name="part_name" class="form-control" required>
                                </div>
                                <div class="col input-group-sm mt-5">
                                    <label for="supplier" class="form-label small font-weight-bolder">
                                        Supplier
                                    </label>
                                    <select class="form-control" id="supplier" name="supplier" required>
                                        <option value="">Select</option>
                                        <?php foreach ($supplier as $s): ?>
                                            <option value="<?= $s['id_supplier'] ?>"><?= $s["name_supplier"] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col mt-5 text-right">
                                    <a href="<?= BASE_URL ?>pages/part_register/" class="btn btn-outline-danger">Batal</a>
                                    <button type="submit" name="submit" class="btn btn-success">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- LOAD JS CUSTOM -->

<?php
require __DIR__ . '/../../includes/footer.php';
?>