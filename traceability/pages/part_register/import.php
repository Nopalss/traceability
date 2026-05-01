<?php

require_once __DIR__ . '/../../includes/config.php';

$_SESSION['halaman'] = 'part register';
$_SESSION['table'] = 'part_register';
$_SESSION['menu'] = 'part_register';
$_SESSION['subHalaman'] = '';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$sql = "SELECT id_supplier,name_supplier 
        FROM tbl_supplier 
        WHERE status='supplier'";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$supplier = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="d-flex flex-column-fluid">
        <div class="container">

            <div class="card card-custom shadow-sm">

                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="card-label">
                            <i class="fas fa-file-import text-primary mr-2"></i>
                            Import Part Register
                        </h3>
                    </div>
                </div>

                <div class="card-body">

                    <div class="alert alert-light-primary mb-7">

                        <b>CSV Format:</b>

                        <a href="<?= BASE_URL ?>pages/part_register/part.csv" download
                            class="btn btn-light-primary btn-sm">

                            <i class="fas fa-download mr-1"></i>
                            Download Template

                        </a>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <label class="font-weight-bold">
                                Upload CSV File
                            </label>

                            <input type="file"
                                id="csvFile"
                                class="form-control form-control-lg"
                                accept=".csv">

                            <small class="text-muted">
                                Supports CSV with comma in part name
                            </small>

                        </div>

                        <div class="col-md-6 d-flex align-items-end">

                            <button class="btn btn-success btn-lg px-8"
                                id="uploadPartBtn">

                                <i class="fas fa-upload mr-2"></i>
                                Upload Part

                            </button>

                        </div>

                    </div>

                    <hr class="my-7">

                    <div>

                        <h4 class="mb-4">
                            Preview Data
                        </h4>

                        <div id="previewArea"></div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="<?= BASE_URL ?>assets/js/papaparse.min.js"></script>

<script>
    let supplierList = <?= json_encode($supplier); ?>;

    /* ==============================
       NORMALIZE FUNCTION
    ============================== */
    function normalizeCode(str) {
        return (str || "")
            .toString()
            .trim()
            .replace(/\s+/g, '');
    }

    function normalizeName(str) {
        return (str || "")
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\s*,\s*/g, ',')
            .replace(/\s+/g, ' ');
    }

    /* ==============================
       CSV READ
    ============================== */
    $("#csvFile").change(function(e) {

        let file = e.target.files[0];

        if (!file) {
            Swal.fire("Error", "File tidak ditemukan", "error");
            return;
        }

        // 🔥 LIMIT FILE SIZE
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire("Warning", "File terlalu besar (max 2MB)", "warning");
            return;
        }

        Papa.parse(file, {

            header: true,
            skipEmptyLines: true,
            dynamicTyping: false,

            complete: function(results) {

                let rows = results.data;

                let html = `
<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead class="thead-light">
<tr>
<th width="60">#</th>
<th>Part Code</th>
<th>Part Name</th>
<th>Supplier</th>
</tr>
</thead>
<tbody>
`;

                let no = 1;

                rows.forEach(function(row) {

                    let part_code =
                        row["Part Code"] ||
                        row["part_code"] ||
                        row["PART_CODE"];

                    let part_name =
                        row["Part Name"] ||
                        row["part_name"] ||
                        row["PART_NAME"];

                    let supplier =
                        row["Supplier"] ||
                        row["supplier"] ||
                        row["SUPPLIER"];

                    if (!part_code) return;

                    let codeNorm = normalizeCode(part_code);

                    // 🔥 SKIP DUPLICATE CSV


                    let supplierOptions = "";

                    supplierList.forEach(function(s) {

                        let selected = "";

                        if (normalizeName(s.name_supplier) === normalizeName(supplier)) {
                            selected = "selected";
                        }

                        supplierOptions += `
<option value="${s.id_supplier}" ${selected}>
${s.name_supplier}
</option>
`;

                    });

                    html += `
<tr>
<td class="text-center">${no}</td>

<td>
<input type="text" class="form-control part_code" value="${codeNorm}">
</td>

<td>
<input type="text" class="form-control part_name" value="${part_name ?? ""}">
</td>

<td>
<select class="form-control supplier">
${supplierOptions}
</select>
</td>

</tr>
`;

                    no++;

                });

                html += `
</tbody>
</table>
</div>
`;

                $("#previewArea").html(html);

            }

        });

    });

    /* ==============================
       UPLOAD
    ============================== */
    $("#uploadPartBtn").click(function() {

        let data = [];

        $("#previewArea tbody tr").each(function() {

            let part_code = normalizeCode($(this).find(".part_code").val());
            let part_name = normalizeName($(this).find(".part_name").val());
            let supplier = $(this).find(".supplier").val();

            if (!part_code) return;

            // 🔥 VALIDASI NUMERIC
            if (!/^[0-9]+$/.test(part_code)) return;

            // 🔥 SKIP DUPLICATE FINAL

            data.push({
                part_code: part_code,
                part_name: part_name.toUpperCase(),
                supplier: supplier
            });

        });

        if (data.length === 0) {
            Swal.fire("Warning", "Tidak ada data untuk di upload", "warning");
            return;
        }

        $.ajax({

            url: "upload_part_csv.php",
            type: "POST",
            data: {
                parts: data
            },
            dataType: "json",

            beforeSend: function() {

                Swal.fire({
                    title: "Uploading...",
                    text: "Processing data",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

            },

            success: function(r) {

                Swal.fire({
                    icon: "success",
                    title: "Import Result",
                    html: `
<b>Inserted :</b> ${r.inserted}<br>
<b>Rejected :</b> ${r.rejected}
`
                }).then(() => {
                    location.reload();
                });

            },

            error: function() {

                Swal.fire(
                    "Error",
                    "Terjadi kesalahan saat upload",
                    "error"
                );

            }

        });

    });
</script>