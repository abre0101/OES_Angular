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
$instructor_id = $_SESSION['ID'];

// Get instructor's courses
$coursesQuery = $con->prepare("SELECT DISTINCT c.course_id, c.course_name, c.course_code
    FROM instructor_courses ic
    INNER JOIN courses c ON ic.course_id = c.course_id
    WHERE ic.instructor_id = ?
    ORDER BY c.course_name");
$coursesQuery->bind_param("i", $instructor_id);
$coursesQuery->execute();
$instructorCourses = $coursesQuery->get_result();

// Get topics
$topicsQuery = $con->query("SELECT * FROM question_topics ORDER BY topic_name");

$message = '';
$messageType = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_practice_question'])) {
    $course_id = $_POST['course_id'];
    $question_type = $_POST['question_type'];
    $question_text = $_POST['question_text'];
    $difficulty_level = $_POST['difficulty_level'];
    $topic_id = !empty($_POST['topic_id']) ? $_POST['topic_id'] : null;
    $explanation = $_POST['explanation'];
    
    // Verify instructor teaches this course
    $verifyStmt = $con->prepare("SELECT COUNT(*) as count FROM instructor_courses 
                                  WHERE instructor_id = ? AND course_id = ?");
    $verifyStmt->bind_param("ii", $instructor_id, $course_id);
    $verifyStmt->execute();
    $canAdd = $verifyStmt->get_result()->fetch_assoc()['count'] > 0;
    $verifyStmt->close();
    
    if($canAdd) {
        if($question_type == 'true_false') {
            // True/False question
            $correct_answer = $_POST['correct_answer_tf'];
            $option_a = 'True';
            $option_b = 'False';
            $option_c = null;
            $option_d = null;
        } else {
            // Multiple Choice question
            $option_a = $_POST['option_a'];
            $option_b = $_POST['option_b'];
            $option_c = !empty($_POST['option_c']) ? $_POST['option_c'] : null;
            $option_d = !empty($_POST['option_d']) ? $_POST['option_d'] : null;
            $correct_answer = $_POST['correct_answer'];
        }
        
        $stmt = $con->prepare("INSERT INTO practice_questions 
                              (course_id, question_text, question_type, option_a, option_b, option_c, option_d, 
                               correct_answer, explanation, difficulty_level, topic_id, created_by)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssii", $course_id, $question_text, $question_type, $option_a, $option_b, 
                         $option_c, $option_d, $correct_answer, $explanation, $difficulty_level, $topic_id, $instructor_id);
        
        if($stmt->execute()) {
            header("Location: ManagePracticeQuestions.php?success=created");
            exit();
        } else {
            $message = 'Error adding practice question: ' . $stmt->error;
            $messageType = 'danger';
        }
        $stmt->close();
    } else {
        $message = 'Error: You can only add practice questions for courses you teach!';
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Practice Question - Instructor</title>
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
        }
        
        .page-header-modern p { margin: 0; opacity: 0.95; font-size: 1.05rem; }
        
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .form-card h3 {
            margin: 0 0 2rem 0;
            font-size: 1.4rem;
            font-weight: 700;
            color: #6f42c1;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 1.75rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #6f42c1;
            font-size: 1rem;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6f42c1;
            box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
        }
        
        .form-group small {
            display: block;
            margin-top: 0.5rem;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .btn {
            padding: 0.9rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6f42c1 0%, #9b59b6 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.3);
        }
        
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
        
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
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .info-box {
            background: linear-gradient(135deg, rgba(111, 66, 193, 0.05), rgba(155, 89, 182, 0.05));
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            border-left: 4px solid #6f42c1;
        }
        
        .info-box strong {
            color: #6f42c1;
            font-size: 1.05rem;
        }
        
        .info-box ul {
            margin: 0.75rem 0 0 1.5rem;
            color: #495057;
            line-height: 1.8;
        }
        
        .question-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .type-option {
            padding: 2rem;
            border: 3px solid #e0e0e0;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .type-option:hover {
            border-color: #6f42c1;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.2);
        }
        
        .type-option.selected {
            border-color: #6f42c1;
            background: linear-gradient(135deg, rgba(111, 66, 193, 0.1), rgba(155, 89, 182, 0.1));
        }
        
        .type-option input[type="radio"] {
            display: none;
        }
        
        .type-option-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .type-option-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #6f42c1;
            margin-bottom: 0.5rem;
        }
        
        .type-option-desc {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .tf-options-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .tf-option-box {
            padding: 1.5rem;
            border: 3px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .tf-option-box:hover {
            border-color: #6f42c1;
        }
        
        .tf-option-box.selected {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .tf-option-box input[type="radio"] {
            display: none;
        }
        
        .tf-option-label {
            font-size: 1.5rem;
            font-weight: 700;
            color: #6f42c1;
        }
        
        @media (max-width: 768px) {
            .page-header-modern { flex-direction: column; align-items: flex-start; }
            .question-type-selector { grid-template-columns: 1fr; }
            .tf-options-container { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
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
                    <h1>➕ Add Practice Question</h1>
                    <p>Create a new practice question for your students</p>
                </div>
                <a href="ManagePracticeQuestions.php" class="btn btn-secondary" style="background: rgba(255, 255, 255, 0.2); color: white; border: 2px solid white;">
                    <span>←</span> Back to Questions
                </a>
            </div>


            <?php if($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <span style="font-size: 1.5rem;"><?php echo $messageType == 'success' ? '✅' : '❌'; ?></span>
                <span><?php echo $message; ?></span>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" id="questionForm">
                <div class="form-card">
                    <h3>� Querstion Type</h3>
                    <div class="question-type-selector">
                        <label class="type-option selected" id="mcqOption">
                            <input type="radio" name="question_type" value="multiple_choice" checked>
                            <div class="type-option-icon">📝</div>
                            <div class="type-option-title">Multiple Choice</div>
                            <div class="type-option-desc">2-4 answer options</div>
                        </label>
                        <label class="type-option" id="tfOption">
                            <input type="radio" name="question_type" value="true_false">
                            <div class="type-option-icon">✓✗</div>
                            <div class="type-option-title">True/False</div>
                            <div class="type-option-desc">Simple true or false</div>
                        </label>
                    </div>
                </div>

                <div class="form-card">
                    <h3>📚 Course & Topic</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Course *</label>
                            <select name="course_id" required>
                                <option value="">-- Select Course --</option>
                                <?php 
                                $instructorCourses->data_seek(0);
                                while($course = $instructorCourses->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $course['course_id']; ?>">
                                    <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                            <small>Select the course this question belongs to</small>
                        </div>

                        <div class="form-group">
                            <label>Topic (Optional)</label>
                            <select name="topic_id">
                                <option value="">-- No Topic --</option>
                                <?php while($topic = $topicsQuery->fetch_assoc()): ?>
                                <option value="<?php echo $topic['topic_id']; ?>">
                                    <?php echo htmlspecialchars($topic['topic_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                            <small>Helps students find related questions</small>
                        </div>
                    </div>
                </div>


                <div class="form-card">
                    <h3>❓ Question Details</h3>
                    <div class="form-group">
                        <label>Question Text *</label>
                        <textarea name="question_text" rows="4" required placeholder="Enter your question here..."></textarea>
                        <small>Write a clear and concise question</small>
                    </div>

                    <!-- Multiple Choice Options -->
                    <div id="mcqOptions">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Option A *</label>
                                <input type="text" name="option_a" placeholder="First option">
                            </div>
                            <div class="form-group">
                                <label>Option B *</label>
                                <input type="text" name="option_b" placeholder="Second option">
                            </div>
                            <div class="form-group">
                                <label>Option C (Optional)</label>
                                <input type="text" name="option_c" placeholder="Third option">
                            </div>
                            <div class="form-group">
                                <label>Option D (Optional)</label>
                                <input type="text" name="option_d" placeholder="Fourth option">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Correct Answer *</label>
                            <select name="correct_answer">
                                <option value="">-- Select Correct Answer --</option>
                                <option value="A">Option A</option>
                                <option value="B">Option B</option>
                                <option value="C">Option C</option>
                                <option value="D">Option D</option>
                            </select>
                        </div>
                    </div>

                    <!-- True/False Options -->
                    <div id="tfOptions" style="display: none;">
                        <div class="form-group">
                            <label>Correct Answer *</label>
                            <div class="tf-options-container">
                                <label class="tf-option-box" id="trueBox">
                                    <input type="radio" name="correct_answer_tf" value="A">
                                    <div class="tf-option-label">✓ TRUE</div>
                                </label>
                                <label class="tf-option-box" id="falseBox">
                                    <input type="radio" name="correct_answer_tf" value="B">
                                    <div class="tf-option-label">✗ FALSE</div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Explanation (Recommended)</label>
                        <textarea name="explanation" rows="3" placeholder="Explain why this is the correct answer..."></textarea>
                        <small>Help students understand the concept better</small>
                    </div>
                </div>


                <div class="form-card">
                    <h3>⚙️ Settings</h3>
                    <div class="form-group">
                        <label>Difficulty Level *</label>
                        <select name="difficulty_level" required>
                            <option value="Easy">😊 Easy</option>
                            <option value="Medium" selected>🤔 Medium</option>
                            <option value="Hard">😰 Hard</option>
                        </select>
                        <small>Help students choose appropriate questions for their level</small>
                    </div>
                </div>

                <div class="info-box">
                    <strong>💡 Tips for Creating Effective Practice Questions:</strong>
                    <ul>
                        <li>Make questions clear and unambiguous</li>
                        <li>Ensure all options are plausible (for multiple choice)</li>
                        <li>Add explanations to help students learn from mistakes</li>
                        <li>Cover important concepts from your course</li>
                        <li>Start with easier questions and progress to harder ones</li>
                        <li>Use True/False for simple fact-checking questions</li>
                        <li>Use Multiple Choice for more complex scenarios</li>
                    </ul>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="ManagePracticeQuestions.php" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" name="add_practice_question" class="btn btn-primary">
                        <span>✅</span> Create Practice Question
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
    <script>
        // Question type toggle
        const mcqOption = document.getElementById('mcqOption');
        const tfOption = document.getElementById('tfOption');
        const mcqOptions = document.getElementById('mcqOptions');
        const tfOptions = document.getElementById('tfOptions');

        mcqOption.addEventListener('click', function() {
            mcqOption.classList.add('selected');
            tfOption.classList.remove('selected');
            mcqOptions.style.display = 'block';
            tfOptions.style.display = 'none';
            
            // Update required fields
            document.querySelector('input[name="option_a"]').required = true;
            document.querySelector('input[name="option_b"]').required = true;
            document.querySelector('select[name="correct_answer"]').required = true;
            document.querySelectorAll('input[name="correct_answer_tf"]').forEach(el => el.required = false);
        });

        tfOption.addEventListener('click', function() {
            tfOption.classList.add('selected');
            mcqOption.classList.remove('selected');
            tfOptions.style.display = 'block';
            mcqOptions.style.display = 'none';
            
            // Update required fields
            document.querySelector('input[name="option_a"]').required = false;
            document.querySelector('input[name="option_b"]').required = false;
            document.querySelector('select[name="correct_answer"]').required = false;
            document.querySelectorAll('input[name="correct_answer_tf"]').forEach(el => el.required = true);
        });

        // True/False selection
        const trueBox = document.getElementById('trueBox');
        const falseBox = document.getElementById('falseBox');

        trueBox.addEventListener('click', function() {
            trueBox.classList.add('selected');
            falseBox.classList.remove('selected');
        });

        falseBox.addEventListener('click', function() {
            falseBox.classList.add('selected');
            trueBox.classList.remove('selected');
        });
    </script>
</body>
</html>
<?php $con->close(); ?>
