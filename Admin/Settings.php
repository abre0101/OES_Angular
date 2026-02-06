<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

// Check if user is logged in
if(!isset($_SESSION['username'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

// Validate user role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Administrator'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Dashboard</title>
    <link href="../assets/css/modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        @media (max-width: 1200px) {
            .settings-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .setting-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-decoration: none;
            display: block;
            position: relative;
            overflow: hidden;
        }
        
        .setting-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .setting-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }
        
        .setting-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2);
        }
        
        .setting-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
        }
        
        .setting-description {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .setting-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1rem;
        }
        
        .setting-action::after {
            content: '→';
            transition: transform 0.3s ease;
        }
        
        .setting-card:hover .setting-action::after {
            transform: translateX(5px);
        }
        
        .page-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            gap: 2rem;
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.05) 0%, rgba(0, 85, 170, 0.05) 100%);
            padding: 2rem;
            border-radius: var(--radius-lg);
            border: 2px solid rgba(0, 51, 102, 0.1);
        }
        
        .page-title-section h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .page-title-section h1 span {
            -webkit-text-fill-color: initial;
            background: none;
        }
        
        .page-subtitle {
            margin: 0;
            color: var(--text-secondary);
            font-size: 1.05rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>

    <div class="admin-main-content">
        <?php 
        $pageTitle = 'Settings';
        include 'header-component.php'; 
        ?>

        <div class="admin-content">
            <div class="page-header-actions">
                <div class="page-title-section">
                    <h1><span>⚙️</span> System Settings</h1>
                    <p class="page-subtitle">Manage system configuration, security, and maintenance</p>
                </div>
            </div>

            <div class="settings-grid">
                <!-- Security Logs -->
                <a href="SecurityLogs.php" class="setting-card">
                    <div class="setting-icon">🔒</div>
                    <div class="setting-title">Security Logs</div>
                    <div class="setting-description">
                        View and monitor system security logs, login attempts, and user activities
                    </div>
                    <div class="setting-action">
                        View Security Logs
                    </div>
                </a>

                <!-- Reset Password -->
                <a href="ResetPassword.php" class="setting-card">
                    <div class="setting-icon">🔑</div>
                    <div class="setting-title">Reset Password</div>
                    <div class="setting-description">
                        Change your administrator password and manage account security
                    </div>
                    <div class="setting-action">
                        Reset Password
                    </div>
                </a>

                <!-- Database Backup -->
                <a href="DatabaseBackup.php" class="setting-card">
                    <div class="setting-icon">💾</div>
                    <div class="setting-title">Database Backup</div>
                    <div class="setting-description">
                        Create and manage database backups to protect your data
                    </div>
                    <div class="setting-action">
                        Manage Backups
                    </div>
                </a>

                <!-- System Settings -->
                <a href="SystemSettings.php" class="setting-card">
                    <div class="setting-icon">⚙️</div>
                    <div class="setting-title">System Configuration</div>
                    <div class="setting-description">
                        Configure system-wide settings, preferences, and parameters
                    </div>
                    <div class="setting-action">
                        Configure System
                    </div>
                </a>

                <!-- Profile Settings -->
                <a href="EditProfile.php" class="setting-card">
                    <div class="setting-icon">👤</div>
                    <div class="setting-title">Profile Settings</div>
                    <div class="setting-description">
                        Update your personal information and account details
                    </div>
                    <div class="setting-action">
                        Edit Profile
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js?v=<?php echo time(); ?>"></script>
</body>
</html>
