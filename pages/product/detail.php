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
   GET DETAIL PRODUCT (FIX)
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
        s.name_supplier
    FROM tbl_detail_product dp
    LEFT JOIN tbl_line l ON l.line_id = dp.line_id
    LEFT JOIN tbl_supplier s ON s.id_supplier = dp.location
    WHERE dp.product_code = ?
    ORDER BY dp.created_at DESC
");
$stmt->execute([$productCode]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalProduced = count($details);

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

    .summary-card h2 {
        font-weight: 700;
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

    .location-badge {
        background: #f3f4f6;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6>Total Lot</h6>
                    <h2><?= $totalProduced ?></h2>
                </div>
                <div>
                    <i class="fa fa-box fa-3x"></i>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lot No</th>
                                <th>Qty</th>
                                <th>Shift</th>
                                <th>Line</th>
                                <th>Operator</th>
                                <th>Ref Number</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php if ($details): ?>
                                <?php foreach ($details as $i => $row): ?>

                                    <?php
                                    $locationName = ($row['location'] == 0 || $row['location'] == null)
                                        ? 'Gudang'
                                        : $row['name_supplier'];

                                    $statusClass = $row['status'] == 'in'
                                        ? 'status-in'
                                        : 'status-out';
                                    ?>

                                    <tr>
                                        <td><?= $i + 1 ?></td>

                                        <td><strong><?= htmlspecialchars($row['serial_no']) ?></strong></td>

                                        <td><?= $row['qty'] ?></td>

                                        <td>
                                            <span class="badge-shift shift-<?= $row['shift'] ?>">
                                                Shift <?= $row['shift'] ?>
                                            </span>
                                        </td>

                                        <td><?= htmlspecialchars($row['line_name'] ?? '-') ?></td>

                                        <td><?= htmlspecialchars($row['operator']) ?></td>

                                        <td><?= htmlspecialchars($row['ref_number']) ?></td>

                                        <td>
                                            <span class="location-badge">
                                                <?= htmlspecialchars($locationName) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge-status <?= $statusClass ?>">
                                                <?= strtoupper($row['status']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                                        </td>
                                    </tr>

                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        Belum ada data produksi.
                                    </td>
                                </tr>
                            <?php endif ?>

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