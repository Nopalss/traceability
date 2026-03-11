<?php
require __DIR__ . "/controllers/login.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Traceability | Login</title>

    <!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet"> -->

    <link href="<?= BASE_URL ?>assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= BASE_URL ?>assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />

    <link href="<?= BASE_URL ?>assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            font-family: Inter, sans-serif;
        }

        .login-card {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, .08);
            border-radius: 20px;
            padding: 40px;
            width: 380px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .4);
        }

        input {
            border-radius: 12px !important;
        }

        .login-btn {
            border-radius: 12px;
            padding: 12px;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="login-card text-center text-white">

        <h2 class="mb-2">Traceability</h2>
        <p class="text-muted mb-5">Manufacturing Login System</p>

        <form method="post">

            <input class="form-control mb-4" name="username" placeholder="Username" required>

            <input type="password" class="form-control mb-4" name="password" placeholder="Password" required>

            <button name="login" class="btn btn-primary w-100 login-btn">Login</button>

        </form>
    </div>
    <script src="<?= BASE_URL ?>assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?= BASE_URL ?>assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
    <script src="<?= BASE_URL ?>assets/js/scripts.bundle.js"></script>
    <script src="<?= BASE_URL ?>assets/js/pages/features/miscellaneous/sweetalert2.js"></script>

    <?php
    if (isset($_SESSION['username'], $_SESSION['rule'])):

        // OPERATOR → LINE
        if ($_SESSION['rule'] === 'operator'):
    ?>

            <script>
                Swal.fire({
                    title: 'Login Line',
                    html: '<input id="line_user" class="swal2-input" placeholder="Line Username">' +
                        '<input id="line_pass" type="password" class="swal2-input" placeholder="Password">',
                    confirmButtonText: 'Login',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    allowOutsideClick: false,
                    focusConfirm: false,
                    preConfirm: () => {

                        const username = document.getElementById('line_user').value.trim();
                        const password = document.getElementById('line_pass').value.trim();

                        if (!username || !password) {
                            Swal.showValidationMessage('Username dan password wajib diisi');
                            return false;
                        }

                        return fetch('controllers/validate_line.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    username,
                                    password
                                })
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (!d.success) {
                                    Swal.showValidationMessage(d.message);
                                    return false;
                                }
                                return true;
                            })
                            .catch(() => {
                                Swal.showValidationMessage('Server error');
                                return false;
                            });

                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'pages/operator/';
                    } else {
                        window.location = 'logout.php';
                    }
                });
            </script>

        <?php
        // LINE → OPERATOR
        elseif ($_SESSION['rule'] === 'line'):
        ?>

            <script>
                Swal.fire({
                    title: 'Login Operator',
                    html: '<input id="op_user" class="swal2-input" placeholder="Operator Username">' +
                        '<input id="op_pass" type="password" class="swal2-input" placeholder="Password">',
                    confirmButtonText: 'Login',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    allowOutsideClick: false,
                    focusConfirm: false,
                    preConfirm: () => {

                        const username = document.getElementById('op_user').value.trim();
                        const password = document.getElementById('op_pass').value.trim();

                        if (!username || !password) {
                            Swal.showValidationMessage('Username dan password wajib diisi');
                            return false;
                        }

                        return fetch('controllers/validate_operator.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    username,
                                    password
                                })
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (!d.success) {
                                    Swal.showValidationMessage(d.message);
                                    return false;
                                }
                                return true;
                            })
                            .catch(() => {
                                Swal.showValidationMessage('Server error');
                                return false;
                            });

                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = 'pages/operator/';
                    } else {
                        window.location = 'controllers/logout.php';
                    }
                });
            </script>

    <?php
        // ADMIN
        elseif ($_SESSION['rule'] === 'admin'):
            echo "<script>location='pages/dashboard.php'</script>";
        endif;

    endif;
    ?>

    <?php if (isset($_SESSION['alert'])): ?>
        <script>
            Swal.fire({
                icon: "<?= $_SESSION['alert']['icon'] ?>",
                title: "<?= $_SESSION['alert']['title'] ?>",
                text: "<?= $_SESSION['alert']['text'] ?>",
                heightAuto: false,
                position: 'center'
            });
        </script>
    <?php unset($_SESSION['alert']);
    endif; ?>
</body>

</html>