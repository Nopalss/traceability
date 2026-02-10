<?php
require_once __DIR__ . '/../../includes/config.php';

$sql = "SELECT part_code, part_name FROM tbl_part ORDER BY part_code ASC";
$parts = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT line_id, line_name FROM tbl_line ORDER BY line_name ASC";
$lines = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM tbl_shift ORDER BY shift ASC";
$shifts = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

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

                <h3 class="mb-5">Production Planning</h3>

                <form method="post" action="<?= BASE_URL ?>controllers/production_planning/create.php">

                    <label>Date</label>
                    <input type="date"
                        name="production_date"
                        class="form-control"
                        required
                        min="<?= date('Y-m-d') ?>"
                        onkeydown="return false">

                    <label class="mt-3">Line</label>
                    <select name="line_id" class="form-control" required>
                        <option value="">Select</option>
                        <?php foreach ($lines as $l): ?>
                            <option value="<?= $l['line_id'] ?>"><?= $l['line_name'] ?></option>
                        <?php endforeach ?>
                    </select>

                    <label class="mt-3">Jumlah Shift</label>
                    <select id="shiftCount" class="form-control" required>
                        <option value="">Select</option>
                        <?php foreach ($shifts as $s): ?>
                            <option value="<?= $s['shift'] ?>"><?= $s['shift'] ?></option>
                        <?php endforeach ?>
                    </select>

                    <hr>

                    <div id="shiftWrapper"></div>

                    <div class="text-right mt-7">
                        <a href="<?= BASE_URL ?>pages/production_planning/" class="btn btn-outline-danger">Batal</a>
                        <button class="btn btn-success">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let shifts = <?= json_encode($shifts) ?>;

    /* ===============================
       DATE INPUT BEHAVIOR
    ================================ */

    $('input[type="date"]').on('click focus', function() {
        this.showPicker();
    });

    /* ===============================
       DISABLE NUMBER SCROLL + ARROW
    ================================ */

    $(document).on('wheel', 'input[type=number]', function(e) {
        $(this).blur();
    });

    $(document).on('keydown', 'input[type=number]', function(e) {
        if (e.which === 38 || e.which === 40) {
            e.preventDefault();
        }
    });

    /* =============================== */

    $('#shiftCount').change(function() {

        $('#shiftWrapper').html('');
        let count = parseInt($(this).val());

        for (let s = 1; s <= count; s++) renderShift(s);

    });

    function renderShift(shiftNo) {

        let shift = shifts[shiftNo - 1];
        let hours = [];

        if (shift.start < shift.end) {
            for (let i = shift.start; i < shift.end; i++) hours.push(i);
        } else {
            for (let i = shift.start; i < 24; i++) hours.push(i);
            for (let i = 0; i < shift.end; i++) hours.push(i);
        }

        let html = `
<div class="card shift-card p-5 mt-5" data-shift="${shiftNo}">
<h5>Shift ${shiftNo}</h5>

<div class="products"></div>

<button type="button" class="btn btn-sm btn-primary addProduct mt-3">+ Product</button>
</div>`;

        $('#shiftWrapper').append(html);

        let card = $('#shiftWrapper .shift-card').last();
        card.data('hours', hours);

        addProduct(card, shiftNo);

    }

    $(document).on('click', '.addProduct', function() {

        let card = $(this).closest('.shift-card');
        let shiftNo = card.data('shift');

        addProduct(card, shiftNo);

    });

    function addProduct(card, shiftNo) {

        let hours = card.data('hours');
        let idx = card.find('.product-card').length;

        let html = `<div class="product-card">

<button type="button" class="btn btn-sm btn-danger remove-product">×</button>

<select name="product_code[${shiftNo}][]" class="form-control mb-3" required>
<option value="">Select Product</option>
<?php foreach ($parts as $p): ?>
<option value="<?= $p['part_code'] ?>"><?= $p['part_code'] ?> - <?= $p['part_name'] ?></option>
<?php endforeach ?>
</select>

<table class="table table-sm">`;

        hours.forEach(h => {
            let n = (h + 1) % 24;
            html += `
<tr>
<td>${h}:00 - ${n}:00</td>
<td>
<input type="number" min="0" step="1" class="form-control qty" name="qty[${shiftNo}][${idx}][]" value="0">
<input type="hidden" name="jam[${shiftNo}][${idx}][]" value="${h}:00-${n}:00">
</td>
</tr>`;
        });

        html += `
<tr>
<td><b>Overtime</b></td>
<td>
<input type="number" min="0" step="1" class="form-control qty" name="qty[${shiftNo}][${idx}][]" value="0">
<input type="hidden" name="jam[${shiftNo}][${idx}][]" value="OT">
</td>
</tr>

<tr><td><b>Total</b></td><td><b class="total">0</b></td></tr>
</table>
</div>`;

        card.find('.products').append(html);

        toggleRemove(card);

    }

    $(document).on('click', '.remove-product', function() {

        let card = $(this).closest('.shift-card');
        $(this).closest('.product-card').remove();
        toggleRemove(card);

    });

    function toggleRemove(card) {

        let count = card.find('.product-card').length;

        if (count <= 1) {
            card.find('.remove-product').hide();
        } else {
            card.find('.remove-product').show();
        }

    }

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