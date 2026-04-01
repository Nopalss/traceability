<?php

require_once __DIR__ . '/../../includes/config.php';

header('Content-Type: application/json');

try {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception('Invalid request');
    }

    // =====================
    // GET DATA
    // =====================
    $shift_id = intval($data['shift_id'] ?? 0);
    $shift = strtoupper(trim($data['shift'] ?? ''));

    // tetap jam
    $start = intval($data['start'] ?? -1);
    $end   = intval($data['end'] ?? -1);

    // sekarang menit
    $time_coffe    = isset($data['time_coffe']) ? intval($data['time_coffe']) : null;
    $duration_time = isset($data['duration_time']) ? intval($data['duration_time']) : 0;

    $break_makan   = isset($data['break_makan']) ? intval($data['break_makan']) : null;
    $duration_bm   = isset($data['duration_bm']) ? intval($data['duration_bm']) : 0;

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
        throw new Exception('Jam start/end tidak valid');
    }

    // convert shift ke menit
    $start_minutes = $start * 60;
    $end_minutes   = $end * 60;

    // coffee harus di dalam shift
    if ($time_coffe !== null && ($time_coffe < $start_minutes || $time_coffe > $end_minutes)) {
        throw new Exception('Coffee break harus di dalam jam shift');
    }

    // =====================
    // VALIDASI BREAK (MENIT)
    // =====================
    if ($time_coffe !== null && ($time_coffe < 0 || $time_coffe > 1439)) {
        throw new Exception('Jam coffee break tidak valid');
    }

    if ($break_makan !== null && ($break_makan < 0 || $break_makan > 1439)) {
        throw new Exception('Jam istirahat makan tidak valid');
    }

    if ($duration_time < 0) {
        throw new Exception('Durasi coffee tidak boleh minus');
    }

    if ($duration_bm < 0) {
        throw new Exception('Durasi makan tidak boleh minus');
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
        SET 
            shift = ?, 
            start = ?, 
            end = ?, 
            time_coffe = ?, 
            duration_time = ?, 
            break_makan = ?, 
            duration_bm = ?
        WHERE shift_id = ?
    ");

    $stmt->execute([
        $shift,
        $start,
        $end,
        $time_coffe,
        $duration_time,
        $break_makan,
        $duration_bm,
        $shift_id
    ]);

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
