<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStudent();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$session_id = $data['session_id'] ?? 0;
$seat_number = $data['seat_number'] ?? null;
$entry_type = $data['entry_type'] ?? ''; // 'manual_seat' or 'no_qr'
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

$conn = getDBConnection();

if ($entry_type === 'manual_seat') {
    // Manual seat entry
    if (empty($seat_number)) {
        echo json_encode(['success' => false, 'message' => 'Seat number is required']);
        exit();
    }
    
    // Get seat by seat number for this hall
    $stmt = $conn->prepare("SELECT seat_id FROM seminar_seats WHERE seat_number = ? AND hall_id = ?");
    $stmt->bind_param("si", $seat_number, $active_session['hall_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $stmt->close();
    
    if (!$seat) {
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Invalid seat number for this hall']);
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
    
    // Insert attendance record (marked as not verified, with no_neighbours = 1)
    $stmt = $conn->prepare("INSERT INTO attendance_records (session_id, student_id, seat_id, verification_code, status, no_neighbours) VALUES (?, ?, ?, ?, 'scanned', 1)");
    $stmt->bind_param("iiis", $session_id, $student_id, $seat['seat_id'], $verification_code);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode([
            'success' => true,
            'message' => 'Attendance marked successfully. Please wait for staff verification.'
        ]);
    } else {
        $error = $conn->error;
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Failed to record attendance: ' . $error]);
    }
    
} elseif ($entry_type === 'no_qr') {
    // No QR available - create a dummy seat entry
    // We need to create a virtual/dummy seat for students without QR codes
    
    // First, check if a dummy seat exists, if not create one
    $stmt = $conn->prepare("SELECT seat_id FROM seminar_seats WHERE hall_id = ? AND seat_number = 'VIRTUAL' LIMIT 1");
    $stmt->bind_param("i", $active_session['hall_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $virtual_seat = $result->fetch_assoc();
    $stmt->close();
    
    if (!$virtual_seat) {
        // Create a virtual seat for this hall
        $stmt = $conn->prepare("INSERT INTO seminar_seats (hall_id, seat_number, row_number, seat_position, qr_code) VALUES (?, 'VIRTUAL', 0, 0, CONCAT('VIRTUAL_', ?))");
        $stmt->bind_param("ii", $active_session['hall_id'], $active_session['hall_id']);
        $stmt->execute();
        $virtual_seat_id = $conn->insert_id;
        $stmt->close();
    } else {
        $virtual_seat_id = $virtual_seat['seat_id'];
    }
    
    // Generate verification code
    $verification_code = generateVerificationCode();
    
    // Insert attendance record with virtual seat_id
    $stmt = $conn->prepare("INSERT INTO attendance_records (session_id, student_id, seat_id, verification_code, status, no_neighbours) VALUES (?, ?, ?, ?, 'scanned', 1)");
    $stmt->bind_param("iiis", $session_id, $student_id, $virtual_seat_id, $verification_code);
    
    if ($stmt->execute()) {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode([
            'success' => true,
            'message' => 'Your presence has been recorded. Please wait for staff verification.'
        ]);
    } else {
        $error = $conn->error;
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false, 'message' => 'Failed to record attendance: ' . $error]);
    }
    
} else {
    closeDBConnection($conn);
    echo json_encode(['success' => false, 'message' => 'Invalid entry type']);
}
?>