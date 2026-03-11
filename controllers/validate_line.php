<?php

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (empty($username) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Username dan password operator wajib diisi.'
    ]);
    exit;
}

try {

    // Cari user operator
    $stmt = $pdo->prepare("
        SELECT * FROM tbl_user 
        WHERE username = :username 
        AND rule = 'line'
        LIMIT 1
    ");

    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {

        echo json_encode([
            'success' => false,
            'message' => 'Akun Line tidak valid.'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT * FROM tbl_line
        WHERE line_name = :line_name 
        LIMIT 1
    ");

    $stmt->execute([':line_name' => $username]);
    $line = $stmt->fetch(PDO::FETCH_ASSOC);

    /**
     * Simpan operator aktif
     * (tidak ganggu session utama login.php)
     */
    $_SESSION['line_id'] = $line['line_id'];
    $_SESSION['active_line'] = $user['username'];

    echo json_encode([
        'success' => true
    ]);
    exit;
} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server.'
    ]);
    exit;
}
