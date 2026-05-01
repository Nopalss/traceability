<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['halaman'] = 'scan product';
$_SESSION['menu'] = 'production';
$_SESSION['subHalaman'] = '';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div
    class="content d-flex flex-column flex-column-fluid pt-0"
    id="kt_content">

    <div
        id="scan-container"
        data-api-scan="<?= BASE_URL ?>api/production/scan-cek-product.php">

        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="row">

                    <!-- LEFT -->
                    <div class="col-lg-5 mb-5">
                        <div class="card">
                            <div class="card-body d-flex flex-column">
                                <h4>Masukkan QR Code</h4>

                                <textarea
                                    id="qr_raw"
                                    class="mt-3 w-100 form-control"
                                    placeholder="Paste / scan QR di sini"
                                    rows="5"
                                    autofocus></textarea>

                                <button
                                    id="btn-execute"
                                    type="button"
                                    class="btn btn-success w-100 mt-2">
                                    Execute
                                </button>
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
                                    <div class="form-row mb-5">
                                        <div class="col input-group-sm">
                                            <label class="form-label small font-weight-bolder">
                                                Ref Product
                                            </label>
                                            <input id="ref_product" class="form-control form-control-sm" disabled>
                                        </div>

                                        <div class="col input-group-sm">
                                            <label class="form-label small font-weight-bolder">
                                                Product Code
                                            </label>
                                            <input id="product_code" class="form-control form-control-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-row mb-5">
                                        <div class="col input-group-sm ">
                                            <label class="form-label small font-weight-bolder">
                                                Product Name
                                            </label>
                                            <input id="product_name" class="form-control form-control-sm" disabled>
                                        </div>

                                        <div class="col input-group-sm">
                                            <label class="form-label small font-weight-bolder">
                                                Production At
                                            </label>
                                            <input id="production_at" class="form-control form-control-sm" disabled>
                                        </div>
                                    </div>
                                    <div class="form-row mb-5">
                                        <div class="col input-group-sm">
                                            <label class="form-label small font-weight-bolder">
                                                Status
                                            </label>
                                            <input id="status" class="form-control form-control-sm" disabled>
                                        </div>

                                    </div>
                                    <div class="form-row">
                                        <div class="col input-group-sm">
                                            <label class="form-label small font-weight-bolder">
                                                Tujuan
                                            </label>
                                            <input id="tujuan" class="form-control form-control-sm" disabled>
                                        </div>
                                        <div class="col input-group-sm">
                                            <label class="form-label small font-weight-bolder">
                                                Delivery Date
                                            </label>
                                            <input id="delivery_date" class="form-control form-control-sm" disabled>
                                        </div>
                                    </div>
                                </form>
                                <hr class="my-5">
                                <!-- PART USED -->
                                <h6 class="mt-8">
                                    Part Yang Digunakan
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-sm small">
                                        <thead class="thead-light small">
                                            <tr>
                                                <td style="font-size:11px">Ref No</td>
                                                <td style="font-size:11px">Part Code</td>
                                                <td style="font-size:11px">Part Name</td>
                                                <td style="font-size:11px">Supplier</td>
                                                <td style="font-size:11px"></td>
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
                                    <a href="<?= BASE_URL ?>pages/production/"
                                        class="btn btn-warning btn-sm">
                                        Kembali
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS KHUSUS SCAN PRODUCT -->
<script src="<?= BASE_URL ?>assets/js/scan/scan-cek-product.js"></script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>