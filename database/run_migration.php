<?php
// Run database migration to add question_type column

$con = new mysqli('localhost', 'root', '', 'oes_professional');

if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}

echo "Running migration: add_question_type_column.sql\n";
echo "==========================================\n\n";

// Add question_type column
$sql1 = "ALTER TABLE `practice_questions` 
         ADD COLUMN `question_type` ENUM('multiple_choice', 'true_false') NOT NULL DEFAULT 'multiple_choice' 
         AFTER `question_text`";

if ($con->query($sql1)) {
    echo "✓ Successfully added question_type column\n";
} else {
    if (strpos($con->error, 'Duplicate column') !== false) {
        echo "✓ Column question_type already exists\n";
    } else {
        echo "✗ Error adding column: " . $con->error . "\n";
    }
}

// Update existing records
$sql2 = "UPDATE `practice_questions` 
         SET `question_type` = 'multiple_choice' 
         WHERE `question_type` IS NULL OR `question_type` = ''";

if ($con->query($sql2)) {
    echo "✓ Updated existing records to multiple_choice\n";
    echo "  Rows affected: " . $con->affected_rows . "\n";
} else {
    echo "✗ Error updating records: " . $con->error . "\n";
}

echo "\n==========================================\n";
echo "Migration completed successfully!\n";

$con->close();
?>
