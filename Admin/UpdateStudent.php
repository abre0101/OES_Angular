<?php
require_once(__DIR__ . "/../utils/session_manager.php");
require_once(__DIR__ . "/../utils/audit_logger.php");
require_once(__DIR__ . "/../utils/audit_helper.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/staff-login.php");
    exit();
}

$Id = $_GET['student_id'];
$Year = $_POST['cmbYear'];
$is_active = $_POST['cmbStatus'];
$Sem = $_POST['cmbSem'];
$Department = $_POST['cmbDep'];

$con = require_once(__DIR__ . "/../Connections/OES.php");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Convert to integers
$Department = intval($Department);
$is_active = intval($is_active);
$Sem = intval($Sem);

// Fetch old data for audit trail
$oldData = fetchRecordForAudit($con, 'students', 'student_id', $Id);

$stmt = $con->prepare("UPDATE students SET department_id=?, is_active=?, semester=?, academic_year=? WHERE student_id=?");
$stmt->bind_param("iiisi", $Department, $is_active, $Sem, $Year, $Id);
$stmt->execute();
$stmt->close();

// Track changes for audit log
$newData = [
    'department_id' => $Department,
    'is_active' => $is_active,
    'semester' => $Sem,
    'academic_year' => $Year
];
$changes = getChangedFields($oldData, $newData);

// Log the update with detailed tracking
if (!empty($changes)) {
    $auditLogger = new AuditLogger($con);
    $studentName = $oldData['full_name'] ?? 'Unknown';
    $studentCode = $oldData['student_code'] ?? '';
    
    $auditLogger->logUpdate(
        $_SESSION['ID'] ?? null,
        'admin',
        'students',
        $Id,
        formatChanges($changes),
        json_encode($newData),
        $changes
    );
}

$con->close();

header("Location: Student.php?msg=updated");
exit();
?>
