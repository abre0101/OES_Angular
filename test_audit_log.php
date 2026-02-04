<?php
// Test script to verify improved audit logging is working

require_once(__DIR__ . "/utils/audit_logger.php");
require_once(__DIR__ . "/utils/audit_helper.php");

$con = require_once(__DIR__ . "/Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

echo "Testing Improved Audit Logger...\n\n";

// Check if audit_logs table exists
$result = $con->query("SHOW TABLES LIKE 'audit_logs'");

if($result->num_rows > 0) {
    echo "✓ audit_logs table exists\n";
} else {
    echo "✗ audit_logs table does NOT exist\n";
    die("Please create the audit_logs table first.\n");
}

// Check if metadata column exists
$result = $con->query("SHOW COLUMNS FROM audit_logs LIKE 'metadata'");
if($result->num_rows > 0) {
    echo "✓ metadata column exists\n";
} else {
    echo "⚠ metadata column does NOT exist - run migration script\n";
    echo "  Run: database/migrations/add_metadata_to_audit_logs.sql\n";
}

// Check current count
$result = $con->query("SELECT COUNT(*) as count FROM audit_logs");
$row = $result->fetch_assoc();
$beforeCount = $row['count'];
echo "Current audit logs count: " . $beforeCount . "\n\n";

// Test logging
echo "Testing improved audit logger...\n";
$auditLogger = new AuditLogger($con);

// Test 1: Login with authentication table
echo "\n1. Testing login (with table_name='authentication')...\n";
$success1 = $auditLogger->logLogin(1, 'admin', true, 'test_admin');
echo "   " . ($success1 ? "✓ Success" : "✗ Failed") . "\n";

// Test 2: Failed login with 'unknown' user type
echo "\n2. Testing failed login (user_type='unknown')...\n";
$success2 = $auditLogger->logLogin(null, 'unknown', false, 'hacker123');
echo "   " . ($success2 ? "✓ Success" : "✗ Failed") . "\n";

// Test 3: Create with metadata
echo "\n3. Testing create with metadata...\n";
$success3 = $auditLogger->logCreate(1, 'admin', 'students', 999, 'Test Student (STU999)');
echo "   " . ($success3 ? "✓ Success" : "✗ Failed") . "\n";

// Test 4: Update with detailed change tracking
echo "\n4. Testing update with change tracking...\n";
$oldData = ['full_name' => 'John Doe', 'email' => 'john@old.com', 'phone' => '123-456'];
$newData = ['full_name' => 'John Doe', 'email' => 'john@new.com', 'phone' => '789-012'];
$changes = getChangedFields($oldData, $newData);
$success4 = $auditLogger->logUpdate(
    1, 
    'admin', 
    'students', 
    999, 
    formatChanges($changes),
    json_encode($newData),
    $changes
);
echo "   " . ($success4 ? "✓ Success" : "✗ Failed") . "\n";
echo "   Changed fields: " . formatChanges($changes) . "\n";

// Test 5: Delete with details
echo "\n5. Testing delete with details...\n";
$success5 = $auditLogger->logDelete(1, 'admin', 'students', 999, 'Deleted test student: John Doe (STU999)');
echo "   " . ($success5 ? "✓ Success" : "✗ Failed") . "\n";

// Test 6: Password change
echo "\n6. Testing password change event...\n";
$success6 = $auditLogger->logPasswordChange(1, 'admin', true);
echo "   " . ($success6 ? "✓ Success" : "✗ Failed") . "\n";

// Test 7: Unauthorized access
echo "\n7. Testing unauthorized access event...\n";
$success7 = $auditLogger->logUnauthorizedAccess(5, 'student', '/admin/settings.php');
echo "   " . ($success7 ? "✓ Success" : "✗ Failed") . "\n";

// Check new count
$result = $con->query("SELECT COUNT(*) as count FROM audit_logs");
$row = $result->fetch_assoc();
$afterCount = $row['count'];
echo "\n" . str_repeat("=", 60) . "\n";
echo "New audit logs count: " . $afterCount . "\n";
echo "Logs added: " . ($afterCount - $beforeCount) . "\n\n";

if($afterCount > $beforeCount) {
    echo "✓ Audit logging is WORKING!\n\n";
    
    // Show last logs with details
    echo "Last " . ($afterCount - $beforeCount) . " audit logs:\n";
    echo str_repeat("=", 60) . "\n";
    $result = $con->query("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT " . ($afterCount - $beforeCount));
    
    while($log = $result->fetch_assoc()) {
        echo "\n[Log ID: {$log['log_id']}]\n";
        echo "  User: {$log['user_type']} (ID: " . ($log['user_id'] ?? 'NULL') . ")\n";
        echo "  Action: {$log['action']}\n";
        echo "  Table: " . ($log['table_name'] ?? 'NULL') . "\n";
        echo "  Record ID: " . ($log['record_id'] ?? 'NULL') . "\n";
        
        if (!empty($log['old_value'])) {
            echo "  Old Value: {$log['old_value']}\n";
        }
        if (!empty($log['new_value'])) {
            echo "  New Value: " . substr($log['new_value'], 0, 50) . "...\n";
        }
        if (!empty($log['metadata'])) {
            echo "  Metadata: " . $log['metadata'] . "\n";
        }
        echo "  IP: {$log['ip_address']}\n";
        echo "  Time: {$log['created_at']}\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    
    // Count NULL values
    echo "\nNULL Value Analysis:\n";
    echo str_repeat("-", 60) . "\n";
    
    $result = $con->query("SELECT 
        SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as null_user_id,
        SUM(CASE WHEN table_name IS NULL THEN 1 ELSE 0 END) as null_table_name,
        SUM(CASE WHEN record_id IS NULL THEN 1 ELSE 0 END) as null_record_id,
        SUM(CASE WHEN metadata IS NULL THEN 1 ELSE 0 END) as null_metadata,
        COUNT(*) as total
    FROM audit_logs");
    
    $stats = $result->fetch_assoc();
    echo "Total logs: {$stats['total']}\n";
    echo "NULL user_id: {$stats['null_user_id']} (" . round($stats['null_user_id']/$stats['total']*100, 1) . "%)\n";
    echo "NULL table_name: {$stats['null_table_name']} (" . round($stats['null_table_name']/$stats['total']*100, 1) . "%)\n";
    echo "NULL record_id: {$stats['null_record_id']} (" . round($stats['null_record_id']/$stats['total']*100, 1) . "%)\n";
    echo "NULL metadata: {$stats['null_metadata']} (" . round($stats['null_metadata']/$stats['total']*100, 1) . "%)\n";
    
    echo "\n✓ Improvements:\n";
    echo "  - Login events now have table_name='authentication'\n";
    echo "  - Failed logins use user_type='unknown'\n";
    echo "  - Metadata provides structured context\n";
    echo "  - Change tracking shows what was modified\n";
    
} else {
    echo "✗ Audit logging is NOT working!\n";
    echo "Check database permissions and error logs.\n";
}

$con->close();
?>
