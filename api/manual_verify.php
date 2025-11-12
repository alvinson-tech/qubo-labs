<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
$student_id = $data['student_id'] ?? 0;
$action = $data['action'] ?? ''; // 'approve' or 'reject'
$staff_id = $_SESSION['user_id'];

// Validate session belongs to this staff
$session = getSessionDetails($session_id);
if (!$session || $session['staff_id'] != $staff_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit();
}

// Validate action
if ($action !== 'approve' && $action !== 'reject') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

// Get student's attendance record
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM attendance_records WHERE session_id = ? AND student_id = ?");
$stmt->bind_param("ii", $session_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$stmt->close();

if (!$record) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Attendance record not found']);
    exit();
}

// Check if student has "no_neighbours" marked
if (!$record['no_neighbours']) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'This student can only be verified through neighbor verification']);
    exit();
}

// Check if already verified
if ($record['status'] === 'verified') {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'This student is already verified']);
    exit();
}

if ($action === 'approve') {
    // Approve: Mark as verified with staff_id as verifier
    $stmt = $conn->prepare("UPDATE attendance_records 
                           SET status = 'verified', 
                               verified_at = NOW(), 
                               verified_by_student_id = NULL 
                           WHERE record_id = ?");
    $stmt->bind_param("i", $record['record_id']);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => true, 'message' => 'Student attendance approved successfully']);
    } else {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Failed to approve attendance']);
    }
} else {
    // Reject: Delete the attendance record
    $stmt = $conn->prepare("DELETE FROM attendance_records WHERE record_id = ?");
    $stmt->bind_param("i", $record['record_id']);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => true, 'message' => 'Student attendance rejected successfully']);
    } else {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Failed to reject attendance']);
    }
}
?>