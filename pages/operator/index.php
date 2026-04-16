<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['halaman'] = 'operator planning';
$_SESSION['menu'] = 'production_planning';
$_SESSION['subHalaman'] = ' | Operator View';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';

$lineId   = $_SESSION['line_id'] ?? 0;
$lineName  = isset($_SESSION['active_line']) ? $_SESSION['active_line'] : $_SESSION['username'];
$operator = isset($_SESSION['active_operator']) ? $_SESSION['active_operator'] :  $_SESSION['username'];
$today    = date('Y-m-d');

// SHIFT MASTER
$shiftStmt = $pdo->query("SELECT * FROM tbl_shift");
$shiftMaster = $shiftStmt->fetchAll(PDO::FETCH_ASSOC);
$ngStmt = $pdo->query("SELECT * FROM tbl_ng_type");
$ngMaster = $ngStmt->fetchAll(PDO::FETCH_ASSOC);

$currentShift = 1; // default
$h = date('G');

foreach ($shiftMaster as $s) {
    if ($s['start'] < $s['end']) {
        if ($h >= $s['start'] && $h < $s['end']) {
            $currentShift = $s['shift'];
        }
    } else {
        if ($h >= $s['start'] || $h < $s['end']) {
            $currentShift = $s['shift'];
        }
    }
}
// ASSY TODAY
$ppStmt = $pdo->prepare("
SELECT DISTINCT product_code
FROM tbl_production_planning
WHERE production_date = ?
  AND line_id = ?
  AND shift = ?
");
$planningDate = date('Y-m-d');

// SHIFT MALAM CROSS DATE
if ($currentShift == 3 && date('H') < 7) {
    $planningDate = date('Y-m-d', strtotime('-1 day'));
}

$ppStmt->execute([$planningDate, $lineId, $currentShift]);

$assyList = $ppStmt->fetchAll(PDO::FETCH_COLUMN);
$jamList = [];
function jamLabel($jam)
{
    if ($jam === 'OT') return 'OT';

    [$a, $b] = explode('-', $jam);

    $a = sprintf('%02d', intval($a));
    $b = sprintf('%02d', intval($b));

    return "$a-$b";
}

?>

<style>
    .operator-card {
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 6px;
        background: #f8f9fa
    }

    .box {
        border: 1px solid #ccc;
        padding: 5px 8px;
        background: #fff
    }

    .summary {
        display: flex;
        gap: 5px
    }

    .btnstyle {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin-left: 2px;
        cursor: pointer;
    }

    .btnNgLot {
        background-color: #fa5252;

    }

    .line {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btnRemoveLot {
        background-color: #6bfa52;
    }

    .btnAdjustLot {
        background-color: #faca52;
    }

    .summary div {
        flex: 1;
        text-align: center;
        padding: 5px;
        font-weight: bold;
        color: #fff;
        font-size: 0.9rem;
    }

    .plan {
        background: #4c6ef5
    }

    .actual {
        background: #fa5252
    }

    .remain {
        background: #40c057
    }

    .planning-table th {
        background: #74b816;
        color: #000;
        text-align: center
    }

    .planning-table thead th {
        position: sticky;
        top: 0;
        background: #74b816;
        z-index: 10;
    }

    /* supaya tbody bisa scroll */
    #planningWrapper {
        max-height: 120px;
        overflow-y: auto;
    }


    .planning-table td {
        font-size: .75rem;
        text-align: center
    }

    .small-table th {
        background: #dee2e6;
        font-size: .8rem
    }

    .small-table td {
        font-size: .6975rem
    }

    /* ===== kecilkan scrollbar planning ===== */
    #planningWrapper::-webkit-scrollbar {
        width: 1px;
        /* ukuran scrollbar */
        height: 1px;
    }

    #planningWrapper::-webkit-scrollbar-track {
        background: transparent;
    }

    #planningWrapper::-webkit-scrollbar-thumb {
        background: #999;
        border-radius: 10px;
    }

    #planningWrapper::-webkit-scrollbar-thumb:hover {
        background: #666;
    }

    /* Firefox */
    #planningWrapper {
        scrollbar-width: thin;
        scrollbar-color: #999 transparent;
    }

    #outputWrapper thead th {
        position: sticky;
        top: 0;
        background: #dee2e6;
        z-index: 5;
    }

    #materialWrapper thead th {
        position: sticky;
        top: 0;
    }

    .unit-grid {

        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;

    }

    .unit-card {

        border: 2px solid #dee2e6;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: 600;

    }

    .unit-card.active {

        background: #fa5252;
        color: white;
        border-color: #fa5252;

    }

    table td,
    table th {

        font-size: 0.8rem;

    }

    .swal2-popup {

        border-radius: 10px;

    }
