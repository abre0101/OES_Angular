<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Instructor session
SessionManager::startSession('Instructor');

// Check if user is logged in
if(!isset($_SESSION['ID'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

// Validate instructor role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Instructor'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$instructor_id = $_SESSION['ID'];
$exam_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($exam_id <= 0) {
    header("Location: MyExams.php?error=invalid_id");
    exit();
}

// Verify the exam belongs to this instructor and check its status
$checkQuery = $con->prepare("SELECT exam_id, approval_status, 
    (SELECT COUNT(*) FROM exam_results WHERE exam_id = ?) as result_count
    FROM exams 
    WHERE exam_id = ? AND created_by = ?");
$checkQuery->bind_param("iii", $exam_id, $exam_id, $instructor_id);
$checkQuery->execute();
$exam = $checkQuery->get_result()->fetch_assoc();
$checkQuery->close();

if(!$exam) {
    mysqli_close($con);
    header("Location: MyExams.php?error=not_found");
    exit();
}

// Only allow deletion of draft exams
if($exam['approval_status'] !== 'draft') {
    mysqli_close($con);
    header("Location: MyExams.php?error=cannot_delete_submitted");
    exit();
}

// Check if any students have taken this exam
if($exam['result_count'] > 0) {
    mysqli_close($con);
    header("Location: MyExams.php?error=has_results");
    exit();
}

// Delete the exam (cascade will handle exam_questions)
$deleteQuery = $con->prepare("DELETE FROM exams WHERE exam_id = ? AND created_by = ?");
$deleteQuery->bind_param("ii", $exam_id, $instructor_id);

if($deleteQuery->execute()) {
    $deleteQuery->close();
    mysqli_close($con);
    header("Location: MyExams.php?success=deleted");
    exit();
} else {
    $deleteQuery->close();
    mysqli_close($con);
    header("Location: MyExams.php?error=delete_failed");
    exit();
}
?>
