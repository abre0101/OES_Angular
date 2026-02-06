<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Insert Department</title>
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

	$DeptCode = $_POST['txtDeptCode'];
	$Name=$_POST['txtName'];
	$DeptFacultyId=$_POST['cmbFacult'];
	
	// Establish Connection with MYSQL
	$con = require_once(__DIR__ . "/../Connections/OES.php");
	
	// Specify the query to Insert Record
	$sql = "INSERT INTO departments (department_code, department_name, faculty_id) VALUES('".$DeptCode."','".$Name."',".$DeptFacultyId.")";
	// execute query
	$con->query($sql);
	// Close The Connection
	$con->close ();
	echo '<script type="text/javascript">alert("New Department Inserted Succesfully");window.location=\'Department.php\';</script>';

?>

</body>
</html>

