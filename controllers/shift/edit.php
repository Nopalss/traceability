<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

try {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception('Invalid request');
    }

    $shift_id = intval($data['shift_id'] ?? 0);
    $shift = strtoupper(trim($data['shift'] ?? ''));
    $start = intval($data['start'] ?? -1);
    $end   = intval($data['end'] ?? -1);

    // =====================
    // BASIC VALIDATION
    // =====================
    if ($shift_id <= 0) {
        throw new Exception('Shift ID tidak valid');
    }

    if ($shift === '') {
        throw new Exception('Nama shift wajib diisi');
    }

    if ($start < 0 || $start > 23 || $end < 0 || $end > 23) {
        throw new Exception('Jam tidak valid');
    }

    // =====================
    // CEK EXIST SHIFT
    // =====================
    $exist = $pdo->prepare("SELECT shift_id FROM tbl_shift WHERE shift_id = ?");
    $exist->execute([$shift_id]);

    if (!$exist->rowCount()) {
        throw new Exception('Data shift tidak ditemukan');
    }

    // =====================
    // PREVENT DUPLICATE NAME
    // =====================
    $check = $pdo->prepare("
        SELECT shift_id 
        FROM tbl_shift 
        WHERE shift = ? AND shift_id != ?
    ");

    $check->execute([$shift, $shift_id]);

    if ($check->rowCount()) {
        throw new Exception('Nama shift sudah digunakan!');
    }

    // =====================
    // UPDATE
    // =====================
    $stmt = $pdo->prepare("
        UPDATE tbl_shift
        SET shift = ?, start = ?, end = ?
        WHERE shift_id = ?
    ");

    $stmt->execute([$shift, $start, $end, $shift_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Shift berhasil diupdate'
    ]);
} catch (Exception $e) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
