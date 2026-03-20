-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 08:43 AM
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
-- Database: `amore`
--

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_level` enum('jhs','shs') NOT NULL DEFAULT 'jhs',
  `grade_level` varchar(255) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `citizenship` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `address` varchar(500) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `school_type` enum('Public','Private') NOT NULL,
  `private_type` enum('ESC','Non-ESC') DEFAULT NULL,
  `student_esc_no` varchar(8) DEFAULT NULL,
  `esc_school_id` varchar(6) DEFAULT NULL,
  `school_name` varchar(255) NOT NULL,
  `strand` enum('STEM','ABM','HUMSS','TVL') DEFAULT NULL,
  `tvl_specialization` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `applicant_id`, `user_id`, `school_level`, `grade_level`, `lrn`, `last_name`, `first_name`, `middle_name`, `dob`, `age`, `gender`, `citizenship`, `religion`, `height`, `weight`, `address`, `phone`, `email`, `school_type`, `private_type`, `student_esc_no`, `esc_school_id`, `school_name`, `strand`, `tvl_specialization`, `mother_name`, `mother_occupation`, `father_name`, `father_occupation`, `status`, `remarks`, `approved_by`, `approved_at`, `approval_notes`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 'jhs', '', '123123123123', 'Dela Cruz', 'John', 'a', '2009-09-01', 16, 'Male', 'Filipino', 'catholic', 165.00, 64.00, 'awdawdaw', '3123123', 'admin@amore.edu', 'Private', 'ESC', '09097898', '321321', 'dawdawd', NULL, NULL, 'Dwadawd', 'dwadaw', 'dawdawd', 'dwadwadaw', 'approved', NULL, 1, '2025-11-04 20:50:31', NULL, NULL, '2025-11-04 20:49:38', '2025-11-04 20:50:31'),
(2, NULL, 3, 'shs', '', '231231231231', 'Omenia', 'Jroa', 'Man', '2010-01-02', 15, 'Male', 'Filipino', 'catholic', 165.00, 65.00, 'dawdwda', '31231241231', 'dwadaw@gmail.com', 'Private', 'ESC', '09097898', '321321', 'dawdawd', 'TVL', 'Hairdressing (NCII)', 'dawdwad', 'dawdawda', 'dwadwada', 'dwadawdwa', 'approved', NULL, 1, '2025-11-04 20:53:09', NULL, NULL, '2025-11-04 20:52:50', '2025-11-04 20:53:09'),
(4, NULL, 5, 'jhs', '', '534234324243', 'dwadawd', 'dwadaw', 'dwadawdwa', '2009-01-02', 16, 'Male', 'dawdaw', 'dwadaw', 187.00, 23.00, 'wadwda', '32131231231', 'dawdawd22@gmail.com', 'Public', NULL, NULL, NULL, 'dwadawdaw', NULL, NULL, 'dwadawdaw', 'dwadawdaw', 'dwadawdaw', 'dwadawdaw', 'approved', NULL, 1, '2025-11-04 21:01:58', NULL, NULL, '2025-11-04 21:01:11', '2025-11-04 21:01:58'),
(5, NULL, 6, 'jhs', 'Grade 8', '019231231241', 'dawdawd', 'dwadawdad', 'dwadawda', '2009-01-02', 16, 'Male', 'awdaw', 'catholic', 165.00, 65.00, 'awdawdaw', '1321321312', 'dawdawd@gmail.cpm', 'Public', NULL, NULL, NULL, 'dwadawd', NULL, NULL, 'dwadawdaw', 'dwadawda', 'dwadawdaw', 'dwadawdaw', 'approved', NULL, 1, '2025-11-04 21:06:47', NULL, NULL, '2025-11-04 21:05:51', '2025-11-04 21:06:47'),
(6, NULL, 8, 'jhs', 'Grade 8', '123123123141', 'bars', 'Mie', 'awd', '2009-01-02', 16, 'Male', 'dawdaw', 'dawdw', 178.00, 65.00, 'awdaw', '09123', 'adwadw@gmail.com', 'Private', 'Non-ESC', NULL, NULL, 'adawdwa', NULL, NULL, 'dawdawd', 'dawdawd', 'dwadada', 'dwadawdaw', 'approved', NULL, 1, '2025-11-05 06:58:54', NULL, NULL, '2025-11-05 06:56:28', '2025-11-05 06:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `target_audience` enum('public','all','students','faculty') NOT NULL DEFAULT 'all',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `audience` enum('all','faculty','students') NOT NULL DEFAULT 'all',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(255) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(255) NOT NULL,
  `place_of_birth` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `house_number` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `enrollment_type` varchar(255) NOT NULL,
  `enrollment_date` date NOT NULL,
  `grade_level` varchar(255) NOT NULL,
  `track_strand` varchar(255) DEFAULT NULL,
  `lrn` varchar(255) NOT NULL,
  `previous_school` varchar(255) NOT NULL,
  `school_address` varchar(255) NOT NULL,
  `general_average` decimal(5,2) NOT NULL,
  `last_grade_completed` varchar(255) NOT NULL,
  `school_doc` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_contact` varchar(255) NOT NULL,
  `father_occupation` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `mother_contact` varchar(255) NOT NULL,
  `mother_occupation` varchar(255) NOT NULL,
  `guardian_name` varchar(255) NOT NULL,
  `relationship` varchar(255) NOT NULL,
  `parent_contact` varchar(255) NOT NULL,
  `parent_address` varchar(255) NOT NULL,
  `emergency_name` varchar(255) NOT NULL,
  `emergency_relationship` varchar(255) NOT NULL,
  `emergency_contact` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `event_type` varchar(255) NOT NULL DEFAULT 'general',
  `color` varchar(255) NOT NULL DEFAULT '#198754',
  `is_all_day` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_positions`
--

CREATE TABLE `faculty_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('administrative','teaching','support') NOT NULL DEFAULT 'teaching',
  `hierarchy_level` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty_positions`
--

INSERT INTO `faculty_positions` (`id`, `name`, `code`, `description`, `category`, `hierarchy_level`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Principal', 'PRINCIPAL', 'Oversees the entire academic community and strategic direction.', 'administrative', 1, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23'),
(2, 'Assistant Principal', 'ASSISTANT_PRINCIPAL', 'Supports the principal in managing academic programs and operations.', 'administrative', 2, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23'),
(3, 'Department Head', 'DEPARTMENT_HEAD', 'Leads a subject department and coordinates curriculum execution.', 'administrative', 3, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23'),
(4, 'Academic Coordinator', 'ACADEMIC_COORDINATOR', 'Coordinates academic initiatives and assessment standards.', 'support', 4, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23'),
(5, 'Senior Teacher', 'SENIOR_TEACHER', 'Provides mentorship and advanced instruction within the department.', 'teaching', 5, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23'),
(6, 'Teacher', 'TEACHER', 'Delivers classroom instruction and student engagement.', 'teaching', 6, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23'),
(7, 'Guidance Counselor', 'GUIDANCE_COUNSELOR', 'Supports student wellbeing and development programs.', 'support', 7, 1, '2025-11-05 09:45:23', '2025-11-05 09:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jhs_admissions`
--

