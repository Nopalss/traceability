<?php
require_once __DIR__ . '/../../includes/config.php';

$partAssy = $_GET['part_assy'] ?? '';
if ($partAssy === '') {
    redirect('pages/part_assy/');
}

// ambil master part
$parts = $pdo->query("
    SELECT part_code, part_name 
    FROM tbl_part 
    ORDER BY part_code
")->fetchAll(PDO::FETCH_ASSOC);

// ambil BOM existing
$stmt = $pdo->prepare("
    SELECT pa.part_code, pa.qty, p.part_name
    FROM tbl_part_assy pa
    JOIN tbl_part p ON pa.part_code = p.part_code
    WHERE pa.part_assy = :part_assy
");

$_SESSION['halaman'] = 'part assy';
$_SESSION['menu']    = 'part_assy';
$_SESSION['subHalaman'] = ' | Edit Part Assy | ' . $partAssy;
$stmt->execute([':part_assy' => $partAssy]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

                        <h3>Form Edit Part Assy</h3>

                        <form method="post" action="<?= BASE_URL ?>controllers/part_assy/edit.php">

                            <input type="hidden" name="part_assy" value="<?= htmlspecialchars($partAssy) ?>">

                            <div class="col input-group-sm mt-7">
                                <label class="form-label small font-weight-bolder">Part Assy</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($partAssy) ?>" disabled>
                            </div>

                            <div class="d-flex mt-10 mb-3 justify-content-between align-items-center">
                                <h3>Assembly Components</h3>
                                <button type="button" id="btnAddRow" class="btn btn-primary">+ Tambah</button>
                            </div>

                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Part Code</th>
                                        <th>Part Name</th>
                                        <th>Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assyTableBody">
                                    <?php foreach ($items as $i => $row): ?>
                                        <tr>
                                            <td class="row-no"><?= $i + 1 ?></td>
                                            <td>
                                                <select name="items[<?= $i ?>][part_code]" class="form-control form-control-sm part-select" required>
                                                    <option value="">select</option>
                                                    <?php foreach ($parts as $p): ?>
                                                        <option value="<?= $p['part_code'] ?>"
                                                            <?= $p['part_code'] === $row['part_code'] ? 'selected' : '' ?>
                                                            data-name="<?= htmlspecialchars($p['part_name']) ?>">
                                                            <?= $p['part_code'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="part-name"><?= htmlspecialchars($row['part_name']) ?></td>
                                            <td style="font-size: 0.85rem; width: 3rem;">
                                                <input type="number" name="items[<?= $i ?>][qty]" class="form-control form-control-sm" oninput="this.value = this.value < 1 ? '' : this.value"
                                                    min="1" value="<?= $row['qty'] ?>" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger btn-remove">
                                                    <i class="flaticon-delete"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="text-right mt-10">
                                <a href="<?= BASE_URL ?>pages/part_assy/" class="btn btn-outline-danger">Batal</a>
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
    const parts = <?= json_encode($parts) ?>;

    function renumber() {
        document.querySelectorAll('#assyTableBody .row-no')
            .forEach((td, i) => td.innerText = i + 1);
    }

    document.getElementById('btnAddRow').addEventListener('click', function() {
        const tbody = document.getElementById('assyTableBody');
        const index = tbody.children.length;

        let options = '<option value="">select</option>';
        parts.forEach(p => {
            options += `<option value="${p.part_code}" data-name="${p.part_name}">${p.part_code}</option>`;
        });

        tbody.insertAdjacentHTML('beforeend', `
        <tr>
            <td class="row-no"></td>
            <td>
                <select name="items[${index}][part_code]"
                        class="form-control form-control-sm part-select"
                        required>
                    ${options}
                </select>
            </td>
            <td class="part-name">-</td>
            <td style="font-size:0.85rem;width:3rem;">
                <input type="number"
                       name="items[${index}][qty]"
                       class="form-control form-control-sm"
                       min="1"
                       oninput="this.value = this.value < 1 ? '' : this.value"
                       required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-remove">
                    <i class="flaticon-delete"></i>
                </button>
            </td>
        </tr>
    `);

        renumber();
    });

    // REMOVE ROW
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();
            renumber();
        }
    });

    // UPDATE PART NAME
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('part-select')) {
            const name = e.target.selectedOptions[0]?.dataset.name || '-';
            e.target.closest('tr').querySelector('.part-name').innerText = name;
        }
    });
</script>


<?php require __DIR__ . '/../../includes/footer.php'; ?>