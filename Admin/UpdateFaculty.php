<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/staff-login.php");
    exit();
}

$Id = $_GET['FacId'];
$Name = $_POST['faculty_name'];

$con = require_once(__DIR__ . "/../Connections/OES.php"); // Auto-fixed connection;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$stmt = $con->prepare("UPDATE faculties SET faculty_name=? WHERE faculty_id=?");
$stmt->bind_param("ss", $Name, $Id);
$stmt->execute();
$stmt->close();
$con->close();

header("Location: Faculty.php?msg=updated");
exit();
?>
