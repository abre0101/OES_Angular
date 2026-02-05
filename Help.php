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
    // Determine user role based on session UserType
    $userType = $_SESSION['UserType'] ?? '';
    switch($userType) {
        case 'Student':
            $userRole = 'student';
            break;
        case 'Instructor':
            $userRole = 'instructor';
            break;
        case 'DepartmentHead':
            $userRole = 'departmenthead';
            break;
        case 'Administrator':
            $userRole = 'admin';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - Debre Markos University Health Campus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #1e3a8a;
            --accent-color: #d4af37;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --white: #ffffff;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: var(--bg-light);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
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

        /* Navigation */
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

        /* Main Content */
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
            max-width: 700px;
            margin: 0 auto;
        }

        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .quick-link-card {
            background: var(--white);
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .quick-link-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-color: var(--accent-color);
        }

        .quick-link-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            display: block;
        }

        .quick-link-card h3 {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
        }

        .quick-link-card p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* FAQ Section */
        .faq-section {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--accent-color);
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

        /* Contact Cards */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .contact-card {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            color: var(--white);
            border-radius: 16px;
            padding: 2rem;
            border: 2px solid var(--accent-color);
        }

        .contact-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--accent-color);
        }

        .contact-card p {
            margin-bottom: 0.75rem;
            line-height: 1.8;
        }

        .contact-card strong {
            color: var(--accent-color);
        }

        /* Tips Section */
        .tips-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 2.5rem;
            margin-top: 3rem;
            border: 2px solid #e5e7eb;
        }

        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .tip-box h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .tip-box ul {
            list-style: none;
            padding: 0;
        }

        .tip-box li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
            color: var(--text-light);
        }

        .tip-box li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--accent-color);
            font-weight: bold;
        }

        /* Buttons */
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

        /* Footer */
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

        /* Responsive */
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

            .quick-links,
            .contact-grid,
            .tips-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
                    <?php if ($isLoggedIn): ?>
                        <a href="<?php 
                            if ($userRole == 'student') echo 'Student/index.php';
                            elseif ($userRole == 'instructor') echo 'Instructor/index.php';
                            elseif ($userRole == 'departmenthead') echo 'DepartmentHead/index.php';
                            elseif ($userRole == 'admin') echo 'Admin/index.php';
                            else echo 'index.php';
                        ?>" class="btn btn-outline">← Dashboard</a>
                    <?php else: ?>
                        <a href="index.php#login" class="btn btn-outline">🔐 Login</a>
                    <?php endif; ?>
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Help & Support</h1>
                <p>Find answers to common questions and get assistance with the examination system</p>
            </div>

            <!-- Quick Info -->
            <div class="quick-links">
                <a href="FAQs.php" style="text-decoration: none; color: inherit;">
                    <div class="quick-link-card">
                        <span class="quick-link-icon">📚</span>
                        <h3>FAQs</h3>
                        <p>Find answers to frequently asked questions</p>
                    </div>
                </a>
                <a href="ContactSupport.php" style="text-decoration: none; color: inherit;">
                    <div class="quick-link-card">
                        <span class="quick-link-icon">📞</span>
                        <h3>Contact Support</h3>
                        <p>Get in touch with our technical support team</p>
                    </div>
                </a>
                <a href="ExamTips.php" style="text-decoration: none; color: inherit;">
                    <div class="quick-link-card">
                        <span class="quick-link-icon">💡</span>
                        <h3>Exam Tips</h3>
                        <p>Best practices for taking online exams</p>
                    </div>
                </a>
            </div>

            <!-- Quick Access Info -->
            <section style="margin-top: 4rem;">
                <div style="background: #f8fafc; border-radius: 16px; padding: 2.5rem; border: 2px solid #e5e7eb; text-align: center;">
                    <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-dark); margin-bottom: 1rem;">Need Immediate Assistance?</h2>
                    <p style="font-size: 1.15rem; color: var(--text-light); margin-bottom: 2rem;">Our support team is here to help you with any questions or technical issues</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="ContactSupport.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; font-size: 1.1rem; font-weight: 700; text-decoration: none; border-radius: 12px; background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%); color: white; transition: all 0.3s ease;">
                            📞 Contact Support
                        </a>
                        <a href="FAQs.php" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; font-size: 1.1rem; font-weight: 700; text-decoration: none; border-radius: 12px; background: transparent; color: #1a2b4a; border: 2px solid #1a2b4a; transition: all 0.3s ease;">
                            📚 View FAQs
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Debre Markos University Health Campus. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // FAQ accordion functionality
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', function() {
                // Close all other items
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== item) {
                        otherItem.classList.remove('active');
                    }
                });
                // Toggle current item
                this.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
