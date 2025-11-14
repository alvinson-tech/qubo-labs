<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

header('Content-Type: application/json');

$session_id = $_GET['session_id'] ?? 0;
$staff_id = $_SESSION['user_id'];

// Verify session belongs to this staff
$session = getSessionDetails($session_id);
if (!$session || $session['staff_id'] != $staff_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit();
}

// Get all students in the class
$conn = getDBConnection();
$query = "SELECT student_id, usn_number, student_name 
          FROM students 
          WHERE class_id = ? 
          ORDER BY usn_number";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $session['class_id']);
$stmt->execute();
$result = $stmt->get_result();
$all_students = [];
while ($row = $result->fetch_assoc()) {
    $all_students[$row['student_id']] = $row;
}
$stmt->close();

// Get attendance records (modified to show N/A for virtual seats)
$query = "SELECT ar.*, 
          CASE WHEN s.seat_number = 'VIRTUAL' THEN 'N/A' ELSE s.seat_number END as seat_number, 
          s.row_number, 
          s.seat_position, 
          st.student_name, 
          st.usn_number
          FROM attendance_records ar
          LEFT JOIN seminar_seats s ON ar.seat_id = s.seat_id
          JOIN students st ON ar.student_id = st.student_id
          WHERE ar.session_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$attendance_data = [];
while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
}
$stmt->close();

// Map attendance data by student_id
$attendance_map = [];
foreach ($attendance_data as $record) {
    $attendance_map[$record['student_id']] = $record;
}

// Build complete student list with attendance status
$complete_list = [];
foreach ($all_students as $student_id => $student) {
    if (isset($attendance_map[$student_id])) {
        // Student has marked attendance
        $record = $attendance_map[$student_id];
        $complete_list[] = [
            'student_id' => $student_id,
            'usn_number' => $student['usn_number'],
            'student_name' => $student['student_name'],
            'seat_number' => $record['seat_number'],
            'status' => $record['status'],
            'no_neighbours' => $record['no_neighbours'],
            'marked' => true
        ];
    } else {
        // Student hasn't marked attendance
        $complete_list[] = [
            'student_id' => $student_id,
            'usn_number' => $student['usn_number'],
            'student_name' => $student['student_name'],
            'seat_number' => '-',
            'status' => 'not_marked',
            'no_neighbours' => 0,
            'marked' => false
        ];
    }
}

// Calculate statistics
$total_students = count($all_students);
$total_marked = count($attendance_data);
$total_verified = count(array_filter($attendance_data, function($record) {
    return $record['status'] === 'verified';
}));
$total_not_marked = $total_students - $total_marked;

closeDBConnection($conn);

echo json_encode([
    'success' => true,
    'attendance' => $attendance_data, // For seat map
    'complete_list' => $complete_list, // For student table
    'stats' => [
        'total_students' => $total_students,
        'total_marked' => $total_marked,
        'total_scanned' => $total_marked,
        'total_verified' => $total_verified,
        'total_not_marked' => $total_not_marked
    ]
]);
?>