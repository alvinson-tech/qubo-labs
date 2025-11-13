<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
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

$conn = getDBConnection();

// Begin transaction
$conn->begin_transaction();

try {
    // Delete all attendance records for this session
    $stmt = $conn->prepare("DELETE FROM attendance_records WHERE session_id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $stmt->close();
    
    // Delete the session itself
    $stmt = $conn->prepare("DELETE FROM attendance_sessions WHERE session_id = ?");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    closeDBConnection($conn);
    
    echo json_encode(['success' => true, 'message' => 'Session cancelled successfully']);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to cancel session: ' . $e->getMessage()]);
}
?>