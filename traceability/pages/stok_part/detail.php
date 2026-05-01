<?php
require_once __DIR__ . '/../../includes/config.php';

$partCode = $_GET['part_code'] ?? '';
$search   = $_GET['search'] ?? '';

// 🔥 FIX: boleh kosong
$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';

$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

if (!$partCode) {
    header('Location: index.php');
    exit;
}

/*
=============================
 GET PART NAME
=============================
*/
$stmt = $pdo->prepare("SELECT p.part_name, p.part_code, s.name_supplier as supplier FROM tbl_part p JOIN tbl_supplier s ON s.id_supplier = p.supplier WHERE id_part = ?");
$stmt->execute([$partCode]);
$part = $stmt->fetch();

if (!$part) {
    header('Location: index.php');
    exit;
}

$partName = $part['part_name'];
$supplier = $part['supplier'];
$partcode_asli = $part['part_code'];

/*
=============================
 LOT QUERY
=============================
*/
$sql = "
SELECT 
    lot_no,
    SUM(qty) as total_qty,
    SUM(remain) as total_remain,
    MAX(incoming_date) as last_date
FROM tbl_detail_part
WHERE part_id = ?
AND status = 'IN'
AND remain > 0
";

$params = [$partCode];

/*
=============================
 DATE FILTER (OPTIONAL)
=============================
*/
if (!empty($date_from) && !empty($date_to)) {
    $sql .= " AND DATE(incoming_date) BETWEEN ? AND ?";
    $params[] = $date_from;
    $params[] = $date_to;
}

/*
=============================
 SEARCH
=============================
*/
if ($search) {
    $sql .= " AND (lot_no LIKE ? OR ref_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= "
GROUP BY lot_no 
ORDER BY last_date DESC
LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
=============================
 COUNT LOT
=============================
*/
$countSql = "
SELECT COUNT(DISTINCT lot_no)
FROM tbl_detail_part
WHERE part_id= ?
AND status = 'IN'
AND remain > 0
";

$countParams = [$partCode];

if (!empty($date_from) && !empty($date_to)) {
    $countSql .= " AND DATE(incoming_date) BETWEEN ? AND ?";
    $countParams[] = $date_from;
    $countParams[] = $date_to;
}

$stmt = $pdo->prepare($countSql);
$stmt->execute($countParams);
$totalLots = $stmt->fetchColumn();

$totalPages = ceil($totalLots / $limit);

/*
=============================
 DETAIL ONLY SELECTED LOT
=============================
*/
$lotList = array_column($lots, 'lot_no');
$detailByLot = [];

if (!empty($lotList)) {

    $inQuery = implode(',', array_fill(0, count($lotList), '?'));

    $stmt = $pdo->prepare("
        SELECT *
        FROM tbl_detail_part
        WHERE part_id = ?
        AND lot_no IN ($inQuery)
        ORDER BY incoming_date DESC
    ");

    $stmt->execute(array_merge([$partCode], $lotList));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $r) {
        $detailByLot[$r['lot_no']][] = $r;
    }
}

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>


<style>
    .summary-box {
        border-radius: 12px;
        background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        color: white;
    }

    .badge-remain {
        font-size: 13px;
        padding: 6px 10px;
    }

    .lot-row {
        cursor: pointer;
    }

    .detail-table {
        background: #f8f9fa;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <!-- HEADER -->
        <div class="card summary-box mb-6 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h4><?= $partcode_asli  ?></h4>
                    <small><?= $partName ?> - <?= $supplier ?></small>
                </div>
                <div>Total Lot : <b><?= $totalLots ?></b></div>
            </div>
        </div>

        <!-- FILTER -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET">

                    <input type="hidden" name="part_code" value="<?= $partCode ?>">

                    <div class="row">

                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search Lot / Ref"
                                value="<?= htmlspecialchars($search) ?>">
                        </div>

                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control"
                                value="<?= $date_from ?>">
                        </div>

                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control"
                                value="<?= $date_to ?>">
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary">Filter</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE tetap sama -->
        <!-- TABLE -->
        <div class="card shadow-sm">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th>Lot No</th>
                            <th>Total Qty</th>
                            <th>Total Remain</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($lots as $lot): ?>
                            <tr>
                                <td><b><?= $lot['lot_no'] ?></b></td>
                                <td><?= number_format($lot['total_qty']) ?></td>
                                <td><?= number_format($lot['total_remain']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info"
                                        data-toggle="collapse"
                                        data-target="#lot_<?= md5($lot['lot_no']) ?>">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <tr class="collapse" id="lot_<?= md5($lot['lot_no']) ?>">
                                <td colspan="4">

                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Ref</th>
                                                <th>Qty</th>
                                                <th>Remain</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($detailByLot[$lot['lot_no']] ?? [] as $d): ?>
                                                <tr>
                                                    <td><?= $d['ref_number'] ?></td>
                                                    <td><?= $d['qty'] ?></td>
                                                    <td><?= $d['remain'] ?></td>
                                                    <td><?= date('d M Y H:i', strtotime($d['incoming_date'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>

                <!-- PAGINATION -->
                <div class="mt-3 text-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?part_code=<?= $partCode ?>&page=<?= $i ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>"
                            class="btn btn-sm <?= $i == $page ? 'btn-primary' : 'btn-light' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>