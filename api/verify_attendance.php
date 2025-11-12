<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStudent();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
$neighbor_code = $data['neighbor_code'] ?? null;
$no_neighbours = $data['no_neighbours'] ?? false;
$student_id = $_SESSION['user_id'];
$class_id = $_SESSION['class_id'];

// Validate session
$active_session = getActiveSession($class_id);
if (!$active_session || $active_session['session_id'] != $session_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid or inactive session']);
    exit();
}

// Get student's attendance record
$my_record = getStudentAttendanceRecord($session_id, $student_id);
if (!$my_record) {
    echo json_encode(['success' => false, 'message' => 'You have not scanned a QR code yet']);
    exit();
}

if ($my_record['status'] === 'verified') {
    echo json_encode(['success' => false, 'message' => 'Your attendance is already verified']);
    exit();
}

$conn = getDBConnection();

// Handle "No Neighbours" case
if ($no_neighbours) {
    // Keep status as 'scanned' but mark no_neighbours as true
    $stmt = $conn->prepare("UPDATE attendance_records SET no_neighbours = TRUE WHERE record_id = ?");
    $stmt->bind_param("i", $my_record['record_id']);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => true, 'message' => 'Attendance marked successfully']);
    } else {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Failed to mark attendance']);
    }
    exit();
}

// Verify neighbor's code
if (empty($neighbor_code)) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Please enter your neighbor\'s code']);
    exit();
}

// FIXED: Get current student's seat information using seminar_seats
$stmt = $conn->prepare("SELECT s.row_number, s.seat_position FROM attendance_records ar 
                        JOIN seminar_seats s ON ar.seat_id = s.seat_id 
                        WHERE ar.record_id = ?");
$stmt->bind_param("i", $my_record['record_id']);
$stmt->execute();
$result = $stmt->get_result();
$my_seat = $result->fetch_assoc();
$stmt->close();

if (!$my_seat) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Could not find your seat information']);
    exit();
}

// FIXED: Find the neighbor with this verification code in the same row using seminar_seats
$stmt = $conn->prepare("SELECT ar.*, s.row_number, s.seat_position 
                        FROM attendance_records ar
                        JOIN seminar_seats s ON ar.seat_id = s.seat_id
                        WHERE ar.session_id = ? AND ar.verification_code = ? AND ar.student_id != ?");
$stmt->bind_param("isi", $session_id, $neighbor_code, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$neighbor_record = $result->fetch_assoc();
$stmt->close();

if (!$neighbor_record) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please check and try again.']);
    exit();
}

// Check if neighbor is in the same row
if ($neighbor_record['row_number'] != $my_seat['row_number']) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'You can only verify with neighbors in the same row.']);
    exit();
}

// Check if neighbor is immediately left or right (seat_position ± 1)
$position_diff = abs($neighbor_record['seat_position'] - $my_seat['seat_position']);
if ($position_diff != 1) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'You can only verify with your immediate left or right neighbor.']);
    exit();
}

// Update current student's record
$stmt = $conn->prepare("UPDATE attendance_records SET status = 'verified', verified_at = NOW(), verified_by_student_id = ? WHERE record_id = ?");
$stmt->bind_param("ii", $neighbor_record['student_id'], $my_record['record_id']);
$success1 = $stmt->execute();
$stmt->close();

// Also mark the neighbor as verified (mutual verification)
$stmt = $conn->prepare("UPDATE attendance_records SET status = 'verified', verified_at = NOW(), verified_by_student_id = ? WHERE record_id = ?");
$stmt->bind_param("ii", $student_id, $neighbor_record['record_id']);
$success2 = $stmt->execute();
$stmt->close();

closeDBConnection($conn);

if ($success1 && $success2) {
    echo json_encode(['success' => true, 'message' => 'Attendance verified successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to verify attendance']);
}
?>