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
$pageTitle = "Practice Questions";
$instructor_id = $_SESSION['ID'];

// Get filter parameters
$course_filter = $_GET['course'] ?? '';
$difficulty_filter = $_GET['difficulty'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Handle delete
if(isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $con->prepare("DELETE FROM practice_questions WHERE practice_id = ? AND created_by = ?");
    $stmt->bind_param("ii", $deleteId, $instructor_id);
    if($stmt->execute()) {
        header("Location: ManagePracticeQuestions.php?success=deleted");
        exit();
    }
    $stmt->close();
}

// Handle toggle active status
if(isset($_GET['toggle_id'])) {
    $toggleId = $_GET['toggle_id'];
    $stmt = $con->prepare("UPDATE practice_questions SET is_active = NOT is_active WHERE practice_id = ? AND created_by = ?");
    $stmt->bind_param("ii", $toggleId, $instructor_id);
    if($stmt->execute()) {
        header("Location: ManagePracticeQuestions.php?success=toggled");
        exit();
    }
    $stmt->close();
}

// Get instructor's courses
$coursesQuery = $con->prepare("SELECT DISTINCT c.course_id, c.course_name, c.course_code
    FROM instructor_courses ic
    INNER JOIN courses c ON ic.course_id = c.course_id
    WHERE ic.instructor_id = ?
    ORDER BY c.course_name");
$coursesQuery->bind_param("i", $instructor_id);
$coursesQuery->execute();
$courses = $coursesQuery->get_result();

// Build practice questions query
$query = "SELECT pq.*, c.course_name, c.course_code, qt.topic_name
    FROM practice_questions pq
    INNER JOIN courses c ON pq.course_id = c.course_id
    INNER JOIN instructor_courses ic ON c.course_id = ic.course_id
    LEFT JOIN question_topics qt ON pq.topic_id = qt.topic_id
    WHERE ic.instructor_id = ? AND pq.created_by = ?";

$params = [$instructor_id, $instructor_id];
$types = "ii";

if($course_filter) {
    $query .= " AND pq.course_id = ?";
    $params[] = $course_filter;
    $types .= "i";
}

if($difficulty_filter) {
    $query .= " AND pq.difficulty_level = ?";
    $params[] = $difficulty_filter;
    $types .= "s";
}

if($type_filter) {
    $query .= " AND pq.question_type = ?";
    $params[] = $type_filter;
    $types .= "s";
}

if($status_filter !== '') {
    $query .= " AND pq.is_active = ?";
    $params[] = $status_filter;
    $types .= "i";
}

if($search) {
    $query .= " AND pq.question_text LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

$query .= " ORDER BY pq.created_at DESC";

$stmt = $con->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$questions = $stmt->get_result();

// Get statistics
$statsQuery = $con->prepare("SELECT 
    COUNT(*) as total_questions,
    COUNT(DISTINCT pq.course_id) as total_courses,
    SUM(CASE WHEN pq.is_active = 1 THEN 1 ELSE 0 END) as active_questions,
    SUM(CASE WHEN pq.difficulty_level = 'Easy' THEN 1 ELSE 0 END) as easy_questions,
    SUM(CASE WHEN pq.difficulty_level = 'Medium' THEN 1 ELSE 0 END) as medium_questions,
    SUM(CASE WHEN pq.difficulty_level = 'Hard' THEN 1 ELSE 0 END) as hard_questions,
    SUM(CASE WHEN pq.question_type = 'multiple_choice' THEN 1 ELSE 0 END) as mcq_questions,
    SUM(CASE WHEN pq.question_type = 'true_false' THEN 1 ELSE 0 END) as tf_questions
    FROM practice_questions pq
    INNER JOIN instructor_courses ic ON pq.course_id = ic.course_id
    WHERE ic.instructor_id = ? AND pq.created_by = ?");
$statsQuery->bind_param("ii", $instructor_id, $instructor_id);
$statsQuery->execute();
$stats = $statsQuery->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice Questions - Instructor</title>
    <link href="../assets/css/modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body.admin-layout { background: #f5f7fa; font-family: 'Poppins', sans-serif; }
        
        .page-header-modern {
            background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(111, 66, 193, 0.3);
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
        
        .page-header-modern p { margin: 0; opacity: 0.95; font-size: 1.05rem; color: white; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 4px solid;
        }
        
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); }
        .stat-card.primary { border-top-color: #6f42c1; }
        .stat-card.success { border-top-color: #28a745; }
        .stat-card.warning { border-top-color: #ffc107; }
        .stat-card.danger { border-top-color: #dc3545; }
        .stat-card.info { border-top-color: #17a2b8; }
        
        .stat-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
        .stat-value { font-size: 2.2rem; font-weight: 900; color: #6f42c1; margin-bottom: 0.5rem; }
        .stat-label { font-size: 0.85rem; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        
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
            color: #6f42c1;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group { margin-bottom: 0; }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #6f42c1;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.7rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #6f42c1;
            box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
        }
        
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; transform: translateY(-2px); }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; transform: translateY(-2px); }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; transform: translateY(-2px); }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; }
        
        .question-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #6f42c1;
            transition: all 0.3s ease;
        }
        
        .question-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }
        
        .question-card.inactive {
            opacity: 0.6;
            border-left-color: #6c757d;
        }
        
        .question-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6f42c1;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .question-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .meta-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .meta-badge.course { background: #e7f3ff; color: #004085; }
        .meta-badge.topic { background: #d4edda; color: #155724; }
        .meta-badge.type-mcq { background: #fff3cd; color: #856404; }
        .meta-badge.type-tf { background: #d1ecf1; color: #0c5460; }
        .meta-badge.difficulty-easy { background: #d4edda; color: #155724; }
        .meta-badge.difficulty-medium { background: #fff3cd; color: #856404; }
        .meta-badge.difficulty-hard { background: #f8d7da; color: #721c24; }
        .meta-badge.status-active { background: #d4edda; color: #155724; }
        .meta-badge.status-inactive { background: #e2e3e5; color: #383d41; }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .option-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            font-size: 0.9rem;
        }
        
        .option-item.correct {
            background: #d4edda;
            border-color: #28a745;
            font-weight: 600;
        }
        
        .tf-options {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .tf-option {
            flex: 1;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .tf-option.correct {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .question-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
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
            margin-bottom: 1.5rem;
        }
        
        .alert {
            padding: 1.25rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .info-card {
            background: linear-gradient(135deg, rgba(111, 66, 193, 0.05), rgba(155, 89, 182, 0.05));
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
            border-left: 4px solid #6f42c1;
        }
        
        .info-card h3 {
            color: #6f42c1;
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .info-card ul {
            margin: 0;
            padding-left: 1.5rem;
            line-height: 2;
            color: #495057;
        }
        
        @media (max-width: 768px) {
            .page-header-modern { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: 1fr; }
            .filter-grid { grid-template-columns: 1fr; }
            .options-grid { grid-template-columns: 1fr; }
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
                    <h1>🎯 Practice Questions</h1>
                    <p>Create practice questions to help students learn and prepare</p>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="ManageQuestions.php" class="btn btn-secondary" style="background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;">
                        <span>📝</span> Exam Questions
                    </a>
                    <a href="AddPracticeQuestion.php" class="btn btn-primary" style="background: white; color: #6f42c1;">
                        <span>➕</span> Create Question
                    </a>
                </div>
            </div>


            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <span style="font-size: 1.5rem;">✅</span>
                <span>
                    <?php 
                    if($_GET['success'] == 'created') echo 'Practice question created successfully!';
                    elseif($_GET['success'] == 'updated') echo 'Practice question updated successfully!';
                    elseif($_GET['success'] == 'deleted') echo 'Practice question deleted successfully!';
                    elseif($_GET['success'] == 'toggled') echo 'Practice question status updated!';
                    ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-value"><?php echo number_format($stats['total_questions']); ?></div>
                    <div class="stat-label">Total Questions</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value"><?php echo number_format($stats['active_questions']); ?></div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">📚</div>
                    <div class="stat-value"><?php echo number_format($stats['total_courses']); ?></div>
                    <div class="stat-label">Courses</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value"><?php echo number_format($stats['mcq_questions']); ?></div>
                    <div class="stat-label">Multiple Choice</div>
                </div>
                <div class="stat-card" style="border-top-color: #17a2b8;">
                    <div class="stat-icon">✓✗</div>
                    <div class="stat-value"><?php echo number_format($stats['tf_questions']); ?></div>
                    <div class="stat-label">True/False</div>
                </div>
            </div>

            <!-- Difficulty Breakdown -->
            <?php if($stats['total_questions'] > 0): ?>
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
                <div class="stat-card" style="border-top-color: #28a745;">
                    <div class="stat-icon">😊</div>
                    <div class="stat-value"><?php echo number_format($stats['easy_questions']); ?></div>
                    <div class="stat-label">Easy</div>
                </div>
                <div class="stat-card" style="border-top-color: #ffc107;">
                    <div class="stat-icon">🤔</div>
                    <div class="stat-value"><?php echo number_format($stats['medium_questions']); ?></div>
                    <div class="stat-label">Medium</div>
                </div>
                <div class="stat-card" style="border-top-color: #dc3545;">
                    <div class="stat-icon">😰</div>
                    <div class="stat-value"><?php echo number_format($stats['hard_questions']); ?></div>
                    <div class="stat-label">Hard</div>
                </div>
            </div>
            <?php endif; ?>


            <!-- Filters -->
            <div class="filter-card">
                <h3>🔍 Filter Practice Questions</h3>
                <form method="GET" action="">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label>Course</label>
                            <select name="course">
                                <option value="">All Courses</option>
                                <?php 
                                $courses->data_seek(0);
                                while($course = $courses->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $course['course_id']; ?>" <?php echo $course_filter == $course['course_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Question Type</label>
                            <select name="type">
                                <option value="">All Types</option>
                                <option value="multiple_choice" <?php echo $type_filter == 'multiple_choice' ? 'selected' : ''; ?>>Multiple Choice</option>
                                <option value="true_false" <?php echo $type_filter == 'true_false' ? 'selected' : ''; ?>>True/False</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Difficulty</label>
                            <select name="difficulty">
                                <option value="">All Levels</option>
                                <option value="Easy" <?php echo $difficulty_filter == 'Easy' ? 'selected' : ''; ?>>Easy</option>
                                <option value="Medium" <?php echo $difficulty_filter == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="Hard" <?php echo $difficulty_filter == 'Hard' ? 'selected' : ''; ?>>Hard</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="">All Status</option>
                                <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="search" placeholder="Search question text..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <span>🔍</span> Apply Filters
                        </button>
                        <?php if($course_filter || $difficulty_filter || $type_filter || $status_filter !== '' || $search): ?>
                        <a href="ManagePracticeQuestions.php" class="btn btn-secondary">
                            <span>🔄</span> Clear Filters
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>


            <!-- Questions List -->
            <div style="margin-bottom: 1rem;">
                <h3 style="color: #6f42c1; font-size: 1.3rem; font-weight: 700;">
                    📋 Practice Questions (<?php echo $questions->num_rows; ?>)
                </h3>
            </div>

            <?php if($questions->num_rows > 0): ?>
                <?php while($q = $questions->fetch_assoc()): ?>
                <div class="question-card <?php echo !$q['is_active'] ? 'inactive' : ''; ?>">
                    <div class="question-text">
                        <?php echo htmlspecialchars($q['question_text']); ?>
                    </div>
                    
                    <div class="question-meta">
                        <?php if($q['course_name']): ?>
                        <span class="meta-badge course">
                            <strong>📚</strong> <?php echo htmlspecialchars($q['course_code']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if(!empty($q['topic_name'])): ?>
                        <span class="meta-badge topic">
                            <strong>🗂️</strong> <?php echo htmlspecialchars($q['topic_name']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <span class="meta-badge type-<?php echo $q['question_type'] == 'multiple_choice' ? 'mcq' : 'tf'; ?>">
                            <strong><?php echo $q['question_type'] == 'multiple_choice' ? '📝' : '✓✗'; ?></strong>
                            <?php echo $q['question_type'] == 'multiple_choice' ? 'Multiple Choice' : 'True/False'; ?>
                        </span>
                        
                        <span class="meta-badge difficulty-<?php echo strtolower($q['difficulty_level']); ?>">
                            <strong>
                                <?php 
                                echo strtolower($q['difficulty_level']) == 'easy' ? '😊' : 
                                    (strtolower($q['difficulty_level']) == 'medium' ? '🤔' : '😰'); 
                                ?>
                            </strong>
                            <?php echo ucfirst($q['difficulty_level']); ?>
                        </span>
                        
                        <span class="meta-badge status-<?php echo $q['is_active'] ? 'active' : 'inactive'; ?>">
                            <strong><?php echo $q['is_active'] ? '✅' : '🔒'; ?></strong>
                            <?php echo $q['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>

                    <?php if($q['question_type'] == 'multiple_choice'): ?>
                    <div class="options-grid">
                        <div class="option-item <?php echo $q['correct_answer'] == 'A' ? 'correct' : ''; ?>">
                            <strong>A)</strong> <?php echo htmlspecialchars($q['option_a']); ?>
                            <?php if($q['correct_answer'] == 'A'): ?>
                            <span style="float: right; color: #28a745;">✓</span>
                            <?php endif; ?>
                        </div>
                        <div class="option-item <?php echo $q['correct_answer'] == 'B' ? 'correct' : ''; ?>">
                            <strong>B)</strong> <?php echo htmlspecialchars($q['option_b']); ?>
                            <?php if($q['correct_answer'] == 'B'): ?>
                            <span style="float: right; color: #28a745;">✓</span>
                            <?php endif; ?>
                        </div>
                        <?php if($q['option_c']): ?>
                        <div class="option-item <?php echo $q['correct_answer'] == 'C' ? 'correct' : ''; ?>">
                            <strong>C)</strong> <?php echo htmlspecialchars($q['option_c']); ?>
                            <?php if($q['correct_answer'] == 'C'): ?>
                            <span style="float: right; color: #28a745;">✓</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if($q['option_d']): ?>
                        <div class="option-item <?php echo $q['correct_answer'] == 'D' ? 'correct' : ''; ?>">
                            <strong>D)</strong> <?php echo htmlspecialchars($q['option_d']); ?>
                            <?php if($q['correct_answer'] == 'D'): ?>
                            <span style="float: right; color: #28a745;">✓</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="tf-options">
                        <div class="tf-option <?php echo $q['correct_answer'] == 'A' ? 'correct' : ''; ?>">
                            ✓ TRUE
                            <?php if($q['correct_answer'] == 'A'): ?>
                            <div style="margin-top: 0.5rem; font-size: 0.9rem;">✅ Correct Answer</div>
                            <?php endif; ?>
                        </div>
                        <div class="tf-option <?php echo $q['correct_answer'] == 'B' ? 'correct' : ''; ?>">
                            ✗ FALSE
                            <?php if($q['correct_answer'] == 'B'): ?>
                            <div style="margin-top: 0.5rem; font-size: 0.9rem;">✅ Correct Answer</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($q['explanation']): ?>
                    <div style="padding: 1rem; background: #e7f3ff; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <strong style="color: #004085;">💡 Explanation:</strong>
                        <div style="margin-top: 0.5rem; color: #004085;">
                            <?php echo nl2br(htmlspecialchars($q['explanation'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="question-actions">
                        <a href="EditPracticeQuestion.php?id=<?php echo $q['practice_id']; ?>" class="btn btn-warning btn-sm">
                            <span>✏️</span> Edit
                        </a>
                        <a href="?toggle_id=<?php echo $q['practice_id']; ?>" 
                           class="btn btn-<?php echo $q['is_active'] ? 'secondary' : 'success'; ?> btn-sm"
                           onclick="return confirm('<?php echo $q['is_active'] ? 'Deactivate' : 'Activate'; ?> this practice question?')">
                            <span><?php echo $q['is_active'] ? '🔒' : '✅'; ?></span>
                            <?php echo $q['is_active'] ? 'Deactivate' : 'Activate'; ?>
                        </a>
                        <a href="?delete_id=<?php echo $q['practice_id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this practice question? This action cannot be undone.')">
                            <span>🗑️</span> Delete
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🎯</div>
                    <h3>No Practice Questions Found</h3>
                    <p>
                        <?php if($course_filter || $difficulty_filter || $type_filter || $status_filter !== '' || $search): ?>
                            No practice questions match your filter criteria. Try adjusting the filters.
                        <?php else: ?>
                            You haven't created any practice questions yet. Start helping your students learn!
                        <?php endif; ?>
                    </p>
                    <a href="AddPracticeQuestion.php" class="btn btn-primary">
                        <span>➕</span> Create Your First Practice Question
                    </a>
                </div>
            <?php endif; ?>

            <!-- Info Card -->
            <div class="info-card">
                <h3>💡 About Practice Questions</h3>
                <ul>
                    <li><strong>Self-Paced Learning:</strong> Students can practice at their own pace without time pressure</li>
                    <li><strong>Instant Feedback:</strong> Students see correct answers and explanations immediately</li>
                    <li><strong>No Approval Required:</strong> Practice questions are available to students right away</li>
                    <li><strong>Unlimited Attempts:</strong> Students can retry questions as many times as they need</li>
                    <li><strong>Two Question Types:</strong> Create Multiple Choice (2-4 options) or True/False questions</li>
                    <li><strong>Difficulty Levels:</strong> Mark questions as Easy, Medium, or Hard to guide student progression</li>
                    <li><strong>Add Explanations:</strong> Help students understand concepts by providing detailed explanations</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
</body>
</html>
<?php $con->close(); ?>
