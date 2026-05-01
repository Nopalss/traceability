<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$_SESSION['table'] = 'all-part-income';
$_SESSION['halaman'] = 'scan incoming part';
$_SESSION['menu'] = 'incoming_part';

// ambil supplier untuk filter
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
                            <span class="svg-icon svg-icon-md"></span>
                            Scan Part
                        </a>
                    </div>

                </div>

                <div class="card-body">

                    <!-- 🔥 FILTER AREA -->
                    <div class="mb-7">

                        <div class="row">

                            <!-- Supplier -->
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

                            <!-- Date From -->
                            <div class="col-md-3">
                                <label>Date From</label>
                                <input type="date" id="date_from" class="form-control">
                            </div>

                            <!-- Date To -->
                            <div class="col-md-3">
                                <label>Date To</label>
                                <input type="date" id="date_to" class="form-control">
                            </div>

                            <!-- Button -->
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="btnFilter" class="btn btn-primary w-100">
                                    Filter
                                </button>
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

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    let datatable;

    /* ==============================
       INIT DATATABLE
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

            layout: {
                scroll: false,
                footer: false
            },

            sortable: true,
            pagination: true,

            columns: [{
                    field: 'part_code',
                    title: 'Part',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.part_code}</span>`;
                    }
                },
                {
                    field: 'part_name',
                    title: 'Name',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.part_name}</span>`;
                    }

                }, {
                    field: 'lot_no',
                    title: 'Lot No',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.lot_no}</span>`;
                    }

                }, {
                    field: 'ref_number',
                    title: 'Ref No',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.ref_number}</span>`;
                    }
                }, {

                    field: 'supplier_name',
                    title: 'Supplier',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.supplier_name}</span>`;
                    }
                }, {
                    field: 'qty',
                    title: 'Quantity',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.qty}</span>`;
                    }
                }, {
                    field: 'incoming_date',
                    title: 'Incoming',
                    template: function(row) {
                        return `<span style="font-size:0.75rem">${row.incoming_date}</span>`;
                    }
                }
            ],

        });
    }

    /* ==============================
       FIRST LOAD
    ============================== */
    loadTable();

    /* ==============================
       FILTER ACTION
    ============================== */
    $("#btnFilter").click(function() {

        let filters = {
            supplier: $("#filter_supplier").val(),
            date_from: $("#date_from").val(),
            date_to: $("#date_to").val()
        };

        loadTable(filters);

    });
</script>