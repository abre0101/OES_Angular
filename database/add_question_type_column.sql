-- Migration: Add question_type column to practice_questions table
-- Date: 2026-02-03
-- Description: Adds support for multiple question types (multiple_choice, true_false)

-- Add question_type column
ALTER TABLE `practice_questions` 
ADD COLUMN `question_type` ENUM('multiple_choice', 'true_false') NOT NULL DEFAULT 'multiple_choice' 
AFTER `question_text`;

-- Update existing records to be multiple_choice
UPDATE `practice_questions` 
SET `question_type` = 'multiple_choice' 
WHERE `question_type` IS NULL OR `question_type` = '';

SELECT 'Migration completed successfully! question_type column added to practice_questions table.' as message;
