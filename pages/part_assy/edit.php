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
   GET BOM DETAIL (UPDATED)
========================= */
$stmt = $pdo->prepare("
SELECT 
    pa.part_code,
    pa.qty,
    pa.unit,
    pa.remark,
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

<script>
    const parts = <?= json_encode($parts); ?>;
    const bom = <?= json_encode($bom); ?>;

    /* ======================
       DUPLICATE CONTROL
    ====================== */
    function getSelectedParts() {
        let arr = [];
        document.querySelectorAll('.part-select').forEach(s => {
            if (s.value) arr.push(s.value);
        });
        return arr;
    }

    function refreshPartOptions() {
        const used = getSelectedParts();

        document.querySelectorAll('.part-select').forEach(select => {
            [...select.options].forEach(opt => {
                if (!opt.value) return;

                let disabled = used.includes(opt.value) && opt.value !== select.value;
                opt.disabled = disabled;

                opt.textContent = (disabled ? '❌ ' : '') + opt.dataset.label;
                opt.style.color = disabled ? '#dc3545' : '';
            });
        });
    }

    /* ======================
       RENDER ROW
    ====================== */
    function renderRow(i, row = null) {

        let options = '<option value="">Select</option>';

        parts.forEach(p => {
            let val = p.part_code + '__' + p.name_supplier;

            options += `<option 
            value="${val}"
            ${row && row.part_code === p.part_code && row.name_supplier === p.name_supplier ? 'selected' : ''}
            data-label="${p.part_code} - ${p.part_name} - ${p.name_supplier}"
            data-supplier="${p.name_supplier}">
            ${p.part_code} - ${p.part_name} - ${p.name_supplier}
        </option>`;
        });

        return `
<tr>
<td class="no"></td>

<td><select class="form-control part-select">${options}</select></td>

<td><input type="number" class="form-control qty" value="${row?.qty || ''}"></td>

<td><input type="text" class="form-control unit" value="${row?.unit || 'Pcs'}"></td>

<td class="supplier">${row?.name_supplier || '-'}</td>

<td>
<select class="form-control remark">
    <option value="0" ${row?.remark == 0 ? 'selected' : ''}>MAIN</option>
    <option value="1" ${row?.remark == 1 ? 'selected' : ''}>SUBSTITUTE</option>
</select>
</td>

<td><span style="color:green;font-weight:bold">OK</span></td>

<td><button class="btn btn-danger btn-sm del">X</button></td>
</tr>`;
    }

    /* ======================
       INIT LOAD DATA
    ====================== */
    function loadData() {
        $("#tableBody").html('');
        bom.forEach((row, i) => {
            $("#tableBody").append(renderRow(i, row));
        });
        renumber();
        refreshPartOptions();
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
       EVENTS
    ====================== */
    $("#btnAddRow").click(function() {
        $("#tableBody").append(renderRow());
        renumber();
        refreshPartOptions();
    });

    $(document).on("change", ".part-select", function() {

        let selected = $(this).find(":selected");
        let supplier = selected.data("supplier") || '-';

        let row = $(this).closest("tr");

        row.find(".supplier").text(supplier);

        refreshPartOptions();
    });

    $(document).on("click", ".del", function() {
        $(this).closest("tr").remove();
        renumber();
        refreshPartOptions();
    });

    /* ======================
       UPDATE SAVE
    ====================== */
    $("#btnSave").click(function() {

        let data = [];
        let used = new Set();

        $("#tableBody tr").each(function() {

            let val = $(this).find(".part-select").val();
            let [part_code, supplier] = val.split("__");

            let qty = $(this).find(".qty").val();
            let unit = $(this).find(".unit").val();
            let remark = $(this).find(".remark").val();

            if (!part_code || !qty) return;

            let key = part_code + '__' + supplier;

            if (used.has(key)) {
                Swal.fire("Error", "Duplicate part tidak boleh!", "error");
                return false;
            }

            used.add(key);

            data.push({
                part_code,
                qty,
                unit,
                remark,
                supplier
            });
        });

        $.post("update_bom.php", {
            id: <?= $id ?>,
            model: $("#modelName").val(),
            assy_code: $("#assyCode").val(),
            assy_name: $("#assyName").val(),
            items: JSON.stringify(data)
        }, function(res) {

            if (res.status === 'success') {
                Swal.fire("Success", "Berhasil update!", "success")
                    .then(() => location.reload());
            } else {
                Swal.fire("Error", res.msg, "error");
            }

        }, 'json');

    });

    /* INIT */
    loadData();
</script>