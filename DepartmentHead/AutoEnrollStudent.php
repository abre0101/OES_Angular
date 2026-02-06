<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Department Head session
SessionManager::startSession('DepartmentHead');

// Check if user is logged in
if(!isset($_SESSION['Name'])){
    header("Location:../auth/staff-login.php");
    exit();
}

// Validate user role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'DepartmentHead'){
    SessionManager::destroySession();
    header("Location:../auth/staff-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$student_id = $_GET['id'] ?? 0;

if($student_id) {
    // Get student details
    $student_query = "SELECT * FROM students WHERE student_id = ?";
    $stmt = $con->prepare($student_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    
    if($student) {
        // Get courses for this student's department, year, and semester
        // Courses are filtered by department and semester only
        $courses_query = "SELECT course_id FROM courses 
                         WHERE department_id = ? 
                         AND semester = ?
                         AND is_active = 1";
        $stmt = $con->prepare($courses_query);
        $stmt->bind_param("ii", $student['department_id'], $student['semester']);
        $stmt->execute();
        $courses = $stmt->get_result();
        
        $enrolled_count = 0;
        $already_enrolled = 0;
        
        while($course = $courses->fetch_assoc()) {
            // Check if already enrolled
            $check_query = "SELECT * FROM student_courses WHERE student_id = ? AND course_id = ?";
            $check_stmt = $con->prepare($check_query);
            $check_stmt->bind_param("ii", $student_id, $course['course_id']);
            $check_stmt->execute();
            $exists = $check_stmt->get_result()->num_rows > 0;
            
            if(!$exists) {
                // Enroll student in course
                $enroll_query = "INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)";
                $enroll_stmt = $con->prepare($enroll_query);
                $enroll_stmt->bind_param("ii", $student_id, $course['course_id']);
                if($enroll_stmt->execute()) {
                    $enrolled_count++;
                }
            } else {
                $already_enrolled++;
            }
        }
        
        $_SESSION['success'] = "Auto-enrollment complete! Enrolled in $enrolled_count new course(s). Already enrolled in $already_enrolled course(s).";
    } else {
        $_SESSION['error'] = "Student not found.";
    }
} else {
    $_SESSION['error'] = "Invalid student ID.";
}

$con->close();
header("Location: ViewStudent.php?id=$student_id");
exit();
?>