CREATE TABLE `jhs_admissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` varchar(255) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `applying_for_grade` varchar(255) NOT NULL,
  `school_year` varchar(255) NOT NULL,
  `previous_school` varchar(255) NOT NULL,
  `school_type` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_occupation` varchar(255) NOT NULL,
  `mother_maiden_name` varchar(255) NOT NULL,
  `mother_occupation` varchar(255) NOT NULL,
  `application_status` varchar(255) NOT NULL DEFAULT 'pending',
  `status` enum('pending','approved','rejected','waitlisted') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `application_date` datetime NOT NULL,
  `confirm_details` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"e6263b03-b1c2-429b-829f-24cc23a0d39c\",\"displayName\":\"App\\\\Mail\\\\RegistrationReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":15:{s:8:\\\"mailable\\\";O:29:\\\"App\\\\Mail\\\\RegistrationReceived\\\":3:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"Zeinabkulang@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\"},\"createdAt\":1762355087,\"delay\":null}', 0, NULL, 1762355087, 1762355087),
(2, 'default', '{\"uuid\":\"0b78139b-287f-4ce7-986b-847274f96ee0\",\"displayName\":\"App\\\\Mail\\\\AccountApproved\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":15:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\AccountApproved\\\":3:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"Zeinabkulang@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\"},\"createdAt\":1762355119,\"delay\":null}', 0, NULL, 1762355119, 1762355119),
(3, 'default', '{\"uuid\":\"c005f15b-94c4-415d-a812-89146a28c335\",\"displayName\":\"App\\\\Mail\\\\RegistrationReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":15:{s:8:\\\"mailable\\\";O:29:\\\"App\\\\Mail\\\\RegistrationReceived\\\":3:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:15:\\\"Nardo@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\"},\"createdAt\":1762363921,\"delay\":null}', 0, NULL, 1762363921, 1762363921),
(4, 'default', '{\"uuid\":\"1e15cb37-6cca-4a3f-8f35-4afd9667a21d\",\"displayName\":\"App\\\\Mail\\\\AccountApproved\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":15:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\AccountApproved\\\":3:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:15:\\\"Nardo@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\"},\"createdAt\":1762363981,\"delay\":null}', 0, NULL, 1762363981, 1762363981),
(5, 'default', '{\"uuid\":\"15ec3697-5e7a-4ea5-b009-3e7cacf2039f\",\"displayName\":\"App\\\\Mail\\\\RegistrationReceived\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":15:{s:8:\\\"mailable\\\";O:29:\\\"App\\\\Mail\\\\RegistrationReceived\\\":3:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:26:\\\"barredomichael74@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\"},\"createdAt\":1762407204,\"delay\":null}', 0, NULL, 1762407205, 1762407205),
(6, 'default', '{\"uuid\":\"a96972e8-b73d-4e2c-9183-c7e7df0225c7\",\"displayName\":\"App\\\\Mail\\\\AccountApproved\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":15:{s:8:\\\"mailable\\\";O:24:\\\"App\\\\Mail\\\\AccountApproved\\\":3:{s:4:\\\"user\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:26:\\\"barredomichael74@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\"},\"createdAt\":1762407219,\"delay\":null}', 0, NULL, 1762407219, 1762407219);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_08_100331_create_logins_table', 1),
(5, '2025_08_14_172144_create_enrollments_table', 1),
(6, '2025_10_16_000001_create_jhs_admissions_table', 1),
(7, '2025_10_16_000002_create_shs_admissions_table', 1),
(8, '2025_10_17_004343_create_events_table', 1),
(9, '2025_10_18_013304_create_announcements_table', 1),
(10, '2025_10_18_052011_add_approval_fields_to_admissions_table', 1),
(11, '2025_10_22_092644_add_target_audience_to_announcements_table', 1),
(12, '2025_11_03_065401_create_admissions_table', 1),
(13, '2025_11_05_000001_add_grade_level_to_admissions_table', 2),
(14, '2025_11_06_000100_create_faculty_positions_table', 3),
(15, '2025_11_06_000200_add_faculty_position_fields_to_users_table', 4),
(16, '2025_11_06_120000_create_subjects_table', 5),
(17, '2025_11_06_180500_update_subjects_drop_department_and_is_active_columns', 6);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0svQIRyHYuliQLWknIMgnDNJSsQ3ysZAbbFTVobL', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWkVvWW53SHN2Z3ZyT2tmbk9aOXR1NVRXdW1NZXdMNW9KcWVqM0xlZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9zdWJqZWN0cz9ncmFkZV9sZXZlbD0mc2VhcmNoPSZzdWJqZWN0X3R5cGU9Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1762414951),
('xKCwvZHMFQHlRLWmGc2D2S7ciXGaBimrhzDkjDSU', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibmx4aThQWkxTY21uSmU4OVllVWRXdHB2NFJtMFNlaVU4cWJVb3R4ZiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGVwYXJ0bWVudC1oZWFkL3N1YmplY3RzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTE7fQ==', 1762414891);

-- --------------------------------------------------------

--
-- Table structure for table `shs_admissions`
--

CREATE TABLE `shs_admissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` varchar(255) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `citizenship` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `applying_for_grade` varchar(255) NOT NULL,
  `school_year` varchar(255) NOT NULL,
  `strand` varchar(255) NOT NULL,
  `tvl_specialization` varchar(255) DEFAULT NULL,
  `previous_school` varchar(255) NOT NULL,
  `school_type` varchar(255) NOT NULL,
  `private_school_type` varchar(255) DEFAULT NULL,
  `esc_student_no` varchar(255) DEFAULT NULL,
  `esc_school_id` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_occupation` varchar(255) NOT NULL,
  `mother_maiden_name` varchar(255) NOT NULL,
  `mother_occupation` varchar(255) NOT NULL,
  `application_status` varchar(255) NOT NULL DEFAULT 'pending',
  `status` enum('pending','approved','rejected','waitlisted') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `application_date` datetime NOT NULL,
  `confirm_details` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_type` enum('core','elective','specialized','extracurricular') DEFAULT 'core',
  `grade_level` enum('7','8','9','10','11','12','all') NOT NULL DEFAULT 'all',
  `hours_per_week` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `subject_type`, `grade_level`, `hours_per_week`, `created_at`, `updated_at`) VALUES
(1, 'English', 'This Subject is for English only', 'specialized', '11', NULL, '2025-11-05 21:31:07', '2025-11-05 23:41:30'),
(2, 'Purposive Communication', 'This Purposive Communication is a subject is a subject', NULL, '11', NULL, '2025-11-05 23:37:26', '2025-11-05 23:37:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `custom_id` varchar(255) NOT NULL,
  `account_type` varchar(255) NOT NULL,
  `faculty_position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `grade_level` varchar(255) DEFAULT NULL,
  `lrn` char(12) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `position_assigned_date` date DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','for_approval') NOT NULL DEFAULT 'active',
  `first_login` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `custom_id`, `account_type`, `faculty_position_id`, `first_name`, `middle_name`, `last_name`, `student_id`, `grade_level`, `lrn`, `department`, `position_assigned_date`, `assigned_by`, `email`, `email_verified_at`, `contact_number`, `profile_picture`, `password`, `status`, `first_login`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '2025-03-0001', 'admin', NULL, 'System', NULL, 'Administrator', NULL, NULL, NULL, 'Administration', NULL, NULL, 'admin@amoreacademy.com', '2025-11-05 09:45:23', '09123456789', NULL, '$2y$12$6I0Q0xTB2s5uouINRpUIRufFS432TvUE0zUKVi5aWgkQY12OdOGf2', 'active', 0, NULL, '2025-11-04 20:45:33', '2025-11-05 09:45:23'),
