<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['halaman'] = 'part assy';
$_SESSION['menu']    = 'part_assy';
$_SESSION['subHalaman'] = ' | Import CSV';
$sql = "
SELECT 
    p.part_code, 
    p.part_name,
    s.name_supplier
FROM tbl_part p
LEFT JOIN tbl_supplier s ON p.supplier = s.id_supplier
WHERE p.status_assy = 0
ORDER BY p.part_code ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <div class="card shadow-sm">
            <div class="card-header">
                <h3>Import BOM CSV</h3>
                <a href="<?= BASE_URL ?>pages/part_assy/Bom_List.csv" download
                    class="btn btn-light-primary btn-sm">

                    <i class="fas fa-download mr-1"></i>
                    Download Template

                </a>
            </div>

            <div class="card-body">

                <!-- HEADER -->
                <div class="row mb-5">
                    <div class="col-md-4">
                        <label>Model</label>
                        <input type="text" id="modelName" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Part Code</label>
                        <input type="text" id="assyCode" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label>Part Name</label>
                        <input type="text" id="assyName" class="form-control">
                    </div>
                </div>

                <!-- UPLOAD -->
                <div class="mb-5">
                    <label>Upload CSV</label>
                    <input type="file" id="csvFile" class="form-control" accept=".csv">
                </div>

                <hr>

                <!-- TABLE -->
                <div class="d-flex justify-content-between mb-3">
                    <h4>Preview BOM</h4>
                    <button class="btn btn-primary btn-sm" id="btnAddRow">+ Tambah</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
                <div class="mt-10 text-right">
                    <button class="btn btn-success" id="btnSave">Simpan</button>
                </div>
            </div>
        </div>

    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
<script src="<?= BASE_URL ?>assets/js/papaparse.min.js"></script>

