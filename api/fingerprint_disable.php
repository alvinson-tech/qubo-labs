<?php
require_once '../includes/session.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

$conn = getDBConnection();

if ($user_type === 'student') {
    $stmt = $conn->prepare("UPDATE students SET webauthn_credential_id = NULL, webauthn_public_key = NULL, fingerprint_enabled = 0 WHERE student_id = ?");
} else {
    $stmt = $conn->prepare("UPDATE staff SET webauthn_credential_id = NULL, webauthn_public_key = NULL, fingerprint_enabled = 0 WHERE staff_id = ?");
}

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => true, 'message' => 'Fingerprint authentication disabled']);
} else {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to disable fingerprint']);
}
?>