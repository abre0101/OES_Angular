<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Instructor session
SessionManager::startSession('Instructor');

// Check if user is logged in
if(!isset($_SESSION['ID'])){
    header("Location: ../auth/staff-login.php");
    exit();
}

// Validate instructor role
if(!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Instructor'){
    SessionManager::destroySession();
    header("Location: ../auth/staff-login.php");
    exit();
}

$con = require_once(__DIR__ . "/../Connections/OES.php");
$pageTitle = "My Profile";

$instructor_id = $_SESSION['ID'];

// Get instructor details
$stmt = $con->prepare("SELECT * FROM instructors WHERE instructor_id = ?");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();
$stmt->close();

if(!$instructor) {
    echo "Instructor not found!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Instructor</title>
    <link href="../assets/css/modern-v2.css" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body.admin-layout { background: #f5f7fa; font-family: 'Poppins', sans-serif; }
        
        .page-header-modern {
            background: linear-gradient(135deg, #003366 0%, #0055aa 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 51, 102, 0.2);
            margin-bottom: 2rem;
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
        
        .profile-card { background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08); margin-bottom: 2rem; }
        .profile-header { text-align: center; padding: 2rem; background: linear-gradient(135deg, #003366 0%, #0055aa 100%); border-radius: 12px; color: white; margin-bottom: 2rem; }
        .profile-avatar { width: 120px; height: 120px; border-radius: 50%; background: white; color: #003366; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; margin: 0 auto 1rem; }
        .profile-name { font-size: 1.8rem; font-weight: 700; margin: 0; color: #ffffff; }
        .profile-role { font-size: 1rem; margin-top: 0.5rem; color: #e3f2fd; font-weight: 500; }
        .profile-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
        .info-item { padding: 1.5rem; background: #f8f9fa; border-radius: 8px; }
        .info-label { font-size: 0.85rem; color: #000000; margin-bottom: 0.5rem; font-weight: 600; text-transform: uppercase; }
        .info-value { font-size: 1.1rem; color: #000000; font-weight: 600; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, #003366 0%, #0055aa 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3); }
    </style>
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>

    <div class="admin-main-content">
        <?php include 'header-component.php'; ?>

        <div class="admin-content">
            <div class="page-header-modern">
                <h1>👤 My Profile</h1>
                <p>View and manage your profile information</p>
            </div>

            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($instructor['full_name'], 0, 1)); ?>
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($instructor['full_name']); ?></h2>
                    <p class="profile-role">Instructor - <?php echo htmlspecialchars($_SESSION['Dept'] ?? 'N/A'); ?></p>
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <div class="info-label">Instructor ID</div>
                        <div class="info-value"><?php echo htmlspecialchars($instructor['instructor_code']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($instructor['username']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($instructor['email'] ?? 'Not set'); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?php echo htmlspecialchars($instructor['phone'] ?? 'Not set'); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo htmlspecialchars($instructor['gender'] ?? 'Not set'); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Account Status</div>
                        <div class="info-value">
                            <?php echo $instructor['is_active'] ? '<span style="color: #28a745;">✓ Active</span>' : '<span style="color: #dc3545;">✗ Inactive</span>'; ?>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem; text-align: center;">
                    <a href="EditProfile.php" class="btn btn-primary">
                        ✏️ Edit Profile
                    </a>
                    <a href="ChangePassword.php" class="btn btn-primary" style="margin-left: 1rem;">
                        🔒 Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
</body>
</html>
<?php $con->close(); ?>
