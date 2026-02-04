<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Insert Faculty</title>
</head>

<body>
<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

// Check if user is logged in
if(!isset($_SESSION['username'])){
    header("Location:../auth/institute-login.php");
    exit();
}

// Validate user role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Administrator'){
    SessionManager::destroySession();
    header("Location:../auth/institute-login.php");
    exit();
}

	$FacultyCode = $_POST['txtFacultyCode'];
	$Name=$_POST['txtFaculty'];
	
	// Establish Connection with MYSQL
	$con = require_once(__DIR__ . "/../Connections/OES.php");
	
	// Specify the query to Insert Record
	$sql = "INSERT INTO faculties (faculty_code, faculty_name) VALUES('".$FacultyCode."','".$Name."')";
	// execute query
	$con->query($sql);

	//mysql_query ($sql,$con);
	// Close The Connection
	$con->close();
	//mysql_close ($con);
	echo '<script type="text/javascript">alert("New Faculty Inserted Succesfully");window.location=\'Faculty.php\';</script>';

?>
</body>
</html>
