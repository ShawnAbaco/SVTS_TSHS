-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2025 at 05:18 AM
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
-- Database: `svts`
--

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
(13, '0001_01_01_000000_create_users_table', 1),
(14, '0001_01_01_000001_create_cache_table', 1),
(15, '0001_01_01_000002_create_jobs_table', 1),
(16, '2025_08_19_100838_create_svts_tables', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `tbl_adviser`
--

CREATE TABLE `tbl_adviser` (
  `adviser_id` bigint(20) UNSIGNED NOT NULL,
  `adviser_fname` varchar(255) NOT NULL,
  `adviser_lname` varchar(255) NOT NULL,
  `adviser_sex` enum('male','female','other') DEFAULT NULL,
  `adviser_email` varchar(255) NOT NULL,
  `adviser_password` varchar(255) NOT NULL,
  `adviser_contactinfo` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `adviser_section` varchar(255) NOT NULL,
  `adviser_gradelevel` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_adviser`
--

INSERT INTO `tbl_adviser` (`adviser_id`, `adviser_fname`, `adviser_lname`, `adviser_sex`, `adviser_email`, `adviser_password`, `adviser_contactinfo`, `profile_image`, `adviser_section`, `adviser_gradelevel`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Kent', 'Flores', 'male', 'kentadviser@gmail.com', '$2y$12$knce1FTbjzWN6X4kiKqXaOzICronfLgaP5g09sFmDty4jQs0/YXMG', '09171234568', NULL, 'Eureka', '11', 'active', '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(2, 'Junald', 'Gonzaga', 'male', 'junaldadviser@gmail.com', '$2y$12$.LI5fiL89oQ02G.Sp90Q1O7Sf2CBTWXgKDl6cFZgNtbZpAMQGOaUC', '09281234569', NULL, 'Formidable', '12', 'active', '2025-10-04 19:17:30', '2025-10-04 19:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_complaints`
--

CREATE TABLE `tbl_complaints` (
  `complaints_id` bigint(20) UNSIGNED NOT NULL,
  `complainant_id` bigint(20) UNSIGNED NOT NULL,
  `respondent_id` bigint(20) UNSIGNED NOT NULL,
  `prefect_id` bigint(20) UNSIGNED NOT NULL,
  `offense_sanc_id` bigint(20) UNSIGNED NOT NULL,
  `complaints_incident` text NOT NULL,
  `complaints_date` date NOT NULL,
  `complaints_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_complaints`
--

INSERT INTO `tbl_complaints` (`complaints_id`, `complainant_id`, `respondent_id`, `prefect_id`, `offense_sanc_id`, `complaints_incident`, `complaints_date`, `complaints_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 2, 'Verbal argument in class', '2025-10-04', '12:03:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 5, 7, 1, 3, 'Cheating during exam', '2025-10-03', '12:06:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 9, 11, 1, 4, 'Minor dispute', '2025-10-02', '12:09:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 13, 15, 1, 5, 'Property damage', '2025-10-01', '12:12:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 2, 4, 1, 2, 'Verbal argument in class', '2025-10-04', '12:03:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 6, 8, 1, 3, 'Cheating during exam', '2025-10-03', '12:06:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 10, 12, 1, 4, 'Minor dispute', '2025-10-02', '12:09:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_complaints_anecdotal`
--

CREATE TABLE `tbl_complaints_anecdotal` (
  `comp_anec_id` bigint(20) UNSIGNED NOT NULL,
  `complaints_id` bigint(20) UNSIGNED NOT NULL,
  `comp_anec_solution` text NOT NULL,
  `comp_anec_recommendation` text NOT NULL,
  `comp_anec_date` date NOT NULL,
  `comp_anec_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_complaints_anecdotal`
--

INSERT INTO `tbl_complaints_anecdotal` (`comp_anec_id`, `complaints_id`, `comp_anec_solution`, `comp_anec_recommendation`, `comp_anec_date`, `comp_anec_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:03:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 2, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:06:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 3, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:09:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 4, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:12:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 5, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:03:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 6, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:06:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 7, 'Mediated student conflict', 'Observe interactions for 1 week', '2025-10-05', '15:09:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_complaints_appointment`
--

CREATE TABLE `tbl_complaints_appointment` (
  `comp_app_id` bigint(20) UNSIGNED NOT NULL,
  `complaints_id` bigint(20) UNSIGNED NOT NULL,
  `comp_app_date` date NOT NULL,
  `comp_app_time` time NOT NULL,
  `comp_app_status` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_complaints_appointment`
--

INSERT INTO `tbl_complaints_appointment` (`comp_app_id`, `complaints_id`, `comp_app_date`, `comp_app_time`, `comp_app_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-10-06', '14:03:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 2, '2025-10-07', '14:06:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 3, '2025-10-08', '14:09:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 4, '2025-10-09', '14:12:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 5, '2025-10-06', '14:03:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 6, '2025-10-07', '14:06:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 7, '2025-10-08', '14:09:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_offenses_with_sanction`
--

CREATE TABLE `tbl_offenses_with_sanction` (
  `offense_sanc_id` bigint(20) UNSIGNED NOT NULL,
  `offense_type` varchar(255) NOT NULL,
  `offense_description` text NOT NULL,
  `sanction_consequences` text NOT NULL,
  `group_number` int(11) NOT NULL DEFAULT 1,
  `stage_number` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_offenses_with_sanction`
--

INSERT INTO `tbl_offenses_with_sanction` (`offense_sanc_id`, `offense_type`, `offense_description`, `sanction_consequences`, `group_number`, `stage_number`, `created_at`, `updated_at`) VALUES
(1, 'Tardiness', 'Late arrival to class', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(2, 'Tardiness', 'Late arrival to class', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(3, 'Tardiness', 'Late arrival to class', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(4, 'Tardiness', 'Late arrival to class', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(5, 'Tardiness', 'Late arrival to class', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(6, 'Tardiness', 'Late arrival to class', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(7, 'Tardiness', 'Late arrival to class', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(8, 'Incomplete homework', 'Failure to submit assigned homework', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(9, 'Incomplete homework', 'Failure to submit assigned homework', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(10, 'Incomplete homework', 'Failure to submit assigned homework', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(11, 'Incomplete homework', 'Failure to submit assigned homework', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(12, 'Incomplete homework', 'Failure to submit assigned homework', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(13, 'Incomplete homework', 'Failure to submit assigned homework', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(14, 'Incomplete homework', 'Failure to submit assigned homework', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(15, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(16, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(17, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(18, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(19, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(20, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(21, 'Disruptive behavior', 'Behaviors that disrupt the learning environment', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(22, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(23, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(24, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(25, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(26, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(27, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(28, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(29, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(30, 'Bullying/harassment', 'Intimidation, teasing, or harassment of others', 'Expulsion', 7, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(31, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(32, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(33, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(34, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(35, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(36, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(37, 'Cheating/plagiarism', 'Unauthorized use of others\' work or ideas', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(38, 'Truancy', 'Unexcused absence from school', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(39, 'Truancy', 'Unexcused absence from school', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(40, 'Truancy', 'Unexcused absence from school', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(41, 'Truancy', 'Unexcused absence from school', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(42, 'Truancy', 'Unexcused absence from school', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(43, 'Truancy', 'Unexcused absence from school', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(44, 'Truancy', 'Unexcused absence from school', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(45, 'Substance abuse', 'Use or possession of prohibited substances', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(46, 'Substance abuse', 'Use or possession of prohibited substances', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(47, 'Substance abuse', 'Use or possession of prohibited substances', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(48, 'Substance abuse', 'Use or possession of prohibited substances', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(49, 'Substance abuse', 'Use or possession of prohibited substances', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(50, 'Substance abuse', 'Use or possession of prohibited substances', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(51, 'Substance abuse', 'Use or possession of prohibited substances', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(52, 'Substance abuse', 'Use or possession of prohibited substances', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(53, 'Substance abuse', 'Use or possession of prohibited substances', 'Expulsion', 7, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(54, 'Physical aggression', 'Physical harm or threat to others', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(55, 'Physical aggression', 'Physical harm or threat to others', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(56, 'Physical aggression', 'Physical harm or threat to others', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(57, 'Physical aggression', 'Physical harm or threat to others', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(58, 'Physical aggression', 'Physical harm or threat to others', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(59, 'Physical aggression', 'Physical harm or threat to others', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(60, 'Physical aggression', 'Physical harm or threat to others', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(61, 'Physical aggression', 'Physical harm or threat to others', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(62, 'Physical aggression', 'Physical harm or threat to others', 'Expulsion', 7, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(63, 'Theft', 'Stealing or unauthorized taking of property', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(64, 'Theft', 'Stealing or unauthorized taking of property', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(65, 'Theft', 'Stealing or unauthorized taking of property', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(66, 'Theft', 'Stealing or unauthorized taking of property', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(67, 'Theft', 'Stealing or unauthorized taking of property', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(68, 'Theft', 'Stealing or unauthorized taking of property', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(69, 'Theft', 'Stealing or unauthorized taking of property', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(70, 'Theft', 'Stealing or unauthorized taking of property', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(71, 'Theft', 'Stealing or unauthorized taking of property', 'Expulsion', 7, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(72, 'Vandalism', 'Willful destruction or damage to property', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(73, 'Vandalism', 'Willful destruction or damage to property', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(74, 'Vandalism', 'Willful destruction or damage to property', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(75, 'Vandalism', 'Willful destruction or damage to property', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(76, 'Vandalism', 'Willful destruction or damage to property', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(77, 'Vandalism', 'Willful destruction or damage to property', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(78, 'Vandalism', 'Willful destruction or damage to property', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(79, 'Vandalism', 'Willful destruction or damage to property', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(80, 'Vandalism', 'Willful destruction or damage to property', 'Expulsion', 7, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(81, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(82, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(83, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(84, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(85, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(86, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(87, 'Unauthorized technology use', 'Improper or unauthorized use of technology', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(88, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(89, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(90, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(91, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(92, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(93, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(94, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(95, 'Defiance/resisting authority', 'Refusal to follow instructions or obey authority', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(96, 'Dress code violation', 'Failure to comply with school dress code', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(97, 'Dress code violation', 'Failure to comply with school dress code', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(98, 'Dress code violation', 'Failure to comply with school dress code', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(99, 'Dress code violation', 'Failure to comply with school dress code', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(100, 'Dress code violation', 'Failure to comply with school dress code', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(101, 'Dress code violation', 'Failure to comply with school dress code', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(102, 'Dress code violation', 'Failure to comply with school dress code', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(103, 'Dress code violation', 'Failure to comply with school dress code', 'Suspension', 6, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(104, 'Dress code violation', 'Failure to comply with school dress code', 'Expulsion', 7, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(105, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(106, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(107, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(108, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(109, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(110, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(111, 'Academic dishonesty', 'Academic dishonesty or cheating', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(112, 'Disrespectful language', 'Rude or offensive language towards others', 'Verbal Warning', 1, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(113, 'Disrespectful language', 'Rude or offensive language towards others', 'Verbal Warning', 1, 2, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(114, 'Disrespectful language', 'Rude or offensive language towards others', 'Verbal Warning', 1, 3, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(115, 'Disrespectful language', 'Rude or offensive language towards others', 'Detention', 2, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(116, 'Disrespectful language', 'Rude or offensive language towards others', 'Parent Notification', 3, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(117, 'Disrespectful language', 'Rude or offensive language towards others', 'Restorative Action', 4, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(118, 'Disrespectful language', 'Rude or offensive language towards others', 'Counseling', 5, 1, '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(119, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(120, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(121, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(122, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(123, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(124, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(125, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(126, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(127, 'Forgery/falsification', 'Forging or falsifying documents or signatures', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(128, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(129, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(130, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(131, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(132, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(133, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(134, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(135, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(136, 'Cyberbullying', 'Bullying or harassment through electronic means', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(137, 'Gambling', 'Participating in games of chance or betting', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(138, 'Gambling', 'Participating in games of chance or betting', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(139, 'Gambling', 'Participating in games of chance or betting', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(140, 'Gambling', 'Participating in games of chance or betting', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(141, 'Gambling', 'Participating in games of chance or betting', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(142, 'Gambling', 'Participating in games of chance or betting', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(143, 'Gambling', 'Participating in games of chance or betting', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(144, 'Gambling', 'Participating in games of chance or betting', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(145, 'Gambling', 'Participating in games of chance or betting', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(146, 'Destruction of property', 'Deliberate damage to school or personal property', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(147, 'Destruction of property', 'Deliberate damage to school or personal property', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(148, 'Destruction of property', 'Deliberate damage to school or personal property', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(149, 'Destruction of property', 'Deliberate damage to school or personal property', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(150, 'Destruction of property', 'Deliberate damage to school or personal property', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(151, 'Destruction of property', 'Deliberate damage to school or personal property', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(152, 'Destruction of property', 'Deliberate damage to school or personal property', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(153, 'Destruction of property', 'Deliberate damage to school or personal property', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(154, 'Destruction of property', 'Deliberate damage to school or personal property', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(155, 'Hate speech', 'Offensive language targeting specific groups', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(156, 'Hate speech', 'Offensive language targeting specific groups', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(157, 'Hate speech', 'Offensive language targeting specific groups', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(158, 'Hate speech', 'Offensive language targeting specific groups', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(159, 'Hate speech', 'Offensive language targeting specific groups', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(160, 'Hate speech', 'Offensive language targeting specific groups', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(161, 'Hate speech', 'Offensive language targeting specific groups', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(162, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(163, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(164, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(165, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(166, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(167, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(168, 'Excessive noise', 'Disruptive noise levels that interfere with learning', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(169, 'Skipping class', 'Unauthorized absence from class or school', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(170, 'Skipping class', 'Unauthorized absence from class or school', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(171, 'Skipping class', 'Unauthorized absence from class or school', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(172, 'Skipping class', 'Unauthorized absence from class or school', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(173, 'Skipping class', 'Unauthorized absence from class or school', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(174, 'Skipping class', 'Unauthorized absence from class or school', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(175, 'Skipping class', 'Unauthorized absence from class or school', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(176, 'Academic misconduct', 'Violation of academic integrity', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(177, 'Academic misconduct', 'Violation of academic integrity', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(178, 'Academic misconduct', 'Violation of academic integrity', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(179, 'Academic misconduct', 'Violation of academic integrity', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(180, 'Academic misconduct', 'Violation of academic integrity', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(181, 'Academic misconduct', 'Violation of academic integrity', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(182, 'Academic misconduct', 'Violation of academic integrity', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(183, 'Verbal harassment', 'Harassment through spoken words', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(184, 'Verbal harassment', 'Harassment through spoken words', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(185, 'Verbal harassment', 'Harassment through spoken words', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(186, 'Verbal harassment', 'Harassment through spoken words', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(187, 'Verbal harassment', 'Harassment through spoken words', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(188, 'Verbal harassment', 'Harassment through spoken words', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(189, 'Verbal harassment', 'Harassment through spoken words', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(190, 'Verbal harassment', 'Harassment through spoken words', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(191, 'Verbal harassment', 'Harassment through spoken words', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(192, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(193, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(194, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(195, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(196, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(197, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(198, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(199, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(200, 'Plagiarism', 'Using someone else\'s work without proper attribution', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(201, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(202, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(203, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(204, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(205, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(206, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(207, 'Inappropriate use of social media', 'Misuse or violation of social media guidelines', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(208, 'Littering', 'Improper disposal of waste materials', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(209, 'Littering', 'Improper disposal of waste materials', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(210, 'Littering', 'Improper disposal of waste materials', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(211, 'Littering', 'Improper disposal of waste materials', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(212, 'Littering', 'Improper disposal of waste materials', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(213, 'Littering', 'Improper disposal of waste materials', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(214, 'Littering', 'Improper disposal of waste materials', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(215, 'Skipping school', 'Unexcused absence from entire school day', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(216, 'Skipping school', 'Unexcused absence from entire school day', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(217, 'Skipping school', 'Unexcused absence from entire school day', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(218, 'Skipping school', 'Unexcused absence from entire school day', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(219, 'Skipping school', 'Unexcused absence from entire school day', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(220, 'Skipping school', 'Unexcused absence from entire school day', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(221, 'Skipping school', 'Unexcused absence from entire school day', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(222, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(223, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(224, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(225, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(226, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(227, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(228, 'Forgery/faking signatures', 'Forging or faking signatures on documents', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(229, 'Discrimination', 'Unfair treatment based on characteristics', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(230, 'Discrimination', 'Unfair treatment based on characteristics', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(231, 'Discrimination', 'Unfair treatment based on characteristics', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(232, 'Discrimination', 'Unfair treatment based on characteristics', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(233, 'Discrimination', 'Unfair treatment based on characteristics', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(234, 'Discrimination', 'Unfair treatment based on characteristics', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(235, 'Discrimination', 'Unfair treatment based on characteristics', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(236, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(237, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(238, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(239, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(240, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(241, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(242, 'Unauthorized use of school equipment', 'Improper or unauthorized use of school equipment', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(243, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(244, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(245, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(246, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(247, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(248, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(249, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(250, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(251, 'Inappropriate physical contact', 'Unwanted or inappropriate physical contact', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(252, 'Unauthorized materials', 'Possession or use of prohibited items', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(253, 'Unauthorized materials', 'Possession or use of prohibited items', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(254, 'Unauthorized materials', 'Possession or use of prohibited items', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(255, 'Unauthorized materials', 'Possession or use of prohibited items', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(256, 'Unauthorized materials', 'Possession or use of prohibited items', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(257, 'Unauthorized materials', 'Possession or use of prohibited items', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(258, 'Unauthorized materials', 'Possession or use of prohibited items', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(259, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(260, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(261, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(262, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(263, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(264, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(265, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(266, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(267, 'Threats or intimidation', 'Expressing intent to harm or intimidate others', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(268, 'Use of profanity', 'Use of offensive or vulgar language', 'Verbal Warning', 1, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(269, 'Use of profanity', 'Use of offensive or vulgar language', 'Verbal Warning', 1, 2, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(270, 'Use of profanity', 'Use of offensive or vulgar language', 'Verbal Warning', 1, 3, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(271, 'Use of profanity', 'Use of offensive or vulgar language', 'Detention', 2, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(272, 'Use of profanity', 'Use of offensive or vulgar language', 'Parent Notification', 3, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(273, 'Use of profanity', 'Use of offensive or vulgar language', 'Restorative Action', 4, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(274, 'Use of profanity', 'Use of offensive or vulgar language', 'Counseling', 5, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(275, 'Use of profanity', 'Use of offensive or vulgar language', 'Suspension', 6, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(276, 'Use of profanity', 'Use of offensive or vulgar language', 'Expulsion', 7, 1, '2025-10-04 19:17:30', '2025-10-04 19:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_parent`
--

CREATE TABLE `tbl_parent` (
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `parent_fname` varchar(255) NOT NULL,
  `parent_lname` varchar(255) NOT NULL,
  `parent_sex` enum('male','female','other') DEFAULT NULL,
  `parent_birthdate` date NOT NULL,
  `parent_email` varchar(255) DEFAULT NULL,
  `parent_contactinfo` varchar(255) NOT NULL,
  `parent_relationship` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_parent`
--

INSERT INTO `tbl_parent` (`parent_id`, `parent_fname`, `parent_lname`, `parent_sex`, `parent_birthdate`, `parent_email`, `parent_contactinfo`, `parent_relationship`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Juan', 'Dela Cruz', 'male', '1975-03-12', 'juan.delacruz@gmail.com', '09171234567', 'Father', 'active', '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(2, 'Maria', 'Santos', 'female', '1980-07-22', 'maria.santos@gmail.com', '09281234567', 'Mother', 'active', '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(3, 'Antonio', 'Reyes', 'male', '1978-11-05', 'antonio.reyes@gmail.com', '09391234567', 'Father', 'active', '2025-10-04 19:17:30', '2025-10-04 19:17:30'),
(4, 'Sofia', 'Cruz', 'female', '1982-02-15', 'sofia.cruz@gmail.com', '09451234567', 'Mother', 'active', '2025-10-04 19:17:30', '2025-10-04 19:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_prefect_of_discipline`
--

CREATE TABLE `tbl_prefect_of_discipline` (
  `prefect_id` bigint(20) UNSIGNED NOT NULL,
  `prefect_fname` varchar(255) NOT NULL,
  `prefect_lname` varchar(255) NOT NULL,
  `prefect_sex` enum('male','female','other') DEFAULT NULL,
  `prefect_email` varchar(255) NOT NULL,
  `prefect_password` varchar(255) NOT NULL,
  `prefect_contactinfo` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_prefect_of_discipline`
--

INSERT INTO `tbl_prefect_of_discipline` (`prefect_id`, `prefect_fname`, `prefect_lname`, `prefect_sex`, `prefect_email`, `prefect_password`, `prefect_contactinfo`, `profile_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Shawn', 'Abaco', 'male', 'tshssvts@gmail.com', '$2y$12$tZ1n0USe2JM17ZNWsjLhbOS4DUvElG1sgGafFdB8Rkd3dBj8ueB3i', '09171234567', NULL, 'active', '2025-10-04 19:17:29', '2025-10-04 19:17:29'),
(2, 'Kent Zyrone', 'Flores', 'male', 'k.zyroneflores@gmail.com', '$2y$12$9DCoTopI7Ja7ysjvMyAKGOwzS14tDWY/0OmgQ2rgVjn6I4MEY1mj2', '09093246917', NULL, 'active', '2025-10-04 19:17:29', '2025-10-04 19:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student`
--

CREATE TABLE `tbl_student` (
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `adviser_id` bigint(20) UNSIGNED NOT NULL,
  `student_fname` varchar(255) NOT NULL,
  `student_lname` varchar(255) NOT NULL,
  `student_sex` enum('male','female','other') DEFAULT NULL,
  `student_birthdate` date NOT NULL,
  `student_address` varchar(255) NOT NULL,
  `student_contactinfo` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_student`
--

INSERT INTO `tbl_student` (`student_id`, `parent_id`, `adviser_id`, `student_fname`, `student_lname`, `student_sex`, `student_birthdate`, `student_address`, `student_contactinfo`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Miguel', 'Garcia', 'male', '2007-01-10', 'Brgy. 1, Tagoloan, Misamis Oriental', '09170001000', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 2, 2, 'Angelica', 'Lopez', 'female', '2006-03-15', 'Brgy. 2, Tagoloan, Misamis Oriental', '09170001001', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 3, 1, 'Joshua', 'Reyes', 'male', '2007-05-22', 'Brgy. 3, Tagoloan, Misamis Oriental', '09170001002', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 4, 2, 'Samantha', 'Cruz', 'female', '2006-07-30', 'Brgy. 4, Tagoloan, Misamis Oriental', '09170001003', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 1, 1, 'Mark', 'Dela Rosa', 'male', '2007-02-12', 'Brgy. 5, Tagoloan, Misamis Oriental', '09170001004', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 2, 2, 'Nicole', 'Santos', 'female', '2006-09-05', 'Brgy. 6, Tagoloan, Misamis Oriental', '09170001005', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 3, 1, 'Ryan', 'Torres', 'male', '2007-08-18', 'Brgy. 7, Tagoloan, Misamis Oriental', '09170001006', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(8, 4, 2, 'Isabella', 'Velasco', 'female', '2006-11-12', 'Brgy. 8, Tagoloan, Misamis Oriental', '09170001007', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(9, 1, 1, 'Daniel', 'Ramos', 'male', '2007-04-25', 'Brgy. 9, Tagoloan, Misamis Oriental', '09170001008', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(10, 2, 2, 'Stephanie', 'Gonzales', 'female', '2006-06-17', 'Brgy. 10, Tagoloan, Misamis Oriental', '09170001009', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(11, 3, 1, 'Kevin', 'Diaz', 'male', '2007-03-09', 'Brgy. 11, Tagoloan, Misamis Oriental', '09170001010', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(12, 4, 2, 'Jessica', 'Mendoza', 'female', '2006-12-02', 'Brgy. 12, Tagoloan, Misamis Oriental', '09170001011', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(13, 1, 1, 'Christian', 'Navarro', 'male', '2007-10-14', 'Brgy. 13, Tagoloan, Misamis Oriental', '09170001012', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(14, 2, 2, 'Alexa', 'Villanueva', 'female', '2006-05-23', 'Brgy. 14, Tagoloan, Misamis Oriental', '09170001013', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(15, 3, 1, 'Patrick', 'Flores', 'male', '2007-07-01', 'Brgy. 15, Tagoloan, Misamis Oriental', '09170001014', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_violation_anecdotal`
--

CREATE TABLE `tbl_violation_anecdotal` (
  `violation_anec_id` bigint(20) UNSIGNED NOT NULL,
  `violation_id` bigint(20) UNSIGNED NOT NULL,
  `violation_anec_solution` text NOT NULL,
  `violation_anec_recommendation` text NOT NULL,
  `violation_anec_date` date NOT NULL,
  `violation_anec_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_violation_anecdotal`
--

INSERT INTO `tbl_violation_anecdotal` (`violation_anec_id`, `violation_id`, `violation_anec_solution`, `violation_anec_recommendation`, `violation_anec_date`, `violation_anec_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:02:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 2, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:04:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 3, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:06:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 4, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:08:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 5, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:10:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 6, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:12:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 7, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:14:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(8, 8, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:16:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(9, 9, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:18:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(10, 10, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:20:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(11, 11, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:22:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(12, 12, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:24:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(13, 13, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:26:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(14, 14, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:28:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(15, 15, 'Counseled student regarding incident', 'Monitor for 1 week', '2025-10-05', '09:30:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_violation_appointment`
--

CREATE TABLE `tbl_violation_appointment` (
  `violation_app_id` bigint(20) UNSIGNED NOT NULL,
  `violation_id` bigint(20) UNSIGNED NOT NULL,
  `violation_app_date` date NOT NULL,
  `violation_app_time` time NOT NULL,
  `violation_app_status` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_violation_appointment`
--

INSERT INTO `tbl_violation_appointment` (`violation_app_id`, `violation_id`, `violation_app_date`, `violation_app_time`, `violation_app_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-10-06', '10:02:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 2, '2025-10-07', '10:04:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 3, '2025-10-08', '10:06:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 4, '2025-10-09', '10:08:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 5, '2025-10-05', '10:10:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 6, '2025-10-06', '10:12:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 7, '2025-10-07', '10:14:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(8, 8, '2025-10-08', '10:16:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(9, 9, '2025-10-09', '10:18:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(10, 10, '2025-10-05', '10:20:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(11, 11, '2025-10-06', '10:22:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(12, 12, '2025-10-07', '10:24:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(13, 13, '2025-10-08', '10:26:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(14, 14, '2025-10-09', '10:28:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(15, 15, '2025-10-05', '10:30:00', 'Scheduled', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_violation_record`
--

CREATE TABLE `tbl_violation_record` (
  `violation_id` bigint(20) UNSIGNED NOT NULL,
  `violator_id` bigint(20) UNSIGNED NOT NULL,
  `prefect_id` bigint(20) UNSIGNED NOT NULL,
  `offense_sanc_id` bigint(20) UNSIGNED NOT NULL,
  `violation_incident` text NOT NULL,
  `violation_date` date NOT NULL,
  `violation_time` time NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_violation_record`
--

INSERT INTO `tbl_violation_record` (`violation_id`, `violator_id`, `prefect_id`, `offense_sanc_id`, `violation_incident`, `violation_date`, `violation_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 'Improper uniform', '2025-10-04', '08:03:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(2, 3, 1, 3, 'Disruptive behavior', '2025-10-03', '08:06:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(3, 5, 1, 4, 'Late submission of homework', '2025-10-02', '08:09:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(4, 7, 1, 5, 'Unprepared for class', '2025-10-01', '08:12:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(5, 9, 1, 6, 'Late to class', '2025-09-30', '08:15:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(6, 11, 1, 7, 'Improper uniform', '2025-09-29', '08:18:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(7, 13, 1, 8, 'Disruptive behavior', '2025-09-28', '08:21:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(8, 15, 1, 9, 'Late submission of homework', '2025-09-27', '08:24:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(9, 2, 1, 10, 'Unprepared for class', '2025-09-26', '08:27:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(10, 4, 1, 1, 'Late to class', '2025-09-25', '08:30:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(11, 6, 1, 2, 'Improper uniform', '2025-09-24', '08:33:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(12, 8, 1, 3, 'Disruptive behavior', '2025-09-23', '08:36:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(13, 10, 1, 4, 'Late submission of homework', '2025-09-22', '08:39:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(14, 12, 1, 5, 'Unprepared for class', '2025-09-21', '08:42:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31'),
(15, 14, 1, 6, 'Late to class', '2025-09-20', '08:45:00', 'active', '2025-10-04 19:17:31', '2025-10-04 19:17:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `tbl_adviser`
--
ALTER TABLE `tbl_adviser`
  ADD PRIMARY KEY (`adviser_id`);

--
-- Indexes for table `tbl_complaints`
--
ALTER TABLE `tbl_complaints`
  ADD PRIMARY KEY (`complaints_id`),
  ADD KEY `tbl_complaints_complainant_id_foreign` (`complainant_id`),
  ADD KEY `tbl_complaints_respondent_id_foreign` (`respondent_id`),
  ADD KEY `tbl_complaints_prefect_id_foreign` (`prefect_id`),
  ADD KEY `tbl_complaints_offense_sanc_id_foreign` (`offense_sanc_id`);

--
-- Indexes for table `tbl_complaints_anecdotal`
--
ALTER TABLE `tbl_complaints_anecdotal`
  ADD PRIMARY KEY (`comp_anec_id`),
  ADD KEY `tbl_complaints_anecdotal_complaints_id_foreign` (`complaints_id`);

--
-- Indexes for table `tbl_complaints_appointment`
--
ALTER TABLE `tbl_complaints_appointment`
  ADD PRIMARY KEY (`comp_app_id`),
  ADD KEY `tbl_complaints_appointment_complaints_id_foreign` (`complaints_id`);

--
-- Indexes for table `tbl_offenses_with_sanction`
--
ALTER TABLE `tbl_offenses_with_sanction`
  ADD PRIMARY KEY (`offense_sanc_id`);

--
-- Indexes for table `tbl_parent`
--
ALTER TABLE `tbl_parent`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `tbl_prefect_of_discipline`
--
ALTER TABLE `tbl_prefect_of_discipline`
  ADD PRIMARY KEY (`prefect_id`);

--
-- Indexes for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `tbl_student_parent_id_foreign` (`parent_id`),
  ADD KEY `tbl_student_adviser_id_foreign` (`adviser_id`);

--
-- Indexes for table `tbl_violation_anecdotal`
--
ALTER TABLE `tbl_violation_anecdotal`
  ADD PRIMARY KEY (`violation_anec_id`),
  ADD KEY `tbl_violation_anecdotal_violation_id_foreign` (`violation_id`);

--
-- Indexes for table `tbl_violation_appointment`
--
ALTER TABLE `tbl_violation_appointment`
  ADD PRIMARY KEY (`violation_app_id`),
  ADD KEY `tbl_violation_appointment_violation_id_foreign` (`violation_id`);

--
-- Indexes for table `tbl_violation_record`
--
ALTER TABLE `tbl_violation_record`
  ADD PRIMARY KEY (`violation_id`),
  ADD KEY `tbl_violation_record_violator_id_foreign` (`violator_id`),
  ADD KEY `tbl_violation_record_prefect_id_foreign` (`prefect_id`),
  ADD KEY `tbl_violation_record_offense_sanc_id_foreign` (`offense_sanc_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_adviser`
--
ALTER TABLE `tbl_adviser`
  MODIFY `adviser_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_complaints`
--
ALTER TABLE `tbl_complaints`
  MODIFY `complaints_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_complaints_anecdotal`
--
ALTER TABLE `tbl_complaints_anecdotal`
  MODIFY `comp_anec_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_complaints_appointment`
--
ALTER TABLE `tbl_complaints_appointment`
  MODIFY `comp_app_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_offenses_with_sanction`
--
ALTER TABLE `tbl_offenses_with_sanction`
  MODIFY `offense_sanc_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=277;

--
-- AUTO_INCREMENT for table `tbl_parent`
--
ALTER TABLE `tbl_parent`
  MODIFY `parent_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_prefect_of_discipline`
--
ALTER TABLE `tbl_prefect_of_discipline`
  MODIFY `prefect_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_student`
--
ALTER TABLE `tbl_student`
  MODIFY `student_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_violation_anecdotal`
--
ALTER TABLE `tbl_violation_anecdotal`
  MODIFY `violation_anec_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_violation_appointment`
--
ALTER TABLE `tbl_violation_appointment`
  MODIFY `violation_app_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_violation_record`
--
ALTER TABLE `tbl_violation_record`
  MODIFY `violation_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_complaints`
--
ALTER TABLE `tbl_complaints`
  ADD CONSTRAINT `tbl_complaints_complainant_id_foreign` FOREIGN KEY (`complainant_id`) REFERENCES `tbl_student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_complaints_offense_sanc_id_foreign` FOREIGN KEY (`offense_sanc_id`) REFERENCES `tbl_offenses_with_sanction` (`offense_sanc_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_complaints_prefect_id_foreign` FOREIGN KEY (`prefect_id`) REFERENCES `tbl_prefect_of_discipline` (`prefect_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_complaints_respondent_id_foreign` FOREIGN KEY (`respondent_id`) REFERENCES `tbl_student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_complaints_anecdotal`
--
ALTER TABLE `tbl_complaints_anecdotal`
  ADD CONSTRAINT `tbl_complaints_anecdotal_complaints_id_foreign` FOREIGN KEY (`complaints_id`) REFERENCES `tbl_complaints` (`complaints_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_complaints_appointment`
--
ALTER TABLE `tbl_complaints_appointment`
  ADD CONSTRAINT `tbl_complaints_appointment_complaints_id_foreign` FOREIGN KEY (`complaints_id`) REFERENCES `tbl_complaints` (`complaints_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD CONSTRAINT `tbl_student_adviser_id_foreign` FOREIGN KEY (`adviser_id`) REFERENCES `tbl_adviser` (`adviser_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_student_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tbl_parent` (`parent_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_violation_anecdotal`
--
ALTER TABLE `tbl_violation_anecdotal`
  ADD CONSTRAINT `tbl_violation_anecdotal_violation_id_foreign` FOREIGN KEY (`violation_id`) REFERENCES `tbl_violation_record` (`violation_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_violation_appointment`
--
ALTER TABLE `tbl_violation_appointment`
  ADD CONSTRAINT `tbl_violation_appointment_violation_id_foreign` FOREIGN KEY (`violation_id`) REFERENCES `tbl_violation_record` (`violation_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_violation_record`
--
ALTER TABLE `tbl_violation_record`
  ADD CONSTRAINT `tbl_violation_record_offense_sanc_id_foreign` FOREIGN KEY (`offense_sanc_id`) REFERENCES `tbl_offenses_with_sanction` (`offense_sanc_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_violation_record_prefect_id_foreign` FOREIGN KEY (`prefect_id`) REFERENCES `tbl_prefect_of_discipline` (`prefect_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_violation_record_violator_id_foreign` FOREIGN KEY (`violator_id`) REFERENCES `tbl_student` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
