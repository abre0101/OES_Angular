<?php
// Database connection configuration
// Supports both local development and Railway deployment

// Check if running on Railway
if (getenv('MYSQLHOST')) {
    // Railway MySQL configuration
    $hostname_OES = getenv('MYSQLHOST');
    $database_OES = getenv('MYSQLDATABASE') ?: 'railway';
    $username_OES = getenv('MYSQLUSER') ?: 'root';
    $password_OES = getenv('MYSQLPASSWORD') ?: '';
    $port_OES = getenv('MYSQLPORT') ?: 3306;
} else {
    // Local development configuration
    $hostname_OES = 'localhost';
    $database_OES = 'oes_professional';
    $username_OES = 'root';
    $password_OES = '';
    $port_OES = 3306;
}

// Create connection
$con = new mysqli($hostname_OES, $username_OES, $password_OES, $database_OES, $port_OES);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Set charset
$con->set_charset("utf8mb4");

// Disable strict mode to allow '0000-00-00' dates
$con->query("SET sql_mode = ''");

return $con;
?>
