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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                                // FIXED: Use seminar_seats instead of auditorium_seats
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
                                // FIXED: Use seminar_seats instead of auditorium_seats
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
    
    <?php closeDBConnection($conn); ?>
    
    <script>
        // Auto-refresh every 10 seconds to check for new sessions
        setTimeout(function() {
            location.reload();
        }, 10000);
    </script>
</body>
</html>