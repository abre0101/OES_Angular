<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Instructor session
SessionManager::startSession('Instructor');

// Check if user is logged in
if(!isset($_SESSION['ID'])){
    header("Location: ../auth/institute-login.php");
    exit();
}

// Validate instructor role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Instructor'){
    SessionManager::destroySession();
    header("Location: ../auth/institute-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$pageTitle = "Analytics & Insights";

// Check if required tables exist
$questionsExists = false;
$studentAnswersExists = false;
$questionTopicsExists = false;
$examResultsExists = false;

$tableCheck = $con->query("SHOW TABLES LIKE 'questions'");
if($tableCheck && $tableCheck->num_rows > 0) {
    $questionsExists = true;
}

$tableCheck = $con->query("SHOW TABLES LIKE 'student_answers'");
if($tableCheck && $tableCheck->num_rows > 0) {
    $studentAnswersExists = true;
}

$tableCheck = $con->query("SHOW TABLES LIKE 'question_topics'");
if($tableCheck && $tableCheck->num_rows > 0) {
    $questionTopicsExists = true;
}

$tableCheck = $con->query("SHOW TABLES LIKE 'exam_results'");
if($tableCheck && $tableCheck->num_rows > 0) {
    $examResultsExists = true;
}

// Get question difficulty analysis
$questionDifficulty = null;
if($questionsExists && $studentAnswersExists) {
    $questionDifficulty = $con->query("SELECT 
        q.question_id,
        q.question_text,
        c.course_name,
        COUNT(DISTINCT sa.answer_id) as attempt_count,
        SUM(CASE WHEN sa.is_correct = 1 THEN 1 ELSE 0 END) as correct_count,
        ROUND((SUM(CASE WHEN sa.is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(sa.answer_id), 0)), 2) as success_rate
        FROM questions q
        INNER JOIN courses c ON q.course_id = c.course_id
        LEFT JOIN student_answers sa ON q.question_id = sa.question_id
        GROUP BY q.question_id
        HAVING attempt_count > 0
        ORDER BY success_rate ASC
        LIMIT 20");
}

// Get performance trends over time
$performanceTrends = null;
if($examResultsExists) {
    $performanceTrends = $con->query("SELECT 
        DATE(er.exam_submitted_at) as exam_date,
        AVG(er.percentage_score) as avg_score,
        COUNT(*) as exam_count,
        SUM(CASE WHEN er.pass_status = 'Pass' THEN 1 ELSE 0 END) as pass_count
        FROM exam_results er
        WHERE er.percentage_score > 0
        GROUP BY DATE(er.exam_submitted_at)
        ORDER BY exam_date DESC
        LIMIT 30");
}

// Get course performance comparison - using exam results
$coursePerformance = null;
if($examResultsExists) {
    $coursePerformance = $con->query("SELECT 
        c.course_name,
        c.course_code,
        COUNT(DISTINCT er.result_id) as exam_count,
        COUNT(DISTINCT er.student_id) as student_count,
        ROUND(AVG(er.percentage_score), 2) as avg_score,
        SUM(CASE WHEN er.pass_status = 'Pass' THEN 1 ELSE 0 END) as pass_count,
        ROUND((SUM(CASE WHEN er.pass_status = 'Pass' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(er.result_id), 0)), 2) as pass_rate
        FROM courses c
        INNER JOIN exams es ON c.course_id = es.course_id
        INNER JOIN exam_results er ON es.exam_id = er.exam_id
        GROUP BY c.course_id
        HAVING exam_count > 0
        ORDER BY avg_score DESC");
}

// Get topic performance (if topics exist)
$topicPerformance = null;
if($questionTopicsExists && $questionsExists && $studentAnswersExists) {
    $topicPerformance = $con->query("SELECT 
        qt.topic_name,
        c.course_name,
        COUNT(DISTINCT q.question_id) as question_count,
        COUNT(DISTINCT sa.answer_id) as attempt_count,
        ROUND(AVG(CASE WHEN sa.is_correct = 1 THEN 100 ELSE 0 END), 2) as avg_accuracy
        FROM question_topics qt
        LEFT JOIN questions q ON qt.topic_id = q.topic_id
        LEFT JOIN courses c ON q.course_id = c.course_id
        LEFT JOIN student_answers sa ON q.question_id = sa.question_id
        GROUP BY qt.topic_id, qt.topic_name, c.course_name
        HAVING question_count > 0 AND attempt_count > 0
        ORDER BY avg_accuracy ASC
        LIMIT 10");
}

// Get overall statistics
$stats = [];
if($questionsExists) {
    $result = $con->query("SELECT COUNT(*) as count FROM questions");
    $stats['total_questions'] = $result ? $result->fetch_assoc()['count'] : 0;
} else {
    $stats['total_questions'] = 0;
}

if($studentAnswersExists) {
    $result = $con->query("SELECT COUNT(*) as count FROM student_answers");
    $stats['total_attempts'] = $result ? $result->fetch_assoc()['count'] : 0;
    
    $result = $con->query("SELECT ROUND(AVG(CASE WHEN is_correct = 1 THEN 100 ELSE 0 END), 2) as avg FROM student_answers");
    $stats['avg_difficulty'] = $result ? ($result->fetch_assoc()['avg'] ?? 0) : 0;
} else {
    $stats['total_attempts'] = 0;
    $stats['avg_difficulty'] = 0;
}

if($questionsExists && $studentAnswersExists) {
    $result = $con->query("SELECT COUNT(*) as count FROM (
        SELECT q.question_id, 
        ROUND((SUM(CASE WHEN sa.is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(sa.answer_id), 0)), 2) as success_rate
        FROM questions q
        LEFT JOIN student_answers sa ON q.question_id = sa.question_id
        GROUP BY q.question_id
        HAVING success_rate < 50 AND COUNT(sa.answer_id) > 0
    ) as hard_questions");
    $stats['hardest_questions'] = $result ? $result->fetch_assoc()['count'] : 0;
} else {
    $stats['hardest_questions'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Insights - Instructor Dashboard</title>
    <link href="../assets/css/modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #003366;
            --primary-light: #0055aa;
            --secondary-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }
        
        body.admin-layout { 
            background: #f5f7fa; 
            font-family: 'Poppins', sans-serif; 
        }
        
        .page-header {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 51, 102, 0.15);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .header-content h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-content p {
            margin: 0;
            opacity: 0.95;
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
            gap: 1.5rem; 
            margin-bottom: 2rem; 
        }
        
        .stat-card { 
            background: white; 
            border-radius: 16px; 
            padding: 1.75rem; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            border-top: 5px solid;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.05), transparent);
            border-radius: 0 0 0 100%;
        }
        
        .stat-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card.primary { border-top-color: #007bff; }
        .stat-card.success { border-top-color: #28a745; }
        .stat-card.warning { border-top-color: #ffc107; }
        .stat-card.danger { border-top-color: #dc3545; }
        
        .stat-icon { 
            font-size: 2.5rem; 
            margin-bottom: 0.75rem; 
        }
        
        .stat-value { 
            font-size: 2.8rem; 
            font-weight: 900; 
            color: #003366; 
            margin-bottom: 0.5rem; 
            line-height: 1;
        }
        
        .stat-label { 
            font-size: 0.95rem; 
            color: #6c757d; 
            font-weight: 500; 
        }
        
        .data-section { 
            background: white; 
            border-radius: 16px; 
            padding: 2rem; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); 
            margin-bottom: 2rem; 
        }
        
        .section-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 1.5rem; 
            padding-bottom: 1rem; 
            border-bottom: 3px solid #f0f0f0; 
        }
        
        .section-title { 
            font-size: 1.4rem; 
            font-weight: 700; 
            color: #003366; 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .question-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .question-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .question-item.hard { border-left-color: #dc3545; }
        .question-item.medium { border-left-color: #ffc107; }
        .question-item.easy { border-left-color: #28a745; }
        
        .course-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .course-card h4 {
            margin: 0 0 1rem 0;
            color: #003366;
            font-size: 1.1rem;
        }
        
        .course-score {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .course-score.excellent { color: #28a745; }
        .course-score.good { color: #17a2b8; }
        .course-score.average { color: #ffc107; }
        .course-score.poor { color: #dc3545; }
        
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .badge-success {
            background: rgba(40, 167, 69, 0.15);
            color: #155724;
        }
        
        .badge-warning {
            background: rgba(255, 193, 7, 0.15);
            color: #856404;
        }
        
        .badge-danger {
            background: rgba(220, 53, 69, 0.15);
            color: #721c24;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #003366;
        }
        
        .empty-state p {
            font-size: 1rem;
            margin: 0;
        }
        
        .insight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 1rem;
        }
        
        .insight-card h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
        }
        
        .insight-card p, .insight-card ul {
            margin: 0.5rem 0 0 0;
            opacity: 1;
            color: white;
        }
        
        .insight-card ul li {
            color: white;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr; }
            .charts-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>

    <div class="admin-main-content">
        <?php include 'header-component.php'; ?>

        <div class="admin-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="header-content">
                    <h1>📊 Analytics & Insights</h1>
                    <p>Question difficulty analysis and student performance trends</p>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon">❓</div>
                    <div class="stat-value"><?php echo number_format($stats['total_questions'] ?? 0); ?></div>
                    <div class="stat-label">Total Questions</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value"><?php echo number_format($stats['total_attempts'] ?? 0); ?></div>
                    <div class="stat-label">Total Attempts</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">📈</div>
                    <div class="stat-value"><?php echo number_format($stats['avg_difficulty'] ?? 0, 1); ?>%</div>
                    <div class="stat-label">Avg Success Rate</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-value"><?php echo number_format($stats['hardest_questions'] ?? 0); ?></div>
                    <div class="stat-label">Hard Questions (<50%)</div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <!-- Question Difficulty Analysis -->
                <div class="chart-card">
                    <div class="section-header">
                        <h3 class="section-title">🎯 Most Difficult Questions</h3>
                    </div>
                    <div style="max-height: 600px; overflow-y: auto;">
                        <?php if($questionDifficulty && $questionDifficulty->num_rows > 0): ?>
                        <?php while($q = $questionDifficulty->fetch_assoc()): 
                            $difficultyClass = $q['success_rate'] < 40 ? 'hard' : ($q['success_rate'] < 70 ? 'medium' : 'easy');
                            $badgeColor = $q['success_rate'] < 40 ? '#dc3545' : ($q['success_rate'] < 70 ? '#ffc107' : '#28a745');
                        ?>
                        <div class="question-item <?php echo $difficultyClass; ?>">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                <div style="flex: 1;">
                                    <strong style="color: #003366;">Question #<?php echo $q['question_id']; ?></strong>
                                    <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.25rem;">
                                        <?php echo htmlspecialchars($q['course_name']); ?>
                                    </div>
                                </div>
                                <span class="badge" style="background: <?php echo $badgeColor; ?>; color: white;">
                                    <?php echo $q['success_rate']; ?>% Success
                                </span>
                            </div>
                            <p style="margin: 0 0 0.75rem 0; color: #6c757d; font-size: 0.9rem;">
                                <?php echo htmlspecialchars(substr($q['question_text'], 0, 150)); ?><?php echo strlen($q['question_text']) > 150 ? '...' : ''; ?>
                            </p>
                            <div style="display: flex; gap: 2rem; font-size: 0.85rem; color: #6c757d;">
                                <div>
                                    <strong><?php echo $q['attempt_count']; ?></strong> attempts
                                </div>
                                <div>
                                    <strong><?php echo $q['correct_count']; ?></strong> correct
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📊</div>
                            <h3>No Data Available</h3>
                            <p>No question attempt data available yet.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Performance Trends Chart -->
                <div class="chart-card">
                    <div class="section-header">
                        <h3 class="section-title">📈 Performance Trends</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Course Performance Comparison -->
            <div class="data-section">
                <div class="section-header">
                    <h3 class="section-title">📚 Course Performance Comparison</h3>
                </div>
                <div>
                    <?php if($coursePerformance && $coursePerformance->num_rows > 0): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                        <?php while($course = $coursePerformance->fetch_assoc()): 
                            $scoreClass = $course['avg_score'] < 50 ? 'poor' : ($course['avg_score'] < 75 ? 'average' : ($course['avg_score'] < 85 ? 'good' : 'excellent'));
                        ?>
                        <div class="course-card">
                            <h4 style="margin: 0 0 1rem 0;">
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </h4>
                            <div class="course-score <?php echo $scoreClass; ?>">
                                <?php echo $course['avg_score']; ?>%
                            </div>
                            <div style="font-size: 0.85rem; color: #6c757d; margin-bottom: 0.75rem;">
                                <?php echo $course['exam_count']; ?> exams • 
                                <?php echo $course['student_count']; ?> students
                            </div>
                            <div style="padding-top: 0.75rem; border-top: 1px solid #e0e0e0; font-size: 0.85rem;">
                                <span style="color: #28a745; font-weight: 600;">
                                    <?php echo $course['pass_rate']; ?>%
                                </span>
                                <span style="color: #6c757d;"> pass rate</span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📚</div>
                        <h3>No Data Available</h3>
                        <p>No course performance data available</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Topic Performance (if available) -->
            <?php if($topicPerformance && $topicPerformance->num_rows > 0): ?>
            <div class="data-section">
                <div class="section-header">
                    <h3 class="section-title">📖 Weakest Topics (Need Attention)</h3>
                </div>
                <div>
                    <?php while($topic = $topicPerformance->fetch_assoc()): 
                        $topicScoreClass = $topic['avg_accuracy'] < 50 ? 'poor' : ($topic['avg_accuracy'] < 70 ? 'average' : 'good');
                        $topicColor = $topic['avg_accuracy'] < 50 ? '#dc3545' : ($topic['avg_accuracy'] < 70 ? '#ffc107' : '#28a745');
                    ?>
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="color: #003366;"><?php echo htmlspecialchars($topic['topic_name']); ?></strong>
                            <div style="font-size: 0.85rem; color: #6c757d;">
                                <?php echo htmlspecialchars($topic['course_name']); ?> • <?php echo $topic['question_count']; ?> questions
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.5rem; font-weight: 800; color: <?php echo $topicColor; ?>;">
                                <?php echo $topic['avg_accuracy']; ?>%
                            </div>
                            <div style="font-size: 0.75rem; color: #6c757d;">Success Rate</div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Insights & Recommendations -->
            <div class="charts-grid">
                <div class="insight-card">
                    <h4>📊 Question Quality</h4>
                    <p>
                        <?php 
                        if($stats['hardest_questions'] > 10) {
                            echo "You have {$stats['hardest_questions']} questions with <50% success rate. Consider reviewing these for clarity or difficulty.";
                        } else {
                            echo "Your questions have good difficulty balance. Keep monitoring student performance.";
                        }
                        ?>
                    </p>
                </div>
                <div class="insight-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h4>✅ Best Practices</h4>
                    <ul>
                        <li>Review questions with <40% success rate</li>
                        <li>Balance easy, medium, and hard questions</li>
                        <li>Use topics to organize questions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
    <script>
        // Performance Trends Chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceData = {
            labels: [
                <?php 
                $trends = [];
                if($performanceTrends) {
                    $performanceTrends->data_seek(0);
                    while($trend = $performanceTrends->fetch_assoc()) {
                        $trends[] = $trend;
                        echo "'" . date('M d', strtotime($trend['exam_date'])) . "',";
                    }
                }
                ?>
            ],
            datasets: [{
                label: 'Average Score',
                data: [
                    <?php 
                    foreach($trends as $trend) {
                        echo $trend['avg_score'] . ",";
                    }
                    ?>
                ],
                borderColor: 'rgb(0, 123, 255)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: performanceData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
<?php $con->close(); ?>
