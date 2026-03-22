-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2026 at 10:41 AM
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
-- Database: `bisu_rites`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `first_name`, `last_name`, `email`, `college_id`, `role_id`, `created_at`) VALUES
(1, 'admin', '$2y$10$7NU7W20Z.BXG0Kd9MufpYOyOh8ZyFTdidnwfjqLPHXChe6nsvSjfa', 'System', 'Administrator', NULL, NULL, 1, '2026-02-10 00:52:41'),
(4, 'kaloy', '$2y$10$w9IYHaIR30/bklPiEdHiVO.FFGn6PnVGV0/stJIfBhfKKq9vXogl6', 'Magdalena', 'Bernales', NULL, NULL, 2, '2026-02-16 04:39:29'),
(5, 'jboy', '$2y$10$8k0o/59QNwT0PwutO/qq9ORSFRZ2jJxEImSVm/IVYKFm1hc3jw5Qe', 'Jeszon', 'Cano', NULL, NULL, 3, '2026-02-16 04:54:54'),
(6, 'Juan', '$2y$10$YGTezHk0i9ez4QkAC1B2M.qIcJ6mml4lJQU9Ol2GqJIQwMAUFUAIa', 'Juan', 'Dela Cruz', NULL, NULL, 9, '2026-03-01 03:13:30'),
(7, 'John', '$2y$10$gOAmtO5M5c.C2zleBcIYkufqOb6Std.qj/qOrqgNYK5rSo5EYAOcK', 'John', 'Marston', NULL, NULL, 8, '2026-03-01 06:27:52'),
(8, 'Rose', '$2y$10$TxDIhfyBPukwVcoLV9ghZu.0ofMrBaAU0KFkkBLoxxYh9/Uhw5HAe', 'Rosario', 'Piloton', NULL, NULL, 4, '2026-03-01 06:34:13'),
(9, 'gulle_jhoelkenneth', '$2y$10$F7WJRvZnlaZqEKCiKjFjn.7gKkyj6jXDFiimoxNql7k5LFaWRYEEu', 'Jhoel Kenneth', 'Gulle', NULL, NULL, 5, '2026-03-16 07:19:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `system_roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
