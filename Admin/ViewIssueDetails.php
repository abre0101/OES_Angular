<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

// Check if user is logged in
if(!isset($_SESSION['username'])){
    header("Location:../auth/staff-login.php");
    exit();
}

// Validate user role - only administrators can access this page
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Administrator'){
    SessionManager::destroySession();
    header("Location:../auth/staff-login.php");
    exit();
}

$con = require_once('../Connections/OES.php');

if (!isset($_GET['issue_id'])) {
    echo '<div class="alert alert-danger">No issue ID provided.</div>';
    exit();
}

$issue_id = mysqli_real_escape_string($con, $_GET['issue_id']);
$admin_id = $_SESSION['ID'];

$query = "SELECT ti.*, s.full_name as student_name, s.student_code, s.email as student_email,
          e.exam_name, e.exam_date, e.start_time, e.end_time,
          c.course_name, c.course_code,
          a.username as resolved_by_name
          FROM technical_issues ti
          INNER JOIN students s ON ti.student_id = s.student_id
          INNER JOIN exams e ON ti.exam_id = e.exam_id
          INNER JOIN courses c ON e.course_id = c.course_id
          LEFT JOIN administrators a ON ti.resolved_by = a.admin_id
          WHERE ti.issue_id = '$issue_id'";

$result = mysqli_query($con, $query);
$issue = mysqli_fetch_assoc($result);

if (!$issue) {
    echo '<div class="alert alert-danger">Issue not found.</div>';
    exit();
}
?>

<div class="issue-details">
    <div class="row">
        <div class="col-md-6">
            <h6><strong>Student Information</strong></h6>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($issue['student_name']); ?></p>
            <p><strong>Student Code:</strong> <?php echo htmlspecialchars($issue['student_code']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($issue['student_email']); ?></p>
        </div>
        <div class="col-md-6">
            <h6><strong>Exam Information</strong></h6>
            <p><strong>Exam:</strong> <?php echo htmlspecialchars($issue['exam_name']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($issue['course_name']) . ' (' . htmlspecialchars($issue['course_code']) . ')'; ?></p>
            <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($issue['exam_date'])); ?></p>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-md-12">
            <h6><strong>Issue Details</strong></h6>
            <p><strong>Issue Type:</strong> 
                <span class="badge badge-secondary"><?php echo ucfirst(str_replace('_', ' ', $issue['issue_type'])); ?></span>
            </p>
            <p><strong>Priority:</strong> 
                <span class="badge badge-priority-<?php echo $issue['priority']; ?>"><?php echo strtoupper($issue['priority']); ?></span>
            </p>
            <p><strong>Status:</strong> 
                <span class="badge badge-status-<?php echo $issue['status']; ?>"><?php echo strtoupper($issue['status']); ?></span>
            </p>
            <p><strong>Reported At:</strong> <?php echo date('M d, Y H:i:s', strtotime($issue['reported_at'])); ?></p>
            <?php if ($issue['resolved_at']): ?>
                <p><strong>Resolved At:</strong> <?php echo date('M d, Y H:i:s', strtotime($issue['resolved_at'])); ?></p>
                <p><strong>Resolved By:</strong> <?php echo htmlspecialchars($issue['resolved_by_name']); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-md-12">
            <h6><strong>Issue Description</strong></h6>
            <div class="alert alert-light">
                <?php echo nl2br(htmlspecialchars($issue['issue_description'])); ?>
            </div>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-md-12">
            <h6><strong>System Information</strong></h6>
            <div class="system-info-box">
                <p><strong>Browser:</strong> <?php echo htmlspecialchars($issue['browser_info'] ?: 'Not captured'); ?></p>
                <p><strong>Operating System:</strong> <?php echo htmlspecialchars($issue['os_info'] ?: 'Not captured'); ?></p>
                <p><strong>Screen Resolution:</strong> <?php echo htmlspecialchars($issue['screen_resolution'] ?: 'Not captured'); ?></p>
                <p><strong>IP Address:</strong> <?php echo htmlspecialchars($issue['ip_address'] ?: 'Not captured'); ?></p>
            </div>
        </div>
    </div>
    
    <?php if ($issue['admin_notes']): ?>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h6><strong>Admin Notes</strong></h6>
            <div class="alert alert-info">
                <?php echo nl2br(htmlspecialchars($issue['admin_notes'])); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <hr>
    
    <form method="POST" action="TechnicalIssues.php">
        <input type="hidden" name="issue_id" value="<?php echo $issue['issue_id']; ?>">
        <input type="hidden" name="update_status" value="1">
        
        <div class="form-group">
            <label><strong>Update Status</strong></label>
            <select name="status" class="form-control" required>
                <option value="pending" <?php echo $issue['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="resolved" <?php echo $issue['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                <option value="closed" <?php echo $issue['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>
        
        <div class="form-group">
            <label><strong>Admin Notes</strong></label>
            <textarea name="admin_notes" class="form-control" rows="4" placeholder="Add notes about resolution or actions taken..."><?php echo htmlspecialchars($issue['admin_notes']); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save"></i> Update Issue
        </button>
    </form>
</div>

<style>
    .badge-priority-critical { background: #dc3545; color: white; }
    .badge-priority-high { background: #fd7e14; color: white; }
    .badge-priority-medium { background: #ffc107; color: #000; }
    .badge-priority-low { background: #28a745; color: white; }
    .badge-status-pending { background: #6c757d; color: white; }
    .badge-status-resolved { background: #28a745; color: white; }
    .badge-status-closed { background: #17a2b8; color: white; }
    .system-info-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        font-size: 0.9em;
    }
    .system-info-box p {
        margin-bottom: 8px;
    }
</style>
