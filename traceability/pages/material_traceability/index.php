<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

$_SESSION['menu'] = 'material_traceability';
$_SESSION['halaman'] = 'material traceability';

$lot = $_GET['lot'] ?? '';

$data = [];
$summary = [];

if ($lot) {

    /* ================= MAIN TRACE ================= */
    $sql = "
SELECT
    dpr.lot_no,
    dpr.ref_product,
    dpr.part_code,

    dpt.part_id,  -- 🔑 INI KUNCI
    pt.part_name,

    dp.serial_no,
    dp.product_code,
    prod.part_name AS product_name,

    dp.operator,
    dp.created_at,
    dp.out_date,

    line.line_name,
    dpr.used_qty,

    loc.name_supplier AS location_name

FROM tbl_detail_production dpr

-- 🔥 JOIN KE DETAIL PART (INI YANG LO MAKSUD)
JOIN tbl_detail_part dpt
    ON dpt.ref_number = dpr.ref_number

-- 🔥 BARU KE PRODUCT
JOIN tbl_detail_product dp
    ON dp.serial_no = dpr.serial_no
    AND dp.ref_number = dpr.ref_product

-- 🔥 PART MASTER (AMAN)
LEFT JOIN tbl_part pt 
    ON pt.id_part = dpt.part_id

LEFT JOIN tbl_part prod ON prod.part_code = dp.product_code
LEFT JOIN tbl_line line ON line.line_id = dp.line_id
LEFT JOIN tbl_supplier loc ON loc.id_supplier = dp.location

WHERE dpr.lot_no LIKE ?

ORDER BY dp.created_at
";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$lot%"]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /* ================= SUMMARY ================= */
    $sqlSummary = "
    SELECT
        dpt.ref_number,
        dpt.part_code,
        pt.part_name,
        dpt.qty,
        dpt.remain,
        dpt.incoming_date,
        sup.name_supplier,

        (
            SELECT SUM(used_qty)
            FROM tbl_detail_production
            WHERE lot_no = dpt.lot_no
            AND part_code = dpt.part_code
        ) AS total_used,

        (
            SELECT SUM(lost_qty)
            FROM tbl_material_loss
            WHERE ref_number = dpt.ref_number
        ) AS total_loss

    FROM tbl_detail_part dpt

    LEFT JOIN tbl_part pt ON pt.part_code = dpt.part_code
    LEFT JOIN tbl_supplier sup ON sup.id_supplier = pt.supplier

    WHERE dpt.lot_no = ?
    LIMIT 1
    ";

    $s = $pdo->prepare($sqlSummary);
    $s->execute([$lot]);
    $summary = $s->fetch(PDO::FETCH_ASSOC);
}
?>

<style>
    .trace-card {
        border-radius: 12px;
    }

    .trace-header {
        background: linear-gradient(135deg, #6f42c1, #8f63ff);
        color: white;
        padding: 20px;
        border-radius: 10px;
    }

    .summary-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-top: 15px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid pt-0">
    <div class="container">

        <div class="card shadow-sm trace-card">

            <div class="card-header d-flex justify-content-between">
                <h5>Material Trace</h5>

                <?php if ($lot && $data) { ?>
                    <a href="export_material_trace.php?lot=<?= urlencode($lot) ?>" class="btn btn-success btn-sm">
                        Export Excel
                    </a>
                <?php } ?>
            </div>

            <div class="card-body">

                <form method="GET" class="row mb-4">
                    <div class="col-md-4">
                        <input type="text" name="lot" value="<?= htmlspecialchars($lot) ?>" class="form-control" placeholder="Search Material Lot">
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary">Search</button>
                        <a href="material_trace.php" class="btn btn-secondary">Reset</a>
                    </div>
                </form>

                <?php if ($lot && $summary) { ?>

                    <div class="trace-header">
                        <h5>Material Lot : <?= $lot ?></h5>
                        Part : <?= $summary['part_code'] ?> - <?= $summary['part_name'] ?>
                    </div>

                    <div class="row summary-box">

                        <div class="col-md-3">
                            <b>Supplier</b><br>
                            <?= $summary['name_supplier'] ?>
                        </div>

                        <div class="col-md-3">
                            <b>Incoming Date</b><br>
                            <?= date("d M Y", strtotime($summary['incoming_date'])) ?>
                        </div>

                        <div class="col-md-3">
                            <b>Ref Number</b><br>
                            <?= $summary['ref_number'] ?>
                        </div>

                        <div class="col-md-3">
                            <b>Qty</b><br>
                            <?= $summary['qty'] ?>
                        </div>

                        <div class="col-md-3 mt-3">
                            <b>Remain</b><br>
                            <?= $summary['remain'] ?>
                        </div>

                        <div class="col-md-3 mt-3">
                            <b>Total Used</b><br>
                            <?= $summary['total_used'] ?? 0 ?>
                        </div>

                        <div class="col-md-3 mt-3">
                            <b>Material Loss</b><br>
                            <?= $summary['total_loss'] ?? 0 ?>
                        </div>

                        <div class="col-md-3 mt-3">
                            <b>NG Product</b><br>
                            0
                        </div>

                    </div>

                    <div class="table-responsive mt-4">

                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Lot Product</th>
                                    <th>Product</th>
                                    <th>Ref Product</th>
                                    <th>Line</th>
                                    <th>Operator</th>
                                    <th>Created At</th>
                                    <th>Location</th>
                                    <th>Out Date</th>
                                    <th>Used Qty</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($data as $r) { ?>

                                    <tr>

                                        <td>
                                            <b>
                                                <a href="<?= BASE_URL ?>pages/production_traceability/detail.php?product=<?= $r['product_code'] ?>&date=<?= date('Y-m-d', strtotime($r['created_at'])) ?>&serial=<?= $r['serial_no'] ?>">
                                                    <?= $r['serial_no'] ?>
                                                </a>
                                            </b>
                                        </td>

                                        <td><?= $r['product_code'] ?> - <?= $r['product_name'] ?></td>
                                        <td><?= $r['ref_product'] ?></td>
                                        <td><?= $r['line_name'] ?></td>
                                        <td><?= $r['operator'] ?></td>
                                        <td><?= $r['created_at'] ?></td>
                                        <td><?= $r['location_name'] ?? 'Warehouse' ?></td>
                                        <td><?= $r['out_date'] ?></td>
                                        <td><?= $r['used_qty'] ?></td>

                                    </tr>

                                <?php } ?>

                            </tbody>
                        </table>

                    </div>

                <?php } elseif ($lot) {
                    echo "<div class='alert alert-warning'>Material tidak dipakai produksi</div>";
                } ?>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>