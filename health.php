<?php
// Health check endpoint for Railway
header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server' => 'Railway'
];

// Check database connection (optional)
try {
    $con = require_once(__DIR__ . "/Connections/OES.php");
    if ($con && !$con->connect_error) {
        $health['database'] = 'connected';
    } else {
        $health['database'] = 'disconnected';
        $health['status'] = 'degraded';
    }
    if ($con) {
        $con->close();
    }
} catch (Exception $e) {
    $health['database'] = 'error';
    $health['database_error'] = $e->getMessage();
    $health['status'] = 'degraded';
}

http_response_code(200);
echo json_encode($health, JSON_PRETTY_PRINT);
?>
