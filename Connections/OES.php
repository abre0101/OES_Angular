<?php
// Database connection configuration
// Supports both local development and Railway deployment

// Check if running on Railway - prioritize internal connection
if (getenv('MYSQLHOST')) {
    // Use Railway's internal MySQL host (mysql.railway.internal)
    $hostname_OES = getenv('MYSQLHOST');
    $database_OES = getenv('MYSQLDATABASE') ?: 'railway';
    $username_OES = getenv('MYSQLUSER') ?: 'root';
    $password_OES = getenv('MYSQLPASSWORD') ?: '';
    $port_OES = getenv('MYSQLPORT') ?: 3306;
} elseif (getenv('MYSQL_PUBLIC_URL')) {
    // Parse MySQL PUBLIC URL for external connections
    $url = parse_url(getenv('MYSQL_PUBLIC_URL'));
    $hostname_OES = $url['host'];
    $database_OES = ltrim($url['path'], '/');
    $username_OES = $url['user'];
    $password_OES = $url['pass'];
    $port_OES = $url['port'] ?? 3306;
} elseif (getenv('MYSQL_URL')) {
    // Parse MySQL URL: mysql://user:password@host:port/database
    $url = parse_url(getenv('MYSQL_URL'));
    $hostname_OES = $url['host'];
    $database_OES = ltrim($url['path'], '/');
    $username_OES = $url['user'];
    $password_OES = $url['pass'];
    $port_OES = $url['port'] ?? 3306;
} elseif (getenv('MYSQL_HOST') || getenv('DB_HOST')) {
    // Railway MySQL configuration - try all possible variable names
    $hostname_OES = getenv('MYSQL_HOST') ?: getenv('DB_HOST');
    $database_OES = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'railway';
    $username_OES = getenv('DB_USER') ?: 'root';
    $password_OES = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';
    $port_OES = getenv('MYSQL_PORT') ?: getenv('DB_PORT') ?: 3306;
} else {
    // Local development configuration
    $hostname_OES = 'localhost';
    $database_OES = 'oes_professional';
    $username_OES = 'root';
    $password_OES = '';
    $port_OES = 3306;
}

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Initialize connection
    $con = mysqli_init();
    
    // Set connection options for MySQL 8 compatibility
    $con->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    
    // Connect to database
    $con->real_connect($hostname_OES, $username_OES, $password_OES, $database_OES, $port_OES);
    
    // Set charset to utf8mb4 for better Unicode support
    $con->set_charset("utf8mb4");
    
} catch (mysqli_sql_exception $e) {
    // Log error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed. Please check database configuration.");
}
// Return connection object
return $con;
?>
