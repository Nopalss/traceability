<?php
require_once __DIR__ . '/../../includes/config.php';

$id = $_GET['id'] ?? 0;

/* =========================
   GET MODEL
========================= */
$stmt = $pdo->prepare("
SELECT m.*, p.part_name 
FROM tbl_model m 
JOIN tbl_part p ON m.part_code = p.part_code 
WHERE id = ?
");
$stmt->execute([$id]);
$model = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$model) {
    die("Data tidak ditemukan");
}

$assyCode = $model['part_code'];

/* =========================
   GET BOM + SUBS
========================= */
$stmt = $pdo->prepare("
SELECT 
    pa.part_code,
    pa.qty,
    pa.unit,
    pa.remark,
    pa.subs,
    p.part_name,
    s.name_supplier
FROM tbl_part_assy pa
LEFT JOIN tbl_part p ON pa.part_id = p.id_part
LEFT JOIN tbl_supplier s ON p.supplier = s.id_supplier
WHERE pa.part_assy = ?
");
$stmt->execute([$assyCode]);
$bom = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GET MASTER PART
========================= */
$stmt = $pdo->prepare("
SELECT 
    p.part_code, 
    p.part_name,
    s.name_supplier
FROM tbl_part p
LEFT JOIN tbl_supplier s ON p.supplier = s.id_supplier
WHERE p.status_assy = 0
ORDER BY p.part_code ASC
");
$stmt->execute();
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>Edit BOM</h3>
        </div>

        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-4">
                    <label>Model</label>
                    <input type="text" id="modelName" class="form-control" value="<?= $model['name'] ?>">
                </div>

                <div class="col-md-4">
                    <label>Part Code</label>
                    <input type="text" id="assyCode" class="form-control" value="<?= $model['part_code'] ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label>Part Name</label>
                    <input type="text" id="assyName" class="form-control" value="<?= $model['part_name'] ?>">
                </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <h4>Detail BOM</h4>
                <button class="btn btn-primary btn-sm" id="btnAddRow">+ Tambah</button>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Part Code</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Supplier</th>
                        <th>Remark</th>
                        <th>Subs</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>

            <div class="text-right">
                <button class="btn btn-success" id="btnSave">Update</button>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const parts = <?= json_encode($parts); ?>;
    const bom = <?= json_encode($bom); ?>;

    function formatPartName(name) {
        return (name || '').replace(/_/g, ' ');
    }

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
       REFRESH OPTION (❌ DUPLICATE)
    ====================== */
    function refreshPartOptions() {

        const used = getSelectedParts();

        document.querySelectorAll('.part-select').forEach(select => {

            [...select.options].forEach(opt => {
                if (!opt.value) return;

                let disabled = false;

                if (used.includes(opt.value) && opt.value !== select.value) {
                    disabled = true;
                }

                opt.disabled = disabled;

                let code = opt.dataset.code;
                let name = formatPartName(opt.dataset.name);
                let supplier = opt.dataset.supplier;

                if (disabled) {
                    opt.textContent = `❌ ${code} - ${name} - ${supplier}`;
                    opt.style.color = '#dc3545';
                } else {
                    opt.textContent = `${code} - ${name} - ${supplier}`;
                    opt.style.color = '';
                }
            });

        });
    }

    /* ====================== */
    function fillSubsOptions() {

        let options = '<option value="">-</option>';

        parts.forEach(p => {
            options += `<option 
            value="${p.part_code}__${p.name_supplier}">
            ${p.part_code} - ${formatPartName(p.part_name)} - ${p.name_supplier}
        </option>`;
        });

        document.querySelectorAll('.subs').forEach(s => {
            let val = s.value;
            s.innerHTML = options;

            if (val && s.querySelector(`option[value="${val}"]`)) {
                s.value = val;
            } else {
                s.value = '';
            }
        });
    }

    /* ====================== */
    function autoSetSubs() {
        let lastMain = null;

        $("#tableBody tr").each(function() {

            let remark = $(this).find(".remark").val();
            let partVal = $(this).find(".part-select").val();
            let subs = $(this).find(".subs");

            if (!partVal) return;

            if (remark == 0) {
                lastMain = partVal;
                subs.val('');
                subs.prop("disabled", true);
            } else {
                if (lastMain) {
                    subs.val(lastMain);
                    subs.prop("disabled", false);
                }
            }
        });
    }

    /* ====================== */
    function renderRow(row = null) {

        let options = '<option value="">Select</option>';

        parts.forEach(p => {
            let val = p.part_code + "__" + p.name_supplier;

            options += `<option 
        value="${val}"
        data-code="${p.part_code}"
        data-name="${p.part_name}"
        data-supplier="${p.name_supplier}"
        ${row && row.part_code==p.part_code && row.name_supplier==p.name_supplier?'selected':''}>
        ${p.part_code} - ${formatPartName(p.part_name)} - ${p.name_supplier}
    </option>`;
        });

        return `
<tr>
<td class="no"></td>

<td><select class="form-control part-select">${options}</select></td>
<td><input type="number" class="form-control qty" value="${row?.qty||''}"></td>
<td><input type="text" class="form-control unit" value="${row?.unit||'Pcs'}"></td>
<td class="supplier">${row?.name_supplier||'-'}</td>

<td>
<select class="form-control remark">
<option value="0" ${row?.remark==0?'selected':''}>MAIN</option>
<option value="1" ${row?.remark==1?'selected':''}>SUBSTITUTE</option>
</select>
</td>

<td>
<select class="form-control subs"></select>
</td>

<td><span style="color:green">OK</span></td>
<td><button class="btn btn-danger btn-sm del">X</button></td>
</tr>`;
    }



    function loadData() {
        $("#tableBody").html('');

        bom.forEach(r => {
            $("#tableBody").append(renderRow(r));
        });

        renumber();
        refreshPartOptions();
        fillSubsOptions();
        autoSetSubs();

        initSelect2(); // 🔥 WAJIB
    }
    /* ====================== */
    function renumber() {
        $("#tableBody tr").each((i, e) => {
            $(e).find(".no").text(i + 1);
        });
    }

    $("#btnAddRow").click(() => {
        $("#tableBody").append(renderRow());

        renumber();
        refreshPartOptions();
        fillSubsOptions();
        autoSetSubs();

        initSelect2(); // 🔥 WAJIB
    });

    $(document).on("change", ".remark", function() {
        autoSetSubs();
    });


    $(document).on("change", ".part-select", function() {

        let selected = $(this).find(":selected");

        let supplier = selected.data("supplier") || '-';

        let row = $(this).closest("tr");
        row.find(".supplier").text(supplier);

        refreshPartOptions();
        fillSubsOptions();
        autoSetSubs();

        // 🔥 refresh select2 display
        $('.part-select').trigger('change.select2');
    });

    $(document).on("click", ".del", function() {
        $(this).closest("tr").remove();
        renumber();
        refreshPartOptions(); // 🔥
        autoSetSubs();
    });

    /* ====================== SAVE */
    $("#btnSave").click(function() {

        let data = [];
        let used = new Set();

        $("#tableBody tr").each(function() {

            let val = $(this).find(".part-select").val();
            let [part_code, supplier] = val.split("__");

            let qty = $(this).find(".qty").val();
            let unit = $(this).find(".unit").val();
            let remark = $(this).find(".remark").val();
            let subs = $(this).find(".subs").val() || '';

            if (remark == 1 && !subs) {
                Swal.fire("Error", "SUBSTITUTE harus punya parent", "error");
                return false;
            }

            if (subs === val) {
                Swal.fire("Error", "Part tidak boleh jadi dirinya sendiri", "error");
                return false;
            }

            let key = part_code + "__" + supplier;
            if (used.has(key)) {
                Swal.fire("Error", "Duplicate part!", "error");
                return false;
            }

            used.add(key);

            data.push({
                part_code,
                qty,
                unit,
                remark,
                supplier,
                subs
            });
        });

        $.post("update_bom.php", {
            id: <?= $id ?>,
            model: $("#modelName").val(),
            assy_code: $("#assyCode").val(),
            assy_name: $("#assyName").val(),
            items: JSON.stringify(data)
        }, res => {
            if (res.status == 'success') {
                Swal.fire("Success", "Updated", "success").then(() => location.reload());
            } else {
                Swal.fire("Error", res.msg, "error");
            }
        }, 'json');

    });

    function initSelect2() {
        $('.part-select').select2({
            placeholder: "Cari part...",
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                let text = data.text.toLowerCase();
                let term = params.term.toLowerCase();

                if (text.includes(term)) {
                    return data;
                }

                return null;
            }
        });
    }
    /* INIT */
    loadData();
</script>