(2, '2025-01-0001', 'student', NULL, 'John', 'a', 'Dela Cruz', NULL, 'Grade 7', '123123123123', NULL, NULL, NULL, 'admin@amore.edu', NULL, '3123123', 'default.jpg', '$2y$12$LOtljJiHi.PErwBZXh782.vBDSjG3E/CWkDACIiaalRhUowGWyMcK', 'active', 1, NULL, '2025-11-04 20:50:31', '2025-11-04 20:50:31'),
(3, '2025-01-0002', 'student', NULL, 'Jroa', 'Man', 'Omenia', NULL, 'Grade 11 - TVL', '231231231231', NULL, NULL, NULL, 'dwadaw@gmail.com', NULL, '31231241231', '1762318428.jpg', '$2y$12$Dk91YZjdU7G8/LwQrPLG5.XZzL3.4/MXOSAWCUP9257r5VSPin6Sy', 'active', 0, NULL, '2025-11-04 20:53:09', '2025-11-04 20:53:48'),
(5, '2025-01-0003', 'student', NULL, 'dwadaw', 'dwadawdwa', 'dwadawd', NULL, 'Grade 7', '534234324243', NULL, NULL, NULL, 'dawdawd22@gmail.com', NULL, '32131231231', 'default.jpg', '$2y$12$e0wABAVihqjOj1p34PSONem/8prj2zRsaSfPi.nIT1yTnNsTCPdx.', 'active', 0, NULL, '2025-11-04 21:01:58', '2025-11-04 21:02:34'),
(6, '2025-01-0004', 'student', NULL, 'dwadawdad', 'dwadawda', 'dawdawd', NULL, 'Grade 7', '019231231241', NULL, NULL, NULL, 'dawdawd@gmail.cpm', NULL, '1321321312', 'default.jpg', '$2y$12$lcAusAXKF3kfAr1zkhjj0.lQ2g3ur1n88KRIGq6DCXftn0AY0Pmn6', 'active', 0, NULL, '2025-11-04 21:06:47', '2025-11-04 21:07:24'),
(8, '2025-01-0005', 'student', NULL, 'Mie', 'awd', 'bars', NULL, 'Grade 7', '123123123141', NULL, NULL, NULL, 'adwadw@gmail.com', NULL, '09123', '1762354778.png', '$2y$12$XlY9A3RKX8oLGvd7zWgrDeWFguiemPUBWC2LRGitGdQiYmz4g49S.', 'active', 0, NULL, '2025-11-05 06:58:54', '2025-11-05 06:59:38'),
(9, '2025-02-0001', 'faculty', 5, 'Jroa', NULL, 'Zeinab', NULL, NULL, NULL, 'Math', '2025-11-06', 1, 'Zeinabkulang@gmail.com', NULL, '09123841231', NULL, '$2y$12$lTf6QOwMHS3Rp.P/UeXU9OGjFT68PD9jTBm/YFazwDVwXWxeO.Kju', 'active', 1, NULL, '2025-11-05 07:04:45', '2025-11-05 20:43:30'),
(10, '2025-02-0002', 'faculty', 1, 'John', NULL, 'Nardo', NULL, NULL, NULL, 'English', '2025-11-05', 1, 'Nardo@gmail.com', NULL, '09123456788', NULL, '$2y$12$sSdvQniRTCBE7GLCUwLqhub5rDCPdhQnaoCUHIZPD9jPjD9cABB5K', 'active', 1, NULL, '2025-11-05 09:32:01', '2025-11-05 09:45:56'),
(11, '2025-02-0003', 'faculty', 3, 'Michael', NULL, 'Barredo', NULL, NULL, NULL, 'English', '2025-11-06', 1, 'barredomichael74@gmail.com', NULL, '0912312314', NULL, '$2y$12$y2a4k6sMoKMWE5E/2Ur38.AF5ZjWmDtjY.y2JDrzgmg829H1INtx2', 'active', 1, NULL, '2025-11-05 21:33:22', '2025-11-05 21:34:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admissions_applicant_id_unique` (`applicant_id`),
  ADD KEY `admissions_user_id_foreign` (`user_id`),
  ADD KEY `admissions_approved_by_foreign` (`approved_by`),
  ADD KEY `admissions_lrn_index` (`lrn`),
  ADD KEY `admissions_school_level_index` (`school_level`),
  ADD KEY `admissions_status_index` (`status`),
  ADD KEY `admissions_applicant_id_index` (`applicant_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_created_by_foreign` (`created_by`),
  ADD KEY `announcements_updated_by_foreign` (`updated_by`),
  ADD KEY `announcements_is_pinned_index` (`is_pinned`),
  ADD KEY `announcements_priority_index` (`priority`),
  ADD KEY `announcements_audience_index` (`audience`),
  ADD KEY `announcements_created_at_index` (`created_at`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_created_by_foreign` (`created_by`);

--
-- Indexes for table `faculty_positions`
--
ALTER TABLE `faculty_positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faculty_positions_code_unique` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jhs_admissions`
--
ALTER TABLE `jhs_admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jhs_admissions_applicant_id_unique` (`applicant_id`),
  ADD KEY `jhs_admissions_user_id_foreign` (`user_id`),
  ADD KEY `jhs_admissions_application_status_index` (`application_status`),
  ADD KEY `jhs_admissions_school_year_index` (`school_year`),
  ADD KEY `jhs_admissions_lrn_index` (`lrn`),
  ADD KEY `jhs_admissions_approved_by_foreign` (`approved_by`),
  ADD KEY `jhs_admissions_status_index` (`status`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_email_unique` (`email`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shs_admissions`
--
ALTER TABLE `shs_admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shs_admissions_applicant_id_unique` (`applicant_id`),
  ADD KEY `shs_admissions_user_id_foreign` (`user_id`),
  ADD KEY `shs_admissions_application_status_index` (`application_status`),
  ADD KEY `shs_admissions_school_year_index` (`school_year`),
  ADD KEY `shs_admissions_strand_index` (`strand`),
  ADD KEY `shs_admissions_lrn_index` (`lrn`),
  ADD KEY `shs_admissions_email_index` (`email`),
  ADD KEY `shs_admissions_approved_by_foreign` (`approved_by`),
  ADD KEY `shs_admissions_status_index` (`status`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subjects_subject_type_grade_level_index` (`subject_type`,`grade_level`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_custom_id_unique` (`custom_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_lrn_unique` (`lrn`),
  ADD KEY `users_account_type_grade_level_index` (`account_type`,`grade_level`),
  ADD KEY `users_faculty_position_id_foreign` (`faculty_position_id`),
  ADD KEY `users_assigned_by_foreign` (`assigned_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_positions`
--
ALTER TABLE `faculty_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jhs_admissions`
--
ALTER TABLE `jhs_admissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shs_admissions`
--
ALTER TABLE `shs_admissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `admissions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jhs_admissions`
--
ALTER TABLE `jhs_admissions`
  ADD CONSTRAINT `jhs_admissions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jhs_admissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `shs_admissions`
--
ALTER TABLE `shs_admissions`
  ADD CONSTRAINT `shs_admissions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shs_admissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_faculty_position_id_foreign` FOREIGN KEY (`faculty_position_id`) REFERENCES `faculty_positions` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
