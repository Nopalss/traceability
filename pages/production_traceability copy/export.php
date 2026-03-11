<?php

require_once __DIR__ . '/../../includes/config.php';

$product = $_GET['product'] ?? '';
$date    = $_GET['date'] ?? '';


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
    pt.part_name

FROM tbl_detail_product dp

JOIN tbl_detail_production dpr 
    ON dpr.serial_no = dp.serial_no

LEFT JOIN tbl_part pt 
    ON pt.part_code = dpr.part_code

LEFT JOIN tbl_line l
    ON l.line_id = dp.line_id

WHERE dp.product_code=? 
AND DATE(dp.created_at)=?

ORDER BY dp.serial_no,dpr.id
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$product, $date]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) die('No data');

/*
====================================
GROUP
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
        'code' => $r['part_code']
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
            font-size: 12px
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
            <td colspan="50"></td>
        </tr>

        <tr>
            <th class="header">Serial</th>

            <?php for ($i = 1; $i <= $maxParts; $i++): ?>
                <th class="header">Part Name <?= $i ?></th>
                <th class="header">Part Code <?= $i ?></th>
            <?php endfor ?>

            <th class="header">Production</th>
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
                <?php endforeach ?>

                <?php
                $miss = $maxParts - count($d['parts']);
                for ($i = 0; $i < $miss; $i++) {
                    echo "<td></td><td></td>";
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