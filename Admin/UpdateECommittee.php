<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/institute-login.php");
    exit();
}

$Id = $_GET['ID'];
$is_active = $_POST['cmbStatus'];
$Department = $_POST['cmbDep'];

$con = require_once(__DIR__ . "/../Connections/OES.php"); // Auto-fixed connection;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Convert to integers
$Department = intval($Department);
$is_active = intval($is_active);

$stmt = $con->prepare("UPDATE exam_committee_members SET department_id=?, is_active=? WHERE committee_member_id=?");
$stmt->bind_param("iis", $Department, $is_active, $Id);
$stmt->execute();
$stmt->close();
$con->close();

header("Location: DepartmentHead.php?msg=updated");
exit();
?>
