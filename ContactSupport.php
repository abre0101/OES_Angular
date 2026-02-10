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
    <title>Contact Support - Debre Markos University Health Campus</title>
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
            max-width: 700px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .contact-card {
            background: linear-gradient(135deg, #1a2b4a 0%, #2c5364 100%);
            color: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            border: 3px solid var(--accent-color);
            transition: all 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .contact-card h3 {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: var(--accent-color);
        }

        .contact-card p {
            margin-bottom: 1rem;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        .contact-card strong {
            color: var(--accent-color);
        }

        .info-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 2.5rem;
            margin-top: 3rem;
            border: 2px solid #e5e7eb;
        }

        .info-section h2 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--accent-color);
        }

        .info-section ul {
            list-style: none;
            padding: 0;
        }

        .info-section li {
            padding: 0.75rem 0;
            padding-left: 2rem;
            position: relative;
            color: var(--text-dark);
            font-size: 1.05rem;
        }

        .info-section li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--accent-color);
            font-weight: bold;
            font-size: 1.2rem;
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

            .contact-grid {
                grid-template-columns: 1fr;
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
                <h1>Contact Support</h1>
                <p>Get in touch with our technical support team for assistance</p>
            </div>

            <div class="contact-grid">
                <div class="contact-card">
                    <h3>📧 Email Support</h3>
                    <p><strong>Email:</strong> debremarkos@dmu.edu.et</p>
                    <p><strong>Response Time:</strong> Within 24 hours</p>
                    <p>Best for non-urgent inquiries, detailed technical issues, and documentation requests</p>
                </div>

                <div class="contact-card">
                    <h3>📞 Phone Support</h3>
                    <p><strong>Phone:</strong> +251-900469816</p>
                    <p><strong>Hours:</strong> Monday - Friday<br>8:00 AM - 5:00 PM</p>
                    <p>For urgent issues during exam periods and immediate assistance</p>
                </div>

                <div class="contact-card">
                    <h3>🏢 In-Person Support</h3>
                    <p><strong>Location:</strong> IT Support Office<br>Debre Markos University Health Campus<br>Debre Markos, Ethiopia</p>
                    <p><strong>Hours:</strong> Monday - Friday<br>8:00 AM - 5:00 PM</p>
                </div>
            </div>

            <div class="info-section">
                <h2>When Contacting Support</h2>
                <ul>
                    <li>Have your Student ID or Employee ID ready</li>
                    <li>Describe the issue clearly and provide specific details</li>
                    <li>Include screenshots if possible</li>
                    <li>Note the date and time when the issue occurred</li>
                    <li>Specify which page or feature you were using</li>
                </ul>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Debre Markos University Health Campus. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
