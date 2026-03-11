<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

$_SESSION['menu'] = 'production_trace';

$product = $_GET['product'] ?? '';
$date    = $_GET['date'] ?? '';
$line    = $_GET['line'] ?? '';

if (!$product || !$date || !$line) {
    echo "<div class='container mt-5 alert alert-danger'>Parameter tidak lengkap</div>";
    exit;
}

/*
=====================================
HEADER PRODUCT (SUMMARY)
=====================================
*/
$h = $pdo->prepare("
SELECT 
    dp.product_code,
    dp.line_id,
    l.line_name,
    dp.shift,
    DATE(dp.created_at) prod_date,
    COUNT(*) qty
FROM tbl_detail_product dp
LEFT JOIN tbl_line l ON l.line_id = dp.line_id
WHERE dp.product_code=? 
AND DATE(dp.created_at)=?
AND dp.line_id=?
GROUP BY dp.product_code,dp.line_id,l.line_name,dp.shift,DATE(dp.created_at)
");
$h->execute([$product, $date, $line]);
$header = $h->fetch(PDO::FETCH_ASSOC);

if (!$header) {
    echo "<div class='container mt-5 alert alert-danger'>Data tidak ditemukan</div>";
    exit;
}

/*
=====================================
GET ALL SERIAL PRODUCT
=====================================
*/
$s = $pdo->prepare("
SELECT *
FROM tbl_detail_product
WHERE product_code=?
AND DATE(created_at)=?
AND line_id=?
ORDER BY created_at
");
$s->execute([$product, $date, $line]);
$serials = $s->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">

        <!-- HEADER CARD -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Production Trace Detail</h5>
            </div>

            <div class="card-body row">

                <div class="col-md-3"><b>Product</b><br><?= $header['product_code'] ?></div>
                <div class="col-md-3"><b>Date</b><br><?= $header['prod_date'] ?></div>
                <div class="col-md-2"><b>Line</b><br><?= $header['line_name'] ?: $header['line_id'] ?></div>
                <div class="col-md-2"><b>Shift</b><br><?= $header['shift'] ?></div>
                <div class="col-md-2"><b>Total Qty</b><br><?= $header['qty'] ?></div>

            </div>
        </div>

        <!-- TRACE TABLE -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">Serial & Material Trace</h6>
            </div>

            <div class="card-body">

                <table class="table table-bordered table-hover">

                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Serial</th>
                            <th>Operator</th>
                            <th>Time</th>
                            <th>Part</th>
                            <th>Part Name</th>
                            <th>Lot</th>
                            <th>Used</th>
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
                                <td><?= $s['operator'] ?></td>
                                <td><?= $s['created_at'] ?></td>
                                <td colspan="4"></td>
                            </tr>

                            <?php
                            $m = $pdo->prepare("
SELECT dpr.part_code,pt.part_name,dpr.used_qty,dpr.lot_no
FROM tbl_detail_production dpr
LEFT JOIN tbl_part pt ON pt.part_code=dpr.part_code
WHERE dpr.serial_no=?
");
                            $m->execute([$s['serial_no']]);
                            $mats = $m->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($mats as $r):
                            ?>

                                <tr>
                                    <td></td>
                                    <td colspan="3"></td>
                                    <td><?= $r['part_code'] ?></td>
                                    <td><?= $r['part_name'] ?></td>
                                    <td><?= $r['lot_no'] ?></td>
                                    <td><?= $r['used_qty'] ?></td>
                                </tr>

                        <?php endforeach;
                        endforeach; ?>

                        <?php if (!$serials): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No data</td>
                            </tr>
                        <?php endif ?>

                    </tbody>
                </table>

            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">⬅ Back</a>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>