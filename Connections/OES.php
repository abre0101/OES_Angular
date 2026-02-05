<?php
// Database connection configuration
// Support both Railway (production) and local development

// Railway environment variables (production)
// Railway provides: MYSQLHOST, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD, MYSQLPORT
$hostname_OES = getenv('MYSQLHOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
$database_OES = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: 'oes_professional';
$username_OES = getenv('MYSQLUSER') ?: getenv('MYSQL_USER') ?: 'root';
$password_OES = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';
$port_OES = getenv('MYSQLPORT') ?: getenv('MYSQL_PORT') ?: 3306;

// Create connection with port support
$con = new mysqli($hostname_OES, $username_OES, $password_OES, $database_OES, $port_OES);

// Check connection
if ($con->connect_error) {
    // Log error for debugging (in production, log to file instead of displaying)
    error_log("Database connection failed: " . $con->connect_error);
    die("Connection failed. Please check database configuration.");
}

// Set charset to utf8mb4 for better Unicode support
$con->set_charset("utf8mb4");

// Return connection object
return $con;
?>
