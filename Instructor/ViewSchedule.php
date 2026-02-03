<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Instructor');

if(!isset($_SESSION['ID'])){
    header("Location: ../auth/institute-login.php");
    exit();
}

if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Instructor'){
    SessionManager::destroySession();
    header("Location: ../auth/institute-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
if ($con->connect_error) {
    die("Database connection failed: " . $con->connect_error);
}

$pageTitle = "Exam Schedule";
$instructor_id = $_SESSION['ID'];

// Get filter parameters with validation
$course_filter = isset($_GET['course']) && is_numeric($_GET['course']) ? intval($_GET['course']) : '';
$month_filter = isset($_GET['month']) ? filter_var($_GET['month'], FILTER_SANITIZE_STRING) : date('Y-m');

// Validate month format
if (!preg_match('/^\d{4}-\d{2}$/', $month_filter)) {
    $month_filter = date('Y-m');
}

// Get instructor's courses
$courses = [];
$coursesQuery = $con->prepare("SELECT DISTINCT c.course_id, c.course_name, c.course_code 
    FROM instructor_courses ic 
    INNER JOIN courses c ON ic.course_id = c.course_id 
    WHERE ic.instructor_id = ? 
    ORDER BY c.course_name");
if ($coursesQuery) {
    $coursesQuery->bind_param("i", $instructor_id);
    $coursesQuery->execute();
    $courses_result = $coursesQuery->get_result();
    while($row = $courses_result->fetch_assoc()) {
        $courses[] = $row;
    }
    $coursesQuery->close();
}

// Get scheduled exams
$exams = [];
$query = "SELECT 
    es.exam_id,
    es.exam_name,
    es.exam_date,
    es.start_time,
    es.end_time,
    es.duration_minutes,
    es.approval_status,
    c.course_name,
    c.course_code,
    ec.category_name,
    (SELECT COUNT(*) FROM exam_questions WHERE exam_id = es.exam_id) as question_count,
    (SELECT COUNT(*) FROM exam_results WHERE exam_id = es.exam_id) as result_count
    FROM exams es
    INNER JOIN courses c ON es.course_id = c.course_id
    INNER JOIN exam_categories ec ON es.exam_category_id = ec.exam_category_id
    WHERE es.created_by = ? 
    AND es.exam_date IS NOT NULL";

$params = [$instructor_id];
$types = "i";

if($course_filter && is_numeric($course_filter)) {
    $query .= " AND es.course_id = ?";
    $params[] = $course_filter;
    $types .= "i";
}

if($month_filter) {
    $query .= " AND DATE_FORMAT(es.exam_date, '%Y-%m') = ?";
    $params[] = $month_filter;
    $types .= "s";
}

$query .= " ORDER BY es.exam_date ASC, es.start_time ASC";

$stmt = $con->prepare($query);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $exams_result = $stmt->get_result();
    while($row = $exams_result->fetch_assoc()) {
        $exams[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Instructor</title>
    <link href="../assets/css/modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body.admin-layout { 
            background: #f5f7fa; 
            font-family: 'Poppins', sans-serif; 
            margin: 0;
            padding: 0;
        }
        
        .page-header-modern {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
            color: white;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 51, 102, 0.2);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header-modern h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .page-header-modern p { 
            margin: 0; 
            opacity: 0.95; 
            font-size: 1.05rem; 
            color: white; 
        }
        
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .filter-card h3 {
            margin: 0 0 1.5rem 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #003366;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group { 
            margin-bottom: 0; 
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #003366;
            font-size: 0.95rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .btn {
            padding: 0.85rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
        }
        
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        
        .btn-secondary:hover { 
            background: #5a6268; 
            transform: translateY(-2px); 
        }
        
        .btn-sm { 
            padding: 0.6rem 1.2rem; 
            font-size: 0.875rem; 
        }
        
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #003366, #0055aa);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 2rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.5rem;
            top: 0.5rem;
            width: 1rem;
            height: 1rem;
            background: white;
            border: 3px solid #003366;
            border-radius: 50%;
            z-index: 1;
        }
        
        .timeline-date {
            font-size: 1.1rem;
            font-weight: 700;
            color: #003366;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .exam-card {
            background: white;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #003366;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .exam-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }
        
        .exam-card.status-draft { border-left-color: #6c757d; }
        .exam-card.status-pending { border-left-color: #ffc107; }
        .exam-card.status-approved { border-left-color: #28a745; }
        .exam-card.status-revision { border-left-color: #fd7e14; }
        .exam-card.status-rejected { border-left-color: #dc3545; }
        
        .exam-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            gap: 1rem;
        }
        
        .exam-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #003366;
            margin: 0 0 0.5rem 0;
        }
        
        .exam-course {
            font-size: 0.95rem;
            color: #6c757d;
            font-weight: 600;
        }
        
        .exam-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #495057;
        }
        
        .meta-item strong {
            color: #003366;
        }
        
        .status-badge {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        
        .status-draft { background: #e9ecef; color: #495057; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-revision { background: #ffe5d0; color: #8b4513; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        
        .exam-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }
        
        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            opacity: 0.3;
        }
        
        .empty-state h3 {
            color: #495057;
            margin-bottom: 0.75rem;
        }
        
        .empty-state p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .calendar-view {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .calendar-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #003366;
            margin: 0;
        }
        
        .admin-content {
            padding: 1.5rem;
        }
        
        .admin-main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            .admin-main-content {
                margin-left: 0;
            }
            
            .page-header-modern { 
                flex-direction: column; 
                align-items: flex-start; 
                padding: 1.5rem;
            }
            
            .filter-grid { 
                grid-template-columns: 1fr; 
            }
            
            .exam-header { 
                flex-direction: column; 
            }
            
            .timeline { 
                padding-left: 1.5rem; 
            }
            
            .timeline-item { 
                padding-left: 1.5rem; 
            }
            
            .timeline-item::before {
                left: -2rem;
            }
            
            .calendar-view {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>

    <div class="admin-main-content">
        <?php include 'header-component.php'; ?>

        <div class="admin-content">
            <!-- Page Header -->
            <div class="page-header-modern">
                <div>
                    <h1>📅 Exam Schedule</h1>
                    <p>View all scheduled exams for your courses</p>
                </div>
                <a href="MyExams.php" class="btn btn-secondary" style="background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;">
                    <span>←</span> Back to My Exams
                </a>
            </div>

            <!-- Filters -->
            <div class="filter-card">
                <h3>🔍 Filter Schedule</h3>
                <form method="GET" action="">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label for="course">Course</label>
                            <select name="course" id="course">
                                <option value="">All Courses</option>
                                <?php foreach($courses as $course): ?>
                                <option value="<?php echo $course['course_id']; ?>" <?php echo $course_filter == $course['course_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="month">Month</label>
                            <input type="month" name="month" id="month" value="<?php echo htmlspecialchars($month_filter); ?>">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <span>🔍</span> Apply Filters
                        </button>
                        <?php if($course_filter || $month_filter != date('Y-m')): ?>
                        <a href="ViewSchedule.php" class="btn btn-secondary">
                            <span>🔄</span> Clear Filters
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Schedule Timeline -->
            <div class="calendar-view">
                <div class="calendar-header">
                    <h3 class="calendar-title">
                        📆 <?php echo date('F Y', strtotime($month_filter . '-01')); ?>
                    </h3>
                    <div style="color: #6c757d; font-size: 0.95rem;">
                        <strong><?php echo count($exams); ?></strong> scheduled exam<?php echo count($exams) != 1 ? 's' : ''; ?>
                    </div>
                </div>

                <?php if(count($exams) > 0): ?>
                    <div class="timeline">
                        <?php 
                        $current_date = '';
                        foreach($exams as $exam): 
                            $exam_date = date('l, F j, Y', strtotime($exam['exam_date']));
                            $show_date = ($exam_date != $current_date);
                            $current_date = $exam_date;
                            
                            if($show_date):
                        ?>
                        <div class="timeline-date">
                            <span>📅</span> <?php echo $exam_date; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="timeline-item">
                            <div class="exam-card status-<?php echo htmlspecialchars($exam['approval_status']); ?>">
                                <div class="exam-header">
                                    <div style="flex: 1;">
                                        <h3 class="exam-title"><?php echo htmlspecialchars($exam['exam_name']); ?></h3>
                                        <div class="exam-course">
                                            📚 <?php echo htmlspecialchars($exam['course_code'] . ' - ' . $exam['course_name']); ?>
                                        </div>
                                    </div>
                                    <span class="status-badge status-<?php echo htmlspecialchars($exam['approval_status']); ?>">
                                        <?php 
                                        $status_icons = [
                                            'draft' => '📝',
                                            'pending' => '⏳',
                                            'approved' => '✅',
                                            'revision' => '🔄',
                                            'rejected' => '❌'
                                        ];
                                        $status_icon = isset($status_icons[$exam['approval_status']]) ? $status_icons[$exam['approval_status']] : '📋';
                                        echo $status_icon . ' ' . strtoupper($exam['approval_status']); 
                                        ?>
                                    </span>
                                </div>

                                <div class="exam-meta">
                                    <div class="meta-item">
                                        <strong>🕐 Time:</strong>
                                        <?php 
                                        if($exam['start_time'] && $exam['end_time']) {
                                            echo date('g:i A', strtotime($exam['start_time'])) . ' - ' . date('g:i A', strtotime($exam['end_time']));
                                        } elseif($exam['start_time']) {
                                            echo date('g:i A', strtotime($exam['start_time']));
                                        } else {
                                            echo 'Not set';
                                        }
                                        ?>
                                    </div>
                                    <div class="meta-item">
                                        <strong>⏱️ Duration:</strong>
                                        <?php echo htmlspecialchars($exam['duration_minutes']); ?> minutes
                                    </div>
                                    <div class="meta-item">
                                        <strong>📂 Category:</strong>
                                        <?php echo htmlspecialchars($exam['category_name']); ?>
                                    </div>
                                    <div class="meta-item">
                                        <strong>❓ Questions:</strong>
                                        <?php echo htmlspecialchars($exam['question_count']); ?>
                                    </div>
                                    <?php if($exam['result_count'] > 0): ?>
                                    <div class="meta-item">
                                        <strong>📊 Results:</strong>
                                        <?php echo htmlspecialchars($exam['result_count']); ?> student<?php echo $exam['result_count'] != 1 ? 's' : ''; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="exam-actions">
                                    <a href="ManageExamQuestions.php?exam_id=<?php echo $exam['exam_id']; ?>" class="btn btn-primary btn-sm">
                                        <span>📝</span> View Questions
                                    </a>
                                    <?php if($exam['approval_status'] == 'approved' && $exam['result_count'] > 0): ?>
                                    <a href="ResultsOverview.php?exam=<?php echo $exam['exam_id']; ?>" class="btn btn-success btn-sm" style="background: #28a745; color: white;">
                                        <span>📊</span> View Results
                                    </a>
                                    <?php endif; ?>
                                    <?php if($exam['approval_status'] == 'draft' || $exam['approval_status'] == 'revision'): ?>
                                    <a href="CreateExam.php?exam_id=<?php echo $exam['exam_id']; ?>" class="btn btn-warning btn-sm" style="background: #ffc107; color: #212529;">
                                        <span>✏️</span> Edit Exam
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📅</div>
                        <h3>No Scheduled Exams</h3>
                        <p>
                            <?php if($course_filter || $month_filter != date('Y-m')): ?>
                                No exams are scheduled for the selected filters. Try adjusting your filters.
                            <?php else: ?>
                                You don't have any exams scheduled for this month. Create an exam and set a date to see it here.
                            <?php endif; ?>
                        </p>
                        <a href="CreateExam.php" class="btn btn-primary">
                            <span>➕</span> Create New Exam
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info Card -->
            <div style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.05), rgba(0, 85, 170, 0.05)); 
                        border-radius: 12px; padding: 2rem; border-left: 4px solid #003366; margin-bottom: 2rem;">
                <h3 style="color: #003366; margin: 0 0 1rem 0; font-size: 1.2rem; font-weight: 700;">
                    💡 About Exam Schedule
                </h3>
                <ul style="margin: 0; padding-left: 1.5rem; line-height: 2; color: #495057;">
                    <li><strong>Timeline View:</strong> See all your scheduled exams in chronological order</li>
                    <li><strong>Status Tracking:</strong> Monitor approval status for each exam</li>
                    <li><strong>Quick Actions:</strong> Access exam questions and results directly from the schedule</li>
                    <li><strong>Filter Options:</strong> Filter by course or month to find specific exams</li>
                    <li><strong>Approved Exams:</strong> Only approved exams are visible to students</li>
                    <li><strong>Set Dates:</strong> Make sure to set exam dates when creating exams to see them here</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
    <script>
        // Auto-submit form when month or course changes
        document.getElementById('month').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('course').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
<?php 
$con->close(); 
?>