<?php
require_once __DIR__ . '/../../includes/config.php';

$success = false;
$error = false;
$error_message = "";

// ==========================
// HANDLE SUBMIT
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $role_name = trim($_POST['role_name'] ?? '');
        $selected_menus = $_POST['menus'] ?? [];

        // ==========================
        // VALIDASI KOSONG
        // ==========================
        if ($role_name === '') {
            throw new Exception("Role name wajib diisi!");
        }

        // ==========================
        // CEK DUPLICATE
        // ==========================
        $check = $pdo->prepare("SELECT COUNT(*) FROM tbl_role WHERE LOWER(role_name) = LOWER(?)");
        $check->execute([$role_name]);

        if ($check->fetchColumn() > 0) {
            throw new Exception("Role sudah ada!");
        }

        // ==========================
        // TRANSACTION START
        // ==========================
        $pdo->beginTransaction();

        // insert role
        $stmt = $pdo->prepare("INSERT INTO tbl_role (role_name) VALUES (?)");
        $stmt->execute([$role_name]);

        $role_id = $pdo->lastInsertId();

        // insert menu access
        foreach ($selected_menus as $menu_id) {
            $stmt = $pdo->prepare("
                INSERT INTO tbl_role_menu (role_id, menu_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$role_id, $menu_id]);
        }

        // commit
        $pdo->commit();
        $success = true;
    } catch (Exception $e) {

        // rollback kalau ada error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $error = true;
        $error_message = $e->getMessage();
    }
}

// ==========================
// GET MENU + GROUPING
// ==========================
$menus = $pdo->query("SELECT * FROM tbl_menu ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);

$grouped = [];
foreach ($menus as $m) {
    if (empty($m['parent_id'])) {
        $grouped[$m['menu_id']] = $m;
        $grouped[$m['menu_id']]['children'] = [];
    }
}

foreach ($menus as $m) {
    if (!empty($m['parent_id']) && isset($grouped[$m['parent_id']])) {
        $grouped[$m['parent_id']]['children'][] = $m;
    }
}

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<!-- SWEET ALERT -->

<style>
    .menu-card {
        border-radius: 14px;
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        transition: 0.2s;
    }

    .menu-card:hover {
        background: #f9fafb;
    }

    .parent {
        font-weight: 600;
        font-size: 15px;
    }

    .child {
        margin-left: 25px;
        margin-top: 8px;
        font-size: 14px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Create Role</h4>
                <small>Assign menu access</small>
            </div>
        </div>

        <form method="POST">

            <div class="row">

                <!-- LEFT -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="role_name" class="form-control" required>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h5 class="mb-3">Menu Access</h5>

                            <?php foreach ($grouped as $parent): ?>

                                <div class="menu-card">

                                    <!-- PARENT -->
                                    <label class="parent">
                                        <input type="checkbox"
                                            class="parent-check"
                                            data-id="<?= $parent['menu_id'] ?>"
                                            name="menus[]"
                                            value="<?= $parent['menu_id'] ?>">

                                        <?= $parent['menu_name'] ?>
                                    </label>

                                    <!-- CHILD -->
                                    <?php foreach ($parent['children'] as $child): ?>
                                        <div class="child">
                                            <label>
                                                <input type="checkbox"
                                                    class="child-check child-<?= $parent['menu_id'] ?>"
                                                    data-parent="<?= $parent['menu_id'] ?>"
                                                    name="menus[]"
                                                    value="<?= $child['menu_id'] ?>">

                                                <?= $child['menu_name'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                </div>

                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-4 text-right">
                <button class="btn btn-primary px-5">Save Role</button>
            </div>

        </form>

    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    // ==========================
    // PARENT -> CHILD
    // ==========================
    document.querySelectorAll('.parent-check').forEach(parent => {
        parent.addEventListener('change', function() {
            let id = this.dataset.id;
            document.querySelectorAll('.child-' + id).forEach(child => {
                child.checked = this.checked;
            });
        });
    });

    // ==========================
    // CHILD -> PARENT
    // ==========================
    document.querySelectorAll('.child-check').forEach(child => {
        child.addEventListener('change', function() {

            let parentId = this.dataset.parent;
            let parent = document.querySelector('.parent-check[data-id="' + parentId + '"]');
            let children = document.querySelectorAll('.child-' + parentId);

            let anyChecked = false;
            children.forEach(c => {
                if (c.checked) anyChecked = true;
            });

            parent.checked = anyChecked;
        });
    });

    // ==========================
    // SWEET ALERT
    // ==========================
    <?php if ($success): ?>
        Swal.fire(
            'Berhasil!',
            'Role berhasil dibuat!',
            'success'
        );
    <?php endif; ?>

    <?php if ($error): ?>
        Swal.fire(
            'Gagal!',
            '<?= $error_message ?>',
            'error'
        );
    <?php endif; ?>
</script>