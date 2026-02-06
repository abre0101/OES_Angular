<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/staff-login.php");
    exit();
}

$Id = $_GET['Id'];
$con = require_once(__DIR__ . "/../Connections/OES.php"); // Auto-fixed connection;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$stmt = $con->prepare("delete FROM instructors where instructor_id=?");
$stmt->bind_param("s", $Id);
$stmt->execute();
$stmt->close();
$con->close();

header("Location: Instructor.php?msg=deleted");
exit();
?>
