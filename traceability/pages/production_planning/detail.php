<?php
require_once __DIR__ . '/../../includes/config.php';

$pp_code = $_GET['pp_code'] ?? '';
if (!$pp_code) die('Invalid PP Code');

// ================= GET DATA =================
$sql = "
SELECT 
    pp.pp_id,
    pp.shift,
    s.shift AS shift_name,
    pp.line_id,
    l.line_name,
    pp.product_code,
    pp.production_date,

    dpp.id AS detail_id,
    dpp.jam,
    dpp.qty,
    dpp.actual

FROM tbl_production_planning pp
JOIN tbl_line l ON l.line_id = pp.line_id
JOIN tbl_shift s ON s.shift_id = pp.shift
LEFT JOIN tbl_detail_production_planning dpp 
    ON dpp.pp_id = pp.pp_id

WHERE pp.pp_code = ?
ORDER BY pp.shift, pp.line_id, pp.product_code, dpp.id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$pp_code]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================= GROUPING =================
$data = [];

foreach ($rows as $r) {
    $data[$r['shift']][$r['line_id']][$r['product_code']]['info'] = [
        'pp_id' => $r['pp_id'],
        'line_name' => $r['line_name'],
        'shift_name' => $r['shift_name']
    ];

    if ($r['detail_id']) {
        $data[$r['shift']][$r['line_id']][$r['product_code']]['detail'][] = [
            'id' => $r['detail_id'],
            'jam' => $r['jam'],
            'qty' => $r['qty'],
            'actual' => $r['actual']
        ];
    }
}

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .card-modern {
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        margin-bottom: 20px;
    }

    .shift-title {
        font-weight: bold;
        font-size: 18px;
        color: #2563eb;
    }

    .line-title {
        font-weight: bold;
        color: #16a34a;
        margin-top: 15px;
    }

    .product-box {
        background: #f9fafb;
        border-radius: 12px;
        padding: 15px;
        margin-top: 10px;
    }

    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-total {
        font-size: 13px;
        color: #374151;
    }

    .done {
        background: #dcfce7 !important;
    }

    .not-done {
        background: #fee2e2 !important;
    }
</style>

<div class="container mt-4">
    <h3>Production Planning Detail</h3>

    <?php foreach ($data as $shiftId => $lines): ?>
        <div class="card card-modern p-3">

            <div class="shift-title">
                Shift <?= $lines[array_key_first($lines)][array_key_first($lines[array_key_first($lines)])]['info']['shift_name'] ?>
            </div>

            <?php foreach ($lines as $lineId => $products): ?>

                <div class="line-title">
                    Line: <?= $products[array_key_first($products)]['info']['line_name'] ?>
                </div>

                <?php foreach ($products as $productCode => $p): ?>

                    <?php
                    $productPlanTotal = 0;
                    $productActualTotal = 0;

                    foreach ($p['detail'] as $d) {
                        $productPlanTotal += $d['qty'];
                        $productActualTotal += $d['actual'];
                    }
                    ?>

                    <div class="product-box">

                        <div class="product-header">
                            <strong>Product: <?= $productCode ?></strong>

                            <div class="product-total">
                                Plan: <b><?= $productPlanTotal ?></b> |
                                Actual: <b><?= $productActualTotal ?></b>
                            </div>
                        </div>

                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>Jam</th>
                                    <th>Plan</th>
                                    <th>Actual</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($p['detail'] as $d): ?>
                                    <?php $done = $d['actual'] >= $d['qty']; ?>
                                    <tr class="<?= $done ? 'done' : 'not-done' ?>">
                                        <td><?= $d['jam'] ?></td>

                                        <td>
                                            <input type="number"
                                                class="form-control qty-input"
                                                data-id="<?= $d['id'] ?>"
                                                value="<?= $d['qty'] ?>">
                                        </td>

                                        <td>
                                            <input type="number"
                                                class="form-control actual-input"
                                                data-id="<?= $d['id'] ?>"
                                                value="<?= $d['actual'] ?>">
                                        </td>

                                        <td>
                                            <?= $done ? '✅ Done' : '❌ Not Yet' ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>

                            </tbody>
                        </table>

                    </div>
                <?php endforeach ?>

            <?php endforeach ?>

        </div>
    <?php endforeach ?>

    <button class="btn btn-success mt-3" id="saveBtn">Save Changes</button>

</div>

<script>
    let changes = {};

    $(document).on('input', '.qty-input, .actual-input', function() {
        let id = $(this).data('id');

        let row = $(this).closest('tr');

        let qty = row.find('.qty-input').val();
        let actual = row.find('.actual-input').val();

        changes[id] = {
            qty,
            actual
        };
    });

    $('#saveBtn').click(function() {

        if (Object.keys(changes).length === 0) {
            alert('Tidak ada perubahan');
            return;
        }

        $.post("update_detail.php", {
            data: changes
        }, function(res) {
            alert('Berhasil disimpan');
            location.reload();
        }, 'json');

    });
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>