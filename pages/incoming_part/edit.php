<?php

require_once __DIR__ . '/../../includes/config.php';
require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';

/* ==============================
   GET DATA
============================== */
$ref_number = $_GET['ref_number'] ?? null;

if (!$ref_number) {
    die("Ref number tidak ditemukan");
}

/* ==============================
   HANDLE UPDATE
============================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $part_code     = $_POST['part_code'];
    $qty           = $_POST['qty'];
    $remain        = $_POST['remain'];
    $status        = $_POST['status'];
    $lot_no        = $_POST['lot_no'];
    $remarks       = $_POST['remarks'];

    $update = $pdo->prepare("
        UPDATE tbl_detail_part 
        SET 
            part_code = ?,
            qty = ?,
            remain = ?,
            status = ?,
            lot_no = ?,
            remarks = ?
        WHERE ref_number = ?
    ");

    $update->execute([
        $part_code,
        $qty,
        $remain,
        $status,
        $lot_no,
        $remarks,
        $ref_number
    ]);

    echo "<script>
        alert('Data berhasil diupdate');
        window.location.href = 'index.php';
    </script>";
    exit;
}

/* ==============================
   GET DETAIL DATA
============================== */
$q = $pdo->prepare("
    SELECT * FROM tbl_detail_part
    WHERE ref_number = ?
");
$q->execute([$ref_number]);
$data = $q->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan");
}

?>

<div class="content d-flex flex-column flex-column-fluid pt-0">
    <div class="container">

        <div class="card shadow-lg border-0">

            <!-- HEADER -->
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="card-title mb-0">Edit Incoming Part</h3>
            </div>

            <!-- BODY -->
            <div class="card-body">

                <form method="POST">

                    <div class="row">

                        <!-- PART CODE -->
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold">Part Code</label>
                            <input type="text" name="part_code"
                                class="form-control form-control-lg"
                                value="<?= $data['part_code'] ?>" required>
                        </div>

                        <!-- LOT -->
                        <div class="col-md-6 mb-4">
                            <label class="font-weight-bold">Lot No</label>
                            <input type="text" name="lot_no"
                                class="form-control form-control-lg"
                                value="<?= $data['lot_no'] ?>" required>
                        </div>

                        <!-- QTY -->
                        <div class="col-md-4 mb-4">
                            <label class="font-weight-bold">Quantity</label>
                            <input type="number" name="qty"
                                class="form-control form-control-lg"
                                value="<?= $data['qty'] ?>" required>
                        </div>

                        <!-- REMAIN -->
                        <div class="col-md-4 mb-4">
                            <label class="font-weight-bold">Remain</label>
                            <input type="number" name="remain"
                                class="form-control form-control-lg"
                                value="<?= $data['remain'] ?>" required>
                        </div>

                        <!-- STATUS -->
                        <div class="col-md-4 mb-4">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control form-control-lg">
                                <option value="IN" <?= $data['status'] == 'IN' ? 'selected' : '' ?>>IN</option>
                                <option value="USED" <?= $data['status'] == 'USED' ? 'selected' : '' ?>>USED</option>
                            </select>
                        </div>

                        <!-- REMARKS -->
                        <div class="col-md-12 mb-4">
                            <label class="font-weight-bold">Remarks</label>
                            <textarea name="remarks"
                                class="form-control form-control-lg"
                                rows="3"><?= $data['remarks'] ?></textarea>
                        </div>

                    </div>

                    <!-- ACTION -->
                    <div class="d-flex justify-content-between mt-5">

                        <a href="index.php" class="btn btn-light-primary btn-lg">
                            ← Back
                        </a>

                        <button type="submit" class="btn btn-primary btn-lg shadow">
                            💾 Save Changes
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>