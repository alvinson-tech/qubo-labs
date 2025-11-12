<?php
require_once 'includes/session.php';
require_once 'config/database.php';

// Clear session token from database before logout
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && isset($_SESSION['session_token'])) {
    $conn = getDBConnection();
    
    if ($_SESSION['user_type'] === 'student') {
        $stmt = $conn->prepare("UPDATE students SET session_token = NULL WHERE student_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    } elseif ($_SESSION['user_type'] === 'staff') {
        $stmt = $conn->prepare("UPDATE staff SET session_token = NULL WHERE staff_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
    
    closeDBConnection($conn);
}

session_unset();
session_destroy();
header('Location: index.php');
exit();
?>