</style>
<div class="d-flex flex-column flex-row-fluid wrapper pt-2" id="kt_wrapper">
    <div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
        <div class="content pt-0">
            <div class="container">
                <div class="operator-card">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6 font-weight-bolder">LINE : <?= $lineName ?></div>
                                <div class="col-md-6 font-weight-bolder" id="shiftText">SHIFT : -</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 small">OPERATOR : <?= $operator ?></div>
                                <div class="col-md-6" id="dateText"></div>
                                <div id="timeText"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6"></div>

                            </div>

                            <div class="row mt-2 align-items-center justify-content-between">
                                <div class="pl-2">Input :</div>
                                <input type="text" id="scanInput" class="col-md-10 box" style="font-size: 0.685rem; padding-block: 5px" autocomplete="off">
                            </div>

                            <div class="row mt-2 align-items-center justify-content-between">
                                <div class="pl-2">Remark :</div>
                                <input type="text" id="remark" class="col-md-10 box" placeholder="(Optional)" style="font-size: 0.685rem; padding-block: 5px">
                            </div>

                            <div class="row mt-2 align-items-center justify-content-between">
                                <div class="pl-2">ASSY :</div>
                                <select id="assySelect" class="col-md-10 box" style="font-size: 0.685rem; padding-block: 5px">
                                    <option value="">-- pilih --</option>
                                    <?php foreach ($assyList as $a): ?>
                                        <option value="<?= $a ?>"><?= $a ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row mt-2">
                                <button id="btnExitMeca" class="btn btn-danger btn-sm col">
                                    EXIT MECA
                                </button>
                                <button id="btnInMeca" class="btn btn-success btn-sm col ml-2">
                                    IN MECA
                                </button>
                            </div>

                        </div>

                        <div class="col-md">

                            <div class="summary">
                                <div class="plan">PLANNING<br><span id="sumPlan">0</span></div>
                                <div class="actual">ACTUAL<br><span id="sumActual">0</span></div>
                                <div class="remain">REMAIN<br><span id="sumRemain">0</span></div>
                            </div>

                            <h5 class="small mt-3 font-weight-bolder">Planning Table</h5>

                            <div id="planningWrapper" style="max-height:90px; overflow-y:auto">
                                <table class="table table-bordered planning-table table-sm">
                                    <!-- <thead>
                                        <tr>
                                            <th style="font-size: 0.685rem; padding-block: 3px">Product</th>
                                            <?php
                                            foreach ($jamList as $jam):
                                            ?>
                                                <th style="font-size: 0.685rem; padding-block: 3px"> <?= jamLabel($jam) ?> </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead> -->
                                    <thead>
                                        <tr id="planningHeader">
                                            <th style="font-size: 0.685rem; padding-block: 3px">Product</th>
                                        </tr>
                                    </thead>

                                    <tbody id="planningBody">
                                        <tr>
                                            <td colspan="11">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div id="materialWrapper" style="max-height:130px; overflow-y:auto">
                        <table class="table table-bordered table-striped small-table">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:25%">PARTCODE</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:25%">PARTNAME</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:20px">USED</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:25%">LOT</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:20px">SPQ</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:20px">REMAIN</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:80px">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="materialBody">
                                <tr>
                                    <td colspan="7">Belum dipilih assy</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4 small font-weight-bolder">OUTPUT</h5>
                    <div id="outputWrapper" style="max-height:160px; overflow-y:auto">
                        <table class="table table-bordered table-striped small-table">
                            <thead>
                                <tr style="text-align: center;">
                                    <th style="font-size: 0.885rem; padding-block: 3px">Date</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:30px">Time</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:20px">Shift</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px">Line</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px">Operator</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px; width:20px">QTY/SPQ</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px">LOT/Serial</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px">Ref</th>
                                    <th style="font-size: 0.885rem; padding-block: 3px">Remark</th>
                                </tr>
                            </thead>
                            <tbody id="outputBody">
                                <tr>
                                    <td colspan="9">Belum ada data</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    // ===== AUTO RESET SETIAP GANTI HARI =====
    const todayKey = new Date().toISOString().slice(0, 10);
    let lastSerial = null;
    let lastQty = 1;

    if (localStorage.getItem('planning_date') !== todayKey) {
        localStorage.clear();
        localStorage.setItem('planning_date', todayKey);
    }

    let lastScanTime = 0;
    const shifts = <?= json_encode($shiftMaster) ?>;
    const lineId = <?= $lineId ?>;
    const operatorName = "<?= isset($_SESSION['active_operator']) ? $_SESSION['active_operator'] :  $_SESSION['username']; ?>";

    let activeShift = null;
    let scanLock = false;


    /* =========================================
   SMART SCANNER FOCUS
========================================= */

    function focusScanner() {

        // jika swal terbuka jangan paksa fokus
        if ($('.swal2-container:visible').length) return;

        let active = document.activeElement;

        // jika user sedang isi remark atau select assy jangan ganggu
        if (active && (
                active.id === 'remark' ||
                active.id === 'assySelect'
            )) return;

        $('#scanInput').focus();

    }


    function getPlanningDate() {
        let now = new Date();
        let hour = now.getHours();

        // shift malam cross date
        if (activeShift == 3 && hour < 7) {
            now.setDate(now.getDate());
        }

        return now.toISOString().slice(0, 10);
    }

    /* =========================
       SHIFT DETECTION
    ========================== */

    function detectShift() {

        let h = new Date().getHours();
        let prev = activeShift;

        activeShift = null; // reset dulu

        for (let s of shifts) {

            let start = parseInt(s.start);
            let end = parseInt(s.end);

            if (start < end) {
                if (h >= start && h < end) {
                    activeShift = s.shift;
                    break;
                }
            } else {
                if (h >= start || h < end) {
                    activeShift = s.shift;
                    break;
                }
            }
        }

        // fallback
        if (!activeShift && shifts.length > 0) {
            activeShift = shifts[0].shift;
        }

        $('#shiftText').text('SHIFT  ' + activeShift);

        if (prev !== activeShift) {
            reloadAssy();
            reloadAll();
        }
    }



    /* =========================
       DATE TIME
    ========================== */
    function formatTanggal() {
        let d = new Date();

        let tanggal = d.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        let waktu = d.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        $('#dateText').text('DATE : ' + tanggal + ' ' + waktu);
    }

    /* =========================
       LOAD PLANNING
    ========================== */
    function loadPlanning() {

        if (!activeShift) return;

        $.get('ajax_operator.php', {
            action: 'planning',
            shift: activeShift,
            production_date: getPlanningDate(),
            line: lineId
        }, res => {

            // $('#planningBody').html(res);

            // setTimeout(() => {
            //     savePlanningToLocal();
            // }, 50);


            let r = JSON.parse(res);
            if (!r.data || Object.keys(r.data).length === 0) {
                $('#planningHeader').html('<th>Product</th>');
                $('#planningBody').html('<tr><td colspan="11">Tidak ada planning</td></tr>');
                $('#sumPlan').text(0);
                $('#sumActual').text(0);
                $('#sumRemain').text(0);
                return;
            }


            // HEADER
            let h = '<th style="font-size:0.685rem;padding-block:3px">Product</th>';

            function formatJam(jam) {
                if (jam === 'OT') return 'OT';

                if (!jam.includes('-')) return jam;

                let [a, b] = jam.split('-');

                // buang menit (:00)
                a = a.split(':')[0];
                b = b.split(':')[0];

                return `${a.padStart(2,'0')}-${b.padStart(2,'0')}`;
            }


            r.jam.forEach(j => {
                h += `<th style="font-size:0.685rem;padding-block:3px">${formatJam(j)}</th>`;
            });

            $('#planningHeader').html(h);

            // BODY
            let body = '';

            Object.keys(r.data).forEach(p => {

                body += `<tr><td>${p}</td>`;

                r.jam.forEach(j => {

                    let row = r.data[p][j] || {
                        actual: 0,
                        qty: 0
                    };

                    body += `<td>${row.actual}/${row.qty}</td>`;
                });

                body += '</tr>';
            });

            $('#planningBody').html(body);

            let plan = 0;
            let actual = 0;

            Object.values(r.data).forEach(row => {
                Object.values(row).forEach(x => {
                    plan += parseInt(x.qty || 0);
                    actual += parseInt(x.actual || 0);
                });
            });
            let selectedAssy = $('#assySelect').val();

            if (!selectedAssy) {
                $('#sumPlan').text(0);
                $('#sumActual').text(0);
                $('#sumRemain').text(0);
            } else {
                hitungPlanningByAssy(selectedAssy);
            }



            let rowCount = $('#planningBody tr').length;
            for (let i = rowCount; i < 3; i++) {
                $('#planningBody').append('<tr><td colspan="11">&nbsp;</td></tr>');
            }
            setTimeout(() => {
                savePlanningToLocal();
            }, 50);
        });
    }

    function savePlanningToLocal() {

        let planning = {};

        $('#planningBody tr').each(function() {

            let product = $(this).find('td:first').text().trim();
            if (!product) return;

            let total = 0;

            $(this).find('td').each(function(i) {

                if (i === 0) return;

                let v = $(this).text().trim(); // 0/3

                if (!v.includes('/')) return;

                let arr = v.split('/');
                total += parseInt(arr[1] || 0);

            });

            planning[product] = total;
        });

        localStorage.setItem('planning_today', JSON.stringify(planning));
    }


    /* =========================
       SAVE BOM
    ========================== */
    function saveBomToLocal(assy) {

        let bom = [];

        $('#materialBody tr').each(function() {

            let part = $(this).find('td:eq(0)').text().trim();
            let qty = parseInt($(this).find('td:eq(2)').text().trim()) || 0;

            if (part) {
                bom.push({
                    part_code: part,
                    qty: qty
                });
            }

        });

        localStorage.setItem('bom_' + assy, JSON.stringify(bom));
    }

    /* =========================
       PARSE BARCODE
    ========================== */
    function parseBarcode(code) {
        let obj = {};
        let parts = code.split('|');

        parts.forEach(p => {
            if (p.startsWith('Z1')) obj.Z1 = p.substring(2);
            if (p.startsWith('Z2')) obj.Z2 = p.substring(2);
            if (p.startsWith('Z3')) obj.Z3 = parseInt(p.substring(2)) || 1;
            if (p.startsWith('Z4')) obj.Z4 = p.substring(2);
            if (p.startsWith('Z5')) obj.Z5 = p.substring(2);
        });

        return obj;
    }

    /* =========================
       HANDLE SCAN
    ========================== */
    // $('#scanInput').on('keypress', function(e) {
    //     if (e.which == 13) {
    //         handleScan(this.value.trim());
    //         this.value = '';
    //     }
    // });
    $('#scanInput').on('keydown', function(e) {

        if (e.key === 'Enter') {

            e.preventDefault();

            let code = this.value.trim();

            if (code) {
                handleScan(code);
            }

            this.value = '';

        }

    });

    function handleScan(code) {

        if (scanLock) return;

        let data = parseBarcode(code);

        if (!data.Z1) {
            Swal.fire('Error', 'Format barcode salah', 'error');
            return;
        }

        let assy = $('#assySelect').val();

        if (!assy) {
            Swal.fire('Error', 'Pilih ASSY dulu', 'error');
            return;
        }

        let bomData = JSON.parse(localStorage.getItem('bom_' + assy)) || [];
        let bom = bomData.map(b => b.part_code);

        scanLock = true;

        // ============================
        // 1️⃣ PRODUCT
        // ============================
        let planning = JSON.parse(localStorage.getItem('planning_today')) || {};

        if (data.Z1 === assy) {

            if (!planning[assy]) {
                scanLock = false;
                Swal.fire('Error', 'Product ini tidak ada planning', 'error');
                return;
            }

            sendProductToServer(data, assy);
            return;
        }


        // ============================
        // 2️⃣ MATERIAL
        // ============================
        if (bom.includes(data.Z1)) {
            sendMaterialToServer(data, assy);
            return;
        }

        // ============================
        // 3️⃣ TIDAK VALID
        // ============================
        scanLock = false;
        Swal.fire('Error', 'Part tidak ada di BOM assy ini', 'error');
    }


    /* =========================
       RELOAD
    ========================== */
    function reloadAll() {
        loadPlanning();

        $.get('ajax_operator.php', {
            action: 'output',
            line: lineId
        }, res => {

            $('#outputBody').html(res);

            let rowCount = $('#outputBody tr').length;
            for (let i = rowCount; i < 4; i++) {
                $('#outputBody').append('<tr><td colspan="9">&nbsp;</td></tr>');
            }
        });
    }


    /* =========================
       MATERIAL
    ========================== */
    function reloadMaterial() {

        let assy = $('#assySelect').val();
        if (!assy) return;

        $.get('ajax_operator.php', {
            action: 'load_bom',
            line: lineId,
            assy
        }, r => {

            $('#materialBody').html(r);

            let rowCount = $('#materialBody tr').length;
            for (let i = rowCount; i < 3; i++) {
                $('#materialBody').append('<tr><td colspan="7">&nbsp;</td></tr>');
            }

            setTimeout(() => {
                saveBomToLocal(assy);
            }, 50);
        });
    }

    function sendMaterialToServer(data, assy) {

        $.post('ajax_operator.php', {
            action: 'scan_material',
            Z1: data.Z1,
            Z2: data.Z2,
            Z3: data.Z3,
            Z4: data.Z4,
            Z5: data.Z5,
            assy: assy,
            shift: activeShift,
            line: lineId
        }, function(res) {

            let r = JSON.parse(res);

            if (r.error) {
                scanLock = false;
                Swal.fire('Error', r.message, 'error');
                return;
            }

            if (r.needConfirm) {

                Swal.fire({
                    title: 'Material sudah ada',
                    html: `
            <button id="btnAdd" class="swal2-confirm swal2-styled" style="margin:5px">ADD</button>
            <button id="btnReplace" class="swal2-confirm swal2-styled" style="margin:5px;background:#e03131">REPLACE</button>
            <button id="btnCancel" class="swal2-cancel swal2-styled" style="margin:5px">BATAL</button>
        `,
                    showConfirmButton: false,
                    showCancelButton: false
                });

                // ADD
                $(document)
                    .off('click', '#btnAdd')
                    .on('click', '#btnAdd', function() {
                        Swal.close();
                        sendMaterialWithMode(data, 'add', assy);
                    });

                // REPLACE
                $(document)
                    .off('click', '#btnReplace')
                    .on('click', '#btnReplace', function() {
                        Swal.close();
                        sendMaterialWithMode(data, 'remove', assy);
                    });

                // BATAL
                $(document)
                    .off('click', '#btnCancel')
                    .on('click', '#btnCancel', function() {
                        Swal.close();
                        scanLock = false;
                    });

                return;
            }


            scanLock = false;
            Swal.fire('Success', 'Material berhasil ditambahkan', 'success');
            lastScanTime = Date.now();
            reloadMaterial();
            reloadAll();
            setTimeout(focusScanner, 200);
        });
    }

    function sendMaterialWithMode(data, mode, assy) {

        $.post('ajax_operator.php', {
            action: 'scan_material',
            Z1: data.Z1,
            Z2: data.Z2,
            Z3: data.Z3,
            Z4: data.Z4,
            Z5: data.Z5,
            mode: mode,
            assy: assy,
            shift: activeShift,
            line: lineId
        }, function(res) {

            scanLock = false;

            let r = JSON.parse(res);

            if (r.error) {
                Swal.fire('Error', r.message, 'error');
                return;
            }

            Swal.fire('Success', 'Material updated', 'success');
            lastScanTime = Date.now();
            reloadMaterial();
            reloadAll();
            setTimeout(focusScanner, 200);
        });
    }

    function hitungPlanningByAssy(assy) {

        let plan = 0;
        let actual = 0;

        $('#planningBody tr').each(function() {

            let product = $(this).find('td:first').text().trim();
            if (product !== assy) return;

            $(this).find('td').each(function(i) {

                if (i === 0) return;

                let val = $(this).text().trim(); // 0/3

                if (!val.includes('/')) return;

                let arr = val.split('/');

                actual += parseInt(arr[0] || 0);
                plan += parseInt(arr[1] || 0);
            });
        });

        $('#sumPlan').text(plan);
        $('#sumActual').text(actual);
        $('#sumRemain').text(plan - actual);
    }



    /* =========================
       PRODUCT
    ========================== */
    function sendProductToServer(data, assy) {

        lastSerial = data.Z2;
        lastQty = data.Z3 || 1;

        let remark = $('#remark').val().trim();

        $.post('ajax_operator.php', {
            action: 'scan_product',
            Z1: data.Z1,
            Z2: data.Z2,
            Z3: data.Z3,
            Z4: data.Z4,
            Z5: data.Z5,
            assy: assy,
            shift: activeShift,
            line: lineId,
            operator: operatorName,
            operator_remark: remark
        }, function(res) {

            scanLock = false;

            let r = JSON.parse(res);

            if (r.error) {
                Swal.fire('Error', r.message, 'error');
                return;
            }

            if (r.finished) {

                Swal.fire({
                    icon: 'success',
                    title: 'Target Tercapai 🎉',
                    text: 'Produk ini sudah sesuai planning',
                    timer: 2500,
                    showConfirmButton: false
                });

            } else {

                Swal.fire('Success', 'Product berhasil diproduksi', 'success');

            }

            $('#remark').val('');

            lastScanTime = Date.now();
            reloadMaterial();
            reloadAll();
            setTimeout(focusScanner, 200);
        });
    }

    function forceMaterialMinRow() {

        let rowCount = $('#materialBody tr').length;

        for (let i = rowCount; i < 3; i++) {
            $('#materialBody').append('<tr><td colspan="7">&nbsp;</td></tr>');
        }
    }

    /* =========================
       ASSY CHANGE
    ========================== */
    $('#assySelect').on('change', function() {

        let assy = this.value;
        localStorage.setItem('currentAssy', assy);


        if (!assy) {
            $('#materialBody').html('<tr><td colspan="7">Belum dipilih assy</td></tr>');
            forceMaterialMinRow();

            $('#sumPlan').text(0);
            $('#sumActual').text(0);
            $('#sumRemain').text(0);

            return;
        }

        hitungPlanningByAssy(assy);

        $.get('ajax_operator.php', {
            action: 'load_bom',
            line: lineId,
            assy
        }, r => {

            $('#materialBody').html(r);
            // FORCE MINIMUM 3 ROWS
            let rowCount = $('#materialBody tr').length;

            for (let i = rowCount; i < 3; i++) {
                $('#materialBody').append('<tr><td colspan="7">&nbsp;</td></tr>');
            }
            setTimeout(() => {
                saveBomToLocal(assy);
            }, 50);
        });
    });

    function reloadAssy() {

        let current = $('#assySelect').val();

        $.get('ajax_operator.php', {
            action: 'load_assy',
            shift: activeShift,
            production_date: getPlanningDate(),
            line: lineId
        }, res => {

            let list = JSON.parse(res);

            let html = '<option value="">-- pilih --</option>';

            list.forEach(a => {
                let selected = (a == current) ? 'selected' : '';
                html += `<option value="${a}" ${selected}>${a}</option>`;
            });

            $('#assySelect').html(html);

            // kalau assy yang lama sudah tidak ada → reset material
            if (!list.includes(current)) {
                $('#materialBody').html('<tr><td colspan="7">Belum dipilih assy</td></tr>');
                forceMaterialMinRow();
                $('#sumPlan').text(0);
            }
        });
    }

    /* =========================
       INIT
    ========================== */
    detectShift();
    reloadAll();
    formatTanggal();
    forceMaterialMinRow()

    setInterval(formatTanggal, 1000);
    // ===============================
    // SMART RANDOM POLLING (15–25 detik)
    // ===============================
    function startPolling() {

        function poll() {

            // skip kalau tab tidak aktif
            if (document.hidden) {
                scheduleNext();
                return;
            }

            // skip kalau baru scan
            if (Date.now() - lastScanTime > 3000) {
                reloadAll();
                reloadAssy();
            }

            scheduleNext();
        }

        function scheduleNext() {
            // random 15–25 detik
            let next = 15000 + Math.random() * 10000;
            setTimeout(poll, next);
        }

        // random delay awal supaya PC gak barengan
        setTimeout(poll, Math.random() * 5000);
    }

    startPolling();

    setInterval(detectShift, 10000);

    $(document).ready(function() {

        setTimeout(() => {
            focusScanner();
        }, 300);

    });

    /* ========================================
   EXIT MECA MAIN BUTTON
======================================== */

    function sendExitBox(barcode, parts) {

        if (parts.length === 0) {
            Swal.fire('Error', 'Pilih part NG terlebih dahulu', 'error')
            return
        }

        $.post('ajax_operator.php', {
            action: 'exit_meca',
            serial: barcode.Z2,
            qty: barcode.Z3,
            ref_product: barcode.Z5,
            parts: parts,
            line: lineId,
            shift: activeShift,

        }, function(res) {

            let r = JSON.parse(res)

            if (r.error) {
                Swal.fire('Error', r.message, 'error')
                return
            }

            Swal.fire({
                icon: 'success',
                title: 'EXIT MECA SUCCESS',
                timer: 1500,
                showConfirmButton: false
            })

            reloadMaterial()
            reloadAll()

        })

    }

    function loadUnitParts(barcode, units) {

        if (units.length === 0) {
            Swal.fire('Error', 'Pilih unit terlebih dahulu', 'error')
            return
        }

        $.post('ajax_operator.php', {

            action: 'get_unit_parts',
            serial: barcode.Z2,
            units: units

        }, function(res) {

            let r = JSON.parse(res)

            if (r.error) {
                Swal.fire('Error', r.message, 'error')
                return
            }

            let rows = ''

            r.parts.forEach((p, i) => {

                rows += `
<tr
data-part="${p.part_code}"
data-lot="${p.lot_no}"
data-ref="${p.ref_number}"
data-part-id="${p.part_id}"
>

<td>
<input type="checkbox"
class="chkPart"
data-index="${i}"
checked>
</td>

<td>${p.part_code}</td>
<td>${p.part_name}</td>
<td>${p.lot_no}</td>
<td>${p.ref_number}</td>
<td>${p.used_qty}</td>

<td>
<input type="number"
class="ngQty form-control form-control-sm"
min="0"
max="${p.used_qty}"
value="${p.used_qty}"
data-index="${i}">
</td>

<td>
<select class="ngTypeRow form-control form-control-sm"
data-index="${i}">
</select>
</td>

</tr>
`
            })

            Swal.fire({

                title: 'EXIT MECA (UNIT)',
                width: 900,

                html: `

            <div style="margin-bottom:10px">

                NG TYPE

               

            </div>

            <table class="table table-bordered table-sm">

                <thead style="background:#f1f3f5">

                    <tr>

                        <th>
                        <input type="checkbox" id="checkAll">
                        </th>

                        <th>PART CODE</th>
                        <th>PART NAME</th>
                        <th>LOT</th>
                        <th>USED</th>
                        <th>NG QTY</th>

                    </tr>

                </thead>

                <tbody>

                    ${rows}

                </tbody>

            </table>

            `,

                confirmButtonText: 'SUBMIT',

                preConfirm: () => {

                    let parts = []

                    $('.chkPart:checked').each(function() {

                        let row = $(this).closest('tr')
                        let ref = row.data('ref')
                        let part = row.data('part')
                        let lot = row.data('lot')

                        let i = $(this).data('index')
                        let qty = $(`.ngQty[data-index=${i}]`).val()

                        if (qty > 0) {

                            let ngType = row.find('.ngTypeRow').val()

                            parts.push({
                                part_code: part,
                                lot_no: lot,
                                ref_number: ref,
                                ng_qty: qty,
                                ng_type: ngType
                            })

                        }

                    })

                    if (parts.length == 0) {

                        Swal.showValidationMessage(
                            'Pilih part NG terlebih dahulu'
                        )

                        return false
                    }

                    return parts
                }

            }).then(res => {

                if (!res.isConfirmed) return

                let parts = res.value

                sendExitUnit(barcode, parts)

            })

            // ==============================
            // 🔥 NAH INI DIA YANG LU TANYA
            // ==============================

            setTimeout(() => {

                $('.ngTypeRow').each(function() {

                    let row = $(this).closest('tr')
                    let partId = row.data('part-id')
                    let select = $(this)

                    $.get('ajax_operator.php', {
                        action: 'get_ng_by_part',
                        part_id: partId
                    }, function(res) {

                        let list = JSON.parse(res)
                        let html = ''

                        list.forEach(n => {
                            html += `<option value="${n.id}">
                            ${n.ng_name}
                         </option>`
                        })

                        select.html(html)

                    })

                })

            }, 100)
            $('#checkAll').click(function() {

                let c = this.checked

                $('.chkPart').prop('checked', c)

                $('.chkPart').each(function() {

                    let i = $(this).data('index')
                    let max = $(`.ngQty[data-index=${i}]`).attr('max')

                    if (c)
                        $(`.ngQty[data-index=${i}]`).val(max)
                    else
                        $(`.ngQty[data-index=${i}]`).val(0)

                })

            })

        })

    }

    function sendExitUnit(barcode, parts) {

        if (parts.length === 0) {
            Swal.fire('Error', 'Pilih part NG', 'error')
            return
        }

        $.post('ajax_operator.php', {

            action: 'exit_meca',
            serial: barcode.Z2,
            qty: barcode.Z3,
            ref_product: barcode.Z5,
            parts: parts,
            line: lineId,
            shift: activeShift,

        }, function(res) {

            let r = JSON.parse(res)

            if (r.error) {
                Swal.fire('Error', r.message, 'error')
                return
            }

            Swal.fire({
                icon: 'success',
                title: 'EXIT MECA SUCCESS',
                timer: 1500,
                showConfirmButton: false
            })

            reloadMaterial()
            reloadAll()

        })

    }
    $('#btnExitMeca').click(function() {

        Swal.fire({
            title: 'SCAN PRODUCT',
            html: `
            <div style="text-align:center">
                <input id="exitScan"
                       class="swal2-input"
                       placeholder="Scan Barcode Product"
                       autocomplete="off">
            </div>
        `,
            focusConfirm: false,
            confirmButtonText: 'SCAN',
            showCancelButton: true,
            onOpen: () => {

                const input = document.getElementById('exitScan');

                setTimeout(() => {
                    input.focus();
                    input.select();
                }, 200);

                // 🔥 ENTER = SUBMIT (INI YANG LO MAU)
                input.addEventListener('keydown', function(e) {

                    if (e.key === 'Enter') {

                        e.preventDefault();

                        const val = input.value.trim();

                        if (!val) return;

                        // langsung klik confirm
                        document.querySelector('.swal2-confirm').click();
                    }

                });

            },
            preConfirm: () => {

                let code = $('#exitScan').val().trim()

                if (!code) {
                    Swal.showValidationMessage('Barcode kosong')
                    return false
                }

                let data = parseBarcode(code)

                if (!data.Z2) {
                    Swal.showValidationMessage('Barcode tidak valid')
                    return false
                }

                return data
            }

        }).then(res => {

            if (!res.isConfirmed) return

            let data = res.value

            // LANGSUNG KE EXIT BOX
            loadExitBox(data)
        })

    })



    /* ========================================
       MODE SELECT
    ======================================== */

    function openExitMode(barcode) {

        Swal.fire({

            title: 'EXIT MECA',
            html: `

        <div style="display:flex;gap:10px;justify-content:center">

            <button id="exitBox"
                    class="btn btn-danger btn-lg">
                NG BOX
            </button>

            <button id="exitUnit"
                    class="btn btn-warning btn-lg">
                NG UNIT
            </button>

        </div>

        `,
            showConfirmButton: false

        })

        $(document).off('click', '#exitBox').on('click', '#exitBox', () => {
            loadExitBox(barcode)
        })

        $(document).off('click', '#exitUnit').on('click', '#exitUnit', () => {
            loadExitUnit(barcode)
        })

    }

    function loadNgType(callback) {

        $.get('ajax_operator.php', {
            action: 'get_ng_type'
        }, function(res) {

            let data = JSON.parse(res)

            let html = ''

            data.forEach(n => {
                html += `<option value="${n.id}">
                        ${n.ng_name}
                     </option>`
            })

            callback(html)

        })

    }

    function loadExitBox(barcode) {

        $.post('ajax_operator.php', {
            action: 'get_exit_material',
            serial: barcode.Z2,
            ref: barcode.Z5
        }, function(res) {

            let r = JSON.parse(res)

            if (r.error) {
                Swal.fire('Error', r.message, 'error')
                return
            }

            let rows = ''

            r.parts.forEach((p, i) => {

                rows += `
<tr
data-part="${p.part_code}"
data-lot="${p.lot_no}"
data-ref="${p.ref_number}"
data-part-id="${p.part_id}"
>

<td>
<input type="checkbox"
class="chkPart"
data-index="${i}"
checked>
</td>

<td>${p.part_code}</td>
<td>${p.part_name}</td>
<td>${p.lot_no}</td>
<td>${p.ref_number}</td>
<td>${p.used_qty}</td>

<td>
<input type="number"
class="ngQty form-control form-control-sm"
min="0"
max="${p.used_qty}"
value="${p.used_qty}"
data-index="${i}">
</td>

<td>
<select class="ngTypeRow"></select>
</td>

</tr>
`
            })

            Swal.fire({

                title: 'EXIT MECA',

                width: 900,

                html: `

            <div style="margin-bottom:10px">

                NG TYPE

             
            </div>
            <table class="table table-sm table-bordered">

                <thead style="background:#f1f3f5">

                    <tr>

                        <th>
                            <input type="checkbox"
                                   id="checkAll">
                        </th>

                        <th>PART CODE</th>
                        <th>PART NAME</th>
                        <th>LOT</th>
                        <th>REF</th>
                        <th>USED</th>
                        <th>NG QTY</th>
                        <th>NG</th>

                    </tr>

                </thead>

                <tbody>

                    ${rows}

                </tbody>

            </table>

            `,

                confirmButtonText: 'SUBMIT',

                preConfirm: () => {

                    let parts = []

                    $('.chkPart:checked').each(function() {

                        let row = $(this).closest('tr')

                        let part = row.data('part')
                        let lot = row.data('lot')
                        let ref = row.data('ref')

                        let i = $(this).data('index')
                        let qty = $(`.ngQty[data-index=${i}]`).val()

                        if (qty > 0) {

                            let ngType = row.find('.ngTypeRow').val()

                            parts.push({
                                part_code: part,
                                lot_no: lot,
                                ref_number: ref,
                                ng_qty: qty,
                                ng_type: ngType // 🔥 WAJIB
                            })

                        }

                    })

                    if (parts.length == 0) {

                        Swal.showValidationMessage(
                            'Pilih part NG terlebih dahulu'
                        )

                        return false
                    }

                    return parts
                }

            }).then(res => {

                if (!res.isConfirmed) return

                let parts = res.value


                sendExitBox(barcode, parts)

            })

            setTimeout(() => {

                $('.ngTypeRow').each(function() {

                    let row = $(this).closest('tr')
                    let partId = row.data('part-id')
                    let select = $(this)

                    $.get('ajax_operator.php', {
                        action: 'get_ng_by_part',
                        part_id: partId
                    }, function(res) {

                        let list = JSON.parse(res)
                        let html = ''

                        list.forEach(n => {
                            html += `<option value="${n.id}">
                            ${n.ng_name}
                         </option>`
                        })

                        select.html(html)

                    })

                })

            }, 100)
            $('#checkAll').prop('checked', true)

            /* SELECT ALL */

            $('#checkAll').click(function() {

                let c = this.checked

                $('.chkPart').prop('checked', c)

                $('.chkPart').each(function() {

                    let i = $(this).data('index')

                    let max = $(`.ngQty[data-index=${i}]`).attr('max')

                    if (c)
                        $(`.ngQty[data-index=${i}]`).val(max)

                    else
                        $(`.ngQty[data-index=${i}]`).val(0)

                })

            })


            /* AUTO FILL */

            $('.chkPart').change(function() {

                let i = $(this).data('index')
                let max = $(`.ngQty[data-index=${i}]`).attr('max')

                if (this.checked) {
                    $(`.ngQty[data-index=${i}]`).val(max)
                } else {
                    $(`.ngQty[data-index=${i}]`).val(0)
                }

                // update checkAll
                let total = $('.chkPart').length
                let checked = $('.chkPart:checked').length

                $('#checkAll').prop('checked', total === checked)

            })


        })

    }

    function loadExitUnit(barcode) {

        $.post('ajax_operator.php', {
            action: 'get_units',
            serial: barcode.Z2
        }, function(res) {

            let r = JSON.parse(res)

            if (r.error) {
                Swal.fire('Error', r.message, 'error')
                return
            }

            let html = `<div class="unit-grid">`

            r.units.forEach(u => {

                html += `
            <div class="unit-card"
                 data-unit="${u}">
                 UNIT ${u}
            </div>
            `

            })

            html += '</div>'


            Swal.fire({

                title: 'Pilih Unit NG',

                html: html,

                confirmButtonText: 'NEXT'

            }).then(res => {

                if (!res.isConfirmed) return

                let units = []

                $('.unit-card.active').each(function() {
                    units.push($(this).data('unit'))
                })

                loadUnitParts(barcode, units)

            })


            $('.unit-card').click(function() {

                $(this).toggleClass('active')

            })


        })

    }

    /* ========================================
   IN MECA BUTTON
======================================== */

    $('#btnInMeca').click(function() {

        Swal.fire({
            title: 'SCAN PRODUCT (IN MECA)',
            html: `
            <div style="text-align:center">
                <input id="inScan"
                       class="swal2-input"
                       placeholder="Scan Barcode Product"
                       autocomplete="off">
            </div>
        `,
            focusConfirm: false,
            confirmButtonText: 'SCAN',
            showCancelButton: true,
            onOpen: () => {

                const input = document.getElementById('inScan');

                setTimeout(() => {
                    input.focus();
                    input.select();
                }, 200);

                input.addEventListener('keydown', function(e) {

                    if (e.key === 'Enter') {

                        e.preventDefault()

                        if (input.value.trim() !== '') {

                            document.querySelector('.swal2-confirm').click()

                        }

                    }

                })

            },
            preConfirm: () => {

                let code = $('#inScan').val().trim()

                if (!code) {
                    Swal.showValidationMessage('Barcode kosong')
                    return false
                }

                let data = parseBarcode(code)

                if (!data.Z2) {
                    Swal.showValidationMessage('Format barcode salah')
                    return false
                }

                return data
            }

        }).then(res => {

            if (!res.isConfirmed) return

            let barcode = res.value

            sendInMeca(barcode)

        })

    })

    /* ========================================
       SEND IN MECA
    ======================================== */

    function sendInMeca(barcode) {

        $.post('ajax_operator.php', {

            action: 'in_meca',
            serial: barcode.Z2,
            ref: barcode.Z5,
            qty: barcode.Z3,

        }, function(res) {

            let r = JSON.parse(res)

            if (r.error) {
                Swal.fire('Error', r.message, 'error')
                return
            }

            Swal.fire({
                icon: 'success',
                title: 'IN MECA SUCCESS',
                timer: 1500,
                showConfirmButton: false
            })

            reloadMaterial()
            reloadAll()

        })

    }

    $(document).on('click', '.btnRemoveLot', function() {

        let part = $(this).data('part')
        let lot = $(this).data('lot')
        let ref = $(this).data('ref')

        Swal.fire({

            title: 'Close Material',
            text: `Yakin ingin menghapus lot ${lot}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'YES REMOVE'

        }).then(res => {

            if (!res.isConfirmed) return

            $.post('ajax_operator.php', {

                action: 'remove_active_material',
                part: part,
                ref: ref,
                lot: lot,
                line: lineId

            }, function(res) {

                let r = JSON.parse(res)

                if (r.error) {

                    Swal.fire('Error', r.message, 'error')
                    return

                }

                Swal.fire('Success', 'Lot berhasil dihapus', 'success')

                reloadMaterial()

            })

        })

    })

    $(document).on('click', '.btnAdjustLot', function() {

        let part = $(this).data('part')
        let lot = $(this).data('lot')
        let remain = $(this).data('remain')
        let ref = $(this).data('ref')

        Swal.fire({

            title: 'Adjust Material',

            html: `

Current Remain : <b>${remain}</b>

<input id="adjQty"
type="number"
class="swal2-input"
placeholder="Qty adjustment">

<select id="adjType"
class="swal2-input">

<option value="ADD">ADD</option>
<option value="SUB">SUBTRACT</option>

</select>

`,

            confirmButtonText: 'SAVE',

            preConfirm: () => {

                return {

                    qty: $('#adjQty').val(),
                    type: $('#adjType').val()

                }

            }

        }).then(res => {

            if (!res.isConfirmed) return

            let d = res.value

            $.post('ajax_operator.php', {

                action: 'adjust_material',
                part: part,
                lot: lot,
                ref: ref,
                type: d.type,
                qty: d.qty,
                line: lineId

            }, function(res) {

                reloadMaterial()

            })

        })

    })
    $(document).on('click', '.btnNgLot', function() {

        let part = $(this).data('part')
        let lot = $(this).data('lot')
        let remain = $(this).data('remain')
        let ref = $(this).data('ref')
        let partId = $(this).data('part-id');

        Swal.fire({

            title: 'Material NG',

            html: `
            <input id="ngQty"
            type="number"
            class="swal2-input"
            placeholder="NG Qty">

            <select id="ngReason" class="swal2-input"></select>
        `,

            confirmButtonText: 'SUBMIT',

            onOpen: () => {

                // 🔥 LOAD NG TYPE DI SINI (BENER)
                $.get('ajax_operator.php', {
                    action: 'get_ng_by_part',
                    part_id: partId
                }, function(res) {

                    let list = JSON.parse(res);
                    let html = '';

                    list.forEach(n => {
                        html += `<option value="${n.id}">${n.ng_name}</option>`;
                    });

                    $('#ngReason').html(html);

                });

            },

            preConfirm: () => {

                return {
                    qty: $('#ngQty').val(),
                    reason: $('#ngReason').val()
                }

            }

        }).then(res => {

            if (!res.isConfirmed) return

            let d = res.value

            $.post('ajax_operator.php', {

                action: 'material_ng',
                part: part,
                ref: ref,
                lot: lot,
                qty: d.qty,
                reason: d.reason,
                line: lineId,
                shift: activeShift

            }, function(res) {

                reloadMaterial()

            })

        })

    })
    setInterval(function() {

        if (!$('.swal2-container:visible').length) {

            if (
                document.activeElement.id !== 'remark' &&
                document.activeElement.id !== 'assySelect' &&
                document.activeElement.id !== 'scanInput'
            ) {

                focusScanner();

            }

        }

    }, 2000);

    $(document).on('shown.bs.modal', function() {
        $('input:visible:first').focus();
    });
</script>