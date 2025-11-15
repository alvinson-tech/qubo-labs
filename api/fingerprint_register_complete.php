<?php
require_once '../includes/session.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$credential_id = $data['credentialId'] ?? '';
$public_key = $data['publicKey'] ?? '';

if (empty($credential_id) || empty($public_key)) {
    echo json_encode(['success' => false, 'message' => 'Missing credential data']);
    exit();
}

// Verify challenge matches
if (!isset($_SESSION['webauthn_challenge'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit();
}

// Clear challenge
unset($_SESSION['webauthn_challenge']);

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Store credential in database
$conn = getDBConnection();

if ($user_type === 'student') {
    $stmt = $conn->prepare("UPDATE students SET webauthn_credential_id = ?, webauthn_public_key = ?, fingerprint_enabled = 1 WHERE student_id = ?");
} else {
    $stmt = $conn->prepare("UPDATE staff SET webauthn_credential_id = ?, webauthn_public_key = ?, fingerprint_enabled = 1 WHERE staff_id = ?");
}

$stmt->bind_param("ssi", $credential_id, $public_key, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode([
        'success' => true,
        'message' => 'Fingerprint authentication enabled successfully'
    ]);
} else {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save fingerprint credentials'
    ]);
}
?>