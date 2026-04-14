<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$_SESSION['halaman'] = 'material report';
$_SESSION['menu'] = 'material_report';

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

?>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <!-- HEADER -->
            <div class="mb-5">
                <h2 class="font-weight-bold">Material Problem Analysis</h2>
                <p class="text-muted">Ranking Part berdasarkan NG & Loss</p>
            </div>

            <!-- FILTER -->
            <div class="card card-custom shadow-sm mb-5">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <input type="text" id="search" class="form-control" placeholder="Search part / supplier...">
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-dark w-100" onclick="loadData(1)">Search</button>
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
                                <th>Part</th>
                                <th>Supplier</th>
                                <th class="text-center text-danger">Total NG</th>
                                <th class="text-center text-warning">Total Loss</th>
                                <th class="text-center">Total Problem</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="tableBody"></tbody>

                    </table>

                    <!-- PAGINATION -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
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
</div>

<script>
    let currentPage = 1;

    function loadData(page = 1) {

        currentPage = page;
        let search = document.getElementById('search').value;

        fetch(`api.php?action=summary&page=${page}&search=${search}`)
            .then(res => res.json())
            .then(res => {

                let html = '';
                let no = res.start;

                res.data.forEach(r => {

                    let total = parseInt(r.total_ng) + parseInt(r.total_loss);

                    html += `
            <tr>
                <td>${no++}</td>
                <td>
                    <b>${r.part_code}</b><br>
                    <small class="text-muted">${r.part_name ?? '-'}</small>
                </td>
                <td>${r.name_supplier ?? '-'}</td>
                <td class="text-center text-danger font-weight-bold">${r.total_ng}</td>
                <td class="text-center text-warning font-weight-bold">${r.total_loss}</td>
                <td class="text-center font-weight-bold">${total}</td>
                <td>
                    <a href="detail.php?part=${r.part_code}" 
                       class="btn btn-sm btn-primary">
                       Detail
                    </a>
                </td>
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
        loadData(currentPage + 1);
    }

    function prevPage() {
        if (currentPage > 1) {
            loadData(currentPage - 1);
        }
    }

    loadData();
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>