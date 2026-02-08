<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Create Student</title>
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

	// Function to generate next student code
	function generateNextStudentCode($con) {
		$query = "SELECT student_code FROM students WHERE student_code LIKE 'STU%' ORDER BY student_code DESC LIMIT 1";
		$result = $con->query($query);
		
		if($result && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$lastCode = $row['student_code'];
			// Extract number from STU004 format
			$number = intval(substr($lastCode, 3));
			$nextNumber = $number + 1;
			return 'STU' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
		} else {
			// First student
			return 'STU001';
		}
	}

	// Establish Connection with MYSQL
	$con = require_once(__DIR__ . "/../Connections/OES.php"); // Auto-fixed connection;
	
	// Auto-generate student code
	$ID = generateNextStudentCode($con);
	
	$Name=$_POST['txtName'];
	$Email=$_POST['txtEmail'] ?? null;
	$Phone=$_POST['txtPhone'] ?? null;
        $StudDept=intval($_POST['cmbDept']); // Convert to integer (department_id)
        $StudYear=$_POST['cmbYear'];
        $StudSem=intval($_POST['cmbSem']); // Convert to integer
        $UserName=$_POST['txtUserName'];
        $Password=$_POST['txtPassword'];
        $Sex=$_POST['gender'];
        $is_active=intval($_POST['cmbStatus']); // Convert to integer
	
	// Debug: Show what was received
	error_log("Department received: " . $_POST['cmbDept'] . " (converted to: " . $StudDept . ")");
	
	// Validate department exists
	if($StudDept <= 0) {
		echo '<script type="text/javascript">alert("Please select a valid department! Received value: ' . htmlspecialchars($_POST['cmbDept']) . '");window.history.back();</script>';
		exit();
	}
	
	// Check if department exists
	$check_dept = $con->prepare("SELECT department_id FROM departments WHERE department_id = ?");
	$check_dept->bind_param("i", $StudDept);
	$check_dept->execute();
	$dept_result = $check_dept->get_result();
	if($dept_result->num_rows == 0) {
		echo '<script type="text/javascript">alert("Selected department does not exist! Please select a valid department.");window.history.back();</script>';
		exit();
	}

	// Hash the password before storing
	$hashedPassword = hashPassword($Password);

	// Specify the query to Insert Record with correct column names
	$stmt = $con->prepare("INSERT INTO students(student_code, full_name, email, phone, department_id, academic_year, semester, gender, username, password, is_active) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("ssssisssssi", $ID, $Name, $Email, $Phone, $StudDept, $StudYear, $StudSem, $Sex, $UserName, $hashedPassword, $is_active);
	// execute query
	if($stmt->execute()) {
		$student_id = $con->insert_id;
		
		// Automatically enroll student in courses for their department and semester
		$courses_query = "SELECT course_id FROM courses 
						 WHERE department_id = ? 
						 AND semester = ?
						 AND is_active = 1";
		$course_stmt = $con->prepare($courses_query);
		$course_stmt->bind_param("ii", $StudDept, $StudSem);
		$course_stmt->execute();
		$courses = $course_stmt->get_result();
		
		$enrolled_count = 0;
		while($course = $courses->fetch_assoc()) {
			$enroll_query = "INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)";
			$enroll_stmt = $con->prepare($enroll_query);
			$enroll_stmt->bind_param("ii", $student_id, $course['course_id']);
			if($enroll_stmt->execute()) {
				$enrolled_count++;
			}
		}
		
		echo '<script type="text/javascript">alert("New Student Inserted Successfully with Code: ' . $ID . ' and enrolled in ' . $enrolled_count . ' course(s)!");window.location=\'Student.php\';</script>';
	} else {
		echo '<script type="text/javascript">alert("Error inserting student: ' . $con->error . '");window.history.back();</script>';
	}
	
	$stmt->close();
	// Close The Connection
	$con->close();

?>
</body>
</html>
