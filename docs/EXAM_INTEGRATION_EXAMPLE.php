<?php
/**
 * Example: How to integrate the Technical Issue Reporter into your exam page
 * 
 * This is a sample showing how to add the quick issue reporting feature
 * to your existing exam interface.
 */

// Your existing exam page code...
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Student');
require_once('../Connections/config.php');

$student_id = $_SESSION['student_id'];
$exam_id = $_GET['exam_id']; // or however you get the exam ID

// Your exam logic here...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    
    <!-- Your existing CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Your existing exam interface -->
    <div class="exam-container">
        <h2>Exam: <?php echo $exam_name; ?></h2>
        
        <!-- Timer, questions, etc. -->
        
        <!-- OPTION 1: Add a manual button in your interface -->
        <button onclick="openIssueReporter()" class="btn btn-danger">
            <i class="fas fa-exclamation-triangle"></i> Report Technical Issue
        </button>
        
        <!-- Your exam questions and answers -->
    </div>
    
    <!-- Your existing scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <!-- ============================================ -->
    <!-- ADD THIS: Technical Issue Reporter Widget -->
    <!-- ============================================ -->
    <script src="exam-issue-reporter.js"></script>
    <script>
        // Set the current exam ID (REQUIRED)
        var currentExamId = <?php echo $exam_id; ?>;
        
        // OPTION 2: Add floating button automatically (uncomment if you want this)
        // window.addEventListener('load', addFloatingReportButton);
    </script>
    <!-- ============================================ -->
</body>
</html>
