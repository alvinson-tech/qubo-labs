<?php
require_once '../includes/session.php';
require_once '../config/database.php';
requireStaff();

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT fingerprint_enabled FROM students WHERE student_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);

$is_enabled = $user['fingerprint_enabled'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Fingerprint - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .fingerprint-card {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
        }
        
        .fingerprint-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .fingerprint-card h2 {
            color: var(--text-primary);
            margin-bottom: 16px;
            font-size: 24px;
        }
        
        .fingerprint-card p {
            color: var(--text-secondary);
            margin-bottom: 24px;
            line-height: 1.6;
        }
        
        .status-enabled {
            background: #d1fae5;
            color: #065f46;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .requirements {
            text-align: left;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        
        .requirements h3 {
            font-size: 16px;
            margin-bottom: 12px;
            color: var(--text-primary);
        }
        
        .requirements ul {
            margin-left: 20px;
            color: var(--text-secondary);
        }
        
        .requirements li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Student</div>
        <div class="nav-user">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="index.php" class="btn btn-sm">← Back</a>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Fingerprint Authentication Setup</h1>
        </div>
        
        <div class="fingerprint-card">
            <div class="fingerprint-icon">👆</div>
            
            <?php if ($is_enabled): ?>
                <div class="status-enabled">✓ Fingerprint Authentication Enabled</div>
                <h2>You're All Set!</h2>
                <p>Fingerprint authentication is active on your account. You can now use your fingerprint to login quickly and securely.</p>
                <button onclick="disableFingerprint()" class="btn btn-danger">
                    Disable Fingerprint
                </button>
            <?php else: ?>
                <h2>Secure Your Account</h2>
                <p>Enable fingerprint authentication to login faster and more securely. Your biometric data never leaves your device.</p>
                
                <div class="requirements">
                    <h3>Requirements:</h3>
                    <ul>
                        <li>A device with fingerprint sensor or Face ID</li>
                        <li>Modern browser (Chrome, Safari, Edge, Firefox)</li>
                        <li>HTTPS connection (secure website)</li>
                    </ul>
                </div>
                
                <button onclick="setupFingerprint()" class="btn btn-primary btn-large">
                    Enable Fingerprint Authentication
                </button>
            <?php endif; ?>
            
            <div id="error-message" class="error-message" style="display: none; margin-top: 20px;"></div>
            <div id="success-message" class="success-message" style="display: none; margin-top: 20px;"></div>
        </div>
    </div>
    
    <script>
        // Check if WebAuthn is supported
        if (!window.PublicKeyCredential) {
            document.querySelector('.fingerprint-card').innerHTML = `
                <div class="fingerprint-icon">⚠️</div>
                <h2>Not Supported</h2>
                <p>Your browser doesn't support fingerprint authentication. Please use a modern browser like Chrome, Safari, Edge, or Firefox.</p>
                <a href="index.php" class="btn btn-secondary">Go Back</a>
            `;
        }
        
        async function setupFingerprint() {
            try {
                const button = event.target;
                button.disabled = true;
                button.textContent = 'Setting up...';
                
                // Step 1: Get registration options
                const startResponse = await fetch('../api/fingerprint_register_start.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const startData = await startResponse.json();
                
                if (!startData.success) {
                    showError(startData.message);
                    button.disabled = false;
                    button.textContent = 'Enable Fingerprint Authentication';
                    return;
                }
                
                // Step 2: Convert challenge from hex to ArrayBuffer
                const options = startData.options;
                options.challenge = hexToArrayBuffer(options.challenge);
                options.user.id = base64urlToArrayBuffer(options.user.id);
                
                // Step 3: Create credentials
                const credential = await navigator.credentials.create({
                    publicKey: options
                });
                
                if (!credential) {
                    showError('Fingerprint setup was cancelled');
                    button.disabled = false;
                    button.textContent = 'Enable Fingerprint Authentication';
                    return;
                }
                
                // Step 4: Complete registration
                const completeResponse = await fetch('../api/fingerprint_register_complete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        credentialId: arrayBufferToBase64url(credential.rawId),
                        publicKey: arrayBufferToBase64url(credential.response.getPublicKey())
                    })
                });
                
                const completeData = await completeResponse.json();
                
                if (completeData.success) {
                    showSuccess('Fingerprint authentication enabled successfully!');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showError(completeData.message);
                    button.disabled = false;
                    button.textContent = 'Enable Fingerprint Authentication';
                }
                
            } catch (error) {
                console.error('Setup error:', error);
                showError('Failed to setup fingerprint: ' + error.message);
                event.target.disabled = false;
                event.target.textContent = 'Enable Fingerprint Authentication';
            }
        }
        
        async function disableFingerprint() {
            if (!confirm('Are you sure you want to disable fingerprint authentication?')) {
                return;
            }
            
            try {
                const response = await fetch('../api/fingerprint_disable.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Fingerprint authentication disabled');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Failed to disable fingerprint');
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
        
        // Utility functions
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