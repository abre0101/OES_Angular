<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Instructor');

if(!isset($_SESSION['ID'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Instructor'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$instructor_id = $_SESSION['ID'];
$searchQuery = $_GET['q'] ?? '';
$results = [];

if(!empty($searchQuery)) {
    $searchTerm = "%$searchQuery%";
    
    // Search Students (enrolled in instructor's courses)
    $studentStmt = $con->prepare("SELECT DISTINCT s.student_id, s.student_code, s.full_name, s.email, 'student' as type 
                                  FROM students s
                                  INNER JOIN student_courses sc ON s.student_id = sc.student_id
                                  INNER JOIN instructor_courses ic ON sc.course_id = ic.course_id
                                  WHERE ic.instructor_id = ? 
                                  AND (s.student_code LIKE ? OR s.full_name LIKE ? OR s.email LIKE ?)
                                  LIMIT 10");
    $studentStmt->bind_param("isss", $instructor_id, $searchTerm, $searchTerm, $searchTerm);
    $studentStmt->execute();
    $studentResults = $studentStmt->get_result();
    while($row = $studentResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Questions
    $questionStmt = $con->prepare("SELECT q.question_id, LEFT(q.question_text, 100) as question_text, c.course_code, 'question' as type 
                                   FROM questions q
                                   INNER JOIN courses c ON q.course_id = c.course_id
                                   INNER JOIN instructor_courses ic ON c.course_id = ic.course_id
                                   WHERE ic.instructor_id = ? 
                                   AND q.question_text LIKE ?
                                   LIMIT 10");
    $questionStmt->bind_param("is", $instructor_id, $searchTerm);
    $questionStmt->execute();
    $questionResults = $questionStmt->get_result();
    while($row = $questionResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Exams
    $examStmt = $con->prepare("SELECT e.exam_id, e.exam_name, c.course_code, 'exam' as type 
                               FROM exams e
                               INNER JOIN courses c ON e.course_id = c.course_id
                               INNER JOIN instructor_courses ic ON c.course_id = ic.course_id
                               WHERE ic.instructor_id = ? 
                               AND e.exam_name LIKE ?
                               LIMIT 10");
    $examStmt->bind_param("is", $instructor_id, $searchTerm);
    $examStmt->execute();
    $examResults = $examStmt->get_result();
    while($row = $examResults->fetch_assoc()) {
        $results[] = $row;
    }
    
    // Search Courses
    $courseStmt = $con->prepare("SELECT c.course_id, c.course_code, c.course_name, 'course' as type 
                                 FROM courses c
                                 INNER JOIN instructor_courses ic ON c.course_id = ic.course_id
                                 WHERE ic.instructor_id = ? 
                                 AND (c.course_code LIKE ? OR c.course_name LIKE ?)
                                 LIMIT 10");
    $courseStmt->bind_param("iss", $instructor_id, $searchTerm, $searchTerm);
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
