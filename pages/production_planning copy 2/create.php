<?php
require_once __DIR__ . '/../../includes/config.php';

$sql = "SELECT part_code, part_name FROM tbl_part WHERE status_assy = 1  ORDER BY part_code ASC";
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
                    <input type="date" name="production_date" class="form-control" required onkeydown="return false">

                    <label class="mt-3">Line</label>
                    <select name="line_id" class="form-control" required>
                        <option value="">Select</option>
                        <?php foreach ($lines as $l): ?>
                            <option value="<?= $l['line_id'] ?>"><?= $l['line_name'] ?></option>
                        <?php endforeach ?>
                    </select>

                    <!-- 🔥 SHIFT CHECKLIST -->
                    <label class="mt-3">Pilih Shift</label>
                    <div id="shiftChecklist">
                        <?php foreach ($shifts as $i => $s): ?>
                            <div>
                                <input type="checkbox" class="shift-check" value="<?= $s['shift_id'] ?>">
                                Shift <?= $s['shift'] ?>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <hr>
                    <div id="shiftWrapper"></div>

                    <div class="text-right mt-7">
                        <a href="<?= BASE_URL ?>pages/production_planning/" class="btn btn-outline-danger">Batal</a>
                        <button class="btn btn-success" id="submitBtn">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>


