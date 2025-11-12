<?php
require_once '../includes/session.php';
require_once '../config/database.php';
requireStaff();

// Validate session token
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT session_token FROM staff WHERE staff_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if session token matches
if (!$user || $user['session_token'] !== $_SESSION['session_token']) {
    // Session invalid - force logout
    $stmt->close();
    closeDBConnection($conn);
    session_unset();
    session_destroy();
    header('Location: ../index.php?error=session_expired');
    exit();
}
$stmt->close();

$staff_id = $_SESSION['user_id'];

// Get all classes
$stmt = $conn->prepare("SELECT * FROM classes ORDER BY class_name, section");
$stmt->execute();
$result = $stmt->get_result();
$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();

// Get recent sessions
$stmt = $conn->prepare("SELECT ats.*, c.class_name, c.section 
                        FROM attendance_sessions ats
                        JOIN classes c ON ats.class_id = c.class_id
                        WHERE ats.staff_id = ?
                        ORDER BY ats.start_time DESC
                        LIMIT 5");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_sessions = [];
while ($row = $result->fetch_assoc()) {
    $recent_sessions[] = $row;
}
$stmt->close();

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Staff</div>
        <div class="nav-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../logout.php" class="btn btn-sm">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Staff Dashboard</h1>
        </div>
        
        <div class="action-section">
            <h2>Start New Attendance Session</h2>
            <form action="start_session.php" method="POST" class="session-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="session_name">Session Name</label>
                        <input type="text" id="session_name" name="session_name" required 
                               placeholder="e.g., Morning Assembly, Seminar">
                    </div>
                    
                    <div class="form-group">
                        <label for="class_id">Select Class</label>
                        <select id="class_id" name="class_id" required>
                            <option value="">Choose a class...</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>">
                                    <?php echo htmlspecialchars($class['class_name'] . ' - Section ' . $class['section']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-large">
                    ▶️ Start Attendance Session
                </button>
            </form>
        </div>
        
        <div class="recent-sessions">
            <h2>Recent Sessions</h2>
            <?php if (empty($recent_sessions)): ?>
                <p class="no-data">No sessions found. Start your first session above!</p>
            <?php else: ?>
                <div class="sessions-list">
                    <?php foreach ($recent_sessions as $session): ?>
                        <div class="session-item <?php echo $session['status']; ?>">
                            <div class="session-info">
                                <h3><?php echo htmlspecialchars($session['session_name']); ?></h3>
                                <p class="session-meta">
                                    <?php echo htmlspecialchars($session['class_name'] . ' - Section ' . $session['section']); ?>
                                </p>
                                <p class="session-time">
                                    <?php echo date('M d, Y h:i A', strtotime($session['start_time'])); ?>
                                    <?php if ($session['end_time']): ?>
                                        - <?php echo date('h:i A', strtotime($session['end_time'])); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="session-actions">
                                <?php if ($session['status'] === 'active'): ?>
                                    <span class="badge badge-active">Active</span>
                                    <a href="live_view.php?session_id=<?php echo $session['session_id']; ?>" 
                                       class="btn btn-primary">View Live</a>
                                <?php else: ?>
                                    <span class="badge badge-ended">Ended</span>
                                    <a href="download_pdf.php?session_id=<?php echo $session['session_id']; ?>" 
                                       class="btn btn-secondary">Download PDF</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>