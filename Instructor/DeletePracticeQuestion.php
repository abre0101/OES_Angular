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

$practice_id = $_GET['id'] ?? 0;

if(!$practice_id) {
    header("Location: ManagePracticeQuestions.php");
    exit();
}

// Establish Connection
$con = require_once(__DIR__ . "/../Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Delete the practice question
$stmt = $con->prepare("DELETE FROM practice_questions WHERE practice_id = ?");
$stmt->bind_param("i", $practice_id);

if($stmt->execute()) {
    $stmt->close();
    $con->close();
    echo '<script type="text/javascript">alert("Practice Question Deleted Successfully");window.location="ManagePracticeQuestions.php";</script>';
} else {
    $error = $stmt->error;
    $stmt->close();
    $con->close();
    echo '<script type="text/javascript">alert("Error: ' . $error . '");window.history.back();</script>';
}
?>
