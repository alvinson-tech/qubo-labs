<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
$present_students = $data['present_students'] ?? [];
$staff_id = $_SESSION['user_id'];

// Verify session belongs to this staff
$session = getSessionDetails($session_id);
if (!$session || $session['staff_id'] != $staff_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit();
}

if ($session['status'] !== 'active') {
    echo json_encode(['success' => false, 'message' => 'Session is not active']);
    exit();
}

// Verify it's a manual session (no hall_id)
if ($session['hall_id']) {
    echo json_encode(['success' => false, 'message' => 'This is not a manual entry session']);
    exit();
}

$conn = getDBConnection();

// Begin transaction
$conn->begin_transaction();

try {
    // Delete all existing records for this session
    $stmt = $conn->prepare("DELETE FROM attendance_records WHERE session_id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $stmt->close();
    
    // Insert new records for present students
    if (!empty($present_students)) {
        $stmt = $conn->prepare("INSERT INTO attendance_records (session_id, student_id, seat_id, verification_code, status, verified_at, scanned_at) 
                                VALUES (?, ?, 0, '0000', 'verified', NOW(), NOW())");
        
        foreach ($present_students as $student_id) {
            $stmt->bind_param("ii", $session_id, $student_id);
            $stmt->execute();
        }
        $stmt->close();
    }
    
    // End the session
    $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'ended', end_time = NOW() WHERE session_id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    closeDBConnection($conn);
    
    echo json_encode(['success' => true, 'message' => 'Attendance recorded successfully']);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to record attendance: ' . $e->getMessage()]);
}
?>