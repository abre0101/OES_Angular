
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
	require_once(__DIR__ . "/../utils/password_helper.php");

	  $Id=$_POST['instID'];
         $Name=$_POST['instName']; 
        // $Email=$_POST['excEmail'];
         $UserName=$_POST['instUName'];
         $Password=$_POST['instPassword'];
	$Department=$_POST['cmbDept']; 
	$cmbCourse=$_POST['cmbCourse'];      
	$is_active=$_POST['cmbStatus'];
	 $Sex=$_POST['gender'];

	// Hash the password before storing
	$hashedPassword = hashPassword($Password);

	
	// Establish Connection with MYSQL
	$con = new mysqli("localhost","root");
	// Select Database
	$con->select_db("oes");
	// Specify the query to Insert Record - Using prepared statement for security
	$stmt = $con->prepare("Insert INTO instructors (instructor_id,full_name,Stud_Sex,department_name,username,course_name,password,is_active) values(?,?,?,?,?,?,?,?)");
	$stmt->bind_param("ssssssss", $Id, $Name, $Sex, $Department, $UserName, $cmbCourse, $hashedPassword, $is_active);
	// execute query
	$stmt->execute();
	$stmt->close();
	// Close The Connection
	$con->close ();
	echo '<script type="text/javascript">alert("User Inserted Succesfully");window.location=\'Instructor.php\';</script>';
?>
</body>
</html>
