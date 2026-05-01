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
   DIAGRAM DATA
================================ */
$shiftSelected = $_GET['shift'] ?? 1;

$diagram = $pdo->prepare("
SELECT 
    l.line_name,
    pp.product_code,
    pr.part_name,
    pp.shift,
    SUM(dp.qty) as plan,
    SUM(dp.actual) as actual

FROM tbl_production_planning pp
JOIN tbl_detail_production_planning dp ON dp.pp_id = pp.pp_id
JOIN tbl_line l ON l.line_id = pp.line_id
LEFT JOIN tbl_part pr ON pr.part_code = pp.product_code

WHERE pp.production_date = CURDATE()
AND pp.shift = ?

GROUP BY l.line_name, pp.product_code, pr.part_name, pp.shift
ORDER BY l.line_name
");

$diagram->execute([$shiftSelected]);
$data = $diagram->fetchAll(PDO::FETCH_ASSOC);

/* GROUP */
$grouped = [];
foreach ($data as $d) {
    $grouped[$d['line_name']][] = $d;
}

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/aside.php';
require __DIR__ . '/../includes/navbar.php';
?>

<style>
    .enterprise-card {
        border-radius: 18px;
        padding: 18px;
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .kpi-number {
        font-size: 28px;
        font-weight: 700;
    }
</style>

<div class="content pt-0">
    <div class="container mt-5">

        <h2 class="mb-4 font-weight-bold">Traceability Dashboard</h2>

        <!-- KPI -->
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

        <!-- DIAGRAM -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="enterprise-card">

                    <div class="d-flex justify-content-between mb-3">
                        <h5>Production Diagram - Shift <?= $shiftSelected ?></h5>

                        <form method="GET">
                            <select name="shift" onchange="this.form.submit()" class="form-control">
                                <option value="1" <?= $shiftSelected == 1 ? 'selected' : '' ?>>Shift 1</option>
                                <option value="2" <?= $shiftSelected == 2 ? 'selected' : '' ?>>Shift 2</option>
                                <option value="3" <?= $shiftSelected == 3 ? 'selected' : '' ?>>Shift 3</option>
                            </select>
                        </form>
                    </div>

                    <div class="row">

                        <?php foreach ($grouped as $line => $products):

                            $totalPlan = array_sum(array_column($products, 'plan'));
                            $totalActual = array_sum(array_column($products, 'actual'));
                            $linePercent = $totalPlan > 0 ? ($totalActual / $totalPlan) * 100 : 0;
                            $lineColor = $linePercent >= 90 ? '#1cc88a' : ($linePercent >= 70 ? '#f6c23e' : '#e74a3b');

                        ?>

                            <div class="col-md-4 mb-4">
                                <div class="enterprise-card">

                                    <!-- LINE HEADER -->
                                    <div style="display:flex;justify-content:space-between;font-weight:700;">
                                        <span><?= $line ?></span>
                                        <span style="font-size:11px;color:#999">Line</span>
                                    </div>

                                    <!-- LINE PERFORMANCE -->
                                    <div style="font-size:11px;margin-top:4px;color:#666">
                                        <?= round($linePercent) ?>% (<?= $totalActual ?> / <?= $totalPlan ?>)
                                    </div>

                                    <div style="height:5px;background:#eee;border-radius:10px;margin:6px 0 10px;">
                                        <div style="width:<?= $linePercent ?>%;height:5px;background:<?= $lineColor ?>;border-radius:10px;"></div>
                                    </div>

                                    <!-- PRODUCT LIST -->
                                    <?php foreach ($products as $p):

                                        $percent = $p['plan'] > 0 ? ($p['actual'] / $p['plan']) * 100 : 0;
                                        $color = $percent >= 90 ? '#1cc88a' : ($percent >= 70 ? '#f6c23e' : '#e74a3b');

                                        if ($p['actual'] == 0) $color = '#e74a3b';

                                    ?>

                                        <div style="
    margin-top:8px;
    padding:10px;
    background:#f9fafc;
    border-radius:10px;
    border-left:4px solid <?= $color ?>;
">

                                            <div style="font-size:13px;font-weight:700">
                                                <?= $p['part_name'] ?? '-' ?>
                                            </div>

                                            <div style="font-size:11px;color:#888">
                                                <?= $p['product_code'] ?>
                                            </div>

                                            <div style="display:flex;justify-content:space-between;font-size:11px;margin-top:4px">
                                                <span>Actual</span>
                                                <span>Plan</span>
                                            </div>

                                            <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600">
                                                <span><?= $p['actual'] ?></span>
                                                <span><?= $p['plan'] ?></span>
                                            </div>

                                            <div style="height:6px;background:#eee;border-radius:10px;margin-top:4px">
                                                <div style="width:<?= $percent ?>%;height:6px;background:<?= $color ?>;border-radius:10px;"></div>
                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>