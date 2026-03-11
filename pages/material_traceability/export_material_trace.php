<?php

require_once __DIR__ . '/../../includes/config.php';

$lot = $_GET['lot'] ?? '';

if (!$lot) {
    die("Lot tidak ditemukan");
}

/*
=====================================
TRACE DATA
=====================================
*/

$sql = "
SELECT

    dpr.lot_no,
    dpr.part_code,
    pt.part_name,

    dp.serial_no,
    dp.product_code,
    prod.part_name AS product_name,

    dp.operator,
    dp.created_at,
    dp.out_date,

    line.line_name,
    dpr.used_qty,

    loc.name_supplier AS location_name

FROM tbl_detail_production dpr

JOIN tbl_detail_product dp
ON dp.serial_no = dpr.serial_no

LEFT JOIN tbl_part pt
ON pt.part_code = dpr.part_code

LEFT JOIN tbl_part prod
ON prod.part_code = dp.product_code

LEFT JOIN tbl_line line
ON line.line_id = dp.line_id

LEFT JOIN tbl_supplier loc
ON loc.id_supplier = dp.location

WHERE dpr.lot_no LIKE ?

ORDER BY dp.created_at
";

$stmt = $pdo->prepare($sql);
$stmt->execute(["%$lot%"]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
=====================================
SUMMARY MATERIAL
=====================================
*/

$sqlSummary = "
SELECT

dpt.ref_number,
dpt.part_code,
pt.part_name,
dpt.qty,
dpt.remain,
dpt.incoming_date,

sup.name_supplier,

(
SELECT SUM(used_qty)
FROM tbl_detail_production
WHERE lot_no = dpt.lot_no
) AS total_used,

(
SELECT SUM(lost_qty)
FROM tbl_material_loss
WHERE ref_number = dpt.ref_number
) AS total_loss

FROM tbl_detail_part dpt

LEFT JOIN tbl_part pt
ON pt.part_code = dpt.part_code

LEFT JOIN tbl_supplier sup
ON sup.id_supplier = pt.supplier

WHERE dpt.lot_no = ?
LIMIT 1
";

$s = $pdo->prepare($sqlSummary);
$s->execute([$lot]);
$summary = $s->fetch(PDO::FETCH_ASSOC);


/*
=====================================
EXCEL HEADER
=====================================
*/

$filename = "MATERIAL_TRACE_" . $lot . ".xls";

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

        .title {
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
        }

        .header {
            background: #6f42c1;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .info {
            background: #f4f6fb;
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
            <td colspan="8" class="title">
                Material Traceability Report
            </td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>

        <tr>
            <td class="info">Material Lot</td>
            <td><?= $lot ?></td>
        </tr>

        <tr>
            <td class="info">Part</td>
            <td>
                <?= $summary['part_code'] ?>
                -
                <?= $summary['part_name'] ?>
            </td>
        </tr>

        <tr>
            <td class="info">Supplier</td>
            <td><?= $summary['name_supplier'] ?></td>
        </tr>

        <tr>
            <td class="info">Incoming Date</td>
            <td><?= $summary['incoming_date'] ?></td>
        </tr>

        <tr>
            <td class="info">Ref Number</td>
            <td><?= $summary['ref_number'] ?></td>
        </tr>

        <tr>
            <td class="info">Qty</td>
            <td><?= $summary['qty'] ?></td>
        </tr>

        <tr>
            <td class="info">Remain</td>
            <td><?= $summary['remain'] ?></td>
        </tr>

        <tr>
            <td class="info">Total Used</td>
            <td><?= $summary['total_used'] ?? 0 ?></td>
        </tr>

        <tr>
            <td class="info">Material Loss</td>
            <td><?= $summary['total_loss'] ?? 0 ?></td>
        </tr>

        <tr>
            <td class="info">NG Product</td>
            <td>0</td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>


        <tr>

            <th class="header">Serial Product</th>
            <th class="header">Product</th>
            <th class="header">Line</th>
            <th class="header">Operator</th>
            <th class="header">Created At</th>
            <th class="header">Location</th>
            <th class="header">Out Date</th>
            <th class="header">Used Qty</th>

        </tr>


        <?php foreach ($data as $r) { ?>

            <tr>

                <td><?= $r['serial_no'] ?></td>

                <td>
                    <?= $r['product_code'] ?>
                    -
                    <?= $r['product_name'] ?>
                </td>

                <td><?= $r['line_name'] ?></td>

                <td><?= $r['operator'] ?></td>

                <td><?= $r['created_at'] ?></td>

                <td><?= $r['location_name'] ?? 'Warehouse' ?></td>

                <td><?= $r['out_date'] ?></td>

                <td><?= $r['used_qty'] ?></td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>

<?php exit; ?>