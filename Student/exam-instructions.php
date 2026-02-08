<?php
require_once(__DIR__ . "/../utils/session_manager.php");
SessionManager::startSession('Student');

if(!isset($_SESSION['Name'])){
    header("Location: ../index.php");
    exit();
}

if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Student'){
    SessionManager::destroySession();
    header("Location: ../auth/student-login.php");
    exit();
}

// Get schedule information
$scheduleId = isset($_GET['exam_id']) ? $_GET['exam_id'] : null;
$examInfo = null;

$con = require_once(__DIR__ . "/../Connections/OES.php"); $con;

// Get student department name and student code
$studentId = $_SESSION['ID'];
$stmt = $con->prepare("SELECT s.student_code, d.department_name 
                       FROM students s 
                       LEFT JOIN departments d ON s.department_id = d.department_id 
                       WHERE s.student_id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$studentInfo = $result->fetch_assoc();
$studentCode = $studentInfo['student_code'];
$departmentName = $studentInfo['department_name'];
$stmt->close();

if ($scheduleId) {
    $stmt = $con->prepare("SELECT es.*, ec.category_name as exam_type_name, c.course_name, c.course_code
                           FROM exams es 
                           LEFT JOIN exam_categories ec ON es.exam_category_id = ec.exam_category_id 
                           LEFT JOIN courses c ON es.course_id = c.course_id
                           WHERE es.exam_id = ?");
    $stmt->bind_param("i", $scheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    $examInfo = $result->fetch_assoc();
    $stmt->close();
}

mysqli_close($con);

if (!$examInfo) {
    header("Location: StartExam.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Instructions - Debre Markos University Health Campus</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../images/istockphoto-1772381872-612x612.jpg') center/cover no-repeat;
            opacity: 0.15;
            z-index: 0;
        }

        .exam-instructions-container {
            max-width: 900px;
            width: 100%;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.6s ease;
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

        .instructions-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(212, 175, 55, 0.3);
        }

        .instructions-header {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            padding: 3rem 2.5rem;
            text-align: center;
            color: white;
            border-bottom: 4px solid #d4af37;
        }

        .instructions-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1.5rem;
            filter: brightness(0) invert(1);
        }

        .instructions-header h1 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            color: #ffd700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .instructions-header p {
            font-size: 1.1rem;
            margin: 0.5rem 0;
            color: rgba(255, 255, 255, 0.95);
        }

        .instructions-body {
            padding: 2.5rem;
        }

        .alert {
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 500;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border: 2px solid #ffc107;
            border-left: 5px solid #ff9800;
            color: #856404;
        }

        h3 {
            color: #1a2b4a;
            font-size: 1.4rem;
            font-weight: 800;
            margin: 2rem 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions-list {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .instructions-list li {
            padding: 1rem 1.5rem;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 10px;
            border-left: 4px solid #d4af37;
            font-size: 1rem;
            color: #1a2b4a;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .instructions-list li:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left-color: #1a2b4a;
        }

        .marking-scheme {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .marking-item {
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .marking-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .marking-item.positive {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
        }

        .marking-item.negative {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 2px solid #dc3545;
        }

        .marking-item.neutral {
            background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
            border: 2px solid #6c757d;
        }

        .marking-icon {
            font-size: 2.5rem;
        }

        .marking-item strong {
            display: block;
            font-size: 1.1rem;
            color: #1a2b4a;
            margin-bottom: 0.25rem;
        }

        .marking-item p {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
            color: #1a2b4a;
        }

        .student-info {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px solid #d4af37;
            margin: 2rem 0;
        }

        .student-info h3 {
            margin-top: 0;
        }

        .student-info p {
            margin: 0.75rem 0;
            font-size: 1rem;
            color: #1a2b4a;
            font-weight: 600;
        }

        .agreement-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 12px;
            margin: 2rem 0;
            border: 2px solid #dee2e6;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a2b4a;
        }

        .checkbox-container input[type="checkbox"] {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: #28a745;
        }

        .instructions-actions {
            display: flex;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1.25rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .btn-success:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .instructions-header {
                padding: 2rem 1.5rem;
            }

            .instructions-header h1 {
                font-size: 1.8rem;
            }

            .instructions-body {
                padding: 1.5rem;
            }

            .marking-scheme {
                grid-template-columns: 1fr;
            }

            .instructions-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="exam-instructions-container">
        <div class="instructions-card">
            <div class="instructions-header">
                <img src="../images/logo1.png" alt="Logo" class="instructions-logo" onerror="this.style.display='none'">
                <h1>📋 Examination Instructions</h1>
                <p style="font-size: 1.3rem; font-weight: 700;"><?php echo htmlspecialchars($examInfo['exam_name']); ?></p>
                <p style="font-size: 1rem; opacity: 0.9;">
                    <?php echo htmlspecialchars($examInfo['course_code']); ?> - <?php echo htmlspecialchars($examInfo['course_name']); ?>
                </p>
                <p style="font-size: 0.95rem; opacity: 0.85; margin-top: 1rem;">
                    📅 <?php echo date('M d, Y', strtotime($examInfo['exam_date'])); ?> | 
                    ⏱️ <?php echo $examInfo['duration_minutes']; ?> minutes | 
                    📊 <?php echo htmlspecialchars($examInfo['exam_type_name']); ?>
                </p>
            </div>

            <div class="instructions-body">
                <div class="alert alert-warning">
                    <strong>⚠️ Read Carefully:</strong> This exam is timed and will start immediately when you click "Agree and Start Exam". Make sure you're in a quiet environment with stable internet before beginning.
                </div>

                <h3>📝 Before You Begin</h3>
                <ul class="instructions-list">
                    <li>⏱️ The exam timer starts immediately when you click "Agree and Proceed"</li>
                    <li>⏰ You have exactly <?php echo $examInfo['duration_minutes']; ?> minutes to complete all questions</li>
                    <li>🔒 The exam will automatically submit when time expires</li>
                    <li>📶 Ensure you have a stable internet connection throughout the exam</li>
                    <li>💻 Use a desktop or laptop for the best experience</li>
                </ul>

                <h3>📊 How to Take the Exam</h3>
                <ul class="instructions-list">
                    <li>✅ Each question has multiple choice options - select the best answer</li>
                    <li>🔄 You can navigate between questions using Next/Previous buttons</li>
                    <li>🟢 Answered questions will be highlighted in green</li>
                    <li>⚪ Unanswered questions remain white - you can return to them later</li>
                    <li>📤 Click "Submit Exam" when you're done, or it will auto-submit at time-out</li>
                </ul>

                <h3>💯 Scoring System</h3>
                <div class="marking-scheme">
                    <div class="marking-item positive">
                        <span class="marking-icon">✅</span>
                        <div>
                            <strong>Correct Answer</strong>
                            <p>Full Marks</p>
                        </div>
                    </div>
                    <div class="marking-item negative">
                        <span class="marking-icon">❌</span>
                        <div>
                            <strong>Wrong Answer</strong>
                            <p>No Marks</p>
                        </div>
                    </div>
                    <div class="marking-item neutral">
                        <span class="marking-icon">⚪</span>
                        <div>
                            <strong>Unanswered</strong>
                            <p>No Marks</p>
                        </div>
                    </div>
                </div>

                <h3>⚠️ Important Rules - DO NOT:</h3>
                <ul class="instructions-list">
                    <li>🚫 Refresh or reload the page during the exam</li>
                    <li>🚫 Close the browser window or tab</li>
                    <li>🚫 Use the browser back button</li>
                    <li>🚫 Open multiple tabs or windows</li>
                    <li>🚫 Leave the exam page - your progress may be lost</li>
                </ul>

                <div class="student-info">
                    <h3>👤 Your Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['Name']); ?></p>
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($studentCode); ?></p>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($departmentName); ?></p>
                </div>

                <div class="agreement-section">
                    <label class="checkbox-container">
                        <input type="checkbox" id="agreeCheckbox">
                        <span class="checkbox-label">I have read and understood all the instructions and agree to follow the exam rules</span>
                    </label>
                </div>

                <div class="instructions-actions">
                    <a href="StartExam.php" class="btn btn-secondary">← Cancel</a>
                    <button id="proceedBtn" class="btn btn-success" disabled onclick="startExam()">
                        Agree and Start Exam ➡️
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const agreeCheckbox = document.getElementById('agreeCheckbox');
        const proceedBtn = document.getElementById('proceedBtn');

        agreeCheckbox.addEventListener('change', function() {
            proceedBtn.disabled = !this.checked;
        });

        function startExam() {
            if (confirm('Ready to begin? The exam will start immediately.')) {
                window.location.href = 'exam-interface.php?exam_id=<?php echo $scheduleId; ?>';
            }
        }
    </script>
</body>
</html>
