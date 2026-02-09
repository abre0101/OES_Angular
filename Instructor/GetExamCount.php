<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Instructor');

if(!isset($_SESSION['ID'])){
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");

$course_id = intval($_GET['course_id'] ?? 0);
$category_id = intval($_GET['category_id'] ?? 0);

if($course_id && $category_id) {
    // Count existing exams for this course and category (excluding rejected)
    $stmt = $con->prepare("SELECT COUNT(*) as count 
                          FROM exams 
                          WHERE course_id = ? 
                          AND exam_category_id = ? 
                          AND approval_status NOT IN ('rejected')");
    $stmt->bind_param("ii", $course_id, $category_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode(['count' => $result['count']]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
}

$con->close();
?>
