-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 13, 2025 at 04:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qubo_labs`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `record_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `verification_code` varchar(4) NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by_student_id` int(11) DEFAULT NULL,
  `no_neighbours` tinyint(1) DEFAULT 0,
  `status` enum('scanned','verified') DEFAULT 'scanned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`record_id`, `session_id`, `student_id`, `seat_id`, `verification_code`, `scanned_at`, `verified_at`, `verified_by_student_id`, `no_neighbours`, `status`) VALUES
(1, 1, 3, 58, 'XO6Q', '2025-11-13 00:19:07', '2025-11-13 00:20:57', NULL, 1, 'verified'),
(2, 2, 3, 21, 'TP54', '2025-11-13 03:18:12', '2025-11-13 03:19:14', 4, 0, 'verified'),
(3, 2, 4, 20, '9VTJ', '2025-11-13 03:18:56', '2025-11-13 03:19:14', 3, 0, 'verified'),
(4, 2, 1, 23, 'GSKV', '2025-11-13 03:19:35', '2025-11-13 03:20:47', NULL, 1, 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `session_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `hall_id` int(11) NOT NULL,
  `session_name` varchar(100) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `status` enum('active','ended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_sessions`
--

INSERT INTO `attendance_sessions` (`session_id`, `staff_id`, `class_id`, `hall_id`, `session_name`, `start_time`, `end_time`, `status`) VALUES
(1, 1, 1, 1, 'Test', '2025-11-13 00:16:10', '2025-11-13 00:22:33', 'ended'),
(2, 1, 1, 1, 'Test', '2025-11-13 03:17:13', '2025-11-13 03:21:19', 'ended'),
(3, 1, 1, 2, 'Test', '2025-11-13 03:27:42', '2025-11-13 03:28:45', 'ended');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `section` varchar(10) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `section`, `semester`, `created_at`) VALUES
(1, 'CSE', 'A', '5th Sem', '2025-11-12 11:42:20'),
(2, 'CSE', 'B', '5th Sem', '2025-11-12 11:42:20'),
(3, 'CSE', 'C', '5th Sem', '2025-11-12 11:42:20'),
(4, 'CSE', 'D', '5th Sem', '2025-11-12 11:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `seminar_halls`
--

CREATE TABLE `seminar_halls` (
  `hall_id` int(11) NOT NULL,
  `hall_name` varchar(50) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `capacity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seminar_halls`
--

INSERT INTO `seminar_halls` (`hall_id`, `hall_name`, `room_number`, `capacity`, `created_at`) VALUES
(1, 'Seminar Hall 1', '025', 104, '2025-11-12 11:42:20'),
(2, 'Seminar Hall 2', '033', 104, '2025-11-12 11:42:20'),
(3, 'Seminar Hall 3', '034', 104, '2025-11-12 11:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `seminar_seats`
--

CREATE TABLE `seminar_seats` (
  `seat_id` int(11) NOT NULL,
  `hall_id` int(11) NOT NULL,
  `seat_number` varchar(20) NOT NULL,
  `row_number` int(11) NOT NULL,
  `seat_position` int(11) NOT NULL,
  `qr_code` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seminar_seats`
--

INSERT INTO `seminar_seats` (`seat_id`, `hall_id`, `seat_number`, `row_number`, `seat_position`, `qr_code`) VALUES
(1, 1, 'A1', 1, 1, 'A1_H1'),
(2, 1, 'A2', 1, 2, 'A2_H1'),
(3, 1, 'A3', 1, 3, 'A3_H1'),
(4, 1, 'A4', 1, 4, 'A4_H1'),
(5, 1, 'A5', 1, 5, 'A5_H1'),
(6, 1, 'A6', 1, 6, 'A6_H1'),
(7, 1, 'A7', 1, 7, 'A7_H1'),
(8, 1, 'A8', 1, 8, 'A8_H1'),
(9, 1, 'A9', 1, 9, 'A9_H1'),
(10, 1, 'A10', 1, 10, 'A10_H1'),
(11, 1, 'A11', 1, 11, 'A11_H1'),
(12, 1, 'A12', 1, 12, 'A12_H1'),
(13, 1, 'A13', 1, 13, 'A13_H1'),
(14, 1, 'A14', 1, 14, 'A14_H1'),
(15, 1, 'A15', 1, 15, 'A15_H1'),
(16, 1, 'A16', 1, 16, 'A16_H1'),
(17, 1, 'A17', 1, 17, 'A17_H1'),
(18, 1, 'A18', 1, 18, 'A18_H1'),
(19, 1, 'A19', 1, 19, 'A19_H1'),
(20, 1, 'A20', 1, 20, 'A20_H1'),
(21, 1, 'A21', 1, 21, 'A21_H1'),
(22, 1, 'B1', 2, 1, 'B1_H1'),
(23, 1, 'B2', 2, 2, 'B2_H1'),
(24, 1, 'B3', 2, 3, 'B3_H1'),
(25, 1, 'B4', 2, 4, 'B4_H1'),
(26, 1, 'B5', 2, 5, 'B5_H1'),
(27, 1, 'B6', 2, 6, 'B6_H1'),
(28, 1, 'B7', 2, 7, 'B7_H1'),
(29, 1, 'B8', 2, 8, 'B8_H1'),
(30, 1, 'B9', 2, 9, 'B9_H1'),
(31, 1, 'B10', 2, 10, 'B10_H1'),
(32, 1, 'B11', 2, 11, 'B11_H1'),
(33, 1, 'B12', 2, 12, 'B12_H1'),
(34, 1, 'B13', 2, 13, 'B13_H1'),
(35, 1, 'B14', 2, 14, 'B14_H1'),
(36, 1, 'B15', 2, 15, 'B15_H1'),
(37, 1, 'B16', 2, 16, 'B16_H1'),
(38, 1, 'B17', 2, 17, 'B17_H1'),
(39, 1, 'B18', 2, 18, 'B18_H1'),
(40, 1, 'B19', 2, 19, 'B19_H1'),
(41, 1, 'B20', 2, 20, 'B20_H1'),
(42, 1, 'B21', 2, 21, 'B21_H1'),
(43, 1, 'C1', 3, 1, 'C1_H1'),
(44, 1, 'C2', 3, 2, 'C2_H1'),
(45, 1, 'C3', 3, 3, 'C3_H1'),
(46, 1, 'C4', 3, 4, 'C4_H1'),
(47, 1, 'C5', 3, 5, 'C5_H1'),
(48, 1, 'C6', 3, 6, 'C6_H1'),
(49, 1, 'C7', 3, 7, 'C7_H1'),
(50, 1, 'C8', 3, 8, 'C8_H1'),
(51, 1, 'C9', 3, 9, 'C9_H1'),
(52, 1, 'C10', 3, 10, 'C10_H1'),
(53, 1, 'C11', 3, 11, 'C11_H1'),
(54, 1, 'C12', 3, 12, 'C12_H1'),
(55, 1, 'C13', 3, 13, 'C13_H1'),
(56, 1, 'C14', 3, 14, 'C14_H1'),
(57, 1, 'C15', 3, 15, 'C15_H1'),
(58, 1, 'C16', 3, 16, 'C16_H1'),
(59, 1, 'C17', 3, 17, 'C17_H1'),
(60, 1, 'C18', 3, 18, 'C18_H1'),
(61, 1, 'C19', 3, 19, 'C19_H1'),
(62, 1, 'C20', 3, 20, 'C20_H1'),
(63, 1, 'C21', 3, 21, 'C21_H1'),
(64, 1, 'D1', 4, 1, 'D1_H1'),
(65, 1, 'D2', 4, 2, 'D2_H1'),
(66, 1, 'D3', 4, 3, 'D3_H1'),
(67, 1, 'D4', 4, 4, 'D4_H1'),
(68, 1, 'D5', 4, 5, 'D5_H1'),
(69, 1, 'D6', 4, 6, 'D6_H1'),
(70, 1, 'D7', 4, 7, 'D7_H1'),
(71, 1, 'D8', 4, 8, 'D8_H1'),
(72, 1, 'D9', 4, 9, 'D9_H1'),
(73, 1, 'D10', 4, 10, 'D10_H1'),
(74, 1, 'D11', 4, 11, 'D11_H1'),
(75, 1, 'D12', 4, 12, 'D12_H1'),
(76, 1, 'D13', 4, 13, 'D13_H1'),
(77, 1, 'D14', 4, 14, 'D14_H1'),
(78, 1, 'D15', 4, 15, 'D15_H1'),
(79, 1, 'D16', 4, 16, 'D16_H1'),
(80, 1, 'D17', 4, 17, 'D17_H1'),
(81, 1, 'D18', 4, 18, 'D18_H1'),
(82, 1, 'D19', 4, 19, 'D19_H1'),
(83, 1, 'D20', 4, 20, 'D20_H1'),
(84, 1, 'D21', 4, 21, 'D21_H1'),
(85, 1, 'E1', 5, 1, 'E1_H1'),
(86, 1, 'E2', 5, 2, 'E2_H1'),
(87, 1, 'E3', 5, 3, 'E3_H1'),
(88, 1, 'E4', 5, 4, 'E4_H1'),
(89, 1, 'E5', 5, 5, 'E5_H1'),
(90, 1, 'E6', 5, 6, 'E6_H1'),
(91, 1, 'E7', 5, 7, 'E7_H1'),
(92, 1, 'E8', 5, 8, 'E8_H1'),
(93, 1, 'E9', 5, 9, 'E9_H1'),
(94, 1, 'E10', 5, 10, 'E10_H1'),
(95, 1, 'E11', 5, 11, 'E11_H1'),
(96, 1, 'E12', 5, 12, 'E12_H1'),
(97, 1, 'E13', 5, 13, 'E13_H1'),
(98, 1, 'E14', 5, 14, 'E14_H1'),
(99, 1, 'E15', 5, 15, 'E15_H1'),
(100, 1, 'E16', 5, 16, 'E16_H1'),
(101, 1, 'E17', 5, 17, 'E17_H1'),
(102, 1, 'E18', 5, 18, 'E18_H1'),
(103, 1, 'E19', 5, 19, 'E19_H1'),
(104, 1, 'E20', 5, 20, 'E20_H1'),
(105, 2, 'A1', 1, 1, 'A1_H2'),
(106, 2, 'A2', 1, 2, 'A2_H2'),
(107, 2, 'A3', 1, 3, 'A3_H2'),
(108, 2, 'A4', 1, 4, 'A4_H2'),
(109, 2, 'A5', 1, 5, 'A5_H2'),
(110, 2, 'A6', 1, 6, 'A6_H2'),
(111, 2, 'A7', 1, 7, 'A7_H2'),
(112, 2, 'A8', 1, 8, 'A8_H2'),
(113, 2, 'A9', 1, 9, 'A9_H2'),
(114, 2, 'A10', 1, 10, 'A10_H2'),
(115, 2, 'A11', 1, 11, 'A11_H2'),
(116, 2, 'A12', 1, 12, 'A12_H2'),
(117, 2, 'A13', 1, 13, 'A13_H2'),
(118, 2, 'A14', 1, 14, 'A14_H2'),
(119, 2, 'A15', 1, 15, 'A15_H2'),
(120, 2, 'A16', 1, 16, 'A16_H2'),
(121, 2, 'A17', 1, 17, 'A17_H2'),
(122, 2, 'A18', 1, 18, 'A18_H2'),
(123, 2, 'A19', 1, 19, 'A19_H2'),
(124, 2, 'A20', 1, 20, 'A20_H2'),
(125, 2, 'A21', 1, 21, 'A21_H2'),
(126, 2, 'B1', 2, 1, 'B1_H2'),
(127, 2, 'B2', 2, 2, 'B2_H2'),
(128, 2, 'B3', 2, 3, 'B3_H2'),
(129, 2, 'B4', 2, 4, 'B4_H2'),
(130, 2, 'B5', 2, 5, 'B5_H2'),
(131, 2, 'B6', 2, 6, 'B6_H2'),
(132, 2, 'B7', 2, 7, 'B7_H2'),
(133, 2, 'B8', 2, 8, 'B8_H2'),
(134, 2, 'B9', 2, 9, 'B9_H2'),
(135, 2, 'B10', 2, 10, 'B10_H2'),
(136, 2, 'B11', 2, 11, 'B11_H2'),
(137, 2, 'B12', 2, 12, 'B12_H2'),
(138, 2, 'B13', 2, 13, 'B13_H2'),
(139, 2, 'B14', 2, 14, 'B14_H2'),
(140, 2, 'B15', 2, 15, 'B15_H2'),
(141, 2, 'B16', 2, 16, 'B16_H2'),
(142, 2, 'B17', 2, 17, 'B17_H2'),
(143, 2, 'B18', 2, 18, 'B18_H2'),
(144, 2, 'B19', 2, 19, 'B19_H2'),
(145, 2, 'B20', 2, 20, 'B20_H2'),
(146, 2, 'B21', 2, 21, 'B21_H2'),
(147, 2, 'C1', 3, 1, 'C1_H2'),
(148, 2, 'C2', 3, 2, 'C2_H2'),
(149, 2, 'C3', 3, 3, 'C3_H2'),
(150, 2, 'C4', 3, 4, 'C4_H2'),
(151, 2, 'C5', 3, 5, 'C5_H2'),
(152, 2, 'C6', 3, 6, 'C6_H2'),
(153, 2, 'C7', 3, 7, 'C7_H2'),
(154, 2, 'C8', 3, 8, 'C8_H2'),
(155, 2, 'C9', 3, 9, 'C9_H2'),
(156, 2, 'C10', 3, 10, 'C10_H2'),
(157, 2, 'C11', 3, 11, 'C11_H2'),
(158, 2, 'C12', 3, 12, 'C12_H2'),
(159, 2, 'C13', 3, 13, 'C13_H2'),
(160, 2, 'C14', 3, 14, 'C14_H2'),
(161, 2, 'C15', 3, 15, 'C15_H2'),
(162, 2, 'C16', 3, 16, 'C16_H2'),
(163, 2, 'C17', 3, 17, 'C17_H2'),
(164, 2, 'C18', 3, 18, 'C18_H2'),
(165, 2, 'C19', 3, 19, 'C19_H2'),
(166, 2, 'C20', 3, 20, 'C20_H2'),
(167, 2, 'C21', 3, 21, 'C21_H2'),
(168, 2, 'D1', 4, 1, 'D1_H2'),
(169, 2, 'D2', 4, 2, 'D2_H2'),
(170, 2, 'D3', 4, 3, 'D3_H2'),
(171, 2, 'D4', 4, 4, 'D4_H2'),
(172, 2, 'D5', 4, 5, 'D5_H2'),
(173, 2, 'D6', 4, 6, 'D6_H2'),
(174, 2, 'D7', 4, 7, 'D7_H2'),
(175, 2, 'D8', 4, 8, 'D8_H2'),
(176, 2, 'D9', 4, 9, 'D9_H2'),
(177, 2, 'D10', 4, 10, 'D10_H2'),
(178, 2, 'D11', 4, 11, 'D11_H2'),
(179, 2, 'D12', 4, 12, 'D12_H2'),
(180, 2, 'D13', 4, 13, 'D13_H2'),
(181, 2, 'D14', 4, 14, 'D14_H2'),
(182, 2, 'D15', 4, 15, 'D15_H2'),
(183, 2, 'D16', 4, 16, 'D16_H2'),
(184, 2, 'D17', 4, 17, 'D17_H2'),
(185, 2, 'D18', 4, 18, 'D18_H2'),
(186, 2, 'D19', 4, 19, 'D19_H2'),
(187, 2, 'D20', 4, 20, 'D20_H2'),
(188, 2, 'D21', 4, 21, 'D21_H2'),
(189, 2, 'E1', 5, 1, 'E1_H2'),
(190, 2, 'E2', 5, 2, 'E2_H2'),
(191, 2, 'E3', 5, 3, 'E3_H2'),
(192, 2, 'E4', 5, 4, 'E4_H2'),
(193, 2, 'E5', 5, 5, 'E5_H2'),
(194, 2, 'E6', 5, 6, 'E6_H2'),
(195, 2, 'E7', 5, 7, 'E7_H2'),
(196, 2, 'E8', 5, 8, 'E8_H2'),
(197, 2, 'E9', 5, 9, 'E9_H2'),
(198, 2, 'E10', 5, 10, 'E10_H2'),
(199, 2, 'E11', 5, 11, 'E11_H2'),
(200, 2, 'E12', 5, 12, 'E12_H2'),
(201, 2, 'E13', 5, 13, 'E13_H2'),
(202, 2, 'E14', 5, 14, 'E14_H2'),
(203, 2, 'E15', 5, 15, 'E15_H2'),
(204, 2, 'E16', 5, 16, 'E16_H2'),
(205, 2, 'E17', 5, 17, 'E17_H2'),
(206, 2, 'E18', 5, 18, 'E18_H2'),
(207, 2, 'E19', 5, 19, 'E19_H2'),
(208, 2, 'E20', 5, 20, 'E20_H2'),
(209, 3, 'A1', 1, 1, 'A1_H3'),
(210, 3, 'A2', 1, 2, 'A2_H3'),
(211, 3, 'A3', 1, 3, 'A3_H3'),
(212, 3, 'A4', 1, 4, 'A4_H3'),
(213, 3, 'A5', 1, 5, 'A5_H3'),
(214, 3, 'A6', 1, 6, 'A6_H3'),
(215, 3, 'A7', 1, 7, 'A7_H3'),
(216, 3, 'A8', 1, 8, 'A8_H3'),
(217, 3, 'A9', 1, 9, 'A9_H3'),
(218, 3, 'A10', 1, 10, 'A10_H3'),
(219, 3, 'A11', 1, 11, 'A11_H3'),
(220, 3, 'A12', 1, 12, 'A12_H3'),
(221, 3, 'A13', 1, 13, 'A13_H3'),
(222, 3, 'A14', 1, 14, 'A14_H3'),
(223, 3, 'A15', 1, 15, 'A15_H3'),
(224, 3, 'A16', 1, 16, 'A16_H3'),
(225, 3, 'A17', 1, 17, 'A17_H3'),
(226, 3, 'A18', 1, 18, 'A18_H3'),
(227, 3, 'A19', 1, 19, 'A19_H3'),
(228, 3, 'A20', 1, 20, 'A20_H3'),
(229, 3, 'A21', 1, 21, 'A21_H3'),
(230, 3, 'B1', 2, 1, 'B1_H3'),
(231, 3, 'B2', 2, 2, 'B2_H3'),
(232, 3, 'B3', 2, 3, 'B3_H3'),
(233, 3, 'B4', 2, 4, 'B4_H3'),
(234, 3, 'B5', 2, 5, 'B5_H3'),
(235, 3, 'B6', 2, 6, 'B6_H3'),
(236, 3, 'B7', 2, 7, 'B7_H3'),
(237, 3, 'B8', 2, 8, 'B8_H3'),
(238, 3, 'B9', 2, 9, 'B9_H3'),
(239, 3, 'B10', 2, 10, 'B10_H3'),
(240, 3, 'B11', 2, 11, 'B11_H3'),
(241, 3, 'B12', 2, 12, 'B12_H3'),
(242, 3, 'B13', 2, 13, 'B13_H3'),
(243, 3, 'B14', 2, 14, 'B14_H3'),
(244, 3, 'B15', 2, 15, 'B15_H3'),
(245, 3, 'B16', 2, 16, 'B16_H3'),
(246, 3, 'B17', 2, 17, 'B17_H3'),
(247, 3, 'B18', 2, 18, 'B18_H3'),
(248, 3, 'B19', 2, 19, 'B19_H3'),
(249, 3, 'B20', 2, 20, 'B20_H3'),
(250, 3, 'B21', 2, 21, 'B21_H3'),
(251, 3, 'C1', 3, 1, 'C1_H3'),
(252, 3, 'C2', 3, 2, 'C2_H3'),
(253, 3, 'C3', 3, 3, 'C3_H3'),
(254, 3, 'C4', 3, 4, 'C4_H3'),
(255, 3, 'C5', 3, 5, 'C5_H3'),
(256, 3, 'C6', 3, 6, 'C6_H3'),
(257, 3, 'C7', 3, 7, 'C7_H3'),
(258, 3, 'C8', 3, 8, 'C8_H3'),
(259, 3, 'C9', 3, 9, 'C9_H3'),
(260, 3, 'C10', 3, 10, 'C10_H3'),
(261, 3, 'C11', 3, 11, 'C11_H3'),
(262, 3, 'C12', 3, 12, 'C12_H3'),
(263, 3, 'C13', 3, 13, 'C13_H3'),
(264, 3, 'C14', 3, 14, 'C14_H3'),
(265, 3, 'C15', 3, 15, 'C15_H3'),
(266, 3, 'C16', 3, 16, 'C16_H3'),
(267, 3, 'C17', 3, 17, 'C17_H3'),
(268, 3, 'C18', 3, 18, 'C18_H3'),
(269, 3, 'C19', 3, 19, 'C19_H3'),
(270, 3, 'C20', 3, 20, 'C20_H3'),
(271, 3, 'C21', 3, 21, 'C21_H3'),
(272, 3, 'D1', 4, 1, 'D1_H3'),
(273, 3, 'D2', 4, 2, 'D2_H3'),
(274, 3, 'D3', 4, 3, 'D3_H3'),
(275, 3, 'D4', 4, 4, 'D4_H3'),
(276, 3, 'D5', 4, 5, 'D5_H3'),
(277, 3, 'D6', 4, 6, 'D6_H3'),
(278, 3, 'D7', 4, 7, 'D7_H3'),
(279, 3, 'D8', 4, 8, 'D8_H3'),
(280, 3, 'D9', 4, 9, 'D9_H3'),
(281, 3, 'D10', 4, 10, 'D10_H3'),
(282, 3, 'D11', 4, 11, 'D11_H3'),
(283, 3, 'D12', 4, 12, 'D12_H3'),
(284, 3, 'D13', 4, 13, 'D13_H3'),
(285, 3, 'D14', 4, 14, 'D14_H3'),
(286, 3, 'D15', 4, 15, 'D15_H3'),
(287, 3, 'D16', 4, 16, 'D16_H3'),
(288, 3, 'D17', 4, 17, 'D17_H3'),
(289, 3, 'D18', 4, 18, 'D18_H3'),
(290, 3, 'D19', 4, 19, 'D19_H3'),
(291, 3, 'D20', 4, 20, 'D20_H3'),
(292, 3, 'D21', 4, 21, 'D21_H3'),
(293, 3, 'E1', 5, 1, 'E1_H3'),
(294, 3, 'E2', 5, 2, 'E2_H3'),
(295, 3, 'E3', 5, 3, 'E3_H3'),
(296, 3, 'E4', 5, 4, 'E4_H3'),
(297, 3, 'E5', 5, 5, 'E5_H3'),
(298, 3, 'E6', 5, 6, 'E6_H3'),
(299, 3, 'E7', 5, 7, 'E7_H3'),
(300, 3, 'E8', 5, 8, 'E8_H3'),
(301, 3, 'E9', 5, 9, 'E9_H3'),
(302, 3, 'E10', 5, 10, 'E10_H3'),
(303, 3, 'E11', 5, 11, 'E11_H3'),
(304, 3, 'E12', 5, 12, 'E12_H3'),
(305, 3, 'E13', 5, 13, 'E13_H3'),
(306, 3, 'E14', 5, 14, 'E14_H3'),
(307, 3, 'E15', 5, 15, 'E15_H3'),
(308, 3, 'E16', 5, 16, 'E16_H3'),
(309, 3, 'E17', 5, 17, 'E17_H3'),
(310, 3, 'E18', 5, 18, 'E18_H3'),
(311, 3, 'E19', 5, 19, 'E19_H3'),
(312, 3, 'E20', 5, 20, 'E20_H3');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `staff_number` varchar(20) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `staff_number`, `staff_name`, `password`, `department`, `session_token`, `last_login`, `created_at`) VALUES
(1, 'REKH2588', 'Prof. Rekha P', 'Pro588', 'Computer Science', '354dc5f1a81e4855553230125a528f5e48aada4401869d2dba0bf5df932fdd68', '2025-11-13 03:27:27', '2025-11-12 11:42:20'),
(2, 'ARFA3901', 'Prof. Arfa Bhandari', 'Pro901', 'Computer Science', NULL, NULL, '2025-11-12 11:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `usn_number` varchar(20) DEFAULT NULL,
  `student_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `usn_number`, `student_name`, `password`, `class_id`, `session_token`, `last_login`, `created_at`) VALUES
(1, '1MJ23CS005', 'Abhishek A', 'Abh005', 1, NULL, '2025-11-13 03:19:32', '2025-11-12 11:42:20'),
(2, '1MJ23CS008', 'Aditya Suresh', 'Adi008', 1, NULL, '2025-11-13 03:20:02', '2025-11-12 11:42:20'),
(3, '1MJ23CS012', 'Alvin Sonny', 'Alv012', 1, NULL, '2025-11-13 03:17:04', '2025-11-12 11:42:20'),
(4, '1MJ23CS015', 'Ananya Sanjiv', 'Ana015', 1, NULL, '2025-11-13 03:18:42', '2025-11-12 11:42:20'),
(5, '1MJ23CS018', 'Arjun Menon', 'Arj018', 1, NULL, NULL, '2025-11-12 11:42:20'),
(6, '1MJ23CS058', 'Ganesha Thejaswi', 'Gan058', 2, NULL, NULL, '2025-11-12 11:42:20'),
(7, '1MJ23CS061', 'Gowri Krishnan Nair', 'Gow061', 2, NULL, NULL, '2025-11-12 11:42:20'),
(8, '1MJ23CS064', 'Harsha Kumar', 'Har064', 2, NULL, NULL, '2025-11-12 11:42:20'),
(9, '1MJ23CS067', 'Ishaan Reddy', 'Ish067', 2, NULL, NULL, '2025-11-12 11:42:20'),
(10, '1MJ23CS070', 'Jaya Prakash', 'Jay070', 2, NULL, NULL, '2025-11-12 11:42:20'),
(11, '1MJ23CS101', 'Karthik Sharma', 'Kar101', 3, NULL, NULL, '2025-11-12 11:42:20'),
(12, '1MJ23CS104', 'Lakshmi Iyer', 'Lak104', 3, NULL, NULL, '2025-11-12 11:42:20'),
(13, '1MJ23CS107', 'Manoj Kumar', 'Man107', 3, NULL, NULL, '2025-11-12 11:42:20'),
(14, '1MJ23CS110', 'Nisha Patel', 'Nis110', 3, NULL, NULL, '2025-11-12 11:42:20'),
(15, '1MJ23CS113', 'Omkar Singh', 'Omk113', 3, NULL, NULL, '2025-11-12 11:42:20'),
(16, '1MJ23CS145', 'Priya Ramesh', 'Pri145', 4, NULL, NULL, '2025-11-12 11:42:20'),
(17, '1MJ23CS148', 'Rahul Verma', 'Rah148', 4, NULL, NULL, '2025-11-12 11:42:20'),
(18, '1MJ23CS151', 'Sneha Desai', 'Sne151', 4, NULL, NULL, '2025-11-12 11:42:20'),
(19, '1MJ23CS154', 'Tarun Gupta', 'Tar154', 4, NULL, NULL, '2025-11-12 11:42:20'),
(20, '1MJ23CS157', 'Usha Nair', 'Ush157', 4, NULL, NULL, '2025-11-12 11:42:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `seat_id` (`seat_id`),
  ADD KEY `verified_by_student_id` (`verified_by_student_id`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `hall_id` (`hall_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `seminar_halls`
--
ALTER TABLE `seminar_halls`
  ADD PRIMARY KEY (`hall_id`);

--
-- Indexes for table `seminar_seats`
--
ALTER TABLE `seminar_seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD UNIQUE KEY `unique_seat_per_hall` (`hall_id`,`seat_number`),
  ADD UNIQUE KEY `unique_qr_code` (`qr_code`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `unique_staff_number` (`staff_number`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_records`
--
ALTER TABLE `attendance_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seminar_halls`
--
ALTER TABLE `seminar_halls`
  MODIFY `hall_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seminar_seats`
--
ALTER TABLE `seminar_seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=313;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `attendance_sessions` (`session_id`),
  ADD CONSTRAINT `attendance_records_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `attendance_records_ibfk_3` FOREIGN KEY (`seat_id`) REFERENCES `seminar_seats` (`seat_id`),
  ADD CONSTRAINT `attendance_records_ibfk_4` FOREIGN KEY (`verified_by_student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `attendance_sessions_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`),
  ADD CONSTRAINT `attendance_sessions_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `attendance_sessions_ibfk_3` FOREIGN KEY (`hall_id`) REFERENCES `seminar_halls` (`hall_id`);

--
-- Constraints for table `seminar_seats`
--
ALTER TABLE `seminar_seats`
  ADD CONSTRAINT `seminar_seats_ibfk_1` FOREIGN KEY (`hall_id`) REFERENCES `seminar_halls` (`hall_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
