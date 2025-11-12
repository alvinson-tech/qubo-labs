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

// End the session
$conn = getDBConnection();
$stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'ended', end_time = NOW() WHERE session_id = ?");
$stmt->bind_param("i", $session_id);

if ($stmt->execute()) {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => true]);
} else {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to end session']);
}
?>