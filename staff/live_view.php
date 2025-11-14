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

// Get seats for this hall
$hall_seats = getSeatsForHall($session['hall_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Session - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .manual-verify-btns {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        
        .btn-verify,
        .btn-reject {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-verify {
            background: #10b981;
            color: white;
        }
        
        .btn-verify:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .btn-reject {
            background: #ef4444;
            color: white;
        }
        
        .btn-reject:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }
        
        .btn-verify:disabled,
        .btn-reject:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .verified-text {
            color: #10b981;
            font-weight: 600;
            font-size: 12px;
        }
        
        .na-text {
            color: #94a3b8;
            font-weight: 500;
            font-size: 12px;
        }
        
        .hall-info {
            background: #f8fafc;
            padding: 12px 20px;
            border-radius: 8px;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .hall-info span {
            font-weight: 700;
            color: #2563eb;
        }
        
        .btn-cancel {
            background: #f97316;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #ea580c;
        }
        
        /* Responsive seating styles */
        .auditorium-layout {
            background: white;
            padding: 24px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            overflow-x: auto;
        }
        
        .seating-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        
        .seat-row {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        
        .seats-container {
            display: flex;
            gap: 6px;
            justify-content: center;
            flex: 1;
            flex-wrap: nowrap;
        }
        
        .seat-box {
            width: calc((100vw - 100px) / 21);
            height: calc((100vw - 100px) / 21);
            max-width: 60px;
            max-height: 60px;
            min-width: 30px;
            min-height: 30px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: clamp(8px, 1.2vw, 11px);
            font-weight: 800;
            cursor: pointer;
            border: 2px solid transparent;
            flex-shrink: 1;
        }
        
        .seat-number {
            font-size: clamp(7px, 1vw, 10px);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h2 {
            margin: 0;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-input {
            padding: 10px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            width: 250px;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-btn {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .search-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .clear-search-btn {
            padding: 10px 16px;
            background: var(--secondary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .clear-search-btn:hover {
            background: #475569;
        }

        .highlight-row {
            animation: highlightOutline 3s ease-in-out;
        }

        @keyframes highlightOutline {
            0% {
                outline: 3px solid var(--primary);
                outline-offset: 2px;
            }
            50% {
                outline: 3px solid var(--primary);
                outline-offset: 2px;
            }
            100% {
                outline: 3px solid transparent;
                outline-offset: 2px;
            }
        }

        .no-results-message {
            text-align: center;
            padding: 20px;
            color: var(--danger);
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            background: #fee2e2;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 1400px) {
            .seat-box {
                width: calc((100vw - 80px) / 21);
                height: calc((100vw - 80px) / 21);
            }
        }
        
        @media (max-width: 1024px) {
            .seat-box {
                width: calc((100vw - 60px) / 21);
                height: calc((100vw - 60px) / 21);
            }
            .seat-row {
                gap: 4px;
            }
            .seats-container {
                gap: 4px;
            }
        }
        
        @media (max-width: 768px) {
            .seat-box {
                width: calc((100vw - 50px) / 21);
                height: calc((100vw - 50px) / 21);
            }
            .seat-row {
                gap: 3px;
            }
            .seats-container {
                gap: 3px;
            }
            .seating-grid {
                gap: 6px;
            }
        }
        
        @media (max-width: 480px) {
            .seat-box {
                width: calc((100vw - 40px) / 21);
                height: calc((100vw - 40px) / 21);
            }
            .seat-row {
                gap: 2px;
            }
            .seats-container {
                gap: 2px;
            }
        }
        
        .attendance-table th:nth-child(1),
        .attendance-table td:nth-child(1) {
            width: 6%;
            text-align: center;
        }
        
        .attendance-table th:nth-child(2),
        .attendance-table td:nth-child(2) {
            width: 16%;
        }
        
        .attendance-table th:nth-child(3),
        .attendance-table td:nth-child(3) {
            width: 25%;
        }
        
        .attendance-table th:nth-child(4),
        .attendance-table td:nth-child(4) {
            width: 12%;
            text-align: center;
        }
        
        .attendance-table th:nth-child(5),
        .attendance-table td:nth-child(5) {
            width: 15%;
        }
        
        .attendance-table th:nth-child(6),
        .attendance-table td:nth-child(6) {
            width: 16%;
            text-align: center;
        }
        
        .attendance-table {
            table-layout: fixed;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Live Session</div>
        <div class="nav-actions">
            <button onclick="cancelSession()" class="btn btn-cancel">Cancel Session</button>
            <button onclick="endSession()" class="btn btn-danger">End Session</button>
            <a href="index.php" class="btn btn-sm">Dashboard</a>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="session-header">
            <h1><?php echo htmlspecialchars($session['session_name']); ?></h1>
            <p class="session-meta">
                <?php echo htmlspecialchars($session['class_name'] . ' - Section ' . $session['section'] . ' (' . $session['semester'] . ')'); ?> | 
                Started: <?php echo date('h:i A', strtotime($session['start_time'])); ?>
            </p>
            <div class="hall-info">
                📍 <span><?php echo htmlspecialchars($session['hall_name'] . ' (Room ' . $session['room_number'] . ')'); ?></span> - 104 Seats Available
            </div>
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
            <h2>Seminar Hall Seating</h2>
            <div class="stage">STAGE / PODIUM</div>
            <div id="seating-grid" class="seating-grid">
                <!-- Seats will be dynamically loaded -->
            </div>
        </div>
        
        <div class="students-attendance-table">
            <div class="table-header">
                <h2>Live Attendance Records</h2>
                <div class="search-box">
                    <input type="text" 
                        id="search-input" 
                        class="search-input" 
                        placeholder="Search USN"
                        maxlength="20">
                    <button class="search-btn" onclick="searchStudent()">
                        Search
                    </button>
                    <button class="clear-search-btn" onclick="clearSearch()" style="display: none;" id="clear-btn">
                        ✕ Clear
                    </button>
                </div>
            </div>
            <div id="search-message" class="no-results-message" style="display: none;"></div>
            <div class="table-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>USN Number</th>
                            <th>Student Name</th>
                            <th>Seat No.</th>
                            <th>Status</th>
                            <th>Manual Verify</th>
                        </tr>
                    </thead>
                    <tbody id="student-list-body">
                        <tr>
                            <td colspan="6" style="text-align: center; color: #999; padding: 20px;">
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
        const hallSeats = <?php echo json_encode($hall_seats); ?>;
        let updateInterval;
        
        function initSeatingGrid() {
            const grid = document.getElementById('seating-grid');
            
            const seatsByRow = {};
            hallSeats.forEach(seat => {
                if (!seatsByRow[seat.row_number]) {
                    seatsByRow[seat.row_number] = [];
                }
                seatsByRow[seat.row_number].push(seat);
            });
            
            for (let rowNum = 1; rowNum <= 5; rowNum++) {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';
                
                const seatsContainer = document.createElement('div');
                seatsContainer.className = 'seats-container';
                
                const rowSeats = seatsByRow[rowNum] || [];
                rowSeats.forEach(seat => {
                    const seatDiv = document.createElement('div');
                    seatDiv.className = 'seat-box empty';
                    seatDiv.id = 'seat-' + seat.seat_number;
                    seatDiv.setAttribute('data-seat', seat.seat_number);
                    seatDiv.innerHTML = `<span class="seat-number">${seat.seat_number}</span>`;
                    seatsContainer.appendChild(seatDiv);
                });
                
                rowDiv.appendChild(seatsContainer);
                grid.appendChild(rowDiv);
            }
        }
        
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
        
        function updateSeating(attendanceData) {
            document.querySelectorAll('.seat-box').forEach(seat => {
                seat.className = 'seat-box empty';
                const seatNumber = seat.getAttribute('data-seat');
                seat.innerHTML = `<span class="seat-number">${seatNumber}</span>`;
            });
            
            attendanceData.forEach(record => {
                const seatElement = document.getElementById('seat-' + record.seat_number);
                if (seatElement) {
                    if (record.status === 'verified') {
                        seatElement.className = 'seat-box verified';
                    } else {
                        seatElement.className = 'seat-box scanned';
                    }
                }
            });
        }
        
        function updateStats(stats) {
            document.getElementById('total-students').textContent = stats.total_students;
            document.getElementById('total-marked').textContent = stats.total_marked;
            document.getElementById('total-verified').textContent = stats.total_verified;
            document.getElementById('total-not-marked').textContent = stats.total_not_marked;
        }
        
        function updateStudentTable(completeList) {
            const tbody = document.getElementById('student-list-body');
            tbody.innerHTML = '';
            
            if (completeList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #999;">No students found</td></tr>';
                return;
            }
            
            completeList.forEach((record, index) => {
                const row = document.createElement('tr');
                
                let statusBadge = '';
                let statusClass = '';
                let manualVerifyCol = '';
                
                if (!record.marked) {
                    statusBadge = '<span class="status-badge badge-not-marked">○ Not Marked</span>';
                    statusClass = 'status-not-marked';
                    manualVerifyCol = '<span class="na-text">N/A</span>';
                } else if (record.status === 'verified') {
                    statusBadge = '<span class="status-badge badge-verified">✓ Verified</span>';
                    statusClass = 'status-verified';
                    manualVerifyCol = '<span class="verified-text">✓ Verified</span>';
                } else if (record.no_neighbours == 1) {
                    statusBadge = '<span class="status-badge badge-not-verified">⚠ Not Verified</span>';
                    statusClass = 'status-not-verified';
                    manualVerifyCol = `
                        <div class="manual-verify-btns">
                            <button class="btn-verify" onclick="manualVerify(${record.student_id}, true)" title="Approve">✓</button>
                            <button class="btn-reject" onclick="manualVerify(${record.student_id}, false)" title="Reject">✗</button>
                        </div>
                    `;
                } else {
                    statusBadge = '<span class="status-badge badge-pending">⏳ Pending</span>';
                    statusClass = 'status-pending';
                    manualVerifyCol = '<span class="na-text">N/A</span>';
                }
                
                row.className = statusClass;
                row.innerHTML = `
                    <td style="font-weight: 700;">${index + 1}</td>
                    <td style="font-weight: 600;">${record.usn_number}</td>
                    <td>${record.student_name}</td>
                    <td style="font-weight: 700; color: #2563eb;">${record.seat_number}</td>
                    <td>${statusBadge}</td>
                    <td>${manualVerifyCol}</td>
                `;
                tbody.appendChild(row);
            });
        }
        
        function manualVerify(studentId, approve) {
            const action = approve ? 'approve' : 'reject';
            const confirmMsg = approve 
                ? 'Are you sure you want to approve this student\'s attendance?' 
                : 'Are you sure you want to reject this student\'s attendance?';
            
            if (!confirm(confirmMsg)) {
                return;
            }
            
            fetch('../api/manual_verify.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    student_id: studentId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    updateLiveData();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error processing manual verification. Please try again.');
            });
        }
        
        function cancelSession() {
            if (confirm('Are you sure you want to cancel this session? All attendance data will be deleted and this session will not be recorded. This cannot be undone.')) {
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

        // Search functionality
        function searchStudent() {
            const searchInput = document.getElementById('search-input').value.trim().toUpperCase();
            const searchMessage = document.getElementById('search-message');
            const clearBtn = document.getElementById('clear-btn');
            
            if (!searchInput) {
                searchMessage.textContent = 'Please enter a USN or partial USN to search';
                searchMessage.style.display = 'block';
                setTimeout(() => {
                    searchMessage.style.display = 'none';
                }, 3000);
                return;
            }
            
            const rows = document.querySelectorAll('#student-list-body tr');
            let found = false;
            
            // Remove previous highlights
            rows.forEach(row => {
                row.classList.remove('highlight-row');
            });
            
            // Search through rows
            for (let row of rows) {
                const usnCell = row.cells[1]; // USN is in the second column
                if (usnCell) {
                    const usn = usnCell.textContent.trim();
                    
                    // Check if USN contains the search term
                    if (usn.includes(searchInput)) {
                        // Scroll to the row
                        row.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        
                        // Highlight the row
                        setTimeout(() => {
                            row.classList.add('highlight-row');
                        }, 300);
                        
                        found = true;
                        clearBtn.style.display = 'inline-block';
                        searchMessage.style.display = 'none';
                        break;
                    }
                }
            }
            
            if (!found) {
                searchMessage.textContent = `No student found with USN containing "${searchInput}"`;
                searchMessage.style.display = 'block';
                clearBtn.style.display = 'none';
            }
        }

        function clearSearch() {
            document.getElementById('search-input').value = '';
            document.getElementById('clear-btn').style.display = 'none';
            document.getElementById('search-message').style.display = 'none';
            
            // Remove all highlights
            const rows = document.querySelectorAll('#student-list-body tr');
            rows.forEach(row => {
                row.classList.remove('highlight-row');
            });
            
            // Scroll to top of table
            document.querySelector('.students-attendance-table').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }

        // Allow Enter key to trigger search
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchStudent();
            }
        });

        // Allow Escape key to clear search
        document.getElementById('search-input').addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });
        
        initSeatingGrid();
        updateLiveData();
        
        updateInterval = setInterval(updateLiveData, 3000);
        
        window.addEventListener('beforeunload', function() {
            clearInterval(updateInterval);
        });
    </script>
</body>
</html>