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
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_id = mysqli_real_escape_string($con, $_POST['exam_id']);
    $issue_type = mysqli_real_escape_string($con, $_POST['issue_type']);
    $issue_description = mysqli_real_escape_string($con, $_POST['issue_description']);
    $browser_info = mysqli_real_escape_string($con, $_POST['browser_info']);
    $os_info = mysqli_real_escape_string($con, $_POST['os_info']);
    $screen_resolution = mysqli_real_escape_string($con, $_POST['screen_resolution']);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    if (!empty($exam_id) && !empty($issue_description)) {
        // Determine priority based on issue type
        $priority = 'medium';
        if (in_array($issue_type, ['submission_failure', 'timer_error', 'system_error'])) {
            $priority = 'high';
        } elseif ($issue_type == 'connectivity') {
            $priority = 'critical';
        }
        
        $sql = "INSERT INTO technical_issues (student_id, exam_id, issue_type, issue_description, 
                browser_info, os_info, screen_resolution, ip_address, priority, status, reported_at) 
                VALUES ('$student_id', '$exam_id', '$issue_type', '$issue_description', 
                '$browser_info', '$os_info', '$screen_resolution', '$ip_address', '$priority', 'pending', NOW())";
        
        if (mysqli_query($con, $sql)) {
            $success_message = "Issue reported successfully. Our team will look into it. Issue ID: " . mysqli_insert_id($con);
        } else {
            $error_message = "Error reporting issue. Please try again.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

// Get student's active exams
$exams_query = "SELECT DISTINCT e.exam_id, e.exam_name, c.course_name 
                FROM exams e
                INNER JOIN courses c ON e.course_id = c.course_id
                INNER JOIN student_courses sc ON c.course_id = sc.course_id
                WHERE sc.student_id = '$student_id' AND e.is_active = 1
                ORDER BY e.exam_date DESC, e.start_time DESC";
$exams_result = mysqli_query($con, $exams_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Technical Issue - Debre Markos University Health Campus</title>
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
            z-index: 100;
            padding: 2rem 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
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

        .report-container {
            animation: fadeInUp 0.8s ease;
        }

        .page-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(220, 53, 69, 0.3);
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0 0 0.5rem 0;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-weight: 500;
        }

        .card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 2rem;
        }

        .alert {
            padding: 1.25rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            animation: fadeInUp 0.5s ease;
            position: relative;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .success-popup {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(40, 167, 69, 0.3);
            animation: successPop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
        }
        
        @keyframes successPop {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: 900;
            flex-shrink: 0;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .success-content {
            flex: 1;
        }
        
        .success-content h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.5rem;
            color: #155724;
            font-weight: 800;
        }
        
        .success-content p {
            margin: 0;
            font-size: 1.1rem;
            color: #155724;
            font-weight: 600;
        }
        
        .close-alert {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 2rem;
            color: #155724;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .close-alert:hover {
            background: rgba(21, 87, 36, 0.1);
            transform: rotate(90deg);
        }

        .system-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            border: 2px solid #2196f3;
        }

        .system-info h6 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1565c0;
            margin-bottom: 1rem;
        }

        .system-info p {
            margin-bottom: 0.5rem;
            color: #0d47a1;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.75rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 700;
            color: #1a2b4a;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #2196f3;
            box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-submit {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            width: 100%;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(220, 53, 69, 0.4);
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            width: 100%;
            margin-top: 1rem;
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
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
                    <li><a href="ReportIssue.php" class="active">Report Issue</a></li>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="report-container">
                <div class="page-header">
                    <h1><span>⚠️</span> Report Technical Issue</h1>
                    <p>Let us know about any technical problems you're experiencing</p>
                </div>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success success-popup">
                        <div class="success-icon">✓</div>
                        <div class="success-content">
                            <h3>Success!</h3>
                            <p><?php echo $success_message; ?></p>
                        </div>
                        <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <strong>Error!</strong> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="system-info">
                        <h6><strong>📊 System Information (Auto-captured)</strong></h6>
                        <p><strong>Browser:</strong> <span id="display_browser">Detecting...</span></p>
                        <p><strong>OS:</strong> <span id="display_os">Detecting...</span></p>
                        <p><strong>Screen:</strong> <span id="display_resolution">Detecting...</span></p>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="exam_id">Select Exam *</label>
                            <select class="form-control" id="exam_id" name="exam_id" required>
                                <option value="">-- Select Exam --</option>
                                <?php while ($exam = mysqli_fetch_assoc($exams_result)): ?>
                                    <option value="<?php echo $exam['exam_id']; ?>">
                                        <?php echo htmlspecialchars($exam['course_name'] . ' - ' . $exam['exam_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="issue_type">Issue Type *</label>
                            <select class="form-control" id="issue_type" name="issue_type" required>
                                <option value="">-- Select Issue Type --</option>
                                <option value="connectivity">❌ Connectivity Problems (Internet drops)</option>
                                <option value="system_error">❌ System Errors (Page freezing/crashing)</option>
                                <option value="submission_failure">❌ Submission Failures (Answers not saving)</option>
                                <option value="display_issue">❌ Display Issues (Questions not showing)</option>
                                <option value="timer_error">❌ Timer Malfunctioning</option>
                                <option value="login_problem">❌ Login Problems</option>
                                <option value="other">🔧 Other Technical Issue</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="issue_description">Describe the Issue *</label>
                            <textarea class="form-control" id="issue_description" name="issue_description" 
                                      rows="6" required placeholder="Please describe the technical issue in detail. Include what you were doing when the issue occurred..."></textarea>
                        </div>
                        
                        <!-- Hidden fields for system info -->
                        <input type="hidden" id="browser_info" name="browser_info">
                        <input type="hidden" id="os_info" name="os_info">
                        <input type="hidden" id="screen_resolution" name="screen_resolution">
                        
                        <button type="submit" class="btn btn-submit">📤 Submit Issue Report</button>
                        <a href="index.php" class="btn btn-back">← Back to Dashboard</a>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="modern-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Debre Markos University Health Campus. All rights reserved.</p>
        </div>
    </footer>
    
    <script>
        // Capture system information automatically
        function captureSystemInfo() {
            const userAgent = navigator.userAgent;
            document.getElementById('browser_info').value = userAgent;
            document.getElementById('os_info').value = navigator.platform;
            document.getElementById('screen_resolution').value = screen.width + 'x' + screen.height;
            
            // Display to user - shortened browser name
            let browserName = 'Unknown Browser';
            if (userAgent.indexOf('Chrome') > -1 && userAgent.indexOf('Edg') === -1) {
                browserName = 'Google Chrome';
            } else if (userAgent.indexOf('Safari') > -1 && userAgent.indexOf('Chrome') === -1) {
                browserName = 'Safari';
            } else if (userAgent.indexOf('Firefox') > -1) {
                browserName = 'Mozilla Firefox';
            } else if (userAgent.indexOf('Edg') > -1) {
                browserName = 'Microsoft Edge';
            } else if (userAgent.indexOf('Opera') > -1 || userAgent.indexOf('OPR') > -1) {
                browserName = 'Opera';
            }
            
            document.getElementById('display_browser').textContent = browserName;
            document.getElementById('display_os').textContent = navigator.platform;
            document.getElementById('display_resolution').textContent = screen.width + 'x' + screen.height;
        }
        
        window.onload = captureSystemInfo;
    </script>
</body>
</html>
