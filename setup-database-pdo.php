<?php
/**
 * Database Setup Script for Railway (PDO Version)
 * Access: https://your-app.up.railway.app/setup-database-pdo.php?key=dmu2026setup
 */

$SETUP_KEY = 'dmu2026setup';
$provided_key = $_GET['key'] ?? '';

if ($provided_key !== $SETUP_KEY) {
    die('Access denied. Provide the correct setup key in URL: ?key=dmu2026setup');
}

echo "<h1>Database Setup for Railway (PDO)</h1>";
echo "<p>Starting database import...</p>";
echo "<pre>";

// Get database credentials
if (getenv('MYSQLHOST')) {
    $hostname = getenv('MYSQLHOST');
    $database = getenv('MYSQLDATABASE') ?: 'railway';
    $username = getenv('MYSQLUSER') ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: '';
    $port = getenv('MYSQLPORT') ?: 3306;
} elseif (getenv('MYSQL_URL')) {
    $url = parse_url(getenv('MYSQL_URL'));
    $hostname = $url['host'];
    $database = ltrim($url['path'], '/');
    $username = $url['user'];
    $password = $url['pass'];
    $port = $url['port'] ?? 3306;
} else {
    $hostname = 'localhost';
    $database = 'oes_professional';
    $username = 'root';
    $password = '';
    $port = 3306;
}

try {
    // Create PDO connection (handles MySQL 8 auth better)
    $dsn = "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    echo "✓ Connected to database successfully\n";
    echo "  Host: $hostname:$port\n";
    echo "  Database: $database\n\n";
    
} catch (PDOException $e) {
    die("✗ Connection failed: " . $e->getMessage() . "\n");
}

// Read SQL file
$sqlFile = 'database/oes_professional.sql';
if (!file_exists($sqlFile)) {
    die("✗ SQL file not found: $sqlFile\n");
}

echo "✓ Found SQL file: $sqlFile\n";
echo "✓ File size: " . round(filesize($sqlFile) / 1024, 2) . " KB\n\n";

// Read SQL content
$sql = file_get_contents($sqlFile);

// Remove comments
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

// Split into queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

echo "Processing " . count($queries) . " SQL statements...\n\n";

$success = 0;
$errors = 0;
$errorMessages = [];

// Disable foreign key checks
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

foreach ($queries as $index => $query) {
    if (empty($query) || strlen($query) < 5) continue;
    
    try {
        $pdo->exec($query);
        $success++;
        if ($success % 20 == 0) {
            echo "✓ Processed $success queries...\n";
            flush();
        }
    } catch (PDOException $e) {
        $errors++;
        $errorMessages[] = "Query " . ($index + 1) . ": " . $e->getMessage();
        if ($errors <= 5) {
            echo "✗ Error in query " . ($index + 1) . ": " . $e->getMessage() . "\n";
        }
    }
}

// Re-enable foreign key checks
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

echo "\n" . str_repeat("=", 60) . "\n";
echo "DATABASE IMPORT COMPLETED!\n";
echo str_repeat("=", 60) . "\n";
echo "✓ Successful queries: $success\n";
echo "✗ Failed queries: $errors\n";

if ($errors > 0 && $errors <= 5) {
    echo "\nError details:\n";
    foreach ($errorMessages as $msg) {
        echo "  - $msg\n";
    }
} elseif ($errors > 5) {
    echo "\n(Showing first 5 errors only)\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "NEXT STEPS:\n";
echo str_repeat("=", 60) . "\n";
echo "1. Delete setup files for security\n";
echo "2. Visit your application homepage\n";
echo "3. Login with default credentials and change password\n";
echo "\n";

echo "</pre>";
echo "<p><strong>Setup completed! Please delete setup files now.</strong></p>";
?>
