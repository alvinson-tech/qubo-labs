<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStudent();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
$qr_code = $data['qr_code'] ?? '';
$student_id = $_SESSION['user_id'];
$class_id = $_SESSION['class_id'];

// Validate session
$active_session = getActiveSession($class_id);
if (!$active_session || $active_session['session_id'] != $session_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid or inactive session']);
    exit();
}

// Check if already marked
if (hasMarkedAttendance($session_id, $student_id)) {
    echo json_encode(['success' => false, 'message' => 'You have already marked attendance for this session']);
    exit();
}

// Validate QR code
$seat = getSeatByQRCode($qr_code);
if (!$seat) {
    echo json_encode(['success' => false, 'message' => 'Invalid QR code']);
    exit();
}

// Check if seat belongs to the correct hall for this session
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT hall_id FROM attendance_sessions WHERE session_id = ?");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$session_hall = $result->fetch_assoc();
$stmt->close();

if ($seat['hall_id'] != $session_hall['hall_id']) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'This QR code is not for the correct seminar hall. Please scan the correct QR code.']);
    exit();
}

// Check if seat is already occupied
$stmt = $conn->prepare("SELECT * FROM attendance_records WHERE session_id = ? AND seat_id = ?");
$stmt->bind_param("ii", $session_id, $seat['seat_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'This seat is already occupied']);
    exit();
}
$stmt->close();

// Generate verification code
$verification_code = generateVerificationCode();

// Insert attendance record
$stmt = $conn->prepare("INSERT INTO attendance_records (session_id, student_id, seat_id, verification_code, status) VALUES (?, ?, ?, ?, 'scanned')");
$stmt->bind_param("iiis", $session_id, $student_id, $seat['seat_id'], $verification_code);

if ($stmt->execute()) {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode([
        'success' => true,
        'verification_code' => $verification_code,
        'seat_number' => $seat['seat_number']
    ]);
} else {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
}
?>