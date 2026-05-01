<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$_SESSION['halaman'] = 'Production Traceability';
$_SESSION['menu'] = 'traceability';

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

/* ================= FILTER ================= */

$from    = $_GET['from'] ?? '';
$to      = $_GET['to'] ?? '';
$product = $_GET['product'] ?? '';
$lotref  = $_GET['lotref'] ?? '';
$lot_material = $_GET['lot_material'] ?? '';

$where = [];
$params = [];

if ($from && $to) {

    $where[] = "dp.created_at BETWEEN ? AND ?";
    $params[] = $from . " 00:00:00";
    $params[] = $to . " 23:59:59";
} elseif ($from) {

    $where[] = "dp.created_at >= ?";
    $params[] = $from . " 00:00:00";
} elseif ($to) {

    $where[] = "dp.created_at <= ?";
    $params[] = $to . " 23:59:59";
}

if ($product) {
    $where[] = "dp.product_code LIKE ?";
    $params[] = "%$product%";
}

if ($lotref) {
    $where[] = "(dp.serial_no LIKE ? OR dp.ref_number LIKE ?)";
    $params[] = "%$lotref%";
    $params[] = "%$lotref%";
}
if ($lot_material) {
    $where[] = "EXISTS (
        SELECT 1 FROM tbl_detail_production dprod
        WHERE dprod.serial_no = dp.serial_no
        AND dprod.lot_no LIKE ?
    )";
    $params[] = "%$lot_material%";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

/*
=====================================
SUMMARY PER PRODUCT + DATE ONLY
(NO LINE, NO SHIFT)
=====================================
*/

$sql = "
SELECT 
    dp.product_code,
    DATE(dp.created_at) production_date,
    COUNT(dp.serial_no) total_qty
FROM tbl_detail_product dp
$whereSql
GROUP BY dp.product_code, DATE(dp.created_at)
ORDER BY production_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">

        <div class="card shadow-sm mb-5">
            <div class="card-header pb-2">
                <h3>Production Traceability</h3>
            </div>

            <form method="get">
                <div class="card-body row g-3">

                    <div class="col-md-3">
                        <label>From</label>
                        <input type="date" name="from" value="<?= $from ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>To</label>
                        <input type="date" name="to" value="<?= $to ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Product</label>
                        <input type="text" name="product" value="<?= $product ?>" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>Serial / Ref</label>
                        <input type="text" name="lotref" value="<?= $lotref ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mt-4">
                        <label>Material Lot</label>
                        <input type="text" name="lot_material"
                            value="<?= $lot_material ?>"
                            class="form-control"
                            placeholder="Search lot material">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Total Qty</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= $r['product_code'] ?></td>
                                <td><?= $r['production_date'] ?></td>
                                <td><?= $r['total_qty'] ?></td>

                                <td>
                                    <a href="detail.php?product=<?= $r['product_code'] ?>&date=<?= $r['production_date'] ?>"
                                        class="btn btn-sm btn-info">
                                        <i class="far fa-eye"></i>
                                    </a>

                                    <a href="export.php?product=<?= $r['product_code'] ?>&date=<?= $r['production_date'] ?>"
                                        class="btn btn-sm btn-success">
                                        <i class="far fa-file-excel"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        <?php if (!$rows): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No data</td>
                            </tr>
                        <?php endif ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>