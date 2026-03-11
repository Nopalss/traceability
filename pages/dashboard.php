<?php
require_once __DIR__ . '/../includes/config.php';
date_default_timezone_set('Asia/Jakarta');
$_SESSION['menu'] = 'dashboard';
$_SESSION['table'] = '';
$_SESSION['halaman'] = 'dashboard';
$_SESSION['subHalaman'] = '';

/* ===============================
   GLOBAL KPI DATA
================================ */
$kpi = $pdo->query("
SELECT 
    (SELECT SUM(total_qty) FROM tbl_production_planning 
     WHERE production_date = CURDATE()) as total_plan,

    (SELECT SUM(d.actual)
     FROM tbl_production_planning p
     LEFT JOIN tbl_detail_production_planning d ON p.pp_id = d.pp_id
     WHERE p.production_date = CURDATE()) as total_actual,

    (SELECT COUNT(*) FROM tbl_detail_product WHERE status='in') as wip,

    (SELECT COUNT(*) FROM tbl_detail_product WHERE status='out' 
     AND DATE(out_date)=CURDATE()) as out_today,

    (SELECT SUM(lost_qty) FROM tbl_material_loss 
     WHERE DATE(created_at)=CURDATE()) as loss_today,

    (SELECT COUNT(*) FROM tbl_active_material) as active_lot
")->fetch(PDO::FETCH_ASSOC);

$plan   = $kpi['total_plan'] ?? 0;
$actual = $kpi['total_actual'] ?? 0;
$ach    = ($plan > 0) ? round(($actual / $plan) * 100) : 0;

/* ===============================
   LINE PERFORMANCE
================================ */
$lines = $pdo->query("
SELECT 
    l.line_name,
    SUM(pp.total_qty) AS plan,
    (
        SELECT SUM(dp.actual)
        FROM tbl_production_planning p2
        JOIN tbl_detail_production_planning dp ON p2.pp_id = dp.pp_id
        WHERE p2.line_id = pp.line_id
          AND p2.production_date = CURDATE()
    ) AS actual
FROM tbl_production_planning pp
LEFT JOIN tbl_line l ON pp.line_id = l.line_id
WHERE pp.production_date = CURDATE()
GROUP BY pp.line_id
")->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   7 DAYS TREND
================================ */
$trend = $pdo->query("
SELECT DATE(created_at) as tgl,
       SUM(qty) as total
FROM tbl_production_output
WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY DATE(created_at)
ORDER BY tgl ASC
")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/aside.php';
require __DIR__ . '/../includes/navbar.php';
?>

<style>
    .enterprise-card {
        border-radius: 20px;
        padding: 20px;
        background: white;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
    }

    .kpi-number {
        font-size: 32px;
        font-weight: 700;
    }
</style>

<div class="content pt-0">
    <div class="container mt-5">

        <h2 class="mb-4 font-weight-bold">Traceability Dashboard</h2>

        <!-- KPI ROW -->
        <div class="row">
            <?php
            $cards = [
                ['Plan Today', $plan, 'primary'],
                ['Actual Today', $actual, 'success'],
                ['Achievement', $ach . '%', 'info'],
                ['WIP', $kpi['wip'], 'warning'],
                ['OUT Today', $kpi['out_today'], 'danger'],
                ['Loss Today', $kpi['loss_today'] ?? 0, 'danger'],
                ['Active Lots', $kpi['active_lot'], 'info']
            ];
            foreach ($cards as $c):
            ?>
                <div class="col-md-3 mb-4">
                    <div class="enterprise-card">
                        <small><?= $c[0] ?></small>
                        <div class="kpi-number text-<?= $c[2] ?>"><?= $c[1] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- CHART SECTION -->
        <div class="row mt-4">

            <div class="col-md-6 mb-4">
                <div class="enterprise-card">
                    <h5>Plan vs Actual Per Line</h5>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="enterprise-card">
                    <h5>Production Trend (Last 7 Days)</h5>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // =============================
    // LINE PLAN VS ACTUAL
    // =============================
    const lineLabels = <?= json_encode(array_column($lines, 'line_name')) ?>;
    const planData = <?= json_encode(array_map(fn($l) => (int)$l['plan'], $lines)) ?>;
    const actualData = <?= json_encode(array_map(fn($l) => (int)$l['actual'], $lines)) ?>;

    new Chart(document.getElementById('lineChart'), {
        type: 'bar',
        data: {
            labels: lineLabels,
            datasets: [{
                    label: 'Plan',
                    data: planData,
                    backgroundColor: '#4e73df'
                },
                {
                    label: 'Actual',
                    data: actualData,
                    backgroundColor: '#1cc88a'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // =============================
    // 7 DAYS TREND
    // =============================
    const trendLabels = <?= json_encode(array_column($trend, 'tgl')) ?>;
    const trendData = <?= json_encode(array_map(fn($t) => (int)$t['total'], $trend)) ?>;

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Production Output',
                data: trendData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>