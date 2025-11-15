<?php
require_once '../includes/session.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Generate a random challenge
$challenge = bin2hex(random_bytes(32));

// Store challenge in session for verification
$_SESSION['webauthn_challenge'] = $challenge;

// Get user info
$conn = getDBConnection();
if ($user_type === 'student') {
    $stmt = $conn->prepare("SELECT student_id, usn_number, student_name FROM students WHERE student_id = ?");
} else {
    $stmt = $conn->prepare("SELECT staff_id, staff_number, staff_name FROM staff WHERE staff_id = ?");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

// Prepare registration options
$options = [
    'challenge' => $challenge,
    'rp' => [
        'name' => 'Qubo Labs',
        'id' => $_SERVER['HTTP_HOST']
    ],
    'user' => [
        'id' => base64_encode(strval($user_id)),
        'name' => $user_type === 'student' ? $user['usn_number'] : $user['staff_number'],
        'displayName' => $user_type === 'student' ? $user['student_name'] : $user['staff_name']
    ],
    'pubKeyCredParams' => [
        ['type' => 'public-key', 'alg' => -7],  // ES256
        ['type' => 'public-key', 'alg' => -257] // RS256
    ],
    'authenticatorSelection' => [
        'authenticatorAttachment' => 'platform',
        'userVerification' => 'required'
    ],
    'timeout' => 60000,
    'attestation' => 'none'
];

echo json_encode([
    'success' => true,
    'options' => $options
]);
?>