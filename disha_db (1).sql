-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 11:05 AM
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
-- Database: `disha_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_type` enum('online','offline') NOT NULL,
  `client_type` enum('new_client','existing_client') NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_phone` varchar(255) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','online_payment') NOT NULL,
  `remarks` text DEFAULT NULL,
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Booked,2=Completed,3=Cancelled',
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_collected_method` varchar(255) NOT NULL DEFAULT 'cash',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_type`, `client_type`, `client_id`, `client_name`, `client_phone`, `appointment_date`, `appointment_time`, `fee_amount`, `payment_method`, `remarks`, `added_by`, `status`, `payment_status`, `payment_collected_method`, `created_at`, `updated_at`) VALUES
(34, 'online', 'existing_client', 21, 'Asif', '6304203030', '2026-06-01', '10:02:00', 1200.00, 'cash', 'ssssss', 2, 1, 'pending', 'cash', '2026-06-01 07:52:21', '2026-06-01 07:52:21'),
(35, 'online', 'existing_client', 17, 'Todaytest', '9863251470', '2026-06-01', '03:03:00', 4000.00, 'cash', 'dsdsd', 2, 1, 'pending', 'cash', '2026-06-01 07:52:54', '2026-06-01 07:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `punch_in` datetime DEFAULT NULL,
  `punch_out` datetime DEFAULT NULL,
  `punch_in_latitude` decimal(10,7) DEFAULT NULL,
  `punch_in_longitude` decimal(10,7) DEFAULT NULL,
  `punch_out_latitude` decimal(10,7) DEFAULT NULL,
  `punch_out_longitude` decimal(10,7) DEFAULT NULL,
  `punch_in_address` text DEFAULT NULL,
  `punch_out_address` text DEFAULT NULL,
  `total_minutes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `user_id`, `attendance_date`, `punch_in`, `punch_out`, `punch_in_latitude`, `punch_in_longitude`, `punch_out_latitude`, `punch_out_longitude`, `punch_in_address`, `punch_out_address`, `total_minutes`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-05-21', '2026-05-21 21:01:41', '2026-05-21 23:39:29', 28.6155857, 77.2057991, 28.6105197, 77.2087746, 'New Delhi, India', 'New Delhi, India', 158, '2026-05-21 15:31:41', '2026-05-21 18:09:29'),
