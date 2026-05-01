<?php

require_once __DIR__ . '/../../includes/config.php';

$product = $_GET['product'] ?? '';
$date    = $_GET['date'] ?? '';
$lot     = $_GET['lot'] ?? '';

if (!$product || !$date) die('Parameter tidak lengkap');

/*
====================================
PRODUCT NAME
====================================
*/
$pname = $pdo->prepare("SELECT part_name FROM tbl_part WHERE part_code=? LIMIT 1");
$pname->execute([$product]);
$productName = $pname->fetchColumn() ?: '';

/*
====================================
TRACE DATA
====================================
*/
$sql = "
SELECT 
    dp.serial_no,
    dp.shift,
    l.line_name,
    dp.operator,
    dp.created_at,

    dpr.part_code,
    pt.part_name,

    dpr.lot_no,
    sup.name_supplier,
    dpt.incoming_date

FROM tbl_detail_product dp

JOIN tbl_detail_production dpr 
    ON dpr.serial_no = dp.serial_no

LEFT JOIN tbl_part pt 
    ON pt.part_code = dpr.part_code

LEFT JOIN tbl_line l
    ON l.line_id = dp.line_id

LEFT JOIN tbl_detail_part dpt
    ON dpt.lot_no = dpr.lot_no
    AND dpt.part_code = dpr.part_code

LEFT JOIN tbl_supplier sup
    ON sup.id_supplier = pt.supplier

WHERE dp.product_code=? 
AND DATE(dp.created_at)=?
";

$params = [$product, $date];

/*
====================================
OPTIONAL LOT FILTER
====================================
*/
if ($lot) {
    $sql .= " AND dpr.lot_no LIKE ? ";
    $params[] = "%$lot%";
}

$sql .= " ORDER BY dp.serial_no,dpr.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) die('No data');

/*
====================================
GROUP SERIAL
====================================
*/
$data = [];

foreach ($rows as $r) {

    $s = $r['serial_no'];

    if (!isset($data[$s])) {

        $data[$s] = [
            'meta' => [
                'prod' => $r['created_at'],
                'line' => $r['line_name'],
                'shift' => $r['shift'],
                'operator' => $r['operator']
            ],
            'parts' => []
        ];
    }

    $data[$s]['parts'][] = [

        'name' => $r['part_name'],
        'code' => $r['part_code'],
        'lot'  => $r['lot_no'],
        'supplier' => $r['name_supplier'],
        'incoming' => $r['incoming_date']

    ];
}

$maxParts = 0;
foreach ($data as $d) $maxParts = max($maxParts, count($d['parts']));

/*
====================================
EXCEL HEADER
====================================
*/

$filename = "TRACEABILITY_{$product}_{$date}.xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

?>

<html>

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Calibri;
            font-size: 12px;
        }

        .header {
            background: #1abc9c;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        td,
        th {
            border: 1px solid #999;
            padding: 6px;
        }

        .title {
            font-weight: bold;
            background: #ecf0f1;
        }
    </style>

</head>

<body>

    <table>

        <tr>
            <td class="title">PRODUCT CODE</td>
            <td><?= $product ?></td>
        </tr>

        <tr>
            <td class="title">PRODUCT NAME</td>
            <td><?= $productName ?></td>
        </tr>

        <tr>
            <td class="title">DATE</td>
            <td><?= $date ?></td>
        </tr>

        <tr>
            <td colspan="50"></td>
        </tr>

        <tr>

            <th class="header">Serial</th>

            <?php for ($i = 1; $i <= $maxParts; $i++): ?>

                <th class="header">Part Name <?= $i ?></th>
                <th class="header">Part Code <?= $i ?></th>
                <th class="header">Lot <?= $i ?></th>
                <th class="header">Supplier <?= $i ?></th>
                <th class="header">Incoming Date <?= $i ?></th>

            <?php endfor ?>

            <th class="header">Production Time</th>
            <th class="header">Line</th>
            <th class="header">Shift</th>
            <th class="header">Operator</th>

        </tr>

        <?php foreach ($data as $serial => $d): ?>

            <tr>

                <td><?= $serial ?></td>

                <?php foreach ($d['parts'] as $p): ?>

                    <td><?= $p['name'] ?></td>
                    <td><?= $p['code'] ?></td>
                    <td><?= $p['lot'] ?></td>
                    <td><?= $p['supplier'] ?></td>
                    <td><?= $p['incoming'] ?></td>

                <?php endforeach ?>

                <?php
                $miss = $maxParts - count($d['parts']);

                for ($i = 0; $i < $miss; $i++) {
                    echo "<td></td><td></td><td></td><td></td><td></td>";
                }
                ?>

                <td><?= $d['meta']['prod'] ?></td>
                <td><?= $d['meta']['line'] ?></td>
                <td><?= $d['meta']['shift'] ?></td>
                <td><?= $d['meta']['operator'] ?></td>

            </tr>

        <?php endforeach ?>

    </table>

</body>

</html>

<?php exit; ?>