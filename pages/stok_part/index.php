<?php
require_once __DIR__ . '/../../includes/config.php';
$_SESSION['menu'] = 'stok_part';
$_SESSION['table'] = 'stok_part';
$_SESSION['halaman'] = 'stok part';
$_SESSION['subHalaman'] = '';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class=" container ">
            <!--begin::Card-->
            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <div class="card-title">
                        <h3 class="card-label">
                            Data Stok Part
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
                    <!--end: Datatable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!-- end::Container -->
    </div>
</div>
<!-- end::entry -->

<?php
require __DIR__ . '/../../includes/footer.php';
?>