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

                    <div class="col-md-3">
                        <input type="date" id="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <select id="type" class="form-control">
                            <option value="all">All</option>
                            <option value="ng">NG</option>
                            <option value="loss">Loss</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-dark w-100" onclick="loadDetail(1)">Search</button>
                    </div>

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
                            <th>Date</th>
                            <th>income</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody"></tbody>

                </table>

                <!-- PAGINATION -->
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

    function loadDetail(page = 1) {

        currentPage = page;

        let date = document.getElementById('date').value;
        let type = document.getElementById('type').value;

        fetch(`api.php?action=detail&part=${part}&page=${page}&date=${date}&type=${type}`)
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
                <td>${r.date}</td>
                <td>${r.income}</td>
            </tr>
            `;
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

    loadDetail();
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>