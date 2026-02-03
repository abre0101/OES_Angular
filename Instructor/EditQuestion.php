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
$pageTitle = "Edit Question";

$question_id = $_GET['id'] ?? 0;
$return_to_exam = $_GET['return'] ?? '';
$exam_id = $_GET['exam_id'] ?? 0;

// Get question details from questions table
$question = $con->query("SELECT q.*, c.course_code, c.course_name 
                         FROM questions q
                         LEFT JOIN courses c ON q.course_id = c.course_id
                         WHERE q.question_id = '$question_id'")->fetch_assoc();

if(!$question) {
    header("Location: ManageQuestions.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $question_text = mysqli_real_escape_string($con, $_POST['question']);
    $option_a = mysqli_real_escape_string($con, $_POST['option1']);
    $option_b = mysqli_real_escape_string($con, $_POST['option2']);
    $option_c = mysqli_real_escape_string($con, $_POST['option3']);
    $option_d = mysqli_real_escape_string($con, $_POST['option4']);
    $correct_answer = $_POST['answer'];
    
    $update = $con->prepare("UPDATE questions 
                             SET course_id = ?, 
                                 question_text = ?, 
                                 option_a = ?, 
                                 option_b = ?, 
                                 option_c = ?, 
                                 option_d = ?, 
                                 correct_answer = ?,
                                 updated_at = NOW()
                             WHERE question_id = ?");
    $update->bind_param("issssssi", $course_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);
    
    if($update->execute()) {
        // Redirect back to exam management if coming from there
        if($return_to_exam == 'exam' && $exam_id > 0) {
            header("Location: ManageExamQuestions.php?exam_id=" . $exam_id . "&success=updated");
            exit();
        }
        header("Location: ManageQuestions.php?success=1");
        exit();
    }
    $update->close();
}

// Get exams for this instructor
$instructor_id = $_SESSION['ID'];
$exams = $con->query("SELECT DISTINCT e.exam_id, e.exam_name, c.course_code
                      FROM exams e
                      INNER JOIN courses c ON e.course_id = c.course_id
                      INNER JOIN instructor_courses ic ON c.course_id = ic.course_id
                      WHERE ic.instructor_id = $instructor_id AND e.created_by = $instructor_id
                      ORDER BY e.exam_name");

// Get courses assigned to this instructor
$courses = $con->query("SELECT DISTINCT c.course_id, c.course_code, c.course_name
                        FROM courses c
                        INNER JOIN instructor_courses ic ON c.course_id = ic.course_id
                        WHERE ic.instructor_id = $instructor_id
                        ORDER BY c.course_code");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question - Instructor</title>
    <link href="../assets/css/modern-v2.css" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>

    <div class="admin-main-content">
        <?php include 'header-component.php'; ?>

        <div class="admin-content">
            <div class="page-header">
                <h1>✏️ Edit Question</h1>
                <p>UPDATE questions details</p>
            </div>

            <div class="form-wrapper">
                <form method="POST">
                    <div class="form-section">
                        <h3 class="form-section-title">Question Details</h3>
                        
                        <div class="form-group">
                            <label>Question ID</label>
                            <input type="text" class="form-control" value="<?php echo $question['question_id']; ?>" disabled>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Course *</label>
                                <select name="course_id" class="form-control" required>
                                    <?php 
                                    if($courses && $courses->num_rows > 0) {
                                        while($course = $courses->fetch_assoc()): 
                                    ?>
                                    <option value="<?php echo $course['course_id']; ?>" <?php echo (isset($question['course_id']) && $question['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['course_code'] . ' - ' . $course['course_name']); ?>
                                    </option>
                                    <?php 
                                        endwhile;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Question Text *</label>
                            <textarea name="question" class="form-control" rows="4" required><?php echo htmlspecialchars($question['question_text'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Answer Options</h3>
                        
                        <div class="form-group">
                            <label>Option A *</label>
                            <input type="text" name="option1" class="form-control" value="<?php echo htmlspecialchars($question['option_a'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Option B *</label>
                            <input type="text" name="option2" class="form-control" value="<?php echo htmlspecialchars($question['option_b'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Option C *</label>
                            <input type="text" name="option3" class="form-control" value="<?php echo htmlspecialchars($question['option_c'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Option D *</label>
                            <input type="text" name="option4" class="form-control" value="<?php echo htmlspecialchars($question['option_d'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Correct Answer *</label>
                            <select name="answer" class="form-control" required>
                                <option value="">Select Correct Answer</option>
                                <option value="A" <?php echo (($question['correct_answer'] ?? '') == 'A') ? 'selected' : ''; ?>>Option A</option>
                                <option value="B" <?php echo (($question['correct_answer'] ?? '') == 'B') ? 'selected' : ''; ?>>Option B</option>
                                <option value="C" <?php echo (($question['correct_answer'] ?? '') == 'C') ? 'selected' : ''; ?>>Option C</option>
                                <option value="D" <?php echo (($question['correct_answer'] ?? '') == 'D') ? 'selected' : ''; ?>>Option D</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            💾 Save Changes
                        </button>
                        <?php if($return_to_exam == 'exam' && $exam_id > 0): ?>
                        <a href="ManageExamQuestions.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-secondary">
                            Cancel
                        </a>
                        <?php else: ?>
                        <a href="ManageQuestions.php" class="btn btn-secondary">
                            Cancel
                        </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger" onclick="deleteQuestion()" style="margin-left: auto;">
                            🗑️ Delete Question
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">👁️ Question Preview</h3>
                </div>
                <div style="padding: 2rem;">
                    <div style="background: var(--bg-light); padding: 1.5rem; border-radius: var(--radius-md);">
                        <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: var(--primary-color);">
                            <?php echo htmlspecialchars($question['question_text'] ?? ''); ?>
                        </p>
                        <div style="margin-left: 1rem;">
                            <p style="margin: 0.5rem 0;"><strong>A.</strong> <?php echo htmlspecialchars($question['option_a'] ?? ''); ?> <?php if(($question['correct_answer'] ?? '') == 'A') echo '<span style="color: var(--success-color); font-weight: 700;">✓</span>'; ?></p>
                            <p style="margin: 0.5rem 0;"><strong>B.</strong> <?php echo htmlspecialchars($question['option_b'] ?? ''); ?> <?php if(($question['correct_answer'] ?? '') == 'B') echo '<span style="color: var(--success-color); font-weight: 700;">✓</span>'; ?></p>
                            <p style="margin: 0.5rem 0;"><strong>C.</strong> <?php echo htmlspecialchars($question['option_c'] ?? ''); ?> <?php if(($question['correct_answer'] ?? '') == 'C') echo '<span style="color: var(--success-color); font-weight: 700;">✓</span>'; ?></p>
                            <p style="margin: 0.5rem 0;"><strong>D.</strong> <?php echo htmlspecialchars($question['option_d'] ?? ''); ?> <?php if(($question['correct_answer'] ?? '') == 'D') echo '<span style="color: var(--success-color); font-weight: 700;">✓</span>'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
    <script>
        function deleteQuestion() {
            if(confirm('Are you sure you want to delete this question?')) {
                window.location.href = 'DeleteQuestion.php?id=<?php echo $question_id; ?>';
            }
        }
    </script>
</body>
</html>
<?php $con->close(); ?>
