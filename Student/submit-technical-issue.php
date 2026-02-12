<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Student');

header('Content-Type: application/json');

if(!isset($_SESSION['Name'])){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");

$exam_id = intval($_POST['exam_id'] ?? 0);
$student_id = intval($_POST['student_id'] ?? 0);
$student_name = mysqli_real_escape_string($con, $_POST['student_name'] ?? '');
$issue_type = mysqli_real_escape_string($con, $_POST['issue_type'] ?? '');
$question_number = !empty($_POST['question_number']) ? intval($_POST['question_number']) : null;
$description = mysqli_real_escape_string($con, $_POST['description'] ?? '');

if(empty($exam_id) || empty($student_id) || empty($issue_type) || empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Get exam name for context
$exam_query = "SELECT exam_name, course_id FROM exams WHERE exam_id = ?";
$stmt = $con->prepare($exam_query);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam_result = $stmt->get_result()->fetch_assoc();
$stmt->close();

$exam_name = $exam_result['exam_name'] ?? 'Unknown Exam';

// Build full description with context
$full_description = "EXAM: $exam_name (ID: $exam_id)\n";
$full_description .= "STUDENT: $student_name (ID: $student_id)\n";
$full_description .= "ISSUE TYPE: $issue_type\n";
if($question_number) {
    $full_description .= "QUESTION NUMBER: $question_number\n";
}
$full_description .= "\nDESCRIPTION:\n$description";

// Insert into technical_issues table - matching the existing table structure
$insert_query = "INSERT INTO technical_issues (
    student_id,
    exam_id,
    issue_description,
    status,
    priority,
    reported_at
) VALUES (?, ?, ?, 'pending', 'medium', NOW())";

$stmt = $con->prepare($insert_query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $con->error]);
    exit();
}

$stmt->bind_param("iis", $student_id, $exam_id, $full_description);

if($stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    echo json_encode(['success' => true, 'message' => 'Issue reported successfully']);
} else {
    $error = $stmt->error;
    $stmt->close();
    mysqli_close($con);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $error]);
}
?>
