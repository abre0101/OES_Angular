<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/institute-login.php");
    exit();
}

require_once(__DIR__ . "/../utils/password_helper.php");

$Id = $_GET['Id'];
$UserName = $_POST['txtUser'];
$Password = $_POST['txtPass'];

$con = require_once(__DIR__ . "/../Connections/OES.php"); // Auto-fixed connection;

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Hash the password before updating
$hashedPassword = hashPassword($Password);

$stmt = $con->prepare("UPDATE administrators SET username=?, password=? WHERE admin_id=?");
$stmt->bind_param("sss", $UserName, $hashedPassword, $Id);
$stmt->execute();
$stmt->close();
$con->close();

// Update session username if changed
$_SESSION['username'] = $UserName;

header("Location: Profile.php?msg=updated");
exit();
?>
