<?php
require_once(__DIR__ . "/../utils/session_manager.php");

// Start Administrator session
SessionManager::startSession('Administrator');

if(!isset($_SESSION['username'])){
    header("Location:../auth/institute-login.php");
    exit();
}

// Database connection
$con = require_once(__DIR__ . "/../Connections/OES.php");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get department head data
$Id = $_GET['Id'];
$sql = "SELECT dh.*, d.department_name 
        FROM department_heads dh 
        LEFT JOIN departments d ON dh.department_id = d.department_id 
        WHERE dh.department_head_id='".$Id."'";
$result = $con->query($sql);

if($row = $result->fetch_array()) {
    $Head_ID = $row['department_head_id'];
    $Head_Code = $row['head_code'];
    $Head_Name = $row['full_name'];
    $Email = $row['email'];
    $Phone = $row['phone'] ?? '';
    $UserName = $row['username'];
    $Department = isset($row['department_name']) ? $row['department_name'] : 'N/A';
    $DepartmentId = isset($row['department_id']) ? $row['department_id'] : '';
    $is_active = $row['is_active'];
    $Status = ($is_active == 1) ? 'Active' : 'Inactive';
} else {
    header("Location: DepartmentHead.php");
    exit();
}

// Get departments for dropdown
$query_dept = "SELECT * FROM departments ORDER BY department_name ASC";
$result_dept = $con->query($query_dept);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department Head - Admin Dashboard</title>
    <link href="../assets/css/modern-v2.css" rel="stylesheet">
    <link href="../assets/css/admin-modern-v2.css" rel="stylesheet">
    <link href="../assets/css/admin-sidebar.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .edit-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-section h3 {
            margin: 0 0 1rem 0;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1rem;
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body class="admin-layout">
    <?php include 'sidebar-component.php'; ?>

    <div class="admin-main-content">
        <?php 
        $pageTitle = 'Edit Department Head';
        include 'header-component.php'; 
        ?>

        <div class="admin-content">
            <div class="page-header">
                <h1>✏️ Edit Department Head</h1>
                <p>Update department head information</p>
            </div>

            <div class="edit-container">
                <div class="info-section">
                    <h3>Current Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">ID</span>
                            <span class="info-value"><?php echo $Head_ID; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Code</span>
                            <span class="info-value"><?php echo $Head_Code; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Username</span>
                            <span class="info-value"><?php echo $UserName; ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value"><?php echo $Status; ?></span>
                        </div>
                    </div>
                </div>

                <form method="post" action="UpdateDepartmentHead.php">
                    <input type="hidden" name="txtId" value="<?php echo $Head_ID; ?>">
                    
                    <div class="form-group">
                        <label for="txtName">Full Name:</label>
                        <input type="text" name="txtName" id="txtName" value="<?php echo $Head_Name; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="txtEmail">Email:</label>
                        <input type="email" name="txtEmail" id="txtEmail" value="<?php echo $Email; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="txtPhone">Phone:</label>
                        <input type="text" name="txtPhone" id="txtPhone" value="<?php echo $Phone; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cmbDept">Department:</label>
                        <select name="cmbDept" id="cmbDept" required>
                            <option value="">-- Select Department --</option>
                            <?php
                            while($dept = $result_dept->fetch_assoc()) {
                                $selected = ($dept['department_id'] == $DepartmentId) ? 'selected' : '';
                                echo '<option value="'.$dept['department_id'].'" '.$selected.'>'.$dept['department_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="cmbStatus">Status:</label>
                        <select name="cmbStatus" id="cmbStatus" required>
                            <option value="1" <?php echo ($is_active == 1) ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($is_active == 0) ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Update Department Head</button>
                        <a href="DepartmentHead.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-sidebar.js"></script>
</body>
</html>
<?php $con->close(); ?>
