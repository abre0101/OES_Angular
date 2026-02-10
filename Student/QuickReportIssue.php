<?php
// Quick issue reporting during exam - can be embedded in exam interface
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Student session
SessionManager::startSession('Student');

// Check if user is logged in
if(!isset($_SESSION['Name'])){
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Validate user role - only students can access this page
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Student'){
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
    exit();
}

$con = require_once('../Connections/OES.php');

$student_id = $_SESSION['ID'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_id = mysqli_real_escape_string($con, $_POST['exam_id']);
    $issue_type = mysqli_real_escape_string($con, $_POST['issue_type']);
    $issue_description = mysqli_real_escape_string($con, $_POST['issue_description']);
    $browser_info = mysqli_real_escape_string($con, $_POST['browser_info']);
    $os_info = mysqli_real_escape_string($con, $_POST['os_info']);
    $screen_resolution = mysqli_real_escape_string($con, $_POST['screen_resolution']);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Determine priority
    $priority = 'medium';
    if (in_array($issue_type, ['submission_failure', 'timer_error', 'system_error'])) {
        $priority = 'high';
    } elseif ($issue_type == 'connectivity') {
        $priority = 'critical';
    }
    
    $sql = "INSERT INTO technical_issues (student_id, exam_id, issue_type, issue_description, 
            browser_info, os_info, screen_resolution, ip_address, priority, status, reported_at) 
            VALUES ('$student_id', '$exam_id', '$issue_type', '$issue_description', 
            '$browser_info', '$os_info', '$screen_resolution', '$ip_address', '$priority', 'pending', NOW())";
    
    if (mysqli_query($con, $sql)) {
        $issue_id = mysqli_insert_id($con);
        echo json_encode([
            'success' => true, 
            'message' => 'Issue reported successfully. Reference ID: #' . $issue_id,
            'issue_id' => $issue_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error reporting issue. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
