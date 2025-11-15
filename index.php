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
    <style>
        /* Prevent scrolling and fit everything on screen */
        html, body {
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .landing-container {
            height: calc(100vh - 50px);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow: hidden;
            padding-top: 2rem;
        }

        .landing-container .header {
            margin-bottom: 0.5rem;
        }

        .landing-container .login-options {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3rem;
        }

        /* Footer Styles */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: 0.5rem 1rem;
            text-align: center;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            z-index: 100;
            height: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .footer-credits {
            margin: 0 0 0.15rem 0;
            font-size: 0.8rem;
            font-weight: 500;
            color: #ffffffff;
        }

        .footer-copyright {
            margin: 0;
            font-size: 0.7rem;
            color: #ffffffff;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .footer {
                padding: 0.4rem 0.5rem;
                height: 45px;
            }
            
            .footer-credits {
                font-size: 0.75rem;
            }
            
            .footer-copyright {
                font-size: 0.65rem;
            }

            .landing-container {
                height: calc(100vh - 45px);
            }
        }
    </style>
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
    
    <footer class="footer">
        <p class="footer-credits">Made with 🤍 by Team Code Squad</p>
        <p class="footer-copyright">&copy; <?php echo date('Y'); ?> Qubo Labs. All rights reserved.</p>
    </footer>
</body>
</html>