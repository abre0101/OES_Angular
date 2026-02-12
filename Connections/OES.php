<?php
// Database connection configuration
$hostname_OES = 'localhost';
$database_OES = 'oes_professional';
$username_OES = 'root';
$password_OES = '';

// Create connection
$con = new mysqli($hostname_OES, $username_OES, $password_OES, $database_OES);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Set charset
$con->set_charset("utf8mb4");

return $con;
?>
