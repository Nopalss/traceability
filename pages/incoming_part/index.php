<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$_SESSION['table'] = 'all-part-income';
$_SESSION['halaman'] = 'scan incoming part';
$_SESSION['menu'] = 'incoming_part';

$suppliers = $pdo->query("
    SELECT id_supplier, name_supplier 
    FROM tbl_supplier 
    WHERE status='supplier'
")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <div class="card card-custom">

                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title"></div>
                    <div class="card-toolbar">
                        <a href="<?= BASE_URL ?>pages/incoming_part/scan.php" class="btn btn-primary font-weight-bolder">
                            Scan Part
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <!-- FILTER -->
                    <div class="mb-7">
                        <div class="row">

                            <div class="col-md-4">
                                <label>Supplier</label>
                                <select id="filter_supplier" class="form-control">
                                    <option value="">-- All Supplier --</option>
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?= $s['id_supplier'] ?>">
                                            <?= $s['name_supplier'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Date From</label>
                                <input type="date" id="date_from" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label>Date To</label>
                                <input type="date" id="date_to" class="form-control">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button id="btnFilter" class="btn btn-primary w-100">Filter</button>
                            </div>

                        </div>
                    </div>

                    <!-- DATATABLE -->
                    <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- =========================
     MODAL EDIT
========================= -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Qty & Actual</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="formEdit">
                <div class="modal-body">

                    <input type="hidden" id="ref_number">

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="qty" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Actual</label>
                        <input type="number" id="remain" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    let datatable;

    /* ==============================
       LOAD TABLE
    ============================== */
    function loadTable(filters = {}) {

        if (datatable) {
            datatable.destroy();
            $("#kt_datatable").html("");
        }

        datatable = $('#kt_datatable').KTDatatable({

            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '<?= BASE_URL ?>api/part/incoming/all.php',
                        method: 'POST',
                        params: filters
                    }
                },
                pageSize: 10
            },

            sortable: true,
            pagination: true,

            columns: [{
                    field: 'part_code',
                    title: 'Part'
                },
                {
                    field: 'part_name',
                    title: 'Name'
                },
                {
                    field: 'lot_no',
                    title: 'Lot No'
                },
                {
                    field: 'ref_number',
                    title: 'Ref No',
                    autoHide: false
                },
                {
                    field: 'supplier_name',
                    title: 'Supplier',
                    autoHide: false,
                },
                {
                    field: 'qty',
                    title: 'Quantity',
                    autoHide: false,
                },
                {
                    field: 'remain',
                    title: 'Remain',
                    autoHide: false,
                },
                {
                    field: 'incoming_date',
                    title: 'Incoming',
                    autoHide: false,
                },

                {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    autoHide: false,
                    template: function(row) {
                        return `
                        <button class="btn btn-sm btn-warning btn-icon"
                            onclick="openEdit('${row.ref_number}', ${row.qty}, ${row.remain})">
                            <i class="flaticon-edit"></i>
                        </button>
                    `;
                    }
                }
            ],
        });
    }

    /* ==============================
       OPEN MODAL
    ============================== */
    function openEdit(ref, qty, remain) {
        $("#ref_number").val(ref);
        $("#qty").val(qty);
        $("#remain").val(remain);
        $("#editModal").modal('show');
    }

    /* ==============================
       SUBMIT EDIT
    ============================== */
    $("#formEdit").submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: 'update_qty.php',
            method: 'POST',
            data: {
                ref_number: $("#ref_number").val(),
                qty: $("#qty").val(),
                remain: $("#remain").val()
            },
            success: function(res) {
                alert("Berhasil update");
                $("#editModal").modal('hide');
                loadTable();
            }
        });
    });

    /* ==============================
       FILTER
    ============================== */
    $("#btnFilter").click(function() {
        loadTable({
            supplier: $("#filter_supplier").val(),
            date_from: $("#date_from").val(),
            date_to: $("#date_to").val()
        });
    });

    /* INIT */
    loadTable();
</script>