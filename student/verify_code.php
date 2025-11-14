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
        .code-input-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 20px 0;
        }

        .code-digit-input {
            width: 60px;
            height: 70px;
            padding: 0;
            font-size: 36px;
            text-align: center;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 800;
            background: white;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s;
        }

        .code-digit-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            transform: scale(1.05);
        }

        .code-digit-input:disabled {
            background: #f1f5f9;
            cursor: not-allowed;
        }

        .code-digit-input::-webkit-outer-spin-button,
        .code-digit-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .code-digit-input[type=number] {
            -moz-appearance: textfield;
        }

        @media (max-width: 768px) {
            .code-digit-input {
                width: 50px;
                height: 60px;
                font-size: 30px;
            }
            
            .code-input-group {
                gap: 8px;
            }
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
                            <input type="number" 
                                class="code-digit-input" 
                                id="digit1" 
                                maxlength="1" 
                                min="0" 
                                max="9"
                                required
                                autocomplete="off">
                            <input type="number" 
                                class="code-digit-input" 
                                id="digit2" 
                                maxlength="1" 
                                min="0" 
                                max="9"
                                required
                                autocomplete="off">
                            <input type="number" 
                                class="code-digit-input" 
                                id="digit3" 
                                maxlength="1" 
                                min="0" 
                                max="9"
                                required
                                autocomplete="off">
                            <input type="number" 
                                class="code-digit-input" 
                                id="digit4" 
                                maxlength="1" 
                                min="0" 
                                max="9"
                                required
                                autocomplete="off">
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
        const digitInputs = document.querySelectorAll('.code-digit-input');
        
        // Handle input for each digit box
        digitInputs.forEach((input, index) => {
            // Move to next input on digit entry
            input.addEventListener('input', function(e) {
                // Remove any non-digit characters
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Limit to 1 digit
                if (this.value.length > 1) {
                    this.value = this.value.slice(0, 1);
                }
                
                // Move to next input if digit entered
                if (this.value.length === 1 && index < digitInputs.length - 1) {
                    digitInputs[index + 1].focus();
                }
            });
            
            // Handle backspace to move to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    digitInputs[index - 1].focus();
                }
            });
            
            // Handle paste - distribute digits across boxes
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                const digits = pastedData.replace(/[^0-9]/g, '').split('');
                
                digits.forEach((digit, i) => {
                    if (index + i < digitInputs.length) {
                        digitInputs[index + i].value = digit;
                    }
                });
                
                // Focus on next empty input or last input
                const nextEmptyIndex = Array.from(digitInputs).findIndex((inp, idx) => idx > index && inp.value === '');
                if (nextEmptyIndex !== -1) {
                    digitInputs[nextEmptyIndex].focus();
                } else {
                    digitInputs[digitInputs.length - 1].focus();
                }
            });
            
            // Select all text on focus
            input.addEventListener('focus', function() {
                this.select();
            });
            
            // Prevent non-numeric input
            input.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
                    e.preventDefault();
                }
            });
        });
        
        // Auto-focus first input on page load
        window.addEventListener('load', function() {
            digitInputs[0].focus();
        });
        
        document.getElementById('verify-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect all digits
            const code = Array.from(digitInputs).map(input => input.value).join('');
            
            if (code.length !== 4) {
                showError('Please enter all 4 digits');
                // Focus on first empty input
                const firstEmpty = Array.from(digitInputs).find(input => input.value === '');
                if (firstEmpty) firstEmpty.focus();
                return;
            }
            
            if (!/^\d{4}$/.test(code)) {
                showError('Code must contain only numbers');
                return;
            }
            
            verifyWithCode(code);
        });
        
        function verifyWithCode(code) {
            // Disable all inputs during verification
            digitInputs.forEach(input => input.disabled = true);
            
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
                    // Re-enable inputs and clear them
                    digitInputs.forEach(input => {
                        input.disabled = false;
                        input.value = '';
                    });
                    digitInputs[0].focus();
                }
            })
            .catch(error => {
                showError('Error verifying attendance. Please try again.');
                digitInputs.forEach(input => {
                    input.disabled = false;
                });
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