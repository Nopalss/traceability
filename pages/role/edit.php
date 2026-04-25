<?php
require_once __DIR__ . '/../../includes/config.php';

$success = false;
$error = false;
$error_message = "";

// ==========================
// GET ROLE ID
// ==========================
$role_id = $_GET['id'] ?? 0;

if (!$role_id) {
    die("Role tidak ditemukan");
}

// ==========================
// GET ROLE DATA
// ==========================
$stmt = $pdo->prepare("SELECT * FROM tbl_role WHERE role_id = ?");
$stmt->execute([$role_id]);
$role = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$role) {
    die("Role tidak ditemukan");
}

// ==========================
// GET SELECTED MENU
// ==========================
$stmt = $pdo->prepare("SELECT menu_id FROM tbl_role_menu WHERE role_id = ?");
$stmt->execute([$role_id]);
$selectedMenus = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ==========================
// HANDLE SUBMIT (UPDATE)
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $role_name = trim($_POST['role_name'] ?? '');
        $menus = $_POST['menus'] ?? [];

        if ($role_name === '') {
            throw new Exception("Role name wajib diisi!");
        }

        // cek duplicate (exclude current)
        $check = $pdo->prepare("
            SELECT COUNT(*) 
            FROM tbl_role 
            WHERE LOWER(role_name) = LOWER(?) 
            AND role_id != ?
        ");
        $check->execute([$role_name, $role_id]);

        if ($check->fetchColumn() > 0) {
            throw new Exception("Role sudah ada!");
        }

        $pdo->beginTransaction();

        // update role
        $stmt = $pdo->prepare("UPDATE tbl_role SET role_name = ? WHERE role_id = ?");
        $stmt->execute([$role_name, $role_id]);

        // delete old menu
        $stmt = $pdo->prepare("DELETE FROM tbl_role_menu WHERE role_id = ?");
        $stmt->execute([$role_id]);

        // insert new menu
        foreach ($menus as $menu_id) {
            $stmt = $pdo->prepare("
                INSERT INTO tbl_role_menu (role_id, menu_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$role_id, $menu_id]);
        }

        $pdo->commit();
        $success = true;

        // refresh selected
        $selectedMenus = $menus;
    } catch (Exception $e) {

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


<style>
    .menu-card {
        border-radius: 14px;
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
    }

    .parent {
        font-weight: 600;
    }

    .child {
        margin-left: 25px;
        margin-top: 8px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Edit Role</h4>
            </div>
        </div>

        <form method="POST">

            <div class="row">

                <!-- LEFT -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <label>Role Name</label>
                            <input type="text" name="role_name"
                                value="<?= $role['role_name'] ?>"
                                class="form-control" required>

                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">

                            <h5>Menu Access</h5>

                            <?php foreach ($grouped as $parent): ?>

                                <div class="menu-card">

                                    <!-- PARENT -->
                                    <label class="parent">
                                        <input type="checkbox"
                                            class="parent-check"
                                            data-id="<?= $parent['menu_id'] ?>"
                                            name="menus[]"
                                            value="<?= $parent['menu_id'] ?>"
                                            <?= in_array($parent['menu_id'], $selectedMenus) ? 'checked' : '' ?>>

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
                                                    value="<?= $child['menu_id'] ?>"
                                                    <?= in_array($child['menu_id'], $selectedMenus) ? 'checked' : '' ?>>

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
                <button class="btn btn-primary px-5">Update Role</button>
            </div>

        </form>

    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    // parent -> child
    document.querySelectorAll('.parent-check').forEach(parent => {
        parent.addEventListener('change', function() {
            let id = this.dataset.id;
            document.querySelectorAll('.child-' + id).forEach(child => {
                child.checked = this.checked;
            });
        });
    });

    // child -> parent
    document.querySelectorAll('.child-check').forEach(child => {
        child.addEventListener('change', function() {

            let parentId = this.dataset.parent;
            let parent = document.querySelector('.parent-check[data-id="' + parentId + '"]');
            let children = document.querySelectorAll('.child-' + parentId);

            parent.checked = Array.from(children).some(c => c.checked);
        });
    });

    // alert
    <?php if ($success): ?>
        Swal.fire('Berhasil!', 'Role berhasil diupdate!', 'success');
    <?php endif; ?>

    <?php if ($error): ?>
        Swal.fire('Gagal!', '<?= $error_message ?>', 'error');
    <?php endif; ?>
</script>