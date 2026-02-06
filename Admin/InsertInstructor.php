
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

// Check if user is logged in
if(!isset($_SESSION['username'])){
    header("Location:../auth/staff-login.php");
    exit();
}

// Validate user role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Administrator'){
    SessionManager::destroySession();
    header("Location:../auth/staff-login.php");
    exit();
}

	require_once(__DIR__ . "/../utils/password_helper.php");

	$InstructorCode = $_POST['instID'];
	$Name = $_POST['instName'];
	$Email = $_POST['instEmail'] ?? '';
	$UserName = $_POST['instUName'];
	$Password = $_POST['instPassword'];
	$Department = $_POST['cmbDept'];
	$is_active = $_POST['cmbStatus'];

	// Hash the password before storing
	$hashedPassword = hashPassword($Password);

	// Establish Connection with MYSQL
	$con = require_once(__DIR__ . "/../Connections/OES.php");
	
	// Specify the query to Insert Record - Using prepared statement for security
	$stmt = $con->prepare("INSERT INTO instructors (instructor_code, full_name, email, department_id, username, password, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssssi", $InstructorCode, $Name, $Email, $Department, $UserName, $hashedPassword, $is_active);
	// execute query
	$stmt->execute();
	$stmt->close();
	// Close The Connection
	$con->close ();
	echo '<script type="text/javascript">alert("User Inserted Succesfully");window.location=\'Instructor.php\';</script>';
?>
</body>
</html>
