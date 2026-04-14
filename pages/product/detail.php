<?php
require_once __DIR__ . '/../../includes/config.php';

$productCode = $_GET['product_code'] ?? '';

if ($productCode == '') {
    header('Location: index.php');
    exit;
}

/* =========================
   GET PRODUCT INFO
========================= */
$stmt = $pdo->prepare("
    SELECT part_code, part_name
    FROM tbl_part
    WHERE part_code = ?
      AND status_assy = 1
");
$stmt->execute([$productCode]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

/* =========================
   GET DETAIL PRODUCT
========================= */
$stmt = $pdo->prepare("
    SELECT 
        dp.product_code,
        dp.serial_no,
        dp.qty,
        dp.shift,
        l.line_name,
        dp.operator,
        dp.ref_number,
        dp.remarks,
        dp.created_at,
        dp.status,
        dp.location,
        dp.out_date,
        s.name_supplier
    FROM tbl_detail_product dp
    LEFT JOIN tbl_line l ON l.line_id = dp.line_id
    LEFT JOIN tbl_supplier s ON s.id_supplier = dp.location
    WHERE dp.product_code = ?
    ORDER BY dp.created_at DESC
");
$stmt->execute([$productCode]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GROUP BY SERIAL NUMBER
========================= */
$grouped = [];

foreach ($details as $row) {
    $serial = $row['serial_no'];

    if (!isset($grouped[$serial])) {
        $grouped[$serial] = [
            'total_qty' => 0,
            'rows' => []
        ];
    }

    $grouped[$serial]['total_qty'] += $row['qty'];
    $grouped[$serial]['rows'][] = $row;
}

$totalProduced = count($grouped);

$_SESSION['menu'] = 'stok_product';
$_SESSION['halaman'] = 'detail product';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<style>
    .summary-card {
        border-radius: 16px;
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        padding: 25px;
    }

    .table-custom thead {
        background: #f8f9fc;
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .status-in {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-out {
        background: #ffebee;
        color: #c62828;
    }

    .badge-shift {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .shift-1 {
        background: #e3f2fd;
        color: #1565c0;
    }

    .shift-2 {
        background: #fce4ec;
        color: #ad1457;
    }

    .shift-3 {
        background: #fce4ec;
        color: #14ad96;
    }

    .location-badge {
        background: #f3f4f6;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }

    .detail-table {
        background: #f8f9fa;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container mt-5">

        <!-- HEADER -->
        <div class="mb-4">
            <h3 class="font-weight-bold">Detail Product</h3>
            <p class="text-muted mb-1">
                <strong><?= $product['part_code'] ?></strong> - <?= $product['part_name'] ?>
            </p>
        </div>

        <!-- SUMMARY -->
        <div class="summary-card mb-5">
            <h6>Total Lot</h6>
            <h2><?= $totalProduced ?></h2>
        </div>

        <!-- TABLE -->
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lot Number</th>
                                <th>Total Qty</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $no = 1;
                            foreach ($grouped as $serial => $data): ?>

                                <?php $collapseId = 'serial_' . md5($serial); ?>

                                <!-- PARENT -->
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= $serial ?></strong></td>
                                    <td><?= number_format($data['total_qty']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"
                                            data-toggle="collapse"
                                            data-target="#<?= $collapseId ?>">
                                            Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- CHILD -->
                                <tr class="collapse detail-table" id="<?= $collapseId ?>">
                                    <td colspan="4">

                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Ref Number</th>
                                                    <th>Qty</th>
                                                    <th>Shift</th>
                                                    <th>Line</th>
                                                    <th>Operator</th>
                                                    <th>Status</th>
                                                    <th>Tanggal Production</th>
                                                    <th>Location</th>
                                                    <th>Tanggal Keluar</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php foreach ($data['rows'] as $row):

                                                    $locationName = ($row['location'] == 0 || $row['location'] == null)
                                                        ? 'Gudang'
                                                        : $row['name_supplier'];

                                                    $statusClass = $row['status'] == 'in'
                                                        ? 'status-in'
                                                        : 'status-out';
                                                ?>

                                                    <tr>
                                                        <td><?= $row['ref_number'] ?></td>
                                                        <td><?= $row['qty'] ?></td>
                                                        <td>
                                                            <span class="badge-shift shift-<?= $row['shift'] ?>">
                                                                Shift <?= $row['shift'] ?>
                                                            </span>
                                                        </td>
                                                        <td><?= $row['line_name'] ?? '-' ?></td>
                                                        <td><?= $row['operator'] ?></td>
                                                        <td>
                                                            <span class="badge-status <?= $statusClass ?>">
                                                                <?= strtoupper($row['status']) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                                        <td><?= $locationName ?></td>
                                                        <td>
                                                            <?= $row['out_date'] ? date('d M Y H:i', strtotime($row['out_date'])) : '-' ?>
                                                        </td>
                                                    </tr>

                                                <?php endforeach; ?>

                                            </tbody>
                                        </table>

                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-light">← Kembali</a>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>