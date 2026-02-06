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

$question_id = $_GET['id'] ?? 0;

if(!$question_id) {
    header("Location: ManageQuestions.php");
    exit();
}

// Establish Connection
$con = require_once(__DIR__ . "/../Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Delete the question
$stmt = $con->prepare("DELETE FROM questions WHERE question_id = ?");
$stmt->bind_param("i", $question_id);

if($stmt->execute()) {
    $stmt->close();
    $con->close();
    echo '<script type="text/javascript">alert("Question Deleted Successfully");window.location="ManageQuestions.php";</script>';
} else {
    $error = $stmt->error;
    $stmt->close();
    $con->close();
    echo '<script type="text/javascript">alert("Error: ' . $error . '");window.history.back();</script>';
}
?>
