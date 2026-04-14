<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['halaman'] = 'part assy';
$_SESSION['menu']    = 'part_assy';
$_SESSION['subHalaman'] = ' | Create Part Assy';
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
                <h3>Create Bom List</h3>
                <a href="<?= BASE_URL ?>pages/part_assy/Bom_List.csv" download
                    class="btn btn-light-primary btn-sm">

                    <i class="fas fa-download mr-1"></i>
                    Download Template CSV

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
                                <!-- <th>Part Name</th> -->
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Supplier</th>
                                <th>Remark</th>
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
    function renderRow(i, code = '', name = '-', qty = '', unit = '', supplier = '-', remark = 0, error = false, isCsvSupplier = 0) {

        let options = '<option value="">Select</option>';
        parts.forEach(p => {
            options += `<option 
    value="${p.part_code}__${p.name_supplier}"
    ${p.part_code === code && p.name_supplier === supplier ? 'selected' : ''}
    data-code="${p.part_code}"
    data-name="${p.part_name}"
    data-supplier="${p.name_supplier || '-'}">
    ${p.part_code} - ${p.part_name} - ${p.name_supplier}
</option>`;
        });

        return `
<tr data-csv-supplier="${isCsvSupplier}" ${error ? 'style="background:#ffe5e5"' : ''}>
<td class="no" style="font-size: .69rem"></td>

<td>
<select class="form-control part-select" style="font-size: .69rem">${options}</select>
</td>


<td><input type="number" class="form-control qty" value="${qty}" style="font-size: .69rem"></td>

<td><input type="text" class="form-control unit" value="${unit || 'Pcs'}" style="font-size: .69rem"></td>

<td class="supplier" style="font-size: .69rem">${supplier}</td>
<td>
<select class="form-control remark" style="font-size: .69rem" >
    <option value="0" ${remark == 0 ? 'selected' : ''}>MAIN</option>
    <option value="1" ${remark == 1 ? 'selected' : ''}>SUBSTITUTE</option>
</select>
</td>

<td style="font-size: .69rem">${error ? '<span style="color:red;font-weight:bold">NOT FOUND</span>' : '<span style="color:green;font-weight:bold">OK</span>'}</td>

<td style="font-size: .69rem"><button class="btn btn-danger btn-sm del">X</button></td>
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

        // 🔥 SAFE parsing
        let supplier = row[4] ? row[4].trim() : '';
        let remarkRaw = row[5] ? row[5].trim().toUpperCase() : '';

        if (!partCode || !/^[A-Za-z0-9]+$/.test(partCode)) return null;

        let remark = 0; // default MAIN

        if (remarkRaw === 'SUBSTITUTE') {
            remark = 1;
        }

        return {
            partCode,
            qty,
            unit: unit || 'Pcs',
            supplier,
            remark
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

                    let part = parts.find(p =>
                        p.part_code === parsed.partCode &&
                        p.name_supplier === parsed.supplier
                    );
                    if (!part) {
                        part = parts.find(p => p.part_code === parsed.partCode);
                    }

                    if (part) {
                        let isCsvSupplier = parsed.supplier ? 1 : 0;
                        let supplierFinal = parsed.supplier || part.name_supplier;
                        $("#tableBody").append(
                            renderRow(index, parsed.partCode, part.part_name, parsed.qty, parsed.unit, supplierFinal, parsed.remark, false, isCsvSupplier)
                        );
                    } else {
                        $("#tableBody").append(
                            renderRow(index, parsed.partCode, '-', parsed.qty, parsed.unit, '-', parsed.remark, true, 0)
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
        $("#tableBody").append(renderRow($("#tableBody tr").length, '', '-', '', 'Pcs', '-', 0));
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

        if (!row.data("csv-supplier")) {
            row.find(".supplier").text(supplier);
        }

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


    $("#btnSave").click(function() {

        let data = [];
        let error = false;
        let used = new Set(); // 🔥 duplicate guard

        let model = $("#modelName").val().trim();
        let assy = $("#assyCode").val().trim();

        if (!model || !assy) {
            Swal.fire("Warning", "Model & Part Code wajib diisi!", "warning");
            return;
        }

        $("#tableBody tr").each(function() {
            let val = $(this).find(".part-select").val();
            let [part_code, supplier_select] = val.split("__");
            let qty = parseFloat($(this).find(".qty").val());
            let unit = $(this).find(".unit").val().trim();
            let status = $(this).find("td:eq(6)").text();
            let remark = $(this).find(".remark").val();
            let supplier = $(this).find(".supplier").text();
            if (!part_code || !qty || !unit) {
                Swal.fire("Error", "Masih ada data kosong!", "error");
                error = true;
                return false;
            }

            let key = part_code + '__' + supplier;

            if (used.has(key)) {
                Swal.fire("Error", "Duplicate part tidak boleh!", "error");
                error = true;
                return false;
            }

            used.add(key);

            if (status.includes("NOT FOUND")) {
                Swal.fire("Error", "Masih ada part tidak valid!", "error");
                error = true;
                return false;
            }

            if (isNaN(qty) || qty <= 0) {
                Swal.fire("Error", "Qty harus > 0!", "error");
                error = true;
                return false;
            }

            // normalize unit
            unit = unit.charAt(0).toUpperCase() + unit.slice(1).toLowerCase();

            data.push({
                part_code,
                qty,
                unit,
                remark,
                supplier
            });
        });

        if (error || data.length === 0) return;

        // 🔥 disable button biar ga double klik
        $("#btnSave").prop("disabled", true);

        Swal.fire({
            title: "Simpan data?",
            icon: "question",
            showCancelButton: true
        }).then(result => {

            if (!result.isConfirmed) {
                $("#btnSave").prop("disabled", false);
                return;
            }

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

                    $("#btnSave").prop("disabled", false);

                    if (res.status === "success") {
                        Swal.fire("Success", res.msg, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res.msg, "error");
                    }

                },
                error: () => {
                    $("#btnSave").prop("disabled", false);
                    Swal.fire("Error", "Gagal menyimpan!", "error");
                }
            });

        });

    });
</script>