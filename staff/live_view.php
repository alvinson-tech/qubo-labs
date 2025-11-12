<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStaff();

$session_id = $_GET['session_id'] ?? 0;
$staff_id = $_SESSION['user_id'];

// Get session details
$session = getSessionDetails($session_id);
if (!$session || $session['staff_id'] != $staff_id) {
    header('Location: index.php');
    exit();
}

if ($session['status'] !== 'active') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Session - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Live Session</div>
        <div class="nav-actions">
            <button onclick="endSession()" class="btn btn-danger">End Session</button>
            <a href="index.php" class="btn btn-sm">Dashboard</a>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="session-header">
            <h1><?php echo htmlspecialchars($session['session_name']); ?></h1>
            <p class="session-meta">
                <?php echo htmlspecialchars($session['class_name'] . ' - Section ' . $session['section']); ?> | 
                Started: <?php echo date('h:i A', strtotime($session['start_time'])); ?>
            </p>
            <div class="attendance-stats">
                <div class="stat-card">
                    <div class="stat-number" id="total-students">0</div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="total-marked">0</div>
                    <div class="stat-label">Marked</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="total-verified">0</div>
                    <div class="stat-label">Verified</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="total-not-marked">0</div>
                    <div class="stat-label">Not Marked</div>
                </div>
            </div>
        </div>
        
        <div class="auditorium-layout">
            <h2>Auditorium Seating</h2>
            <div class="stage">STAGE</div>
            <div id="seating-grid" class="seating-grid">
                <!-- Seats will be dynamically loaded -->
            </div>
        </div>
        
        <!-- Students Attendance Table -->
        <div class="students-attendance-table">
            <h2>Live Attendance Records</h2>
            <div class="table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">S.No</th>
                            <th style="width: 18%;">Roll Number</th>
                            <th style="width: 28%;">Student Name</th>
                            <th style="width: 15%;">Seat No.</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="student-list-body">
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999; padding: 20px;">
                                No students have marked attendance yet
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        const sessionId = <?php echo $session_id; ?>;
        let updateInterval;
        
        // Initialize seating grid
        function initSeatingGrid() {
            const grid = document.getElementById('seating-grid');
            
            // Create 10 rows x 10 seats
            const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            
            rows.forEach((row, rowIndex) => {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';
                rowDiv.innerHTML = `<div class="row-label">${row}</div>`;
                
                const seatsContainer = document.createElement('div');
                seatsContainer.className = 'seats-container';
                
                for (let i = 1; i <= 10; i++) {
                    const seatDiv = document.createElement('div');
                    seatDiv.className = 'seat-box empty';
                    seatDiv.id = 'seat-' + row + i;
                    seatDiv.setAttribute('data-seat', row + i);
                    seatDiv.innerHTML = `<span class="seat-number">${row}${i}</span>`;
                    seatsContainer.appendChild(seatDiv);
                }
                
                rowDiv.appendChild(seatsContainer);
                grid.appendChild(rowDiv);
            });
        }
        
        // Update live data
        function updateLiveData() {
            fetch('../api/get_live_data.php?session_id=' + sessionId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateSeating(data.attendance);
                        updateStats(data.stats);
                        updateStudentTable(data.complete_list);
                    }
                })
                .catch(error => {
                    console.error('Error fetching live data:', error);
                });
        }
        
        // Update seating display
        function updateSeating(attendanceData) {
            // Reset all seats
            document.querySelectorAll('.seat-box').forEach(seat => {
                seat.className = 'seat-box empty';
                seat.innerHTML = `<span class="seat-number">${seat.getAttribute('data-seat')}</span>`;
            });
            
            // Update with current data
            attendanceData.forEach(record => {
                const seatElement = document.getElementById('seat-' + record.seat_number);
                if (seatElement) {
                    if (record.status === 'verified') {
                        seatElement.className = 'seat-box verified';
                    } else {
                        // Both 'scanned' and 'no_neighbours' show as yellow
                        seatElement.className = 'seat-box scanned';
                    }
                }
            });
        }
        
        // Update statistics and student table
        function updateStats(stats) {
            document.getElementById('total-students').textContent = stats.total_students;
            document.getElementById('total-marked').textContent = stats.total_marked;
            document.getElementById('total-verified').textContent = stats.total_verified;
            document.getElementById('total-not-marked').textContent = stats.total_not_marked;
        }
        
        // Update student list table
        function updateStudentTable(completeList) {
            const tbody = document.getElementById('student-list-body');
            tbody.innerHTML = '';
            
            if (completeList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No students found</td></tr>';
                return;
            }
            
            completeList.forEach((record, index) => {
                const row = document.createElement('tr');
                
                let statusBadge = '';
                let statusClass = '';
                
                if (!record.marked) {
                    statusBadge = '<span class="status-badge badge-not-marked">○ Not Marked</span>';
                    statusClass = 'status-not-marked';
                } else if (record.status === 'verified') {
                    statusBadge = '<span class="status-badge badge-verified">✓ Verified</span>';
                    statusClass = 'status-verified';
                } else if (record.no_neighbours == 1) {
                    statusBadge = '<span class="status-badge badge-not-verified">⚠ Not Verified</span>';
                    statusClass = 'status-not-verified';
                } else {
                    statusBadge = '<span class="status-badge badge-pending">⏳ Pending</span>';
                    statusClass = 'status-pending';
                }
                
                row.className = statusClass;
                row.innerHTML = `
                    <td style="font-weight: 700;">${index + 1}</td>
                    <td style="font-weight: 600;">${record.roll_number}</td>
                    <td>${record.student_name}</td>
                    <td style="text-align: center; font-weight: 700; color: #19A7CE;">${record.seat_number}</td>
                    <td>${statusBadge}</td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // End session
        function endSession() {
            if (confirm('Are you sure you want to end this session? This cannot be undone.')) {
                fetch('end_session.php', {
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
                        alert('Session ended successfully!');
                        window.location.href = 'index.php';
                    } else {
                        alert('Error ending session: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error ending session. Please try again.');
                });
            }
        }
        
        // Initialize
        initSeatingGrid();
        updateLiveData();
        
        // Update every 3 seconds
        updateInterval = setInterval(updateLiveData, 3000);
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            clearInterval(updateInterval);
        });
    </script>
</body>
</html>