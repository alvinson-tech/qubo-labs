<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

$session_id = $_GET['session_id'] ?? 0;
$staff_id = $_SESSION['user_id'];

// Verify session belongs to this staff
$session = getSessionDetails($session_id);
if (!$session || $session['staff_id'] != $staff_id) {
    header('Location: index.php');
    exit();
}

// Get attendance records
$records = getAttendanceReport($session_id);

// Get all students in the class to find absentees
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT student_id, usn_number, student_name FROM students WHERE class_id = ? ORDER BY usn_number");
$stmt->bind_param("i", $session['class_id']);
$stmt->execute();
$result = $stmt->get_result();
$all_students = [];
while ($row = $result->fetch_assoc()) {
    $all_students[$row['student_id']] = $row;
}
$stmt->close();
closeDBConnection($conn);

// Find absentees
$present_student_ids = array_column($records, 'student_id');
$absentees = [];
foreach ($all_students as $student_id => $student) {
    if (!in_array($student_id, $present_student_ids)) {
        $absentees[] = $student;
    }
}

// Sort records by USN number (ascending)
usort($records, function($a, $b) {
    return strcmp($a['usn_number'], $b['usn_number']);
});

// Sort absentees by USN number (ascending)
usort($absentees, function($a, $b) {
    return strcmp($a['usn_number'], $b['usn_number']);
});

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance Report - <?php echo htmlspecialchars($session['subject_name'] ?? $session['session_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 40px;
            background: #ffffff;
            color: #1a1a1a;
            line-height: 1.6;
            font-size: 13px;
        }
        
        .document-header {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        
        .logo-section {
            margin-bottom: 15px;
        }
        
        .logo-section h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        
        .logo-section p {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .report-title {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 12px;
        }
        
        .subject-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 6px 16px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
        
        .session-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .info-item {
            display: flex;
            padding: 8px 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #475569;
            min-width: 110px;
            font-size: 13px;
        }
        
        .info-value {
            color: #0f172a;
            font-size: 13px;
        }
        
        .comments-section {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 25px;
        }
        
        .comments-title {
            font-weight: 700;
            color: #92400e;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .comments-text {
            color: #78350f;
            font-size: 13px;
            font-style: italic;
        }
        
        .stats-container {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            flex: 1;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 18px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 11px;
            opacity: 0.95;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            font-size: 12px;
            table-layout: fixed;
        }
        
        thead {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        
        th {
            padding: 14px 12px;
            text-align: left;
            color: white;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 12px;
        }
        
        tbody tr:hover {
            background: #f8fafc;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        th:nth-child(1), td:nth-child(1) {
            width: 8%;
            text-align: center;
        }
        
        th:nth-child(2), td:nth-child(2) {
            width: 18%;
        }
        
        th:nth-child(3), td:nth-child(3) {
            width: 28%;
        }
        
        th:nth-child(4), td:nth-child(4) {
            width: 15%;
            text-align: center;
        }
        
        th:nth-child(5), td:nth-child(5) {
            width: 16%;
        }
        
        th:nth-child(6), td:nth-child(6) {
            width: 15%;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-verified {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-scanned {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-not-verified {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .no-neighbour {
            font-style: italic;
            color: #64748b;
            font-size: 10px;
        }
        
        .empty-state {
            text-align: center;
            color: #94a3b8;
            padding: 40px;
        }
        
        .action-buttons {
            margin-top: 30px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 28px;
            margin: 0 6px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-secondary {
            background: #64748b;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #475569;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }
        
        .footer p {
            color: #94a3b8;
            font-size: 11px;
        }
        
        .footer-logo {
            color: #2563eb;
            font-weight: 600;
        }
        
        .absentees-table th:nth-child(1),
        .absentees-table td:nth-child(1) {
            width: 10%;
            text-align: center;
        }
        
        .absentees-table th:nth-child(2),
        .absentees-table td:nth-child(2) {
            width: 25%;
        }
        
        .absentees-table th:nth-child(3),
        .absentees-table td:nth-child(3) {
            width: 65%;
        }
        
        @media print {
            body {
                padding: 20px;
            }
            .action-buttons {
                display: none;
            }
            .stat-card {
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }
        }
        
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="document-header">
        <div class="logo-section">
            <h1>QUBO LABS</h1>
            <p>Smart Attendance Management System</p>
        </div>
        <div class="report-title">Attendance Report</div>
        <?php if (!empty($session['subject_code'])): ?>
            <div class="subject-badge"><?php echo htmlspecialchars($session['subject_code']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="session-info">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Subject:</span>
                <span class="info-value"><?php echo htmlspecialchars($session['subject_name'] ?? $session['session_name']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Class:</span>
                <span class="info-value"><?php echo htmlspecialchars($session['class_name'] . ' - Section ' . $session['section']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Venue:</span>
                <span class="info-value"><?php echo htmlspecialchars($session['hall_name'] . ' (Room ' . $session['room_number'] . ')'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Instructor:</span>
                <span class="info-value"><?php echo htmlspecialchars($session['staff_name']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Date:</span>
                <span class="info-value"><?php echo date('F d, Y', strtotime($session['start_time'])); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Time:</span>
                <span class="info-value">
                    <?php echo date('h:i A', strtotime($session['start_time'])); ?>
                    <?php if ($session['end_time']): ?>
                        - <?php echo date('h:i A', strtotime($session['end_time'])); ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </div>
    
    <?php if (!empty($session['comments'])): ?>
    <div class="comments-section">
        <div class="comments-title">📝 Instructor Comments</div>
        <div class="comments-text"><?php echo htmlspecialchars($session['comments']); ?></div>
    </div>
    <?php endif; ?>
    
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($all_students); ?></div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number"><?php echo count($records); ?></div>
            <div class="stat-label">Present</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo count(array_filter($records, function($r) { return $r['status'] === 'verified'; })); ?></div>
            <div class="stat-label">Verified</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?php echo count($absentees); ?></div>
            <div class="stat-label">Absent</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>USN Number</th>
                <th>Student Name</th>
                <th>Seat No.</th>
                <th>Scan Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($records)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <p>No attendance records found for this session</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php $sno = 1; foreach ($records as $record): ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo $sno++; ?></td>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($record['usn_number']); ?></td>
                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                        <td style="font-weight: 600; color: #2563eb;"><?php echo htmlspecialchars($record['seat_number']); ?></td>
                        <td><?php echo date('h:i A', strtotime($record['scanned_at'])); ?></td>
                        <td>
                            <?php if ($record['status'] === 'verified'): ?>
                                <span class="status-badge status-verified">✓ Verified</span>
                            <?php elseif ($record['no_neighbours']): ?>
                                <span class="status-badge status-not-verified">⚠ Not Verified</span>
                                <br><span class="no-neighbour">No Neighbours</span>
                            <?php else: ?>
                                <span class="status-badge status-scanned">⏳ Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <?php if (!empty($absentees)): ?>
    <div style="margin-top: 40px;">
        <h2 style="color: #ef4444; font-size: 20px; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #fee2e2;">
            📋 Absentees (<?php echo count($absentees); ?>)
        </h2>
        <table class="absentees-table" style="border: 2px solid #fee2e2;">
            <thead style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <tr>
                    <th>S.No</th>
                    <th>USN Number</th>
                    <th>Student Name</th>
                </tr>
            </thead>
            <tbody>
                <?php $abs_no = 1; foreach ($absentees as $absentee): ?>
                    <tr style="background: #fef2f2;">
                        <td style="font-weight: 600; color: #991b1b;"><?php echo $abs_no++; ?></td>
                        <td style="font-weight: 600; color: #991b1b;"><?php echo htmlspecialchars($absentee['usn_number']); ?></td>
                        <td style="color: #7f1d1d;"><?php echo htmlspecialchars($absentee['student_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print / Save as PDF
        </button>
        <a href="index.php" class="btn btn-secondary">
            ✕ Close
        </a>
    </div>
    
    <div class="footer">
        <p>Generated by <span class="footer-logo">Qubo Labs</span> on <?php echo date('F d, Y \a\t h:i A'); ?></p>
        <p style="margin-top: 5px;">This is a computer-generated document and does not require a signature.</p>
    </div>
</body>
</html>