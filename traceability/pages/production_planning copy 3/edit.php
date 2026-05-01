<?php
require_once __DIR__ . '/../../includes/config.php';

$id = $_GET['id'] ?? 0;

// ambil dulu pp_code
$stmt = $pdo->prepare("SELECT pp_code FROM tbl_production_planning WHERE pp_id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) die('Data tidak ditemukan');

$ppCode = $row['pp_code'];

$stmt = $pdo->prepare("
SELECT * FROM tbl_production_planning 
WHERE pp_code=?
ORDER BY shift ASC
");
$stmt->execute([$ppCode]);
$headers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$details = [];
$materials = [];

foreach ($headers as $h) {

    $ppId = $h['pp_id'];

    // detail
    $stmt = $pdo->prepare("
        SELECT jam, qty 
        FROM tbl_detail_production_planning 
        WHERE pp_id=?
    ");
    $stmt->execute([$ppId]);
    $details[$ppId] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // material
    $stmt = $pdo->prepare("
        SELECT part_id 
        FROM tbl_pp_material 
        WHERE pp_id=?
    ");
    $stmt->execute([$ppId]);
    $materials[$ppId] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'part_id');
}


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

    .material-table td {
        vertical-align: middle;
    }
</style>

<div class="content pt-0">
    <div class="container">
        <div class="card card-modern">
            <div class="card-body">

                <h3 class="mb-4">🚀 Production Planning (Smart Mode)</h3>

                <form method="post" action="<?= BASE_URL ?>controllers/production_planning/update.php">
                    <input type="hidden" name="pp_code" value="<?= $ppCode ?>">

                    <label>Date</label>
                    <input type="date" name="production_date" class="form-control"
                        value="<?= $headers[0]['production_date'] ?>" required>

                    <label class="mt-3">Pilih Shift</label>
                    <?php foreach ($shifts as $s): ?>
                        <div>
                            <input type="checkbox" class="shift-check" value="<?= $s['shift_id'] ?>">
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

    let existing = <?= json_encode($headers) ?>;
    let existingDetails = <?= json_encode($details) ?>;
    let existingMaterials = <?= json_encode($materials) ?>;

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
        let index = Date.now() + Math.floor(Math.random() * 1000);

        let html = `
<div class="product-card" data-index="${index}">
<button type="button" class="btn btn-danger btn-sm remove-btn remove-product">×</button>

<div class="row">
<div class="col">
<label>Target</label>
<input type="number" class="form-control target">
</div>
<div class="col">
<label>Cycle</label>
<input type="number" class="form-control cycle">
</div>
<div class="col d-flex align-items-end">
<button type="button" class="btn btn-dark generate load-material">Generate</button>
</div>
</div>

<select class="form-control mt-3 product-select">
<option value="">Model</option>
<?php foreach ($parts as $p): ?>
<option value="<?= $p['part_code'] ?>"><?= $p['part_code'] ?> - <?= $p['part_name'] ?></option>
<?php endforeach ?>
</select>

<table class="table table-sm mt-3 time-table"></table>
<table class="table table-bordered mt-3 material-table"></table>

</div>`;

        lineCard.find('.products').append(html);
    });


    // ================= GENERATE =================
    $(document).on('click', '.generate', function() {

        let card = $(this).closest('.product-card');
        let index = card.data('index');

        let shiftId = card.closest('.shift-card').data('id');
        let lineId = card.closest('.line-card').find('.line-select').val();
        let product = card.find('.product-select').val();

        if (!product || !lineId) return alert('Line & Model wajib');

        // ================= RESET MATERIAL =================
        card.find('.material-table').html('');

        // ================= SAVE PRODUCT =================
        if (!card.find('.hidden-product').length) {
            card.append(`<input type="hidden" 
        name="product_code[${shiftId}][${lineId}][${index}]" 
        value="${product}">`);
        }

        let shift = shifts.find(s => s.shift_id == shiftId);
        let target = parseInt(card.find('.target').val()) || 0;
        let cycle = parseInt(card.find('.cycle').val()) || 1;

        let current = shift.start * 60;
        let produced = 0;
        let hourly = {};

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function formatTime(min) {
            let h = Math.floor(min / 60) % 24;
            let m = Math.floor(min % 60);
            return `${pad(h)}:${pad(m)}`;
        }

        function getBreakInfo(hourStart, hourEnd) {
            let info = [];

            if (shift.time_coffe) {
                let s = shift.time_coffe;
                let e = s + shift.duration_time;
                if (s < hourEnd && e > hourStart) {
                    info.push(`Coffee ${formatTime(s)}-${formatTime(e)}`);
                }
            }

            if (shift.break_makan) {
                let s = shift.break_makan;
                let e = s + shift.duration_bm;
                if (s < hourEnd && e > hourStart) {
                    info.push(`Break ${formatTime(s)}-${formatTime(e)}`);
                }
            }

            return info.join(', ');
        }

        function isBreak(min) {
            let rel = min - (shift.start * 60);

            if (shift.time_coffe) {
                let s = shift.time_coffe - (shift.start * 60);
                if (rel >= s && rel < s + shift.duration_time) return true;
            }

            if (shift.break_makan) {
                let s = shift.break_makan - (shift.start * 60);
                if (rel >= s && rel < s + shift.duration_bm) return true;
            }

            return false;
        }

        // ================= SIMULASI =================
        while (produced < target) {
            if (!isBreak(current)) {
                let h = Math.floor(current / 60);
                hourly[h] = (hourly[h] || 0) + 1;

                produced++;
                current += cycle / 60;
            } else {
                current++;
            }
        }

        // ================= BUILD JAM =================
        let hours = [];
        if (shift.start < shift.end) {
            for (let i = shift.start; i < shift.end; i++) hours.push(i);
        } else {
            for (let i = shift.start; i < 24; i++) hours.push(i);
            for (let i = 0; i < shift.end; i++) hours.push(i);
        }

        let table = '';
        let total = 0;

        hours.forEach(h => {

            let next = (h + 1) % 24;
            let val = hourly[h] || 0;
            total += val;

            let hourStart = h * 60;
            let hourEnd = (next === 0 ? 24 : next) * 60;

            let breakInfo = getBreakInfo(hourStart, hourEnd);
            let jam = `${pad(h)}:00-${pad(next)}:00`;

            table += `
<tr>
<td>
${jam}
${breakInfo ? `<div style="font-size:11px;color:#f59e0b;">${breakInfo}</div>` : ''}
</td>
<td>
<input name="qty[${shiftId}][${lineId}][${index}][${jam}]" 
value="${val}" class="form-control" >
</td>
</tr>`;
        });

        // ================= OT =================
        let ot = 0;
        Object.keys(hourly).forEach(h => {
            if (!hours.includes(parseInt(h))) ot += hourly[h];
        });

        table += `
<tr>
<td>OT</td>
<td>
<input name="qty[${shiftId}][${lineId}][${index}][OT]" 
value="${ot}" class="form-control" >
</td>
</tr>`;

        card.find('.time-table').html(table);

        // ================= FINISH =================
        let fh = Math.floor(current / 60) % 24;
        let fm = Math.floor(current % 60);

        if (!card.find('.finish-box').length) {
            card.append(`<div class="finish-box mt-2"></div>`);
        }

        card.find('.finish-box').html(`
Selesai: ${pad(fh)}:${pad(fm)}
${ot>0?'<span style="color:red">(Overtime)</span>':''}
`);

        // ================= LOAD MATERIAL (AUTO) =================
        $.post("<?= BASE_URL ?>controllers/production_planning/get_material.php", {
            product,
            target
        }, function(res) {

            let html = `<tr>
<th>Pilih</th><th>Part</th><th>Supplier</th><th>Type</th><th>Stock</th><th>Need</th><th>Shortage</th>
</tr>`;

            res.forEach(r => {
                let isShort = r.shortage > 0;

                html += `
<tr ${isShort?'style="background:#ffe5e5"':''}>
<td>
<input type="checkbox"
name="material[${shiftId}][${lineId}][${index}][]"
value="${r.part_id}"
${r.remark==0 && r.stock > 0?'checked':''}
>
</td>
<td>${r.part_code}</td>
<td>${r.supplier ?? '-'}</td>
<td>${r.remark==0?'MAIN':'SUB'}</td>
<td style="color:${isShort?'red':'green'}">${r.stock}</td>
<td>${r.need}</td>
<td style="color:${isShort?'red':'green'}">${r.shortage}</td>
</tr>`;
            });

            card.find('.material-table').html(html);

        }, 'json');
        setTimeout(() => {
            productCard.find('.generate').click();
        }, 100);

    });

    $(document).ready(function() {

        existing.forEach(item => {

            let shiftId = item.shift;
            let lineId = item.line_id;
            let product = item.product_code;
            let ppId = item.pp_id;

            // 1. check shift
            $(`.shift-check[value="${shiftId}"]`)
                .prop('checked', true)
                .trigger('change');

            setTimeout(() => {

                let shiftCard = $(`.shift-card[data-id="${shiftId}"]`);

                // 2. add line
                shiftCard.find('.add-line').click();

                setTimeout(() => {

                    let lineCard = shiftCard.find('.line-card').last();

                    lineCard.find('.line-select').val(lineId);

                    // 3. add product
                    lineCard.find('.add-product').click();

                    setTimeout(() => {

                        let productCard = lineCard.find('.product-card').last();
                        productCard.find('.target').val(target);
                        productCard.find('.cycle').val(60); // default
                        let index = productCard.data('index');

                        productCard.find('.product-select').val(product);

                        // ===== LOAD QTY =====
                        let table = '';
                        let target = 0;

                        (existingDetails[ppId] || []).forEach(d => {
                            target += parseInt(d.qty) || 0;

                            table += `
<tr>
<td>${d.jam}</td>
<td>
<input name="qty[${shiftId}][${lineId}][${index}][${d.jam}]"
value="${d.qty}" class="form-control">
</td>
</tr>`;
                        });

                        productCard.find('.time-table').html(table);

                        // ===== LOAD MATERIAL =====
                        $.post("<?= BASE_URL ?>controllers/production_planning/get_material.php", {
                            product,
                            target
                        }, function(res) {

                            let html = `<tr>
<th>Pilih</th><th>Part</th><th>Supplier</th><th>Type</th><th>Stock</th><th>Need</th><th>Shortage</th>
</tr>`;

                            res.forEach(r => {

                                let checked = (existingMaterials[ppId] || [])
                                    .includes(parseInt(r.part_id));

                                html += `
<tr>
<td>
<input type="checkbox"
name="material[${shiftId}][${lineId}][${index}][]"
value="${r.part_id}"
${checked?'checked':''}
>
</td>
<td>${r.part_code}</td>
<td>${r.supplier ?? '-'}</td>
<td>${r.remark==0?'MAIN':'SUB'}</td>
<td>${r.stock}</td>
<td>${r.need}</td>
<td>${r.shortage}</td>
</tr>`;
                            });

                            productCard.find('.material-table').html(html);

                        }, 'json');

                    }, 200);

                }, 200);

            }, 200);

        });

    });
</script>