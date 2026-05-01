<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['halaman'] = 'operator planning';
$_SESSION['menu'] = 'production_planning';
$_SESSION['subHalaman'] = ' | Operator View';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';

$lineId     = $_SESSION['line_id'] ?? 7;
$operator   = $_SESSION['username'] ?? 'ANNA';
$today      = date('Y-m-d');

// SHIFT MASTER
$shiftStmt = $pdo->query("SELECT * FROM tbl_shift");
$shiftMaster = $shiftStmt->fetchAll(PDO::FETCH_ASSOC);

// ASSY TODAY
$ppStmt = $pdo->prepare("
SELECT DISTINCT product_code
FROM tbl_production_planning
WHERE production_date = ? AND line_id = ?
");
$ppStmt->execute([$today, $lineId]);
$assyList = $ppStmt->fetchAll(PDO::FETCH_COLUMN);
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

    .summary div {
        flex: 1;
        text-align: center;
        padding: 10px;
        font-weight: bold;
        color: #fff
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
</style>

<div class="d-flex flex-column flex-row-fluid wrapper pt-2" id="kt_wrapper">
    <div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
        <div class="content pt-0">
            <div class="container">
                <div class="operator-card">

                    <div class="row mb-3">
                        <div class="col-md-6">

                            <div class="row">
                                <div class="col-md-6 font-weight-bolder">LINE : <?= htmlspecialchars($lineId) ?></div>
                                <div class="col-md-6 font-weight-bolder" id="shiftText">SHIFT : -</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">OPERATOR : <?= htmlspecialchars($operator) ?></div>
                                <div class="col-md-6" id="dateText"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6" id="timeText"></div>
                            </div>

                            <div class="row mt-2 align-items-center justify-content-between">
                                <div class="pl-2">Input :</div>
                                <input type="text" id="scanInput" class="col-md-10 box" autocomplete="off">
                            </div>

                            <div class="row mt-2 align-items-center justify-content-between">
                                <div class="pl-2">Remark :</div>
                                <input type="text" id="remark" class="col-md-10 box">
                            </div>

                            <div class="row mt-2 align-items-center justify-content-between">
                                <div class="pl-2">ASSY :</div>
                                <select id="assySelect" class="col-md-10 box">
                                    <option value="">-- pilih --</option>
                                    <?php foreach ($assyList as $a): ?>
                                        <option value="<?= $a ?>"><?= $a ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>

                        <div class="col-md">
                            <div class="summary">
                                <div class="plan">PLANNING<br><span id="sumPlan">0</span></div>
                                <div class="actual">ACTUAL<br><span id="sumActual">0</span></div>
                                <div class="remain">REMAIN<br><span id="sumRemain">0</span></div>
                            </div>

                            <h5 class="small mt-3 font-weight-bolder">Planning Table</h5>
                            <div style="overflow-x:auto">
                                <table class="table table-bordered planning-table table-sm">
                                    <thead>
                                        <tr>
                                            <th>SHIFT</th>
                                            <th>07-08</th>
                                            <th>08-09</th>
                                            <th>09-10</th>
                                            <th>10-11</th>
                                            <th>11-12</th>
                                            <th>12-13</th>
                                            <th>13-14</th>
                                            <th>14-15</th>
                                            <th>15-16</th>
                                            <th>OT</th>
                                        </tr>
                                    </thead>
                                    <tbody id="planningBody">
                                        <tr>
                                            <td colspan="11">Silakan pilih ASSY</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-small small-table mt-0">
                        <thead>
                            <tr>
                                <th>PARTCODE</th>
                                <th>PARTNAME</th>
                                <th>USED</th>
                                <th>LOT</th>
                                <th>SPQ</th>
                                <th>REMAIN</th>
                            </tr>
                        </thead>
                        <tbody id="materialBody">
                            <tr>
                                <td colspan="6">Belum dipilih assy</td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="mt-4 small font-weight-bolder">OUTPUT</h5>
                    <table class="table table-bordered small-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Shift</th>
                                <th>Line</th>
                                <th>Operator</th>
                                <th>QTY/SPQ</th>
                                <th>LOT/Serial</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody id="outputBody">
                            <tr>
                                <td colspan="8">Belum ada data</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const shifts = <?= json_encode($shiftMaster) ?>;
    let activeShift = null;

    function detectShift() {
        let h = new Date().getHours();
        shifts.forEach(s => {
            if (s.start < s.end) {
                if (h >= s.start && h < s.end) activeShift = s.shift;
            } else {
                if (h >= s.start || h < s.end) activeShift = s.shift;
            }
        });
        $('#shiftText').text('SHIFT : ' + activeShift);
    }

    function formatTanggal() {
        let d = new Date();
        $('#dateText').text('DATE : ' + d.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }));
        $('#timeText').text('TIME : ' + d.toLocaleTimeString('id-ID', {
            hour12: false
        }));
    }

    setInterval(formatTanggal, 1000);
    detectShift();
    formatTanggal();

    // ======================
    // PLANNING (by ASSY)
    // ======================
    function loadPlanning(assy) {

        if (!assy) {
            $('#planningBody').html('<tr><td colspan="11">Silakan pilih ASSY</td></tr>');
            $('#sumPlan').text(0);
            return;
        }

        $.get('ajax_operator.php', {
            action: 'planning',
            assy: assy
        }, function(res) {

            $('#planningBody').html(res);

            // hitung total planning dari table
            let total = 0;
            $('#planningBody td').each(function() {
                let t = $(this).text();
                if (t.includes('/')) {
                    let q = parseInt(t.split('/')[1]) || 0;
                    total += q;
                }
            });

            $('#sumPlan').text(total);
        });
    }

    // ======================
    // BOM
    // ======================
    $('#assySelect').on('change', function() {
        let assy = this.value;

        loadPlanning(assy);

        if (!assy) {
            $('#materialBody').html('<tr><td colspan="6">Belum dipilih assy</td></tr>');
            return;
        }

        $.get('ajax_operator.php', {
            action: 'load_bom',
            assy
        }, res => {
            $('#materialBody').html(res);
        });
    });

    // ======================
    // SCAN
    // ======================
    $('#scanInput').on('keypress', function(e) {
        if (e.which != 13) return;
        let code = this.value.trim();
        this.value = '';
        if (!code) return;

        $.post('ajax_operator.php', {
            action: 'scan',
            code,
            assy: $('#assySelect').val(),
            remark: $('#remark').val()
        }, function(res) {

            if (res.needConfirm) {
                Swal.fire({
                    title: 'Lot masih ada sisa',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'ADD',
                    denyButtonText: 'REMOVE'
                }).then(r => {
                    if (r.isConfirmed) sendMode(code, 'add');
                    else if (r.isDenied) sendMode(code, 'remove');
                });
            } else reloadAll();

        }, 'json');
    });

    function sendMode(code, mode) {
        $.post('ajax_operator.php', {
            action: 'scan',
            code,
            mode,
            assy: $('#assySelect').val()
        }, () => reloadAll());
    }

    function reloadAll() {
        $('#assySelect').trigger('change');
        $.get('ajax_operator.php', {
            action: 'output'
        }, res => {
            $('#outputBody').html(res);
        });
    }
</script>