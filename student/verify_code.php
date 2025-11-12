<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStudent();

$session_id = $_GET['session_id'] ?? 0;
$student_id = $_SESSION['user_id'];
$class_id = $_SESSION['class_id'];

// Verify session is active
$active_session = getActiveSession($class_id);
if (!$active_session || $active_session['session_id'] != $session_id) {
    header('Location: index.php');
    exit();
}

// Get current student's record
$my_record = getStudentAttendanceRecord($session_id, $student_id);
if (!$my_record) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Attendance - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Verify</div>
        <div class="nav-user">
            <a href="index.php" class="btn btn-sm">← Back</a>
        </div>
    </div>
    
    <div class="container">
        <div class="verify-container">
            <h1>Verify Your Attendance</h1>
            
            <div class="verification-info">
                <div class="my-code-section">
                    <h2>Your Verification Code</h2>
                    <div class="verification-code-large">
                        <?php echo htmlspecialchars($my_record['verification_code']); ?>
                    </div>
                    <p class="info-text">Share this code with your neighbor sitting beside you</p>
                </div>
                
                <div class="divider">AND</div>
                
                <div class="neighbor-code-section">
                    <h2>Enter Your Neighbor's Code</h2>
                    <p class="instruction">Ask your neighbor for their 4-digit code and enter it below</p>
                    
                    <form id="verify-form">
                        <div class="code-input-group">
                            <input type="text" id="neighbor-code" maxlength="4" placeholder="XXXX" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Verify Attendance</button>
                    </form>
                    
                    <div class="or-divider">OR</div>
                    
                    <button onclick="markNoNeighbours()" class="btn btn-secondary btn-block">
                        No Neighbours Besides Me
                    </button>
                </div>
            </div>
            
            <div id="error-message" class="error-message" style="display: none;"></div>
            <div id="success-message" class="success-message" style="display: none;"></div>
        </div>
    </div>
    
    <script>
        const sessionId = <?php echo $session_id; ?>;
        
        document.getElementById('verify-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = document.getElementById('neighbor-code').value.trim().toUpperCase();
            
            if (code.length !== 4) {
                showError('Please enter a valid 4-character code');
                return;
            }
            
            verifyWithCode(code);
        });
        
        function verifyWithCode(code) {
            fetch('../api/verify_attendance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    neighbor_code: code,
                    no_neighbours: false
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Attendance verified successfully!');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Error verifying attendance. Please try again.');
            });
        }
        
        function markNoNeighbours() {
            if (confirm('Are you sure you have no neighbours sitting beside you?')) {
                fetch('../api/verify_attendance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: sessionId,
                        neighbor_code: null,
                        no_neighbours: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('Attendance marked successfully!');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 2000);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    showError('Error marking attendance. Please try again.');
                });
            }
        }
        
        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('success-message').style.display = 'none';
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('success-message');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            document.getElementById('error-message').style.display = 'none';
        }
    </script>
</body>
</html>