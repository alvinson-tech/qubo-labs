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
        
        .student-item.present:hover {
            background: #dcfce7;
        }
        
        .student-item.absent {
            background: #fef2f2;
        }
        
        .student-item.absent:hover {
            background: #fee2e2;
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
        
        .attendance-toggle {
            display: flex;
            gap: 12px;
        }
        
        .toggle-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 24px;
            background: white;
        }
        
        .toggle-btn.present-btn {
            border-color: #10b981;
            color: #065f46;
        }
        
        .toggle-btn.present-btn.active {
            background: #10b981;
            color: white;
        }
        
        .toggle-btn.present-btn:hover {
            background: #10b981;
            color: white;
            transform: scale(1.1);
        }
        
        .toggle-btn.absent-btn {
            border-color: #ef4444;
            color: #991b1b;
        }
        
        .toggle-btn.absent-btn.active {
            background: #ef4444;
            color: white;
        }
        
        .toggle-btn.absent-btn:hover {
            background: #ef4444;
            color: white;
            transform: scale(1.1);
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
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Manual Entry</div>
        <div class="nav-user">
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
                    <div class="stat-number present" id="present-count"><?php echo count($marked_students); ?></div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number absent" id="absent-count"><?php echo count($students) - count($marked_students); ?></div>
                    <div class="stat-label">Absent</div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button onclick="confirmAttendance()" class="btn btn-primary btn-large">
                    ✅ Confirm Attendance
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
                <?php $is_present = in_array($student['student_id'], $marked_students); ?>
                <div class="student-item <?php echo $is_present ? 'present' : 'absent'; ?>" data-student-id="<?php echo $student['student_id']; ?>">
                    <div class="student-info">
                        <span class="student-number"><?php echo $sno++; ?></span>
                        <span class="student-usn"><?php echo htmlspecialchars($student['usn_number']); ?></span>
                        <span class="student-name"><?php echo htmlspecialchars($student['student_name']); ?></span>
                    </div>
                    <div class="attendance-toggle">
                        <button class="toggle-btn present-btn <?php echo $is_present ? 'active' : ''; ?>" 
                                onclick="markPresent(<?php echo $student['student_id']; ?>)" 
                                title="Mark Present">✓</button>
                        <button class="toggle-btn absent-btn <?php echo !$is_present ? 'active' : ''; ?>" 
                                onclick="markAbsent(<?php echo $student['student_id']; ?>)" 
                                title="Mark Absent">✗</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        const sessionId = <?php echo $session_id; ?>;
        let attendanceState = {};
        
        // Initialize state
        <?php foreach ($students as $student): ?>
            attendanceState[<?php echo $student['student_id']; ?>] = <?php echo in_array($student['student_id'], $marked_students) ? 'true' : 'false'; ?>;
        <?php endforeach; ?>
        
        function markPresent(studentId) {
            attendanceState[studentId] = true;
            updateUI(studentId);
            updateStats();
        }
        
        function markAbsent(studentId) {
            attendanceState[studentId] = false;
            updateUI(studentId);
            updateStats();
        }
        
        function updateUI(studentId) {
            const item = document.querySelector(`[data-student-id="${studentId}"]`);
            const presentBtn = item.querySelector('.toggle-btn.present-btn');
            const absentBtn = item.querySelector('.toggle-btn.absent-btn');
            
            if (attendanceState[studentId]) {
                item.classList.remove('absent');
                item.classList.add('present');
                presentBtn.classList.add('active');
                absentBtn.classList.remove('active');
            } else {
                item.classList.remove('present');
                item.classList.add('absent');
                presentBtn.classList.remove('active');
                absentBtn.classList.add('active');
            }
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
                updateUI(parseInt(studentId));
            });
            updateStats();
        }
        
        function markAllAbsent() {
            if (confirm('Are you sure you want to mark all students as absent?')) {
                Object.keys(attendanceState).forEach(studentId => {
                    attendanceState[studentId] = false;
                    updateUI(parseInt(studentId));
                });
                updateStats();
            }
        }
        
        function confirmAttendance() {
            const presentCount = Object.values(attendanceState).filter(v => v === true).length;
            const totalCount = Object.keys(attendanceState).length;
            
            if (confirm(`Confirm attendance?\n\nPresent: ${presentCount}\nAbsent: ${totalCount - presentCount}\n\nThis will end the session.`)) {
                // Prepare data
                const presentStudents = Object.keys(attendanceState)
                    .filter(id => attendanceState[id] === true)
                    .map(id => parseInt(id));
                
                // Submit attendance
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
                        alert('Attendance confirmed successfully!');
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