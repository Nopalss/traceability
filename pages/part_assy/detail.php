<?php
require_once __DIR__ . '/../../includes/config.php';

$partAssy = $_GET['part_assy'] ?? '';
if ($partAssy === '') {
    redirect('pages/part_assy/');
}


// ambil BOM existing
$stmt = $pdo->prepare("
    SELECT pa.part_code, pa.qty, p.part_name, s.name_supplier
    FROM tbl_part_assy pa
    JOIN tbl_part p ON pa.part_code = p.part_code
    JOIN tbl_supplier s ON p.supplier = s.id_supplier
    WHERE pa.part_assy = :part_assy
");
$stmt->execute([':part_assy' => $partAssy]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ambil BOM existing
// --- Ambil data part ---
$stmt = $pdo->prepare(
    "SELECT p.id_part, p.part_code, p.part_name, p.supplier, s.name_supplier
     FROM tbl_part p
     JOIN tbl_supplier s ON p.supplier = s.id_supplier
     WHERE p.part_code = :part_code
     LIMIT 1"
);
$stmt->execute([':part_code' => $partAssy]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);
$_SESSION['halaman'] = 'part assy';
$_SESSION['menu']    = 'part_assy';
$_SESSION['subHalaman'] = ' | Detail Part Assy | ' . $partAssy;


require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-2">
                    <div class="card">
                        <div class="card-body pb-3 pt-5">
                            <h3>Detail Part</h3>
                            <div class="col mt-5 mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Part Code</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['part_code'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Part Name</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['part_name'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col mb-4">
                                <div class="form-row align-items-center">
                                    <div class="col-4 font-weight-bolder" style="font-size: 0.75rem;">
                                        <label>Supplier</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-sm small font-weight-bold"
                                            style="font-size: 0.8125rem;"
                                            value="<?= $part['name_supplier'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h5>Assembly Components</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th style="font-size: 0.85rem">No</th>
                                            <th style="font-size: 0.85rem">Part Code</th>
                                            <th style="font-size: 0.85rem">Part Name</th>
                                            <th style="font-size: 0.85rem">Qty</th>
                                            <th style="font-size: 0.85rem">Supplier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($items)): ?>
                                            <?php $i = 1;
                                            foreach ($items as $p): ?>
                                                <tr>
                                                    <td style="font-size: 0.85rem"><?= $i++ ?></td>
                                                    <td style="font-size: 0.85rem" class="font-weight-bolder">
                                                        <!-- <a href="<?= BASE_URL ?>pages/part_assy/detail.php?part_assy=<?= $i['part_code'] ?>">
                                                            <?= $p['part_code'] ?>
                                                        </a> -->
                                                        <?= $p['part_code'] ?>
                                                    </td>
                                                    <td style="font-size: 0.85rem"><?= $p['part_name'] ?></td>
                                                    <td style="font-size: 0.85rem"><?= $p['qty'] ?></td>
                                                    <td style="font-size: 0.85rem"><?= $p['name_supplier'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5"
                                                    class="text-muted text-center font-weight-bolder"
                                                    style="font-size: 0.85rem">
                                                    Not Yet Used in Production
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>