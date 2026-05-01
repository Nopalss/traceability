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

    .product-card {
        position: relative;
        overflow: visible;
    }

    .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 999;
        /* 🔥 penting */
        cursor: pointer;
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

                <h3 class="mb-4">Production Planning</h3>

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


    // 🔥 TEMP STOCK (SIMULASI)
    let tempStock = {};
    let usagePerProduct = {};

    // ================= SHIFT =================
    $(document).on('change', '.shift-check', function() {

        let shiftId = $(this).val();

        if ($(this).is(':checked')) {

            // kalau belum ada, baru render
            if ($(`.shift-card[data-id="${shiftId}"]`).length === 0) {
                renderShift(shiftId);
            }

        } else {

            let shiftCard = $(`.shift-card[data-id="${shiftId}"]`);

            // 🔥 BALIKIN STOCK SEMUA PRODUCT DI SHIFT
            shiftCard.find('.product-card').each(function() {

                let index = $(this).data('index');

                if (usagePerProduct[index]) {
                    Object.keys(usagePerProduct[index]).forEach(part => {

                        let used = usagePerProduct[index][part];

                        tempStock[part] = (tempStock[part] || 0) - used;

                        if (tempStock[part] <= 0) {
                            delete tempStock[part];
                        }
                    });

                    delete usagePerProduct[index];
                }

            });

            shiftCard.remove();
        }

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

        let lineCard = $(this).closest('.line-card'); // 🔥 WAJIB ADA

        let index = Date.now() + Math.floor(Math.random() * 1000);

        let html = `
<div class="product-card" data-index="${index}" data-generated="false">
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

    // ================= VALIDATION =================
    function isLineDuplicate(shiftId, lineId) {
        let count = 0;
        $(`.shift-card[data-id="${shiftId}"] .line-select`).each(function() {
            if ($(this).val() == lineId) count++;
        });
        return count > 1;
    }

    function isModelDuplicate(shiftId, lineId, product, currentCard) {
        let isDuplicate = false;

        $(`.shift-card[data-id="${shiftId}"] .line-card`).each(function() {

            let currentLineId = $(this).find('.line-select').val();

            if (currentLineId == lineId) {
                $(this).find('.product-card').each(function() {

                    if (this === currentCard[0]) return;

                    let val = $(this).find('.product-select').val();

                    if (val == product) {
                        isDuplicate = true;
                    }
                });
            }
        });

        return isDuplicate;
    }

    // 🔥 AMBIL END TIME TERAKHIR (SMART)
    function getLastEndTime(lineCard, shift) {

        let lastEnd = shift.start * 60;

        lineCard.find('.product-card').each(function() {

            let finishText = $(this).find('.finish-box').text();

            if (finishText.includes(':')) {
                let time = finishText.match(/(\d{2}):(\d{2})/);

                if (time) {
                    let h = parseInt(time[1]);
                    let m = parseInt(time[2]);

                    let total = h * 60 + m;

                    if (total > lastEnd) lastEnd = total;
                }
            }
        });

        return lastEnd;

    }
    // ================= GENERATE =================
    $(document).on('click', '.generate', function() {

        let card = $(this).closest('.product-card'); // 🔥 HARUS PALING ATAS
        let lineCard = card.closest('.line-card');
        let index = card.data('index');
        let current;

        // cek product sebelumnya
        let prev = card.prev('.product-card');

        let shiftId = card.closest('.shift-card').data('id');
        let shift = shifts.find(s => s.shift_id == shiftId); // 🔥 PINDAH KE ATAS

        let lineId = card.closest('.line-card').find('.line-select').val();
        let product = card.find('.product-select').val();
        let date = $('input[name=production_date]').val();


        // cek product sebelumnya

        if (prev.length && prev.find('.finish-box').length) {

            let txt = prev.find('.finish-box').text();
            let match = txt.match(/(\d{2}):(\d{2})/);

            if (match) {
                current = parseInt(match[1]) * 60 + parseInt(match[2]);
            } else {
                current = shift.start * 60;
            }

        } else {
            current = shift.start * 60;
        }
        if (!product || !lineId) return alert('Line & Model wajib');

        if (isLineDuplicate(shiftId, lineId)) {
            return alert('Line sudah dipakai di shift ini!');
        }

        if (isModelDuplicate(shiftId, lineId, product, card)) {
            return alert('Model sudah ada di line ini!');
        }

        if (card.attr('data-generated') === 'true') {
            if (!confirm('Planning sudah ada. Mau generate ulang?')) return;

            card.attr('data-generated', 'false');

            // 🔥 BALIKIN STOCK PRODUCT INI SAJA
            if (usagePerProduct[index]) {
                Object.keys(usagePerProduct[index]).forEach(part => {

                    let used = usagePerProduct[index][part];

                    tempStock[part] = (tempStock[part] || 0) - used;

                    if (tempStock[part] <= 0) {
                        delete tempStock[part];
                    }

                });

                delete usagePerProduct[index];
            }
        }
        card.find('.material-table').html('');

        if (!card.find('.hidden-product').length) {
            card.append(`<input type="hidden" 
        name="product_code[${shiftId}][${lineId}][${index}]" 
        value="${product}">`);
        }

        let target = parseInt(card.find('.target').val()) || 0;
        let cycle = parseInt(card.find('.cycle').val()) || 1;

        // 🔥 SMART START TIME


        let produced = 0;
        let hourly = {};

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function getBreakInfo(hourStart, hourEnd) {
            let info = [];

            if (shift.time_coffe) {
                let s = shift.time_coffe;
                let e = s + shift.duration_time;
                if (s < hourEnd && e > hourStart) {
                    info.push(`Coffee ${pad(Math.floor(s/60))}:${pad(s%60)}-${pad(Math.floor(e/60))}:${pad(e%60)}`);
                }
            }

            if (shift.break_makan) {
                let s = shift.break_makan;
                let e = s + shift.duration_bm;
                if (s < hourEnd && e > hourStart) {
                    info.push(`Break ${pad(Math.floor(s/60))}:${pad(s%60)}-${pad(Math.floor(e/60))}:${pad(e%60)}`);
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

        while (produced < target) {
            if (!isBreak(current)) {
                let h = Math.floor(current / 60) % 24; // 🔥 FIX
                hourly[h] = (hourly[h] || 0) + 1;

                produced++;
                current += cycle / 60;
            } else {
                current++;
            }
        }

        let hours = [];
        if (shift.start < shift.end) {
            for (let i = shift.start; i < shift.end; i++) hours.push(i);
        } else {
            for (let i = shift.start; i < 24; i++) hours.push(i);
            for (let i = 0; i < shift.end; i++) hours.push(i);
        }

        let table = '';

        hours.forEach(h => {

            let next = (h + 1) % 24;
            let val = hourly[h] || 0;
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
value="${val}" class="form-control">
</td>
</tr>`;
        });

        let ot = 0;
        Object.keys(hourly).forEach(h => {
            if (!hours.includes(parseInt(h))) ot += hourly[h];
        });

        table += `
<tr>
<td>OT</td>
<td>
<input name="qty[${shiftId}][${lineId}][${index}][OT]" 
value="${ot}" class="form-control">
</td>
</tr>`;

        card.find('.time-table').html(table);

        // 🔥 CLEANUP USAGE LAMA DULU
        if (usagePerProduct[index]) {

            Object.keys(usagePerProduct[index]).forEach(part => {

                let used = usagePerProduct[index][part];

                tempStock[part] = (tempStock[part] || 0) - used;

                if (tempStock[part] <= 0) {
                    delete tempStock[part];
                }

            });

            delete usagePerProduct[index];
        }
        // ================= STOCK SIMULATION =================
        $.post("<?= BASE_URL ?>controllers/production_planning/get_material.php", {
            product,
            target
        }, function(res) {

            let html = `<tr>
<th>Pilih</th><th>Part</th><th>Supplier</th><th>Type</th><th>Stock</th><th>Need</th><th>Shortage</th>
</tr>`;

            res.forEach(r => {

                let used = tempStock[r.part_code] || 0;
                let realStock = r.stock - used;
                let shortage = realStock - r.need;

                if (!usagePerProduct[index]) {
                    usagePerProduct[index] = {};
                }

                usagePerProduct[index][r.part_code] = r.need;
                tempStock[r.part_code] = used + r.need;

                let isShort = shortage < 0;

                html += `
<tr ${isShort?'style="background:#ffe5e5"':''}>
<td>
<input type="checkbox"
name="material[${shiftId}][${lineId}][${index}][]"
value="${r.id_pa}"
${r.remark==0 && realStock > 0?'checked':''}
>
</td>
<td>${r.part_code}</td>
<td>${r.supplier ?? '-'}</td>
<td>${r.remark==0?'MAIN':'SUB'}</td>
<td style="color:${isShort?'red':'green'}">${realStock}</td>
<td>${r.need}</td>
<td style="color:${isShort?'red':'green'}">${shortage}</td>
</tr>`;
            });

            card.find('.material-table').html(html);

        }, 'json');

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

        card.attr('data-generated', 'true');

    });
    $(document).on('click', '.remove-line', function() {

        if (!confirm('Hapus line ini?')) return;

        let line = $(this).closest('.line-card');

        line.find('.product-card').each(function() {

            let index = $(this).data('index');

            if (usagePerProduct[index]) {
                Object.keys(usagePerProduct[index]).forEach(part => {

                    let used = usagePerProduct[index][part];

                    tempStock[part] = (tempStock[part] || 0) - used;

                    if (tempStock[part] <= 0) {
                        delete tempStock[part];
                    }
                });

                delete usagePerProduct[index];
            }

        });

        line.remove();
    });

    $(document).on('click', '.remove-product', function() {

        if (!confirm('Hapus product ini?')) return;

        let card = $(this).closest('.product-card');
        let index = card.data('index');

        // 🔥 BALIKIN STOCK
        if (usagePerProduct[index]) {
            Object.keys(usagePerProduct[index]).forEach(part => {

                let used = usagePerProduct[index][part];

                tempStock[part] = (tempStock[part] || 0) - used;

                if (tempStock[part] <= 0) {
                    delete tempStock[part];
                }
            });

            delete usagePerProduct[index];
        }

        card.remove();
    });
</script>