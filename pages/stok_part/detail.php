<?php
require_once __DIR__ . '/../../includes/config.php';

$partCode = $_GET['part_code'] ?? '';
$search   = $_GET['search'] ?? '';

if (!$partCode) {
    header('Location: index.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| GET PART NAME
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
SELECT part_name 
FROM tbl_part 
WHERE part_code = ?
");
$stmt->execute([$partCode]);
$part = $stmt->fetch();

if (!$part) {
    header('Location: index.php');
    exit;
}

$partName = $part['part_name'];

/*
|--------------------------------------------------------------------------
| GET LOT SUMMARY
|--------------------------------------------------------------------------
*/

$sql = "
SELECT 
    lot_no,
    SUM(qty) as total_qty,
    SUM(remain) as total_remain
FROM tbl_detail_part
WHERE part_code = ?
AND status = 'IN'
";

$params = [$partCode];

if ($search) {
    $sql .= " AND (lot_no LIKE ? OR ref_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " GROUP BY lot_no ORDER BY MAX(incoming_date) DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| GET ALL DETAIL DATA
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT *
FROM tbl_detail_part
WHERE part_code = ?
AND status = 'IN'
ORDER BY incoming_date DESC
");
$stmt->execute([$partCode]);
$allRows = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| GROUP DETAIL BY LOT
|--------------------------------------------------------------------------
*/

$detailByLot = [];

foreach ($allRows as $r) {

    $detailByLot[$r['lot_no']][] = $r;
}


require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .summary-box {
        border-radius: 12px;
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: white;
    }

    .badge-remain {
        font-size: 13px;
        padding: 6px 10px;
    }

    .lot-row {
        cursor: pointer;
    }

    .detail-table {
        background: #f8f9fa;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <!-- HEADER -->
        <div class="card summary-box mb-6 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="mb-1"><?= $partCode ?></h4>
                    <small><?= $partName ?></small>
                </div>

                <div class="text-right">
                    <div>Total Lot : <b><?= count($lots) ?></b></div>
                </div>

            </div>
        </div>


        <!-- SEARCH -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">

                <form method="GET">

                    <input type="hidden" name="part_code" value="<?= $partCode ?>">

                    <div class="row">

                        <div class="col-md-4">
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search Lot No / Ref Number"
                                value="<?= htmlspecialchars($search) ?>">
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary">
                                Search
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>


        <!-- TABLE -->
        <div class="card shadow-sm">

            <div class="card-header">
                <h5>Detail Lot Material</h5>
            </div>

            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="thead-light">

                        <tr>
                            <th width="200">Lot No</th>
                            <th width="150">Total Qty</th>
                            <th width="150">Total Remain</th>
                            <th width="100">Detail</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($lots as $lot): ?>

                            <tr class="lot-row">

                                <td>
                                    <b><?= $lot['lot_no'] ?></b>
                                </td>

                                <td><?= number_format($lot['total_qty']) ?></td>

                                <td>

                                    <?php

                                    $badge = "badge-success";

                                    if ($lot['total_remain'] == 0) {
                                        $badge = "badge-danger";
                                    } elseif ($lot['total_remain'] < ($lot['total_qty'] * 0.3)) {
                                        $badge = "badge-warning";
                                    }

                                    ?>

                                    <span class="badge <?= $badge ?> badge-remain">
                                        <?= number_format($lot['total_remain']) ?>
                                    </span>

                                </td>

                                <td>

                                    <button
                                        class="btn btn-sm btn-info"
                                        data-toggle="collapse"
                                        data-target="#lot_<?= md5($lot['lot_no']) ?>">

                                        Detail

                                    </button>

                                </td>

                            </tr>


                            <tr class="collapse detail-table" id="lot_<?= md5($lot['lot_no']) ?>">

                                <td colspan="4">

                                    <table class="table table-sm table-bordered">

                                        <thead>

                                            <tr>
                                                <th>Ref Number</th>
                                                <th>Qty</th>
                                                <th>Remain</th>
                                                <th>Status</th>
                                                <th>Incoming Date</th>
                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php foreach ($detailByLot[$lot['lot_no']] ?? [] as $d): ?>

                                                <tr>

                                                    <td><?= $d['ref_number'] ?></td>

                                                    <td><?= number_format($d['qty']) ?></td>

                                                    <td><?= number_format($d['remain']) ?></td>

                                                    <td><?= $d['status'] ?></td>

                                                    <td><?= date('d M Y H:i', strtotime($d['incoming_date'])) ?></td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <div class="mt-4">
            <a href="index.php" class="btn btn-light">← Kembali</a>
        </div>


    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>