<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

$_SESSION['halaman'] = 'production planning';
$_SESSION['menu'] = 'production_planning';
$_SESSION['subHalaman'] = '| Edit Production Plan';

// ================================
// Ambil pp_code
// ================================
$ppCode = $_GET['pp_code'] ?? '';

if ($ppCode === '') {
    header('Location: ' . BASE_URL . 'pages/production_planning/');
    exit;
}

// ================================
// Ambil data planning by pp_code
// ================================
$stmt = $pdo->prepare(
    "SELECT *
     FROM tbl_production_planning
     WHERE pp_code = :pp_code
     ORDER BY shift ASC"
);
$stmt->execute([':pp_code' => $ppCode]);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$plans) {
    header('Location: ' . BASE_URL . 'pages/production_planning/');
    exit;
}

$header = $plans[0];

// ================================
// Master data
// ================================
$parts = $pdo->query("SELECT id_part, part_code, part_name FROM tbl_part ORDER BY part_code ASC")->fetchAll(PDO::FETCH_ASSOC);
$lines = $pdo->query("SELECT line_id, line_name FROM tbl_line ORDER BY line_name ASC")->fetchAll(PDO::FETCH_ASSOC);

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

                        <h3>Form Edit Production Plan</h3>

                        <form method="post" action="<?= BASE_URL ?>controllers/production_planning/edit.php">

                            <input type="hidden" name="pp_code" value="<?= htmlspecialchars($ppCode) ?>">

                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Production Date</label>
                                <input type="date" name="production_date" class="form-control"
                                    value="<?= $header['production_date'] ?>" required>
                            </div>

                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Line</label>
                                <select name="line_id" class="form-control" required>
                                    <option value="">select</option>
                                    <?php foreach ($lines as $l): ?>
                                        <option value="<?= $l['line_id'] ?>" <?= $l['line_id'] == $header['line_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($l['line_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Part Code</label>
                                <select name="part_assy" class="form-control" required>
                                    <option value="">select</option>
                                    <?php foreach ($parts as $p): ?>
                                        <option value="<?= $p['part_code'] ?>" <?= $p['part_code'] == $header['product_code'] ? 'selected' : '' ?>>
                                            <?= $p['part_code'] ?> - <?= $p['part_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-flex mt-10 mb-3 justify-content-between align-items-center">
                                <h3>Shift</h3>
                                <button type="button" id="btnAddRow" class="btn btn-primary">+ Tambah</button>
                            </div>

                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Shift</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Start</th>
                                        <th class="text-center">End</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="assyTableBody">
                                    <?php foreach ($plans as $row): ?>
                                        <tr>
                                            <td class="text-center align-middle shift-no font-weight-bolder"><?= $row['shift'] ?></td>
                                            <td>
                                                <input type="hidden" name="shift[]" value="<?= $row['shift'] ?>">
                                                <input type="number" name="qty[]" class="form-control form-control-sm" value="<?= $row['qty'] ?>" min="1" required>
                                            </td>
                                            <td>
                                                <select name="start[]" class="form-control form-control-sm" required>
                                                    <option value="">select</option>
                                                    <?php foreach ($jam as $j): ?>
                                                        <option value="<?= $j ?>" <?= substr($row['start'], 0, 5) === $j ? 'selected' : '' ?>><?= $j ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="end[]" class="form-control form-control-sm" required>
                                                    <option value="">select</option>
                                                    <?php foreach ($jam as $j): ?>
                                                        <option value="<?= $j ?>" <?= substr($row['end'], 0, 5) === $j ? 'selected' : '' ?>><?= $j ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger btn-remove">
                                                    <i class="flaticon-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <hr>

                            <div class="col d-flex justify-content-around align-items-center mt-7">
                                <h5>Total Quantity</h5>
                                <h2 class="font-weight-bolder total-qty"><?= $header['total_qty'] ?></h2>
                                <input type="hidden" name="total_qty" value="<?= $header['total_qty'] ?>">
                            </div>

                            <div class="text-right mt-23">
                                <a href="<?= BASE_URL ?>pages/production_planning/" class="btn btn-outline-danger">Batal</a>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let shiftCounter = document.querySelectorAll('#assyTableBody tr').length;

    document.getElementById('btnAddRow').addEventListener('click', function() {
        shiftCounter++;

        const row = document.createElement('tr');
        row.innerHTML = `
        <td class="text-center align-middle shift-no font-weight-bolder">${shiftCounter}</td>
        <td>
            <input type="hidden" name="shift[]" value="${shiftCounter}">
            <input type="number" name="qty[]" class="form-control form-control-sm" min="1" required>
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

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();
            renumberShift();
            updateTotalQty();
        }
    });

    function renumberShift() {
        let i = 0;
        document.querySelectorAll('#assyTableBody tr').forEach(row => {
            i++;
            row.querySelector('.shift-no').innerText = i;
            row.querySelector('input[name="shift[]"]').value = i;
        });
    }

    function updateTotalQty() {
        let total = 0;
        document.querySelectorAll('input[name="qty[]"]').forEach(i => {
            total += parseInt(i.value || 0);
        });
        document.querySelector('.total-qty').innerText = total;
        document.querySelector('input[name="total_qty"]').value = total;
    }

    document.addEventListener('input', e => {
        if (e.target.matches('input[name="qty[]"]')) updateTotalQty();
    });

    updateTotalQty();
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>