<script>
    const parts = <?= json_encode($parts); ?>;

    /* ======================
       GET SELECTED PARTS
    ====================== */
    function getSelectedParts() {
        let arr = [];
        document.querySelectorAll('.part-select').forEach(s => {
            if (s.value) arr.push(s.value);
        });
        return arr;
    }

    /* ======================
       REFRESH OPTION (ANTI DUPLICATE)
    ====================== */
    function refreshPartOptions() {

        const used = getSelectedParts();

        document.querySelectorAll('.part-select').forEach(select => {

            [...select.options].forEach(opt => {
                if (!opt.value) return;

                let disabled = false;

                // disable kalau sudah dipakai di row lain
                if (used.includes(opt.value) && opt.value !== select.value) {
                    disabled = true;
                }

                opt.disabled = disabled;

                if (disabled) {
                    opt.textContent = '❌ ' + opt.value + ' - ' + opt.dataset.name;
                    opt.style.color = '#dc3545';
                } else {
                    opt.textContent = opt.value + ' - ' + opt.dataset.name;
                    opt.style.color = '';
                }

            });

        });
    }

    /* ======================
       RENDER ROW
    ====================== */
    function renderRow(i, code = '', name = '-', qty = '', unit = '', supplier = '-', error = false) {

        let options = '<option value="">Select</option>';
        parts.forEach(p => {
            options += `<option value="${p.part_code}" 
        ${p.part_code === code ? 'selected' : ''} 
        data-name="${p.part_name}"
        data-supplier="${p.name_supplier || '-'}">
    ${p.part_code} - ${p.part_name}</option>`;
        });

        return `
<tr ${error ? 'style="background:#ffe5e5"' : ''}>
<td class="no"></td>

<td>
<select class="form-control part-select">${options}</select>
</td>

<td class="name">${name}</td>

<td><input type="number" class="form-control qty" value="${qty}"></td>

<td><input type="text" class="form-control unit" value="${unit || 'Pcs'}"></td>

<td class="supplier">${supplier}</td>

<td>${error ? '<span style="color:red;font-weight:bold">NOT FOUND</span>' : '<span style="color:green;font-weight:bold">OK</span>'}</td>

<td><button class="btn btn-danger btn-sm del">X</button></td>
</tr>`;
    }

    /* ======================
       RENUMBER
    ====================== */
    function renumber() {
        $("#tableBody tr").each(function(i) {
            $(this).find(".no").text(i + 1);
        });
    }

    /* ======================
       PARSER
    ====================== */
    function parseRow(row) {

        let partCode = row[0]?.trim();
        let qty = row[2]?.trim();
        let unit = row[3]?.trim();

        if (!partCode || !/^[A-Za-z0-9]+$/.test(partCode)) return null;

        return {
            partCode,
            qty,
            unit: unit || 'Pcs'
        };
    }

    /* ======================
       IMPORT CSV
    ====================== */
    $("#csvFile").change(function(e) {

        let file = e.target.files[0];
        if (!file) return;

        Papa.parse(file, {
            header: false,
            skipEmptyLines: true,
            delimitersToGuess: [',', ';', '\t'],

            complete: function(res) {

                let rows = res.data;

                $("#modelName").val(rows.find(r => r[0]?.trim() === 'Model')?.[1] || '');
                $("#assyCode").val(rows.find(r => r[0]?.trim() === 'Part Code')?.[1] || '');
                $("#assyName").val(rows.find(r => r[0]?.trim() === 'Part Name')?.[1] || '');

                let start = rows.findIndex(r =>
                    (r[0] || '').toUpperCase().includes('PART CODE')
                );

                if (start === -1) {
                    Swal.fire("Error", "Format CSV tidak valid", "error");
                    return;
                }

                start++;

                $("#tableBody").html('');
                let index = 0;

                for (let i = start; i < rows.length; i++) {

                    let parsed = parseRow(rows[i]);
                    if (!parsed) continue;

                    let part = parts.find(p => p.part_code === parsed.partCode);

                    if (part) {
                        $("#tableBody").append(
                            renderRow(index, parsed.partCode, part.part_name, parsed.qty, parsed.unit, part.name_supplier, false)
                        );
                    } else {
                        $("#tableBody").append(
                            renderRow(index, parsed.partCode, '-', parsed.qty, parsed.unit, '-', true)
                        );
                    }

                    index++;
                }

                renumber();
                refreshPartOptions(); // 🔥 penting
            }
        });

    });

    /* ======================
       ADD ROW
    ====================== */
    $("#btnAddRow").click(function() {
        $("#tableBody").append(renderRow($("#tableBody tr").length, '', '-', '', 'Pcs', '-'));
        renumber();
        refreshPartOptions();
    });

    /* ======================
       CHANGE SELECT
    ====================== */
    $(document).on("change", ".part-select", function() {

        let selected = $(this).find(":selected");

        let name = selected.data("name") || '-';
        let supplier = selected.data("supplier") || '-';

        let row = $(this).closest("tr");

        row.find(".name").text(name);
        row.find(".supplier").text(supplier);

        refreshPartOptions(); // 🔥 penting
    });

    /* ======================
       DELETE
    ====================== */
    $(document).on("click", ".del", function() {
        $(this).closest("tr").remove();
        renumber();
        refreshPartOptions(); // 🔥 penting
    });

    /* ======================
       SAVE (SWEETALERT)
    ====================== */
    $("#btnSave").click(function() {

        let data = [];
        let error = false;

        let model = $("#modelName").val().trim();
        let assy = $("#assyCode").val().trim();

        if (!model || !assy) {
            Swal.fire("Warning", "Model & Part Code wajib diisi!", "warning");
            return;
        }

        $("#tableBody tr").each(function() {

            let part_code = $(this).find(".part-select").val();
            let qty = $(this).find(".qty").val();
            let unit = $(this).find(".unit").val();
            let status = $(this).find("td:eq(6)").text();

            if (!part_code || !qty || !unit) {
                Swal.fire("Error", "Masih ada data kosong!", "error");
                error = true;
                return false;
            }

            if (status.includes("NOT FOUND")) {
                Swal.fire("Error", "Masih ada part tidak valid!", "error");
                error = true;
                return false;
            }

            if (qty <= 0) {
                Swal.fire("Error", "Qty harus > 0!", "error");
                error = true;
                return false;
            }

            data.push({
                part_code,
                qty,
                unit
            });
        });

        if (error || data.length === 0) return;

        Swal.fire({
            title: "Simpan data?",
            icon: "question",
            showCancelButton: true
        }).then(result => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: "<?= BASE_URL ?>pages/part_assy/save_bom.php",
                method: "POST",
                data: {
                    model,
                    assy_code: assy,
                    assy_name: $("#assyName").val(),
                    items: JSON.stringify(data)
                },
                beforeSend: () => {
                    Swal.fire({
                        title: "Menyimpan...",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(res) {

                    if (res.status === "success") {
                        Swal.fire("Success", res.msg, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.msg, "error");
                    }

                },
                error: () => {
                    Swal.fire("Error", "Gagal menyimpan!", "error");
                }
            });

        });

    });
</script>