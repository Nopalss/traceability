<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

$_SESSION['halaman'] = 'part assy';
$_SESSION['menu']    = 'part_assy';
$_SESSION['subHalaman'] = ' | Add Part Assy';

// ambil semua part
$sql = "SELECT id_part, part_code, part_name FROM tbl_part ORDER BY part_code ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$parts = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                        <h3>Form Add Part Assy</h3>

                        <form method="post" action="<?= BASE_URL ?>controllers/part_assy/create.php" id="formPartAssy">

                            <!-- PART ASSY -->
                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Part Assy</label>
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
                                <h3>Assembly Components</h3>
                                <button type="button" id="btnAddRow" class="btn btn-primary">+ Tambah</button>
                            </div>

                            <!-- TABLE -->
                            <table class="table table-sm table-striped ">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Part Code</th>
                                        <th class="text-center">Part Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assyTableBody">
                                    <!-- BARIS DINAMIS -->
                                </tbody>
                            </table>

                            <div class="text-right mt-23">
                                <a href="<?= BASE_URL ?>pages/part_assy/" class="btn btn-outline-danger">Batal</a>
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
    const parts = <?= json_encode($parts); ?>;

    function renderRow(index) {
        let options = '<option value="">select</option>';
        parts.forEach(p => {
            options += `<option value="${p.part_code}" data-name="${p.part_name}">${p.part_code}</option>`;
        });

        return `
        <tr class="align-middle">
            <td class="row-no align-middle  text-center font-weight-bolder "style="font-size: 0.9rem"></td>
            <td style="font-size: 0.85rem">
                <select name="items[${index}][part_code] style="font-size: 0.85rem"
                        class="form-control form-control-sm part-select" required>
                    ${options}
                </select>
            </td>
            <td class="part-name align-middle text-center" style="font-size: 0.90rem" >-</td>
            <td  style="font-size: 0.85rem; width: 3rem; ">
                <input type="number"
                       name="items[${index}][qty]"
                       class="form-control form-control-sm"
                      oninput="this.value = this.value < 1 ? '' : this.value"
                       min="1" required>
            </td>
            <td style="font-size: 0.85rem"  class="text-center"> 
                <button type="button" class="btn btn-sm btn-danger btn-remove">
                    <i class="flaticon-delete"></i>
                </button>
            </td>
        </tr>
    `;
    }

    function renumberRows() {
        document.querySelectorAll('#assyTableBody tr').forEach((tr, i) => {
            tr.querySelector('.row-no').innerText = i + 1;
        });
    }

    // ADD ROW
    document.getElementById('btnAddRow').addEventListener('click', function() {
        const tbody = document.getElementById('assyTableBody');
        const index = tbody.children.length;
        tbody.insertAdjacentHTML('beforeend', renderRow(index));
        renumberRows();
    });

    // UPDATE PART NAME
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('part-select')) {
            const name = e.target.selectedOptions[0].dataset.name || '-';
            e.target.closest('tr').querySelector('.part-name').innerText = name;
        }
    });

    // REMOVE ROW
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            e.target.closest('tr').remove();
            renumberRows();
        }
    });
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>