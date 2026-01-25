<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['halaman'] = 'scan incoming part';
$_SESSION['menu'] = 'incoming_part';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<!-- LOAD LIBRARY QR (SATU KALI) -->
<script src="<?= BASE_URL ?>assets/js/scan/html5-qrcode.min.js"></script>

<div
    class="content d-flex flex-column flex-column-fluid pt-0"
    id="kt_content">
    <div
        id="scan-container"
        data-api-url="<?= BASE_URL ?>api/part/incoming/scan.php">
        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="row">

                    <!-- LEFT -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">

                                <!-- MODE HP (KAMERA) -->
                                <div id="reader" style="width:270px;height:270px;"></div>

                                <!-- MODE SCANNER GUN -->
                                <input
                                    type="text"
                                    id="qr_raw"
                                    class="mt-3 w-100 form-control"
                                    placeholder="Scan QR di sini"
                                    autofocus>
                            </div>
                        </div>

                        <!-- disini aja kali ya -->
                        <div class="card mt-3">
                            <div class="card-header py-2">
                                <h6 class="mb-0">Log Scan Hari Ini</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive" style="max-height: 220px; overflow:auto;">
                                    <table class="table table-sm table-bordered mb-0 small">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ref. No</th>
                                                <th>Part Code</th>
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

                    <!-- RIGHT -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header py-3 pb-2">
                                <h5>Detail Part</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div id="scan-alert" class="alert d-none" role="alert"></div>
                                <form>
                                    <div class="col input-group-sm">
                                        <label class="form-label small font-weight-bolder">Part Code</label>
                                        <input id="part_code" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-5">
                                        <label class="form-label small font-weight-bolder">Lot No</label>
                                        <input id="lot_no" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-5">
                                        <label class="form-label small font-weight-bolder">Quantity</label>
                                        <input id="qty" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-5">
                                        <label class="form-label small font-weight-bolder">Ref. No</label>
                                        <input id="ref_no" class="form-control" disabled>
                                    </div>

                                    <div class="col input-group-sm mt-5 mb-7 ">
                                        <label class="form-label small font-weight-bolder">Remarks</label>
                                        <input id="remarks" class="form-control" disabled>
                                    </div>
                                </form>
                                <div class="text-right col">
                                    <a href="<?= BASE_URL ?>pages/incoming_part/" class="btn btn-warning btn-sm">kembali</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- LOAD JS CUSTOM -->
<script src="<?= BASE_URL ?>assets/js/scan/incoming-part.js"></script>

<?php
require __DIR__ . '/../../includes/footer.php';
?>