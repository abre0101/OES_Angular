-- Migration: Add question_type support to practice_questions table
-- This allows True/False questions in addition to Multiple Choice

-- Add question_type column
ALTER TABLE `practice_questions` 
ADD COLUMN `question_type` ENUM('multiple_choice','true_false') DEFAULT 'multiple_choice' AFTER `topic_id`;

-- Make option columns nullable (for True/False questions)
ALTER TABLE `practice_questions` 
MODIFY COLUMN `option_a` VARCHAR(500) DEFAULT NULL,
MODIFY COLUMN `option_b` VARCHAR(500) DEFAULT NULL;

-- Update correct_answer enum to include True/False
ALTER TABLE `practice_questions` 
MODIFY COLUMN `correct_answer` ENUM('A','B','C','D','True','False') NOT NULL;

-- Set existing questions to multiple_choice type
UPDATE `practice_questions` SET `question_type` = 'multiple_choice' WHERE `question_type` IS NULL;

SELECT 'Migration completed: practice_questions table now supports True/False questions' as message;
