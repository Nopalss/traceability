<?php
require_once __DIR__ . '/config.php';

$user_id = $_SESSION['user_id'] ?? 0;
$currentMenu = $_SESSION['menu'] ?? '';

// ==========================
// GET MENU FROM DATABASE
// ==========================
$query = "
SELECT m.*
FROM tbl_menu m
JOIN tbl_role_menu rm ON m.menu_id = rm.menu_id
JOIN tbl_user u ON u.role_id = rm.role_id
WHERE u.user_id = ?
AND m.is_active = 1
ORDER BY m.parent_id ASC, m.urutan ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$menuss = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// BUILD TREE MENU
// ==========================
$menuTree = [];
foreach ($menuss as $menu) {
    if ($menu['parent_id'] == NULL) {
        $menuTree[$menu['menu_id']] = $menu;
        $menuTree[$menu['menu_id']]['children'] = [];
    }
}

foreach ($menuss as $menu) {
    if ($menu['parent_id'] != NULL) {
        $menuTree[$menu['parent_id']]['children'][] = $menu;
    }
}

// ==========================
// HELPER ACTIVE
// ==========================
function isActive($currentMenu, $menuKey)
{
    return $currentMenu == $menuKey ? 'menu-item-active' : '';
}

function isOpen($currentMenu, $children)
{
    foreach ($children as $child) {
        if ($currentMenu == $child['menu_key']) {
            return 'menu-item-open';
        }
    }
    return '';
}
?>

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading aside-minimize">

    <!--begin::Main-->
    <!--begin::Header Mobile-->
    <div id="kt_header_mobile" class="header-mobile align-items-center  header-mobile-fixed ">


        <!--begin::Toolbar-->
        <div class="d-flex align-items-center">
            <!--begin::Aside Mobile Toggle-->
            <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
                <span></span>
            </button>
            <!--end::Aside Mobile Toggle-->

            <!--begin::Header Menu Mobile Toggle-->
            <button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
                <span></span>
            </button>
            <!--end::Header Menu Mobile Toggle-->

            <!--begin::Topbar Mobile Toggle-->
            <button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
                <span class="svg-icon svg-icon-xl"><!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon points="0 0 24 0 24 24 0 24" />
                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                        </g>
                    </svg><!--end::Svg Icon--></span> </button>
            <!--end::Topbar Mobile Toggle-->
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header Mobile-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">

            <div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">

                <div class="brand flex-column-auto " id="kt_brand">
                    <!--begin::Logo-->
                    <a href="" class="brand-logo">
                        <!-- <img alt="Logo" src="<?= BASE_URL ?>assets/media/logos/logo.png" class="max-w-90px" /> -->
                    </a>
                    <!--end::Logo-->

                    <!--begin::Toggle-->
                    <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                        <span class="svg-icon svg-icon svg-icon-xl"><!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Angle-double-left.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                    <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                                </g>
                            </svg><!--end::Svg Icon--></span> </button>
                    <!--end::Toolbar-->
                </div>

                <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

                    <!--begin::Menu Container-->
                    <div
                        id="kt_aside_menu"
                        class="aside-menu my-4 d-flex flex-column justify-content-between"
                        data-menu-vertical="1"
                        data-menu-scroll="1" data-menu-dropdown-timeout="500">
                        <!--begin::Menu Nav-->
                        <ul class="menu-nav ">

                            <?php foreach ($menuTree as $menu): ?>

                                <?php if (!empty($menu['children'])): ?>
                                    <!-- PARENT MENU -->

                                    <li class="menu-item menu-item-submenu <?= isOpen($currentMenu, $menu['children']) ?>" data-menu-toggle="hover">

                                        <a href="javascript:;" class="menu-link menu-toggle">
                                            <?= $menu['menu_icon'] ?>
                                            <span class="menu-text"><?= $menu['menu_name'] ?></span>
                                            <i class="menu-arrow"></i>
                                        </a>

                                        <div class="menu-submenu">
                                            <ul class="menu-subnav">

                                                <?php foreach ($menu['children'] as $child): ?>

                                                    <li class="menu-item <?= isActive($currentMenu, $child['menu_key']) ?>">
                                                        <a href="<?= BASE_URL . $child['menu_url'] ?>" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot"><span></span></i>
                                                            <span class="menu-text"><?= $child['menu_name'] ?></span>
                                                        </a>
                                                    </li>

                                                <?php endforeach; ?>

                                            </ul>
                                        </div>

                                    </li>

                                <?php else: ?>
                                    <!-- SINGLE MENU -->

                                    <li class="menu-item <?= isActive($currentMenu, $menu['menu_key']) ?>">
                                        <a href="<?= BASE_URL . $menu['menu_url'] ?>" class="menu-link">
                                            <?= $menu['menu_icon'] ?>

                                            <span class="menu-text"><?= $menu['menu_name'] ?></span>
                                        </a>
                                    </li>

                                <?php endif; ?>

                            <?php endforeach; ?>

                            <!-- LOGOUT -->
                            <li class="menu-item">
                                <a onclick="logoutConfirm()" class="menu-link ">
                                    <span class="svg-icon menu-icon svg-icon-danger "><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\themes\metronic\theme\html\demo1\dist/../src/media/svg/icons\Communication\Right.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path d="M12.6572352,10 L12.6572352,5.67013288 C12.6572352,5.25591932 12.3214488,4.92013288 11.9072352,4.92013288 C11.7235496,4.92013288 11.5462507,4.98754181 11.4089624,5.10957589 L4.25173515,11.4715556 C3.94214808,11.7467441 3.91426253,12.2207984 4.18945104,12.5303855 C4.19921056,12.541365 4.20929054,12.5520553 4.21967795,12.5624427 L11.3769052,19.7196699 C11.6697984,20.0125631 12.1446721,20.0125631 12.4375653,19.7196699 C12.5782176,19.5790176 12.6572352,19.3882522 12.6572352,19.1893398 L12.6572352,15 C14.0044226,14.9188289 16.8348635,14.9157978 21.1485581,14.9909069 L21.1485586,14.9908794 C21.424644,14.9956866 21.6523523,14.7757721 21.6571595,14.4996868 C21.65721,14.4967857 21.6572352,14.4938842 21.6572352,14.4909827 L21.6572888,10.5050185 C21.6572888,10.2288465 21.4334072,10.0049649 21.1572352,10.0049649 C21.1556184,10.0049649 21.1540016,10.0049728 21.1523849,10.0049884 C16.0216074,10.0547574 13.1898909,10.0530946 12.6572352,10 Z" fill="#000000" fill-rule="nonzero" />
                                            </g>
                                        </svg><!--end::Svg Icon--></span>
                                    <span class="menu-text text-danger">Sign Out</span>
                                </a>
                            </li>

                        </ul>

                        <!-- FOOTER -->
                        <div class="aside-footer d-flex flex-column align-items-center py-5">
                            <div class="text-muted fs-8 text-center">
                                <div><strong>Version:</strong> <?= VERSION ?>-development</div>
                                <div>PT. Surya Technology Industri</div>
                                <div>&copy;<?= date('Y') ?></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>