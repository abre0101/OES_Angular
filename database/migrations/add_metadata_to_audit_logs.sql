-- Migration: Add metadata column to audit_logs table
-- This adds a JSON column for flexible additional data storage
-- Run this on existing databases to update the schema

USE `oes_professional`;

-- Add metadata column if it doesn't exist
ALTER TABLE `audit_logs` 
ADD COLUMN IF NOT EXISTS `metadata` json DEFAULT NULL AFTER `user_agent`;

-- Increase action field length for more detailed descriptions
ALTER TABLE `audit_logs` 
MODIFY COLUMN `action` varchar(255) NOT NULL;

-- Add 'unknown' to user_type enum for failed login attempts
ALTER TABLE `audit_logs` 
MODIFY COLUMN `user_type` enum('student','instructor','department_head','admin','unknown') DEFAULT NULL;

-- Add index on table_name for better query performance
ALTER TABLE `audit_logs` 
ADD INDEX IF NOT EXISTS `idx_table_name` (`table_name`);

SELECT 'Migration completed successfully!' as message;
