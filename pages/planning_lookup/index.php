<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['halaman'] = 'planning lookup';
$_SESSION['menu'] = 'production_planning';
$_SESSION['subHalaman'] = ' | Planning Lookup';

$date = $_GET['date'] ?? date('Y-m-d');

/* ===============================
   MASTER SHIFT
   =============================== */
$shiftRows = $pdo->query("SELECT shift,start FROM tbl_shift")->fetchAll(PDO::FETCH_ASSOC);
$shiftStart = [];
foreach ($shiftRows as $s) {
    $shiftStart[$s['shift']] = (int)$s['start'];
}

/* ===============================
   MASTER PRODUCT NAME
   =============================== */
$productRows = $pdo->query("
    SELECT part_code, part_name
    FROM tbl_part
")->fetchAll(PDO::FETCH_ASSOC);

$productNames = [];
foreach ($productRows as $p) {
    $productNames[$p['part_code']] = $p['part_name'];
}

/* ===============================
   DATA PLANNING
   =============================== */
$sql = "
SELECT
    l.line_name,
    pp.product_code,
    pp.shift,
    d.jam,
    d.qty
FROM tbl_production_planning pp
JOIN tbl_detail_production_planning d ON pp.pp_id = d.pp_id
JOIN tbl_line l ON pp.line_id = l.line_id
WHERE pp.production_date = :dt
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':dt' => $date]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
[line][shift][product][jam] = qty
*/
$data = [];

foreach ($rows as $r) {
    $data[$r['line_name']][$r['shift']][$r['product_code']][$r['jam']] = $r['qty'];
}

/* ===============================
   HELPER JAM
   =============================== */
function jamLabel($jam)
{
    if ($jam === 'OT') return 'OT';
    [$a, $b] = explode('-', $jam);
    return intval($a) . '-' . intval($b);
}

function jamStart($jam)
{
    if ($jam === 'OT') return 999;
    return (int)explode(':', $jam)[0];
}

function sortByShiftStart($a, $b, $start)
{
    if ($a === 'OT') return 1;
    if ($b === 'OT') return -1;

    $ja = jamStart($a);
    $jb = jamStart($b);

    $ra = ($ja < $start) ? $ja + 24 : $ja;
    $rb = ($jb < $start) ? $jb + 24 : $jb;

    return $ra <=> $rb;
}

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .lookup-table th {
        white-space: nowrap;
        font-size: .8rem;
        background: #f8f9fa
    }

    .lookup-table td {
        font-size: .85rem
    }

    .qty-zero {
        color: #bbb
    }

    .product-card {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 15px;
        margin-top: 15px
    }

    .shift-badge {
        background: #e8f2ff;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: .75rem
    }
</style>

<div class="content pt-0">
    <div class="container">

        <div class="card mb-5">
            <div class="card-body d-flex align-items-center">
                <form method="get" class="form-inline">
                    <label class="mr-3 font-weight-bolder">Tanggal</label>
                    <input type="date"
                        name="date"
                        value="<?= $date ?>"
                        class="form-control mr-3"
                        onclick="this.showPicker()">
                    <button class="btn btn-primary">Cari</button>
                </form>
            </div>
        </div>

        <?php foreach ($data as $line => $shifts): ?>

            <div class="card mb-7 shadow-sm">
                <div class="card-body">

                    <h4>Line <?= $line ?></h4>

                    <?php foreach ($shifts as $shift => $products): ?>

                        <div class="mt-4">
                            <span class="shift-badge font-weight-bolder">Shift <?= $shift ?></span>

                            <?php foreach ($products as $product => $detail):

                                $productName = $productNames[$product] ?? '-';
                                $hours = array_keys($detail);
                                $start = $shiftStart[$shift] ?? 0;

                                usort($hours, function ($a, $b) use ($start) {
                                    return sortByShiftStart($a, $b, $start);
                                });
                            ?>

                                <div class="product-card">

                                    <h5>
                                        Product : <?= $product ?>
                                        <span class="text-muted">- <?= $productName ?></span>
                                    </h5>

                                    <div style="overflow-x:auto" class="mt-2">
                                        <table class="table table-sm table-bordered lookup-table">

                                            <thead>
                                                <tr class="text-center">
                                                    <th>Jam</th>
                                                    <?php foreach ($hours as $h): ?>
                                                        <th><?= jamLabel($h) ?></th>
                                                    <?php endforeach ?>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr class="text-center">
                                                    <td><b>Qty</b></td>
                                                    <?php foreach ($hours as $h):
                                                        $q = $detail[$h] ?? 0;
                                                    ?>
                                                        <td class="<?= $q == 0 ? 'qty-zero' : '' ?>">
                                                            <?= $q ?>
                                                        </td>
                                                    <?php endforeach ?>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>

                                </div>

                            <?php endforeach ?>
                        </div>

                    <?php endforeach ?>

                </div>
            </div>

        <?php endforeach ?>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>