<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
requireStaff();

$staff_id = $_SESSION['user_id'];
$session_id = $_GET['session_id'] ?? 0;

// If session_id is provided, load existing session
if ($session_id > 0) {
    $session = getSessionDetails($session_id);
    if (!$session || $session['staff_id'] != $staff_id || $session['status'] !== 'active' || $session['hall_id']) {
        header('Location: index.php');
        exit();
    }
    $subject_id = $session['subject_id'];
    $class_id = $session['class_id'];
    $comments = $session['comments'];
} else {
    // New session - get params
    $subject_id = $_GET['subject_id'] ?? 0;
    $class_id = $_GET['class_id'] ?? 0;
    $comments = $_GET['comments'] ?? '';
    
    if (empty($subject_id) || empty($class_id)) {
        header('Location: index.php');
        exit();
    }
    
    // Create new manual session
    $subject = getSubjectDetails($subject_id);
    if (!$subject) {
        header('Location: index.php');
        exit();
    }
    
    $session_name = $subject['subject_name'];
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO attendance_sessions (staff_id, class_id, hall_id, subject_id, session_name, comments, status) 
                            VALUES (?, ?, NULL, ?, ?, ?, 'active')");
    $stmt->bind_param("iiiss", $staff_id, $class_id, $subject_id, $session_name, $comments);
    
    if ($stmt->execute()) {
        $session_id = $conn->insert_id;
        $stmt->close();
        closeDBConnection($conn);
    } else {
        $stmt->close();
        closeDBConnection($conn);
        header('Location: index.php?error=create_failed');
        exit();
    }
    
    $session = getSessionDetails($session_id);
}

