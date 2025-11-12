<?php
require_once 'includes/session.php';

// If already logged in, redirect to appropriate page
if (isLoggedIn()) {
    if (isStudent()) {
        header('Location: student/index.php');
        exit();
    } elseif (isStaff()) {
        header('Location: staff/index.php');
        exit();
    }
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qubo Labs - Attendance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="landing-container">
        <div class="header">
            <h1>Qubo Labs</h1>
            <p>Smart Attendance Management System</p>
        </div>
        
        <div class="login-options">
            <div class="login-card">
                <div class="icon">👨‍🎓</div>
                <h2>Student Login</h2>
                <p>Mark your attendance by scanning QR codes</p>
                <a href="login.php?type=student" class="btn btn-primary">Login as Student</a>
            </div>
            
            <div class="login-card">
                <div class="icon">👨‍🏫</div>
                <h2>Staff Login</h2>
                <p>Manage attendance sessions and view analytics</p>
                <a href="login.php?type=staff" class="btn btn-secondary">Login as Staff</a>
            </div>
        </div>
    </div>
</body>
</html>