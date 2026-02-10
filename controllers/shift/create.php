<?php

require_once __DIR__ . '/../../includes/config.php'; // (Menyediakan $pdo dan memulai session)

header('Content-Type: application/json');

try {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception('Invalid request');
    }

    $shift = strtoupper(trim($data['shift'] ?? ''));
    $start = intval($data['start'] ?? -1);
    $end   = intval($data['end'] ?? -1);

    // =====================
    // BASIC VALIDATION
    // =====================
    if ($shift === '') {
        throw new Exception('Nama shift wajib diisi');
    }

    if ($start < 0 || $start > 23 || $end < 0 || $end > 23) {
        throw new Exception('Jam tidak valid');
    }

    // =====================
    // PREVENT DUPLICATE SHIFT
    // =====================
    $check = $pdo->prepare("SELECT shift_id FROM tbl_shift WHERE shift = ?");
    $check->execute([$shift]);

    if ($check->rowCount()) {
        throw new Exception('Shift sudah ada!');
    }

    // =====================
    // INSERT
    // =====================
    $stmt = $pdo->prepare("
        INSERT INTO tbl_shift (shift, start, end)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([$shift, $start, $end]);

    echo json_encode([
        'success' => true,
        'message' => 'Shift berhasil ditambahkan'
    ]);
} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
