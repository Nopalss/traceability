<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['menu'] = 'outbound_product';
$_SESSION['halaman'] = 'scan product out';
$_SESSION['subHalaman'] = '';

/* ======================
   GET SUPPLIER LIST
====================== */
$suppliers = $pdo->query("
    SELECT id_supplier, name_supplier
    FROM tbl_supplier WHERE  status = 'customer'
    ORDER BY name_supplier ASC
")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .scan-card {
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .gradient-header {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        border-radius: 18px 18px 0 0;
        padding: 20px;
    }

    #qr_raw {
        font-size: 18px;
        height: 100px;
    }

    .detail-box {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 15px;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
    }

    .status-in {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-out {
        background: #ffebee;
        color: #c62828;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid pt-0">
    <div class="container mt-6">

        <div class="card scan-card">

            <div class="gradient-header">
                <h4 class="mb-1">Scan Product Out</h4>
                <small>Scan QR untuk mengeluarkan produk ke Customer</small>
            </div>

            <div class="card-body">
                <div class="row">

                    <!-- LEFT -->
                    <div class="col-lg-5">

                        <label class="font-weight-bold">Kirim Ke (Customer)</label>

                        <div class="d-flex mb-3">
                            <select id="supplier_id" class="form-control">
                                <option value="">-- Pilih Customer Tujuan --</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['id_supplier'] ?>">
                                        <?= $s['name_supplier'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>

                            <button id="btn-default"
                                type="button"
                                class="btn btn-primary ml-2">
                                Default
                            </button>
                        </div>

                        <label class="font-weight-bold">Scan / Paste QR</label>
                        <textarea id="qr_raw" class="form-control mb-3" autofocus></textarea>

                        <button id="btn-scan" class="btn btn-success btn-block">
                            Proses Scan Out
                        </button>

                        <div id="scan-alert" class="alert mt-3 d-none"></div>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-lg-7">

                        <div class="detail-box">

                            <h5 class="mb-4">Detail Product</h5>

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Serial No</label>
                                    <input id="serial_no" class="form-control" disabled>
                                </div>
                                <div class="col">
                                    <label>Product Code</label>
                                    <input id="product_code" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Ref number</label>
                                    <input id="ref_number" class="form-control" disabled>
                                </div>
                                <div class="col">
                                    <label>Production Date</label>
                                    <input id="product_name" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Location (Customer)</label>
                                    <input id="location_name" class="form-control" disabled>
                                </div>
                                <div class="col">
                                    <label>Production Date</label>
                                    <input id="created_at" class="form-control" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label>Status</label>
                                    <div id="status_badge"></div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        const qrInput = document.getElementById('qr_raw');
        const supplierSelect = document.getElementById('supplier_id');
        const btnScan = document.getElementById('btn-scan');
        const btnDefault = document.getElementById('btn-default');
        const alertBox = document.getElementById('scan-alert');

        const serialField = document.getElementById('serial_no');
        const refField = document.getElementById('ref_number');
        const productField = document.getElementById('product_code');
        const nameField = document.getElementById('product_name');
        const dateField = document.getElementById('created_at');
        const locationField = document.getElementById('location_name');
        const statusBadge = document.getElementById('status_badge');


        /* =====================================
           LOAD DEFAULT CUSTOMER
        ===================================== */

        const defaultSupplier = localStorage.getItem('default_supplier');

        if (defaultSupplier) {
            supplierSelect.value = defaultSupplier;
        }


        /* =====================================
           SET DEFAULT CUSTOMER
        ===================================== */

        btnDefault.addEventListener('click', function() {

            let val = supplierSelect.value;

            if (!val) {
                showAlert('Pilih customer dulu sebelum set default', 'warning');
                supplierSelect.focus();
                return;
            }

            localStorage.setItem('default_supplier', val);

            showAlert('Customer berhasil dijadikan default', 'success');

            qrInput.focus();

        });


        /* =====================================
           AUTO FOCUS SCANNER
        ===================================== */

        // fokus saat halaman load
        window.addEventListener('load', () => {
            qrInput.focus();
        });

        // jika user mengetik apapun di halaman dan textarea kosong
        document.addEventListener('keydown', function(e) {

            if (document.activeElement !== qrInput) {

                // kalau scanner mulai mengetik
                if (e.key.length === 1) {
                    qrInput.focus();
                }

            }

        });


        /* =====================================
           SCANNER AUTO ENTER
        ===================================== */

        qrInput.addEventListener('keydown', function(e) {

            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                processScan();
            }

        });


        /* =====================================
           BUTTON SCAN
        ===================================== */

        btnScan.addEventListener('click', processScan);


        /* =====================================
           MAIN SCAN FUNCTION
        ===================================== */

        function processScan() {

            let qr = qrInput.value.trim();
            let supplierId = supplierSelect.value;

            clearAlert();

            if (!qr) {
                showAlert('QR tidak boleh kosong', 'danger');
                qrInput.focus();
                return;
            }

            if (!supplierId) {
                showAlert('Pilih Customer tujuan terlebih dahulu', 'warning');
                supplierSelect.focus();
                return;
            }

            btnScan.disabled = true;
            btnScan.innerText = 'Processing...';

            fetch("<?= BASE_URL ?>controllers/outbound_product/scan.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        qr: qr,
                        supplier_id: supplierId
                    })
                })
                .then(res => res.json())
                .then(res => {

                    if (res.success) {

                        fillDetail(res.data);
                        showAlert(res.message, 'success');

                        qrInput.value = '';
                        qrInput.focus();

                    } else {

                        showAlert(res.message, 'danger');

                    }

                })
                .catch(err => {
                    showAlert('Terjadi kesalahan server', 'danger');
                    console.error(err);
                })
                .finally(() => {
                    btnScan.disabled = false;
                    btnScan.innerText = 'Proses Scan Out';
                });

        }


        /* =====================================
           FILL DETAIL BOX
        ===================================== */

        function fillDetail(data) {

            serialField.value = data.serial_no ?? '';
            productField.value = data.product_code ?? '';
            nameField.value = data.product_name ?? '';
            dateField.value = data.created_at ?? '';
            locationField.value = data.location ?? '';
            locationField.value = data.location ?? '';

            statusBadge.innerHTML =
                `<span class="badge-status status-out">OUT</span>`;

        }


        /* =====================================
           ALERT
        ===================================== */

        function showAlert(message, type) {

            alertBox.className = 'alert alert-' + type;
            alertBox.innerText = message;
            alertBox.classList.remove('d-none');

        }

        function clearAlert() {
            alertBox.classList.add('d-none');
        }
    </script>


    <?php require __DIR__ . '/../../includes/footer.php'; ?>