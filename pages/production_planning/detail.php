<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../helper/handlePdoError.php';

$_SESSION['halaman'] = 'production planning';
$_SESSION['menu'] = 'production_planning';
$_SESSION['subHalaman'] = '| Detail Production Plan';

// ================================
// Ambil pp_code
// ================================
$ppCode = $_GET['pp_code'] ?? '';

if ($ppCode === '') {
    header('Location: ' . BASE_URL . 'pages/production_planning/');
    exit;
}

// ================================
// Ambil data by pp_code
// ================================
$stmt = $pdo->prepare("
    SELECT
        pp.*,
        l.line_name
    FROM tbl_production_planning pp
    JOIN tbl_line l ON pp.line_id = l.line_id
    WHERE pp.pp_code = :pp_code
    ORDER BY pp.shift ASC
");
$stmt->execute([':pp_code' => $ppCode]);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$plans) {
    header('Location: ' . BASE_URL . 'pages/production_planning/');
    exit;
}

$header = $plans[0];

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">

        <!-- HEADER CARD -->
        <div class="card mb-7">
            <div class="card-body">
                <h3 class="mb-6">Production Planning Detail</h3>

                <div class="row">
                    <div class="col-md-3">
                        <div class="text-muted">Line</div>
                        <div class="font-weight-bolder"><?= htmlspecialchars($header['line_name']) ?></div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted">Product Code</div>
                        <div class="font-weight-bolder"><?= htmlspecialchars($header['product_code']) ?></div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted">Production Date</div>
                        <div class="font-weight-bolder"><?= $header['production_date'] ?></div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted">Status</div>
                        <span class="badge badge-light-primary text-uppercase px-4 py-2">
                            <?= $header['status'] ?>
                        </span>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-3">
                        <div class="text-muted">Total Quantity</div>
                        <div class="font-weight-bolder display-4 text-primary">
                            <?= (int)$header['total_qty'] ?>
                        </div>
                    </div>

                    <div class="col-md-9 d-flex align-items-end justify-content-end">
                        <a href="<?= BASE_URL ?>pages/production_planning/edit.php?pp_code=<?= urlencode($ppCode) ?>"
                            class="btn btn-warning mr-2">
                            <i class="flaticon-edit"></i> Edit
                        </a>

                        <a href="<?= BASE_URL ?>pages/production_planning/"
                            class="btn btn-outline-secondary">
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL SHIFT TABLE -->
        <div class="card">
            <div class="card-body">
                <h4 class="mb-5">Shift Detail</h4>

                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">Shift</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Start</th>
                            <th class="text-center">End</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $row): ?>
                            <tr>
                                <td class="text-center font-weight-bolder">
                                    <?= $row['shift'] ?>
                                </td>
                                <td class="text-center">
                                    <?= $row['qty'] ?>
                                </td>
                                <td class="text-center">
                                    <?= substr($row['start'], 0, 5) ?>
                                </td>
                                <td class="text-center">
                                    <?= substr($row['end'], 0, 5) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>