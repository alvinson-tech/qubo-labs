<?php
require_once 'includes/session.php';
require_once 'config/database.php';

// If already logged in, redirect
if (isLoggedIn()) {
    if (isStudent()) {
        header('Location: student/index.php');
        exit();
    } elseif (isStaff()) {
        header('Location: staff/index.php');
        exit();
    }
}

$user_type = isset($_GET['type']) ? $_GET['type'] : 'student';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'] ?? '';
    $password = $_POST['password'] ?? '';
    $type = $_POST['type'] ?? 'student';
    
    if (empty($identifier) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = getDBConnection();
        
        if ($type === 'student') {
            $stmt = $conn->prepare("SELECT student_id, student_name, usn_number, password, class_id, session_token FROM students WHERE usn_number = ?");
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if ($password === $user['password']) {
                    if (!empty($user['session_token'])) {
                        $error = 'This account is already logged in on another device. Please logout from the other device first.';
                        $stmt->close();
                        closeDBConnection($conn);
                    } else {
                        $session_token = bin2hex(random_bytes(32));
                        $update_stmt = $conn->prepare("UPDATE students SET session_token = ?, last_login = NOW() WHERE student_id = ?");
                        $update_stmt->bind_param("si", $session_token, $user['student_id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        $_SESSION['user_id'] = $user['student_id'];
                        $_SESSION['user_name'] = $user['student_name'];
                        $_SESSION['usn_number'] = $user['usn_number'];
                        $_SESSION['user_type'] = 'student';
                        $_SESSION['class_id'] = $user['class_id'];
                        $_SESSION['session_token'] = $session_token;
                        
                        $stmt->close();
                        closeDBConnection($conn);
                        header('Location: student/index.php');
                        exit();
                    }
                } else {
                    $error = 'Invalid USN or password';
                    $stmt->close();
                }
            } else {
                $error = 'Invalid USN or password';
                $stmt->close();
            }
        } elseif ($type === 'staff') {
            $stmt = $conn->prepare("SELECT staff_id, staff_name, staff_number, password, department, session_token FROM staff WHERE staff_number = ?");
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if ($password === $user['password']) {
                    if (!empty($user['session_token'])) {
                        $error = 'This account is already logged in on another device. Please logout from the other device first.';
                        $stmt->close();
                        closeDBConnection($conn);
                    } else {
                        $session_token = bin2hex(random_bytes(32));
                        $update_stmt = $conn->prepare("UPDATE staff SET session_token = ?, last_login = NOW() WHERE staff_id = ?");
                        $update_stmt->bind_param("si", $session_token, $user['staff_id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        $_SESSION['user_id'] = $user['staff_id'];
                        $_SESSION['user_name'] = $user['staff_name'];
                        $_SESSION['staff_number'] = $user['staff_number'];
                        $_SESSION['user_type'] = 'staff';
                        $_SESSION['department'] = $user['department'];
                        $_SESSION['session_token'] = $session_token;
                        
                        $stmt->close();
                        closeDBConnection($conn);
                        header('Location: staff/index.php');
                        exit();
                    }
                } else {
                    $error = 'Invalid Staff Number or password';
                    $stmt->close();
                }
            } else {
                $error = 'Invalid Staff Number or password';
                $stmt->close();
            }
        }
        
        closeDBConnection($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Qubo Labs</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .fingerprint-section {
            margin-top: 20px;
            text-align: center;
        }
        
        .fingerprint-divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 600;
        }
        
        .fingerprint-divider::before,
        .fingerprint-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        
        .fingerprint-divider span {
            padding: 0 15px;
        }
        
        .btn-fingerprint {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'DM Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-fingerprint:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .btn-fingerprint:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .fingerprint-icon {
            font-size: 20px;
        }
        
        .biometric-info {
            margin-top: 15px;
            padding: 12px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            font-size: 12px;
            color: #166534;
            line-height: 1.5;
        }
        
        .biometric-error {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Qubo Labs</h1>
            <h2><?php echo ucfirst($user_type); ?> Login</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="login-form">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($user_type); ?>">
                
                <div class="form-group">
                    <label for="identifier">
                        <?php echo $user_type === 'student' ? 'USN Number' : 'Staff ID'; ?>
                    </label>
                    <input type="text" 
                           id="identifier" 
                           name="identifier" 
                           required
                           placeholder="<?php echo $user_type === 'student' ? 'Enter your USN' : 'Enter your Staff ID'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <!-- Fingerprint Authentication Section -->
            <div class="fingerprint-section">
                <div class="fingerprint-divider">
                    <span>OR</span>
                </div>
                
                <button type="button" id="fingerprint-btn" class="btn-fingerprint">
                    <span class="fingerprint-icon">☝️</span>
                    <span>Login with Fingerprint</span>
                </button>
                
                <div id="fingerprint-info" class="biometric-info" style="display: none;">
                    Enter your USN/Staff ID above and click this button to login with fingerprint
                </div>
            </div>
            
            <div class="login-footer">
                <a href="index.php">← Back to Home</a>
            </div>
        </div>
    </div>
    
    <script>
        const userType = '<?php echo $user_type; ?>';
        const fingerprintBtn = document.getElementById('fingerprint-btn');
        const identifierInput = document.getElementById('identifier');
        const fingerprintInfo = document.getElementById('fingerprint-info');
        
        // Check if WebAuthn is supported
        if (!window.PublicKeyCredential) {
            fingerprintBtn.disabled = true;
            fingerprintInfo.textContent = 'Biometric authentication is not supported on this device';
            fingerprintInfo.classList.add('biometric-error');
            fingerprintInfo.style.display = 'block';
        }
        
        // Auto-uppercase the identifier input
        identifierInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
        
        // Fingerprint authentication
        fingerprintBtn.addEventListener('click', async function() {
            const identifier = identifierInput.value.trim();
            
            if (!identifier) {
                showError('Please enter your ' + (userType === 'student' ? 'USN' : 'Staff ID'));
                return;
            }
            
            try {
                fingerprintBtn.disabled = true;
                fingerprintBtn.innerHTML = '<span>🔄 Authenticating...</span>';
                
                // Step 1: Get authentication options
                const startResponse = await fetch('api/fingerprint_auth_start.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        identifier: identifier,
                        user_type: userType
                    })
                });
                
                const startData = await startResponse.json();
                
                if (!startData.success) {
                    showError(startData.message);
                    resetButton();
                    return;
                }
                
                // Step 2: Convert challenge from hex to ArrayBuffer
                const options = startData.options;
                options.challenge = hexToArrayBuffer(options.challenge);
                options.allowCredentials = options.allowCredentials.map(cred => ({
                    ...cred,
                    id: base64urlToArrayBuffer(cred.id)
                }));
                
                // Step 3: Request authentication from user
                const credential = await navigator.credentials.get({
                    publicKey: options
                });
                
                if (!credential) {
                    showError('Authentication cancelled');
                    resetButton();
                    return;
                }
                
                // Step 4: Complete authentication
                const completeResponse = await fetch('api/fingerprint_auth_complete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        credentialId: arrayBufferToBase64url(credential.rawId)
                    })
                });
                
                const completeData = await completeResponse.json();
                
                if (completeData.success) {
                    fingerprintBtn.innerHTML = '<span>✓ Success! Redirecting...</span>';
                    window.location.href = completeData.redirect;
                } else {
                    showError(completeData.message);
                    resetButton();
                }
                
            } catch (error) {
                console.error('Fingerprint auth error:', error);
                if (error.name === 'NotAllowedError') {
                    showError('Authentication was cancelled or timed out');
                } else {
                    showError('Fingerprint authentication failed. Please try again.');
                }
                resetButton();
            }
        });
        
        function resetButton() {
            fingerprintBtn.disabled = false;
            fingerprintBtn.innerHTML = '<span class="fingerprint-icon">👆</span><span>Sign in with Fingerprint</span>';
        }
        
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            errorDiv.style.marginTop = '15px';
            
            // Remove any existing error messages
            const existingError = document.querySelector('.fingerprint-section .error-message');
            if (existingError) {
                existingError.remove();
            }
            
            document.querySelector('.fingerprint-section').appendChild(errorDiv);
            
            setTimeout(() => errorDiv.remove(), 5000);
        }
        
        // Utility functions for WebAuthn
        function hexToArrayBuffer(hex) {
            const bytes = new Uint8Array(hex.length / 2);
            for (let i = 0; i < hex.length; i += 2) {
                bytes[i / 2] = parseInt(hex.substr(i, 2), 16);
            }
            return bytes.buffer;
        }
        
        function base64urlToArrayBuffer(base64url) {
            const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
            const binary = atob(base64);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) {
                bytes[i] = binary.charCodeAt(i);
            }
            return bytes.buffer;
        }
        
        function arrayBufferToBase64url(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            for (let i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        }
    </script>
</body>
</html>