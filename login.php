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
    $identifier = $_POST['identifier'] ?? ''; // USN or Staff Number
    $password = $_POST['password'] ?? '';
    $type = $_POST['type'] ?? 'student';
    
    if (empty($identifier) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = getDBConnection();
        
        if ($type === 'student') {
            // Login using USN number
            $stmt = $conn->prepare("SELECT student_id, student_name, usn_number, password, class_id, session_token FROM students WHERE usn_number = ?");
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                // Direct password comparison (plain text)
                if ($password === $user['password']) {
                    // Check if user is already logged in elsewhere
                    if (!empty($user['session_token'])) {
                        $error = 'This account is already logged in on another device. Please logout from the other device first.';
                        $stmt->close();
                        closeDBConnection($conn);
                    } else {
                        // Generate unique session token
                        $session_token = bin2hex(random_bytes(32));
                        
                        // Update session token in database
                        $update_stmt = $conn->prepare("UPDATE students SET session_token = ?, last_login = NOW() WHERE student_id = ?");
                        $update_stmt->bind_param("si", $session_token, $user['student_id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        // Set session variables
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
            // Login using Staff Number
            $stmt = $conn->prepare("SELECT staff_id, staff_name, staff_number, password, department, session_token FROM staff WHERE staff_number = ?");
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                // Direct password comparison (plain text)
                if ($password === $user['password']) {
                    // Check if user is already logged in elsewhere
                    if (!empty($user['session_token'])) {
                        $error = 'This account is already logged in on another device. Please logout from the other device first.';
                        $stmt->close();
                        closeDBConnection($conn);
                    } else {
                        // Generate unique session token
                        $session_token = bin2hex(random_bytes(32));
                        
                        // Update session token in database
                        $update_stmt = $conn->prepare("UPDATE staff SET session_token = ?, last_login = NOW() WHERE staff_id = ?");
                        $update_stmt->bind_param("si", $session_token, $user['staff_id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                        
                        // Set session variables
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
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Qubo Labs</h1>
            <h2><?php echo ucfirst($user_type); ?> Login</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($user_type); ?>">
                
                <div class="form-group">
                    <label for="identifier">
                        <?php echo $user_type === 'student' ? 'USN Number' : 'Staff ID'; ?>
                    </label>
                    <input type="text" 
                           id="identifier" 
                           name="identifier" 
                           required
                           placeholder="<?php echo $user_type === 'student' ? 'Enter your USN' : 'Enter your Staff ID'; ?>"
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
            
            <div class="login-footer">
                <a href="index.php">← Back to Home</a>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-uppercase the identifier input
        document.getElementById('identifier').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
</body>
</html>