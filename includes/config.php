<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "127.0.0.1"; // 👈 Gunakan IP langsung, bukan "localhost"
$user = "root";
$pass = "";
$db = "traceability_db";
$charset = "utf8mb4";

// Timezone & Base URL
date_default_timezone_set('Asia/Jakarta');
$http_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', "http://{$http_host}/traceability/");
define('VERSION', 'V1.0.0');

// --- PDO CONFIG ---
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Lempar error, bukan silent fail
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return array as assoc
    PDO::ATTR_PERSISTENT         => true,                    // 👈 Persistent connection (wajib buat Redis worker)
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"      // Pastikan charset benar
];

function formatTanggal($date)
{
    if (!$date) return '-';

    try {
        return (new DateTime($date))->format('d F Y');
    } catch (Exception $e) {
        return '-';
    }
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] DB connect error: " . $e->getMessage() . PHP_EOL, 3, __DIR__ . '/../logs/db_error.log');
    die("Koneksi ke database gagal: " . $e->getMessage());
}
