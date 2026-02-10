<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Student session
SessionManager::startSession('Student');

// Check if user is logged in
if(!isset($_SESSION['Name'])){
    header("Location: ../index.php");
    exit();
}

// Validate user role - only students can access this page
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Student'){
    SessionManager::destroySession();
    header("Location: ../auth/student-login.php");
    exit();
}

$con = require_once('../Connections/OES.php');

$student_id = $_SESSION['ID'];

// Get student's reported issues
$issues_query = "SELECT ti.*, e.exam_name, c.course_name, c.course_code
                 FROM technical_issues ti
                 INNER JOIN exams e ON ti.exam_id = e.exam_id
                 INNER JOIN courses c ON e.course_id = c.course_id
                 WHERE ti.student_id = '$student_id'
                 ORDER BY ti.reported_at DESC";
$issues_result = mysqli_query($con, $issues_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reported Issues - Debre Markos University Health Campus</title>
    <link href="../assets/css/modern-v2.css" rel="stylesheet">
    <link href="../assets/css/student-modern.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'includes/modern-header-styles.php'; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../images/istockphoto-1772381872-612x612.jpg') center/cover no-repeat;
            opacity: 0.35;
            z-index: 1;
            pointer-events: none;
        }

        .main-content {
            flex: 1;
            position: relative;
            z-index: 200;
            padding: 2rem 0;
            min-height: calc(100vh - 300px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 200;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
            border-radius: 25px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(23, 162, 184, 0.5);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            animation: fadeInUp 0.8s ease;
            position: relative;
            z-index: 200;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.875rem 1.75rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-report-new {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }

        .btn-report-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(220, 53, 69, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
        }

        .issue-card {
            background: #ffffff !important;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease;
            position: relative;
            z-index: 200;
        }

        .issue-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
            border-color: #17a2b8;
        }

        .issue-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .issue-title {
            flex: 1;
        }

        .issue-title h5 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a2b4a;
            margin-bottom: 0.5rem;
        }

        .issue-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-end;
        }

        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-priority-critical { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
        .badge-priority-high { background: linear-gradient(135deg, #fd7e14 0%, #e8590c 100%); color: white; }
        .badge-priority-medium { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #000; }
        .badge-priority-low { background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; }
        .badge-status-pending { background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; }
        .badge-status-resolved { background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; }
        .badge-status-closed { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; }

        .issue-details {
            color: #495057;
            line-height: 1.8;
        }

        .issue-details p {
            margin-bottom: 0.75rem;
        }

        .issue-details strong {
            color: #1a2b4a;
            font-weight: 700;
        }

        .admin-response {
            background: linear-gradient(135deg, #e7f3ff 0%, #cfe2ff 100%) !important;
            padding: 1.5rem;
            border-radius: 15px;
            margin-top: 1.5rem;
            border-left: 5px solid #0d6efd;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
            position: relative;
            z-index: 200;
        }

        .admin-response strong {
            color: #0d6efd;
            font-size: 1.1rem;
        }

        .no-issues {
            background: #ffffff !important;
            border-radius: 25px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
            border: 2px solid #e0e0e0;
            animation: fadeInUp 0.8s ease;
            position: relative;
            z-index: 200;
        }

        .no-issues i {
            color: #28a745;
            margin-bottom: 1.5rem;
        }

        .no-issues h4 {
            font-size: 2rem;
            font-weight: 800;
            color: #1a2b4a;
            margin-bottom: 1rem;
        }

        .no-issues p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                text-align: center;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .page-actions {
                width: 100%;
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Footer Styling */
        .modern-footer {
            background: rgba(26, 43, 74, 0.95);
            backdrop-filter: blur(20px);
            padding: 2rem 0;
            margin-top: 3rem;
            border-top: 2px solid rgba(212, 175, 55, 0.3);
            position: relative;
            z-index: 100;
        }
        
        .modern-footer .container {
            text-align: center;
        }
        
        .modern-footer p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="modern-header">
        <div class="header-top">
            <div class="container">
                <div class="university-info">
                    <img src="../images/logo1.png" alt="Debre Markos University Health Campus" class="university-logo" onerror="this.style.display='none'">
                    <div class="university-name">
                        <h1>Debre Markos University Health Campus</h1>
                        <p>Online Examination System - Student Portal</p>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="user-dropdown">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['Name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div style="font-weight: 600;"><?php echo $_SESSION['Name']; ?></div>
                                <div style="font-size: 0.75rem; opacity: 0.8;">Student</div>
                            </div>
                            <svg style="width: 20px; height: 20px; margin-left: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="dropdown-menu">
                            <a href="Profile.php" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="EditProfile.php" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                <span>Account Settings</span>
                            </a>
                            <a href="../Help.php" class="dropdown-item">
                                <i class="fas fa-question-circle"></i>
                                <span>Help</span>
                            </a>
                            <a href="../AboutUs.php" class="dropdown-item">
                                <i class="fas fa-info-circle"></i>
                                <span>About</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="Logout.php" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="main-nav">
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="StartExam.php">Take Exam</a></li>
                    <li><a href="Result.php">Results</a></li>
                    <li><a href="practice-selection.php">Practice</a></li>
                    <li><a href="Profile.php">Profile</a></li>
                    <li><a href="MyReportedIssues.php" class="active">My Issues</a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-list-alt"></i> My Reported Issues</h1>
                <div class="page-actions">
                    <a href="ReportIssue.php" class="btn btn-report-new">
                        <i class="fas fa-plus"></i> Report New Issue
                    </a>
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        
            <?php if (mysqli_num_rows($issues_result) > 0): ?>
                <?php while ($issue = mysqli_fetch_assoc($issues_result)): ?>
                    <div class="issue-card">
                        <div class="issue-header">
                            <div class="issue-title">
                                <h5>
                                    <span class="badge badge-secondary">#<?php echo $issue['issue_id']; ?></span>
                                    <?php echo htmlspecialchars($issue['exam_name']); ?>
                                </h5>
                                <p style="color: #6c757d; margin: 0;">
                                    <i class="fas fa-book"></i> <?php echo htmlspecialchars($issue['course_name']); ?> 
                                    (<?php echo htmlspecialchars($issue['course_code']); ?>)
                                </p>
                            </div>
                            <div class="issue-meta">
                                <span class="badge badge-priority-<?php echo $issue['priority']; ?>">
                                    <?php echo strtoupper($issue['priority']); ?>
                                </span>
                                <span class="badge badge-status-<?php echo $issue['status']; ?>">
                                    <?php echo strtoupper($issue['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="issue-details">
                            <p>
                                <strong>Issue Type:</strong> 
                                <span class="badge badge-light">
                                    <?php echo ucfirst(str_replace('_', ' ', $issue['issue_type'])); ?>
                                </span>
                            </p>
                            <p>
                                <strong>Description:</strong><br>
                                <?php echo nl2br(htmlspecialchars($issue['issue_description'])); ?>
                            </p>
                            <p style="color: #6c757d; font-size: 0.9rem;">
                                <i class="fas fa-clock"></i> 
                                Reported: <?php echo date('M d, Y H:i', strtotime($issue['reported_at'])); ?>
                            </p>
                            <?php if ($issue['resolved_at']): ?>
                                <p style="color: #28a745; font-size: 0.9rem;">
                                    <i class="fas fa-check-circle"></i> 
                                    Resolved: <?php echo date('M d, Y', strtotime($issue['resolved_at'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($issue['admin_notes']): ?>
                            <div class="admin-response">
                                <strong><i class="fas fa-user-shield"></i> Admin Response:</strong>
                                <p style="margin: 0.75rem 0 0 0;"><?php echo nl2br(htmlspecialchars($issue['admin_notes'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-issues">
                    <i class="fas fa-check-circle fa-5x"></i>
                    <h4>No Issues Reported</h4>
                    <p>You haven't reported any technical issues yet.</p>
                    <a href="ReportIssue.php" class="btn btn-report-new">
                        <i class="fas fa-plus"></i> Report Your First Issue
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Debre Markos University Health Campus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
