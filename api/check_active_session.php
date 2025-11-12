<?php
require_once '../includes/session.php';
require_once '../includes/functions.php';
requireStudent();

header('Content-Type: application/json');

$class_id = $_SESSION['class_id'];
$active_session = getActiveSession($class_id);

if ($active_session) {
    echo json_encode([
        'success' => true,
        'has_session' => true,
        'session' => $active_session
    ]);
} else {
    echo json_encode([
        'success' => true,
        'has_session' => false
    ]);
}
?>