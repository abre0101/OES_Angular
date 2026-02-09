<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('DepartmentHead');

if(!isset($_SESSION['ID'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'DepartmentHead'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$dept_id = $_SESSION['DeptId'] ?? null;
$searchQuery = $_GET['q'] ?? '';
$results = [];

if(!empty($searchQuery) && $dept_id) {
    $searchTerm = "%$searchQuery%";
    
    // Search Exams (in department)
    $examStmt = $con->prepare("SELECT e.exam_id, e.exam_name, c.course_code, e.approval_status, 'exam' as type 
                               FROM exams e
                               INNER JOIN courses c ON e.course_id = c.course_id
                               WHERE c.department_id = ? 
                               AND e.exam_name LIKE ?
                               LIMIT 10");
    $examStmt->bind_param("is", $dept_id, $searchTerm);
    $examStmt->execute();
    $examResults = $examStmt->get_result();
    while($row = $examResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Questions (in department courses)
    $questionStmt = $con->prepare("SELECT q.question_id, LEFT(q.question_text, 100) as question_text, c.course_code, 'question' as type 
                                   FROM questions q
                                   INNER JOIN courses c ON q.course_id = c.course_id
                                   WHERE c.department_id = ? 
                                   AND q.question_text LIKE ?
                                   LIMIT 10");
    $questionStmt->bind_param("is", $dept_id, $searchTerm);
    $questionStmt->execute();
    $questionResults = $questionStmt->get_result();
    while($row = $questionResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Instructors (in department)
    $instructorStmt = $con->prepare("SELECT instructor_id, instructor_code, full_name, email, 'instructor' as type 
                                     FROM instructors 
                                     WHERE department_id = ? 
                                     AND (instructor_code LIKE ? OR full_name LIKE ? OR email LIKE ?)
                                     LIMIT 10");
    $instructorStmt->bind_param("isss", $dept_id, $searchTerm, $searchTerm, $searchTerm);
    $instructorStmt->execute();
    $instructorResults = $instructorStmt->get_result();
    while($row = $instructorResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Students (in department)
    $studentStmt = $con->prepare("SELECT student_id, student_code, full_name, email, 'student' as type 
                                  FROM students 
                                  WHERE department_id = ? 
                                  AND (student_code LIKE ? OR full_name LIKE ? OR email LIKE ?)
                                  LIMIT 10");
    $studentStmt->bind_param("isss", $dept_id, $searchTerm, $searchTerm, $searchTerm);
    $studentStmt->execute();
    $studentResults = $studentStmt->get_result();
    while($row = $studentResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Courses (in department)
    $courseStmt = $con->prepare("SELECT course_id, course_code, course_name, 'course' as type 
                                 FROM courses 
                                 WHERE department_id = ? 
                                 AND (course_code LIKE ? OR course_name LIKE ?)
                                 LIMIT 10");
    $courseStmt->bind_param("iss", $dept_id, $searchTerm, $searchTerm);
    $courseStmt->execute();
    $courseResults = $courseStmt->get_result();
    while($row = $courseResults->fetch_assoc()) {
        $results[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($results);
$con->close();
?>
