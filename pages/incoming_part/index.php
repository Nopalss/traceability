<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';
$_SESSION['halaman'] = 'incoming part';
$_SESSION['menu'] = 'incoming part';


$sql = "
    SELECT 
        d.ref_number,
        d.part_code,
        d.qty,
        d.incoming_date,
        d.status,
        d.lot_no,
        d.remarks,
        p.part_name,
        p.supplier
    FROM tbl_detail_part d
    JOIN tbl_part p 
        ON p.part_code = d.part_code
    ORDER BY d.incoming_date DESC
    LIMIT 5
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="card card-custom">
                        <div class="card-header flex-wrap border-0 pt-6 pb-0">
                            <div class="card-title">

                            </div>
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <a href="<?= BASE_URL ?>pages/incoming_part/scan.php" class="btn btn-primary font-weight-bolder">
                                    <span class="svg-icon svg-icon-md"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Code\Plus.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                <path d="M11,11 L11,7 C11,6.44771525 11.4477153,6 12,6 C12.5522847,6 13,6.44771525 13,7 L13,11 L17,11 C17.5522847,11 18,11.4477153 18,12 C18,12.5522847 17.5522847,13 17,13 L13,13 L13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 L11,13 L7,13 C6.44771525,13 6,12.5522847 6,12 C6,11.4477153 6.44771525,11 7,11 L11,11 Z" fill="#000000" />
                                            </g>
                                        </svg><!--end::Svg Icon--></span>Scan Part
                                </a>
                                <!--end::Button-->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3>5 Part Terakhir Masuk</h3>
                                <a href="<?= BASE_URL ?>pages/incoming_part/all.php">Lihat Semua</a>
                            </div>
                            <div class="table-responsive">

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Ref. NO</th>
                                            <th>Part Code</th>
                                            <th>Quantity</th>
                                            <th>Supplier</th>
                                            <th>Incoming</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $r): ?>
                                            <tr>
                                                <td><?= $r['ref_number'] ?></td>
                                                <td><?= $r['part_code'] ?></td>
                                                <td><?= $r['qty'] ?></td>
                                                <td><?= $r['supplier'] ?></td>
                                                <td><?= $r['incoming_date'] ?></td>
                                                <td><a href="<?= BASE_URL ?>pages/incoming_part/detail.php?ref_no=<?= $r['ref_number'] ?>">Detail</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!--end: Datatable-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require __DIR__ . '/../../includes/footer.php';
?>