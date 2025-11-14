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
    <style>
        .alternative-options {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 20px;
        }
        
        .btn-alternative {
            padding: 12px 20px;
            border: 2px solid var(--primary);
            background: white;
            color: var(--primary);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-alternative:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
        }
        
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-overlay.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        
        .modal-content h2 {
            color: var(--text-primary);
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 800;
        }
        
        .modal-content p {
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .seat-input {
            width: 100%;
            padding: 14px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        
        .seat-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .modal-buttons {
            display: flex;
            gap: 12px;
        }
        
        .btn-modal {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-modal-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-modal-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-modal-secondary {
            background: var(--secondary);
            color: white;
        }
        
        .btn-modal-secondary:hover {
            background: #475569;
        }
        
        .confirmation-message {
            background: #fffbeb;
            border: 2px solid #fbbf24;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .confirmation-message strong {
            color: #92400e;
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .alternative-options {
                flex-direction: column;
            }
            
            .modal-content {
                padding: 30px 20px;
            }
            
            .modal-buttons {
                flex-direction: column;
            }
        }
    </style>
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
            
            <div class="alternative-options">
                <button class="btn-alternative" onclick="showManualSeatModal()">
                    🔧 QR Code Not Working?
                </button>
                <button class="btn-alternative" onclick="showNoQRModal()">
                    ❌ No QR Code Available
                </button>
            </div>
        </div>
    </div>
    
    <!-- Manual Seat Entry Modal -->
    <div id="manual-seat-modal" class="modal-overlay">
        <div class="modal-content">
            <h2>Enter Your Seat Number</h2>
            <p>Since the QR code isn't working, please enter your seat number manually.</p>
            <input type="text" 
                   id="manual-seat-input" 
                   class="seat-input" 
                   placeholder="e.g., C16"
                   maxlength="5">
            <p style="font-size: 12px; color: #64748b; margin-bottom: 20px;">
                Your seat number should be visible on your seat (e.g., A1, B10, C16)
            </p>
            <div class="modal-buttons">
                <button class="btn-modal btn-modal-secondary" onclick="closeModal('manual-seat-modal')">
                    Cancel
                </button>
                <button class="btn-modal btn-modal-primary" onclick="submitManualSeat()">
                    Send
                </button>
            </div>
        </div>
    </div>
    
    <!-- No QR Available Modal -->
    <div id="no-qr-modal" class="modal-overlay">
        <div class="modal-content">
            <h2>No QR Code Available</h2>
            <div class="confirmation-message">
                <strong>⚠️ Important Notice</strong>
                Kindly let your staff know you are present. Your presence will be monitored within the hall. Wait for your attendance to be verified by the staff!
            </div>
            <p>Your attendance will be marked as present but will need manual verification from your instructor.</p>
            <div class="modal-buttons">
                <button class="btn-modal btn-modal-secondary" onclick="closeModal('no-qr-modal')">
                    Cancel
                </button>
                <button class="btn-modal btn-modal-primary" onclick="submitNoQR()">
                    Send for Verification
                </button>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        const sessionId = <?php echo $session_id; ?>;
        let html5QrcodeScanner;
        
        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning
            html5QrcodeScanner.clear();
            
            // Submit the scanned QR code
            submitQRCode(decodedText);
        }
        
        function onScanError(errorMessage) {
            // Handle scan error silently
        }
        
        html5QrcodeScanner = new Html5QrcodeScanner(
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
        
        function showManualSeatModal() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(err => console.log(err));
            }
            document.getElementById('manual-seat-modal').classList.add('active');
            document.getElementById('manual-seat-input').focus();
        }
        
        function showNoQRModal() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(err => console.log(err));
            }
            document.getElementById('no-qr-modal').classList.add('active');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            // Restart scanner
            html5QrcodeScanner.render(onScanSuccess, onScanError);
        }
        
        function submitManualSeat() {
            const seatNumber = document.getElementById('manual-seat-input').value.trim().toUpperCase();
            
            if (!seatNumber) {
                alert('Please enter your seat number');
                return;
            }
            
            // Validate seat format (e.g., A1, B10, C16)
            if (!/^[A-E]\d{1,2}$/.test(seatNumber)) {
                alert('Please enter a valid seat number (e.g., A1, B10, C16)');
                return;
            }
            
            fetch('../api/submit_manual_seat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    seat_number: seatNumber,
                    entry_type: 'manual_seat'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Attendance marked! Please wait for staff verification.');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error submitting attendance. Please try again.');
            });
        }
        
        function submitNoQR() {
            fetch('../api/submit_manual_seat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    seat_number: null,
                    entry_type: 'no_qr'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your presence has been recorded! Please wait for staff verification.');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error submitting attendance. Please try again.');
            });
        }
        
        // Auto-uppercase input
        document.getElementById('manual-seat-input').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>