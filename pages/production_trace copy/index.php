<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';

$_SESSION['halaman'] = 'Production Traceability';
$_SESSION['menu'] = 'traceability';

require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

// ================= FILTER =================

$line   = $_GET['line'] ?? '';
$from   = $_GET['from'] ?? '';
$to     = $_GET['to'] ?? '';
$product = $_GET['product'] ?? '';
$lotref = $_GET['lotref'] ?? '';

$where = [];
$params = [];

if ($line) {
    $where[] = "dp.line_id=?";
    $params[] = $line;
}

if ($from && $to) {
    $where[] = "DATE(dp.created_at) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
}

if ($product) {
    $where[] = "dp.product_code LIKE ?";
    $params[] = "%$product%";
}

if ($lotref) {
    $where[] = "(dp.serial_no LIKE ? OR dp.ref_number LIKE ? OR dpr.lot_no LIKE ?)";
    $params[] = "%$lotref%";
    $params[] = "%$lotref%";
    $params[] = "%$lotref%";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";


$sql = "
SELECT 
    dp.product_code,
    DATE(dp.created_at) production_date,
    dp.line_id,
    l.line_name,
    dp.shift,
    COUNT(dp.serial_no) AS qty
FROM tbl_detail_product dp
LEFT JOIN tbl_line l ON l.line_id = dp.line_id
$whereSql
GROUP BY dp.product_code, DATE(dp.created_at), dp.line_id, l.line_name, dp.shift
ORDER BY production_date DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// load line
$lines = $pdo->query("SELECT line_id,line_name FROM tbl_line")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">

        <!-- FILTER CARD -->
        <div class="card shadow-sm mb-6">
            <div class="card-header">
                <h3 class="card-title">Production Traceability</h3>
            </div>

            <form method="get">
                <div class="card-body row g-3">

                    <div class="col-md-2">
                        <label>Line</label>
                        <select name="line" class="form-control">
                            <option value="">All</option>
                            <?php foreach ($lines as $l): ?>
                                <option value="<?= $l['line_id'] ?>" <?= $line == $l['line_id'] ? 'selected' : '' ?>>
                                    <?= $l['line_name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>From</label>
                        <input type="date" name="from" value="<?= $from ?>" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>To</label>
                        <input type="date" name="to" value="<?= $to ?>" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>Product</label>
                        <input type="text" name="product" value="<?= $product ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Lot / Ref / Serial</label>
                        <input type="text" name="lotref" value="<?= $lotref ?>" class="form-control">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary w-100"><i class="fa fa-search"></i></button>
                    </div>

                </div>
            </form>
        </div>

        <!-- RESULT -->
        <div class="card shadow-sm">
            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Line</th>
                            <th>Shift</th>
                            <th>Qty</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= $r['product_code'] ?></td>
                                <td><?= $r['production_date'] ?></td>
                                <td><?= $r['line_name'] ?: $r['line_id'] ?></td>
                                <td><?= $r['shift'] ?></td>
                                <td><?= $r['qty'] ?></td>
                                <td>
                                    <a href="detail.php?product=<?= $r['product_code'] ?>&date=<?= $r['production_date'] ?>&line=<?= $r['line_id'] ?>" class="btn btn-sm btn-info"><i class="far fa-eye"></i></a>

                                    <a href="export.php?product=<?= $r['product_code'] ?>&date=<?= $r['production_date'] ?>&line=<?= $r['line_id'] ?>" class="btn btn-sm btn-success"><i class="far fa-file-excel"></i></a>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        <?php if (!$rows): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No data</td>
                            </tr>
                        <?php endif ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>