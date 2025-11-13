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
    <style>
        .code-input-group input {
            width: 220px;
            padding: 24px;
            font-size: 48px;
            text-align: center;
            letter-spacing: 18px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 800;
            background: white;
            color: var(--text-primary);
            outline: none;
        }
        
        .code-input-group input::placeholder {
            letter-spacing: 12px;
            color: #cbd5e1;
        }
        
        .code-input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        /* Hide increment/decrement arrows for number input */
        .code-input-group input::-webkit-outer-spin-button,
        .code-input-group input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        .code-input-group input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
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
                    <p class="info-text">Share this 4-digit code with your neighbor sitting beside you</p>
                </div>
                
                <div class="divider">AND</div>
                
                <div class="neighbor-code-section">
                    <h2>Enter Your Neighbor's Code</h2>
                    <p class="instruction">Ask your neighbor for their 4-digit code and enter it below</p>
                    
                    <form id="verify-form">
                        <div class="code-input-group">
                            <input 
                                type="number" 
                                id="neighbor-code" 
                                maxlength="4" 
                                placeholder="1234" 
                                pattern="[0-9]{4}"
                                inputmode="numeric"
                                required>
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
        const codeInput = document.getElementById('neighbor-code');
        
        // Limit input to 4 digits
        codeInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Limit to 4 digits
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });
        
        // Prevent paste of non-numeric values
        codeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/[^0-9]/g, '').slice(0, 4);
            this.value = numericOnly;
        });
        
        document.getElementById('verify-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = document.getElementById('neighbor-code').value.trim();
            
            if (code.length !== 4) {
                showError('Please enter a valid 4-digit code');
                return;
            }
            
            if (!/^\d{4}$/.test(code)) {
                showError('Code must contain only numbers');
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