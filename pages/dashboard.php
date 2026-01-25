<?php

require_once __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/clear_temp_session.php';
$_SESSION['halaman'] = 'dashboard';
$_SESSION['menu'] = 'dashboard';

$_SESSION['subHalaman'] = '';

require __DIR__ . '/../includes/aside.php';
require __DIR__ . '/../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 ml-0">
                    <div class="card">
                        <div class="card-body px-5 py-4 mb-2">
                            <div class="d-flex align-items-center">
                                <div class=""><img src="<?= BASE_URL ?>assets/media/icon/icon1.png" alt="" style="width:50px"></div>
                                <div class=" ml-3">
                                    <h3 class="small mb-2">Total part</h3>
                                    <h3>1250</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 ml-0">
                    <div class="card">
                        <div class="card-body px-5 py-4 mb-2">
                            <div class="d-flex align-items-center">
                                <div class=""><img src="<?= BASE_URL ?>assets/media/icon/icon2.png" alt="" style="width:50px"></div>
                                <div class=" ml-3">
                                    <p class="small mb-2">Incoming Hari Ini</p>
                                    <h3>1250</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 ml-0">
                    <div class="card">
                        <div class="card-body px-5 py-4 mb-2">
                            <div class="d-flex align-items-center">
                                <div class=""><img src="<?= BASE_URL ?>assets/media/icon/icon3.png" alt="" style="width:50px"></div>
                                <div class=" ml-3">
                                    <p class="small mb-2">Stok Minim</p>
                                    <h3>1250</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 ml-0">
                    <div class="card">
                        <div class="card-body px-5 py-4 mb-2">
                            <div class="d-flex align-items-center">
                                <div class=""><img src="<?= BASE_URL ?>assets/media/icon/icon4.png" alt="" style="width:50px"></div>
                                <div class=" ml-3">
                                    <p class="small mb-2">Produksi Aktif</p>
                                    <h3>1250</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                .
            </div>
        </div>
    </div>
</div>


<?php
require __DIR__ . '/../includes/footer.php';
?>