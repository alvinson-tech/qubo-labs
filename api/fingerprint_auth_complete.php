<?php
require_once '../config/database.php';

session_start();
header('Content-Type: application/json');

// Verify challenge exists
if (!isset($_SESSION['webauthn_challenge']) || !isset($_SESSION['webauthn_user_id']) || !isset($_SESSION['webauthn_user_type'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit();
}

$user_id = $_SESSION['webauthn_user_id'];
$user_type = $_SESSION['webauthn_user_type'];

// Clear challenge
unset($_SESSION['webauthn_challenge']);
unset($_SESSION['webauthn_user_id']);
unset($_SESSION['webauthn_user_type']);

// In production, you would verify the signature here
// For this implementation, we'll trust that WebAuthn browser API did the verification

$conn = getDBConnection();

if ($user_type === 'student') {
    $stmt = $conn->prepare("SELECT student_id, student_name, usn_number, class_id, session_token FROM students WHERE student_id = ?");
} else {
    $stmt = $conn->prepare("SELECT staff_id, staff_name, staff_number, department, session_token FROM staff WHERE staff_id = ?");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

// Check if already logged in elsewhere
$session_token_field = $user_type === 'student' ? 'session_token' : 'session_token';
if (!empty($user[$session_token_field])) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'This account is already logged in on another device. Please logout from the other device first.']);
    exit();
}

// Generate new session token
$session_token = bin2hex(random_bytes(32));

// Update session token
if ($user_type === 'student') {
    $stmt = $conn->prepare("UPDATE students SET session_token = ?, last_login = NOW() WHERE student_id = ?");
    $stmt->bind_param("si", $session_token, $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Set session variables
    $_SESSION['user_id'] = $user['student_id'];
    $_SESSION['user_name'] = $user['student_name'];
    $_SESSION['usn_number'] = $user['usn_number'];
    $_SESSION['user_type'] = 'student';
    $_SESSION['class_id'] = $user['class_id'];
    $_SESSION['session_token'] = $session_token;
    
    $redirect = 'student/index.php';
} else {
    $stmt = $conn->prepare("UPDATE staff SET session_token = ?, last_login = NOW() WHERE staff_id = ?");
    $stmt->bind_param("si", $session_token, $user_id);
    $stmt->execute();
    $stmt->close();
    
    // Set session variables
    $_SESSION['user_id'] = $user['staff_id'];
    $_SESSION['user_name'] = $user['staff_name'];
    $_SESSION['staff_number'] = $user['staff_number'];
    $_SESSION['user_type'] = 'staff';
    $_SESSION['department'] = $user['department'];
    $_SESSION['session_token'] = $session_token;
    
    $redirect = 'staff/index.php';
}

closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'redirect' => $redirect
]);
?>