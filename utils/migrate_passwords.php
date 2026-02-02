<?php
/**
 * Password Migration Script
 * This script hashes all existing plain text passwords in the database
 * 
 * IMPORTANT: Run this script ONCE after implementing password encryption
 * 
 * Usage: php migrate_passwords.php
 * Or access via browser: http://yoursite.com/utils/migrate_passwords.php
 */

// Prevent direct browser access for security (optional - remove if you want browser access)
// if (php_sapi_name() !== 'cli') {
//     die('This script can only be run from command line');
// }

require_once(__DIR__ . '/password_helper.php');
require_once(__DIR__ . '/../Connections/OES.php');

echo "=== Password Migration Script ===\n\n";

$migrationResults = [
    'students' => 0,
    'instructors' => 0,
    'exam_committee_members' => 0,
    'administrators' => 0,
    'errors' => []
];

// Migrate Students
echo "Migrating student passwords...\n";
try {
    $result = $con->query("SELECT student_id, Password FROM students WHERE Password IS NOT NULL AND Password != ''");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Check if password is already hashed (bcrypt hashes start with $2y$)
            if (substr($row['Password'], 0, 4) !== '$2y$') {
                $hashedPassword = hashPassword($row['Password']);
                $stmt = $con->prepare("UPDATE students SET Password = ? WHERE student_id = ?");
                $stmt->bind_param("ss", $hashedPassword, $row['student_id']);
                if ($stmt->execute()) {
                    $migrationResults['students']++;
                }
                $stmt->close();
            }
        }
        echo "✓ Migrated {$migrationResults['students']} student passwords\n";
    }
} catch (Exception $e) {
    $migrationResults['errors'][] = "Students: " . $e->getMessage();
    echo "✗ Error migrating students: " . $e->getMessage() . "\n";
}

// Migrate Instructors
echo "Migrating instructor passwords...\n";
try {
    $result = $con->query("SELECT instructor_id, Password FROM instructors WHERE Password IS NOT NULL AND Password != ''");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (substr($row['Password'], 0, 4) !== '$2y$') {
                $hashedPassword = hashPassword($row['Password']);
                $stmt = $con->prepare("UPDATE instructors SET Password = ? WHERE instructor_id = ?");
                $stmt->bind_param("ss", $hashedPassword, $row['instructor_id']);
                if ($stmt->execute()) {
                    $migrationResults['instructors']++;
                }
                $stmt->close();
            }
        }
        echo "✓ Migrated {$migrationResults['instructors']} instructor passwords\n";
    }
} catch (Exception $e) {
    $migrationResults['errors'][] = "Instructors: " . $e->getMessage();
    echo "✗ Error migrating instructors: " . $e->getMessage() . "\n";
}

// Migrate Exam Committee Members
echo "Migrating exam committee passwords...\n";
try {
    $result = $con->query("SELECT committee_member_id, Password FROM exam_committee_members WHERE Password IS NOT NULL AND Password != ''");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (substr($row['Password'], 0, 4) !== '$2y$') {
                $hashedPassword = hashPassword($row['Password']);
                $stmt = $con->prepare("UPDATE exam_committee_members SET Password = ? WHERE committee_member_id = ?");
                $stmt->bind_param("ss", $hashedPassword, $row['committee_member_id']);
                if ($stmt->execute()) {
                    $migrationResults['exam_committee_members']++;
                }
                $stmt->close();
            }
        }
        echo "✓ Migrated {$migrationResults['exam_committee_members']} exam committee passwords\n";
    }
} catch (Exception $e) {
    $migrationResults['errors'][] = "Exam Committee: " . $e->getMessage();
    echo "✗ Error migrating exam committee: " . $e->getMessage() . "\n";
}

// Migrate Administrators
echo "Migrating administrator passwords...\n";
try {
    $result = $con->query("SELECT admin_id, Password FROM administrators WHERE Password IS NOT NULL AND Password != ''");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (substr($row['Password'], 0, 4) !== '$2y$') {
                $hashedPassword = hashPassword($row['Password']);
                $stmt = $con->prepare("UPDATE administrators SET Password = ? WHERE admin_id = ?");
                $stmt->bind_param("ss", $hashedPassword, $row['admin_id']);
                if ($stmt->execute()) {
                    $migrationResults['administrators']++;
                }
                $stmt->close();
            }
        }
        echo "✓ Migrated {$migrationResults['administrators']} administrator passwords\n";
    }
} catch (Exception $e) {
    $migrationResults['errors'][] = "Administrators: " . $e->getMessage();
    echo "✗ Error migrating administrators: " . $e->getMessage() . "\n";
}

$con->close();

// Summary
echo "\n=== Migration Summary ===\n";
echo "Students: {$migrationResults['students']}\n";
echo "Instructors: {$migrationResults['instructors']}\n";
echo "Exam Committee: {$migrationResults['exam_committee_members']}\n";
echo "Administrators: {$migrationResults['administrators']}\n";
$total = $migrationResults['students'] + $migrationResults['instructors'] + 
         $migrationResults['exam_committee_members'] + $migrationResults['administrators'];
echo "Total: $total passwords migrated\n";

if (!empty($migrationResults['errors'])) {
    echo "\nErrors encountered:\n";
    foreach ($migrationResults['errors'] as $error) {
        echo "- $error\n";
    }
}

echo "\n✓ Migration complete!\n";
echo "\nIMPORTANT: For security, delete or move this script after running it.\n";
?>
