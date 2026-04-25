<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$part = $_GET['part'] ?? '';

$_SESSION['halaman'] = 'material detail';
$_SESSION['menu'] = 'material_report';

// GET PART INFO
$stmt = $pdo->prepare("
    SELECT p.part_code, p.part_name, s.name_supplier
    FROM tbl_part p
    LEFT JOIN tbl_supplier s ON s.id_supplier = p.supplier
    WHERE p.part_code = ?
");
$stmt->execute([$part]);
$partData = $stmt->fetch(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

?>

<style>
    .card-summary {
        border-radius: 14px;
        padding: 20px;
        color: #fff;
        height: 100%;
    }

    .bg-ng {
        background: linear-gradient(45deg, #e74c3c, #c0392b);
    }

    .bg-loss {
        background: linear-gradient(45deg, #f39c12, #d35400);
    }

    .bg-over {
        background: linear-gradient(45deg, #2ecc71, #27ae60);
    }

    .summary-title {
        font-size: 13px;
        opacity: 0.8;
    }

    .summary-value {
        font-size: 22px;
        font-weight: bold;
    }

    .ng-item {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 4px 0;
        font-size: 13px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <!-- HEADER -->
        <div class="mb-5">
            <h2 class="font-weight-bold">Material Detail</h2>
            <div class="text-muted">
                <b><?= $partData['part_code'] ?></b> - <?= $partData['part_name'] ?><br>
                Supplier: <?= $partData['name_supplier'] ?? '-' ?>
            </div>
        </div>

        <!-- FILTER -->
        <div class="card card-custom shadow-sm mb-4">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-2">
                        <label>From</label>
                        <input type="date" id="date_from" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>To</label>
                        <input type="date" id="date_to" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>Type</label>
                        <select id="type" class="form-control">
                            <option value="all">All</option>
                            <option value="ng">NG</option>
                            <option value="loss">Loss</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button class="btn btn-dark w-100" onclick="loadAll()">Search</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="row mb-4">

            <!-- NG TYPE -->
            <div class="col-md-6">
                <div class="card-summary bg-ng">
                    <div class="summary-title">NG BY TYPE</div>
                    <div id="ngBreakdown"></div>
                    <hr style="border-color:rgba(255,255,255,0.3)">
                    <div class="summary-value">TOTAL: <span id="totalNG">0</span></div>
                </div>
            </div>

            <!-- LOSS -->
            <div class="col-md-3">
                <div class="card-summary bg-loss">
                    <div class="summary-title">TOTAL LOSS</div>
                    <div class="summary-value" id="totalLoss">0</div>
                </div>
            </div>

            <!-- OVER -->
            <div class="col-md-3">
                <div class="card-summary bg-over">
                    <div class="summary-title">TOTAL OVER</div>
                    <div class="summary-value" id="totalOver">0</div>
                </div>
            </div>

        </div>

        <!-- TABLE -->
        <div class="card card-custom shadow-sm">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Lot</th>
                            <th>Ref</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>Line</th>
                            <th>Shift</th>
                            <th>Date</th>
                            <th>Income</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody"></tbody>

                </table>

                <div class="d-flex justify-content-between mt-3">
                    <div id="info"></div>
                    <div>
                        <button class="btn btn-sm btn-light" onclick="prevPage()">Prev</button>
                        <button class="btn btn-sm btn-light" onclick="nextPage()">Next</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    let currentPage = 1;
    let part = "<?= $part ?>";

    function loadAll() {
        loadSummary();
        loadDetail(1);
    }

    /* ================= SUMMARY ================= */
    function loadSummary() {

        let from = document.getElementById('date_from').value;
        let to = document.getElementById('date_to').value;

        fetch(`api.php?action=summary&part=${part}&from=${from}&to=${to}`)
            .then(res => res.json())
            .then(res => {

                let totalNG = 0;
                let html = '';

                res.ng_detail.forEach(n => {
                    totalNG += parseInt(n.qty);

                    html += `
                <div class="ng-item">
                    <span>${n.ng_name}</span>
                    <b>${n.qty}</b>
                </div>`;
                });

                document.getElementById('ngBreakdown').innerHTML = html;
                document.getElementById('totalNG').innerText = totalNG;

                document.getElementById('totalLoss').innerText = res.total_loss;
                document.getElementById('totalOver').innerText = res.total_over;

            });
    }

    /* ================= TABLE ================= */
    function loadDetail(page = 1) {

        currentPage = page;

        let from = document.getElementById('date_from').value;
        let to = document.getElementById('date_to').value;
        let type = document.getElementById('type').value;

        fetch(`api.php?action=detail&part=${part}&page=${page}&from=${from}&to=${to}&type=${type}`)
            .then(res => res.json())
            .then(res => {

                let html = '';
                let no = res.start;

                res.data.forEach(r => {

                    let badge = r.type === 'NG' ?
                        '<span class="badge badge-danger">NG</span>' :
                        '<span class="badge badge-warning">LOSS</span>';

                    html += `
                <tr>
                    <td>${no++}</td>
                    <td>${badge}</td>
                    <td>${r.lot}</td>
                    <td>${r.ref}</td>
                    <td class="font-weight-bold">${r.qty}</td>
                    <td>${r.reason ?? '-'}</td>
                    <td>${r.line_name ?? '-'}</td>
                    <td>${r.shift ?? '-'}</td>
                    <td>${r.date}</td>
                    <td>${r.income}</td>
                </tr>`;
                });

                document.getElementById('tableBody').innerHTML = html;

                document.getElementById('info').innerHTML = `
                Showing ${res.start} - ${res.end} of ${res.total}
            `;

            });
    }

    function nextPage() {
        loadDetail(currentPage + 1);
    }

    function prevPage() {
        if (currentPage > 1) {
            loadDetail(currentPage - 1);
        }
    }

    loadAll();
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>