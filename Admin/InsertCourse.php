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

	$CourseCode = $_POST['txtCourseCode'];
	$Name=$_POST['txtDeptName'];
	$Credit=$_POST['txtDeptCredit'];
	$Sem=$_POST['cmbSem'];
	$Dept=$_POST['cmbDept'];
	$Inst=$_POST['cmbInst'];
	
	// Establish Connection with MYSQL
	$con = require_once(__DIR__ . "/../Connections/OES.php");
	
	// Convert to integers
	$Sem = intval($Sem);
	$Dept = intval($Dept);
	$Inst = intval($Inst);
	
	// Insert course
	$sql = "INSERT INTO courses (course_code, course_name, credit_hours, semester, department_id) 
	        VALUES('".$CourseCode."','".$Name."','".$Credit."',".$Sem.",".$Dept.")";
	$con->query($sql);
	
	// Get the inserted course_id
	$course_id = $con->insert_id;
	
	// Insert instructor assignment
	$sql2 = "INSERT INTO instructor_courses (course_id, instructor_id) 
	         VALUES(".$course_id.",".$Inst.")";
	$con->query($sql2);
	
	// Close The Connection
	$con->close();
	echo '<script type="text/javascript">alert("New Course Inserted Successfully");window.location=\'Course.php\';</script>';

?>
</body>
</html>
