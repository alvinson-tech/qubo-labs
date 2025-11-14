<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
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
    $stmt->close();
    closeDBConnection($conn);
    session_unset();
    session_destroy();
    header('Location: ../index.php?error=session_expired');
    exit();
}
$stmt->close();

$staff_id = $_SESSION['user_id'];

// Get all classes grouped by semester
$stmt = $conn->prepare("SELECT * FROM classes ORDER BY semester, section");
$stmt->execute();
$result = $stmt->get_result();
$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}
$stmt->close();

// Get all seminar halls
$halls = getSeminarHalls();

// Get all subjects
$subjects = getAllSubjects();

// Get recent sessions (only QR-based sessions with hall_id)
$stmt = $conn->prepare("SELECT ats.*, c.class_name, c.section, c.semester, h.hall_name, h.room_number,
                        sub.subject_name, sub.subject_code
                        FROM attendance_sessions ats
                        JOIN classes c ON ats.class_id = c.class_id
                        JOIN seminar_halls h ON ats.hall_id = h.hall_id
                        JOIN subjects sub ON ats.subject_id = sub.subject_id
                        WHERE ats.staff_id = ? AND ats.hall_id IS NOT NULL
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
    <style>
        .form-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 1024px) {
            .form-row-3 {
                grid-template-columns: 1fr;
            }
        }
        
        .session-subject {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        
        .session-type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-right: 8px;
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
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
        
        <!-- Start New Attendance Session -->
        <div class="action-section">
            <h2>Start New Attendance Session</h2>
            <form action="start_session.php" method="POST" class="session-form">
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="subject_id">Select Subject *</label>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">Choose a subject...</option>
                            <?php 
                            $current_type = '';
                            foreach ($subjects as $subject): 
                                if ($current_type !== $subject['subject_type']) {
                                    if ($current_type !== '') {
                                        echo '</optgroup>';
                                    }
                                    $current_type = $subject['subject_type'];
                                    echo '<optgroup label="' . ucfirst(htmlspecialchars($current_type)) . ' Subjects">';
                                }
                            ?>
                                <option value="<?php echo $subject['subject_id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                </option>
                            <?php 
                            endforeach;
                            if ($current_type !== '') {
                                echo '</optgroup>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="class_id">Select Class *</label>
                        <select id="class_id" name="class_id" required>
                            <option value="">Choose a class...</option>
                            <?php 
                            $current_semester = '';
                            foreach ($classes as $class): 
                                if ($current_semester !== $class['semester']) {
                                    if ($current_semester !== '') {
                                        echo '</optgroup>';
                                    }
                                    $current_semester = $class['semester'];
                                    echo '<optgroup label="' . htmlspecialchars($current_semester) . '">';
                                }
                            ?>
                                <option value="<?php echo $class['class_id']; ?>">
                                    <?php echo htmlspecialchars($class['class_name'] . ' - Section ' . $class['section']); ?>
                                </option>
                            <?php 
                            endforeach;
                            if ($current_semester !== '') {
                                echo '</optgroup>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="hall_id">Select Seminar Hall *</label>
                        <select id="hall_id" name="hall_id" required>
                            <option value="">Choose a seminar hall...</option>
                            <?php foreach ($halls as $hall): ?>
                                <option value="<?php echo $hall['hall_id']; ?>">
                                    <?php echo htmlspecialchars($hall['hall_name'] . ' (Room ' . $hall['room_number'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="comments">Comments (Optional)</label>
                    <textarea id="comments" 
                              name="comments" 
                              rows="3" 
                              placeholder="Add any additional notes or instructions for students..."
                              style="width: 100%; padding: 12px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: 'DM Sans', sans-serif; resize: vertical; background: white; color: var(--text-primary);"></textarea>
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
                                <?php if (!empty($session['subject_code'])): ?>
                                    <p class="session-subject">
                                        <span class="session-type-badge">QR</span>
                                        <?php echo htmlspecialchars($session['subject_code']); ?>
                                    </p>
                                <?php endif; ?>
                                <h3><?php echo htmlspecialchars($session['subject_name'] ?? $session['session_name']); ?></h3>
                                <p class="session-meta">
                                    <?php echo htmlspecialchars($session['class_name'] . ' - Section ' . $session['section'] . ' (' . $session['semester'] . ')'); ?>
                                    | <?php echo htmlspecialchars($session['hall_name'] . ' (Room ' . $session['room_number'] . ')'); ?>
                                </p>
                                <?php if (!empty($session['comments'])): ?>
                                    <p style="color: var(--text-secondary); font-size: 12px; margin-top: 4px; font-style: italic;">
                                        💬 <?php echo htmlspecialchars($session['comments']); ?>
                                    </p>
                                <?php endif; ?>
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