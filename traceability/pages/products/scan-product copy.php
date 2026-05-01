<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['halaman'] = 'scan product';
$_SESSION['menu'] = 'production';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<!-- LOAD LIBRARY QR -->
<script src="<?= BASE_URL ?>assets/js/scan/html5-qrcode.min.js"></script>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div
        id="scan-container"
        data-api-url="<?= BASE_URL ?>api/production/scan-product.php">

        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="row">

                    <!-- LEFT : SCANNER -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">

                                <!-- MODE CAMERA -->
                                <div id="reader" style="width:270px;height:270px;"></div>

                                <!-- MODE SCANNER GUN -->
                                <input
                                    type="text"
                                    id="qr_raw"
                                    class="mt-3 w-100 form-control"
                                    placeholder="Scan QR Product di sini"
                                    autofocus>
                            </div>
                        </div>

                        <!-- LOG SCAN -->
                        <div class="card mt-3">
                            <div class="card-header py-2">
                                <h6 class="mb-0">Log Scan Hari Ini</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive" style="max-height:220px;overflow:auto;">
                                    <table class="table table-sm table-bordered mb-0 small">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ref Product</th>
                                                <th>Status</th>
                                                <th>Jam</th>
                                            </tr>
                                        </thead>
                                        <tbody id="scan-log-body">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">
                                                    Belum ada scan
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT : DETAIL PRODUCT -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header py-3 pb-2">
                                <h5>Detail Product</h5>
                            </div>

                            <div class="card-body pt-3">
                                <div id="scan-alert" class="alert d-none" role="alert"></div>

                                <!-- PRODUCT INFO -->
                                <form>
                                    <div class="col input-group-sm">
                                        <label class="form-label small font-weight-bolder">Ref Product</label>
                                        <input id="ref_product" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-4">
                                        <label class="form-label small font-weight-bolder">Product Code</label>
                                        <input id="product_code" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-4">
                                        <label class="form-label small font-weight-bolder">Product Name</label>
                                        <input id="product_name" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-4">
                                        <label class="form-label small font-weight-bolder">Production At</label>
                                        <input id="production_at" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-4">
                                        <label class="form-label small font-weight-bolder">Status</label>
                                        <input id="status" class="form-control" disabled>
                                    </div>
                                </form>

                                <!-- PART USED -->
                                <h6 class="mt-8 font-weight-bolder">Part Yang Digunakan</h6>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered small">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ref No</th>
                                                <th>Part Code</th>
                                                <th>Part Name</th>
                                                <th>Supplier</th>
                                                <th>Qty</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody id="part-used-body">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    Belum ada data
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right mt-4">
                                    <a href="<?= BASE_URL ?>pages/production/" class="btn btn-warning btn-sm">
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->
            </div><!-- container -->
        </div>
    </div>
</div>

<!-- LOAD JS SCAN PRODUCT -->
<script src="<?= BASE_URL ?>assets/js/scan/scan-product.js"></script>

<?php
require __DIR__ . '/../../includes/footer.php';
?>