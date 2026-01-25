<?php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . "/../../helper/handlePdoError.php";

$_SESSION['halaman'] = 'Incoming Part';
$_SESSION['menu'] = 'dashboard';

$id = $_GET['ref_no'] ?? null;
if (!$id) {
    die('Ref No tidak valid');
}

$_SESSION['subHalaman'] = '| detail | ' . $id;

/*
|--------------------------------------------------------------------------
| DETAIL PART + PART INFO
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT 
        dp.*,
        p.part_name,
        p.supplier
    FROM tbl_detail_part dp
    LEFT JOIN tbl_part p ON p.part_code = dp.part_code
    WHERE dp.ref_number = ?
");
$stmt->execute([$id]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$part) {

    setAlert('warning', "Data Not Found", 'The requested data is not available or may no longer exist.', 'danger', 'Close');
    redirect("pages/incoming_part/");
}

/*
|--------------------------------------------------------------------------
| PRODUCTION USAGE
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT 
        dp.*,
        p.product_name
    FROM tbl_detail_production dp
    JOIN tbl_product p ON p.product_code = dp.product_code
    WHERE dp.ref_number = ?
");
$stmt->execute([$id]);
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-2">
                    <div class="card">
                        <div class="card-body pb-3 pt-5">

                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Ref. No</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['ref_number'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Part Code</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['part_code'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Part Name</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['part_name'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Supplier</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['supplier'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Lot No</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['lot_no'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Quantity</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['qty'] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Status</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['status'] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Incoming Date</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['incoming_date'] ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Remarks</label>
                                    </div>
                                    <div class="col">
                                        <textarea class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;" disabled><?= $part['remarks'] ?></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h5>Production Usage</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 0.75rem">No</th>
                                            <th style="font-size: 0.75rem">Ref Product</th>
                                            <th style="font-size: 0.75rem">Product Code</th>
                                            <th style="font-size: 0.75rem">Product</th>
                                            <th style="font-size: 0.75rem">Production At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($product)): ?>
                                            <?php $i = 1;
                                            foreach ($product as $p): ?>
                                                <tr>
                                                    <td style="font-size: 0.75rem"><?= $i++ ?></td>
                                                    <td style="font-size: 0.75rem">
                                                        <a href="<?= BASE_URL ?>pages/products/ref/detail.php?ref_product=<?= $p['ref_product'] ?>">
                                                            <?= $p['ref_product'] ?>
                                                        </a>
                                                    </td>
                                                    <td style="font-size: 0.75rem"><?= $p['product_code'] ?></td>
                                                    <td style="font-size: 0.75rem"><?= $p['product_name'] ?></td>
                                                    <td style="font-size: 0.75rem"><?= formatTanggal($p['production_at']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5"
                                                    class="text-muted text-center font-weight-bolder"
                                                    style="font-size: 0.75rem">
                                                    Not Yet Used in Production
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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