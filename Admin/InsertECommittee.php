
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
    header("Location:../auth/institute-login.php");
    exit();
}

// Validate user role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Administrator'){
    SessionManager::destroySession();
    header("Location:../auth/institute-login.php");
    exit();
}

	require_once(__DIR__ . "/../utils/password_helper.php");

        $Id=$_POST['excID'];
         $Name=$_POST['excName']; 
         $Email=$_POST['excEmail'];
         $UserName=$_POST['excUName'];
         $Password=$_POST['excPassword'];
	$Department=$_POST['cmbDep'];       
	$is_active=$_POST['cmbStatus'];
	
	// Hash the password before storing
	$hashedPassword = hashPassword($Password);
	
	// Establish Connection with MYSQL
	$con = new mysqli("localhost","root");
	// Select Database
	$con->select_db("oes");
	// Specify the query to Insert Record - Using prepared statement for security
	$stmt = $con->prepare("Insert INTO exam_committee_members (committee_member_id,full_name,email,username,password,department_name,is_active) values(?,?,?,?,?,?,?)");
	$stmt->bind_param("sssssss", $Id, $Name, $Email, $UserName, $hashedPassword, $Department, $is_active);
	// execute query
	$stmt->execute();
	$stmt->close();
	// Close The Connection
	$con->close ();
	echo '<script type="text/javascript">alert("User Inserted Succesfully");window.location=\'ECommittee.php\';</script>';

?>
</body>
</html>
