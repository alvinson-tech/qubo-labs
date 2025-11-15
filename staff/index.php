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
        /* Enhanced Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .nav-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .nav-user-info {
            text-align: right;
            line-height: 1.3;
        }
        
        .nav-user-greeting {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            color: var(--text-secondary);
            text-transform: uppercase;
        }
        
        .nav-user-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-primary);
        }
        
        .hamburger-btn {
            width: 40px;
            height: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .hamburger-btn:hover {
            background: #1e40af;
            transform: scale(1.05);
        }
        
        .hamburger-btn span {
            width: 20px;
            height: 2px;
            background: white;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .hamburger-btn.active span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        
        .hamburger-btn.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger-btn.active span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }
        
        /* Sidebar Styles */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: -350px;
            width: 350px;
            height: 100%;
            background: white;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar.active {
            right: 0;
        }
        
        .sidebar-header {
            padding: 30px;
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            color: white;
        }
        
        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .sidebar-header p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .sidebar-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu-item {
            margin-bottom: 10px;
        }
        
        .sidebar-menu-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 16px 20px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .sidebar-menu-link:hover {
            background: #e2e8f0;
            border-color: var(--primary);
            transform: translateX(-4px);
        }
        
        .sidebar-menu-icon {
            font-size: 20px;
        }
        
        .sidebar-menu-link.danger {
            background: #fee2e2;
            border-color: #fca5a5;
            color: var(--danger);
        }
        
        .sidebar-menu-link.danger:hover {
            background: #fecaca;
            border-color: var(--danger);
        }
        
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
        
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .sidebar {
                width: 100%;
                right: -100%;
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
        
        .logout-confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .logout-confirmation-modal.active {
            display: flex;
        }
        
        .logout-confirmation-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        .logout-confirmation-content h2 {
            color: var(--text-primary);
            margin-bottom: 12px;
            font-size: 24px;
            font-weight: 800;
        }
        
        .logout-confirmation-content p {
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-size: 15px;
        }
        
        .logout-confirmation-buttons {
            display: flex;
            gap: 12px;
        }
        
        .logout-confirmation-btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        
        .logout-confirmation-btn-cancel {
            background: var(--secondary);
            color: white;
        }
        
        .logout-confirmation-btn-cancel:hover {
            background: #475569;
        }
        
        .logout-confirmation-btn-logout {
            background: var(--danger);
            color: white;
        }
        
        .logout-confirmation-btn-logout:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>MENU</h2>
            <p>Staff Dashboard Options</p>
        </div>
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="setup_fingerprint.php" class="sidebar-menu-link">
                        <span class="sidebar-menu-icon">🔒</span>
                        <span>Fingerprint Setup</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link danger" onclick="showLogoutConfirmation(); return false;">
                        <span class="sidebar-menu-icon">🚪</span>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Logout Confirmation Modal -->
    <div id="logout-confirmation-modal" class="logout-confirmation-modal">
        <div class="logout-confirmation-content">
            <h2>🚪 Logout Confirmation</h2>
            <p>Are you sure you want to logout?</p>
            <div class="logout-confirmation-buttons">
                <button class="logout-confirmation-btn logout-confirmation-btn-cancel" onclick="closeLogoutConfirmation()">
                    Cancel
                </button>
                <button class="logout-confirmation-btn logout-confirmation-btn-logout" onclick="confirmLogout()">
                    Logout
                </button>
            </div>
        </div>
    </div>
    
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Staff</div>
        <div class="nav-user">
            <div class="nav-user-info">
                <div class="nav-user-greeting">WELCOME,</div>
                <div class="nav-user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
            </div>
            <button class="hamburger-btn" id="hamburger-btn" onclick="toggleSidebar()">
                <span></span>
                <span></span>
                <span></span>
            </button>
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
    
    <script>
        // Sidebar functions
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const hamburger = document.getElementById('hamburger-btn');
            
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            hamburger.classList.toggle('active');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const hamburger = document.getElementById('hamburger-btn');
            
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            hamburger.classList.remove('active');
        }
        
        // Logout confirmation functions
        function showLogoutConfirmation() {
            closeSidebar();
            document.getElementById('logout-confirmation-modal').classList.add('active');
        }
        
        function closeLogoutConfirmation() {
            document.getElementById('logout-confirmation-modal').classList.remove('active');
        }
        
        function confirmLogout() {
            window.location.href = '../logout.php';
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
                closeLogoutConfirmation();
            }
        });
        
        // Close modal when clicking outside
        document.getElementById('logout-confirmation-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogoutConfirmation();
            }
        });
    </script>
</body>
</html>