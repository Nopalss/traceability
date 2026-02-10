<?php
require_once __DIR__ . '/../../includes/config.php';

$ppCode = $_GET['pp_code'] ?? '';

if ($ppCode == '') {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| LOAD HEADER
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT pp.*, l.line_name
    FROM tbl_production_planning pp
    JOIN tbl_line l ON l.line_id = pp.line_id
    WHERE pp.pp_code=?
    ORDER BY pp.shift ASC
");
$stmt->execute([$ppCode]);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$plans) {
    header('Location: index.php');
    exit;
}

$productionDate = $plans[0]['production_date'];
$lineName = $plans[0]['line_name'];

/*
|--------------------------------------------------------------------------
| LOAD DETAIL PER SHIFT + PRODUCT
|--------------------------------------------------------------------------
*/
$data = [];

foreach ($plans as $p) {

    $d = $pdo->prepare("
        SELECT dp.*
        FROM tbl_detail_production_planning dp
        JOIN tbl_production_planning pp ON pp.pp_id = dp.pp_id
        JOIN tbl_shift s ON s.shift = pp.shift
        WHERE dp.pp_id=?
        ORDER BY
            CASE
                WHEN dp.jam='OT' THEN 999
                WHEN CAST(SUBSTRING_INDEX(dp.jam, ':', 1) AS UNSIGNED) >= s.start
                    THEN CAST(SUBSTRING_INDEX(dp.jam, ':', 1) AS UNSIGNED)
                ELSE CAST(SUBSTRING_INDEX(dp.jam, ':', 1) AS UNSIGNED)+24
            END
    ");

    $d->execute([$p['pp_id']]);

    $data[$p['shift']][] = [
        'product' => $p['product_code'],
        'qty'     => $p['qty'],
        'detail'  => $d->fetchAll(PDO::FETCH_ASSOC)
    ];
}

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .card-shift {
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .05)
    }

    .card-product {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px
    }

    .badge-shift {
        background: #4f46e5;
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px
    }
</style>

<div class="content pt-0">
    <div class="container">

        <div class="card mb-7">
            <div class="card-body">

                <h3 class="mb-3">Production Planning Detail</h3>

                <div class="row mb-5">
                    <div class="col-md-4"><b>PP Code</b><br><?= $ppCode ?></div>
                    <div class="col-md-4"><b>Date</b><br><?= $productionDate ?></div>
                    <div class="col-md-4"><b>Line</b><br><?= $lineName ?></div>
                </div>

                <?php
                $grandTotal = 0;
                foreach ($data as $shiftNo => $products):
                ?>

                    <div class="card card-shift p-5 mb-7">

                        <h5 class="mb-4">
                            <span class="badge-shift">Shift <?= $shiftNo ?></span>
                        </h5>

                        <?php
                        $shiftTotal = 0;
                        foreach ($products as $prod):
                            $shiftTotal += $prod['qty'];
                            $grandTotal += $prod['qty'];
                        ?>

                            <div class="card-product">

                                <h6 class="mb-3 font-weight-bolder">Product Code: <?= $prod['product'] ?></h6>

                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Jam</th>
                                            <th width="120">Qty</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php foreach ($prod['detail'] as $d): ?>

                                            <tr>
                                                <td><?= $d['jam'] ?></td>
                                                <td><?= $d['qty'] ?></td>
                                            </tr>

                                        <?php endforeach ?>

                                        <tr class="font-weight-bolder">
                                            <td>Total Product</td>
                                            <td><?= $prod['qty'] ?></td>
                                        </tr>

                                    </tbody>
                                </table>

                            </div>

                        <?php endforeach ?>

                        <hr>

                        <div class="text-right">
                            <b>Total Shift <?= $shiftNo ?> : <?= $shiftTotal ?></b>
                        </div>

                    </div>

                <?php endforeach ?>

                <div class="card p-5">
                    <h4 class="text-right">Grand Total : <?= $grandTotal ?></h4>
                </div>

                <div class="text-right mt-7">
                    <a href="<?= BASE_URL ?>pages/production_planning/" class="btn btn-outline-secondary">Back</a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>