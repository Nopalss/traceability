<?php

require_once __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/clear_temp_session.php';
$_SESSION['halaman'] = 'dashboard';
$_SESSION['menu'] = 'dashboard';

require __DIR__ . '/../includes/aside.php';
require __DIR__ . '/../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">

            </div>
        </div>
    </div>
</div>


<?php
require __DIR__ . '/../includes/footer.php';
?>