<script>
    let shifts = <?= json_encode($shifts) ?>;

    $('input[type="date"]').on('click focus', function() {
        this.showPicker();
    });

    $(document).on('wheel', 'input[type=number]', function(e) {
        $(this).blur();
    });

    $(document).on('keydown', 'input[type=number]', function(e) {
        if (e.which === 38 || e.which === 40) e.preventDefault();
    });

    // ======================
    // ORIGINAL (TETAP)
    // ======================
    $('#shiftCount').change(function() {
        $('#shiftWrapper').html('');
        let count = parseInt($(this).val());
        for (let s = 1; s <= count; s++) renderShift(s);
    });

    // ======================
    // RENDER SHIFT
    // ======================
    function renderShift(shiftId) {

        let shift = shifts.find(s => s.shift_id == shiftId);

        if (!shift) {
            console.error('Shift tidak ditemukan:', shiftId);
            return;
        }

        let start = parseInt(shift.start);
        let end = parseInt(shift.end);

        let hours = [];

        if (start < end) {
            for (let i = start; i < end; i++) {
                hours.push(i);
            }
        } else {
            for (let i = start; i < 24; i++) {
                hours.push(i);
            }
            for (let i = 0; i < end; i++) {
                hours.push(i);
            }
        }

        let html = `
<div class="card shift-card p-5 mt-5" data-shift="${shiftId}">
<h5>Shift ${shift.shift}</h5>
<div class="products"></div>
<button type="button" class="btn btn-sm btn-primary addProduct mt-3">+ Product</button>
</div>`;

        $('#shiftWrapper').append(html);
        let card = $('#shiftWrapper .shift-card').last();

        card.data('hours', hours);
        let shiftData = {
            ...shift,
            start: parseInt(shift.start),
            end: parseInt(shift.end),
            time_coffe: parseInt(shift.time_coffe) || 0,
            duration_time: parseInt(shift.duration_time) || 0,
            break_makan: parseInt(shift.break_makan) || 0,
            duration_bm: parseInt(shift.duration_bm) || 0
        };

        card.data('shiftData', shiftData);
        card.data('shift', shiftId);

        addProduct(card, shiftId);
    }
    // ======================
    // CHECKLIST SHIFT
    // ======================
    $(document).on('change', '.shift-check', function() {
        $('#shiftWrapper').html('');
        $('.shift-check:checked').each(function() {
            renderShift(parseInt($(this).val()));
        });
    });

    // ======================
    // HELPER
    // ======================
    function toggleRemove(card) {
        let count = card.find('.product-card').length;
        if (count <= 1) card.find('.remove-product').hide();
        else card.find('.remove-product').show();
    }

    function getOverlap(start1, end1, start2, end2) {
        let start = Math.max(start1, start2);
        let end = Math.min(end1, end2);
        return Math.max(0, end - start);
    }

    // ======================
    // ❌ GENERATE LAMA (DIMATIKAN)
    // ======================
    $(document).on('click', '.generate', function() {
        return;
    });

    // ======================
    // ADD PRODUCT (UPDATED)
    // ======================
    function addProduct(card, shiftNo) {

        let hours = card.data('hours');
        let idx = card.find('.product-card').length;

        let html = `<div class="product-card">
<button type="button" class="btn btn-sm btn-danger remove-product">×</button>

<!-- 🔥 INPUT PER PRODUCT -->
<div class="row mb-3">
    <div class="col">
        <label>Target</label>
        <input type="number" class="form-control target-product" min="0">
    </div>
    <div class="col">
        <label>Cycle Time (detik)</label>
        <input type="number" class="form-control cycle-product" min="1">
    </div>
    <div class="col d-flex align-items-end">
        <button type="button" class="btn btn-success generate-product">Generate</button>
    </div>
</div>

<select name="product_code[${shiftNo}][]" class="form-control mb-3 product-select" required>
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
<input type="number" min="0" class="form-control qty"
name="qty[${shiftNo}][${idx}][]" value="0">
<input type="hidden"
name="jam[${shiftNo}][${idx}][]"
value="${h}:00-${n}:00">
</td>
</tr>`;
        });

        html += `
<tr>
<td><b>Overtime</b></td>
<td>
<input type="number" min="0" class="form-control qty"
name="qty[${shiftNo}][${idx}][]" value="0">
<input type="hidden"
name="jam[${shiftNo}][${idx}][]"
value="OT">
</td>
</tr>

<tr><td><b>Total</b></td><td><b class="total">0</b></td></tr>
</table>

<div class="material-status mt-2"></div>
</div>`;

        card.find('.products').append(html);
        toggleRemove(card);
    }

    // ======================
    // 🔥 GENERATE PER PRODUCT (FINAL)
    // ======================
    $(document).on('click', '.generate-product', function() {

        let productCard = $(this).closest('.product-card');
        let shiftCard = $(this).closest('.shift-card');

        let target = parseInt(productCard.find('.target-product').val()) || 0;
        let cycle = parseInt(productCard.find('.cycle-product').val()) || 1;

        if (target <= 0 || cycle <= 0) return;

        let hours = shiftCard.data('hours');
        let shift = shiftCard.data('shiftData');

        let capacities = [];
        let totalCapacity = 0;

        let shiftStart = shift.start * 60;

        hours.forEach(h => {

            let startHour = h * 60;
            let endHour = (h + 1) * 60;

            let minutes = 60;

            // ======================
            // COFFEE BREAK (RELATIVE)
            // ======================
            if (shift.time_coffe && shift.duration_time) {

                let coffeeStart = shift.time_coffe - shiftStart;
                let coffeeEnd = coffeeStart + shift.duration_time;

                minutes -= getOverlap(
                    startHour - shiftStart,
                    endHour - shiftStart,
                    coffeeStart,
                    coffeeEnd
                );
            }

            // ======================
            // LUNCH BREAK (RELATIVE)
            // ======================
            if (shift.break_makan && shift.duration_bm) {

                let makanStart = shift.break_makan - shiftStart;
                let makanEnd = makanStart + shift.duration_bm;

                minutes -= getOverlap(
                    startHour - shiftStart,
                    endHour - shiftStart,
                    makanStart,
                    makanEnd
                );
            }

            if (minutes < 0) minutes = 0;

            let cap = Math.floor((minutes * 60) / cycle);

            capacities.push(cap);
            totalCapacity += cap;
        });

        if (totalCapacity === 0) {
            alert('Tidak ada kapasitas produksi');
            return;
        }

        let distributed = [];
        let overtime = 0;

        if (target <= totalCapacity) {

            distributed = capacities.map(c => Math.floor((c / totalCapacity) * target));

            let sum = distributed.reduce((a, b) => a + b, 0);
            let diff = target - sum;

            for (let i = 0; i < diff; i++) {
                distributed[i % distributed.length]++;
            }

        } else {
            distributed = [...capacities];
            overtime = target - totalCapacity;
        }

        let inputs = productCard.find('.qty');

        inputs.each(function(i) {

            if (i === inputs.length - 1) {
                $(this).val(overtime);
            } else {
                $(this).val(distributed[i] || 0);
            }

        });

        productCard.find('.qty').trigger('input');
    });

    // ======================
    // ADD PRODUCT BUTTON
    // ======================
    $(document).on('click', '.addProduct', function() {
        let card = $(this).closest('.shift-card');
        let shiftNo = card.data('shift');
        addProduct(card, shiftNo);
    });
    // ======================
    // 🔥 HITUNG TOTAL + VALIDATE
    // ======================
    $(document).on('input change', '.qty, .product-select', function() {

        if ($(this).hasClass('qty') && $(this).val() < 0) {
            $(this).val(0);
        }

        let box = $(this).closest('.product-card');
        let sum = 0;

        box.find('.qty').each(function() {
            sum += parseInt($(this).val()) || 0;
        });

        box.find('.total').text(sum);

        validateAll();
    });

    function validateAll() {

        let products = [];

        $('.product-card').each(function() {

            let productCode = $(this).find('.product-select').val();
            let total = parseInt($(this).find('.total').text()) || 0;

            if (productCode && total > 0) {
                products.push({
                    product_code: productCode,
                    qty: total
                });
            }
        });

        if (products.length === 0) {
            $('.material-status').html('');
            $('#submitBtn').prop('disabled', false);
            return;
        }

        $.post(
            "<?= BASE_URL ?>controllers/production_planning/check_material_global.php", {
                products: products
            },
            function(res) {

                $('.material-status').html('');

                let adaKekurangan = false;

                res.details.forEach(d => {

                    $('.product-card').each(function() {

                        let code = $(this).find('.product-select').val();

                        if (String(code) === String(d.product)) {

                            let alertClass = d.status === 'kurang' ?
                                'alert-danger' :
                                'alert-success';

                            if (d.status === 'kurang') {
                                adaKekurangan = true;
                            }

                            $(this).find('.material-status').append(
                                `<div class="alert ${alertClass} py-2 mb-2">
                                <b>${d.part_name}</b><br>
                                Kebutuhan: ${d.needed}<br>
                                Stok Tersedia: ${d.available}<br>
                                Kekurangan: ${d.shortage}
                            </div>`
                            );
                        }
                    });
                });

                $('#submitBtn').prop('disabled', adaKekurangan);
            },
            'json'
        );
    }
</script>