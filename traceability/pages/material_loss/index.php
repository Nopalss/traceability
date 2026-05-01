<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['menu'] = 'material_loss';
$_SESSION['table'] = 'material_loss';
$_SESSION['halaman'] = 'Material Loss Report';
$_SESSION['subHalaman'] = '';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';


/* ==============================
   FILTER DATE
============================== */

$start = $_GET['start'] ?? date('Y-m-01');
$end   = $_GET['end'] ?? date('Y-m-d');


$stmt = $pdo->prepare("
SELECT *
FROM tbl_material_loss
WHERE DATE(created_at) BETWEEN ? AND ?
ORDER BY created_at DESC
");

$stmt->execute([$start, $end]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==============================
   SUMMARY
============================== */

$summary = $pdo->prepare("
SELECT 
COUNT(*) total_record,
SUM(lost_qty) total_loss
FROM tbl_material_loss
WHERE DATE(created_at) BETWEEN ? AND ?
");

$summary->execute([$start, $end]);
$sum = $summary->fetch(PDO::FETCH_ASSOC);

$totalRecord = $sum['total_record'] ?? 0;
$totalLoss   = $sum['total_loss'] ?? 0;

?>


<style>
    .loss-card {
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        padding: 20px;
        background: #fff;
    }

    .summary-card {
        border-radius: 14px;
        padding: 20px;
        color: white;
    }

    .bg-loss {
        background: linear-gradient(135deg, #ff6b6b, #ff8787);
    }

    .bg-record {
        background: linear-gradient(135deg, #339af0, #74c0fc);
    }

    .table-modern thead {
        background: #f1f3f5;
        font-weight: 600;
    }

    .badge-loss {
        background: #ffe3e3;
        color: #c92a2a;
        padding: 5px 10px;
        border-radius: 12px;
    }
</style>



<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="d-flex flex-column-fluid">

        <div class="container">


            <!-- =========================
SUMMARY
========================= -->

            <div class="row mb-6">

                <div class="col-md-6">

                    <div class="summary-card bg-loss">

                        <h5>Total Material Loss</h5>
                        <h2><?= number_format($totalLoss) ?></h2>

                    </div>

                </div>


                <div class="col-md-6">

                    <div class="summary-card bg-record">

                        <h5>Total Record</h5>
                        <h2><?= number_format($totalRecord) ?></h2>

                    </div>

                </div>

            </div>




            <!-- =========================
FILTER
========================= -->

            <div class="card card-custom mb-6">

                <div class="card-body">

                    <form method="GET">

                        <div class="row">

                            <div class="col-md-4">

                                <label>Start Date</label>
                                <input type="date" name="start" class="form-control" value="<?= $start ?>">

                            </div>


                            <div class="col-md-4">

                                <label>End Date</label>
                                <input type="date" name="end" class="form-control" value="<?= $end ?>">

                            </div>


                            <div class="col-md-4 d-flex align-items-end">

                                <button class="btn btn-primary mr-2">
                                    Filter
                                </button>

                                <a href="" class="btn btn-light">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>




            <!-- =========================
TABLE
========================= -->

            <div class="card card-custom loss-card">

                <div class="card-header">

                    <h3 class="card-title">
                        Material Loss Report
                    </h3>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-modern">

                            <thead>

                                <tr>

                                    <th>Date</th>
                                    <th>Part Code</th>
                                    <th>Loss Qty</th>
                                    <th>Old Remain</th>
                                    <th>Reason</th>
                                    <th>Operator</th>
                                    <th>Ref Number</th>
                                    <th>Shift</th>
                                    <th>Line</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($data as $row): ?>

                                    <tr>

                                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>

                                        <td><?= $row['part_code'] ?></td>

                                        <td>
                                            <span class="badge-loss">
                                                <?= $row['lost_qty'] ?>
                                            </span>
                                        </td>

                                        <td><?= $row['old_remain'] ?></td>

                                        <td><?= $row['reason'] ?></td>

                                        <td><?= $row['operator'] ?></td>

                                        <td><?= $row['ref_number'] ?></td>

                                        <td><?= $row['shift'] ?></td>

                                        <td><?= $row['line_id'] ?></td>

                                    </tr>

                                <?php endforeach ?>

                                <?php if (empty($data)): ?>

                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            No Data
                                        </td>
                                    </tr>

                                <?php endif ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>



<?php
require __DIR__ . '/../../includes/footer.php';
?>