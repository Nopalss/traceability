<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

$_SESSION['halaman'] = 'production planning';
$_SESSION['menu'] = 'production_planning';
$_SESSION['subHalaman'] = '| Add Production Plan';
// ambil semua part
$sql = "SELECT id_part, part_code, part_name FROM tbl_part ORDER BY part_code ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT line_id, line_name FROM tbl_line ORDER BY line_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

$jam = [
    "00:00",
    "01:00",
    "02:00",
    "03:00",
    "04:00",
    "05:00",
    "06:00",
    "07:00",
    "08:00",
    "09:00",
    "10:00",
    "11:00",
    "12:00",
    "13:00",
    "14:00",
    "15:00",
    "16:00",
    "17:00",
    "18:00",
    "19:00",
    "20:00",
    "21:00",
    "22:00",
    "23:00"
];
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <h3>Form Add Production Plan</h3>

                        <form method="post" action="<?= BASE_URL ?>controllers/production_planning/create.php" id="">

                            <!-- PART ASSY -->
                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Production Date</label>
                                <input
                                    type="date"
                                    name="production_date"
                                    id="production_date"
                                    class="form-control"
                                    required
                                    min="<?= date('Y-m-d') ?>"
                                    onkeydown="return false">
                            </div>
                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Line</label>
                                <select name="line_id" class="form-control" required>
                                    <option value="">select</option>
                                    <?php foreach ($lines as $l): ?>
                                        <option value="<?= $l['line_id']; ?>">
                                            <?= htmlspecialchars($l['line_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- PART ASSY -->
                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Part Code</label>
                                <select name="part_assy" class="form-control" required>
                                    <option value="">select</option>
                                    <?php foreach ($parts as $p): ?>
                                        <option value="<?= $p['part_code']; ?>">
                                            <?= htmlspecialchars($p['part_code']) . " - " .  htmlspecialchars($p['part_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>



                            <!-- HEADER TABLE -->
                            <div class="d-flex mt-10 mb-3 justify-content-between align-items-center">
                                <h3>Shift</h3>
                                <button type="button" id="btnAddRow" class="btn btn-primary">+ Tambah</button>
                            </div>

                            <!-- TABLE -->
                            <table class="table table-sm table-striped ">
                                <thead>
                                    <tr>
                                        <th class="text-center">Shift</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Start Production</th>
                                        <th class="text-center">End Production</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assyTableBody">
                                    <!-- SHIFT 1 -->
                                    <tr>
                                        <td class="text-center align-middle shift-no font-weight-bolder">1</td>

                                        <td>
                                            <input type="hidden" name="shift[]" value="1">
                                            <input type="number" name="qty[]" class="form-control form-control-sm" oninput="this.value = this.value < 1 ? '' : this.value" min="1" required>
                                        </td>

                                        <td>
                                            <select name="start[]" class="form-control form-control-sm" required>
                                                <option value="">select</option>
                                                <?php foreach ($jam as $j): ?>
                                                    <?php $selected = $j === "07:00" ? "selected" : "" ?>
                                                    <option value="<?= $j; ?>" <?= $selected ?>>
                                                        <?= $j; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>

                                        <td>
                                            <select name="end[]" class="form-control form-control-sm" required>
                                                <option value="">select</option>
                                                <?php foreach ($jam as $j): ?>
                                                    <?php $selected = $j === "16:00" ? "selected" : "" ?>
                                                    <option value="<?= $j; ?>" <?= $selected ?>>
                                                        <?= $j; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove">
                                                <i class="flaticon-delete"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- SHIFT 2 (DEFAULT BARU) -->
                                    <tr>
                                        <td class="text-center align-middle shift-no font-weight-bolder">2</td>

                                        <td>
                                            <input type="hidden" name="shift[]" value="2">
                                            <input type="number" name="qty[]" class="form-control form-control-sm" oninput="this.value = this.value < 1 ? '' : this.value" min="1" required>
                                        </td>

                                        <td>
                                            <select name="start[]" class="form-control form-control-sm" required>
                                                <option value="">select</option>
                                                <?php foreach ($jam as $j): ?>
                                                    <?php $selected = $j === "16:00" ? "selected" : "" ?>
                                                    <option value="<?= $j; ?>" <?= $selected ?>>
                                                        <?= $j; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="end[]" class="form-control form-control-sm" required>
                                                <option value="">select</option>
                                                <?php foreach ($jam as $j): ?>
                                                    <?php $selected = $j === "06:00" ? "selected" : "" ?>
                                                    <option value="<?= $j; ?>" <?= $selected ?>>
                                                        <?= $j; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove">
                                                <i class="flaticon-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="col d-flex justify-content-around align-items-center  mt-7">
                                <h5 class="form-label ">Total Quantity</h5>
                                <h2 class="font-weight-bolder total-qty">100</h2>
                                <input
                                    type="hidden"
                                    name="total_qty"
                                    class="form-control "
                                    required
                                    readonly>
                            </div>
                            <div class="text-right mt-23">
                                <a href="<?= BASE_URL ?>pages/production_planning/" class="btn btn-outline-danger">Batal</a>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('production_date').addEventListener('click', function() {
        if (this.showPicker) {
            this.showPicker();
        }
    });
    let shiftCounter = document.querySelectorAll('#assyTableBody tr').length;

    document.getElementById('btnAddRow').addEventListener('click', function() {
        shiftCounter++;

        const row = document.createElement('tr');
        row.innerHTML = `
        <td class="text-center align-middle shift-no font-weight-bolder">${shiftCounter}</td>

        <td>
            <input type="hidden" name="shift[]" value="${shiftCounter}">
            <input type="number" name="qty[]" class="form-control form-control-sm" min="1"  oninput="this.value = this.value < 1 ? '' : this.value" required>
        </td>

        <td>
            <select name="start[]" class="form-control form-control-sm" required>
                <option value="">select</option>
                <?php foreach ($jam as $j): ?>
                    <option value="<?= $j ?>"><?= $j ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <select name="end[]" class="form-control form-control-sm" required>
                <option value="">select</option>
                <?php foreach ($jam as $j): ?>
                    <option value="<?= $j ?>"><?= $j ?></option>
                <?php endforeach; ?>
            </select>
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove">
                <i class="flaticon-delete"></i>
            </button>
        </td>
    `;

        document.getElementById('assyTableBody').appendChild(row);
    });

    // HAPUS ROW
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();
            renumberShift();
        }
    });

    function renumberShift() {
        shiftCounter = 0;
        document.querySelectorAll('#assyTableBody tr').forEach((row) => {
            shiftCounter++;
            row.querySelector('.shift-no').innerText = shiftCounter;
            row.querySelector('input[name="shift[]"]').value = shiftCounter;
        });
    }

    function updateTotalQty() {
        let total = 0;

        document.querySelectorAll('input[name="qty[]"]').forEach(input => {
            const val = parseInt(input.value, 10);
            if (!isNaN(val) && val > 0) {
                total += val;
            }
        });

        // update angka tampilan
        document.querySelector('.total-qty').innerText = total;

        // update hidden input
        const hiddenTotal = document.querySelector('input[name="total_qty"]');
        if (hiddenTotal) {
            hiddenTotal.value = total;
        }
    }

    // realtime saat input qty berubah
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name="qty[]"]')) {
            updateTotalQty();
        }
    });

    // update saat row ditambah / dihapus
    document.addEventListener('click', function(e) {
        if (
            e.target.closest('#btnAddRow') ||
            e.target.closest('.btn-remove')
        ) {
            setTimeout(updateTotalQty, 0);
        }
    });

    // init pertama kali
    updateTotalQty();
</script>



<?php require __DIR__ . '/../../includes/footer.php'; ?>