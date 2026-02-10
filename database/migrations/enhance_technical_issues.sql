-- Migration to enhance technical_issues table with additional tracking fields
-- Run this migration to add browser info, system details, and better tracking

ALTER TABLE `technical_issues` 
ADD COLUMN `issue_type` ENUM('connectivity', 'system_error', 'submission_failure', 'display_issue', 'timer_error', 'login_problem', 'other') DEFAULT 'other' AFTER `issue_description`,
ADD COLUMN `browser_info` VARCHAR(255) NULL AFTER `issue_type`,
ADD COLUMN `os_info` VARCHAR(255) NULL AFTER `browser_info`,
ADD COLUMN `screen_resolution` VARCHAR(50) NULL AFTER `os_info`,
ADD COLUMN `ip_address` VARCHAR(45) NULL AFTER `screen_resolution`,
ADD COLUMN `screenshot_path` VARCHAR(500) NULL AFTER `ip_address`,
ADD COLUMN `admin_notes` TEXT NULL AFTER `screenshot_path`,
ADD COLUMN `resolved_by` INT(11) NULL AFTER `admin_notes`,
ADD COLUMN `resolved_at` TIMESTAMP NULL AFTER `resolved_by`,
ADD COLUMN `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium' AFTER `resolved_at`;

-- Add index for better query performance
CREATE INDEX idx_status_priority ON technical_issues(status, priority);
CREATE INDEX idx_reported_at ON technical_issues(reported_at);
