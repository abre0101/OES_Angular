<?php
// Include session manager
require_once(__DIR__ . "/utils/session_manager.php");

// Try to detect user type from multiple sources
$userType = null;

// First, try all possible session types to find an active one
$sessionTypes = ['Student', 'Instructor', 'Administrator', 'DepartmentHead'];
foreach ($sessionTypes as $type) {
    SessionManager::startSession($type);
    if (isset($_SESSION['UserType']) && $_SESSION['UserType'] === $type) {
        $userType = $type;
        break;
    }
}

// If no session found, check referer as fallback
if (!$userType) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($referer, '/Student/') !== false) {
        $userType = 'Student';
    } elseif (strpos($referer, '/Instructor/') !== false) {
        $userType = 'Instructor';
    } elseif (strpos($referer, '/Admin/') !== false) {
        $userType = 'Administrator';
    } elseif (strpos($referer, '/DepartmentHead/') !== false) {
        $userType = 'DepartmentHead';
    }
}

// Start appropriate session
if ($userType) {
    SessionManager::startSession($userType);
} else {
    // Start default session for public access
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_lifetime' => 86400,
            'cookie_path' => '/',
            'cookie_secure' => false,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax'
        ]);
    }
}

$isLoggedIn = isset($_SESSION['UserType']) && isset($_SESSION['Name']);
$userRole = '';
if ($isLoggedIn) {
    $userType = $_SESSION['UserType'] ?? '';
    switch($userType) {
        case 'Student': $userRole = 'student'; break;
        case 'Instructor': $userRole = 'instructor'; break;
        case 'DepartmentHead': $userRole = 'departmenthead'; break;
        case 'Administrator': $userRole = 'admin'; break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - Debre Markos University Health Campus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --accent-color: #d4af37;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --white: #ffffff;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 4px solid var(--accent-color);
        }

        .header-top {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            padding: 1.5rem 0;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .header-top .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }

        .university-branding {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .university-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
        }

        .university-info h1 {
            font-size: 1.85rem;
            font-weight: 900;
            color: #ffffff;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .university-info p {
            font-size: 1.15rem;
            color: #ffd700;
            font-weight: 700;
            margin: 0.35rem 0 0 0;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .main-nav {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 0;
            margin: 0;
            padding: 0;
            justify-content: center;
        }

        .nav-menu li a {
            display: block;
            padding: 1rem 2rem;
            color: #1a2b4a;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-menu li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: #1a2b4a;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-menu li a:hover,
        .nav-menu li a.active {
            background: rgba(26, 43, 74, 0.15);
        }

        .nav-menu li a:hover::after,
        .nav-menu li a.active::after {
            width: 80%;
        }

        .main-content {
            flex: 1;
            padding: 4rem 0;
            background: var(--white);
        }

        .page-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 900;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.25rem;
            color: var(--text-light);
        }

        .faq-item {
            background: var(--white);
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: var(--accent-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .faq-question {
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.05rem;
        }

        .faq-icon {
            color: var(--accent-color);
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .faq-answer {
            margin-top: 1rem;
            color: var(--text-light);
            display: none;
            line-height: 1.8;
        }

        .faq-answer ul, .faq-answer ol {
            margin-left: 1.5rem;
            margin-top: 0.5rem;
        }

        .faq-answer li {
            margin-bottom: 0.5rem;
        }

        .faq-item.active {
            border-color: var(--accent-color);
            background: #fafbfc;
        }

        .faq-item.active .faq-answer {
            display: block;
            animation: slideDown 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-outline {
            background: transparent;
            color: white;
            border-color: white;
        }

        .btn-outline:hover {
            background: white;
            color: #1a2b4a;
        }

        .footer {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            color: var(--white);
            padding: 2rem 0;
            text-align: center;
            border-top: 4px solid var(--accent-color);
        }

        .footer p {
            margin: 0;
            color: rgba(255, 255, 255, 0.9);
        }

        @media (max-width: 768px) {
            .header-top .container {
                flex-direction: column;
                text-align: center;
            }

            .university-branding {
                flex-direction: column;
            }

            .university-info h1 {
                font-size: 1.25rem;
            }

            .nav-menu {
                flex-direction: column;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="container">
                <div class="university-branding">
                    <img src="images/logo1.png" alt="DMU Logo" class="university-logo" onerror="this.style.display='none'">
                    <div class="university-info">
                        <h1>Debre Markos University Health Campus</h1>
                        <p>Online Examination System</p>
                    </div>
                </div>
                <div class="header-cta">
                    <a href="Help.php" class="btn btn-outline">← Back to Help</a>
                </div>
            </div>
        </div>
        <nav class="main-nav">
            <div class="container">
                <ul class="nav-menu">
                    <li><a href="index.php">🏠 Home</a></li>
                    <li><a href="AboutUs.php">ℹ️ About Us</a></li>
                    <li><a href="Help.php" class="active">❓ Help</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>📚 Frequently Asked Questions</h1>
                <p>Find answers to common questions about the examination system</p>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>How do I login to the system?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>Navigate to the home page and click on either "Student Login" or "Staffs Login" depending on your role. Enter your credentials (username and password) and click the login button to access your dashboard.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>What should I do if I forget my password?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>Click on the "Forgot Password?" link on the login page. You'll need to provide your registered email or student ID. Follow the instructions sent to your email to reset your password. If you encounter issues, contact the IT support team.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>What are the system requirements?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>To use the system effectively, you need:</p>
                    <ul>
                        <li>A modern web browser (Chrome, Firefox, Safari, or Edge - latest version)</li>
                        <li>Stable internet connection (minimum 2 Mbps recommended)</li>
                        <li>JavaScript and cookies enabled in your browser</li>
                        <li>Screen resolution of at least 1024x768 pixels</li>
                    </ul>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>How do I take an exam?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>Follow these steps to take an exam:</p>
                    <ol>
                        <li>Login to your student account</li>
                        <li>Navigate to the "Available Exams" section</li>
                        <li>Select the exam you want to take</li>
                        <li>Read all instructions carefully</li>
                        <li>Click "Start Exam" when ready</li>
                        <li>Answer questions within the time limit</li>
                        <li>Review your answers before submitting</li>
                        <li>Click "Submit Exam" to finalize</li>
                    </ol>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>What happens if my internet disconnects during an exam?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>The system automatically saves your progress periodically. If your connection drops, simply log back in and continue from where you left off. Note that the exam timer continues running, so reconnect as quickly as possible.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>How can I view my exam results?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>Login to your student dashboard and navigate to the "Results" or "My Exams" section. Results are typically available within 24-48 hours for objective exams and 3-7 days for subjective exams, depending on instructor grading.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Can I use my mobile phone to take exams?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>Yes, the system is fully responsive and works on mobile devices. However, we strongly recommend using a computer or tablet for a better experience, especially for exams with complex questions, essays, or file uploads.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>How do I report a technical issue?</span>
                    <span class="faq-icon">▼</span>
                </div>
                <div class="faq-answer">
                    <p>Contact our technical support team immediately via email at support@dmu.edu.et or call +251-900469816. Provide details about the issue, including screenshots if possible, your student ID, and the time the issue occurred.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Debre Markos University Health Campus. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                this.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