(2, 1, '2026-05-21', '2026-05-21 21:39:00', '2026-05-21 21:39:02', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-05-21 16:09:00', '2026-05-21 16:09:02'),
(3, 3, '2026-05-21', '2026-05-21 21:39:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-05-21 16:09:20', '2026-05-21 16:09:20'),
(4, 2, '2026-05-22', '2026-05-22 11:00:52', '2026-05-22 14:59:18', 28.6152601, 77.2125692, 28.6181772, 77.2089207, 'New Delhi, India', 'New Delhi, India', 238, '2026-05-22 05:30:52', '2026-05-22 09:29:18'),
(5, 1, '2026-05-22', '2026-05-22 12:10:28', '2026-05-22 12:10:34', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-05-22 06:40:28', '2026-05-22 06:40:34'),
(6, 2, '2026-05-23', '2026-05-23 17:46:00', '2026-05-23 17:46:03', 28.6120576, 77.2068929, 28.6117492, 77.2086609, 'New Delhi, India', 'New Delhi, India', 0, '2026-05-23 12:16:00', '2026-05-23 12:16:03'),
(7, 4, '2026-05-23', '2026-05-23 20:04:44', '2026-05-23 20:05:04', 17.2833994, 78.4500079, 17.2833933, 78.4500100, '7FM2+92G, Hyderabad, Telangana, India', '7FM2+92G, Hyderabad, Telangana, India', 0, '2026-05-23 14:34:44', '2026-05-23 14:35:04'),
(8, 2, '2026-05-24', '2026-05-24 06:39:12', '2026-05-24 06:39:42', 17.5094969, 78.4569518, 17.5094642, 78.4569476, '7/165, Hyderabad, Telangana, India', '7/165, Hyderabad, Telangana, India', 1, '2026-05-24 01:09:12', '2026-05-24 01:09:42'),
(9, 5, '2026-05-25', '2026-05-25 16:12:30', '2026-05-25 16:12:31', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-05-25 10:42:30', '2026-05-25 10:42:31');

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
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `call_type` enum('incoming','outgoing') NOT NULL,
  `lead_type` enum('cold','hot','warm') NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `referance` varchar(255) DEFAULT NULL,
  `case_type` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Active,0=Inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `call_type`, `lead_type`, `fullname`, `phone`, `location`, `referance`, `case_type`, `remarks`, `added_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'incoming', 'hot', 'Test', '9632587410', 'Test', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 15:40:32', '2026-05-21 15:40:32'),
(2, 'outgoing', 'warm', 'mani', '9632587411', 'Test', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 16:05:47', '2026-05-21 16:05:47'),
(3, 'outgoing', 'warm', 'mani', '9876543210', 'Test', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 16:06:19', '2026-05-21 16:06:19'),
(4, 'incoming', 'warm', 'Mani', '9631478520', 'Hyd', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 16:08:38', '2026-05-21 16:08:38'),
(5, 'incoming', 'warm', 'Asif', '9440161007', 'Nellore', 'Sample', 'Civil', 'Civil case discussions', 3, 1, '2026-05-21 16:10:03', '2026-05-21 16:10:03'),
(6, 'incoming', 'warm', 'Mani', '9866755888', 'Hyd', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 16:11:12', '2026-05-21 16:11:12'),
(7, 'incoming', 'warm', 'Mani', '9866755889', 'Hyd', 'Test', 'Test', NULL, 2, 1, '2026-05-21 16:41:56', '2026-05-21 16:41:56'),
(8, 'incoming', 'warm', 'Mani', '9866755832', 'Hyd', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 16:49:08', '2026-05-21 16:49:08'),
(9, 'incoming', 'warm', 'Testtinh', '9638521478', 'Hyd', 'Test', 'Test', 'Test', 2, 1, '2026-05-21 17:41:09', '2026-05-21 17:41:09'),
(10, 'incoming', 'warm', 'Testtinh', '9638527418', 'Hyd', 'Test', 'Test', 'Hello', 2, 1, '2026-05-21 18:08:41', '2026-05-21 18:08:41'),
(11, 'incoming', 'warm', 'Manikanta', '9630741852', 'Hyd', 'No', 'Test', 'Twst', 2, 1, '2026-05-21 18:20:16', '2026-05-21 18:20:16'),
(12, 'incoming', 'cold', 'Manikanta', '9876543120', 'Hyd', 'No.', 'Test', 'Test', 2, 1, '2026-05-21 18:28:23', '2026-05-21 18:28:23'),
(13, 'outgoing', 'cold', 'Rahul Test', '9630852741', 'Hyd', 'Test', 'Civil', 'Test', 2, 1, '2026-05-22 05:40:30', '2026-05-22 05:40:30'),
(14, 'incoming', 'warm', 'Asif', '9440161009', 'Hyderabad', 'Google', 'Criminal', 'Na', 2, 1, '2026-05-22 09:30:26', '2026-05-22 09:30:26'),
(16, 'incoming', 'warm', 'Asif', '9440161000', 'Hyderabad', 'Tet', 'Criminal', 'Test', 2, 1, '2026-05-22 10:44:19', '2026-05-22 10:44:19'),
(17, 'incoming', 'cold', 'Todaytest', '9863251470', 'Hyderabad', 'No.', 'Testing', 'Test', 2, 1, '2026-05-23 10:27:49', '2026-05-23 10:27:49'),
(21, 'incoming', 'warm', 'Asif', '6304203030', 'Asif', 'aa', 'aaa', 'aaa', 2, 1, '2026-05-31 18:49:49', '2026-05-31 18:49:49');

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
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `followup_date` date NOT NULL,
  `remarks` text NOT NULL,
  `followp_time` varchar(255) DEFAULT NULL,
  `followp_type` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Pending,1=Completed',
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followups`
--

INSERT INTO `followups` (`id`, `client_id`, `appointment_id`, `followup_date`, `remarks`, `followp_time`, `followp_type`, `status`, `added_by`, `created_at`, `updated_at`) VALUES
(11, 21, NULL, '2026-06-01', 'Call', '12:30', 'Call', 0, 2, '2026-06-01 08:14:09', '2026-06-01 08:14:09'),
(12, 21, NULL, '2026-06-02', 'Call', '10:20', 'Call', 0, 2, '2026-06-01 08:15:06', '2026-06-01 08:15:06'),
(13, 21, NULL, '2026-06-03', 'Call', '10:03', 'Call', 0, 2, '2026-06-01 08:17:46', '2026-06-01 08:17:46');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_20_115803_create_personal_access_tokens_table', 1),
(5, '2026_01_20_124318_create_password_resets_table', 1),
(6, '2026_05_20_150647_add_added_by_to_users_table', 1),
(7, '2026_05_20_151809_add_added_by_to_users_table', 1),
(8, '2026_05_20_165835_create_attendances_table', 1),
(9, '2026_05_20_174436_create_clients_table', 1),
(10, '2026_05_20_180546_create_appointments_table', 1),
(11, '2026_05_20_210845_create_followups_table', 1),
(12, '2026_05_21_154351_add_payment_status_to_appointments', 2),
(13, '2026_05_21_162934_add_payment_status_to_appointments', 2),
(14, '2026_05_31_001237_add_indexes_to_clients_table', 3),
(15, '2026_06_01_132614_add_fields_folowup', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
('0DZ1F234fGJzklzZrLuJUrPj2PLPkR6Wdo80JZXG', NULL, '152.57.236.91', 'WhatsApp/2.23.20.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoia1dEWjUzOWtwYU5BMGNCY25LV1FxMFIwdG9XT253ZldwUms4SktvMyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779292978),
('2ooFvdLzy7pT9zdMKYQJfM3t3WxrbQYEMUF4JiYi', NULL, '157.51.128.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiY2J6SHF0TEoyWEtSeEpIYUdOSDFvT0h3Ulhka2d6cHVqOHRFZGt2byI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779377058),
('420HSB0uAZjoYi6VztOPSThzSL7J3h7enDdfrear', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUVBwUnpPeFJubHpWWmE2YURYYVcwMmI5RFdFcGZoMm1JWjRVMFhMcyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780249888),
('6Dz8hxZZrzxyUrSVcpfxav1u8w4xVBzcaoiGDqLe', NULL, '157.51.147.179', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiMWhQZ3FOSUxYVWpwblEySWw0cW15VWNDeldCbmc1c3VBbzJYUkF2QyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779295347),
('6WGKn3qLQd8VxfjVCyhZEdbfpSn7XhuzC8NEvt9y', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiQllpczJBdkVuMU5hVWtXc3dsV3doRnpvdGV5UVNSdEdwUWpwYlM3UCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780292938),
('CqVUXmeXGYpvfDBV18WCSYYIQUcjb0PjfdXwh1vh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUDhuSXExY09nMmhIRVE5ZGFzQ3daSHJOdXFibng4MGZSZXJMUE1iWiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780292932),
('dAjtOD6odVo2ZlqrcTzUb6NoVTD5KsQjVJnVw0Iz', NULL, '152.57.236.91', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoieDNpeW5leTZrV1VzblJQcGRPamJjZ3gzRmpweENodTFJbWlVeDRtcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779297559),
('F6448mdUTcrdKVycUTMdGtZPmEbijAI5C7rZ3mim', NULL, '152.57.172.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNVlPcHl1TjVaWkFwWFFBRVB2eDhKTkVybEpaaEZveWZ2aFRITFdrdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vZGlzaGEtYXBpLmFqdGVjaHNvbHV0aW9uLmluL3B1YmxpYyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779379531),
('HszZpXUZ3UCp7YmLanYhtxEdsvfE0gQYxh09d6dQ', NULL, '152.59.205.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWUZiVklXOHVaOVJJMko2Q1RIY2JTYXEyRlRKV2daMDhxejIwcnR3eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vZGlzaGEtYXBpLmFqdGVjaHNvbHV0aW9uLmluL3B1YmxpYyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779447381),
('Kz8aawFnyqvpYCJiXyQzxDpOCzXUt9YbRZ9JSucn', NULL, '27.4.118.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUElJR3NHQmplOXFnMzN1eFUwR0tvUGNXSGw1YVBydWg4cGNsNkpEMiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779335510),
('lZk30H2HmCpMSFmTQOheqQGW6fRptoCXCJDP4nHz', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiQ1RwbUY4S0xaVFB1Mjh0QktDdjRkTndqMjJwaDdSaGdkbkxzODNudiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780299472),
('NQTPi5NqgcdqCoec2si9UvTGSLjEnjmjqMF50zJZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQlFvT01oWWQ1S3hDbU50Q0wxcUd4cnBZcVpuc3o2VUdhbmRjN1E0OCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780216982),
('pj0LtrOouQ728zeKPZ2uxdYBFcvgZsxQwvUQfAg7', NULL, '152.59.198.51', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiYTNQVWFBMTBHSmg2M0ZkMmNPYlMzUWZVN1kwMkQ2a2UzcndMYWZGSyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779525643),
('pWZRAqzLHPuur68Dxr28JSI3k9gMsn8UtWRBvxWM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidjdkR0tZdmJrWVd3SUVHUU5VVWNvZmVZSWFVR1FHN3h3YWwzOXVHViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780124222),
('qzbIzKYgD1MgUIekM2hbV6e27BFTMzny5HkjbqHT', NULL, '152.57.236.91', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYzh0T0RoTXVTQVZYQk12Y2lTa1BHRDRsN3hwd1dmbHJDY204bWZqUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vZGlzaGEtYXBpLmFqdGVjaHNvbHV0aW9uLmluL3B1YmxpYyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779292829),
('RbcoR0fvaX4422fE8RkdToSgTP3wnQqTuaSxv3jn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiSFhiVlQ2c2l0TElRVXBRYk40d3lnYzkyckgzSFgzSHF1SEwzdGZmWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779711024),
('sXqxeJPz8JwoiM4mznG8Uj83RsgpMo6xbJ2sCs60', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiaDJEcXBCOVdpcDBjTnQyY2djRXVnTm9MbW4ySUpmaERYeFZWd0RHSyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780300852),
('t7OLZ9nVZhVVljVlFe0tNx34jIqBJVrQLO3He1bl', NULL, '157.51.134.188', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiZE5rM04xVURMR1NhazVvTWdFWkFucjlCSHJ3QVN0SWRGOHNCUGNFbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779350526),
('XC1P1Fjkho8kB8DWpuYfrDDkWS1UmySLY19qUVvO', NULL, '157.51.135.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiblJJWXY2cHVDT2NiSjhEUjkwZjRNckxOS2V6azU2MU95ZVRwbXlIZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1779445731),
('XoU0HdelH7gB9bwlhZtkmaxGsoqArmc1NaPR3eGg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidU45ZGUyT2M1SUlqZVMzaGZQY0laeDJRQWt0UjVyeG5tTkZvbng1VSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780220381);

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
  `phone` bigint(20) DEFAULT NULL,
  `role` enum('admin','telecaller','receptionist') NOT NULL,
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Active,0=Inactive',
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `role`, `added_by`, `status`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$12$cVmMKj55aErjIif6Q8Kqp.0QJ4SfwgWVRGvPgXeXZ/Xvb8H5NT/6C', 9440161007, 'admin', NULL, 1, NULL, NULL, '2026-05-20 16:01:01', '2026-05-20 16:01:01'),
(2, 'telecaller', 'telecaller@gmail.com', NULL, '$2y$12$4ReOR4kxTX21IUKzspNCd.PymL47wuN8ZEFP3d7ynbY1GWewE4U.m', 9999999999, 'telecaller', NULL, 1, NULL, NULL, '2026-05-20 16:01:21', '2026-05-20 16:01:21'),
(3, 'Radha', 'radha@gmail.com', NULL, '$2y$12$P76BgVJXLi9z3rb.EOoj1e2Rub6rVSgVcqzOkYuO.sfoK2sqDkAkq', 9999999990, 'receptionist', 1, 1, NULL, NULL, '2026-05-21 16:08:52', '2026-05-21 16:08:52'),
(4, 'Aayan', 'aayan@gmail.com', NULL, '$2y$12$u9Zmqd/PbxkYbCHRxQjoN.tXFjDlWwQGU2CfHohoc4LQvJpwJleqy', 8919273834, 'telecaller', 1, 1, NULL, NULL, '2026-05-23 14:34:15', '2026-05-23 14:34:15'),
(5, 'Rec', 'rec@gmail.com', NULL, '$2y$12$lit7Z.bRHp3hUl42.0.a7uYBZFirwmpP8EbcomHsP1cR4agRrWEUy', 8877665544, 'receptionist', 1, 1, NULL, NULL, '2026-05-25 10:35:43', '2026-05-25 10:35:43'),
(6, 'Ramya', 'ramya@gmail.com', NULL, '$2y$12$XNXQsuoQrTFQbLRujVzJtOAeE344F2m/eaG8Owk2LDA/r0ULsM5ga', 9988779988, 'telecaller', 1, 1, NULL, NULL, '2026-05-25 11:50:12', '2026-05-25 11:50:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_lead_type_index` (`lead_type`),
  ADD KEY `clients_created_at_index` (`created_at`),
  ADD KEY `clients_phone_index` (`phone`),
  ADD KEY `clients_fullname_index` (`fullname`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
