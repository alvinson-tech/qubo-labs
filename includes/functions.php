<?php
require_once __DIR__ . '/../config/database.php';

// Generate random 4-digit numeric code
function generateVerificationCode() {
    // Generate a random 4-digit number (1000-9999)
    return str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
}

// Check if there's an active session for a class
function getActiveSession($class_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM attendance_sessions WHERE class_id = ? AND status = 'active' ORDER BY start_time DESC LIMIT 1");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $session;
}

// Get seat information by QR code
function getSeatByQRCode($qr_code) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM seminar_seats WHERE qr_code = ?");
    $stmt->bind_param("s", $qr_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $seat;
}

// Check if student already marked attendance in this session
function hasMarkedAttendance($session_id, $student_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM attendance_records WHERE session_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $session_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    closeDBConnection($conn);
    return $exists;
}

// Get student's attendance record for verification
function getStudentAttendanceRecord($session_id, $student_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM attendance_records WHERE session_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $session_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $record;
}

// Get live attendance data for staff view
function getLiveAttendanceData($session_id) {
    $conn = getDBConnection();
    $query = "SELECT ar.*, s.seat_number, s.row_number, s.seat_position, 
              st.student_name, st.usn_number
              FROM attendance_records ar
              JOIN seminar_seats s ON ar.seat_id = s.seat_id
              JOIN students st ON ar.student_id = st.student_id
              WHERE ar.session_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    closeDBConnection($conn);
    return $data;
}

// Get all attendance records for a session (for PDF)
function getAttendanceReport($session_id) {
    $conn = getDBConnection();
    $query = "SELECT ar.*, s.seat_number, st.student_name, st.usn_number,
              verifier.student_name as verifier_name, verifier.usn_number as verifier_usn
              FROM attendance_records ar
              JOIN seminar_seats s ON ar.seat_id = s.seat_id
              JOIN students st ON ar.student_id = st.student_id
              LEFT JOIN students verifier ON ar.verified_by_student_id = verifier.student_id
              WHERE ar.session_id = ?
              ORDER BY ar.scanned_at";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    closeDBConnection($conn);
    return $data;
}

// Get session details
function getSessionDetails($session_id) {
    $conn = getDBConnection();
    $query = "SELECT ats.*, st.staff_name, c.class_name, c.section, c.semester, h.hall_name, h.room_number
              FROM attendance_sessions ats
              JOIN staff st ON ats.staff_id = st.staff_id
              JOIN classes c ON ats.class_id = c.class_id
              JOIN seminar_halls h ON ats.hall_id = h.hall_id
              WHERE ats.session_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();
    closeDBConnection($conn);
    return $session;
}

// Get all seminar halls
function getSeminarHalls() {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM seminar_halls ORDER BY hall_name");
    $stmt->execute();
    $result = $stmt->get_result();
    $halls = [];
    while ($row = $result->fetch_assoc()) {
        $halls[] = $row;
    }
    $stmt->close();
    closeDBConnection($conn);
    return $halls;
}

// Get seats for a specific hall
function getSeatsForHall($hall_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM seminar_seats WHERE hall_id = ? ORDER BY row_number, seat_position");
    $stmt->bind_param("i", $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seats = [];
    while ($row = $result->fetch_assoc()) {
        $seats[] = $row;
    }
    $stmt->close();
    closeDBConnection($conn);
    return $seats;
}
?>