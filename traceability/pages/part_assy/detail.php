<?php
require_once __DIR__ . '/../../includes/config.php';

$partAssy = $_GET['part_assy'] ?? '';
if ($partAssy === '') {
    redirect('pages/part_assy/');
}

// ================================
// Ambil BOM (FIXED pakai part_id)
// ================================
$stmt = $pdo->prepare("
    SELECT 
        pa.part_code,
        pa.qty,
        pa.unit,
        pa.remark,
        p.part_name,
        s.name_supplier
    FROM tbl_part_assy pa
    JOIN tbl_part p ON pa.part_id = p.id_part
    JOIN tbl_supplier s ON p.supplier = s.id_supplier
    WHERE pa.part_assy = :part_assy
");
$stmt->execute([':part_assy' => $partAssy]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================================
// Ambil Part Header
// ================================
$stmt = $pdo->prepare("
    SELECT p.id_part, p.part_code, p.part_name, s.name_supplier
    FROM tbl_part p
    JOIN tbl_supplier s ON p.supplier = s.id_supplier
    WHERE p.part_code = :part_code
    LIMIT 1
");
$stmt->execute([':part_code' => $partAssy]);
$part = $stmt->fetch(PDO::FETCH_ASSOC);

// ================================
// Ambil Model
// ================================
$getModel = $pdo->prepare("
    SELECT id, name 
    FROM tbl_model 
    WHERE part_code = :part
");

$getModel->execute([
    'part' => $partAssy,
]);

$model = $getModel->fetch(PDO::FETCH_ASSOC);

$_SESSION['halaman'] = 'part assy';
$_SESSION['menu'] = 'part_assy';
$_SESSION['subHalaman'] = '| Detail Part Assy';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid pt-0" id="kt_content">
    <div class="container">

        <!-- HEADER -->
        <div class="card mb-7">
            <div class="card-body">
                <h3 class="mb-6">Part Assembly Detail</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="text-muted">Model</div>
                        <div class="font-weight-bolder"><?= htmlspecialchars($model['name']) ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Part Code</div>
                        <div class="font-weight-bolder"><?= htmlspecialchars($part['part_code']) ?></div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted">Part Name</div>
                        <div class="font-weight-bolder"><?= htmlspecialchars($part['part_name']) ?></div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <div class="text-muted">Total Components</div>
                        <div class="display-4 font-weight-bolder text-primary">
                            <?= count($items) ?>
                        </div>
                    </div>

                    <div class="col-md-8 d-flex align-items-end justify-content-end">
                        <a href="<?= BASE_URL ?>pages/part_assy/edit.php?id=<?= urlencode($model['id']) ?>"
                            class="btn btn-warning mr-2">
                            <i class="flaticon-edit"></i> Edit BOM
                        </a>

                        <a href="<?= BASE_URL ?>pages/part_assy/"
                            class="btn btn-outline-secondary">
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">
                <h4 class="mb-5">Assembly Components</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th class="text-center" width="80">Qty</th>
                                <th class="text-center" width="80">Unit</th>
                                <th>Supplier</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($items): ?>
                                <?php $i = 1;
                                foreach ($items as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="font-weight-bolder"><?= $row['part_code'] ?></td>
                                        <td><?= $row['part_name'] ?></td>
                                        <td class="text-center"><?= $row['qty'] ?></td>
                                        <td class="text-center"><?= $row['unit'] ?></td>
                                        <td><?= $row['name_supplier'] ?></td>
                                        <td>
                                            <?= $row['remark'] == 0
                                                ? '<span class="badge badge-success">MAIN</span>'
                                                : '<span class="badge badge-warning">SUBSTITUTE</span>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7"
                                        class="text-center text-muted font-weight-bolder">
                                        No component defined for this assembly
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

<?php require __DIR__ . '/../../includes/footer.php'; ?>