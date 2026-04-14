<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['menu'] = 'customer';
$_SESSION['table'] = 'customer';
$_SESSION['halaman'] = 'customer';
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
                            Data Customer
                        </h3>
                    </div>

                    <div class="card-toolbar">

                        <button class="btn btn-light-success mr-5 font-weight-bolder" id="btnImportCSV">
                            <i class="far fa-file-excel"></i> Import CSV
                        </button>

                        <button class="btn btn-primary font-weight-bolder" id="addCustomerBtn">
                            Add Customer
                        </button>

                    </div>
                </div>

                <div class="card-body">
                    <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- MODAL IMPORT -->
<div class="modal fade" id="importCSVModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Import Customer CSV</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <div class="alert alert-light-info">
                    Format CSV harus seperti ini:
                    <br>
                    <a href="<?= BASE_URL ?>pages/customer/customer.csv" download
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
                <button class="btn btn-success" id="uploadCustomerBtn">
                    Upload Customer
                </button>
            </div>

        </div>
    </div>
</div>

<?php
require __DIR__ . '/../../includes/footer.php';
?>

<script src="<?= BASE_URL ?>assets/js/papaparse.min.js"></script>

<script>
    let customerData = [];

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
            .replace(/\s+/g, " ")
            .replace(/[^a-z0-9 ]/g, '');
    }

    /* =============================
       READ CSV
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

        // limit file size
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
                customerData = [];
                let customerSet = new Set();

                let html = `
<table class="table table-bordered table-hover">
<thead>
<tr>
<th width="60">No</th>
<th>Customer Name</th>
</tr>
</thead>
<tbody>
`;

                let no = 1;

                rows.forEach(function(row) {

                    let name =
                        row["Customer"] ||
                        row["customer"] ||
                        row["CUSTOMER"] ||
                        Object.values(row)[0];

                    if (!name) return;

                    let rawName = name.trim();
                    let normalized = normalize(rawName);

                    if (!normalized) return;

                    if (customerSet.has(normalized)) return;

                    customerSet.add(normalized);
                    customerData.push(normalized);

                    html += `
<tr>
<td>${no}</td>
<td>
<input type="text" class="form-control customer-input" value="${rawName}">
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
    $("#uploadCustomerBtn").click(function() {

        let customers = [];

        $(".customer-input").each(function() {

            let name = $(this).val();
            let normalized = normalize(name);

            if (!normalized) return;

            // validasi minimal ada huruf
            if (!/[a-z]/i.test(normalized)) return;

            customers.push(normalized);
        });

        // remove duplicate final
        customers = [...new Set(customers)];

        if (customers.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak ada data customer'
            });
            return;
        }

        $.ajax({
            url: "upload_csv_customer.php",
            type: "POST",
            data: {
                customers: customers
            },

            beforeSend: function() {
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Sedang memproses customer',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },

            success: function(res) {

                let data = typeof res === 'string' ? JSON.parse(res) : res;

                let message = `
Customer berhasil ditambahkan : ${data.inserted}
Customer ditolak : ${data.rejected}
`;

                if (data.duplicates.length > 0) {
                    message += "\n\nCustomer sudah ada:\n" + data.duplicates.join("\n");
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Import Customer',
                    text: message
                }).then(() => {
                    location.reload();
                });
            },

            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error'
                });
            }
        });
    });
</script>