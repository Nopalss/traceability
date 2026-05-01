<?php
require_once __DIR__ . '/../../includes/config.php';

$parts = $pdo->query("SELECT part_code, part_name FROM tbl_part WHERE status_assy = 1 ORDER BY part_code ASC")->fetchAll(PDO::FETCH_ASSOC);
$lines = $pdo->query("SELECT line_id, line_name FROM tbl_line ORDER BY line_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$shifts = $pdo->query("SELECT * FROM tbl_shift ORDER BY shift ASC")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .card-modern {
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        border: none;
    }

    .shift-card {
        background: #f8fafc;
    }

    .line-card {
        background: #fff;
        border-left: 4px solid #22c55e;
    }

    .product-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 15px;
        margin-top: 15px;
        position: relative;
    }

    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .finish-box {
        font-weight: bold;
        color: #16a34a;
    }
</style>

<div class="content pt-0">
    <div class="container">
        <div class="card card-modern">
            <div class="card-body">

                <h3 class="mb-4">🚀 Production Planning (Smart Mode)</h3>

                <form method="post" action="<?= BASE_URL ?>controllers/production_planning/create.php">

                    <label>Date</label>
                    <input type="date" name="production_date" class="form-control" required>

                    <label class="mt-3">Pilih Shift</label>
                    <?php foreach ($shifts as $s): ?>
                        <div>
                            <input type="checkbox" class="shift-check" value="<?= $s['shift_id'] ?>">
                            Shift <?= $s['shift'] ?>
                        </div>
                    <?php endforeach ?>

                    <hr>
                    <div id="wrapper"></div>

                    <div class="text-right mt-4">
                        <button class="btn btn-success">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    let shifts = <?= json_encode($shifts) ?>;
    let lines = <?= json_encode($lines) ?>;

    // ================= SHIFT =================
    $(document).on('change', '.shift-check', function() {
        $('#wrapper').html('');
        $('.shift-check:checked').each(function() {
            renderShift($(this).val());
        });
    });

    function renderShift(id) {
        let shift = shifts.find(s => s.shift_id == id);

        let html = `
    <div class="card card-modern shift-card p-4 mt-4" data-id="${id}">
        <h5>Shift ${shift.shift}</h5>
        <div class="lines"></div>
        <button type="button" class="btn btn-primary btn-sm add-line mt-2">+ Line</button>
    </div>`;

        $('#wrapper').append(html);
    }

    // ================= LINE =================
    $(document).on('click', '.add-line', function() {
        let card = $(this).closest('.shift-card');
        let shiftId = card.data('id');

        let html = `
    <div class="line-card p-3 mt-3">
        <button type="button" class="btn btn-danger btn-sm remove-btn remove-line">×</button>

        <label>Line</label>
        <select class="form-control line-select" name="line[${shiftId}][]">
            <option value="">Select</option>
            ${lines.map(l=>`<option value="${l.line_id}">${l.line_name}</option>`).join('')}
        </select>

        <div class="products"></div>
        <button type="button" class="btn btn-success btn-sm add-product mt-2">+ Product</button>
    </div>`;

        card.find('.lines').append(html);
    });

    // ================= PRODUCT =================
    $(document).on('click', '.add-product', function() {

        let lineCard = $(this).closest('.line-card');

        let html = `
    <div class="product-card">
        <button type="button" class="btn btn-danger btn-sm remove-btn remove-product">×</button>

        <div class="row">
            <div class="col">
                <label>Target</label>
                <input type="number" class="form-control target">
            </div>
            <div class="col">
                <label>Cycle (detik)</label>
                <input type="number" class="form-control cycle">
            </div>
            <div class="col d-flex align-items-end">
                <button type="button" class="btn btn-dark generate">Generate</button>
            </div>
        </div>

        <select class="form-control mt-3 product-select">
            <option value="">Model</option>
            <?php foreach ($parts as $p): ?>
            <option value="<?= $p['part_code'] ?>"><?= $p['part_code'] ?> - <?= $p['part_name'] ?></option>
            <?php endforeach ?>
        </select>

        <!-- 🔥 TABLE JAM -->
        <table class="table table-sm mt-3 time-table"></table>

        <div class="finish-box mt-2"></div>
        <div class="material-status mt-2"></div>
    </div>`;

        lineCard.find('.products').append(html);
    });

    // ================= REMOVE =================
    $(document).on('click', '.remove-line', function() {
        $(this).closest('.line-card').remove();
    });
    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-card').remove();
    });

    // ================= CORE LOGIC =================
    $(document).on('click', '.generate', function() {

        let productCard = $(this).closest('.product-card');
        let shiftCard = $(this).closest('.shift-card');

        let shift = shifts.find(s => s.shift_id == shiftCard.data('id'));

        let target = parseInt(productCard.find('.target').val()) || 0;
        let cycle = parseInt(productCard.find('.cycle').val()) || 1;

        if (target <= 0) return;

        let current = shift.start * 60;
        let produced = 0;

        let hourly = {};

        function isBreak(min) {
            let rel = min - (shift.start * 60);

            if (shift.time_coffe) {
                let s = shift.time_coffe - (shift.start * 60);
                let e = s + shift.duration_time;
                if (rel >= s && rel < e) return true;
            }

            if (shift.break_makan) {
                let s = shift.break_makan - (shift.start * 60);
                let e = s + shift.duration_bm;
                if (rel >= s && rel < e) return true;
            }

            return false;
        }

        // SIMULASI
        while (produced < target) {

            if (!isBreak(current)) {
                let h = Math.floor(current / 60);
                hourly[h] = (hourly[h] || 0) + 1;

                produced++;
                current += (cycle / 60);
            } else {
                current++;
            }
        }

        // BUILD JAM
        let start = shift.start;
        let end = shift.end;
        let hours = [];

        if (start < end) {
            for (let i = start; i < end; i++) hours.push(i);
        } else {
            for (let i = start; i < 24; i++) hours.push(i);
            for (let i = 0; i < end; i++) hours.push(i);
        }

        let table = '';
        let total = 0;

        // normal hours
        hours.forEach(h => {
            let next = (h + 1) % 24;
            let val = hourly[h] || 0;
            total += val;

            table += `
    <tr>
        <td>${h}:00 - ${next}:00</td>
        <td><input class="form-control" value="${val}" readonly></td>
    </tr>`;
        });

        // overtime
        let overtime = 0;
        Object.keys(hourly).forEach(h => {
            if (!hours.includes(parseInt(h))) {
                overtime += hourly[h];
            }
        });

        table += `
<tr><td><b>Overtime</b></td><td>${overtime}</td></tr>
<tr><td><b>Total</b></td><td>${total}</td></tr>
`;

        productCard.find('.time-table').html(table);

        // finish time
        let h = Math.floor(current / 60) % 24;
        let m = Math.floor(current % 60);

        productCard.find('.finish-box').html(`
Selesai: ${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}
${overtime>0 ? '<span class="text-danger">(Overtime)</span>' : ''}
`);

    });

    // ================= VALIDATION =================
    $(document).on('input change', '.product-select, .target', function() {

        let products = [];

        $('.product-card').each(function() {
            let code = $(this).find('.product-select').val();
            let qty = parseInt($(this).find('.target').val()) || 0;

            if (code && qty > 0) {
                products.push({
                    product_code: code,
                    qty: qty
                });
            }
        });

        if (products.length === 0) return;

        $.post("<?= BASE_URL ?>controllers/production_planning/check_material_global.php", {
            products
        }, function(res) {

            $('.material-status').html('');

            res.details.forEach(d => {
                $('.product-card').each(function() {

                    let code = $(this).find('.product-select').val();

                    if (code == d.product) {
                        $(this).find('.material-status').append(`
            <div class="alert ${d.status=='kurang'?'alert-danger':'alert-success'}">
            ${d.part_name}<br>
            Need: ${d.needed} | Stock: ${d.available}
            </div>
            `);
                    }
                });
            });

        }, 'json');

    });
</script>