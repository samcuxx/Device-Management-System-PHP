-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2024 at 05:16 PM
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
-- Database: `comprint_services`
--

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `device_type` enum('Computer','Printer','Network','Other') NOT NULL,
  `problem_description` text DEFAULT NULL,
  `ram_size` varchar(20) DEFAULT NULL,
  `drive_size` varchar(20) DEFAULT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_contact` varchar(50) NOT NULL,
  `status` enum('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assigned_to` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_name`, `device_type`, `problem_description`, `ram_size`, `drive_size`, `client_name`, `client_contact`, `status`, `created_at`, `updated_at`, `assigned_to`) VALUES
(2, 'accer', 'Computer', 'ftgh', '8', '512', 'SamCux', '0531114854', 'In Progress', '2024-11-09 13:58:05', '2024-11-09 13:58:05', NULL),
(4, 'Hello', 'Printer', 'rtgsrtyserytgsedfyhretdsyh', NULL, NULL, 'SamCux', '0531114854', 'Completed', '2024-11-09 15:18:27', '2024-11-09 15:39:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `device_attachments`
--

CREATE TABLE `device_attachments` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_history`
--

CREATE TABLE `device_history` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status_change` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'Comprint Services', 'company_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:10'),
(2, 'company_address', 'ytur', 'company_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:10'),
(3, 'company_phone', 'rtyj', 'company_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:10'),
(4, 'company_email', 'tryurtuh@gfju.vcc', 'company_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:10'),
(5, 'default_pagination', '25', 'system_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:31'),
(6, 'date_format', 'd/m/Y', 'system_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:31'),
(7, 'timezone', 'Africa/Accra', 'system_settings', '2024-11-09 12:39:21', '2024-11-09 15:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','employee') NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'Tracks user last login time',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `contact_number`, `created_at`, `last_login`, `updated_at`) VALUES
(1, 'SamCux', '$2y$10$5iwhaWvpqCXmzAnT4kRuhu1SGV/vTlj4/BzeHo5PTujgYua9G4MJC', 'SamCux', 'samcux@comprint.com', 'admin', NULL, '2024-11-09 13:17:16', '2024-11-09 16:13:37', '2024-11-09 16:13:37'),
(2, 'finisher', '$2y$10$71dNvmYG1RHaC7V.NjDiZeuU2TzcQwIpwWaWkfyJwL9s2feoglfDW', 'Finisher', 'finish12@gmail.com', 'admin', '0552232004', '2024-11-09 14:48:40', '2024-11-09 15:55:44', '2024-11-09 15:55:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `device_attachments`
--
ALTER TABLE `device_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`);

--
-- Indexes for table `device_history`
--
ALTER TABLE `device_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_id` (`device_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `device_attachments`
--
ALTER TABLE `device_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_history`
--
ALTER TABLE `device_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `device_attachments`
--
ALTER TABLE `device_attachments`
  ADD CONSTRAINT `device_attachments_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `device_history`
--
ALTER TABLE `device_history`
  ADD CONSTRAINT `device_history_ibfk_1` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `device_history_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
