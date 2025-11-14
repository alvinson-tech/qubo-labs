<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
requireStudent();

// Validate session token
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT session_token FROM students WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if session token matches
if (!$user || $user['session_token'] !== $_SESSION['session_token']) {
    closeDBConnection($conn);
    session_unset();
    session_destroy();
    header('Location: ../index.php?error=session_expired');
    exit();
}

$class_id = $_SESSION['class_id'];
$student_id = $_SESSION['user_id'];
$active_session = getActiveSession($class_id);

// Get student's attendance summary by subject
$stmt = $conn->prepare("
    SELECT 
        sub.subject_id,
        sub.subject_code,
        sub.subject_name,
        sub.subject_type,
        COUNT(DISTINCT ats.session_id) AS total_sessions,
        COUNT(DISTINCT CASE WHEN ar.status = 'verified' THEN ar.record_id END) AS attended_sessions,
        COALESCE(
            ROUND(
                (COUNT(DISTINCT CASE WHEN ar.status = 'verified' THEN ar.record_id END) * 100.0) / 
                NULLIF(COUNT(DISTINCT ats.session_id), 0), 
                1
            ), 
            0
        ) AS attendance_percentage
    FROM subjects sub
    LEFT JOIN student_subjects ss ON sub.subject_id = ss.subject_id AND ss.student_id = ?
    LEFT JOIN attendance_sessions ats ON ats.class_id = ? AND ats.subject_id = sub.subject_id AND ats.status = 'ended'
    LEFT JOIN attendance_records ar ON ar.session_id = ats.session_id AND ar.student_id = ?
    WHERE ss.mapping_id IS NOT NULL
    GROUP BY sub.subject_id, sub.subject_code, sub.subject_name, sub.subject_type
    ORDER BY sub.subject_code
");
$stmt->bind_param("iii", $student_id, $class_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$subjects_attendance = [];
while ($row = $result->fetch_assoc()) {
    $subjects_attendance[] = $row;
}
$stmt->close();

// Calculate overall attendance percentage
$total_sessions_all = 0;
$total_attended_all = 0;
foreach ($subjects_attendance as $subject) {
    $total_sessions_all += $subject['total_sessions'];
    $total_attended_all += $subject['attended_sessions'];
}
$overall_percentage = $total_sessions_all > 0 ? round(($total_attended_all / $total_sessions_all) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .overall-attendance-card {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border-radius: 12px;
            padding: 30px;
            color: white;
            text-align: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .overall-attendance-card h2 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            opacity: 0.95;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .overall-radial-progress {
            width: 180px;
            height: 180px;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .overall-radial-progress svg {
            transform: rotate(-90deg);
        }
        
        .overall-radial-progress circle {
            fill: none;
            stroke-width: 12;
        }
        
        .overall-radial-progress .bg-circle {
            stroke: rgba(255, 255, 255, 0.2);
        }
        
        .overall-radial-progress .progress-circle {
            stroke: white;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.5s ease;
        }
        
        .overall-radial-progress .percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 36px;
            font-weight: 800;
            color: white;
        }
        
        .overall-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .overall-stat {
            text-align: center;
        }
        
        .overall-stat-value {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 4px;
        }
        
        .overall-stat-label {
            font-size: 11px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }
        
        .subject-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }
        
        .subject-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
        
        .subject-header {
            margin-bottom: 12px;
        }
        
        .subject-code {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        
        .subject-name {
            font-size: 11px;
            color: var(--text-secondary);
            font-weight: 600;
            line-height: 1.3;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .radial-progress {
            width: 120px;
            height: 120px;
            margin: 0 auto 12px;
            position: relative;
        }
        
        .radial-progress svg {
            transform: rotate(-90deg);
        }
        
        .radial-progress circle {
            fill: none;
            stroke-width: 8;
        }
        
        .radial-progress .bg-circle {
            stroke: #f1f5f9;
        }
        
        .radial-progress .progress-circle {
            stroke-linecap: round;
            transition: stroke-dashoffset 0.5s ease;
        }
        
        .radial-progress .percentage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
        }
        
        .attendance-stats {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        
        .stat-value {
            color: var(--text-primary);
            font-weight: 700;
        }
        
        /* Color coding based on attendance percentage */
        .progress-critical { stroke: #ef4444; }
        .progress-warning { stroke: #f59e0b; }
        .progress-good { stroke: #3b82f6; }
        .progress-excellent { stroke: #10b981; }
        
        @media (max-width: 1400px) {
            .subjects-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .subjects-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .subjects-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            
            .subject-card {
                padding: 16px;
            }
            
            .radial-progress {
                width: 100px;
                height: 100px;
            }
            
            .radial-progress .percentage {
                font-size: 18px;
            }

            .overall-radial-progress .percentage {
                font-size: 32px;
            }
        }
        
        @media (max-width: 480px) {
            .subjects-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .no-sessions-message {
            text-align: center;
            padding: 40px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }
        
        .no-sessions-message h3 {
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 18px;
        }
        
        .no-sessions-message p {
            color: var(--text-secondary);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Student</div>
        <div class="nav-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../logout.php" class="btn btn-sm">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Attendance Dashboard</h1>
        </div>
        
        <div class="dashboard-grid">
            <!-- Overall Attendance Card -->
            <div class="overall-attendance-card">
                <h2>📊 Overall Attendance</h2>
                <div class="overall-radial-progress">
                    <?php
                        $radius = 70;
                        $circumference = 2 * pi() * $radius;
                        $offset = $circumference - ($overall_percentage / 100) * $circumference;
                    ?>
                    <svg width="180" height="180" viewBox="0 0 180 180">
                        <circle class="bg-circle" cx="90" cy="90" r="<?php echo $radius; ?>"></circle>
                        <circle class="progress-circle" 
                                cx="90" cy="90" r="<?php echo $radius; ?>"
                                stroke-dasharray="<?php echo $circumference; ?>"
                                stroke-dashoffset="<?php echo $offset; ?>">
                        </circle>
                    </svg>
                    <div class="percentage"><?php echo number_format($overall_percentage, 1); ?>%</div>
                </div>
                <div class="overall-stats">
                    <div class="overall-stat">
                        <div class="overall-stat-value"><?php echo $total_attended_all; ?></div>
                        <div class="overall-stat-label">Attended</div>
                    </div>
                    <div class="overall-stat">
                        <div class="overall-stat-value"><?php echo $total_sessions_all; ?></div>
                        <div class="overall-stat-label">Total</div>
                    </div>
                </div>
            </div>
            
            <!-- Active Session Card -->
            <div>
                <?php if ($active_session): ?>
                    <?php
                    $has_marked = hasMarkedAttendance($active_session['session_id'], $student_id);
                    $attendance_record = getStudentAttendanceRecord($active_session['session_id'], $student_id);
                    ?>
                    
                    <div class="session-card active">
                        <div class="session-badge">ACTIVE SESSION</div>
                        <h2><?php echo htmlspecialchars($active_session['session_name']); ?></h2>
                        <p class="session-time">Started: <?php echo date('M d, Y h:i A', strtotime($active_session['start_time'])); ?></p>
                        
                        <?php if (!$has_marked): ?>
                            <a href="scan_qr.php?session_id=<?php echo $active_session['session_id']; ?>" class="btn btn-primary btn-large">
                                📷 Scan QR Code
                            </a>
                        <?php elseif ($attendance_record['status'] === 'scanned' && !$attendance_record['no_neighbours']): ?>
                            <div class="status-message warning">
                                <h3>⏳ Waiting for Verification</h3>
                                <p>Your QR code has been scanned. Share your verification code with your neighbor.</p>
                                <div class="verification-code-display">
                                    <?php echo htmlspecialchars($attendance_record['verification_code']); ?>
                                </div>
                                <p class="info-text">Your neighbor should enter this code to verify your attendance.</p>
                                <a href="verify_code.php?session_id=<?php echo $active_session['session_id']; ?>" class="btn btn-primary" style="margin-top: 20px;">
                                    Enter Neighbor's Code
                                </a>
                            </div>
                        <?php elseif ($attendance_record['status'] === 'scanned' && $attendance_record['no_neighbours']): ?>
                            <div class="status-message warning">
                                <h3>⚠️ Not Verified</h3>
                                <p>You marked "No Neighbours" - Your attendance is recorded but not verified.</p>
                                <p class="info-text">Your attendance will appear as "Not Verified" in the report.</p>
                                <div class="attendance-details">
                                    <p><strong>Seat:</strong> <?php 
                                        $stmt = $conn->prepare("SELECT seat_number FROM seminar_seats WHERE seat_id = ?");
                                        $stmt->bind_param("i", $attendance_record['seat_id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $seat = $result->fetch_assoc();
                                        echo htmlspecialchars($seat['seat_number']);
                                        $stmt->close();
                                    ?></p>
                                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($attendance_record['scanned_at'])); ?></p>
                                </div>
                            </div>
                        <?php elseif ($attendance_record['status'] === 'verified'): ?>
                            <div class="status-message success">
                                <h3>✅ Attendance Verified</h3>
                                <p>Your attendance has been successfully marked and verified.</p>
                                <div class="verification-code-display">
                                    <?php echo htmlspecialchars($attendance_record['verification_code']); ?>
                                </div>
                                <p class="info-text">Your verification code (for your neighbor's reference)</p>
                                <div class="attendance-details">
                                    <p><strong>Seat:</strong> <?php 
                                        $stmt = $conn->prepare("SELECT seat_number FROM seminar_seats WHERE seat_id = ?");
                                        $stmt->bind_param("i", $attendance_record['seat_id']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $seat = $result->fetch_assoc();
                                        echo htmlspecialchars($seat['seat_number']);
                                        $stmt->close();
                                    ?></p>
                                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($attendance_record['verified_at'])); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="no-session-message">
                        <div class="icon">📅</div>
                        <h2>No Active Session</h2>
                        <p>There are no active attendance sessions at the moment.</p>
                        <p class="info-text">Please wait for your instructor to start a session.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Subjects Attendance Overview -->
        <div style="margin-top: 40px;">
            <h2 style="margin-bottom: 20px; color: var(--text-primary); font-size: 24px;">Subject-wise Attendance</h2>
            
            <?php if (empty($subjects_attendance)): ?>
                <div class="no-sessions-message">
                    <h3>📚 No Attendance Data Yet</h3>
                    <p>Attendance tracking will appear here once sessions are conducted.</p>
                </div>
            <?php else: ?>
                <div class="subjects-grid">
                    <?php foreach ($subjects_attendance as $subject): ?>
                        <?php
                            $percentage = $subject['attendance_percentage'];
                            $progress_class = 'progress-excellent';
                            if ($percentage < 75) $progress_class = 'progress-critical';
                            elseif ($percentage < 80) $progress_class = 'progress-warning';
                            elseif ($percentage < 85) $progress_class = 'progress-good';
                            
                            // Calculate circle progress
                            $radius = 50;
                            $circumference = 2 * pi() * $radius;
                            $offset = $circumference - ($percentage / 100) * $circumference;
                        ?>
                        <div class="subject-card">
                            <div class="subject-header">
                                <div class="subject-code"><?php echo htmlspecialchars($subject['subject_code']); ?></div>
                                <div class="subject-name"><?php echo htmlspecialchars($subject['subject_name']); ?></div>
                            </div>
                            
                            <div class="radial-progress">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle class="bg-circle" cx="60" cy="60" r="<?php echo $radius; ?>"></circle>
                                    <circle class="progress-circle <?php echo $progress_class; ?>" 
                                            cx="60" cy="60" r="<?php echo $radius; ?>"
                                            stroke-dasharray="<?php echo $circumference; ?>"
                                            stroke-dashoffset="<?php echo $offset; ?>">
                                    </circle>
                                </svg>
                                <div class="percentage"><?php echo number_format($percentage, 1); ?>%</div>
                            </div>
                            
                            <div class="attendance-stats">
                                <span class="stat-value"><?php echo $subject['attended_sessions']; ?></span>
                                <span>/</span>
                                <span class="stat-value"><?php echo $subject['total_sessions']; ?></span>
                                <span>classes</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php closeDBConnection($conn); ?>
    
    <script>
        // Auto-refresh every 10 seconds to check for new sessions
        setTimeout(function() {
            location.reload();
        }, 10000);
    </script>
</body>
</html>