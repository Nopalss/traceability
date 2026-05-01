<?php
require_once __DIR__ . '/../../includes/config.php';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

$parts = $pdo->query("SELECT id_part, part_code, part_name FROM tbl_part ORDER BY part_code ASC")->fetchAll();
?>

<div class="content">
    <div class="container">

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <h4>NG Type</h4>
                <button class="btn btn-primary" id="addNgBtn">+ Add</button>
            </div>

            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NG</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $q = $pdo->query("SELECT * FROM tbl_ng_type");
                        foreach ($q as $r) {
                            echo "
<tr>
<td>{$r['id']}</td>
<td><b>{$r['ng_code']}</b><br><small>{$r['ng_name']}</small></td>
<td>{$r['status']}</td>
<td>
<button class='btn btn-warning btn-sm editNg'
data-id='{$r['id']}'
data-code='" . htmlspecialchars($r['ng_code'], ENT_QUOTES) . "'
data-status='{$r['status']}'>Edit</button>

<button class='btn btn-danger btn-sm deleteNg'
data-id='{$r['id']}'>Delete</button>
</td>
</tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="ngModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">NG Type</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <input id="ng_code" class="form-control mb-2" placeholder="NG Title">

                <select id="ng_status" class="form-control mb-3">
                    <option value="ACTIVE">ACTIVE</option>
                    <option value="INACTIVE">INACTIVE</option>
                </select>

                <input type="text" id="searchPart" class="form-control mb-2" placeholder="🔍 Search part...">

                <div class="selected-count mb-2">
                    Selected: <span id="countSelected">0</span>
                </div>

                <div class="part-box" id="partContainer"></div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="saveNg">Save</button>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
<style>
    .part-box {
        border: 1px solid #ddd;
        border-radius: 10px;
        max-height: 300px;
        overflow: auto;
        padding: 10px;
        background: #fafafa;
    }

    .part-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
    }

    .part-row:hover {
        background: #eef3ff;
    }

    .part-row.active {
        background: #dce8ff;
    }
</style>

<script>
    let PARTS = <?= json_encode($parts) ?>;
    let selectedParts = [];
    let editId = null;

    function renderParts() {

        let html = '';

        PARTS.forEach(p => {

            let checked = selectedParts.includes(p.id_part.toString()) ? 'checked' : '';
            let active = checked ? 'active' : '';

            html += `
        <div class="part-row ${active}">
            <input type="checkbox" class="part-check" value="${p.id_part}" ${checked}>
            <div>
                <b>${p.part_code}</b><br>
                <small>${p.part_name}</small>
            </div>
        </div>`;
        });

        $('#partContainer').html(html);
        updateUI();
    }

    function updateUI() {
        $('#countSelected').text($('.part-check:checked').length);

        $('.part-row').each(function() {
            let cb = $(this).find('.part-check');
            $(this).toggleClass('active', cb.is(':checked'));
        });
    }

    /* CLICK ROW */
    $(document).on('click', '.part-row', function(e) {

        if (e.target.tagName !== 'INPUT') {
            let cb = $(this).find('.part-check');
            cb.prop('checked', !cb.prop('checked'));
        }

        updateUI();
    });

    /* SEARCH (INI FIX 100%) */
    $('#searchPart').on('input', function() {

        let val = $(this).val().toLowerCase();

        $('#partContainer .part-row').each(function() {

            let text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(val));

        });

    });

    /* ADD */
    $('#addNgBtn').click(function() {

        editId = null;
        selectedParts = [];

        $('#ng_code').val('');
        $('#ng_status').val('ACTIVE');

        renderParts();
        $('#ngModal').modal('show');
    });

    /* EDIT */
    $('.editNg').click(function() {

        let btn = $(this);

        editId = btn.data('id');

        $('#ng_code').val(btn.data('code'));
        $('#ng_status').val(btn.data('status'));

        $.get('ajax_ng_type.php', {
            action: 'get_parts',
            id: editId
        }, function(res) {

            selectedParts = (res || []).map(x => x.toString());

            renderParts();
            $('#ngModal').modal('show');

        });
    });

    /* SAVE */
    $('#saveNg').click(function() {

        let data = {
            code: $('#ng_code').val(),
            status: $('#ng_status').val(),
            parts: []
        };

        $('.part-check:checked').each(function() {
            data.parts.push($(this).val());
        });

        let action = editId ? 'update' : 'insert';

        $.post('ajax_ng_type.php', {
            action: action,
            id: editId,
            ...data
        }, function() {
            location.reload();
        });

    });
</script>