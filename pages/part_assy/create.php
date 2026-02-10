<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

$_SESSION['halaman'] = 'part assy';
$_SESSION['menu']    = 'part_assy';
$_SESSION['subHalaman'] = ' | Add Part Assy';

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

                            <div class="col input-group-sm mt-7">
                                <label class="form-label font-weight-bolder">Part Assy</label>
                                <select name="part_assy" class="form-control" required id="partAssySelect">
                                    <option value="">select</option>
                                    <?php foreach ($parts as $p): ?>
                                        <option value="<?= $p['part_code']; ?>">
                                            <?= htmlspecialchars($p['part_code']) ?> - <?= htmlspecialchars($p['part_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-flex mt-10 mb-3 justify-content-between align-items-center">
                                <h3>Assembly Components</h3>
                                <button type="button" id="btnAddRow" class="btn btn-primary">+ Tambah</button>
                            </div>

                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Part Code</th>
                                        <th class="text-center">Part Name</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="assyTableBody"></tbody>
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

    <script>
        const parts = <?= json_encode($parts); ?>;
        let selectedAssy = '';

        function renderRow(index) {

            let options = '<option value="">select</option>';
            parts.forEach(p => {
                options += `<option value="${p.part_code}" data-name="${p.part_name}">${p.part_code} - ${p.part_name}</option>`;
            });

            return `
<tr class="align-middle">
<td class="row-no align-middle text-center font-weight-bolder" style="font-size:0.9rem"></td>

<td style="font-size:0.85rem">
<select name="items[${index}][part_code]" class="form-control form-control-sm part-select" required>${options}</select>
</td>

<td class="part-name align-middle text-center" style="font-size:0.90rem">-</td>

<td style="font-size:0.85rem;width:3rem">
<input type="number"
name="items[${index}][qty]"
class="form-control form-control-sm qty"
min="1"
onwheel="this.blur()"
oninput="this.value=this.value<1?'':this.value"
required>
</td>

<td class="text-center">
<button type="button" class="btn btn-sm btn-danger btn-remove"><i class="flaticon-delete"></i></button>
</td>
</tr>`;
        }

        function renumberRows() {
            document.querySelectorAll('#assyTableBody tr').forEach((tr, i) => {
                tr.querySelector('.row-no').innerText = i + 1;
            });
        }

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

                    let disabled = false;

                    if (opt.value === selectedAssy) disabled = true;
                    if (used.includes(opt.value) && opt.value !== select.value) disabled = true;

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

        document.getElementById('partAssySelect').addEventListener('change', function() {
            selectedAssy = this.value;
            refreshPartOptions();
        });

        document.getElementById('btnAddRow').addEventListener('click', () => {
            const tbody = document.getElementById('assyTableBody');
            tbody.insertAdjacentHTML('beforeend', renderRow(tbody.children.length));
            renumberRows();
            refreshPartOptions();
        });

        document.addEventListener('change', e => {
            if (e.target.classList.contains('part-select')) {
                const name = e.target.selectedOptions[0]?.dataset.name || '-';
                e.target.closest('tr').querySelector('.part-name').innerText = name;
                refreshPartOptions();
            }
        });

        document.addEventListener('click', e => {
            if (e.target.closest('.btn-remove')) {
                e.target.closest('tr').remove();
                renumberRows();
                refreshPartOptions();
            }
        });

        /* ===============================
           DISABLE NUMBER SCROLL + ARROW
        ================================ */

        document.addEventListener('wheel', function(e) {
            if (e.target.type === 'number') e.target.blur();
        }, {
            passive: false
        });

        document.addEventListener('keydown', function(e) {
            if (e.target.type === 'number' && (e.which === 38 || e.which === 40)) {
                e.preventDefault();
            }
        });
    </script>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>