<?php
require_once __DIR__ . '/../../includes/config.php';

$partCode = $_GET['part_code'] ?? '';

if (!$partCode) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT 
        d.ref_number,
        d.lot_no,
        d.qty,
        d.remain,
        d.incoming_date,
        d.status,
        p.part_name
    FROM tbl_detail_part d
    JOIN tbl_part p ON p.part_code = d.part_code
    WHERE d.part_code = ? AND d.status = 'IN'
    ORDER BY d.incoming_date DESC
");
$stmt->execute([$partCode]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    header('Location: index.php');
    exit;
}

$partName = $rows[0]['part_name'];

$totalQty = 0;
$totalRemain = 0;

foreach ($rows as $r) {
    $totalQty += $r['qty'];
    $totalRemain += $r['remain'];
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
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <!-- HEADER INFO -->
        <div class="card summary-box mb-6 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"><?= $partCode ?></h4>
                    <small><?= $partName ?></small>
                </div>
                <div class="text-right">
                    <div>Total Incoming : <b><?= number_format($totalQty) ?></b></div>
                    <div>Total Remain : <b><?= number_format($totalRemain) ?></b></div>
                </div>
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
                            <th>Ref Number</th>
                            <th>Lot No</th>
                            <th>Qty Awal</th>
                            <th>Remain</th>
                            <th>Status</th>
                            <th>Incoming Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($rows as $r):

                            $badgeClass = 'badge-success';

                            if ($r['remain'] == 0) {
                                $badgeClass = 'badge-danger';
                            } elseif ($r['remain'] < ($r['qty'] * 0.3)) {
                                $badgeClass = 'badge-warning';
                            }

                        ?>

                            <tr>
                                <td><?= $r['ref_number'] ?></td>
                                <td><?= $r['lot_no'] ?></td>
                                <td><?= number_format($r['qty']) ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> badge-remain">
                                        <?= number_format($r['remain']) ?>
                                    </span>
                                </td>
                                <td><?= $r['status'] ?></td>
                                <td><?= date('d M Y H:i', strtotime($r['incoming_date'])) ?></td>
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