// Get all students in the class (ordered by USN)
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT student_id, usn_number, student_name FROM students WHERE class_id = ? ORDER BY usn_number ASC");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// Get already marked attendance
$stmt = $conn->prepare("SELECT student_id FROM attendance_records WHERE session_id = ?");
$stmt->bind_param("i", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$marked_students = [];
while ($row = $result->fetch_assoc()) {
    $marked_students[] = $row['student_id'];
}
$stmt->close();

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Attendance Entry - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .students-list {
            background: white;
            border-radius: 10px;
            border: 1px solid var(--border);
            overflow: hidden;
        }
        
        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
        }
        
        .student-item:last-child {
            border-bottom: none;
        }
        
        .student-item:hover {
            background: #f8fafc;
        }
        
        .student-item.present {
            background: #f0fdf4;
        }
        
        .student-item.absent {
            background: #fef2f2;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }
        
        .student-number {
            font-weight: 800;
            color: var(--text-secondary);
            font-size: 14px;
            min-width: 40px;
        }
        
        .student-usn {
            font-weight: 700;
            color: var(--primary);
            font-size: 14px;
            min-width: 140px;
        }
        
        .student-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .attendance-checkbox {
            width: 28px;
            height: 28px;
            cursor: pointer;
            accent-color: var(--primary);
        }
        
        .stats-bar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats-left {
            display: flex;
            gap: 30px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        
        .stat-number.present {
            color: #10b981;
        }
        
        .stat-number.absent {
            color: #ef4444;
        }
        
        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
        }
        
        .select-all-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .btn-cancel {
            background: #f97316;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #ea580c;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Manual Entry</div>
        <div class="nav-user">
            <button onclick="cancelSession()" class="btn btn-cancel btn-sm">Cancel Session</button>
            <a href="index.php" class="btn btn-sm">← Dashboard</a>
        </div>
    </div>
    
    <div class="container">
        <div class="session-header">
            <h1><?php echo htmlspecialchars($session['session_name']); ?></h1>
            <p class="session-meta">
                <?php echo htmlspecialchars($session['class_name'] . ' - Section ' . $session['section'] . ' (' . $session['semester'] . ')'); ?> | 
                Manual Entry | Started: <?php echo date('h:i A', strtotime($session['start_time'])); ?>
            </p>
        </div>
        
        <div class="stats-bar">
            <div class="stats-left">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($students); ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number present" id="present-count"><?php echo count($students); ?></div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number absent" id="absent-count">0</div>
                    <div class="stat-label">Absent</div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button onclick="confirmAttendance()" class="btn btn-primary btn-large">
                    ✅ Confirm & End Session
                </button>
            </div>
        </div>
        
        <div class="select-all-buttons">
            <button onclick="markAllPresent()" class="btn btn-sm" style="background: #10b981;">
                ✓ Mark All Present
            </button>
            <button onclick="markAllAbsent()" class="btn btn-sm" style="background: #ef4444;">
                ✗ Mark All Absent
            </button>
        </div>
        
        <div class="students-list" id="students-list">
            <?php $sno = 1; foreach ($students as $student): ?>
                <div class="student-item present" data-student-id="<?php echo $student['student_id']; ?>">
                    <div class="student-info">
                        <span class="student-number"><?php echo $sno++; ?></span>
                        <span class="student-usn"><?php echo htmlspecialchars($student['usn_number']); ?></span>
                        <span class="student-name"><?php echo htmlspecialchars($student['student_name']); ?></span>
                    </div>
                    <input type="checkbox" 
                           class="attendance-checkbox" 
                           data-student-id="<?php echo $student['student_id']; ?>"
                           onchange="toggleAttendance(<?php echo $student['student_id']; ?>)"
                           checked>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        const sessionId = <?php echo $session_id; ?>;
        let attendanceState = {};
        
        // Initialize all students as present by default
        <?php foreach ($students as $student): ?>
            attendanceState[<?php echo $student['student_id']; ?>] = true;
        <?php endforeach; ?>
        
        function toggleAttendance(studentId) {
            const checkbox = document.querySelector(`input[data-student-id="${studentId}"]`);
            const item = document.querySelector(`div[data-student-id="${studentId}"]`);
            
            attendanceState[studentId] = checkbox.checked;
            
            if (checkbox.checked) {
                item.classList.remove('absent');
                item.classList.add('present');
            } else {
                item.classList.remove('present');
                item.classList.add('absent');
            }
            
            updateStats();
        }
        
        function updateStats() {
            const presentCount = Object.values(attendanceState).filter(v => v === true).length;
            const totalCount = Object.keys(attendanceState).length;
            const absentCount = totalCount - presentCount;
            
            document.getElementById('present-count').textContent = presentCount;
            document.getElementById('absent-count').textContent = absentCount;
        }
        
        function markAllPresent() {
            Object.keys(attendanceState).forEach(studentId => {
                attendanceState[studentId] = true;
                const checkbox = document.querySelector(`input[data-student-id="${studentId}"]`);
                const item = document.querySelector(`div[data-student-id="${studentId}"]`);
                checkbox.checked = true;
                item.classList.remove('absent');
                item.classList.add('present');
            });
            updateStats();
        }
        
        function markAllAbsent() {
            if (confirm('Are you sure you want to mark all students as absent?')) {
                Object.keys(attendanceState).forEach(studentId => {
                    attendanceState[studentId] = false;
                    const checkbox = document.querySelector(`input[data-student-id="${studentId}"]`);
                    const item = document.querySelector(`div[data-student-id="${studentId}"]`);
                    checkbox.checked = false;
                    item.classList.remove('present');
                    item.classList.add('absent');
                });
                updateStats();
            }
        }
        
        function cancelSession() {
            if (confirm('Are you sure you want to cancel this session? All data will be deleted and this session will not be recorded. This cannot be undone.')) {
                fetch('../api/cancel_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: sessionId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Session cancelled successfully!');
                        window.location.href = 'index.php';
                    } else {
                        alert('Error cancelling session: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error cancelling session. Please try again.');
                });
            }
        }
        
        function confirmAttendance() {
            const presentCount = Object.values(attendanceState).filter(v => v === true).length;
            const totalCount = Object.keys(attendanceState).length;
            
            if (confirm(`Confirm attendance and end session?\n\nPresent: ${presentCount}\nAbsent: ${totalCount - presentCount}\n\nThis action cannot be undone.`)) {
                const presentStudents = Object.keys(attendanceState)
                    .filter(id => attendanceState[id] === true)
                    .map(id => parseInt(id));
                
                fetch('submit_manual_attendance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        present_students: presentStudents
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Attendance confirmed and session ended successfully!');
                        window.location.href = 'index.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error submitting attendance. Please try again.');
                    console.error(error);
                });
            }
        }
    </script>
</body>
</html>