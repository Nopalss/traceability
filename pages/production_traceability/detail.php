<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

$_SESSION['menu'] = 'production_trace';

$product = $_GET['product'] ?? '';
$date    = $_GET['date'] ?? '';
$lot     = $_GET['lot'] ?? '';
$serial  = $_GET['serial'] ?? '';

if (!$product || !$date) {
    echo "<div class='container mt-5 alert alert-danger'>Parameter tidak lengkap</div>";
    exit;
}
$p = $pdo->prepare("SELECT part_name FROM tbl_part WHERE part_code=?");
$p->execute([$product]);
$productInfo = $p->fetch(PDO::FETCH_ASSOC);
/*
=====================================
HEADER SUMMARY
=====================================
*/
$headerSql = "
SELECT 
    p.part_name,
    COUNT(DISTINCT dp.serial_no) AS total_qty
FROM tbl_detail_product dp

LEFT JOIN tbl_part p
ON p.part_code = dp.product_code
";

$paramsHeader = [];

if ($lot) {
    $headerSql .= "
    INNER JOIN tbl_detail_production dpr 
        ON dpr.serial_no = dp.serial_no
    ";
}

$headerSql .= "
WHERE dp.product_code=? 
AND DATE(dp.created_at)=?
";


$paramsHeader[] = $product;
$paramsHeader[] = $date;

if ($lot) {
    $headerSql .= " AND dpr.lot_no LIKE ? ";
    $paramsHeader[] = "%$lot%";
}

$h = $pdo->prepare($headerSql);
$h->execute($paramsHeader);
$header = $h->fetch(PDO::FETCH_ASSOC);

$header['product_code'] = $product;
$header['prod_date'] = $date;
/*
=====================================
GET SERIAL (OPTIONAL FILTER LOT)
=====================================
*/

$sql = "
SELECT 
    dp.*,
    l.line_name,
    sup.name_supplier AS location_name
FROM tbl_detail_product dp
LEFT JOIN tbl_line l ON l.line_id = dp.line_id
LEFT JOIN tbl_supplier sup ON sup.id_supplier = dp.location
";

$params = [];

if ($lot) {

    $sql .= "
    INNER JOIN tbl_detail_production dpr 
        ON dpr.serial_no = dp.serial_no
    ";
}

$sql .= "
WHERE dp.product_code=? 
AND DATE(dp.created_at)=?
";

$params[] = $product;
$params[] = $date;

if ($lot) {
    $sql .= " AND dpr.lot_no LIKE ? ";
    $params[] = "%$lot%";
}

if ($serial) {
    $sql .= " AND dp.serial_no LIKE ? ";
    $params[] = "%$serial%";
}

$sql .= " ORDER BY dp.created_at";

$s = $pdo->prepare($sql);
$s->execute($params);
$serials = $s->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .trace-table {
        min-width: 1200px;
    }

    .trace-table th,
    .trace-table td {
        white-space: nowrap;
        vertical-align: middle;
        font-size: 0.8rem !important;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">

        <!-- HEADER CARD -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Production Trace Detail</h5>
            </div>

            <div class="card-body row">

                <div class="col-md">
                    <b>Product</b><br>
                    <?= $product ?> - <?= $productInfo['part_name'] ?? '' ?>
                </div>

                <div class="col-md-3">
                    <b>Date</b><br>
                    <?= $header['prod_date'] ?>
                </div>

                <div class="col-md-3">
                    <b>Total Qty</b><br>
                    <?= $header['total_qty'] ?>
                </div>
                <div class="col-md-2">
                    <a href="export.php?product=<?= urlencode($product) ?>&date=<?= urlencode($date) ?>&lot=<?= urlencode($lot) ?>&serial=<?= urlencode($serial) ?>"
                        class="btn btn-success">
                        Export Excel
                    </a>
                </div>

            </div>
        </div>


        <!-- SEARCH -->
        <div class="card shadow-sm mb-5">
            <div class="card-body">

                <form method="GET">

                    <input type="hidden" name="product" value="<?= $product ?>">
                    <input type="hidden" name="date" value="<?= $date ?>">

                    <div class="row">
                        <div class="col-md-3">
                            <input type="text"
                                name="serial"
                                value="<?= htmlspecialchars($serial) ?>"
                                class="form-control"
                                placeholder="Search Lot Product...">
                        </div>


                        <div class="col-md-3">
                            <input type="text"
                                name="lot"
                                value="<?= htmlspecialchars($lot) ?>"
                                class="form-control"
                                placeholder="Search Lot Material...">
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary">Search</button>
                            <a href="?product=<?= $product ?>&date=<?= $date ?>" class="btn btn-secondary">Reset</a>
                        </div>

                    </div>

                </form>

            </div>
        </div>


        <!-- TRACE TABLE -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">Serial & Material Trace (All Line)</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered table-hover trace-table">

                        <thead class="table-light">
                            <tr>
                                <th width="40">#</th>
                                <th>Lot Product</th>
                                <th>Line</th>
                                <th>Operator</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Out Date</th>
                                <th>Part</th>
                                <th>Part Name</th>
                                <th>Lot</th>
                                <th>Used</th>
                                <th>Supplier</th>
                                <th>Incoming Date</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $no = 1;
                            foreach ($serials as $s):
                            ?>

                                <tr class="table-primary">
                                    <td><?= $no++ ?></td>
                                    <td><?= $s['serial_no'] ?></td>
                                    <td><?= $s['line_name'] ?: $s['line_id'] ?></td>
                                    <td><?= $s['operator'] ?></td>
                                    <td><?= $s['created_at'] ?></td>
                                    <td><?= $s['location_name'] ?? 'Warehouse' ?></td>
                                    <td><?= $s['out_date'] ?: '-' ?></td>
                                    <td colspan="6"></td>
                                </tr>

                                <?php

                                $m = $pdo->prepare("
SELECT 
    dpr.part_code,
    pt.part_name,
    dpr.used_qty,
    dpr.lot_no,
    sup.name_supplier,
    dp.incoming_date
FROM tbl_detail_production dpr

LEFT JOIN tbl_part pt 
ON pt.part_code = dpr.part_code

LEFT JOIN tbl_detail_part dp 
ON dp.lot_no = dpr.lot_no
AND dp.part_code = dpr.part_code

LEFT JOIN tbl_supplier sup
ON sup.id_supplier = pt.supplier

WHERE dpr.serial_no=?
");

                                $m->execute([$s['serial_no']]);
                                $mats = $m->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($mats as $r):
                                ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="6"></td>
                                        <th><?= $r['part_code'] ?></th>
                                        <td><?= $r['part_name'] ?></td>
                                        <th><?= $r['lot_no'] ?></th>
                                        <td><?= $r['used_qty'] ?></td>
                                        <td><?= $r['name_supplier'] ?></td>
                                        <td><?= $r['incoming_date'] ?></td>
                                    </tr>

                            <?php endforeach;
                            endforeach; ?>

                            <?php if (!$serials): ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No data</td>
                                </tr>
                            <?php endif ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">⬅ Back</a>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>