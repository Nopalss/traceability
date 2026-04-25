<?php

require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../helper/handlePdoError.php";


// 2. Hanya jalankan jika ada kiriman form POST
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        setAlert('warning', "Username dan password harus diisi", 'Silakan coba lagi!', 'danger', 'Coba Lagi');
        redirect("");
    }

    try {

        $stmt = $pdo->prepare("SELECT * FROM tbl_user WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['rule']     = $user['rule'];


            /**
             * ADMIN langsung dashboard
             */
            if ($user['rule'] !== 'line' || $user['rule'] !== 'operator') {
                redirect("pages/dashboard.php");
            }

            if ($user['rule'] ===  "line") {
                $stmt = $pdo->prepare("
                    SELECT * FROM tbl_line
                    WHERE line_name = :line_name 
                    LIMIT 1
                ");

                $stmt->execute([':line_name' => $user['username']]);
                $line = $stmt->fetch(PDO::FETCH_ASSOC);

                /**
                 * Simpan operator aktif
                 * (tidak ganggu session utama login.php)
                 */
                $_SESSION['line_id'] = $line['line_id'];
            }

            /**
             * OPERATOR & LINE balik ke login page
             * biar SweetAlert jalan
             */
            redirect("");
        } else {

            setAlert('error', "Login Gagal!", 'Username atau Password Salah!', 'danger', 'Coba Lagi');
            redirect("");
        }
    } catch (PDOException $e) {

        handlePdoError($e, "");
    }
}
