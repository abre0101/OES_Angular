<?php
/**
 * Web-based Migration Runner
 * Navigate to this file to run database migrations
 * Example: http://localhost/database/migrations/run-migration-web.php
 */

// Security: Only allow from localhost or add authentication
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Access denied. This script can only be run from localhost.');
}

$con = require_once(__DIR__ . "/../../Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Migration Runner</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #004085; background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Database Migration Runner</h1>";

// Check if migration should be run
if (isset($_POST['run_migration'])) {
    $migration_file = __DIR__ . '/enhance_technical_issues.sql';
    
    if (!file_exists($migration_file)) {
        echo "<div class='error'>Migration file not found: enhance_technical_issues.sql</div>";
    } else {
        echo "<div class='info'>Running migration: enhance_technical_issues.sql</div>";
        
        $sql = file_get_contents($migration_file);
        
        // Split by semicolon to execute multiple statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($statements as $statement) {
            if (empty($statement) || strpos($statement, '--') === 0) {
                continue;
            }
            
            if ($con->query($statement) === TRUE) {
                $success_count++;
                echo "<div class='success'>✓ Executed successfully</div>";
                echo "<pre>" . htmlspecialchars(substr($statement, 0, 200)) . "...</pre>";
            } else {
                $error_count++;
                echo "<div class='error'>✗ Error: " . $con->error . "</div>";
                echo "<pre>" . htmlspecialchars(substr($statement, 0, 200)) . "...</pre>";
            }
        }
        
        echo "<div class='info'><strong>Summary:</strong> $success_count successful, $error_count errors</div>";
        
        if ($error_count === 0) {
            echo "<div class='success'><strong>Migration completed successfully!</strong></div>";
            echo "<p><a href='../../Admin/TechnicalIssues.php'>Go to Technical Issues Dashboard</a></p>";
        }
    }
} else {
    // Show migration info
    echo "<div class='info'>
        <h3>Technical Issues Enhancement Migration</h3>
        <p>This migration will enhance the technical_issues table with:</p>
        <ul>
            <li>Issue type categorization</li>
            <li>Browser and OS information capture</li>
            <li>Screen resolution tracking</li>
            <li>IP address logging</li>
            <li>Screenshot path support</li>
            <li>Admin notes field</li>
            <li>Resolution tracking</li>
            <li>Priority levels</li>
            <li>Performance indexes</li>
        </ul>
        <p><strong>Note:</strong> This is safe to run. It only adds new columns and indexes.</p>
    </div>";
    
    // Check if columns already exist
    $check_query = "SHOW COLUMNS FROM technical_issues LIKE 'issue_type'";
    $result = $con->query($check_query);
    
    if ($result && $result->num_rows > 0) {
        echo "<div class='error'><strong>Warning:</strong> Migration may have already been run. The 'issue_type' column already exists.</div>";
    }
    
    echo "<form method='POST'>
        <button type='submit' name='run_migration'>Run Migration Now</button>
    </form>";
}

echo "</body></html>";

$con->close();
?>
