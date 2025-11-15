<?php
require_once '../config/database.php';

header('Content-Type: application/json');

// Don't require login - this is for login page
$data = json_decode(file_get_contents('php://input'), true);
$identifier = $data['identifier'] ?? '';
$user_type = $data['user_type'] ?? 'student';

if (empty($identifier)) {
    echo json_encode(['success' => false, 'message' => 'USN/Staff Number required']);
    exit();
}

$conn = getDBConnection();

if ($user_type === 'student') {
    $stmt = $conn->prepare("SELECT student_id, webauthn_credential_id, fingerprint_enabled FROM students WHERE usn_number = ?");
} else {
    $stmt = $conn->prepare("SELECT staff_id, webauthn_credential_id, fingerprint_enabled FROM staff WHERE staff_number = ?");
}

$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

if (!$user['fingerprint_enabled'] || empty($user['webauthn_credential_id'])) {
    echo json_encode(['success' => false, 'message' => 'Fingerprint authentication not set up for this account']);
    exit();
}

// Generate challenge
$challenge = bin2hex(random_bytes(32));

// Start session to store challenge
session_start();
$_SESSION['webauthn_challenge'] = $challenge;
$_SESSION['webauthn_user_id'] = $user_type === 'student' ? $user['student_id'] : $user['staff_id'];
$_SESSION['webauthn_user_type'] = $user_type;

// Prepare authentication options
$options = [
    'challenge' => $challenge,
    'timeout' => 60000,
    'rpId' => $_SERVER['HTTP_HOST'],
    'allowCredentials' => [
        [
            'type' => 'public-key',
            'id' => $user['webauthn_credential_id']
        ]
    ],
    'userVerification' => 'required'
];

echo json_encode([
    'success' => true,
    'options' => $options
]);
?>