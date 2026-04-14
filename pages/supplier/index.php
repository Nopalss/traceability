<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['menu'] = 'supplier';
$_SESSION['table'] = 'supplier';
$_SESSION['halaman'] = 'supplier';
$_SESSION['subHalaman'] = '';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <div class="card card-custom">

                <div class="card-header flex-wrap border-0 pt-6 pb-0">

                    <div class="card-title">
                        <h3 class="card-label">
                            Data Supplier
                        </h3>
                    </div>

                    <div class="card-toolbar">

                        <button class="btn btn-light-success mr-5 font-weight-bolder" id="btnImportCSV">
                            <i class="far fa-file-excel"></i> Import CSV
                        </button>

                        <button class="btn btn-primary font-weight-bolder" id="addSupplierBtn">
                            Add Supplier
                        </button>

                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-7">
                        <div class="row align-items-center">
                            <div class="col-lg-12 col-xl-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4 my-2 my-md-0">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" />
                                            <span><i class="flaticon2-search-1 text-muted"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
                </div>

            </div>

        </div>
    </div>
</div>


<!-- ================= MODAL IMPORT ================= -->

<div class="modal fade" id="importCSVModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Import Supplier CSV</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="alert alert-light-info">
                    Format CSV harus seperti ini:
                    <br>
                    <a href="<?= BASE_URL ?>pages/supplier/supplier.csv" download
                        class="btn btn-light-primary btn-sm">

                        <i class="fas fa-download mr-1"></i>
                        Download Template

                    </a>
                </div>

                <div class="form-group">
                    <label>Select CSV File</label>
                    <input type="file" id="csvFile" class="form-control" accept=".csv">
                </div>

                <div id="previewArea"></div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-success" id="uploadSupplierBtn">
                    Upload Supplier
                </button>

            </div>

        </div>
    </div>
</div>


<?php
require __DIR__ . '/../../includes/footer.php';
?>

<!-- PapaParse -->
<script src="<?= BASE_URL ?>assets/js/papaparse.min.js"></script>

<script>
    let supplierData = [];

    $("#btnImportCSV").click(function() {
        $("#importCSVModal").modal("show");
    });

    /* =============================
       NORMALIZE
    ============================= */
    function normalize(str) {
        return (str || "")
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\s+/g, " ");
    }

    /* =============================
       READ CSV (FIXED)
    ============================= */

    $("#csvFile").change(function(e) {

        let file = e.target.files[0];


        if (!file) {
            Swal.fire({
                icon: 'error',
                title: 'File tidak ditemukan'
            });
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'warning',
                title: 'File terlalu besar (max 2MB)'
            });
            return;
        }

        Papa.parse(file, {

            header: true,
            skipEmptyLines: true,

            complete: function(results) {

                let rows = results.data;
                supplierData = [];

                let html = `
<table class="table table-bordered table-hover">
<thead>
<tr>
<th width="60">No</th>
<th>Supplier Name</th>
</tr>
</thead>
<tbody>
`;

                let no = 1;

                rows.forEach(function(row) {

                    let name =
                        row["Supplier"] ||
                        row["supplier"] ||
                        row["SUPPLIER"] ||
                        Object.values(row)[0]; // fallback kalau tanpa header

                    if (!name) return;
                    let rawName = name;
                    name = normalize(name);

                    // skip kalau kosong setelah normalize
                    if (!name) return;

                    // hindari duplicate di preview
                    if (supplierData.includes(name)) return;

                    supplierData.push(name);

                    html += `
<tr>
<td>${no}</td>
<td>
<input type="text" class="form-control supplier-input" value="${rawName}">
</td>
</tr>
`;

                    no++;

                });

                html += "</tbody></table>";

                $("#previewArea").html(html);

            }

        });

    });

    /* =============================
       UPLOAD
    ============================= */

    $("#uploadSupplierBtn").click(function() {

        let suppliers = [];

        $(".supplier-input").each(function() {

            let name = $(this).val().trim();

            if (name !== '') {
                suppliers.push(name);
            }

        });

        if (suppliers.length === 0) {

            Swal.fire({
                icon: 'warning',
                title: 'Tidak ada data supplier'
            });

            return;

        }

        $.ajax({

            url: "upload_csv_supplier.php",
            type: "POST",
            data: {
                suppliers: suppliers
            },

            beforeSend: function() {

                Swal.fire({
                    title: 'Uploading...',
                    text: 'Sedang memproses supplier',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

            },

            success: function(res) {

                try {

                    let data = typeof res === 'string' ? JSON.parse(res) : res;

                    let message = `
Supplier berhasil ditambahkan : ${data.inserted} |
Supplier ditolak : ${data.rejected}
`;

                    if (data.duplicates.length > 0) {
                        message += "\n\nSupplier sudah ada:\n" + data.duplicates.join("\n");
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Import Supplier',
                        text: message
                    }).then(() => {
                        location.reload();
                    });

                } catch (err) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Response Error',
                        text: res
                    });

                }

            },

            error: function() {

                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Terjadi kesalahan saat upload'
                });

            }

        });

    });
</script>