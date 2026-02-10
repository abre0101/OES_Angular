-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2026 at 08:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oes_professional`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`admin_id`, `username`, `password`, `full_name`, `email`, `phone`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@dmu.edu.et', '+251911000001', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('student','instructor','department_head','admin','unknown') DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `user_type`, `action`, `table_name`, `record_id`, `old_value`, `new_value`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES
(1, 1, 'student', 'Login successful - Username: alem.h', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"alem.h\",\"timestamp\":\"2026-02-09 08:45:47\"}', '2026-02-09 07:45:47'),
(2, NULL, 'student', 'Login failed - Username: abebe.t', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":false,\"username\":\"abebe.t\",\"timestamp\":\"2026-02-09 08:52:54\"}', '2026-02-09 07:52:54'),
(3, NULL, 'student', 'Login failed - Username: abebe.t', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":false,\"username\":\"abebe.t\",\"timestamp\":\"2026-02-09 08:53:33\"}', '2026-02-09 07:53:33'),
(4, 0, 'admin', 'Logout', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"action_type\":\"logout\",\"timestamp\":\"2026-02-09 09:32:50\"}', '2026-02-09 08:32:50'),
(5, 1, 'department_head', 'Login successful - Username: solomon.k', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"solomon.k\",\"timestamp\":\"2026-02-09 09:33:00\"}', '2026-02-09 08:33:00'),
(6, 1, 'instructor', 'Login successful - Username: abebe.t', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"abebe.t\",\"timestamp\":\"2026-02-09 09:45:41\"}', '2026-02-09 08:45:41'),
(7, 1, 'student', 'Login successful - Username: alem.h', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"alem.h\",\"timestamp\":\"2026-02-10 07:15:28\"}', '2026-02-10 06:15:28'),
(8, 1, 'admin', 'Login successful - Username: admin', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"admin\",\"timestamp\":\"2026-02-10 07:24:52\"}', '2026-02-10 06:24:52'),
(9, 1, 'admin', 'Login successful - Username: admin', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"admin\",\"timestamp\":\"2026-02-10 07:25:20\"}', '2026-02-10 06:25:20'),
(10, 1, 'admin', 'Login successful - Username: admin', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"admin\",\"timestamp\":\"2026-02-10 07:26:59\"}', '2026-02-10 06:26:59'),
(11, 1, 'admin', 'Login successful - Username: admin', 'authentication', NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"event_type\":\"authentication\",\"success\":true,\"username\":\"admin\",\"timestamp\":\"2026-02-10 07:52:09\"}', '2026-02-10 06:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(200) NOT NULL,
  `credit_hours` int(11) DEFAULT 3,
  `semester` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `department_id`, `course_code`, `course_name`, `credit_hours`, `semester`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'NURS101', 'Fundamentals of Nursing', 4, 1, 'Introduction to basic nursing principles and practices', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(2, 1, 'NURS102', 'Anatomy and Physiology for Nurses', 5, 1, 'Study of human body structure and function', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(3, 1, 'NURS103', 'Medical-Surgical Nursing I', 4, 2, 'Care of adult patients with medical-surgical conditions', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(4, 2, 'MIDW101', 'Introduction to Midwifery', 3, 1, 'Fundamentals of midwifery practice', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(5, 2, 'MIDW102', 'Reproductive Health', 4, 1, 'Women\'s reproductive health and family planning', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(6, 3, 'PHO101', 'Introduction to Public Health', 3, 1, 'Overview of public health principles', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(7, 3, 'PHO102', 'Epidemiology', 4, 1, 'Study of disease patterns and prevention', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(8, 4, 'ANES101', 'Fundamentals of Anesthesia', 4, 1, 'Basic principles of anesthesia', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(9, 4, 'ANES102', 'Pharmacology for Anesthesia', 4, 1, 'Anesthetic drugs and their effects', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(10, 5, 'MLT101', 'Clinical Chemistry', 4, 1, 'Chemical analysis of body fluids', 1, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(11, 5, 'MLT102', 'Hematology', 4, 1, 'Study of blood and blood disorders', 1, '2026-02-08 18:02:22', '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `faculty_id`, `department_code`, `department_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'NURS', 'Nursing', 'Bachelor of Science in Nursing', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(2, 1, 'MIDW', 'Midwifery', 'Bachelor of Science in Midwifery', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(3, 1, 'PHO', 'Public Health Officer', 'Public Health Officer Program', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(4, 1, 'ANES', 'Anesthesia', 'Anesthesia Technology Program', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21'),
(5, 1, 'MLT', 'Medical Laboratory Technology', 'Medical Laboratory Science', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `department_heads`
--

CREATE TABLE `department_heads` (
  `department_head_id` int(11) NOT NULL,
  `head_code` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_heads`
--

INSERT INTO `department_heads` (`department_head_id`, `head_code`, `username`, `password`, `full_name`, `email`, `phone`, `department_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'DH001', 'solomon.k', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Solomon Kebede', 'solomon.k@dmu.edu.et', '+251911234580', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(2, 'DH002', 'rahel.t', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Rahel Tesfaye', 'rahel.t@dmu.edu.et', '+251911234581', 2, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(3, 'DH003', 'yared.m', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Yared Mengistu', 'yared.m@dmu.edu.et', '+251911234582', 3, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(4, 'DH004', 'helen.w', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Helen Worku', 'helen.w@dmu.edu.et', '+251911234583', 4, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(5, 'DH005', 'daniel.a', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Daniel Alemu', 'daniel.a@dmu.edu.et', '+251911234584', 5, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `exam_category_id` int(11) NOT NULL,
  `exam_name` varchar(200) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL,
  `total_marks` int(11) DEFAULT 100,
  `pass_marks` int(11) DEFAULT 50,
  `instructions` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `approval_status` enum('draft','pending','approved','rejected','revision') DEFAULT 'draft',
  `submitted_at` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approval_comments` text DEFAULT NULL,
  `revision_count` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `course_id`, `exam_category_id`, `exam_name`, `exam_date`, `start_time`, `end_time`, `duration_minutes`, `total_marks`, `pass_marks`, `instructions`, `is_active`, `approval_status`, `submitted_at`, `approved_by`, `approved_at`, `approval_comments`, `revision_count`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Fundamentals of Nursing - Midterm', '2026-02-08', '21:10:00', '22:40:00', 90, 20, 10, 'Read all questions carefully. Choose the best answer.', 1, 'approved', '2026-02-01 10:00:00', 1, '2026-02-02 14:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:09:56'),
(2, 1, 2, 'Fundamentals of Nursing - Final Exam', '2026-05-20', '09:00:00', '11:00:00', 120, 30, 15, 'Comprehensive final exam covering all course material.', 1, 'approved', '2026-04-01 10:00:00', 1, '2026-04-02 14:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(3, 1, 3, 'Fundamentals of Nursing - Quiz 1', '2026-02-08', '21:00:00', '21:30:00', 30, 10, 5, 'Quick quiz on chapters 1-3.', 1, 'approved', '2026-02-10 09:00:00', 1, '2026-02-11 10:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:14:58'),
(4, 1, 3, 'Fundamentals of Nursing - Quiz 2', '2026-04-10', '10:00:00', '10:30:00', 30, 10, 5, 'Quiz on chapters 4-6.', 1, 'approved', '2026-03-25 09:00:00', 1, '2026-03-26 10:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(5, 2, 1, 'Anatomy and Physiology - Midterm', '2026-03-16', '09:00:00', '10:30:00', 90, 20, 10, 'Answer all questions. Use of notes is not permitted.', 1, 'approved', '2026-02-01 11:00:00', 1, '2026-02-02 15:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(6, 2, 2, 'Anatomy and Physiology - Final Exam', '2026-05-21', '09:00:00', '11:00:00', 120, 30, 15, 'Final examination. No materials allowed.', 1, 'approved', '2026-04-01 11:00:00', 1, '2026-04-02 15:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(7, 2, 3, 'Anatomy and Physiology - Quiz 1', '2026-02-08', '21:00:00', '21:30:00', 30, 10, 5, 'Quiz on skeletal system.', 1, 'approved', '2026-02-12 09:00:00', 1, '2026-02-13 10:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:28:52'),
(8, 3, 1, 'Medical-Surgical Nursing I - Midterm', '2026-03-20', '09:00:00', '10:30:00', 90, 20, 10, 'Midterm examination covering medical-surgical nursing concepts.', 1, 'approved', '2026-02-08 14:00:00', 1, '2026-02-09 09:40:44', '', 0, 1, '2026-02-08 18:02:24', '2026-02-09 08:40:44'),
(9, 4, 1, 'Introduction to Midwifery - Midterm', '2026-03-17', '10:00:00', '11:30:00', 90, 15, 8, 'Answer all questions to the best of your ability.', 1, 'approved', '2026-02-03 09:00:00', 2, '2026-02-04 10:00:00', NULL, 0, 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(10, 4, 2, 'Introduction to Midwifery - Final Exam', '2026-05-22', '10:00:00', '12:00:00', 120, 25, 13, 'Comprehensive final exam.', 1, 'approved', '2026-04-02 09:00:00', 2, '2026-04-03 10:00:00', NULL, 0, 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(11, 5, 1, 'Reproductive Health - Midterm', NULL, NULL, NULL, 90, 0, 0, 'Draft exam - questions being added.', 0, 'draft', NULL, NULL, NULL, NULL, 0, 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(12, 6, 1, 'Introduction to Public Health - Midterm', '2026-03-18', '14:00:00', '15:30:00', 90, 15, 8, 'Read instructions carefully before starting.', 1, 'approved', '2026-02-03 10:00:00', 3, '2026-02-04 11:00:00', NULL, 0, 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(13, 6, 2, 'Introduction to Public Health - Final Exam', '2026-05-23', '14:00:00', '16:00:00', 120, 25, 13, 'Final exam covering all public health topics.', 1, 'approved', '2026-04-03 10:00:00', 3, '2026-04-04 11:00:00', NULL, 0, 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(14, 6, 3, 'Introduction to Public Health - Quiz 1', '2026-03-01', '14:00:00', '14:30:00', 30, 10, 5, 'Quiz on epidemiology basics.', 1, 'approved', '2026-02-14 09:00:00', 3, '2026-02-15 10:00:00', NULL, 0, 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(15, 8, 1, 'Fundamentals of Anesthesia - Midterm', '2026-03-19', '11:00:00', '12:30:00', 90, 15, 8, 'Midterm exam on anesthesia fundamentals.', 1, 'approved', '2026-02-04 10:00:00', 4, '2026-02-05 11:00:00', NULL, 0, 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(16, 8, 3, 'Fundamentals of Anesthesia - Quiz 1', '2026-02-26', '11:00:00', '11:30:00', 30, 10, 5, 'Quiz covering basic anesthesia principles.', 1, 'approved', '2026-02-10 10:00:00', 4, '2026-02-11 11:00:00', NULL, 0, 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(17, 9, 1, 'Pharmacology for Anesthesia - Midterm', NULL, NULL, NULL, 90, 15, 8, 'Midterm exam on anesthetic pharmacology.', 0, 'rejected', '2026-02-05 10:00:00', 4, '2026-02-06 09:00:00', NULL, 0, 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(18, 10, 1, 'Clinical Chemistry - Midterm', '2026-03-21', '13:00:00', '14:30:00', 90, 20, 10, 'Laboratory chemistry midterm exam.', 1, 'approved', '2026-02-08 15:00:00', 1, '2026-02-09 09:36:43', 'good', 0, 6, '2026-02-08 18:02:24', '2026-02-09 08:36:43'),
(19, 11, 1, 'Hematology - Midterm', NULL, NULL, NULL, 90, 0, 0, 'Draft exam - under construction.', 0, 'draft', NULL, NULL, NULL, NULL, 0, 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(20, 1, 4, 'Fundamentals of Nursing - Makeup Exam', '2026-06-10', '09:00:00', '11:00:00', 120, 30, 15, 'Makeup exam for students who missed the final.', 1, 'approved', '2026-05-25 10:00:00', 1, '2026-05-26 14:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(21, 2, 4, 'Anatomy and Physiology - Makeup Exam', '2026-06-11', '09:00:00', '11:00:00', 120, 30, 15, 'Makeup exam for absent students.', 1, 'approved', '2026-05-25 11:00:00', 1, '2026-05-26 15:00:00', NULL, 0, 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(24, 3, 3, 'Medical-Surgical Nursing I - Quiz 1', NULL, NULL, NULL, 68, 6, 3, 'h', 1, 'approved', '2026-02-09 12:00:08', 1, '2026-02-09 10:02:35', '', 0, 1, '2026-02-09 08:58:50', '2026-02-09 09:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `exam_approval_history`
--

CREATE TABLE `exam_approval_history` (
  `history_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `action` enum('submitted','approved','revision_requested','rejected','resubmitted') NOT NULL,
  `performed_by` int(11) NOT NULL,
  `performed_by_type` enum('instructor','department_head') NOT NULL,
  `comments` text DEFAULT NULL,
  `previous_status` enum('draft','pending','approved','revision','rejected') DEFAULT NULL,
  `new_status` enum('draft','pending','approved','revision','rejected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_approval_history`
--

INSERT INTO `exam_approval_history` (`history_id`, `exam_id`, `action`, `performed_by`, `performed_by_type`, `comments`, `previous_status`, `new_status`, `created_at`) VALUES
(4, 18, 'approved', 1, 'department_head', 'good', 'pending', 'approved', '2026-02-09 06:36:43'),
(5, 8, 'approved', 1, 'department_head', '', 'pending', 'approved', '2026-02-09 06:40:44'),
(6, 24, 'submitted', 1, 'instructor', '', 'draft', 'pending', '2026-02-09 09:00:08'),
(7, 24, 'approved', 1, 'department_head', '', 'pending', 'approved', '2026-02-09 07:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `exam_categories`
--

CREATE TABLE `exam_categories` (
  `exam_category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_categories`
--

INSERT INTO `exam_categories` (`exam_category_id`, `category_name`, `description`, `is_active`, `created_at`) VALUES
(1, 'Midterm', 'Mid-semester examination', 1, '2026-02-08 18:02:22'),
(2, 'Final', 'End of semester examination', 1, '2026-02-08 18:02:22'),
(3, 'Quiz', 'Short assessment', 1, '2026-02-08 18:02:22'),
(4, 'Makeup', 'Makeup examination', 1, '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `exam_question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `question_order` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`exam_question_id`, `exam_id`, `question_id`, `question_order`, `created_at`) VALUES
(1, 1, 1, 1, '2026-02-08 18:02:24'),
(2, 1, 2, 2, '2026-02-08 18:02:24'),
(3, 1, 3, 3, '2026-02-08 18:02:24'),
(4, 1, 4, 4, '2026-02-08 18:02:24'),
(5, 1, 5, 5, '2026-02-08 18:02:24'),
(6, 1, 6, 6, '2026-02-08 18:02:24'),
(7, 1, 31, 7, '2026-02-08 18:02:24'),
(8, 1, 32, 8, '2026-02-08 18:02:24'),
(9, 1, 33, 9, '2026-02-08 18:02:24'),
(10, 1, 34, 10, '2026-02-08 18:02:24'),
(11, 1, 35, 11, '2026-02-08 18:02:24'),
(12, 1, 36, 12, '2026-02-08 18:02:24'),
(13, 1, 37, 13, '2026-02-08 18:02:24'),
(14, 1, 38, 14, '2026-02-08 18:02:24'),
(15, 1, 39, 15, '2026-02-08 18:02:24'),
(16, 1, 40, 16, '2026-02-08 18:02:24'),
(17, 1, 41, 17, '2026-02-08 18:02:24'),
(18, 1, 42, 18, '2026-02-08 18:02:24'),
(19, 1, 43, 19, '2026-02-08 18:02:24'),
(20, 1, 44, 20, '2026-02-08 18:02:24'),
(21, 2, 1, 1, '2026-02-08 18:02:24'),
(22, 2, 2, 2, '2026-02-08 18:02:24'),
(23, 2, 3, 3, '2026-02-08 18:02:24'),
(24, 2, 4, 4, '2026-02-08 18:02:24'),
(25, 2, 5, 5, '2026-02-08 18:02:24'),
(26, 2, 6, 6, '2026-02-08 18:02:24'),
(27, 2, 31, 7, '2026-02-08 18:02:24'),
(28, 2, 32, 8, '2026-02-08 18:02:24'),
(29, 2, 33, 9, '2026-02-08 18:02:24'),
(30, 2, 34, 10, '2026-02-08 18:02:24'),
(31, 2, 35, 11, '2026-02-08 18:02:24'),
(32, 2, 36, 12, '2026-02-08 18:02:24'),
(33, 2, 37, 13, '2026-02-08 18:02:24'),
(34, 2, 38, 14, '2026-02-08 18:02:24'),
(35, 2, 39, 15, '2026-02-08 18:02:24'),
(36, 2, 40, 16, '2026-02-08 18:02:24'),
(37, 2, 41, 17, '2026-02-08 18:02:24'),
(38, 2, 42, 18, '2026-02-08 18:02:24'),
(39, 2, 43, 19, '2026-02-08 18:02:24'),
(40, 2, 44, 20, '2026-02-08 18:02:24'),
(41, 2, 1, 21, '2026-02-08 18:02:24'),
(42, 2, 2, 22, '2026-02-08 18:02:24'),
(43, 2, 3, 23, '2026-02-08 18:02:24'),
(44, 2, 4, 24, '2026-02-08 18:02:24'),
(45, 2, 5, 25, '2026-02-08 18:02:24'),
(46, 2, 6, 26, '2026-02-08 18:02:24'),
(47, 2, 31, 27, '2026-02-08 18:02:24'),
(48, 2, 32, 28, '2026-02-08 18:02:24'),
(49, 2, 33, 29, '2026-02-08 18:02:24'),
(50, 2, 34, 30, '2026-02-08 18:02:24'),
(51, 3, 1, 1, '2026-02-08 18:02:25'),
(52, 3, 2, 2, '2026-02-08 18:02:25'),
(53, 3, 3, 3, '2026-02-08 18:02:25'),
(54, 3, 4, 4, '2026-02-08 18:02:25'),
(55, 3, 5, 5, '2026-02-08 18:02:25'),
(56, 3, 6, 6, '2026-02-08 18:02:25'),
(57, 3, 31, 7, '2026-02-08 18:02:25'),
(58, 3, 32, 8, '2026-02-08 18:02:25'),
(59, 3, 33, 9, '2026-02-08 18:02:25'),
(60, 3, 34, 10, '2026-02-08 18:02:25'),
(61, 4, 35, 1, '2026-02-08 18:02:25'),
(62, 4, 36, 2, '2026-02-08 18:02:25'),
(63, 4, 37, 3, '2026-02-08 18:02:25'),
(64, 4, 38, 4, '2026-02-08 18:02:25'),
(65, 4, 39, 5, '2026-02-08 18:02:25'),
(66, 4, 40, 6, '2026-02-08 18:02:25'),
(67, 4, 41, 7, '2026-02-08 18:02:25'),
(68, 4, 42, 8, '2026-02-08 18:02:25'),
(69, 4, 43, 9, '2026-02-08 18:02:25'),
(70, 4, 44, 10, '2026-02-08 18:02:25'),
(71, 5, 7, 1, '2026-02-08 18:02:25'),
(72, 5, 8, 2, '2026-02-08 18:02:25'),
(73, 5, 9, 3, '2026-02-08 18:02:25'),
(74, 5, 10, 4, '2026-02-08 18:02:25'),
(75, 5, 11, 5, '2026-02-08 18:02:25'),
(76, 5, 12, 6, '2026-02-08 18:02:25'),
(77, 5, 45, 7, '2026-02-08 18:02:25'),
(78, 5, 46, 8, '2026-02-08 18:02:25'),
(79, 5, 47, 9, '2026-02-08 18:02:25'),
(80, 5, 48, 10, '2026-02-08 18:02:25'),
(81, 5, 49, 11, '2026-02-08 18:02:25'),
(82, 5, 50, 12, '2026-02-08 18:02:25'),
(83, 5, 51, 13, '2026-02-08 18:02:25'),
(84, 5, 52, 14, '2026-02-08 18:02:25'),
(85, 5, 53, 15, '2026-02-08 18:02:25'),
(86, 5, 54, 16, '2026-02-08 18:02:25'),
(87, 5, 55, 17, '2026-02-08 18:02:25'),
(88, 5, 56, 18, '2026-02-08 18:02:25'),
(89, 5, 57, 19, '2026-02-08 18:02:25'),
(90, 5, 58, 20, '2026-02-08 18:02:25'),
(91, 6, 7, 1, '2026-02-08 18:02:25'),
(92, 6, 8, 2, '2026-02-08 18:02:25'),
(93, 6, 9, 3, '2026-02-08 18:02:25'),
(94, 6, 10, 4, '2026-02-08 18:02:25'),
(95, 6, 11, 5, '2026-02-08 18:02:25'),
(96, 6, 12, 6, '2026-02-08 18:02:25'),
(97, 6, 45, 7, '2026-02-08 18:02:25'),
(98, 6, 46, 8, '2026-02-08 18:02:25'),
(99, 6, 47, 9, '2026-02-08 18:02:25'),
(100, 6, 48, 10, '2026-02-08 18:02:25'),
(101, 6, 49, 11, '2026-02-08 18:02:25'),
(102, 6, 50, 12, '2026-02-08 18:02:25'),
(103, 6, 51, 13, '2026-02-08 18:02:25'),
(104, 6, 52, 14, '2026-02-08 18:02:25'),
(105, 6, 53, 15, '2026-02-08 18:02:25'),
(106, 6, 54, 16, '2026-02-08 18:02:25'),
(107, 6, 55, 17, '2026-02-08 18:02:25'),
(108, 6, 56, 18, '2026-02-08 18:02:25'),
(109, 6, 57, 19, '2026-02-08 18:02:25'),
(110, 6, 58, 20, '2026-02-08 18:02:25'),
(111, 6, 7, 21, '2026-02-08 18:02:25'),
(112, 6, 8, 22, '2026-02-08 18:02:25'),
(113, 6, 9, 23, '2026-02-08 18:02:25'),
(114, 6, 10, 24, '2026-02-08 18:02:25'),
(115, 6, 11, 25, '2026-02-08 18:02:25'),
(116, 6, 12, 26, '2026-02-08 18:02:25'),
(117, 6, 45, 27, '2026-02-08 18:02:25'),
(118, 6, 46, 28, '2026-02-08 18:02:25'),
(119, 6, 47, 29, '2026-02-08 18:02:25'),
(120, 6, 48, 30, '2026-02-08 18:02:25'),
(121, 7, 7, 1, '2026-02-08 18:02:25'),
(122, 7, 8, 2, '2026-02-08 18:02:25'),
(123, 7, 9, 3, '2026-02-08 18:02:25'),
(124, 7, 10, 4, '2026-02-08 18:02:25'),
(125, 7, 11, 5, '2026-02-08 18:02:25'),
(126, 7, 12, 6, '2026-02-08 18:02:25'),
(127, 7, 45, 7, '2026-02-08 18:02:25'),
(128, 7, 46, 8, '2026-02-08 18:02:25'),
(129, 7, 47, 9, '2026-02-08 18:02:25'),
(130, 7, 48, 10, '2026-02-08 18:02:25'),
(131, 8, 59, 1, '2026-02-08 18:02:25'),
(132, 8, 60, 2, '2026-02-08 18:02:25'),
(133, 8, 61, 3, '2026-02-08 18:02:25'),
(134, 8, 62, 4, '2026-02-08 18:02:25'),
(135, 8, 63, 5, '2026-02-08 18:02:25'),
(136, 8, 64, 6, '2026-02-08 18:02:25'),
(137, 8, 65, 7, '2026-02-08 18:02:25'),
(138, 8, 66, 8, '2026-02-08 18:02:25'),
(139, 8, 67, 9, '2026-02-08 18:02:25'),
(140, 8, 68, 10, '2026-02-08 18:02:25'),
(141, 8, 69, 11, '2026-02-08 18:02:25'),
(142, 8, 70, 12, '2026-02-08 18:02:25'),
(143, 8, 59, 13, '2026-02-08 18:02:25'),
(144, 8, 60, 14, '2026-02-08 18:02:25'),
(145, 8, 61, 15, '2026-02-08 18:02:25'),
(146, 8, 62, 16, '2026-02-08 18:02:25'),
(147, 8, 63, 17, '2026-02-08 18:02:25'),
(148, 8, 64, 18, '2026-02-08 18:02:25'),
(149, 8, 65, 19, '2026-02-08 18:02:25'),
(150, 8, 66, 20, '2026-02-08 18:02:25'),
(151, 9, 13, 1, '2026-02-08 18:02:25'),
(152, 9, 14, 2, '2026-02-08 18:02:25'),
(153, 9, 15, 3, '2026-02-08 18:02:25'),
(154, 9, 16, 4, '2026-02-08 18:02:25'),
(155, 9, 17, 5, '2026-02-08 18:02:25'),
(156, 9, 71, 6, '2026-02-08 18:02:25'),
(157, 9, 72, 7, '2026-02-08 18:02:25'),
(158, 9, 73, 8, '2026-02-08 18:02:25'),
(159, 9, 74, 9, '2026-02-08 18:02:25'),
(160, 9, 75, 10, '2026-02-08 18:02:25'),
(161, 9, 76, 11, '2026-02-08 18:02:25'),
(162, 9, 77, 12, '2026-02-08 18:02:25'),
(163, 9, 78, 13, '2026-02-08 18:02:25'),
(164, 9, 79, 14, '2026-02-08 18:02:25'),
(165, 9, 80, 15, '2026-02-08 18:02:25'),
(166, 10, 13, 1, '2026-02-08 18:02:25'),
(167, 10, 14, 2, '2026-02-08 18:02:25'),
(168, 10, 15, 3, '2026-02-08 18:02:25'),
(169, 10, 16, 4, '2026-02-08 18:02:25'),
(170, 10, 17, 5, '2026-02-08 18:02:25'),
(171, 10, 71, 6, '2026-02-08 18:02:25'),
(172, 10, 72, 7, '2026-02-08 18:02:25'),
(173, 10, 73, 8, '2026-02-08 18:02:25'),
(174, 10, 74, 9, '2026-02-08 18:02:25'),
(175, 10, 75, 10, '2026-02-08 18:02:25'),
(176, 10, 76, 11, '2026-02-08 18:02:25'),
(177, 10, 77, 12, '2026-02-08 18:02:25'),
(178, 10, 78, 13, '2026-02-08 18:02:25'),
(179, 10, 79, 14, '2026-02-08 18:02:25'),
(180, 10, 80, 15, '2026-02-08 18:02:25'),
(181, 10, 13, 16, '2026-02-08 18:02:25'),
(182, 10, 14, 17, '2026-02-08 18:02:25'),
(183, 10, 15, 18, '2026-02-08 18:02:25'),
(184, 10, 16, 19, '2026-02-08 18:02:25'),
(185, 10, 17, 20, '2026-02-08 18:02:25'),
(186, 10, 71, 21, '2026-02-08 18:02:25'),
(187, 10, 72, 22, '2026-02-08 18:02:25'),
(188, 10, 73, 23, '2026-02-08 18:02:25'),
(189, 10, 74, 24, '2026-02-08 18:02:25'),
(190, 10, 75, 25, '2026-02-08 18:02:25'),
(191, 12, 18, 1, '2026-02-08 18:02:25'),
(192, 12, 19, 2, '2026-02-08 18:02:25'),
(193, 12, 20, 3, '2026-02-08 18:02:25'),
(194, 12, 21, 4, '2026-02-08 18:02:25'),
(195, 12, 22, 5, '2026-02-08 18:02:25'),
(196, 12, 81, 6, '2026-02-08 18:02:25'),
(197, 12, 82, 7, '2026-02-08 18:02:25'),
(198, 12, 83, 8, '2026-02-08 18:02:25'),
(199, 12, 84, 9, '2026-02-08 18:02:25'),
(200, 12, 85, 10, '2026-02-08 18:02:25'),
(201, 12, 86, 11, '2026-02-08 18:02:25'),
(202, 12, 87, 12, '2026-02-08 18:02:25'),
(203, 12, 88, 13, '2026-02-08 18:02:25'),
(204, 12, 89, 14, '2026-02-08 18:02:25'),
(205, 12, 90, 15, '2026-02-08 18:02:25'),
(206, 13, 18, 1, '2026-02-08 18:02:25'),
(207, 13, 19, 2, '2026-02-08 18:02:25'),
(208, 13, 20, 3, '2026-02-08 18:02:25'),
(209, 13, 21, 4, '2026-02-08 18:02:25'),
(210, 13, 22, 5, '2026-02-08 18:02:25'),
(211, 13, 81, 6, '2026-02-08 18:02:25'),
(212, 13, 82, 7, '2026-02-08 18:02:25'),
(213, 13, 83, 8, '2026-02-08 18:02:25'),
(214, 13, 84, 9, '2026-02-08 18:02:25'),
(215, 13, 85, 10, '2026-02-08 18:02:25'),
(216, 13, 86, 11, '2026-02-08 18:02:25'),
(217, 13, 87, 12, '2026-02-08 18:02:25'),
(218, 13, 88, 13, '2026-02-08 18:02:25'),
(219, 13, 89, 14, '2026-02-08 18:02:25'),
(220, 13, 90, 15, '2026-02-08 18:02:25'),
(221, 13, 18, 16, '2026-02-08 18:02:25'),
(222, 13, 19, 17, '2026-02-08 18:02:25'),
(223, 13, 20, 18, '2026-02-08 18:02:25'),
(224, 13, 21, 19, '2026-02-08 18:02:25'),
(225, 13, 22, 20, '2026-02-08 18:02:25'),
(226, 13, 81, 21, '2026-02-08 18:02:25'),
(227, 13, 82, 22, '2026-02-08 18:02:25'),
(228, 13, 83, 23, '2026-02-08 18:02:25'),
(229, 13, 84, 24, '2026-02-08 18:02:25'),
(230, 13, 85, 25, '2026-02-08 18:02:25'),
(231, 14, 18, 1, '2026-02-08 18:02:25'),
(232, 14, 19, 2, '2026-02-08 18:02:25'),
(233, 14, 20, 3, '2026-02-08 18:02:25'),
(234, 14, 21, 4, '2026-02-08 18:02:25'),
(235, 14, 22, 5, '2026-02-08 18:02:25'),
(236, 14, 81, 6, '2026-02-08 18:02:25'),
(237, 14, 82, 7, '2026-02-08 18:02:25'),
(238, 14, 83, 8, '2026-02-08 18:02:25'),
(239, 14, 84, 9, '2026-02-08 18:02:25'),
(240, 14, 85, 10, '2026-02-08 18:02:25'),
(241, 15, 23, 1, '2026-02-08 18:02:25'),
(242, 15, 24, 2, '2026-02-08 18:02:25'),
(243, 15, 25, 3, '2026-02-08 18:02:25'),
(244, 15, 26, 4, '2026-02-08 18:02:25'),
(245, 15, 27, 5, '2026-02-08 18:02:25'),
(246, 15, 91, 6, '2026-02-08 18:02:25'),
(247, 15, 92, 7, '2026-02-08 18:02:25'),
(248, 15, 93, 8, '2026-02-08 18:02:25'),
(249, 15, 94, 9, '2026-02-08 18:02:25'),
(250, 15, 95, 10, '2026-02-08 18:02:25'),
(251, 15, 96, 11, '2026-02-08 18:02:25'),
(252, 15, 97, 12, '2026-02-08 18:02:25'),
(253, 15, 98, 13, '2026-02-08 18:02:25'),
(254, 15, 99, 14, '2026-02-08 18:02:25'),
(255, 15, 100, 15, '2026-02-08 18:02:25'),
(256, 16, 23, 1, '2026-02-08 18:02:25'),
(257, 16, 24, 2, '2026-02-08 18:02:25'),
(258, 16, 25, 3, '2026-02-08 18:02:25'),
(259, 16, 26, 4, '2026-02-08 18:02:25'),
(260, 16, 27, 5, '2026-02-08 18:02:25'),
(261, 16, 91, 6, '2026-02-08 18:02:25'),
(262, 16, 92, 7, '2026-02-08 18:02:25'),
(263, 16, 93, 8, '2026-02-08 18:02:25'),
(264, 16, 94, 9, '2026-02-08 18:02:25'),
(265, 16, 95, 10, '2026-02-08 18:02:25'),
(266, 17, 91, 1, '2026-02-08 18:02:25'),
(267, 17, 92, 2, '2026-02-08 18:02:25'),
(268, 17, 93, 3, '2026-02-08 18:02:25'),
(269, 17, 94, 4, '2026-02-08 18:02:25'),
(270, 17, 95, 5, '2026-02-08 18:02:25'),
(271, 17, 96, 6, '2026-02-08 18:02:25'),
(272, 17, 97, 7, '2026-02-08 18:02:25'),
(273, 17, 98, 8, '2026-02-08 18:02:25'),
(274, 17, 99, 9, '2026-02-08 18:02:25'),
(275, 17, 100, 10, '2026-02-08 18:02:25'),
(276, 17, 91, 11, '2026-02-08 18:02:25'),
(277, 17, 92, 12, '2026-02-08 18:02:25'),
(278, 17, 93, 13, '2026-02-08 18:02:25'),
(279, 17, 94, 14, '2026-02-08 18:02:25'),
(280, 17, 95, 15, '2026-02-08 18:02:25'),
(281, 18, 28, 1, '2026-02-08 18:02:26'),
(282, 18, 29, 2, '2026-02-08 18:02:26'),
(283, 18, 30, 3, '2026-02-08 18:02:26'),
(284, 18, 101, 4, '2026-02-08 18:02:26'),
(285, 18, 102, 5, '2026-02-08 18:02:26'),
(286, 18, 103, 6, '2026-02-08 18:02:26'),
(287, 18, 104, 7, '2026-02-08 18:02:26'),
(288, 18, 105, 8, '2026-02-08 18:02:26'),
(289, 18, 106, 9, '2026-02-08 18:02:26'),
(290, 18, 107, 10, '2026-02-08 18:02:26'),
(291, 18, 108, 11, '2026-02-08 18:02:26'),
(292, 18, 109, 12, '2026-02-08 18:02:26'),
(293, 18, 110, 13, '2026-02-08 18:02:26'),
(294, 18, 28, 14, '2026-02-08 18:02:26'),
(295, 18, 29, 15, '2026-02-08 18:02:26'),
(296, 18, 30, 16, '2026-02-08 18:02:26'),
(297, 18, 101, 17, '2026-02-08 18:02:26'),
(298, 18, 102, 18, '2026-02-08 18:02:26'),
(299, 18, 103, 19, '2026-02-08 18:02:26'),
(300, 18, 104, 20, '2026-02-08 18:02:26'),
(301, 20, 1, 1, '2026-02-08 18:02:26'),
(302, 20, 2, 2, '2026-02-08 18:02:26'),
(303, 20, 3, 3, '2026-02-08 18:02:26'),
(304, 20, 4, 4, '2026-02-08 18:02:26'),
(305, 20, 5, 5, '2026-02-08 18:02:26'),
(306, 20, 6, 6, '2026-02-08 18:02:26'),
(307, 20, 31, 7, '2026-02-08 18:02:26'),
(308, 20, 32, 8, '2026-02-08 18:02:26'),
(309, 20, 33, 9, '2026-02-08 18:02:26'),
(310, 20, 34, 10, '2026-02-08 18:02:26'),
(311, 20, 35, 11, '2026-02-08 18:02:26'),
(312, 20, 36, 12, '2026-02-08 18:02:26'),
(313, 20, 37, 13, '2026-02-08 18:02:26'),
(314, 20, 38, 14, '2026-02-08 18:02:26'),
(315, 20, 39, 15, '2026-02-08 18:02:26'),
(316, 20, 40, 16, '2026-02-08 18:02:26'),
(317, 20, 41, 17, '2026-02-08 18:02:26'),
(318, 20, 42, 18, '2026-02-08 18:02:26'),
(319, 20, 43, 19, '2026-02-08 18:02:26'),
(320, 20, 44, 20, '2026-02-08 18:02:26'),
(321, 20, 1, 21, '2026-02-08 18:02:26'),
(322, 20, 2, 22, '2026-02-08 18:02:26'),
(323, 20, 3, 23, '2026-02-08 18:02:26'),
(324, 20, 4, 24, '2026-02-08 18:02:26'),
(325, 20, 5, 25, '2026-02-08 18:02:26'),
(326, 20, 6, 26, '2026-02-08 18:02:26'),
(327, 20, 31, 27, '2026-02-08 18:02:26'),
(328, 20, 32, 28, '2026-02-08 18:02:26'),
(329, 20, 33, 29, '2026-02-08 18:02:26'),
(330, 20, 34, 30, '2026-02-08 18:02:26'),
(331, 21, 7, 1, '2026-02-08 18:02:26'),
(332, 21, 8, 2, '2026-02-08 18:02:26'),
(333, 21, 9, 3, '2026-02-08 18:02:26'),
(334, 21, 10, 4, '2026-02-08 18:02:26'),
(335, 21, 11, 5, '2026-02-08 18:02:26'),
(336, 21, 12, 6, '2026-02-08 18:02:26'),
(337, 21, 45, 7, '2026-02-08 18:02:26'),
(338, 21, 46, 8, '2026-02-08 18:02:26'),
(339, 21, 47, 9, '2026-02-08 18:02:26'),
(340, 21, 48, 10, '2026-02-08 18:02:26'),
(341, 21, 49, 11, '2026-02-08 18:02:26'),
(342, 21, 50, 12, '2026-02-08 18:02:26'),
(343, 21, 51, 13, '2026-02-08 18:02:26'),
(344, 21, 52, 14, '2026-02-08 18:02:26'),
(345, 21, 53, 15, '2026-02-08 18:02:26'),
(346, 21, 54, 16, '2026-02-08 18:02:26'),
(347, 21, 55, 17, '2026-02-08 18:02:26'),
(348, 21, 56, 18, '2026-02-08 18:02:26'),
(349, 21, 57, 19, '2026-02-08 18:02:26'),
(350, 21, 58, 20, '2026-02-08 18:02:26'),
(351, 21, 7, 21, '2026-02-08 18:02:26'),
(352, 21, 8, 22, '2026-02-08 18:02:26'),
(353, 21, 9, 23, '2026-02-08 18:02:26'),
(354, 21, 10, 24, '2026-02-08 18:02:26'),
(355, 21, 11, 25, '2026-02-08 18:02:26'),
(356, 21, 12, 26, '2026-02-08 18:02:26'),
(357, 21, 45, 27, '2026-02-08 18:02:26'),
(358, 21, 46, 28, '2026-02-08 18:02:26'),
(359, 21, 47, 29, '2026-02-08 18:02:26'),
(360, 21, 48, 30, '2026-02-08 18:02:26'),
(361, 9, 59, 1, '2026-02-08 18:02:26'),
(362, 9, 60, 2, '2026-02-08 18:02:26'),
(363, 9, 61, 3, '2026-02-08 18:02:26'),
(364, 9, 62, 4, '2026-02-08 18:02:26'),
(365, 9, 63, 5, '2026-02-08 18:02:26'),
(366, 9, 64, 6, '2026-02-08 18:02:26'),
(367, 9, 65, 7, '2026-02-08 18:02:26'),
(368, 9, 66, 8, '2026-02-08 18:02:26'),
(369, 9, 67, 9, '2026-02-08 18:02:26'),
(370, 9, 68, 10, '2026-02-08 18:02:26'),
(371, 9, 69, 11, '2026-02-08 18:02:26'),
(372, 9, 70, 12, '2026-02-08 18:02:26'),
(373, 9, 59, 13, '2026-02-08 18:02:26'),
(374, 9, 60, 14, '2026-02-08 18:02:26'),
(375, 9, 61, 15, '2026-02-08 18:02:26'),
(376, 9, 62, 16, '2026-02-08 18:02:26'),
(377, 9, 63, 17, '2026-02-08 18:02:26'),
(378, 9, 64, 18, '2026-02-08 18:02:26'),
(379, 9, 65, 19, '2026-02-08 18:02:26'),
(380, 9, 66, 20, '2026-02-08 18:02:26'),
(381, 10, 28, 1, '2026-02-08 18:02:26'),
(382, 10, 29, 2, '2026-02-08 18:02:26'),
(383, 10, 30, 3, '2026-02-08 18:02:26'),
(384, 10, 101, 4, '2026-02-08 18:02:26'),
(385, 10, 102, 5, '2026-02-08 18:02:26'),
(386, 10, 103, 6, '2026-02-08 18:02:26'),
(387, 10, 104, 7, '2026-02-08 18:02:26'),
(388, 10, 105, 8, '2026-02-08 18:02:26'),
(389, 10, 106, 9, '2026-02-08 18:02:26'),
(390, 10, 107, 10, '2026-02-08 18:02:26'),
(391, 10, 108, 11, '2026-02-08 18:02:26'),
(392, 10, 109, 12, '2026-02-08 18:02:26'),
(393, 10, 110, 13, '2026-02-08 18:02:26'),
(394, 10, 28, 14, '2026-02-08 18:02:26'),
(395, 10, 29, 15, '2026-02-08 18:02:26'),
(396, 10, 30, 16, '2026-02-08 18:02:26'),
(397, 10, 101, 17, '2026-02-08 18:02:26'),
(398, 10, 102, 18, '2026-02-08 18:02:26'),
(399, 10, 103, 19, '2026-02-08 18:02:26'),
(400, 10, 104, 20, '2026-02-08 18:02:26'),
(401, 24, 53, 1, '2026-02-09 08:58:58'),
(402, 24, 54, 2, '2026-02-09 08:59:03'),
(403, 24, 60, 3, '2026-02-09 08:59:08'),
(404, 24, 55, 4, '2026-02-09 08:59:23'),
(405, 24, 58, 5, '2026-02-09 08:59:28'),
(406, 24, 59, 6, '2026-02-09 08:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `result_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `correct_answers` int(11) DEFAULT 0,
  `wrong_answers` int(11) DEFAULT 0,
  `unanswered` int(11) DEFAULT 0,
  `total_points_earned` decimal(10,2) DEFAULT 0.00,
  `total_points_possible` decimal(10,2) NOT NULL,
  `percentage_score` decimal(5,2) DEFAULT 0.00,
  `letter_grade` varchar(5) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `pass_status` enum('Pass','Fail') DEFAULT 'Fail',
  `exam_started_at` timestamp NULL DEFAULT NULL,
  `exam_submitted_at` timestamp NULL DEFAULT NULL,
  `time_taken_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`result_id`, `student_id`, `exam_id`, `total_questions`, `correct_answers`, `wrong_answers`, `unanswered`, `total_points_earned`, `total_points_possible`, `percentage_score`, `letter_grade`, `gpa`, `pass_status`, `exam_started_at`, `exam_submitted_at`, `time_taken_minutes`, `created_at`) VALUES
(2, 1, 1, 20, 0, 20, 0, 0.00, 200.00, 0.00, 'F', 0.00, 'Fail', '2026-02-08 18:10:33', '2026-02-08 18:10:33', 90, '2026-02-08 18:10:33'),
(3, 1, 3, 10, 1, 9, 0, 10.00, 100.00, 10.00, 'F', 0.00, 'Fail', '2026-02-08 18:16:15', '2026-02-08 18:16:15', 30, '2026-02-08 18:16:15'),
(4, 1, 7, 10, 2, 8, 0, 20.00, 100.00, 20.00, NULL, NULL, 'Fail', '2026-02-08 18:30:30', '2026-02-08 18:30:30', 30, '2026-02-08 18:30:30');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `faculty_code` varchar(20) NOT NULL,
  `faculty_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'FHS', 'Faculty of Health Sciences', 'Health and Medical Sciences Programs', 1, '2026-02-08 18:02:21', '2026-02-08 18:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `grading_config`
--

CREATE TABLE `grading_config` (
  `config_id` int(11) NOT NULL,
  `grade_letter` varchar(5) NOT NULL,
  `min_percentage` decimal(5,2) NOT NULL,
  `max_percentage` decimal(5,2) NOT NULL,
  `gpa_value` decimal(3,2) NOT NULL,
  `status_label` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grading_config`
--

INSERT INTO `grading_config` (`config_id`, `grade_letter`, `min_percentage`, `max_percentage`, `gpa_value`, `status_label`, `display_order`, `is_active`, `updated_at`) VALUES
(1, 'A+', 90.00, 100.00, 4.00, 'Excellent', 1, 1, '2026-02-08 18:02:23'),
(2, 'A', 85.00, 89.99, 3.75, 'Excellent', 2, 1, '2026-02-08 18:02:23'),
(3, 'A-', 80.00, 84.99, 3.50, 'Excellent', 3, 1, '2026-02-08 18:02:23'),
(4, 'B+', 75.00, 79.99, 3.00, 'Good', 4, 1, '2026-02-08 18:02:23'),
(5, 'B', 70.00, 74.99, 2.75, 'Good', 5, 1, '2026-02-08 18:02:23'),
(6, 'B-', 65.00, 69.99, 2.50, 'Good', 6, 1, '2026-02-08 18:02:23'),
(7, 'C+', 60.00, 64.99, 2.00, 'Satisfactory', 7, 1, '2026-02-08 18:02:23'),
(8, 'C', 55.00, 59.99, 1.75, 'Satisfactory', 8, 1, '2026-02-08 18:02:23'),
(9, 'C-', 50.00, 54.99, 1.50, 'Satisfactory', 9, 1, '2026-02-08 18:02:23'),
(10, 'D', 45.00, 49.99, 1.00, 'Pass', 10, 1, '2026-02-08 18:02:23'),
(11, 'F', 0.00, 44.99, 0.00, 'Fail', 11, 1, '2026-02-08 18:02:23');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `instructor_id` int(11) NOT NULL,
  `instructor_code` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`instructor_id`, `instructor_code`, `username`, `password`, `full_name`, `email`, `phone`, `department_id`, `gender`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'INST001', 'abebe.t', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Abebe Tadesse', 'abebe.t@dmu.edu.et', '+251911234567', 1, 'Male', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(2, 'INST002', 'marta.g', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sr. Marta Gebre', 'marta.g@dmu.edu.et', '+251911234568', 1, 'Female', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(3, 'INST003', 'sara.m', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Sara Mulugeta', 'sara.m@dmu.edu.et', '+251911234569', 2, 'Female', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(4, 'INST004', 'daniel.h', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Daniel Hailu', 'daniel.h@dmu.edu.et', '+251911234570', 3, 'Male', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(5, 'INST005', 'helen.t', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Helen Tesfaye', 'helen.t@dmu.edu.et', '+251911234571', 4, 'Female', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(6, 'INST006', 'yohannes.b', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Yohannes Bekele', 'yohannes.b@dmu.edu.et', '+251911234572', 5, 'Male', 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_courses`
--

CREATE TABLE `instructor_courses` (
  `assignment_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instructor_courses`
--

INSERT INTO `instructor_courses` (`assignment_id`, `instructor_id`, `course_id`, `assigned_at`) VALUES
(83, 1, 1, '2026-02-08 18:02:22'),
(84, 1, 3, '2026-02-08 18:02:22'),
(85, 2, 2, '2026-02-08 18:02:22'),
(86, 3, 4, '2026-02-08 18:02:22'),
(87, 3, 5, '2026-02-08 18:02:22'),
(88, 4, 6, '2026-02-08 18:02:22'),
(89, 4, 7, '2026-02-08 18:02:22'),
(90, 5, 8, '2026-02-08 18:02:22'),
(91, 5, 9, '2026-02-08 18:02:22'),
(92, 6, 10, '2026-02-08 18:02:22'),
(93, 6, 11, '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `practice_questions`
--

CREATE TABLE `practice_questions` (
  `practice_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `question_type` enum('multiple_choice','true_false') DEFAULT 'multiple_choice',
  `question_text` text NOT NULL,
  `option_a` varchar(500) DEFAULT NULL,
  `option_b` varchar(500) DEFAULT NULL,
  `option_c` varchar(500) DEFAULT NULL,
  `option_d` varchar(500) DEFAULT NULL,
  `correct_answer` enum('A','B','C','D','True','False') NOT NULL,
  `difficulty_level` enum('Easy','Medium','Hard') DEFAULT 'Medium',
  `explanation` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `practice_questions`
--

INSERT INTO `practice_questions` (`practice_id`, `course_id`, `topic_id`, `question_type`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `difficulty_level`, `explanation`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'multiple_choice', 'What is the first step in the nursing process?', 'Planning', 'Assessment', 'Implementation', 'Evaluation', 'B', 'Easy', 'Assessment is always the first step where nurses gather patient information.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(2, 1, 1, 'multiple_choice', 'Which vital sign is measured in beats per minute?', 'Temperature', 'Blood Pressure', 'Pulse', 'Respiratory Rate', 'C', 'Easy', 'Pulse is measured in beats per minute (bpm).', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(3, 1, 9, 'multiple_choice', 'How long should hands be scrubbed during surgical hand washing?', '10 seconds', '30 seconds', '2-6 minutes', '10 minutes', 'C', 'Medium', 'Surgical hand washing requires 2-6 minutes of thorough scrubbing.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(4, 1, 9, 'true_false', 'Gloves can replace hand washing in patient care.', 'True', 'False', NULL, NULL, 'False', 'Easy', 'Gloves are an additional barrier but do not replace proper hand hygiene.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(5, 1, 1, 'true_false', 'Nurses can diagnose medical conditions independently.', 'True', 'False', NULL, NULL, 'False', 'Medium', 'Nurses make nursing diagnoses, but medical diagnoses are made by physicians.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(6, 1, 9, 'true_false', 'Standard precautions should be used for all patients.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Standard precautions are infection control practices used for all patients.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(7, 2, 2, 'multiple_choice', 'Which bone protects the brain?', 'Femur', 'Skull', 'Ribs', 'Vertebrae', 'B', 'Easy', 'The skull (cranium) protects the brain from injury.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(8, 2, 3, 'multiple_choice', 'What is the largest artery in the human body?', 'Pulmonary artery', 'Carotid artery', 'Aorta', 'Femoral artery', 'C', 'Medium', 'The aorta is the largest artery, carrying oxygenated blood from the heart.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(9, 2, 2, 'multiple_choice', 'How many pairs of ribs does a human have?', '10', '12', '14', '16', 'B', 'Medium', 'Humans have 12 pairs of ribs (24 ribs total).', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(10, 2, 2, 'true_false', 'The femur is the longest bone in the human body.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'The femur (thigh bone) is the longest and strongest bone in the body.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(11, 2, 3, 'true_false', 'The heart has three chambers.', 'True', 'False', NULL, NULL, 'False', 'Easy', 'The heart has four chambers: two atria and two ventricles.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(12, 2, 3, 'true_false', 'Arteries carry blood away from the heart.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Arteries carry oxygenated blood away from the heart to body tissues.', 1, 1, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(13, 4, 5, 'multiple_choice', 'What is the average length of a menstrual cycle?', '21 days', '28 days', '35 days', '40 days', 'B', 'Easy', 'The average menstrual cycle is 28 days, though 21-35 days is considered normal.', 1, 3, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(14, 4, 5, 'multiple_choice', 'At what week does the second trimester begin?', 'Week 10', 'Week 13', 'Week 16', 'Week 20', 'B', 'Medium', 'The second trimester begins at week 13 and ends at week 27.', 1, 3, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(15, 4, 5, 'multiple_choice', 'What is the normal fetal heart rate range?', '60-100 bpm', '110-160 bpm', '180-200 bpm', '200-220 bpm', 'B', 'Medium', 'Normal fetal heart rate is 110-160 beats per minute.', 1, 3, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(16, 4, 5, 'true_false', 'Morning sickness only occurs in the morning.', 'True', 'False', NULL, NULL, 'False', 'Easy', 'Despite its name, morning sickness can occur at any time of day.', 1, 3, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(17, 4, 5, 'true_false', 'Pregnant women should avoid all exercise.', 'True', 'False', NULL, NULL, 'False', 'Medium', 'Moderate exercise is beneficial during pregnancy unless contraindicated.', 1, 3, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(18, 4, 5, 'true_false', 'The placenta provides oxygen and nutrients to the fetus.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'The placenta transfers oxygen and nutrients from mother to fetus.', 1, 3, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(19, 6, 6, 'multiple_choice', 'What does CDC stand for?', 'Center for Disease Control', 'Centers for Disease Control and Prevention', 'Central Disease Center', 'Clinical Disease Control', 'B', 'Easy', 'CDC stands for Centers for Disease Control and Prevention.', 1, 4, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(20, 6, 6, 'multiple_choice', 'Which disease was eradicated globally through vaccination?', 'Polio', 'Smallpox', 'Measles', 'Tuberculosis', 'B', 'Medium', 'Smallpox was declared eradicated in 1980 through global vaccination efforts.', 1, 4, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(21, 6, 6, 'multiple_choice', 'What is the primary mode of HIV transmission?', 'Mosquito bites', 'Sharing food', 'Blood and body fluids', 'Casual contact', 'C', 'Medium', 'HIV is transmitted through blood, sexual contact, and from mother to child.', 1, 4, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(22, 6, 6, 'true_false', 'Epidemiology is the study of disease patterns in populations.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Epidemiology studies the distribution and determinants of health conditions in populations.', 1, 4, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(23, 6, 6, 'true_false', 'Antibiotics are effective against all types of infections.', 'True', 'False', NULL, NULL, 'False', 'Medium', 'Antibiotics only work against bacterial infections, not viral or fungal infections.', 1, 4, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(24, 6, 6, 'true_false', 'Clean water is essential for preventing waterborne diseases.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Access to clean water prevents diseases like cholera, typhoid, and dysentery.', 1, 4, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(25, 8, 8, 'multiple_choice', 'What does ASA stand for in anesthesia?', 'American Society of Anesthesiologists', 'Anesthesia Safety Association', 'Advanced Surgical Anesthesia', 'Anesthetic Standard Assessment', 'A', 'Medium', 'ASA is the American Society of Anesthesiologists classification system.', 1, 5, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(26, 8, 8, 'multiple_choice', 'Which drug reverses opioid effects?', 'Atropine', 'Naloxone', 'Epinephrine', 'Dopamine', 'B', 'Hard', 'Naloxone (Narcan) is an opioid antagonist that reverses opioid effects.', 1, 5, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(27, 8, 9, 'multiple_choice', 'What is the normal oxygen saturation level?', '70-80%', '85-90%', '95-100%', '100-110%', 'C', 'Easy', 'Normal oxygen saturation (SpO2) is 95-100%.', 1, 5, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(28, 8, 8, 'true_false', 'Spinal anesthesia is a type of regional anesthesia.', 'True', 'False', NULL, NULL, 'True', 'Medium', 'Spinal anesthesia blocks sensation in a specific region of the body.', 1, 5, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(29, 8, 9, 'true_false', 'Patients should fast before general anesthesia.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Fasting reduces the risk of aspiration during anesthesia.', 1, 5, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(30, 8, 8, 'true_false', 'Local anesthesia causes loss of consciousness.', 'True', 'False', NULL, NULL, 'False', 'Easy', 'Local anesthesia only numbs a specific area without affecting consciousness.', 1, 5, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(31, 10, 7, 'multiple_choice', 'What is the normal range for blood glucose (fasting)?', '50-70 mg/dL', '70-100 mg/dL', '120-140 mg/dL', '150-180 mg/dL', 'B', 'Medium', 'Normal fasting blood glucose is 70-100 mg/dL.', 1, 6, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(32, 10, 7, 'multiple_choice', 'Which blood cell fights infection?', 'Red blood cells', 'White blood cells', 'Platelets', 'Plasma cells', 'B', 'Easy', 'White blood cells (leukocytes) are part of the immune system.', 1, 6, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(33, 10, 7, 'multiple_choice', 'What does CBC stand for?', 'Complete Blood Count', 'Central Blood Center', 'Clinical Blood Chemistry', 'Cellular Blood Composition', 'A', 'Easy', 'CBC is a Complete Blood Count test that measures blood components.', 1, 6, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(34, 10, 7, 'true_false', 'Hemoglobin carries oxygen in the blood.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Hemoglobin in red blood cells binds and transports oxygen.', 1, 6, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(35, 10, 7, 'true_false', 'Blood type AB is the universal recipient.', 'True', 'False', NULL, NULL, 'True', 'Medium', 'People with AB blood type can receive blood from any blood type.', 1, 6, '2026-02-08 18:06:19', '2026-02-08 18:06:19'),
(36, 10, 7, 'true_false', 'Platelets are responsible for blood clotting.', 'True', 'False', NULL, NULL, 'True', 'Easy', 'Platelets (thrombocytes) play a crucial role in blood clotting.', 1, 6, '2026-02-08 18:06:19', '2026-02-08 18:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false') NOT NULL DEFAULT 'multiple_choice',
  `option_a` varchar(500) NOT NULL,
  `option_b` varchar(500) NOT NULL,
  `option_c` varchar(500) DEFAULT NULL,
  `option_d` varchar(500) DEFAULT NULL,
  `correct_answer` enum('A','B','C','D','True','False') NOT NULL,
  `point_value` int(11) DEFAULT 1,
  `explanation` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `course_id`, `topic_id`, `question_text`, `question_type`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `point_value`, `explanation`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'What is the primary goal of nursing care?', 'multiple_choice', 'To cure all diseases', 'To promote health and prevent illness', 'To perform medical procedures', 'To manage hospital operations', 'B', 1, 'The primary goal of nursing is to promote health, prevent illness, and help patients cope with illness.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(2, 1, 1, 'Which of the following is a basic human need according to Maslow\'s hierarchy?', 'multiple_choice', 'Internet access', 'Physiological needs', 'Entertainment', 'Social media', 'B', 1, 'Maslow\'s hierarchy starts with physiological needs like food, water, and shelter.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(3, 1, 9, 'What is the correct order for hand hygiene?', 'multiple_choice', 'Dry, rinse, soap, wet', 'Wet, soap, rinse, dry', 'Soap, wet, dry, rinse', 'Rinse, dry, wet, soap', 'B', 1, 'Proper hand hygiene: wet hands, apply soap, scrub, rinse, and dry.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(4, 1, 9, 'Hand hygiene is the single most important practice to prevent healthcare-associated infections.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Hand hygiene is universally recognized as the most effective way to prevent the spread of infections in healthcare settings.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(5, 1, 9, 'Sterile gloves must be worn when taking a patient\'s blood pressure.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Taking blood pressure is a non-invasive procedure that requires clean technique, not sterile gloves.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(6, 1, 1, 'The nursing process consists of five steps: Assessment, Diagnosis, Planning, Implementation, and Evaluation.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'These five steps (ADPIE) form the foundation of the nursing process.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(7, 2, 2, 'Which organ is responsible for pumping blood throughout the body?', 'multiple_choice', 'Liver', 'Lungs', 'Heart', 'Kidneys', 'C', 1, 'The heart is the muscular organ that pumps blood through the circulatory system.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(8, 2, 2, 'How many chambers does the human heart have?', 'multiple_choice', 'Two', 'Three', 'Four', 'Five', 'C', 1, 'The heart has four chambers: two atria and two ventricles.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(9, 2, 3, 'What is the normal resting heart rate for adults?', 'multiple_choice', '40-50 bpm', '60-100 bpm', '120-140 bpm', '150-180 bpm', 'B', 1, 'Normal resting heart rate for adults is 60-100 beats per minute.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(10, 2, 2, 'The human body has 206 bones in the adult skeleton.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'An adult human skeleton typically contains 206 bones, though babies are born with about 270 bones that fuse as they grow.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(11, 2, 3, 'The liver is located in the left upper quadrant of the abdomen.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'The liver is primarily located in the right upper quadrant of the abdomen, beneath the diaphragm.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(12, 2, 2, 'The skin is the largest organ in the human body.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'The skin is the largest organ, covering the entire body surface.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(13, 4, 5, 'What is the normal duration of pregnancy?', 'multiple_choice', '30 weeks', '40 weeks', '50 weeks', '60 weeks', 'B', 1, 'Normal pregnancy duration is approximately 40 weeks or 280 days from the last menstrual period.', 3, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(14, 4, 5, 'Which trimester is considered the most critical for fetal development?', 'multiple_choice', 'First trimester', 'Second trimester', 'Third trimester', 'All are equally critical', 'A', 1, 'The first trimester is crucial as major organs and structures develop during this period.', 3, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(15, 4, 5, 'A normal pregnancy lasts approximately 40 weeks from the first day of the last menstrual period.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Pregnancy duration is calculated as 40 weeks or 280 days from the last menstrual period (LMP).', 3, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(16, 4, 5, 'Fetal movements should be felt by the mother starting from the first trimester.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Fetal movements (quickening) are typically felt between 16-25 weeks of pregnancy, which is in the second trimester.', 3, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(17, 4, 5, 'Folic acid supplementation helps prevent neural tube defects in developing fetuses.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Folic acid is essential for preventing neural tube defects and should be taken before and during pregnancy.', 3, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(18, 6, 6, 'What is the primary focus of public health?', 'multiple_choice', 'Individual patient care', 'Population health', 'Hospital management', 'Pharmaceutical sales', 'B', 1, 'Public health focuses on protecting and improving the health of entire populations.', 4, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(19, 6, 6, 'Which of the following is a communicable disease?', 'multiple_choice', 'Diabetes', 'Tuberculosis', 'Hypertension', 'Cancer', 'B', 1, 'Tuberculosis is a communicable disease that spreads from person to person.', 4, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(20, 6, 6, 'Vaccination is one of the most cost-effective public health interventions.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Vaccines prevent millions of deaths annually and are considered one of the most successful and cost-effective public health measures.', 4, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(21, 6, 6, 'Antibiotics are effective against viral infections like the common cold.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Antibiotics only work against bacterial infections, not viral infections. Misuse of antibiotics contributes to antibiotic resistance.', 4, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(22, 6, 6, 'Hand washing is one of the most effective ways to prevent disease transmission.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Hand washing is one of the most effective ways to prevent the spread of infections.', 4, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(23, 8, 8, 'What is the primary purpose of anesthesia?', 'multiple_choice', 'To cure diseases', 'To prevent pain during procedures', 'To increase blood pressure', 'To improve digestion', 'B', 1, 'Anesthesia is used to prevent pain and discomfort during medical procedures.', 5, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(24, 8, 8, 'Which type of anesthesia affects the entire body?', 'multiple_choice', 'Local anesthesia', 'Regional anesthesia', 'General anesthesia', 'Topical anesthesia', 'C', 1, 'General anesthesia affects the entire body and causes unconsciousness.', 5, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(25, 8, 8, 'General anesthesia causes complete loss of consciousness.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'General anesthesia induces a reversible state of unconsciousness, allowing surgical procedures to be performed without pain or awareness.', 5, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(26, 8, 8, 'Local anesthesia affects the entire body.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Local anesthesia only numbs a specific area of the body where it is applied, without affecting consciousness.', 5, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(27, 8, 9, 'Oxygen saturation must be continuously monitored during anesthesia.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Oxygen saturation is critical to monitor to ensure adequate oxygenation during anesthesia.', 5, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(28, 10, 7, 'What is the normal pH range of human blood?', 'multiple_choice', '6.35-6.45', '7.35-7.45', '8.35-8.45', '9.35-9.45', 'B', 1, 'Normal blood pH is slightly alkaline, ranging from 7.35 to 7.45.', 6, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(29, 10, 7, 'Which blood type is considered the universal donor?', 'multiple_choice', 'A', 'B', 'AB', 'O', 'D', 1, 'Type O negative blood is the universal donor as it can be given to any blood type.', 6, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(30, 10, 7, 'Blood type O negative is considered the universal donor.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'O negative blood can be given to patients of any blood type in emergency situations, making it the universal donor.', 6, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(31, 10, 7, 'Hemoglobin is found in white blood cells.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Hemoglobin is the oxygen-carrying protein found in red blood cells, not white blood cells.', 6, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(32, 10, 7, 'Red blood cells are responsible for transporting oxygen throughout the body.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Red blood cells contain hemoglobin which transports oxygen throughout the body.', 6, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(33, 1, 1, 'What does the acronym ADPIE stand for in the nursing process?', 'multiple_choice', 'Assess, Diagnose, Plan, Implement, Evaluate', 'Analyze, Develop, Perform, Inspect, Execute', 'Admit, Discharge, Prescribe, Inject, Examine', 'Advise, Direct, Prepare, Intervene, Exit', 'A', 1, 'ADPIE represents the five steps of the nursing process.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(34, 1, 9, 'How long should you scrub your hands during hand washing?', 'multiple_choice', '5 seconds', '10 seconds', '20 seconds', '60 seconds', 'C', 1, 'Proper hand washing requires at least 20 seconds of scrubbing.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(35, 1, 1, 'Which vital sign is measured in beats per minute?', 'multiple_choice', 'Temperature', 'Blood pressure', 'Pulse', 'Respiratory rate', 'C', 1, 'Pulse is measured in beats per minute (bpm).', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(36, 1, 9, 'What is the proper angle for intramuscular injection?', 'multiple_choice', '15 degrees', '45 degrees', '90 degrees', '180 degrees', 'C', 1, 'Intramuscular injections are given at a 90-degree angle.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(37, 1, 1, 'What is the normal body temperature in Celsius?', 'multiple_choice', '35.5°C', '37°C', '38.5°C', '40°C', 'B', 1, 'Normal body temperature is approximately 37°C or 98.6°F.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(38, 1, 9, 'Nurses should always identify patients using two identifiers before administering medication.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Two patient identifiers (name and date of birth) are required for patient safety.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(39, 1, 1, 'Documentation in nursing should be done at the end of the shift.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Documentation should be done immediately after care is provided to ensure accuracy.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(40, 1, 9, 'Standard precautions should be used with all patients regardless of diagnosis.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Standard precautions are infection control practices used with all patients.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(41, 1, 1, 'Nurses can delegate assessment tasks to unlicensed assistive personnel.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Assessment is a nursing responsibility that cannot be delegated to unlicensed personnel.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(42, 1, 9, 'Gloves should be changed between tasks on the same patient.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Gloves should be changed to prevent cross-contamination between different body sites.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(43, 2, 2, 'Which bone protects the brain?', 'multiple_choice', 'Femur', 'Skull', 'Ribs', 'Pelvis', 'B', 1, 'The skull (cranium) protects the brain.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(44, 2, 3, 'What is the largest artery in the human body?', 'multiple_choice', 'Pulmonary artery', 'Carotid artery', 'Aorta', 'Femoral artery', 'C', 1, 'The aorta is the largest artery, carrying blood from the heart to the body.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(45, 2, 2, 'How many pairs of ribs does a human have?', 'multiple_choice', '10', '12', '14', '16', 'B', 1, 'Humans have 12 pairs of ribs (24 ribs total).', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(46, 2, 3, 'Which organ produces insulin?', 'multiple_choice', 'Liver', 'Pancreas', 'Kidney', 'Spleen', 'B', 1, 'The pancreas produces insulin to regulate blood sugar.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(47, 2, 2, 'What is the longest bone in the human body?', 'multiple_choice', 'Humerus', 'Tibia', 'Femur', 'Radius', 'C', 1, 'The femur (thigh bone) is the longest and strongest bone.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(48, 2, 3, 'The lungs are located in the thoracic cavity.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'The lungs are housed in the thoracic (chest) cavity, protected by the rib cage.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(49, 2, 2, 'The human spine has 33 vertebrae.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'The spine consists of 33 vertebrae: 7 cervical, 12 thoracic, 5 lumbar, 5 sacral (fused), and 4 coccygeal (fused).', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(50, 2, 3, 'The kidneys filter approximately 180 liters of blood per day.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'The kidneys filter about 180 liters of blood daily, producing 1-2 liters of urine.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(51, 2, 2, 'Cartilage is a type of connective tissue.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Cartilage is a flexible connective tissue found in joints, ears, nose, and other structures.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(52, 2, 3, 'The stomach is located in the right upper quadrant of the abdomen.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'The stomach is primarily located in the left upper quadrant of the abdomen.', 1, '2026-02-08 18:02:23', '2026-02-08 18:02:23'),
(53, 3, 4, 'What is the antidote for warfarin overdose?', 'multiple_choice', 'Protamine sulfate', 'Vitamin K', 'Naloxone', 'Flumazenil', 'B', 1, 'Vitamin K is the antidote for warfarin (Coumadin) overdose.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(54, 3, 9, 'What is the priority nursing action for a patient with chest pain?', 'multiple_choice', 'Document the pain', 'Administer oxygen', 'Call the family', 'Ambulate the patient', 'B', 1, 'Administering oxygen is priority to improve cardiac oxygenation.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(55, 3, 4, 'Which medication is used to treat hypertension?', 'multiple_choice', 'Insulin', 'Lisinopril', 'Aspirin', 'Metformin', 'B', 1, 'Lisinopril is an ACE inhibitor used to treat high blood pressure.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(56, 3, 9, 'What is the normal range for adult blood pressure?', 'multiple_choice', '90/60 to 120/80 mmHg', '130/90 to 150/100 mmHg', '160/100 to 180/110 mmHg', '200/120 to 220/130 mmHg', 'A', 1, 'Normal blood pressure is less than 120/80 mmHg.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(57, 3, 4, 'Which lab value indicates kidney function?', 'multiple_choice', 'Hemoglobin', 'Creatinine', 'Glucose', 'Cholesterol', 'B', 1, 'Creatinine levels indicate how well the kidneys are filtering waste.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(58, 3, 9, 'Patients with diabetes should skip meals if their blood sugar is high.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Diabetic patients should maintain regular meal schedules and work with healthcare providers to adjust medications.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(59, 3, 4, 'Aspirin is an anticoagulant medication.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Aspirin inhibits platelet aggregation and acts as an anticoagulant.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(60, 3, 9, 'A patient with a myocardial infarction should be kept on bed rest initially.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Initial bed rest reduces cardiac workload and oxygen demand after a heart attack.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(61, 3, 4, 'Antibiotics are effective against all types of infections.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Antibiotics only work against bacterial infections, not viral or fungal infections.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(62, 3, 9, 'Patients should be assessed for pain using a standardized pain scale.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Standardized pain scales ensure consistent and accurate pain assessment.', 1, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(63, 4, 5, 'What is the first stage of labor?', 'multiple_choice', 'Delivery of the baby', 'Cervical dilation', 'Delivery of placenta', 'Recovery', 'B', 1, 'The first stage of labor involves cervical dilation from 0 to 10 cm.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(64, 4, 5, 'At what week is a fetus considered full-term?', 'multiple_choice', '32 weeks', '35 weeks', '37 weeks', '42 weeks', 'C', 1, 'A pregnancy is considered full-term at 37 weeks.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(65, 4, 5, 'What is the normal fetal heart rate?', 'multiple_choice', '60-80 bpm', '80-100 bpm', '110-160 bpm', '180-200 bpm', 'C', 1, 'Normal fetal heart rate is 110-160 beats per minute.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(66, 4, 5, 'Which hormone maintains pregnancy?', 'multiple_choice', 'Estrogen', 'Progesterone', 'Testosterone', 'Insulin', 'B', 1, 'Progesterone is essential for maintaining pregnancy.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(67, 4, 5, 'What is the recommended weight gain during pregnancy for normal BMI?', 'multiple_choice', '5-10 kg', '11-16 kg', '20-25 kg', '30-35 kg', 'B', 1, 'Recommended weight gain for normal BMI is 11-16 kg (25-35 lbs).', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(68, 5, 5, 'What is postpartum hemorrhage defined as?', 'multiple_choice', 'Blood loss >500 ml after vaginal delivery', 'Blood loss >100 ml after delivery', 'Blood loss >200 ml after delivery', 'Any bleeding after delivery', 'A', 1, 'Postpartum hemorrhage is blood loss exceeding 500 ml after vaginal delivery.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(69, 4, 5, 'Prenatal care should begin in the second trimester.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Prenatal care should ideally begin as soon as pregnancy is confirmed, in the first trimester.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(70, 4, 5, 'Breastfeeding should be initiated within the first hour after birth.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Early initiation of breastfeeding promotes bonding and provides important antibodies to the newborn.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(71, 5, 5, 'The umbilical cord contains two arteries and one vein.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'The umbilical cord normally contains two arteries and one vein.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(72, 4, 5, 'Morning sickness only occurs in the morning.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Despite its name, morning sickness can occur at any time of day.', 3, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(73, 6, 6, 'What does WHO stand for?', 'multiple_choice', 'World Health Office', 'World Health Organization', 'Worldwide Health Operations', 'World Hospital Organization', 'B', 1, 'WHO is the World Health Organization, a UN agency for public health.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(74, 6, 6, 'Which disease was eradicated globally through vaccination?', 'multiple_choice', 'Polio', 'Smallpox', 'Measles', 'Tuberculosis', 'B', 1, 'Smallpox was declared eradicated in 1980 through global vaccination efforts.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(75, 6, 6, 'What is the leading cause of death worldwide?', 'multiple_choice', 'Cancer', 'Cardiovascular disease', 'Respiratory infections', 'Accidents', 'B', 1, 'Cardiovascular diseases are the leading cause of death globally.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(76, 7, 6, 'What is the basic reproduction number (R0) in epidemiology?', 'multiple_choice', 'Number of deaths', 'Number of new infections from one case', 'Number of recovered patients', 'Number of vaccinated individuals', 'B', 1, 'R0 represents the average number of people infected by one contagious person.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(77, 6, 6, 'Which vitamin deficiency causes scurvy?', 'multiple_choice', 'Vitamin A', 'Vitamin B12', 'Vitamin C', 'Vitamin D', 'C', 1, 'Scurvy is caused by severe vitamin C deficiency.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(78, 7, 6, 'What is herd immunity?', 'multiple_choice', 'Immunity in animals', 'Individual immunity', 'Population-level immunity', 'Temporary immunity', 'C', 1, 'Herd immunity occurs when enough people are immune to prevent disease spread.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(79, 6, 6, 'Malaria is transmitted by mosquitoes.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Malaria is transmitted through the bite of infected Anopheles mosquitoes.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(80, 7, 6, 'Incidence refers to existing cases of disease in a population.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Incidence refers to new cases; prevalence refers to existing cases.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(81, 6, 6, 'Clean water access is a social determinant of health.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Access to clean water significantly impacts population health outcomes.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(82, 7, 6, 'An epidemic affects multiple countries or continents.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'An epidemic is widespread in one region; a pandemic affects multiple countries or continents.', 4, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(83, 8, 8, 'What is the ASA classification system used for?', 'multiple_choice', 'Anesthesia dosing', 'Patient physical status', 'Surgery duration', 'Recovery time', 'B', 1, 'ASA classification assesses patient physical status before anesthesia.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(84, 8, 8, 'Which drug is commonly used for induction of general anesthesia?', 'multiple_choice', 'Aspirin', 'Propofol', 'Insulin', 'Warfarin', 'B', 1, 'Propofol is a commonly used induction agent for general anesthesia.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(85, 9, 4, 'What is the antidote for opioid overdose?', 'multiple_choice', 'Epinephrine', 'Naloxone', 'Atropine', 'Dopamine', 'B', 1, 'Naloxone (Narcan) reverses opioid overdose effects.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(86, 8, 8, 'What does MAC stand for in anesthesia?', 'multiple_choice', 'Maximum Anesthesia Concentration', 'Monitored Anesthesia Care', 'Minimal Airway Control', 'Medical Anesthesia Certification', 'B', 1, 'MAC is Monitored Anesthesia Care, a type of sedation.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(87, 9, 4, 'Which gas is commonly used for general anesthesia?', 'multiple_choice', 'Oxygen', 'Sevoflurane', 'Carbon dioxide', 'Nitrogen', 'B', 1, 'Sevoflurane is a volatile anesthetic gas used for general anesthesia.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(88, 8, 8, 'Spinal anesthesia is a type of regional anesthesia.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Spinal anesthesia blocks nerve transmission in a specific region of the body.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(89, 9, 9, 'Patients should fast before general anesthesia to prevent aspiration.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Fasting reduces the risk of aspiration of stomach contents during anesthesia.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(90, 8, 8, 'Epidural anesthesia is commonly used for cesarean sections.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Epidural anesthesia provides effective pain relief for cesarean deliveries.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(91, 9, 4, 'Atropine is used to increase heart rate.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Atropine is an anticholinergic drug that increases heart rate.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(92, 8, 9, 'Capnography measures carbon dioxide levels in exhaled breath.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Capnography monitors CO2 levels and is essential during anesthesia.', 5, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(93, 10, 7, 'What is the normal range for fasting blood glucose?', 'multiple_choice', '50-70 mg/dL', '70-100 mg/dL', '120-150 mg/dL', '180-200 mg/dL', 'B', 1, 'Normal fasting blood glucose is 70-100 mg/dL.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(94, 10, 7, 'Which test measures kidney function?', 'multiple_choice', 'Hemoglobin A1C', 'Creatinine', 'Lipid panel', 'Liver enzymes', 'B', 1, 'Serum creatinine is a key indicator of kidney function.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(95, 11, 7, 'What is anemia?', 'multiple_choice', 'High white blood cell count', 'Low red blood cell count', 'High platelet count', 'Low glucose level', 'B', 1, 'Anemia is a condition with low red blood cells or hemoglobin.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(96, 10, 7, 'What does HbA1c measure?', 'multiple_choice', 'Current blood sugar', 'Average blood sugar over 3 months', 'Kidney function', 'Liver function', 'B', 1, 'HbA1c reflects average blood glucose levels over 2-3 months.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(97, 11, 7, 'What is the normal white blood cell count?', 'multiple_choice', '1,000-3,000 cells/μL', '4,000-11,000 cells/μL', '15,000-20,000 cells/μL', '25,000-30,000 cells/μL', 'B', 1, 'Normal WBC count is 4,000-11,000 cells per microliter.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(98, 10, 7, 'Cholesterol levels should be checked while fasting.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Fasting lipid panels provide more accurate cholesterol measurements.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(99, 11, 7, 'Platelets are responsible for blood clotting.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Platelets (thrombocytes) play a crucial role in blood clotting.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(100, 10, 7, 'Urine should be tested within 2 hours of collection.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'Urine samples should be tested promptly to ensure accurate results.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(101, 11, 7, 'A complete blood count (CBC) includes hemoglobin, WBC, and platelet counts.', 'true_false', 'True', 'False', NULL, NULL, 'True', 1, 'CBC is a comprehensive blood test that measures multiple blood components.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24'),
(102, 10, 7, 'Elevated liver enzymes always indicate liver disease.', 'true_false', 'True', 'False', NULL, NULL, 'False', 1, 'Elevated liver enzymes can have various causes and require further investigation.', 6, '2026-02-08 18:02:24', '2026-02-08 18:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `question_topics`
--

CREATE TABLE `question_topics` (
  `topic_id` int(11) NOT NULL,
  `topic_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `question_topics`
--

INSERT INTO `question_topics` (`topic_id`, `topic_name`, `description`, `created_at`) VALUES
(1, 'Nursing Fundamentals', 'Basic nursing concepts and skills', '2026-02-08 18:02:23'),
(2, 'Anatomy', 'Human body structure', '2026-02-08 18:02:23'),
(3, 'Physiology', 'Body functions and systems', '2026-02-08 18:02:23'),
(4, 'Pharmacology', 'Drug therapy and medications', '2026-02-08 18:02:23'),
(5, 'Maternal Health', 'Pregnancy and childbirth', '2026-02-08 18:02:23'),
(6, 'Public Health', 'Community health concepts', '2026-02-08 18:02:23'),
(7, 'Laboratory Techniques', 'Lab procedures and tests', '2026-02-08 18:02:23'),
(8, 'Anesthesia Basics', 'Anesthesia principles', '2026-02-08 18:02:23'),
(9, 'Patient Safety', 'Safety protocols and procedures', '2026-02-08 18:02:23');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_code` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `academic_year` varchar(20) DEFAULT 'Year 1',
  `semester` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_code`, `username`, `password`, `full_name`, `email`, `phone`, `gender`, `department_id`, `academic_year`, `semester`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'STU001', 'alem.h', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alem Hailu', 'alem.h@student.dmu.edu.et', '+251911111001', 'Male', 1, 'Year 1', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(2, 'STU002', 'bethel.k', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bethel Kebede', 'bethel.k@student.dmu.edu.et', '+251911111002', 'Female', 1, 'Year 1', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(3, 'STU003', 'chala.m', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Chala Mengistu', 'chala.m@student.dmu.edu.et', '+251911111003', 'Male', 2, 'Year 1', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(4, 'STU004', 'eden.t', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eden Tesfaye', 'eden.t@student.dmu.edu.et', '+251911111004', 'Female', 3, 'Year 1', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(5, 'STU005', 'frehiwot.a', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Frehiwot Alemu', 'frehiwot.a@student.dmu.edu.et', '+251911111005', 'Female', 4, 'Year 1', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22'),
(6, 'STU006', 'genet.w', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Genet Worku', 'genet.w@student.dmu.edu.et', '+251911111006', 'Female', 5, 'Year 1', 1, 1, NULL, '2026-02-08 18:02:22', '2026-02-08 18:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `answer_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer` enum('A','B','C','D') DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `points_earned` decimal(5,2) DEFAULT 0.00,
  `answered_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`answer_id`, `result_id`, `question_id`, `selected_answer`, `is_correct`, `points_earned`, `answered_at`) VALUES
(1, 3, 1, 'A', 0, 0.00, NULL),
(2, 3, 2, 'A', 0, 0.00, NULL),
(3, 3, 3, 'B', 1, 1.00, NULL),
(4, 4, 7, 'C', 1, 1.00, NULL),
(5, 4, 8, 'C', 1, 1.00, NULL),
(6, 4, 10, '', 0, 0.00, NULL),
(7, 4, 11, '', 0, 0.00, NULL),
(8, 4, 45, 'D', 0, 0.00, NULL),
(9, 4, 46, 'D', 0, 0.00, NULL),
(10, 4, 47, 'D', 0, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_courses`
--

CREATE TABLE `student_courses` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('enrolled','completed','dropped','failed') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_courses`
--

INSERT INTO `student_courses` (`enrollment_id`, `student_id`, `course_id`, `enrolled_at`, `status`) VALUES
(96, 1, 1, '2026-02-08 18:02:22', 'enrolled'),
(97, 1, 2, '2026-02-08 18:02:22', 'enrolled'),
(98, 2, 1, '2026-02-08 18:02:22', 'enrolled'),
(99, 2, 2, '2026-02-08 18:02:22', 'enrolled'),
(100, 3, 4, '2026-02-08 18:02:22', 'enrolled'),
(101, 3, 5, '2026-02-08 18:02:22', 'enrolled'),
(102, 4, 6, '2026-02-08 18:02:22', 'enrolled'),
(103, 4, 7, '2026-02-08 18:02:22', 'enrolled'),
(104, 5, 8, '2026-02-08 18:02:22', 'enrolled'),
(105, 5, 9, '2026-02-08 18:02:22', 'enrolled'),
(106, 6, 10, '2026-02-08 18:02:22', 'enrolled'),
(107, 6, 11, '2026-02-08 18:02:22', 'enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `technical_issues`
--

CREATE TABLE `technical_issues` (
  `issue_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `issue_description` text NOT NULL,
  `issue_type` enum('connectivity','system_error','submission_failure','display_issue','timer_error','login_problem','other') DEFAULT 'other',
  `browser_info` varchar(255) DEFAULT NULL,
  `os_info` varchar(255) DEFAULT NULL,
  `screen_resolution` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `screenshot_path` varchar(500) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('pending','resolved','closed') DEFAULT 'pending',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technical_issues`
--

INSERT INTO `technical_issues` (`issue_id`, `student_id`, `exam_id`, `issue_description`, `issue_type`, `browser_info`, `os_info`, `screen_resolution`, `ip_address`, `screenshot_path`, `admin_notes`, `resolved_by`, `resolved_at`, `priority`, `status`, `reported_at`) VALUES
(1, 1, 21, 'it didnt show me the the correct date', 'other', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'medium', 'pending', '2026-02-10 06:17:52'),
(2, 1, 21, 'it didnt show me the the correct date', 'other', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'medium', 'closed', '2026-02-10 06:18:31'),
(3, 1, 21, 'it didnt show me the the correct date', 'other', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'medium', 'resolved', '2026-02-10 06:20:59'),
(4, 1, 20, 'please fix it as soon as possible', 'login_problem', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'Win32', '1536x864', '::1', NULL, NULL, NULL, NULL, 'medium', 'pending', '2026-02-10 07:18:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `table_name` (`table_name`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_code` (`department_code`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `department_heads`
--
ALTER TABLE `department_heads`
  ADD PRIMARY KEY (`department_head_id`),
  ADD UNIQUE KEY `head_code` (`head_code`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `exam_category_id` (`exam_category_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `exam_approval_history`
--
ALTER TABLE `exam_approval_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exam_categories`
--
ALTER TABLE `exam_categories`
  ADD PRIMARY KEY (`exam_category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`exam_question_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `faculty_code` (`faculty_code`);

--
-- Indexes for table `grading_config`
--
ALTER TABLE `grading_config`
  ADD PRIMARY KEY (`config_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`instructor_id`),
  ADD UNIQUE KEY `instructor_code` (`instructor_code`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `instructor_courses`
--
ALTER TABLE `instructor_courses`
  ADD PRIMARY KEY (`assignment_id`),
  ADD UNIQUE KEY `unique_instructor_course` (`instructor_id`,`course_id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `practice_questions`
--
ALTER TABLE `practice_questions`
  ADD PRIMARY KEY (`practice_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `question_topics`
--
ALTER TABLE `question_topics`
  ADD PRIMARY KEY (`topic_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_code` (`student_code`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `result_id` (`result_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `unique_student_course` (`student_id`,`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `technical_issues`
--
ALTER TABLE `technical_issues`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `idx_status_priority` (`status`,`priority`),
  ADD KEY `idx_reported_at` (`reported_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `department_heads`
--
ALTER TABLE `department_heads`
  MODIFY `department_head_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `exam_approval_history`
--
ALTER TABLE `exam_approval_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exam_categories`
--
ALTER TABLE `exam_categories`
  MODIFY `exam_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `exam_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=407;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grading_config`
--
ALTER TABLE `grading_config`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `instructor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `instructor_courses`
--
ALTER TABLE `instructor_courses`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `practice_questions`
--
ALTER TABLE `practice_questions`
  MODIFY `practice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `question_topics`
--
ALTER TABLE `question_topics`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `technical_issues`
--
ALTER TABLE `technical_issues`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_courses_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_departments_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `department_heads`
--
ALTER TABLE `department_heads`
  ADD CONSTRAINT `fk_department_heads_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `fk_exams_approver` FOREIGN KEY (`approved_by`) REFERENCES `department_heads` (`department_head_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exams_category` FOREIGN KEY (`exam_category_id`) REFERENCES `exam_categories` (`exam_category_id`),
  ADD CONSTRAINT `fk_exams_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exams_creator` FOREIGN KEY (`created_by`) REFERENCES `instructors` (`instructor_id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_approval_history`
--
ALTER TABLE `exam_approval_history`
  ADD CONSTRAINT `fk_approval_history_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `fk_exam_questions_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exam_questions_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `fk_exam_results_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exam_results_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
