<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
$student_id = $data['student_id'] ?? 0;
$staff_id = $_SESSION['user_id'];

// Validate session belongs to this staff
$session = getSessionDetails($session_id);
if (!$session || $session['staff_id'] != $staff_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit();
}

// Check if student already marked attendance
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM attendance_records WHERE session_id = ? AND student_id = ?");
$stmt->bind_param("ii", $session_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$existing_record = $result->fetch_assoc();
$stmt->close();

if ($existing_record) {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Student has already marked attendance']);
    exit();
}

// Get or create a MANUAL virtual seat for this hall
$stmt = $conn->prepare("SELECT seat_id FROM seminar_seats WHERE hall_id = ? AND seat_number = 'MANUAL' LIMIT 1");
$stmt->bind_param("i", $session['hall_id']);
$stmt->execute();
$result = $stmt->get_result();
$manual_seat = $result->fetch_assoc();
$stmt->close();

if (!$manual_seat) {
    // Create a MANUAL virtual seat for this hall
    $stmt = $conn->prepare("INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES (?, 'MANUAL', 0, 0, CONCAT('MANUAL_', ?))");
    $stmt->bind_param("ii", $session['hall_id'], $session['hall_id']);
    $stmt->execute();
    $manual_seat_id = $conn->insert_id;
    $stmt->close();
} else {
    $manual_seat_id = $manual_seat['seat_id'];
}

// Generate verification code
$verification_code = generateVerificationCode();

// Insert attendance record - marked as verified immediately since staff is adding it
$stmt = $conn->prepare("INSERT INTO attendance_records (session_id, student_id, seat_id, verification_code, status, no_neighbours, verified_at) VALUES (?, ?, ?, ?, 'verified', 1, NOW())");
$stmt->bind_param("iiis", $session_id, $student_id, $manual_seat_id, $verification_code);

if ($stmt->execute()) {
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode([
        'success' => true,
        'message' => 'Manual attendance marked successfully'
    ]);
} else {
    $error = $conn->error;
    $stmt->close();
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to mark attendance: ' . $error]);
}
?>