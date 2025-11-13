<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$staff_id = $_SESSION['user_id'];
$subject_id = intval($_POST['subject_id'] ?? 0);
$class_id = intval($_POST['class_id'] ?? 0);
$comments = trim($_POST['comments'] ?? '');

if (empty($subject_id) || empty($class_id)) {
    header('Location: index.php?error=missing_fields');
    exit();
}

// Get subject name
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();
$subject = $result->fetch_assoc();
$stmt->close();

if (!$subject) {
    closeDBConnection($conn);
    header('Location: index.php?error=invalid_subject');
    exit();
}

$session_name = $subject['subject_name'];

// Create manual session (hall_id is NULL for manual entry)
$stmt = $conn->prepare("INSERT INTO attendance_sessions (staff_id, class_id, hall_id, subject_id, session_name, comments, status) 
                        VALUES (?, ?, NULL, ?, ?, ?, 'active')");
$stmt->bind_param("iiiss", $staff_id, $class_id, $subject_id, $session_name, $comments);

if ($stmt->execute()) {
    $session_id = $conn->insert_id;
    $stmt->close();
    closeDBConnection($conn);
    header('Location: mark_manual_attendance.php?session_id=' . $session_id);
    exit();
} else {
    $error = $stmt->error;
    $stmt->close();
    closeDBConnection($conn);
    die("Error creating session: " . htmlspecialchars($error));
}
?>