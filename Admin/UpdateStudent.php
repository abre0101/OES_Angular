<?php
require_once(__DIR__ . "/../utils/session_manager.php");
require_once(__DIR__ . "/../utils/audit_logger.php");
require_once(__DIR__ . "/../utils/audit_helper.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/staff-login.php");
    exit();
}

$Id = $_GET['student_id'];
$Year = $_POST['cmbYear'];
$is_active = $_POST['cmbStatus'];
$Sem = $_POST['cmbSem'];
$Department = $_POST['cmbDep'];

$con = require_once(__DIR__ . "/../Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Convert to integers
$Department = intval($Department);
$is_active = intval($is_active);
$Sem = intval($Sem);

// Fetch old data for audit trail
$oldData = fetchRecordForAudit($con, 'students', 'student_id', $Id);

$stmt = $con->prepare("UPDATE students SET department_id=?, is_active=?, semester=?, academic_year=? WHERE student_id=?");
$stmt->bind_param("iiisi", $Department, $is_active, $Sem, $Year, $Id);
$stmt->execute();
$stmt->close();

// Check if semester changed - if so, re-enroll in new semester courses
if($oldData && $Sem != $oldData['semester']) {
    // Remove old enrollments
    $delete_query = "DELETE FROM student_courses WHERE student_id = ?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param("i", $Id);
    $delete_stmt->execute();
    
    // Enroll in new semester courses
    $courses_query = "SELECT course_id FROM courses 
                     WHERE department_id = ? 
                     AND semester = ?
                     AND is_active = 1";
    $course_stmt = $con->prepare($courses_query);
    $course_stmt->bind_param("ii", $Department, $Sem);
    $course_stmt->execute();
    $courses = $course_stmt->get_result();
    
    $enrolled_count = 0;
    while($course = $courses->fetch_assoc()) {
        $enroll_query = "INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)";
        $enroll_stmt = $con->prepare($enroll_query);
        $enroll_stmt->bind_param("ii", $Id, $course['course_id']);
        if($enroll_stmt->execute()) {
            $enrolled_count++;
        }
    }
}

// Track changes for audit log
$newData = [
    'department_id' => $Department,
    'is_active' => $is_active,
    'semester' => $Sem,
    'academic_year' => $Year
];
$changes = getChangedFields($oldData, $newData);

// Log the update with detailed tracking
if (!empty($changes)) {
    $auditLogger = new AuditLogger($con);
    $studentName = $oldData['full_name'] ?? 'Unknown';
    $studentCode = $oldData['student_code'] ?? '';
    
    $auditLogger->logUpdate(
        $_SESSION['ID'] ?? null,
        'admin',
        'students',
        $Id,
        formatChanges($changes),
        json_encode($newData),
        $changes
    );
}

$con->close();

header("Location: Student.php?msg=updated");
exit();
?>
