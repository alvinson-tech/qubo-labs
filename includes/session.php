<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Check if user is student
function isStudent() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
}

// Check if user is staff
function isStaff() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'staff';
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit();
    }
}

// Redirect to login if not student
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header('Location: ../index.php');
        exit();
    }
}

// Redirect to login if not staff
function requireStaff() {
    requireLogin();
    if (!isStaff()) {
        header('Location: ../index.php');
        exit();
    }
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    // Redirect to landing page (root)
    $redirect = (strpos($_SERVER['REQUEST_URI'], '/staff/') !== false || 
                 strpos($_SERVER['REQUEST_URI'], '/student/') !== false) 
                ? '../index.php' : 'index.php';
    header('Location: ' . $redirect);
    exit();
}
?>