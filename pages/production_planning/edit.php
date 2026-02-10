<?php
require_once __DIR__ . '/../../includes/config.php';

$ppCode = $_GET['pp_code'] ?? '';

if ($ppCode == '') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM tbl_production_planning WHERE pp_code=? ORDER BY shift ASC");
$stmt->execute([$ppCode]);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$plans) {
    header('Location: index.php');
    exit;
}

$productionDate = $plans[0]['production_date'];
$lineId = $plans[0]['line_id'];

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
        WHEN CAST(SUBSTRING_INDEX(dp.jam, ':', 1) AS UNSIGNED) >= s.start THEN
            CAST(SUBSTRING_INDEX(dp.jam, ':', 1) AS UNSIGNED)
        ELSE
            CAST(SUBSTRING_INDEX(dp.jam, ':', 1) AS UNSIGNED) + 24
    END
");
    $d->execute([$p['pp_id']]);

    $data[$p['shift']][] = [
        'product_code' => $p['product_code'],
        'detail' => $d->fetchAll(PDO::FETCH_ASSOC)
    ];
}

$parts = $pdo->query("SELECT part_code, part_name FROM tbl_part ORDER BY part_code ASC")->fetchAll(PDO::FETCH_ASSOC);
$lines = $pdo->query("SELECT line_id, line_name FROM tbl_line ORDER BY line_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$shifts = $pdo->query("SELECT * FROM tbl_shift ORDER BY shift ASC")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .shift-card {
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, .05)
    }

    .product-card {
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
        position: relative
    }

    .remove-product {
        position: absolute;
        right: 10px;
        top: 10px
    }
</style>

<div class="content pt-0">
    <div class="container">
        <div class="card">
            <div class="card-body">

                <h3>Edit Production Planning</h3>

                <form method="post" action="<?= BASE_URL ?>controllers/production_planning/edit.php">

                    <input type="hidden" name="pp_code" value="<?= $ppCode ?>">

                    <label>Date</label>
                    <input type="date"
                        name="production_date"
                        class="form-control"
                        value="<?= $productionDate ?>"
                        required
                        onkeydown="return false">

                    <label class="mt-3">Line</label>
                    <select name="line_id" class="form-control">
                        <?php foreach ($lines as $l): ?>
                            <option value="<?= $l['line_id'] ?>" <?= $l['line_id'] == $lineId ? 'selected' : '' ?>>
                                <?= $l['line_name'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>

                    <hr>

                    <div id="shiftWrapper">

                        <?php foreach ($data as $shiftNo => $products): ?>

                            <div class="card shift-card p-5 mt-5" data-shift="<?= $shiftNo ?>">
                                <h5>Shift <?= $shiftNo ?></h5>

                                <div class="products">

                                    <?php foreach ($products as $pi => $prod): ?>

                                        <div class="product-card">

                                            <button type="button" class="btn btn-sm btn-danger remove-product">×</button>

                                            <select name="product_code[<?= $shiftNo ?>][]" class="form-control mb-3" required>
                                                <?php foreach ($parts as $p): ?>
                                                    <option value="<?= $p['part_code'] ?>" <?= $p['part_code'] == $prod['product_code'] ? 'selected' : '' ?>>
                                                        <?= $p['part_code'] ?> - <?= $p['part_name'] ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>

                                            <table class="table table-sm">

                                                <?php $total = 0;
                                                foreach ($prod['detail'] as $d): $total += $d['qty']; ?>

                                                    <tr>
                                                        <td><?= $d['jam'] ?></td>
                                                        <td>
                                                            <input type="number" min="0" class="form-control qty" name="qty[<?= $shiftNo ?>][<?= $pi ?>][]" value="<?= $d['qty'] ?>">
                                                            <input type="hidden" name="jam[<?= $shiftNo ?>][<?= $pi ?>][]" value="<?= $d['jam'] ?>">
                                                        </td>
                                                    </tr>

                                                <?php endforeach ?>

                                                <tr>
                                                    <td><b>Total</b></td>
                                                    <td><b class="total"><?= $total ?></b></td>
                                                </tr>

                                            </table>
                                        </div>

                                    <?php endforeach ?>

                                </div>

                                <button type="button" class="btn btn-sm btn-primary addProduct mt-3">+ Product</button>
                            </div>

                        <?php endforeach ?>

                    </div>

                    <div class="text-right mt-7">
                        <a href="<?= BASE_URL ?>pages/production_planning/" class="btn btn-outline-danger">Batal</a>
                        <button class="btn btn-success">Update</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let shifts = <?= json_encode($shifts) ?>;

    /* DATE */
    $('input[type="date"]').on('click focus', function() {
        this.showPicker();
    });

    /* DISABLE NUMBER SCROLL + ARROW */
    $(document).on('wheel', 'input[type=number]', function() {
        $(this).blur();
    });

    $(document).on('keydown', 'input[type=number]', function(e) {
        if (e.which == 38 || e.which == 40) e.preventDefault();
    });

    $(document).on('click', '.addProduct', function() {

        let card = $(this).closest('.shift-card');
        let shiftNo = card.data('shift');
        let shift = shifts.find(s => s.shift == shiftNo);
        let hours = [];

        if (shift.start < shift.end) {
            for (let i = shift.start; i < shift.end; i++) hours.push(i);
        } else {
            for (let i = shift.start; i < 24; i++) hours.push(i);
            for (let i = 0; i < shift.end; i++) hours.push(i);
        }

        let idx = card.find('.product-card').length;

        let html = `<div class="product-card">
<button type="button" class="btn btn-sm btn-danger remove-product">×</button>

<select name="product_code[${shiftNo}][]" class="form-control mb-3">
<option value="">Select</option>
<?php foreach ($parts as $p): ?>
<option value="<?= $p['part_code'] ?>"><?= $p['part_code'] ?> - <?= $p['part_name'] ?></option>
<?php endforeach ?>
</select>

<table class="table table-sm">`;

        hours.forEach(h => {
            let n = (h + 1) % 24;
            html += `
<tr>
<td>${h}:00-${n}:00</td>
<td>
<input type="number" min="0" class="form-control qty" name="qty[${shiftNo}][${idx}][]" value="0">
<input type="hidden" name="jam[${shiftNo}][${idx}][]" value="${h}:00-${n}:00">
</td>
</tr>`;
        });

        html += `
<tr>
<td><b>Overtime</b></td>
<td>
<input type="number" min="0" class="form-control qty" name="qty[${shiftNo}][${idx}][]" value="0">
<input type="hidden" name="jam[${shiftNo}][${idx}][]" value="OT">
</td>
</tr>

<tr><td><b>Total</b></td><td><b class="total">0</b></td></tr>
</table></div>`;

        card.find('.products').append(html);

    });

    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-card').remove();
    });

    $(document).on('input', '.qty', function() {

        if ($(this).val() < 0) $(this).val(0);

        let box = $(this).closest('.product-card');
        let sum = 0;

        box.find('.qty').each(function() {
            sum += parseInt($(this).val()) || 0;
        });

        box.find('.total').text(sum);

    });
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>