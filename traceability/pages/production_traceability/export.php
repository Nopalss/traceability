<?php

require_once __DIR__ . '/../../includes/config.php';

$product = $_GET['product'] ?? '';
$date    = $_GET['date'] ?? '';
$lot     = $_GET['lot'] ?? '';
$serial  = $_GET['serial'] ?? '';

if (!$product || !$date) die("Parameter tidak lengkap");

/* ================= PRODUCT ================= */
$p = $pdo->prepare("SELECT part_name FROM tbl_part WHERE part_code=?");
$p->execute([$product]);
$productName = $p->fetchColumn();

/* ================= MAIN QUERY ================= */
$sql = "
SELECT 
    dp.serial_no,
    dp.ref_number,
    dp.operator,
    dp.created_at,
    dp.out_date,
    l.line_name,
    loc.name_supplier AS location_name,

    dpr.part_code,
    dpr.used_qty,
    dpr.lot_no,

    dpt.incoming_date,
    pt.part_name,
    sup.name_supplier

FROM tbl_detail_product dp

JOIN tbl_detail_production dpr 
    ON dpr.serial_no = dp.serial_no
    AND dpr.ref_product = dp.ref_number

JOIN tbl_detail_part dpt 
    ON dpt.ref_number = dpr.ref_number

LEFT JOIN tbl_part pt 
    ON pt.part_code = dpt.part_code

LEFT JOIN tbl_supplier sup 
    ON sup.id_supplier = pt.supplier

LEFT JOIN tbl_line l 
    ON l.line_id = dp.line_id

LEFT JOIN tbl_supplier loc 
    ON loc.id_supplier = dp.location

WHERE dp.product_code=? 
AND DATE(dp.created_at)=?
";

$params = [$product, $date];

if ($serial) {
    $sql .= " AND dp.serial_no LIKE ? ";
    $params[] = "%$serial%";
}

if ($lot) {
    $sql .= " AND dpr.lot_no LIKE ? ";
    $params[] = "%$lot%";
}

$sql .= " ORDER BY dp.serial_no, dp.ref_number, dpr.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) die("No data");

/* ================= GROUP ================= */
$data = [];

foreach ($rows as $r) {
    $data[$r['serial_no']][$r['ref_number']][] = $r;
}

/* ================= HEADER ================= */
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=TRACE_{$product}_{$date}.xls");

?>

<html>

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Calibri;
            font-size: 12px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
        }

        .header {
            background: #2F75B5;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .serial {
            background: #BDD7EE;
            font-weight: bold;
        }

        .ref {
            background: #FFE699;
            font-weight: bold;
        }

        td,
        th {
            border: 1px solid #999;
            padding: 6px;
        }
    </style>

</head>

<body>

    <table>

        <tr>
            <td colspan="13" class="title">
                TRACEABILITY REPORT
            </td>
        </tr>

        <tr>
            <td>Product</td>
            <td><?= $product ?> - <?= $productName ?></td>
        </tr>

        <tr>
            <td>Date</td>
            <td><?= $date ?></td>
        </tr>

        <tr>
            <td colspan="13"></td>
        </tr>

        <tr class="header">
            <th>#</th>
            <th>Lot Product</th>
            <th>Ref Number</th>
            <th>Line</th>
            <th>Operator</th>
            <th>Time</th>
            <th>Location</th>
            <th>Out Date</th>
            <th>Part</th>
            <th>Part Name</th>
            <th>Lot</th>
            <th>Used</th>
            <th>Supplier</th>
            <th>Incoming Date</th>
        </tr>

        <?php $no = 1;
        foreach ($data as $serial => $refs): ?>

            <tr class="serial">
                <td><?= $no++ ?></td>
                <td colspan="13"><?= $serial ?></td>
            </tr>

            <?php foreach ($refs as $ref => $materials):
                $meta = $materials[0];
            ?>

                <tr class="ref">
                    <td></td>
                    <td></td>
                    <td><?= $ref ?></td>
                    <td><?= $meta['line_name'] ?></td>
                    <td><?= $meta['operator'] ?></td>
                    <td><?= $meta['created_at'] ?></td>
                    <td><?= $meta['location_name'] ?></td>
                    <td><?= $meta['out_date'] ?></td>
                    <td colspan="6"></td>
                </tr>

                <?php foreach ($materials as $m): ?>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                        <td><?= $m['part_code'] ?></td>
                        <td><?= $m['part_name'] ?></td>
                        <td><?= $m['lot_no'] ?></td>
                        <td><?= $m['used_qty'] ?></td>
                        <td><?= $m['name_supplier'] ?></td>
                        <td><?= $m['incoming_date'] ?></td>
                    </tr>

        <?php endforeach;
            endforeach;
        endforeach; ?>

    </table>

</body>

</html>