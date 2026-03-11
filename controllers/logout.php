<?php

require_once __DIR__ . '/../includes/config.php';

// Hapus semua session
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect balik ke login page
header("Location: " . BASE_URL);
exit;
