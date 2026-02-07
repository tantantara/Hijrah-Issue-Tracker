-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2026 at 12:07 PM
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
-- Database: `tracking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `folder_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folders`
--

INSERT INTO `folders` (`id`, `folder_name`, `created_at`, `is_hidden`) VALUES
(1, 'November 2026', '2026-01-06 04:11:36', 0),
(2, 'December 2025', '2026-01-06 04:22:23', 1),
(8, '12. september 2025 - weekly issue tracker', '2026-01-07 07:06:41', 0),
(9, 'JANUARY 2026', '2026-01-08 12:16:44', 0),
(10, 'FEBRUARY 2026', '2026-02-03 04:20:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) DEFAULT NULL,
  `site_id` int(11) DEFAULT NULL,
  `pacs_ris_ccis` varchar(100) DEFAULT NULL,
  `open_time_date` datetime NOT NULL,
  `issue_title` varchar(255) NOT NULL,
  `issue_details` text DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `engineer_in_charge` varchar(100) DEFAULT NULL,
  `issue_status` enum('open','close') DEFAULT 'open',
  `resolution` text DEFAULT NULL,
  `close_time_date` datetime DEFAULT NULL,
  `created_by_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `folder_id`, `site_id`, `pacs_ris_ccis`, `open_time_date`, `issue_title`, `issue_details`, `serial_number`, `engineer_in_charge`, `issue_status`, `resolution`, `close_time_date`, `created_by_user_id`) VALUES
(5, 9, 4, 'PACS', '2026-01-15 10:04:00', 'CANNOT VIEW IMAGE', 'IMAGE ON PACS UV CANNOT BE VIEWED BY CLINICIANS', '', 'AINI', 'close', '123', '2026-01-23 23:24:00', 13),
(7, 9, 4, 'eki', '2026-01-01 15:28:00', 'ee', 'ee4', '34x', 'd4', 'close', '', '2026-01-20 22:35:00', 12);

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `id` int(11) NOT NULL,
  `site_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`id`, `site_name`) VALUES
(4, 'RSH'),
(5, 'UNIMAS');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','engineer') NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(6, NULL, 'geadmin@hijrahinovatif.com', 'geadmin123', 'admin', 'approved', '2026-01-06 04:55:49'),
(8, 'System Admin', 'admin@hijrahinovatif.com', 'admin123', 'admin', 'approved', '2026-01-07 06:30:21'),
(11, 'aina', 'aina@gmail.com', 'aina', 'engineer', 'approved', '2026-01-07 06:38:27'),
(12, 'ain', 'ain@gmail.com', 'ain', 'engineer', 'approved', '2026-01-07 07:27:36'),
(13, 'aini', 'aini@gmail.com', 'aini', 'engineer', '', '2026-01-08 12:15:23'),
(14, 'najwa', 'najwa@gmail.com', 'najwa', 'engineer', 'approved', '2026-02-03 04:20:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `created_by_user_id` (`created_by_user_id`);

--
-- Indexes for table `sites`
--
ALTER TABLE `sites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sites`
--
ALTER TABLE `sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issues_ibfk_2` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issues_ibfk_3` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
