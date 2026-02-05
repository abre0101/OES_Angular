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

// Get course from URL parameter
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';

if (empty($selectedCourse)) {
    header("Location: practice-selection.php");
    exit();
}

// Get practice questions from database for selected course
require_once(__DIR__ . "/../Connections/OES.php");
$stmt = $con->prepare("SELECT pq.*, c.course_name 
    FROM practice_questions pq
    INNER JOIN courses c ON pq.course_id = c.course_id 
    WHERE c.course_name = ? AND pq.is_active = 1
    ORDER BY RAND() LIMIT 10");
$stmt->bind_param("s", $selectedCourse);
$stmt->execute();
$result = $stmt->get_result();
$questions = [];
while($row = mysqli_fetch_array($result)) {
    $questions[] = $row;
}
$totalQuestions = count($questions);
$stmt->close();
mysqli_close($con);

if ($totalQuestions == 0) {
    header("Location: practice-selection.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice Mode - Debre Markos University Health Campus</title>
    <link href="../assets/css/modern-v2.css" rel="stylesheet">
    <link href="../assets/css/exam-modern.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .practice-header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }
        
        .feedback-correct {
            background: rgba(40, 167, 69, 0.1);
            border: 2px solid var(--success-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-top: 1rem;
            color: var(--success-color);
            font-weight: 600;
        }
        
        .feedback-wrong {
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid var(--danger-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-top: 1rem;
            color: var(--danger-color);
            font-weight: 600;
        }
        
        .option-label.correct-answer {
            border-color: var(--success-color);
            background: rgba(40, 167, 69, 0.1);
        }
        
        .option-label.wrong-answer {
            border-color: var(--danger-color);
            background: rgba(220, 53, 69, 0.1);
        }
        
        .practice-badge {
            background: #6366f1;
            color: white;
        }
        
        .score-display {
            background: white;
            padding: 1rem 2rem;
            border-radius: var(--radius-lg);
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .score-item {
            text-align: center;
        }
        
        .score-item .label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .score-item .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Professional Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(30px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        .modal-icon {
            font-size: 4rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1a2b4a;
            text-align: center;
            margin-bottom: 1rem;
        }

        .modal-message {
            font-size: 1.05rem;
            color: #4b5563;
            text-align: center;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .modal-stats {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .modal-stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-stat-row:last-child {
            border-bottom: none;
        }

        .modal-stat-label {
            font-weight: 600;
            color: #1a2b4a;
        }

        .modal-stat-value {
            font-weight: 700;
            color: #6366f1;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .modal-btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            min-width: 140px;
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .modal-btn-secondary {
            background: #e5e7eb;
            color: #1a2b4a;
        }

        .modal-btn-secondary:hover {
            background: #d1d5db;
        }
    </style>
</head>
<body class="exam-body">
    <!-- Practice Header -->
    <div class="exam-header practice-header">
        <div class="exam-header-left">
            <img src="../images/logo1.png" alt="Logo" class="exam-logo">
            <div>
                <h2>Practice Mode</h2>
                <p><?php echo $_SESSION['Name']; ?> (<?php echo $_SESSION['ID']; ?>)</p>
            </div>
        </div>
        <div class="exam-header-center">
            <div class="practice-badge course-badge"><?php echo htmlspecialchars($selectedCourse); ?></div>
            <div class="score-display">
                <div class="score-item">
                    <div class="label">Correct</div>
                    <div class="value" id="correctCount">0</div>
                </div>
                <div class="score-item">
                    <div class="label">Wrong</div>
                    <div class="value" id="wrongCount">0</div>
                </div>
                <div class="score-item">
                    <div class="label">Score</div>
                    <div class="value" id="scoreDisplay">0%</div>
                </div>
            </div>
        </div>
        <div class="exam-header-right">
            <a href="practice-selection.php" class="btn btn-warning">
                🔄 Change Course
            </a>
        </div>
    </div>

    <!-- Main Practice Container -->
    <div class="exam-container">
        <!-- Question Area -->
        <div class="question-area">
            <div class="question-card" id="questionCard">
                <!-- Questions will be loaded here by JavaScript -->
            </div>

            <div id="feedbackArea"></div>

            <div class="question-navigation">
                <button class="btn btn-primary" id="checkBtn" onclick="checkAnswer()">
                    Check Answer
                </button>
                <button class="btn btn-secondary" id="nextBtn" onclick="nextQuestion()" style="display:none;">
                    Next Question →
                </button>
                <button class="btn btn-success" id="finishBtn" onclick="finishPractice()" style="display:none;">
                    Finish Practice
                </button>
            </div>
        </div>

        <!-- Question Panel Sidebar -->
        <div class="question-panel">
            <div class="panel-header">
                <h3>📊 Question Panel</h3>
                <p>Total: <?php echo $totalQuestions; ?> Questions</p>
            </div>

            <div class="panel-legend">
                <div class="legend-item">
                    <span class="legend-box answered"></span>
                    <span>Correct</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box skipped"></span>
                    <span>Wrong</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box" style="background: #e5e7eb;"></span>
                    <span>Not Attempted</span>
                </div>
            </div>

            <div class="panel-grid" id="questionPanel">
                <!-- Question numbers will be loaded here -->
            </div>

            <div class="panel-summary">
                <div class="summary-item">
                    <span>Attempted:</span>
                    <strong id="attemptedCount">0</strong>
                </div>
                <div class="summary-item">
                    <span>Remaining:</span>
                    <strong id="remainingCount"><?php echo $totalQuestions; ?></strong>
                </div>
            </div>

            <div class="panel-submit">
                <button class="btn btn-success btn-block" onclick="finishPractice()">
                    Finish Practice
                </button>
            </div>
        </div>
    </div>

    <!-- Professional Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-content" id="modalContent">
            <!-- Modal content will be inserted here -->
        </div>
    </div>

    <script>
        // Questions data from PHP
        const questions = <?php echo json_encode($questions); ?>;
        const totalQuestions = questions.length;
        let currentQuestion = 0;
        let answers = {};
        let questionStatus = {}; // 'correct', 'wrong', 'unattempted'
        let correctCount = 0;
        let wrongCount = 0;
        let hasChecked = false;

        // Initialize practice
        function initPractice() {
            createQuestionPanel();
            loadQuestion(0);
        }

        // Load question
        function loadQuestion(index) {
            currentQuestion = index;
            hasChecked = false;
            const q = questions[index];
            
            const questionHTML = `
                <div class="question-number">Question No. ${index + 1}</div>
                <div class="question-text">${q.question_text || 'Question text here'}</div>
                <div class="options-container">
                    <label class="option-label">
                        <input type="radio" name="answer" value="A">
                        <span class="option-text"><strong>(A)</strong> ${q.option_a || 'Option A'}</span>
                    </label>
                    <label class="option-label">
                        <input type="radio" name="answer" value="B">
                        <span class="option-text"><strong>(B)</strong> ${q.option_b || 'Option B'}</span>
                    </label>
                    <label class="option-label">
                        <input type="radio" name="answer" value="C">
                        <span class="option-text"><strong>(C)</strong> ${q.option_c || 'Option C'}</span>
                    </label>
                    <label class="option-label">
                        <input type="radio" name="answer" value="D">
                        <span class="option-text"><strong>(D)</strong> ${q.option_d || 'Option D'}</span>
                    </label>
                </div>
            `;
            
            document.getElementById('questionCard').innerHTML = questionHTML;
            document.getElementById('feedbackArea').innerHTML = '';
            
            // Add event listeners to options
            document.querySelectorAll('input[name="answer"]').forEach(input => {
                input.addEventListener('change', function() {
                    document.querySelectorAll('.option-label').forEach(label => {
                        label.classList.remove('selected');
                    });
                    this.parentElement.classList.add('selected');
                });
            });
            
            updateNavigationButtons();
            updateQuestionPanel();
        }

        // Check answer
        function checkAnswer() {
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                showModal({
                    icon: '⚠️',
                    title: 'No Answer Selected',
                    message: 'Please select an answer before checking.',
                    buttons: [
                        { text: 'OK', class: 'modal-btn-primary', action: 'close' }
                    ]
                });
                return;
            }

            hasChecked = true;
            const userAnswerLetter = selectedAnswer.value; // A, B, C, or D
            const q = questions[currentQuestion];
            
            // Get the text of the user's selected option
            const userAnswerText = q['option_' + userAnswerLetter.toLowerCase()];
            
            // Get correct answer from database - could be letter or full text
            const correctAnswerFromDB = q.correct_answer;
            
            // Find which letter corresponds to the correct answer
            let correctAnswerLetter = '';
            
            // Check if correct_answer is just a letter (A, B, C, D)
            if (correctAnswerFromDB && correctAnswerFromDB.length === 1 && /[A-D]/i.test(correctAnswerFromDB)) {
                correctAnswerLetter = correctAnswerFromDB.toUpperCase();
            } else {
                // Otherwise, match against option texts
                if (q.option_a === correctAnswerFromDB) correctAnswerLetter = 'A';
                else if (q.option_b === correctAnswerFromDB) correctAnswerLetter = 'B';
                else if (q.option_c === correctAnswerFromDB) correctAnswerLetter = 'C';
                else if (q.option_d === correctAnswerFromDB) correctAnswerLetter = 'D';
            }
            
            // Get the correct option text
            const correctAnswerText = q['option_' + correctAnswerLetter.toLowerCase()];
            
            const isCorrect = userAnswerLetter === correctAnswerLetter;

            // Save answer
            answers[currentQuestion] = userAnswerLetter;
            
            // Update status
            if (questionStatus[currentQuestion] === undefined) {
                if (isCorrect) {
                    correctCount++;
                    questionStatus[currentQuestion] = 'correct';
                } else {
                    wrongCount++;
                    questionStatus[currentQuestion] = 'wrong';
                }
                updateScore();
            }

            // Disable all options
            document.querySelectorAll('input[name="answer"]').forEach(input => {
                input.disabled = true;
            });

            // Highlight correct and wrong answers
            document.querySelectorAll('.option-label').forEach(label => {
                const input = label.querySelector('input');
                if (input.value === correctAnswerLetter) {
                    label.classList.add('correct-answer');
                }
                if (input.value === userAnswerLetter && !isCorrect) {
                    label.classList.add('wrong-answer');
                }
            });

            // Show feedback with explanation
            let feedbackHTML = '';
            if (isCorrect) {
                feedbackHTML = `<div class="feedback-correct">
                    <strong>✅ Correct! Well done!</strong>
                </div>`;
            } else {
                feedbackHTML = `<div class="feedback-wrong">
                    <strong>❌ Wrong!</strong><br>
                    <p style="margin: 0.5rem 0;">The correct answer is: <strong>(${correctAnswerLetter})</strong> ${correctAnswerText || 'N/A'}</p>
                </div>`;
            }
            
            // Add explanation if available
            if (q.explanation && q.explanation.trim() !== '') {
                feedbackHTML += `<div style="background: #f0f9ff; border: 2px solid #3b82f6; border-radius: var(--radius-md); padding: 1rem; margin-top: 1rem;">
                    <strong style="color: #1e40af;">💡 Explanation:</strong>
                    <p style="margin: 0.5rem 0 0 0; color: #1e3a8a;">${q.explanation}</p>
                </div>`;
            }
            
            document.getElementById('feedbackArea').innerHTML = feedbackHTML;

            // Update buttons
            document.getElementById('checkBtn').style.display = 'none';
            if (currentQuestion < totalQuestions - 1) {
                document.getElementById('nextBtn').style.display = 'inline-block';
            } else {
                document.getElementById('finishBtn').style.display = 'inline-block';
            }

            updateQuestionPanel();
        }

        // Next question
        function nextQuestion() {
            if (currentQuestion < totalQuestions - 1) {
                loadQuestion(currentQuestion + 1);
            }
        }

        // Jump to question
        function jumpToQuestion(index) {
            loadQuestion(index);
        }

        // Update navigation buttons
        function updateNavigationButtons() {
            document.getElementById('checkBtn').style.display = hasChecked ? 'none' : 'inline-block';
            document.getElementById('nextBtn').style.display = 'none';
            document.getElementById('finishBtn').style.display = 'none';
        }

        // Question Panel
        function createQuestionPanel() {
            const panel = document.getElementById('questionPanel');
            let html = '';
            for (let i = 0; i < totalQuestions; i++) {
                html += `<button class="panel-question" onclick="jumpToQuestion(${i})" id="panelQ${i}">${i + 1}</button>`;
            }
            panel.innerHTML = html;
        }

        function updateQuestionPanel() {
            for (let i = 0; i < totalQuestions; i++) {
                const btn = document.getElementById(`panelQ${i}`);
                btn.classList.remove('answered', 'skipped', 'current');
                
                if (questionStatus[i] === 'correct') {
                    btn.classList.add('answered');
                } else if (questionStatus[i] === 'wrong') {
                    btn.classList.add('skipped');
                }
                
                if (i === currentQuestion) {
                    btn.classList.add('current');
                }
            }
            
            updateSummary();
        }

        function updateSummary() {
            const attemptedCount = Object.keys(questionStatus).length;
            document.getElementById('attemptedCount').textContent = attemptedCount;
            document.getElementById('remainingCount').textContent = totalQuestions - attemptedCount;
        }

        function updateScore() {
            document.getElementById('correctCount').textContent = correctCount;
            document.getElementById('wrongCount').textContent = wrongCount;
            
            const attempted = correctCount + wrongCount;
            const percentage = attempted > 0 ? Math.round((correctCount / attempted) * 100) : 0;
            document.getElementById('scoreDisplay').textContent = percentage + '%';
        }

        // Finish practice
        function finishPractice() {
            const attempted = Object.keys(questionStatus).length;
            const percentage = attempted > 0 ? Math.round((correctCount / attempted) * 100) : 0;
            
            const statsHTML = `
                <div class="modal-stat-row">
                    <span class="modal-stat-label">Total Questions:</span>
                    <span class="modal-stat-value">${totalQuestions}</span>
                </div>
                <div class="modal-stat-row">
                    <span class="modal-stat-label">Attempted:</span>
                    <span class="modal-stat-value">${attempted}</span>
                </div>
                <div class="modal-stat-row">
                    <span class="modal-stat-label">Correct Answers:</span>
                    <span class="modal-stat-value" style="color: #10b981;">${correctCount}</span>
                </div>
                <div class="modal-stat-row">
                    <span class="modal-stat-label">Wrong Answers:</span>
                    <span class="modal-stat-value" style="color: #ef4444;">${wrongCount}</span>
                </div>
                <div class="modal-stat-row">
                    <span class="modal-stat-label">Final Score:</span>
                    <span class="modal-stat-value" style="font-size: 1.5rem; color: #6366f1;">${percentage}%</span>
                </div>
            `;
            
            showModal({
                icon: '🎯',
                title: 'Practice Session Complete!',
                message: 'Great job! Here\'s your performance summary:',
                stats: statsHTML,
                buttons: [
                    { text: 'New Practice', class: 'modal-btn-primary', action: () => window.location.reload() },
                    { text: 'Back to Dashboard', class: 'modal-btn-secondary', action: () => window.location.href = 'index.php' }
                ]
            });
        }

        // Professional Modal Function
        function showModal(options) {
            const modal = document.getElementById('modalOverlay');
            const content = document.getElementById('modalContent');
            
            let buttonsHTML = '';
            options.buttons.forEach(btn => {
                const action = btn.action === 'close' ? 'closeModal()' : `(${btn.action})()`;
                buttonsHTML += `<button class="modal-btn ${btn.class}" onclick="${action}">${btn.text}</button>`;
            });
            
            content.innerHTML = `
                <div class="modal-icon">${options.icon}</div>
                <h2 class="modal-title">${options.title}</h2>
                <p class="modal-message">${options.message}</p>
                ${options.stats ? `<div class="modal-stats">${options.stats}</div>` : ''}
                <div class="modal-buttons">
                    ${buttonsHTML}
                </div>
            `;
            
            modal.classList.add('active');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Close modal on overlay click
        document.getElementById('modalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Initialize on load
        window.onload = initPractice;
    </script>
</body>
</html>
