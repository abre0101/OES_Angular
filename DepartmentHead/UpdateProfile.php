<?php
require_once(__DIR__ . "/../utils/session_manager.php");
require_once(__DIR__ . "/../utils/password_helper.php");

// Start Department Head session
SessionManager::startSession('DepartmentHead');

// Check if user is logged in
if(!isset($_SESSION['ID'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

// Validate user role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'DepartmentHead'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}

$Id = $_SESSION['ID'];
$FullName = $_POST['full_name'] ?? '';
$Email = $_POST['email'] ?? '';
$Phone = $_POST['phone'] ?? '';
$UserName = $_POST['username'] ?? '';

if(empty($UserName)) {
    echo '<script type="text/javascript">alert("Username is required");window.history.back();</script>';
    exit();
}

// Establish Connection
$con = require_once(__DIR__ . "/../Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Update profile (without password)
$stmt = $con->prepare("UPDATE department_heads SET full_name=?, email=?, phone=?, username=? WHERE department_head_id=?");
$stmt->bind_param("ssssi", $FullName, $Email, $Phone, $UserName, $Id);

if($stmt->execute()) {
    $_SESSION['username'] = $UserName;
    $_SESSION['Name'] = $FullName;
    $stmt->close();
    $con->close();
    echo '<script type="text/javascript">alert("Profile Updated Successfully");window.location="Profile.php";</script>';
} else {
    $error = $stmt->error;
    $stmt->close();
    $con->close();
    echo '<script type="text/javascript">alert("Error: ' . $error . '");window.history.back();</script>';
}
?>
