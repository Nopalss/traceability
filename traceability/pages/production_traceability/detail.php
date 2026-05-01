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
$page    = intval($_GET['page'] ?? 1);
$limit   = 10;
$offset  = ($page - 1) * $limit;

if (!$product || !$date) {
    echo "<div class='container mt-5 alert alert-danger'>Parameter tidak lengkap</div>";
    exit;
}

$p = $pdo->prepare("SELECT part_name FROM tbl_part WHERE part_code=?");
$p->execute([$product]);
$productInfo = $p->fetch(PDO::FETCH_ASSOC);

/* ================= HEADER ================= */
$headerSql = "
SELECT COUNT(DISTINCT dp.serial_no, dp.ref_number) AS total_qty
FROM tbl_detail_product dp
WHERE dp.product_code=? AND DATE(dp.created_at)=?
";
$h = $pdo->prepare($headerSql);
$h->execute([$product, $date]);
$header = $h->fetch(PDO::FETCH_ASSOC);

/* ================= COUNT ================= */
$countSql = "
SELECT COUNT(DISTINCT dp.serial_no)
FROM tbl_detail_product dp
LEFT JOIN tbl_detail_production dpr 
    ON dpr.serial_no = dp.serial_no 
    AND dpr.ref_product = dp.ref_number
WHERE dp.product_code=? AND DATE(dp.created_at)=?
";

$paramsCount = [$product, $date];

if ($serial) {
    $countSql .= " AND dp.serial_no LIKE ? ";
    $paramsCount[] = "%$serial%";
}

if ($lot) {
    $countSql .= " AND dpr.lot_no LIKE ? ";
    $paramsCount[] = "%$lot%";
}

$c = $pdo->prepare($countSql);
$c->execute($paramsCount);
$totalRows = $c->fetchColumn();

/* ================= MAIN ================= */
$sql = "
SELECT 
    dp.id,
    dp.product_code,
    dp.serial_no,
    dp.ref_number,
    dp.operator,
    dp.created_at,
    dp.out_date,
    dp.line_id,

    l.line_name,
    sup.name_supplier AS location_name

FROM tbl_detail_product dp

LEFT JOIN tbl_line l ON l.line_id = dp.line_id
LEFT JOIN tbl_supplier sup ON sup.id_supplier = dp.location

WHERE dp.product_code=? 
AND DATE(dp.created_at)=?
";



$params = [$product, $date];

if ($serial) {
    $sql .= " AND dp.serial_no LIKE ? ";
    $params[] = "%$serial%";
}

$sql .= " ORDER BY dp.serial_no, dp.ref_number 
LIMIT $limit OFFSET $offset";

$s = $pdo->prepare($sql);
$s->execute($params);
$data = $s->fetchAll(PDO::FETCH_ASSOC);

/* ================= GROUP ================= */
$grouped = [];
foreach ($data as $row) {
    $grouped[$row['serial_no']][] = $row;
}
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

<div class="content d-flex flex-column flex-column-fluid pt-0">
    <div class="container">

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
                    <?= date("d M Y", strtotime($date)) ?>
                </div>

                <div class="col-md-3">
                    <b>Total Qty</b><br>
                    <?= $header['total_qty'] ?>
                </div>
            </div>
        </div>


        <!-- 🔥 FILTER + EXPORT -->
        <div class="card-body pt-0">
            <form method="GET" class="row">
                <input type="hidden" name="product" value="<?= $product ?>">
                <input type="hidden" name="date" value="<?= $date ?>">

                <div class="col-md-3">
                    <input type="text" name="serial" class="form-control" placeholder="Lot Product" value="<?= $serial ?>">
                </div>

                <div class="col-md-3">
                    <input type="text" name="lot" class="form-control" placeholder="Lot Part" value="<?= $lot ?>">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary">Search</button>

                    <a href="?product=<?= $product ?>&date=<?= $date ?>" class="btn btn-secondary">
                        Reset
                    </a>
                </div>

                <div class="col-md-3 text-right">
                    <a href="export.php?product=<?= $product ?>&date=<?= $date ?>&serial=<?= $serial ?>&lot=<?= $lot ?>"
                        class="btn btn-success">
                        Export Excel
                    </a>
                </div>

            </form>
        </div>

        <div class="card shadow-sm">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover trace-table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Lot Product</th>
                            <th>Ref Number</th>
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

                        <?php $no = 1;
                        foreach ($grouped as $serial_no => $refs): ?>

                            <tr class="table-primary">
                                <td><?= $no++ ?></td>
                                <td colspan="13"><b><?= $serial_no ?></b></td>
                            </tr>

                            <?php foreach ($refs as $r): ?>

                                <tr class="table-warning">
                                    <td colspan="2"></td>
                                    <td><b><?= $r['ref_number'] ?></b></td>
                                    <td><?= $r['line_name'] ?: $r['line_id'] ?></td>
                                    <td><?= $r['operator'] ?></td>
                                    <td><?= $r['created_at'] ?></td>
                                    <td><?= $r['location_name'] ?? 'Warehouse' ?></td>
                                    <td><?= $r['out_date'] ?: '-' ?></td>
                                    <td colspan="6"></td>
                                </tr>

                                <?php
                                $m = $pdo->prepare("
SELECT 
    dpr.part_code,
    pt.part_name,
    dpr.used_qty,
    dpr.lot_no,
    dpt.incoming_date,
    sup.name_supplier

FROM tbl_detail_production dpr

-- 🔥 IDENTITY MATERIAL (WAJIB)
JOIN tbl_detail_part dpt 
    ON dpt.ref_number = dpr.ref_number

-- 🔥 MASTER PART (DISPLAY)
LEFT JOIN tbl_part pt 
    ON pt.id_part = dpt.part_id

-- 🔥 SUPPLIER BENAR
LEFT JOIN tbl_supplier sup 
    ON sup.id_supplier = pt.supplier

WHERE dpr.serial_no=? 
AND dpr.ref_product=?
");

                                $m->execute([$r['serial_no'], $r['ref_number']]);
                                $mats = $m->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($mats as $mat):
                                ?>

                                    <tr>
                                        <td></td>
                                        <td colspan="7"></td>
                                        <td><?= $mat['part_code'] ?></td>
                                        <td><?= $mat['part_name'] ?></td>
                                        <td><?= $mat['lot_no'] ?></td>
                                        <td><?= $mat['used_qty'] ?></td>
                                        <td><?= $mat['name_supplier'] ?></td>
                                        <td><?= $mat['incoming_date'] ?></td>
                                    </tr>

                        <?php endforeach;
                            endforeach;
                        endforeach; ?>

                        <?php if (!$grouped): ?>
                            <tr>
                                <td colspan="13" class="text-center text-muted">No data</td>
                            </tr>
                        <?php endif ?>

                    </tbody>
                </table>

                <div class="d-flex justify-content-between mt-3">
                    <div>Total Lot: <?= $totalRows ?></div>
                    <div>
                        <?php if ($page > 1): ?>
                            <a class="btn btn-sm btn-light"
                                href="?product=<?= $product ?>&date=<?= $date ?>&page=<?= $page - 1 ?>&serial=<?= $serial ?>&lot=<?= $lot ?>">
                                Prev
                            </a>
                        <?php endif; ?>

                        <a class="btn btn-sm btn-light"
                            href="?product=<?= $product ?>&date=<?= $date ?>&page=<?= $page + 1 ?>&serial=<?= $serial ?>&lot=<?= $lot ?>">
                            Next
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>