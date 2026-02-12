<?php
/**
 * Quick Database Import for Railway
 * Run this from command line: php import-db-railway.php
 */

echo "=== Railway Database Import ===\n\n";

// Include database connection
$con = include('Connections/OES.php');

if (!$con) {
    die("✗ Failed to connect to database\n");
}

echo "✓ Connected to database successfully\n";

// Read SQL file
$sqlFile = 'database/oes_professional.sql';
if (!file_exists($sqlFile)) {
    die("✗ SQL file not found: $sqlFile\n");
}

echo "✓ Found SQL file\n";

// Read SQL content
$sql = file_get_contents($sqlFile);

// Remove comments
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

// Split into queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

echo "✓ Processing " . count($queries) . " SQL statements...\n\n";

$success = 0;
$errors = 0;

// Disable foreign key checks
$con->query('SET FOREIGN_KEY_CHECKS=0');

foreach ($queries as $query) {
    if (empty($query) || strlen($query) < 5) continue;
    
    if ($con->multi_query($query) || $con->query($query)) {
        $success++;
        if ($success % 50 == 0) {
            echo "  Processed $success queries...\n";
        }
        // Clear any pending results
        while ($con->more_results()) {
            $con->next_result();
        }
    } else {
        $errors++;
        if ($errors <= 3) {
            echo "  ✗ Error: " . $con->error . "\n";
        }
    }
}

// Re-enable foreign key checks
$con->query('SET FOREIGN_KEY_CHECKS=1');

echo "\n=== IMPORT COMPLETE ===\n";
echo "✓ Success: $success queries\n";
echo "✗ Errors: $errors queries\n";

if ($errors == 0) {
    echo "\n✓ Database imported successfully!\n";
} else {
    echo "\n⚠ Import completed with some errors\n";
}

$con->close();
?>
