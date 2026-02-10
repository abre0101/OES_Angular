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

$admin_id = $_SESSION['ID'];
$success_message = '';
$error_message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $issue_id = mysqli_real_escape_string($con, $_POST['issue_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']);
    $admin_notes = mysqli_real_escape_string($con, $_POST['admin_notes']);
    
    $resolved_at = ($new_status == 'resolved') ? ", resolved_at = NOW(), resolved_by = '$admin_id'" : "";
    
    $update_sql = "UPDATE technical_issues 
                   SET status = '$new_status', admin_notes = '$admin_notes' $resolved_at 
                   WHERE issue_id = '$issue_id'";
    
    if (mysqli_query($con, $update_sql)) {
        $success_message = "Issue status updated successfully.";
    } else {
        $error_message = "Error updating issue status.";
    }
}

// Filter parameters
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$filter_priority = isset($_GET['priority']) ? $_GET['priority'] : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// Build query
$where_clauses = [];
if ($filter_status != 'all') {
    $where_clauses[] = "ti.status = '" . mysqli_real_escape_string($con, $filter_status) . "'";
}
if ($filter_priority != 'all') {
    $where_clauses[] = "ti.priority = '" . mysqli_real_escape_string($con, $filter_priority) . "'";
}
if (!empty($search)) {
    $where_clauses[] = "(s.full_name LIKE '%$search%' OR e.exam_name LIKE '%$search%' OR ti.issue_description LIKE '%$search%')";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

$issues_query = "SELECT ti.*, s.full_name as student_name, s.student_code, s.email as student_email,
                 e.exam_name, c.course_name, c.course_code,
                 a.username as resolved_by_name
                 FROM technical_issues ti
                 INNER JOIN students s ON ti.student_id = s.student_id
                 INNER JOIN exams e ON ti.exam_id = e.exam_id
                 INNER JOIN courses c ON e.course_id = c.course_id
                 LEFT JOIN administrators a ON ti.resolved_by = a.admin_id
                 $where_sql
                 ORDER BY 
                    CASE ti.priority 
                        WHEN 'critical' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                    END,
                    ti.reported_at DESC";

$issues_result = mysqli_query($con, $issues_query);

// Get statistics
$stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN priority = 'critical' THEN 1 ELSE 0 END) as critical
                FROM technical_issues";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Issues Management - Admin Dashboard</title>
    <link href="../assets/css/modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .page-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 2rem;
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(255, 0, 0, 0.05) 100%);
            padding: 2rem;
            border-radius: var(--radius-lg);
            border: 2px solid rgba(220, 53, 69, 0.1);
        }
        
        .page-title-section h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-title-section h1 span {
            -webkit-text-fill-color: initial;
            background: none;
        }
        
        .page-title-section p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 1.05rem;
            font-weight: 500;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 2px solid #e8eef3;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }
        
        .stat-card.total::before { background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); }
        .stat-card.pending::before { background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.resolved::before { background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%); }
        .stat-card.critical::before { background: linear-gradient(90deg, #fa709a 0%, #fee140 100%); }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
        }
        
        .stat-card.total .stat-number { color: #667eea; }
        .stat-card.pending .stat-number { color: #f5576c; }
        .stat-card.resolved .stat-number { color: #00f2fe; }
        .stat-card.critical .stat-number { color: #fa709a; }
        
        .stat-label {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 2px solid #e8eef3;
            margin-bottom: 2rem;
        }
        
        .filter-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e8eef3;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .btn-filter {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
        }
        
        .btn-reset {
            background: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .issues-table-container {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 2px solid #e8eef3;
            overflow-x: auto;
        }
        
        .issues-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .issues-table thead {
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.05) 0%, rgba(0, 85, 170, 0.05) 100%);
        }
        
        .issues-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: var(--primary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e8eef3;
        }
        
        .issues-table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f4f8;
            font-size: 0.95rem;
        }
        
        .issues-table tbody tr {
            transition: all 0.2s ease;
        }
        
        .issues-table tbody tr:hover {
            background: rgba(0, 123, 255, 0.02);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-priority-critical { background: #dc3545; color: white; }
        .badge-priority-high { background: #fd7e14; color: white; }
        .badge-priority-medium { background: #ffc107; color: #000; }
        .badge-priority-low { background: #28a745; color: white; }
        .badge-status-pending { background: #6c757d; color: white; }
        .badge-status-resolved { background: #28a745; color: white; }
        .badge-status-closed { background: #17a2b8; color: white; }
        
        .btn-view {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-dialog {
            background: white;
            border-radius: var(--radius-lg);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 2px solid #e8eef3;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>
    
    <div class="admin-main-content">
        <div class="admin-content">
        
        <!-- Page Header -->
        <div class="page-header-actions">
            <div class="page-title-section">
                <h1><span>⚠️</span> Technical Issues Management</h1>
                <p>Monitor and resolve student-reported technical issues</p>
            </div>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Issues</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card resolved">
                <div class="stat-number"><?php echo $stats['resolved']; ?></div>
                <div class="stat-label">Resolved</div>
            </div>
            <div class="stat-card critical">
                <div class="stat-number"><?php echo $stats['critical']; ?></div>
                <div class="stat-label">Critical Priority</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="resolved" <?php echo $filter_status == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="closed" <?php echo $filter_status == 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Priority</label>
                    <select name="priority" onchange="this.form.submit()">
                        <option value="all" <?php echo $filter_priority == 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="critical" <?php echo $filter_priority == 'critical' ? 'selected' : ''; ?>>Critical</option>
                        <option value="high" <?php echo $filter_priority == 'high' ? 'selected' : ''; ?>>High</option>
                        <option value="medium" <?php echo $filter_priority == 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="low" <?php echo $filter_priority == 'low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn-filter">🔍 Filter</button>
                <a href="TechnicalIssues.php" class="btn-reset">🔄 Reset</a>
            </form>
        </div>
        
        <!-- Issues Table -->
        <div class="issues-table-container">
            <table class="issues-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Exam</th>
                        <th>Issue Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Reported</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($issues_result) > 0): ?>
                        <?php while ($issue = mysqli_fetch_assoc($issues_result)): ?>
                            <tr>
                                <td><strong>#<?php echo $issue['issue_id']; ?></strong></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($issue['student_name']); ?></strong><br>
                                    <small style="color: #6c757d;"><?php echo htmlspecialchars($issue['student_code']); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($issue['exam_name']); ?></strong><br>
                                    <small style="color: #6c757d;"><?php echo htmlspecialchars($issue['course_code']); ?></small>
                                </td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $issue['issue_type'])); ?></td>
                                <td>
                                    <span class="badge badge-priority-<?php echo $issue['priority']; ?>">
                                        <?php echo strtoupper($issue['priority']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-status-<?php echo $issue['status']; ?>">
                                        <?php echo strtoupper($issue['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($issue['reported_at'])); ?></td>
                                <td>
                                    <button class="btn-view" onclick="viewIssue(<?php echo $issue['issue_id']; ?>)">
                                        👁️ View
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: #6c757d;">
                                No issues found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
    
    <!-- View/Update Issue Modal -->
    <div class="modal" id="issueModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h5 class="modal-title">Issue Details</h5>
                <button type="button" class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="issueDetails">
                Loading...
            </div>
        </div>
    </div>
    
    <script>
        function viewIssue(issueId) {
            const modal = document.getElementById('issueModal');
            modal.classList.add('show');
            document.getElementById('issueDetails').innerHTML = '<div style="text-align: center; padding: 2rem;"><div style="font-size: 2rem;">⏳</div><p>Loading...</p></div>';
            
            fetch('ViewIssueDetails.php?issue_id=' + issueId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('issueDetails').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('issueDetails').innerHTML = '<div class="alert alert-danger">Error loading issue details.</div>';
                });
        }
        
        function closeModal() {
            document.getElementById('issueModal').classList.remove('show');
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('issueModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
