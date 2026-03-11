<?php
require_once __DIR__ . '/../includes/config.php';

$_SESSION['halaman'] = 'Dashboard';
$_SESSION['menu'] = 'production';
$_SESSION['subHalaman'] = '';

date_default_timezone_set('Asia/Jakarta');
$today = date('Y-m-d');

/* =========================
   SUMMARY QUERIES
========================= */

// Incoming today
$incomingToday = $pdo->query("
    SELECT COUNT(*) FROM tbl_detail_part
    WHERE DATE(incoming_date) = CURDATE()
")->fetchColumn();

// Active material
$activeMaterial = $pdo->query("
    SELECT COUNT(*) FROM tbl_active_material
")->fetchColumn();

// Production today
$productionToday = $pdo->query("
    SELECT SUM(qty) FROM tbl_production_output
    WHERE DATE(created_at) = CURDATE()
")->fetchColumn();

// Product OUT today
$outToday = $pdo->query("
    SELECT COUNT(*) FROM tbl_detail_product
    WHERE status = 'out'
      AND DATE(out_date) = CURDATE()
")->fetchColumn();

// Material loss today
$lossToday = $pdo->query("
    SELECT SUM(lost_qty) FROM tbl_material_loss
    WHERE DATE(created_at) = CURDATE()
")->fetchColumn();

// Planning progress today
$planning = $pdo->query("
    SELECT SUM(total_qty) as total_plan,
           SUM(d.actual) as total_actual
    FROM tbl_production_planning p
    LEFT JOIN tbl_detail_production_planning d ON p.pp_id = d.pp_id
    WHERE p.production_date = CURDATE()
")->fetch(PDO::FETCH_ASSOC);

$plan  = $planning['total_plan'] ?? 0;
$actual = $planning['total_actual'] ?? 0;
$progress = ($plan > 0) ? round(($actual / $plan) * 100) : 0;

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/aside.php';
require __DIR__ . '/../includes/navbar.php';
?>

<style>
    .dashboard-card {
        border-radius: 18px;
        padding: 20px;
        background: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
    }

    .progress {
        height: 10px;
        border-radius: 10px;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        border-radius: 20px;
        padding: 25px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid pt-0">
    <div class="container mt-5">

        <h3 class="mb-4 font-weight-bold">Traceability Dashboard</h3>

        <!-- SUMMARY CARDS -->
        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <small>Incoming Today</small>
                    <div class="stat-number text-primary"><?= $incomingToday ?></div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <small>Active Material</small>
                    <div class="stat-number text-success"><?= $activeMaterial ?></div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <small>Production Today</small>
                    <div class="stat-number text-info"><?= $productionToday ?? 0 ?></div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <small>Product OUT Today</small>
                    <div class="stat-number text-danger"><?= $outToday ?></div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <small>Material Loss Today</small>
                    <div class="stat-number text-warning"><?= $lossToday ?? 0 ?></div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="dashboard-card">
                    <small>Planning Progress</small>
                    <div class="stat-number"><?= $progress ?>%</div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success"
                            style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- TRACEABILITY SEARCH -->
        <div class="gradient-bg mt-4">
            <h5>Quick Traceability Check</h5>
            <div class="row mt-3">
                <div class="col-md-6">
                    <input type="text" id="trace_serial"
                        class="form-control"
                        placeholder="Input Serial Number">
                </div>
                <div class="col-md-3">
                    <button id="btn-trace" class="btn btn-light btn-block">
                        Trace Now
                    </button>
                </div>
            </div>
            <div id="trace-result" class="mt-3"></div>
        </div>

    </div>
</div>

<script>
    document.getElementById('btn-trace').addEventListener('click', function() {

        let serial = document.getElementById('trace_serial').value.trim();
        if (!serial) return;

        fetch("<?= BASE_URL ?>controllers/dashboard/trace-serial.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    serial_no: serial
                })
            })
            .then(res => res.json())
            .then(res => {
                let box = document.getElementById('trace-result');

                if (res.success) {
                    box.innerHTML =
                        `<div class="alert alert-success">
                    Product Code: <b>${res.data.product_code}</b><br>
                    Status: <b>${res.data.status}</b><br>
                    Location: <b>${res.data.location}</b>
                </div>`;
                } else {
                    box.innerHTML =
                        `<div class="alert alert-danger">${res.message}</div>`;
                }
            });

    });
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>