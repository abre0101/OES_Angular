<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/institute-login.php");
    exit();
}

$ID = $_GET['ID'];
$con = require_once(__DIR__ . "/../Connections/OES.php"); // Auto-fixed connection;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$stmt = $con->prepare("delete FROM departments where department_id=?");
$stmt->bind_param("s", $ID);
$stmt->execute();
$stmt->close();
$con->close();

header("Location: Department.php?msg=deleted");
exit();
?>
