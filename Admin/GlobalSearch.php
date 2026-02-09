<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Admin');

if(!isset($_SESSION['username'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$searchQuery = $_GET['q'] ?? '';
$results = [];

if(!empty($searchQuery)) {
    $searchTerm = "%$searchQuery%";
    
    // Search Students
    $studentStmt = $con->prepare("SELECT student_id, student_code, full_name, email, 'student' as type 
                                  FROM students 
                                  WHERE student_code LIKE ? OR full_name LIKE ? OR email LIKE ? 
                                  LIMIT 10");
    $studentStmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $studentStmt->execute();
    $studentResults = $studentStmt->get_result();
    while($row = $studentResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Instructors
    $instructorStmt = $con->prepare("SELECT instructor_id, instructor_code, full_name, email, 'instructor' as type 
                                     FROM instructors 
                                     WHERE instructor_code LIKE ? OR full_name LIKE ? OR email LIKE ? 
                                     LIMIT 10");
    $instructorStmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $instructorStmt->execute();
    $instructorResults = $instructorStmt->get_result();
    while($row = $instructorResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Courses
    $courseStmt = $con->prepare("SELECT course_id, course_code, course_name, 'course' as type 
                                 FROM courses 
                                 WHERE course_code LIKE ? OR course_name LIKE ? 
                                 LIMIT 10");
    $courseStmt->bind_param("ss", $searchTerm, $searchTerm);
    $courseStmt->execute();
    $courseResults = $courseStmt->get_result();
    while($row = $courseResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Departments
    $deptStmt = $con->prepare("SELECT department_id, department_code, department_name, 'department' as type 
                               FROM departments 
                               WHERE department_code LIKE ? OR department_name LIKE ? 
                               LIMIT 10");
    $deptStmt->bind_param("ss", $searchTerm, $searchTerm);
    $deptStmt->execute();
    $deptResults = $deptStmt->get_result();
    while($row = $deptResults->fetch_assoc()) {
        $results[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($results);
$con->close();
?>
