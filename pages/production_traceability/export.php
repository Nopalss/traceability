<?php

require_once __DIR__ . '/../../includes/config.php';

$product = $_GET['product'] ?? '';
$date    = $_GET['date'] ?? '';
$lot     = $_GET['lot'] ?? '';
$serial  = $_GET['serial'] ?? '';

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
    dp.out_date,

    loc.name_supplier AS location_name,

    dpr.part_code,
    pt.part_name,
    dpr.used_qty,

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

LEFT JOIN tbl_supplier loc
    ON loc.id_supplier = dp.location

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
OPTIONAL FILTER
====================================
*/

if ($lot) {
    $sql .= " AND dpr.lot_no LIKE ? ";
    $params[] = "%$lot%";
}

if ($serial) {
    $sql .= " AND dp.serial_no LIKE ? ";
    $params[] = "%$serial%";
}

$sql .= " ORDER BY dp.serial_no,dpr.id";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) die('No data');


/*
====================================
GROUP BY SERIAL
====================================
*/

$data = [];

foreach ($rows as $r) {

    $serialNo = $r['serial_no'];

    if (!isset($data[$serialNo])) {

        $data[$serialNo] = [
            'meta' => [
                'line' => $r['line_name'],
                'operator' => $r['operator'],
                'time' => $r['created_at'],
                'location' => $r['location_name'],
                'out_date' => $r['out_date']
            ],
            'parts' => []
        ];
    }

    $data[$serialNo]['parts'][] = [
        'code' => $r['part_code'],
        'name' => $r['part_name'],
        'lot' => $r['lot_no'],
        'used' => $r['used_qty'],
        'supplier' => $r['name_supplier'],
        'incoming' => $r['incoming_date']
    ];
}


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
            background: #4472C4;
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

        .serial {
            background: #BDD7EE;
            font-weight: bold;
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
            <td colspan="20"></td>
        </tr>

        <tr class="header">
            <th>#</th>
            <th>Lot Product</th>
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

        <?php

        $no = 1;

        foreach ($data as $serialNo => $d):

        ?>

            <tr class="serial">
                <td><?= $no++ ?></td>
                <td><?= $serialNo ?></td>
                <td><?= $d['meta']['line'] ?></td>
                <td><?= $d['meta']['operator'] ?></td>
                <td><?= $d['meta']['time'] ?></td>
                <td><?= $d['meta']['location'] ?? 'Warehouse' ?></td>
                <td><?= $d['meta']['out_date'] ?: '-' ?></td>
                <td colspan="6"></td>
            </tr>

            <?php foreach ($d['parts'] as $p): ?>

                <tr>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td><?= $p['code'] ?></td>
                    <td><?= $p['name'] ?></td>
                    <td><?= $p['lot'] ?></td>
                    <td><?= $p['used'] ?></td>
                    <td><?= $p['supplier'] ?></td>
                    <td><?= $p['incoming'] ?></td>

                </tr>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </table>

</body>

</html>

<?php exit; ?>