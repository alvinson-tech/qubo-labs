<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$staff_id = $_SESSION['user_id'];
$subject_id = $_POST['subject_id'] ?? 0;
$class_id = $_POST['class_id'] ?? 0;
$hall_id = $_POST['hall_id'] ?? 0;
$comments = trim($_POST['comments'] ?? '');

if (empty($subject_id) || empty($class_id) || empty($hall_id)) {
    header('Location: index.php?error=missing_fields');
    exit();
}

// Check if there's already an active session for this class
$active_session = getActiveSession($class_id);
if ($active_session) {
    header('Location: index.php?error=session_exists');
    exit();
}

// Get subject name to use as session name
$subject = getSubjectDetails($subject_id);
if (!$subject) {
    header('Location: index.php?error=invalid_subject');
    exit();
}

$session_name = $subject['subject_name'];

// Create new session with subject
$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO attendance_sessions (staff_id, class_id, hall_id, subject_id, session_name, comments, status) 
                        VALUES (?, ?, ?, ?, ?, ?, 'active')");
$stmt->bind_param("iiiiss", $staff_id, $class_id, $hall_id, $subject_id, $session_name, $comments);

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