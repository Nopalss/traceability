<?php
require_once __DIR__ . '/../../includes/config.php';

// ==========================
// GET ROLE ID
// ==========================
$role_id = $_GET['id'] ?? 0;

if (!$role_id) {
    die("Role tidak ditemukan");
}

// ==========================
// GET ROLE
// ==========================
$stmt = $pdo->prepare("SELECT * FROM tbl_role WHERE role_id = ?");
$stmt->execute([$role_id]);
$role = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$role) {
    die("Role tidak ditemukan");
}

// ==========================
// GET ROLE MENUS
// ==========================
$stmt = $pdo->prepare("SELECT menu_id FROM tbl_role_menu WHERE role_id = ?");
$stmt->execute([$role_id]);
$roleMenus = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ==========================
// GET ALL MENU
// ==========================
$menus = $pdo->query("SELECT * FROM tbl_menu ORDER BY urutan ASC")->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// GROUP MENU
// ==========================
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
    .role-header {
        border-radius: 16px;
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        padding: 25px;
    }

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

    .badge-access {
        background: #e6f4ea;
        color: #1cc88a;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
        margin-left: 8px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid">
    <div class="container">

        <!-- HEADER -->
        <div class="role-header mb-5 shadow">
            <h3><?= htmlspecialchars($role['role_name']) ?></h3>
            <small>Detail akses menu untuk role ini</small>
        </div>

        <div class="row">

            <!-- LEFT INFO -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5>Informasi Role</h5>
                        <hr>

                        <p><strong>Role ID:</strong> <?= $role['role_id'] ?></p>
                        <p><strong>Nama Role:</strong> <?= htmlspecialchars($role['role_name']) ?></p>

                    </div>
                </div>
            </div>

            <!-- RIGHT MENU -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="mb-3">Menu Access</h5>

                        <?php foreach ($grouped as $parent): ?>

                            <?php
                            $parentChecked = in_array($parent['menu_id'], $roleMenus);
                            $hasChildChecked = false;

                            foreach ($parent['children'] as $child) {
                                if (in_array($child['menu_id'], $roleMenus)) {
                                    $hasChildChecked = true;
                                    break;
                                }
                            }

                            if (!$parentChecked && !$hasChildChecked) continue;
                            ?>

                            <div class="menu-card">

                                <!-- PARENT -->
                                <div class="parent">
                                    <?= $parent['menu_name'] ?>
                                    <span class="badge-access">akses</span>
                                </div>

                                <!-- CHILD -->
                                <?php foreach ($parent['children'] as $child): ?>
                                    <?php if (in_array($child['menu_id'], $roleMenus)): ?>
                                        <div class="child">
                                            • <?= $child['menu_name'] ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>