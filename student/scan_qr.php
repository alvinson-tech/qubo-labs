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

// Check if already marked
if (hasMarkedAttendance($session_id, $student_id)) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - Qubo Labs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="navbar">
        <div class="nav-brand">Qubo Labs - Scan QR</div>
        <div class="nav-user">
            <a href="index.php" class="btn btn-sm">← Back</a>
        </div>
    </div>
    
    <div class="container">
        <div class="scan-container">
            <h1>Scan Your Seat QR Code</h1>
            <p class="instruction">Point your camera at the QR code on your seat</p>
            
            <div id="qr-reader"></div>
            <div id="qr-reader-results"></div>
            
            <div class="manual-input" style="display: none;">
                <p>Or enter the seat code manually:</p>
                <input type="text" id="manual-code" placeholder="e.g., QR_A1">
                <button onclick="submitManualCode()" class="btn btn-secondary">Submit</button>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        const sessionId = <?php echo $session_id; ?>;
        
        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning
            html5QrcodeScanner.clear();
            
            // Submit the scanned QR code
            submitQRCode(decodedText);
        }
        
        function onScanError(errorMessage) {
            // Handle scan error silently
        }
        
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", 
            { 
                fps: 10,
                qrbox: 250,
                aspectRatio: 1.0,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanError);
        
        function submitQRCode(qrCode) {
            fetch('../api/submit_scan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    qr_code: qrCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'verify_code.php?session_id=' + sessionId;
                } else {
                    alert('Error: ' + data.message);
                    html5QrcodeScanner.render(onScanSuccess, onScanError);
                }
            })
            .catch(error => {
                alert('Error submitting QR code. Please try again.');
                html5QrcodeScanner.render(onScanSuccess, onScanError);
            });
        }
        
        function submitManualCode() {
            const code = document.getElementById('manual-code').value.trim();
            if (code) {
                submitQRCode(code);
            } else {
                alert('Please enter a valid seat code');
            }
        }
    </script>
</body>
</html>