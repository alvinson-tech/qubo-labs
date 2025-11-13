-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 13, 2025 at 08:20 AM
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
) ;

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
(1, 'REKH2699', 'Prof. Rekha P', 'Pro699', 'Computer Science', NULL, NULL, '2025-11-12 11:42:20'),
(2, 'ARFA2700', 'Prof. Arfa Bhandari', 'Pro700', 'Computer Science', NULL, NULL, '2025-11-12 11:42:20'),
(3, 'HELE2701', 'Prof. Helan', 'Pro701', 'Computer Science', NULL, NULL, '2025-11-13 07:13:29'),
(4, 'ARNA2702', 'Prof. Arnab Tah', 'Pro702', 'Computer Science', NULL, NULL, '2025-11-13 07:13:29'),
(5, 'ANJA2703', 'Prof. Anjana Manoj', 'Pro703', 'Computer Science', NULL, NULL, '2025-11-13 07:13:29'),
(6, 'POSH2704', 'Prof. Poshita', 'Pro704', 'Computer Science', NULL, NULL, '2025-11-13 07:13:29'),
(7, 'NAVY2705', 'Prof. Navya', 'Pro705', 'Computer Science', NULL, NULL, '2025-11-13 07:13:29'),
(8, 'MUFL2706', 'Prof. Mufli Ali', 'Pro706', 'Computer Science', NULL, NULL, '2025-11-13 07:13:29');

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
(1, '1MJ23CS001', 'A LAVANYA', 'Ala001', 1, NULL, NULL, '2025-11-13 07:10:20'),
(2, '1MJ23CS002', 'A S BINUSHA', 'Asb002', 1, NULL, NULL, '2025-11-13 07:10:20'),
(3, '1MJ23CS003', 'AAKANKSHA ANIL KUMAR', 'Aak003', 1, NULL, NULL, '2025-11-13 07:10:20'),
(4, '1MJ23CS004', 'ABDUL TOUHEED', 'Abd004', 1, NULL, NULL, '2025-11-13 07:10:20'),
(5, '1MJ23CS005', 'ABHISHEK A', 'Abh005', 1, NULL, NULL, '2025-11-13 07:10:20'),
(6, '1MJ23CS006', 'ABHISHEK K', 'Abh006', 1, NULL, NULL, '2025-11-13 07:10:20'),
(7, '1MJ23CS007', 'ABHISHEK S S', 'Abh007', 1, NULL, NULL, '2025-11-13 07:10:20'),
(8, '1MJ23CS008', 'ADITYA SURESH APPAGUNDI', 'Adi008', 1, NULL, NULL, '2025-11-13 07:10:20'),
(9, '1MJ23CS009', 'AKASH SURENDRA AINAPUR', 'Aka009', 1, NULL, NULL, '2025-11-13 07:10:20'),
(10, '1MJ23CS010', 'AKSHATA SURESH VITTAPPANAVAR', 'Aks010', 1, NULL, NULL, '2025-11-13 07:10:20'),
(11, '1MJ23CS011', 'AKSHAY H', 'Aks011', 1, NULL, NULL, '2025-11-13 07:10:20'),
(12, '1MJ23CS012', 'ALVIN SONNY', 'Alv012', 1, NULL, NULL, '2025-11-13 07:10:20'),
(13, '1MJ23CS013', 'AMULYA G GOUDA', 'Amu013', 1, NULL, NULL, '2025-11-13 07:10:20'),
(14, '1MJ23CS014', 'ANAGHASHREE G K', 'Ana014', 1, NULL, NULL, '2025-11-13 07:10:20'),
(15, '1MJ23CS015', 'ANANYA SANJIV', 'Ana015', 1, NULL, NULL, '2025-11-13 07:10:20'),
(16, '1MJ23CS016', 'ANIKETHA H N', 'Ani016', 1, NULL, NULL, '2025-11-13 07:10:20'),
(17, '1MJ23CS017', 'ANKITA CHARAN PAHADI', 'Ank017', 1, NULL, NULL, '2025-11-13 07:10:20'),
(18, '1MJ23CS018', 'ANUSHREE D S', 'Anu018', 1, NULL, NULL, '2025-11-13 07:10:20'),
(19, '1MJ23CS019', 'ARLUR DARSHAN KUMAR', 'Arl019', 1, NULL, NULL, '2025-11-13 07:10:20'),
(20, '1MJ23CS020', 'ARFA KULSUM', 'Arf020', 1, NULL, NULL, '2025-11-13 07:10:20'),
(21, '1MJ23CS021', 'ARJUN SHARMA', 'Arj021', 1, NULL, NULL, '2025-11-13 07:10:20'),
(22, '1MJ23CS022', 'ARUN', 'Aru022', 1, NULL, NULL, '2025-11-13 07:10:20'),
(23, '1MJ23CS024', 'AVIRAJ BHAWRHA', 'Avi024', 1, NULL, NULL, '2025-11-13 07:10:20'),
(24, '1MJ23CS025', 'AYUSH KUMAR', 'Ayu025', 1, NULL, NULL, '2025-11-13 07:10:20'),
(25, '1MJ23CS026', 'BALAJI R', 'Bal026', 1, NULL, NULL, '2025-11-13 07:10:20'),
(26, '1MJ23CS027', 'BAPUGOUDA JALIKATTI', 'Bap027', 1, NULL, NULL, '2025-11-13 07:10:20'),
(27, '1MJ23CS028', 'BASAVARAJ', 'Bas028', 1, NULL, NULL, '2025-11-13 07:10:20'),
(28, '1MJ23CS029', 'BASAVARAJ B', 'Bas029', 1, NULL, NULL, '2025-11-13 07:10:20'),
(29, '1MJ23CS030', 'BHARATH C', 'Bha030', 1, NULL, NULL, '2025-11-13 07:10:20'),
(30, '1MJ23CS031', 'BHARGAVI V R', 'Bha031', 1, NULL, NULL, '2025-11-13 07:10:20'),
(31, '1MJ23CS032', 'BHAVANA D S', 'Bha032', 1, NULL, NULL, '2025-11-13 07:10:20'),
(32, '1MJ23CS033', 'C H CHANDANASHREE', 'Chc033', 1, NULL, NULL, '2025-11-13 07:10:20'),
(33, '1MJ23CS034', 'CHANDAN D P', 'Cha034', 1, NULL, NULL, '2025-11-13 07:10:20'),
(34, '1MJ23CS035', 'CHANNANABASAVA', 'Cha035', 1, NULL, NULL, '2025-11-13 07:10:20'),
(35, '1MJ23CS036', 'CHETHAN KUMAR B S', 'Che036', 1, NULL, NULL, '2025-11-13 07:10:20'),
(36, '1MJ23CS037', 'CHETHANASHREE P', 'Che037', 1, NULL, NULL, '2025-11-13 07:10:20'),
(37, '1MJ23CS038', 'CHIRANTH L', 'Chi038', 1, NULL, NULL, '2025-11-13 07:10:20'),
(38, '1MJ23CS039', 'CHRISTY SHEPHARD', 'Chr039', 1, NULL, NULL, '2025-11-13 07:10:20'),
(39, '1MJ23CS040', 'D MANOHAR', 'Dma040', 1, NULL, NULL, '2025-11-13 07:10:20'),
(40, '1MJ23CS041', 'D NEHA', 'Dne041', 1, NULL, NULL, '2025-11-13 07:10:20'),
(41, '1MJ23CS042', 'DANISH KHAJURIA', 'Dan042', 1, NULL, NULL, '2025-11-13 07:10:20'),
(42, '1MJ23CS043', 'DARSHAN', 'Dar043', 1, NULL, NULL, '2025-11-13 07:10:20'),
(43, '1MJ23CS044', 'DEBADITYA DAS', 'Deb044', 1, NULL, NULL, '2025-11-13 07:10:20'),
(44, '1MJ23CS045', 'DEEKSHITHA A U', 'Dee045', 1, NULL, NULL, '2025-11-13 07:10:20'),
(45, '1MJ23CS046', 'DEEPTI K M', 'Dee046', 1, NULL, NULL, '2025-11-13 07:10:20'),
(46, '1MJ23CS047', 'DEERAJ ASHOK', 'Dee047', 1, NULL, NULL, '2025-11-13 07:10:20'),
(47, '1MJ23CS048', 'DHANUSH D', 'Dha048', 1, NULL, NULL, '2025-11-13 07:10:20'),
(48, '1MJ23CS049', 'DHANUSH JAIN', 'Dha049', 1, NULL, NULL, '2025-11-13 07:10:20'),
(49, '1MJ23CS050', 'DHARSHAN V', 'Dha050', 1, NULL, NULL, '2025-11-13 07:10:20'),
(50, '1MJ23CS051', 'DINESH MANGE', 'Din051', 1, NULL, NULL, '2025-11-13 07:10:20'),
(51, '1MJ23CS052', 'DISHA V O', 'Dis052', 1, NULL, NULL, '2025-11-13 07:10:20'),
(52, '1MJ23CS053', 'E VANDANA', 'Eva053', 1, NULL, NULL, '2025-11-13 07:10:20'),
(53, '1MJ23CS054', 'G M VISHWANATH', 'Gmv054', 1, NULL, NULL, '2025-11-13 07:10:20'),
(54, '1MJ23CS208', 'V PRAJWAL', 'Vpr208', 1, NULL, NULL, '2025-11-13 07:10:20'),
(55, '1MJ23CS900', 'MYTHRI SARAVANA', 'Myt900', 1, NULL, NULL, '2025-11-13 07:10:20'),
(56, '1MJ23CS400', 'ABDULAZIZ MOHAMMED', 'Abd400', 1, NULL, NULL, '2025-11-13 07:10:20'),
(57, '1MJ24CS401', 'ABHISHEK GOUDA', 'Abh401', 1, NULL, NULL, '2025-11-13 07:10:20'),
(58, '1MJ24CS402', 'ABHISHEK POLICE PATIL', 'Abh402', 1, NULL, NULL, '2025-11-13 07:10:20'),
(59, '1MJ24CS404', 'ARPITHA SURESH', 'Arp404', 1, NULL, NULL, '2025-11-13 07:10:20'),
(60, '1MJ24CS405', 'ARYKA K S', 'Ary405', 1, NULL, NULL, '2025-11-13 07:10:20'),
(61, '1MJ24CS406', 'ASHISH GUPTA', 'Ash406', 1, NULL, NULL, '2025-11-13 07:10:20'),
(62, '1MJ24CS407', 'AYESHA SIDDIQA', 'Aye407', 1, NULL, NULL, '2025-11-13 07:10:20'),
(63, '1MJ24CS408', 'BHATI MOHAMMED', 'Bha408', 1, NULL, NULL, '2025-11-13 07:10:20'),
(64, '1MJ24CS409', 'BHAVANA A V', 'Bha409', 1, NULL, NULL, '2025-11-13 07:10:20'),
(65, '1MJ24CS410', 'CHANDAN KUMAR V', 'Cha410', 1, NULL, NULL, '2025-11-13 07:10:20'),
(66, '1MJ24CS411', 'D S VISHESH DHYAN', 'Dsv411', 1, NULL, NULL, '2025-11-13 07:10:20'),
(67, '1MJ24CS412', 'DARSHAN B', 'Dar412', 1, NULL, NULL, '2025-11-13 07:10:20'),
(68, '1MJ24CS413', 'DARSHAN C', 'Dar413', 1, NULL, NULL, '2025-11-13 07:10:20'),
(69, '1MJ23CS055', 'GAAZIL ZAHID', 'Gaa055', 2, NULL, NULL, '2025-11-13 07:10:20'),
(70, '1MJ23CS056', 'GANESH N', 'Gan056', 2, NULL, NULL, '2025-11-13 07:10:20'),
(71, '1MJ23CS057', 'GANESH V', 'Gan057', 2, NULL, NULL, '2025-11-13 07:10:20'),
(72, '1MJ23CS058', 'GANESHA TEJASWI', 'Gan058', 2, NULL, NULL, '2025-11-13 07:10:20'),
(73, '1MJ23CS059', 'GEETHA G S', 'Gee059', 2, NULL, NULL, '2025-11-13 07:10:20'),
(74, '1MJ23CS060', 'ANIKET GORTY', 'Ani060', 2, NULL, NULL, '2025-11-13 07:10:20'),
(75, '1MJ23CS061', 'GOWRI KRISHNAN NAIR', 'Gow061', 2, NULL, NULL, '2025-11-13 07:10:20'),
(76, '1MJ23CS062', 'RISHI', 'Ris062', 2, NULL, NULL, '2025-11-13 07:10:20'),
(77, '1MJ23CS063', 'ANANYA H D', 'Ana063', 2, NULL, NULL, '2025-11-13 07:10:20'),
(78, '1MJ23CS064', 'HANNAH DANIEL', 'Han064', 2, NULL, NULL, '2025-11-13 07:10:20'),
(79, '1MJ23CS065', 'HARSHITA', 'Har065', 2, NULL, NULL, '2025-11-13 07:10:20'),
(80, '1MJ23CS066', 'HARSHITH A', 'Har066', 2, NULL, NULL, '2025-11-13 07:10:20'),
(81, '1MJ23CS067', 'HARSHITHA PRASAD', 'Har067', 2, NULL, NULL, '2025-11-13 07:10:20'),
(82, '1MJ23CS068', 'HARSHITHA Y S', 'Har068', 2, NULL, NULL, '2025-11-13 07:10:20'),
(83, '1MJ23CS069', 'HEMANTH SHIDENUR', 'Hem069', 2, NULL, NULL, '2025-11-13 07:10:20'),
(84, '1MJ23CS070', 'HOLABASU B GANIGER', 'Hol070', 2, NULL, NULL, '2025-11-13 07:10:20'),
(85, '1MJ23CS071', 'HRISHITA DEY PURKAYASTHA', 'Hri071', 2, NULL, NULL, '2025-11-13 07:10:20'),
(86, '1MJ23CS072', 'IRFAN ULLA KHAN', 'Irf072', 2, NULL, NULL, '2025-11-13 07:10:20'),
(87, '1MJ23CS073', 'ISSAC NATHAN P', 'Iss073', 2, NULL, NULL, '2025-11-13 07:10:20'),
(88, '1MJ23CS074', 'ISHWAR P MANDI', 'Ish074', 2, NULL, NULL, '2025-11-13 07:10:20'),
(89, '1MJ23CS075', 'JEEVAN S', 'Jee075', 2, NULL, NULL, '2025-11-13 07:10:20'),
(90, '1MJ23CS076', 'JOHNAZ NADAF', 'Joh076', 2, NULL, NULL, '2025-11-13 07:10:20'),
(91, '1MJ23CS077', 'JEEVITHA', 'Jee077', 2, NULL, NULL, '2025-11-13 07:10:20'),
(92, '1MJ23CS078', 'K SANDEEP', 'K Sa078', 2, NULL, NULL, '2025-11-13 07:10:20'),
(93, '1MJ23CS079', 'KALPASHREE', 'Kal079', 2, NULL, NULL, '2025-11-13 07:10:20'),
(94, '1MJ23CS081', 'KEETHANA G', 'Kee081', 2, NULL, NULL, '2025-11-13 07:10:20'),
(95, '1MJ23CS082', 'KEERTHI VASAN V Y', 'Kee082', 2, NULL, NULL, '2025-11-13 07:10:20'),
(96, '1MJ23CS083', 'KHANAK JUTTIYAVAR', 'Kha083', 2, NULL, NULL, '2025-11-13 07:10:20'),
(97, '1MJ23CS084', 'KHUSHI SHARMA', 'Khu084', 2, NULL, NULL, '2025-11-13 07:10:20'),
(98, '1MJ23CS085', 'KIRAN RAJA N', 'Kir085', 2, NULL, NULL, '2025-11-13 07:10:20'),
(99, '1MJ23CS086', 'KRISH SHARMA', 'Kri086', 2, NULL, NULL, '2025-11-13 07:10:20'),
(100, '1MJ23CS087', 'KRUTHI S N', 'Kru087', 2, NULL, NULL, '2025-11-13 07:10:20'),
(101, '1MJ23CS088', 'KUSUM R', 'Kus088', 2, NULL, NULL, '2025-11-13 07:10:20'),
(102, '1MJ23CS089', 'LAKSHMESH B C', 'Lak089', 2, NULL, NULL, '2025-11-13 07:10:20'),
(103, '1MJ23CS090', 'LAKSHMI N', 'Lak090', 2, NULL, NULL, '2025-11-13 07:10:20'),
(104, '1MJ23CS091', 'LATHA C R', 'Lat091', 2, NULL, NULL, '2025-11-13 07:10:20'),
(105, '1MJ23CS092', 'LAVANYA', 'Lav092', 2, NULL, NULL, '2025-11-13 07:10:20'),
(106, '1MJ23CS093', 'LOKESH M', 'Lok093', 2, NULL, NULL, '2025-11-13 07:10:20'),
(107, '1MJ23CS094', 'MAHADEV', 'Mah094', 2, NULL, NULL, '2025-11-13 07:10:20'),
(108, '1MJ23CS095', 'MAITHRI R C', 'Mai095', 2, NULL, NULL, '2025-11-13 07:10:20'),
(109, '1MJ23CS096', 'MANASVI SUTAR', 'Man096', 2, NULL, NULL, '2025-11-13 07:10:20'),
(110, '1MJ23CS097', 'MANASVINI B V', 'Man097', 2, NULL, NULL, '2025-11-13 07:10:20'),
(111, '1MJ23CS098', 'MANDLE NAVDEEP YADAV', 'Man098', 2, NULL, NULL, '2025-11-13 07:10:20'),
(112, '1MJ23CS099', 'MANEET B', 'Man099', 2, NULL, NULL, '2025-11-13 07:10:20'),
(113, '1MJ23CS100', 'MANJUNATH M', 'Man100', 2, NULL, NULL, '2025-11-13 07:10:20'),
(114, '1MJ23CS101', 'MANJUNATH L', 'Man101', 2, NULL, NULL, '2025-11-13 07:10:20'),
(115, '1MJ23CS102', 'MANOGNA GOWDA', 'Man102', 2, NULL, NULL, '2025-11-13 07:10:20'),
(116, '1MJ23CS103', 'MANOHAR PRASAD', 'Man103', 2, NULL, NULL, '2025-11-13 07:10:20'),
(117, '1MJ23CS104', 'MANOHARA M', 'Man104', 2, NULL, NULL, '2025-11-13 07:10:20'),
(118, '1MJ23CS209', 'MEGHA M V', 'Meg209', 2, NULL, NULL, '2025-11-13 07:10:20'),
(119, '1MJ23CS210', 'SHUBHAM SHARMA', 'Shu210', 2, NULL, NULL, '2025-11-13 07:10:20'),
(120, '1MJ23CS211', 'SRUSHTI A', 'Sru211', 2, NULL, NULL, '2025-11-13 07:10:20'),
(121, '1MJ23CS212', 'ANWIN TOM REJI', 'Anw212', 2, NULL, NULL, '2025-11-13 07:10:20'),
(122, '1MJ23CS213', 'SANKET M HEGADE', 'San213', 2, NULL, NULL, '2025-11-13 07:10:20'),
(123, '1MJ23CS214', 'VYSHU N', 'Vys214', 2, NULL, NULL, '2025-11-13 07:10:20'),
(124, '1MJ24CS415', 'DOSHI SAYALI', 'Dos415', 2, NULL, NULL, '2025-11-13 07:10:20'),
(125, '1MJ24CS416', 'HARISH R', 'Har416', 2, NULL, NULL, '2025-11-13 07:10:20'),
(126, '1MJ24CS417', 'KEERTHI S Y', 'Kee417', 2, NULL, NULL, '2025-11-13 07:10:20'),
(127, '1MJ24CS418', 'ABDUL RAB SIDDIQUI', 'Abd418', 2, NULL, NULL, '2025-11-13 07:10:20'),
(128, '1MJ24CS419', 'KISHORE R', 'Kis419', 2, NULL, NULL, '2025-11-13 07:10:20'),
(129, '1MJ24CS420', 'KOTRESH K N', 'Kot420', 2, NULL, NULL, '2025-11-13 07:10:20'),
(130, '1MJ24CS421', 'KRUTHIKA R N', 'Kru421', 2, NULL, NULL, '2025-11-13 07:10:20'),
(131, '1MJ24CS422', 'KULSUM FATHIMA', 'Kul422', 2, NULL, NULL, '2025-11-13 07:10:20'),
(132, '1MJ24CS424', 'MALLIKARJUN HIREMATH', 'Mal424', 2, NULL, NULL, '2025-11-13 07:10:20'),
(133, '1MJ24CS425', 'MANJUNATH B GOGI', 'Man425', 2, NULL, NULL, '2025-11-13 07:10:20'),
(134, '1MJ24CS426', 'MAYUR', 'May426', 2, NULL, NULL, '2025-11-13 07:10:20'),
(135, '1MJ24CS427', 'MEHEK TAJ', 'Meh427', 2, NULL, NULL, '2025-11-13 07:10:20'),
(136, '1MJ24CS428', 'AWAIZ BAIZ', 'Awa428', 2, NULL, NULL, '2025-11-13 07:10:20'),
(137, '1MJ24CS429', 'MRUTHYUNJAY B K', 'Mru429', 2, NULL, NULL, '2025-11-13 07:10:20'),
(138, '1MJ24CS452', 'TAMANNA M', 'Tam452', 2, NULL, NULL, '2025-11-13 07:10:20'),
(139, '1MJ23CS106', 'MANUSHREE MAHESH NANDIHAL', 'Man106', 3, NULL, NULL, '2025-11-13 07:10:20'),
(140, '1MJ23CS107', 'MANVITH B M', 'Man107', 3, NULL, NULL, '2025-11-13 07:10:20'),
(141, '1MJ23CS108', 'MANYA U', 'Man108', 3, NULL, NULL, '2025-11-13 07:10:20'),
(142, '1MJ23CS109', 'MASSIL NISSAR', 'Mas109', 3, NULL, NULL, '2025-11-13 07:10:20'),
(143, '1MJ23CS110', 'MD NADEEM', 'MD N110', 3, NULL, NULL, '2025-11-13 07:10:20'),
(144, '1MJ23CS111', 'MOHAMMED RAHIL RAFEEK', 'Moh111', 3, NULL, NULL, '2025-11-13 07:10:20'),
(145, '1MJ23CS112', 'MOHAN GOWDA M', 'Moh112', 3, NULL, NULL, '2025-11-13 07:10:20'),
(146, '1MJ23CS113', 'MOHAN V', 'Moh113', 3, NULL, NULL, '2025-11-13 07:10:20'),
(147, '1MJ23CS114', 'MONIKA R', 'Mon114', 3, NULL, NULL, '2025-11-13 07:10:20'),
(148, '1MJ23CS115', 'MUKESH MADAWAT', 'Muk115', 3, NULL, NULL, '2025-11-13 07:10:20'),
(149, '1MJ23CS116', 'MUSSARRAT KITTUR', 'Mus116', 3, NULL, NULL, '2025-11-13 07:10:20'),
(150, '1MJ23CS117', 'NAGASHREE AVADHANI', 'Nag117', 3, NULL, NULL, '2025-11-13 07:10:20'),
(151, '1MJ23CS118', 'NAKSHATRA', 'Nak118', 3, NULL, NULL, '2025-11-13 07:10:20'),
(152, '1MJ23CS119', 'NANI N', 'Nan119', 3, NULL, NULL, '2025-11-13 07:10:20'),
(153, '1MJ23CS120', 'NAVEENA C A', 'Nav120', 3, NULL, NULL, '2025-11-13 07:10:20'),
(154, '1MJ23CS121', 'NAYAN JAIN', 'Nay121', 3, NULL, NULL, '2025-11-13 07:10:20'),
(155, '1MJ23CS122', 'NAYANA S', 'Nay122', 3, NULL, NULL, '2025-11-13 07:10:20'),
(156, '1MJ23CS123', 'NEETHU REDDY P', 'Nee123', 3, NULL, NULL, '2025-11-13 07:10:20'),
(157, '1MJ23CS124', 'NEHA DEEPAK MALI', 'Neh124', 3, NULL, NULL, '2025-11-13 07:10:20'),
(158, '1MJ23CS126', 'NIKITHA G M', 'Nik126', 3, NULL, NULL, '2025-11-13 07:10:20'),
(159, '1MJ23CS127', 'NISHA N', 'Nis127', 3, NULL, NULL, '2025-11-13 07:10:20'),
(160, '1MJ23CS128', 'PALLAVI R', 'Pal128', 3, NULL, NULL, '2025-11-13 07:10:20'),
(161, '1MJ23CS129', 'PAVAN KUMAR S H', 'Pav129', 3, NULL, NULL, '2025-11-13 07:10:20'),
(162, '1MJ23CS130', 'PAVANI S', 'Pav130', 3, NULL, NULL, '2025-11-13 07:10:20'),
(163, '1MJ23CS131', 'POOJA SINGH G', 'Poo131', 3, NULL, NULL, '2025-11-13 07:10:20'),
(164, '1MJ23CS132', 'POORVIETHA R', 'Poo132', 3, NULL, NULL, '2025-11-13 07:10:20'),
(165, '1MJ23CS133', 'PRABJYOT SINGH', 'Pra133', 3, NULL, NULL, '2025-11-13 07:10:20'),
(166, '1MJ23CS134', 'PRAJWAL PUNDALIK MANGAJI', 'Pra134', 3, NULL, NULL, '2025-11-13 07:10:20'),
(167, '1MJ23CS135', 'PRAKRUTHI S', 'Pra135', 3, NULL, NULL, '2025-11-13 07:10:20'),
(168, '1MJ23CS136', 'PRASANNA RAJENDRAN', 'Pra136', 3, NULL, NULL, '2025-11-13 07:10:20'),
(169, '1MJ23CS137', 'PRATHAM SACHIN KHAIRMODE', 'Pra137', 3, NULL, NULL, '2025-11-13 07:10:20'),
(170, '1MJ23CS138', 'PUJARI JAYA SAI', 'Puj138', 3, NULL, NULL, '2025-11-13 07:10:20'),
(171, '1MJ23CS139', 'R NIRANJAN', 'R Ni139', 3, NULL, NULL, '2025-11-13 07:10:20'),
(172, '1MJ23CS140', 'R V LEHENYA', 'R V140', 3, NULL, NULL, '2025-11-13 07:10:20'),
(173, '1MJ23CS141', 'RAAGAV KRISHNAN', 'Raa141', 3, NULL, NULL, '2025-11-13 07:10:20'),
(174, '1MJ23CS142', 'RACHITHA V', 'Rac142', 3, NULL, NULL, '2025-11-13 07:10:20'),
(175, '1MJ23CS143', 'RAGHAVENDRA', 'Rag143', 3, NULL, NULL, '2025-11-13 07:10:20'),
(176, '1MJ23CS144', 'RAGHAVENDRA S', 'Rag144', 3, NULL, NULL, '2025-11-13 07:10:20'),
(177, '1MJ23CS145', 'RAHUL S', 'Rah145', 3, NULL, NULL, '2025-11-13 07:10:20'),
(178, '1MJ23CS146', 'RAKSHITA M SHETTI', 'Rak146', 3, NULL, NULL, '2025-11-13 07:10:20'),
(179, '1MJ23CS147', 'RAKSHITHA JM', 'Rak147', 3, NULL, NULL, '2025-11-13 07:10:20'),
(180, '1MJ23CS148', 'RAKSHITHA N', 'Rak148', 3, NULL, NULL, '2025-11-13 07:10:20'),
(181, '1MJ23CS149', 'RAKSHITHA TEJA REDDY', 'Rak149', 3, NULL, NULL, '2025-11-13 07:10:20'),
(182, '1MJ23CS150', 'RANA BISWAS', 'Ran150', 3, NULL, NULL, '2025-11-13 07:10:20'),
(183, '1MJ23CS152', 'RINNAH JOHN', 'Rin152', 3, NULL, NULL, '2025-11-13 07:10:20'),
(184, '1MJ23CS153', 'ROHIT MOHAN', 'Roh153', 3, NULL, NULL, '2025-11-13 07:10:20'),
(185, '1MJ23CS154', 'ROHIT PADARA', 'Roh154', 3, NULL, NULL, '2025-11-13 07:10:20'),
(186, '1MJ23CS156', 'S MANOJ KUMAR', 'S Ma156', 3, NULL, NULL, '2025-11-13 07:10:20'),
(187, '1MJ23CS215', 'A K RENU', 'A K215', 3, NULL, NULL, '2025-11-13 07:10:20'),
(188, '1MJ23CS216', 'RITESH BATAKURKI', 'Rit216', 3, NULL, NULL, '2025-11-13 07:10:20'),
(189, '1MJ23CS217', 'LAKSHMI N', 'Lak217', 3, NULL, NULL, '2025-11-13 07:10:20'),
(190, '1MJ23CS218', 'RAHUL BS', 'Rah218', 3, NULL, NULL, '2025-11-13 07:10:20'),
(191, '1MJ24CS423', 'MAHATEJASWI', 'Mah423', 3, NULL, NULL, '2025-11-13 07:10:20'),
(192, '1MJ24CS430', 'NAKUL', 'Nak430', 3, NULL, NULL, '2025-11-13 07:10:20'),
(193, '1MJ24CS431', 'NITHIN AIHOLE', 'Nit431', 3, NULL, NULL, '2025-11-13 07:10:20'),
(194, '1MJ24CS432', 'OMKAR SHARAD VERNEKAR', 'Omk432', 3, NULL, NULL, '2025-11-13 07:10:20'),
(195, '1MJ24CS433', 'PALLAVI N PEJOLLI', 'Pal433', 3, NULL, NULL, '2025-11-13 07:10:20'),
(196, '1MJ24CS434', 'PAVAN KUMAR ITEKAR', 'Pav434', 3, NULL, NULL, '2025-11-13 07:10:20'),
(197, '1MJ24CS435', 'PAVAN NITESH', 'Pav435', 3, NULL, NULL, '2025-11-13 07:10:20'),
(198, '1MJ24CS436', 'PREETAM KULKARNI', 'Pre436', 3, NULL, NULL, '2025-11-13 07:10:20'),
(199, '1MJ24CS437', 'PRIYANKA S SHELLIGERI', 'Pri437', 3, NULL, NULL, '2025-11-13 07:10:20'),
(200, '1MJ24CS438', 'PRIYANSHU PAUL', 'Pri438', 3, NULL, NULL, '2025-11-13 07:10:20'),
(201, '1MJ24CS439', 'PRUTHVI N GOWDA', 'Pru439', 3, NULL, NULL, '2025-11-13 07:10:20'),
(202, '1MJ24CS440', 'RADHIKA METAGAR', 'Rad440', 3, NULL, NULL, '2025-11-13 07:10:20'),
(203, '1MJ24CS441', 'SACHIN V SAGARNAL', 'Sac441', 3, NULL, NULL, '2025-11-13 07:10:20'),
(204, '1MJ24CS442', 'SAEED SHAIKH', 'Sae442', 3, NULL, NULL, '2025-11-13 07:10:20'),
(205, '1MJ24CS443', 'SANTOSHA D', 'San443', 3, NULL, NULL, '2025-11-13 07:10:20'),
(206, '1MJ24CS453', 'TARUN S', 'Tar453', 3, NULL, NULL, '2025-11-13 07:10:20'),
(207, '1MJ23CS157', 'S MRIGNAYA SRI', 'S Mr157', 4, NULL, NULL, '2025-11-13 07:10:20'),
(208, '1MJ23CS158', 'S NAREN KUMAR', 'S Na158', 4, NULL, NULL, '2025-11-13 07:10:20'),
(209, '1MJ23CS159', 'S PAVAN', 'S Pa159', 4, NULL, NULL, '2025-11-13 07:10:20'),
(210, '1MJ23CS160', 'SACHIN KUMAR PATEL', 'Sac160', 4, NULL, NULL, '2025-11-13 07:10:20'),
(211, '1MJ23CS161', 'JAYASIMHA REDDY K', 'Jay161', 4, NULL, NULL, '2025-11-13 07:10:20'),
(212, '1MJ23CS162', 'ANOOP V', 'Ano162', 4, NULL, NULL, '2025-11-13 07:10:20'),
(213, '1MJ23CS163', 'SATYAM', 'Sat163', 4, NULL, NULL, '2025-11-13 07:10:20'),
(214, '1MJ23CS164', 'SANTOSH VARMA', 'San164', 4, NULL, NULL, '2025-11-13 07:10:20'),
(215, '1MJ23CS165', 'SAPTAPARNO GHOSH', 'Sap165', 4, NULL, NULL, '2025-11-13 07:10:20'),
(216, '1MJ23CS168', 'SHAILA P B', 'Sha168', 4, NULL, NULL, '2025-11-13 07:10:20'),
(217, '1MJ23CS169', 'SHALINI KANNAN', 'Sha169', 4, NULL, NULL, '2025-11-13 07:10:20'),
(218, '1MJ23CS170', 'SHABISTA SEHAR', 'Sha170', 4, NULL, NULL, '2025-11-13 07:10:20'),
(219, '1MJ23CS171', 'SHARAN KUMAR R', 'Sha171', 4, NULL, NULL, '2025-11-13 07:10:20'),
(220, '1MJ23CS172', 'SHRUSTI G', 'Shr172', 4, NULL, NULL, '2025-11-13 07:10:20'),
(221, '1MJ23CS173', 'SHASHANK J', 'Sha173', 4, NULL, NULL, '2025-11-13 07:10:20'),
(222, '1MJ23CS174', 'SHILPA A P', 'Shi174', 4, NULL, NULL, '2025-11-13 07:10:20'),
(223, '1MJ23CS175', 'SHRAVAN RAMAKUNJA', 'Shr175', 4, NULL, NULL, '2025-11-13 07:10:20'),
(224, '1MJ23CS176', 'SHREERAKSHA', 'Shr176', 4, NULL, NULL, '2025-11-13 07:10:20'),
(225, '1MJ23CS177', 'SHREYAS V', 'Shr177', 4, NULL, NULL, '2025-11-13 07:10:20'),
(226, '1MJ23CS183', 'SINCHANA', 'Sin183', 4, NULL, NULL, '2025-11-13 07:10:20'),
(227, '1MJ23CS184', 'SINCHANA S D', 'Sin184', 4, NULL, NULL, '2025-11-13 07:10:20'),
(228, '1MJ23CS185', 'SOUJANYA', 'Sou185', 4, NULL, NULL, '2025-11-13 07:10:20'),
(229, '1MJ23CS186', 'SRUJAN', 'Sru186', 4, NULL, NULL, '2025-11-13 07:10:20'),
(230, '1MJ23CS187', 'SUJAY', 'Suj187', 4, NULL, NULL, '2025-11-13 07:10:20'),
(231, '1MJ23CS188', 'SUKANYA', 'Suk188', 4, NULL, NULL, '2025-11-13 07:10:20'),
(232, '1MJ23CS189', 'SUPREET K', 'Sup189', 4, NULL, NULL, '2025-11-13 07:10:20'),
(233, '1MJ23CS190', 'SHRISHAIL H', 'Shr190', 4, NULL, NULL, '2025-11-13 07:10:20'),
(234, '1MJ23CS191', 'SHUBHAM SHARMA', 'Shu191', 4, NULL, NULL, '2025-11-13 07:10:20'),
(235, '1MJ23CS192', 'SIDDAJI', 'Sid192', 4, NULL, NULL, '2025-11-13 07:10:20'),
(236, '1MJ23CS193', 'THARUN KUMAR V R', 'Tha193', 4, NULL, NULL, '2025-11-13 07:10:20'),
(237, '1MJ23CS194', 'UDAY SHARMA', 'Uda194', 4, NULL, NULL, '2025-11-13 07:10:20'),
(238, '1MJ23CS195', 'UJJAWALA R SINGH', 'Ujj195', 4, NULL, NULL, '2025-11-13 07:10:20'),
(239, '1MJ23CS196', 'V NIVETHA', 'V Ni196', 4, NULL, NULL, '2025-11-13 07:10:20'),
(240, '1MJ23CS197', 'VANDANA LY', 'Van197', 4, NULL, NULL, '2025-11-13 07:10:20'),
(241, '1MJ23CS198', 'VARSHA NAIK', 'Var198', 4, NULL, NULL, '2025-11-13 07:10:20'),
(242, '1MJ23CS199', 'VIBHA GS', 'Vib199', 4, NULL, NULL, '2025-11-13 07:10:20'),
(243, '1MJ23CS200', 'VIGNESH SHETTY', 'Vig200', 4, NULL, NULL, '2025-11-13 07:10:20'),
(244, '1MJ23CS201', 'VIKAS BENNUR', 'Vik201', 4, NULL, NULL, '2025-11-13 07:10:20'),
(245, '1MJ23CS202', 'VIKAS S REDDY', 'Vik202', 4, NULL, NULL, '2025-11-13 07:10:20'),
(246, '1MJ23CS203', 'VINUTHA YM', 'Vin203', 4, NULL, NULL, '2025-11-13 07:10:20'),
(247, '1MJ23CS204', 'TARUN E', 'Tar204', 4, NULL, NULL, '2025-11-13 07:10:20'),
(248, '1MJ23CS205', 'VISHWARADYA', 'Vis205', 4, NULL, NULL, '2025-11-13 07:10:20'),
(249, '1MJ23CS206', 'YAMUNA H S', 'Yam206', 4, NULL, NULL, '2025-11-13 07:10:20'),
(250, '1MJ23CS207', 'YASHAS REDDY T S', 'Yas207', 4, NULL, NULL, '2025-11-13 07:10:20'),
(251, '1MJ23CS221', 'VISHAL B S', 'Vis221', 4, NULL, NULL, '2025-11-13 07:10:20'),
(252, '1MJ23CS223', 'SHASHI KUMAR B V', 'Sha223', 4, NULL, NULL, '2025-11-13 07:10:20'),
(253, '1MJ23CS224', 'P ROHIT', 'P Ro224', 4, NULL, NULL, '2025-11-13 07:10:20'),
(254, '1MJ24CS444', 'SANTOSHAKUMAR K', 'San444', 4, NULL, NULL, '2025-11-13 07:10:20'),
(255, '1MJ24CS445', 'SARTHAK PAI', 'Sar445', 4, NULL, NULL, '2025-11-13 07:10:20'),
(256, '1MJ24CS446', 'SHARAN R B', 'Sha446', 4, NULL, NULL, '2025-11-13 07:10:20'),
(257, '1MJ24CS447', 'SHIVSAIGANESH', 'Shi447', 4, NULL, NULL, '2025-11-13 07:10:20'),
(258, '1MJ24CS448', 'SOWMYA G', 'Sow448', 4, NULL, NULL, '2025-11-13 07:10:20'),
(259, '1MJ24CS449', 'SPURTI DHANYAKUMAR PATIL', 'Spu449', 4, NULL, NULL, '2025-11-13 07:10:20'),
(260, '1MJ24CS450', 'SRINIVASAN U', 'Sri450', 4, NULL, NULL, '2025-11-13 07:10:20'),
(261, '1MJ24CS451', 'SURYA R', 'Sur451', 4, NULL, NULL, '2025-11-13 07:10:20'),
(262, '1MJ24CS454', 'VARSHA SALLY A', 'Var454', 4, NULL, NULL, '2025-11-13 07:10:20'),
(263, '1MJ24CS455', 'VINAY KUMAR REDDY', 'Vin455', 4, NULL, NULL, '2025-11-13 07:10:20'),
(264, '1MJ24CS456', 'Y CHARAN', 'Y Ch456', 4, NULL, NULL, '2025-11-13 07:10:20'),
(265, '1MJ24CS457', 'Y RAJENDRA', 'Y Ra457', 4, NULL, NULL, '2025-11-13 07:10:20'),
(266, '1MJ24CS458', 'YASHASHWINI V S', 'Yas458', 4, NULL, NULL, '2025-11-13 07:10:20');

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
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

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
