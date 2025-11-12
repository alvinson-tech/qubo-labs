<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$staff_id = $_SESSION['user_id'];
$session_name = $_POST['session_name'] ?? '';
$class_id = $_POST['class_id'] ?? 0;

if (empty($session_name) || empty($class_id)) {
    header('Location: index.php?error=missing_fields');
    exit();
}

// Check if there's already an active session for this class
$active_session = getActiveSession($class_id);
if ($active_session) {
    header('Location: index.php?error=session_exists');
    exit();
}

// Create new session
$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO attendance_sessions (staff_id, class_id, session_name, status) VALUES (?, ?, ?, 'active')");
$stmt->bind_param("iis", $staff_id, $class_id, $session_name);

if ($stmt->execute()) {
    $session_id = $conn->insert_id;
    $stmt->close();
    closeDBConnection($conn);
    
    header('Location: live_view.php?session_id=' . $session_id);
    exit();
} else {
    $stmt->close();
    closeDBConnection($conn);
    header('Location: index.php?error=create_failed');
    exit();
}
?>