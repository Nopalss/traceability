-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 30, 2026 at 06:21 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `traceability_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_active_material`
--

CREATE TABLE `tbl_active_material` (
  `id` int NOT NULL,
  `part_code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lot_no` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `spq` int DEFAULT NULL,
  `remain` int DEFAULT NULL,
  `ref_number` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `line_id` int DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `part_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_active_material`
--

INSERT INTO `tbl_active_material` (`id`, `part_code`, `lot_no`, `spq`, `remain`, `ref_number`, `line_id`, `updated_at`, `part_id`) VALUES
(29, '169390101', '01725Z22D1Z1111', 900, 9692, 'HECIYS8OWYHJ1J111', 7, '2026-04-30 10:00:18', 1),
(30, '172503000', '01725Z22D1Z222', 900, 9636, 'HECIYS8OWYHJ1J222', 7, '2026-04-30 10:00:18', 2),
(31, '169455303', '01725Z22D1Z333', 900, 9860, 'HECIYS8OWYHJ1J333', 7, '2026-04-30 10:00:18', 3),
(32, '217711301', '01725Z22D1Z444', 900, 9860, 'HECIYS8OWYHJ1J444', 7, '2026-04-30 10:00:18', 4),
(33, '169387700', '01725Z22D1Z888', 900, 9860, 'HECIYS8OWYHJ1J888', 7, '2026-04-30 10:00:18', 8),
(34, '218496702', '01725Z22D1Z1000', 900, 9860, 'HECIYS8OWYHJ1J1000', 7, '2026-04-30 10:00:18', 10),
(35, '169387600', '01725Z22D1Z1003', 900, 9860, 'HECIYS8OWYHJ1J1003', 7, '2026-04-30 10:00:18', 13),
(36, '128237800', '01725Z22D1Z1004', 900, 9604, 'HECIYS8OWYHJ1J1004', 7, '2026-04-30 10:00:18', 14),
(37, '189358800', '01725Z22D1Z1005', 10000, 9860, 'HECIYS8OWYHJ1J1005', 7, '2026-04-30 10:00:18', 15);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_part`
--

CREATE TABLE `tbl_detail_part` (
  `ref_number` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `remain` int NOT NULL,
  `incoming_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lot_no` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_part`
--

INSERT INTO `tbl_detail_part` (`ref_number`, `part_code`, `qty`, `remain`, `incoming_date`, `status`, `lot_no`, `remarks`, `part_id`) VALUES
('HECIYS8OWYHJ1J1000', '218496702', 10000, 9860, '2026-04-26 12:32:45', 'USED', '01725Z22D1Z1000', 'XYRON Z552H OK-X6922 {A1A3301}', 10),
('HECIYS8OWYHJ1J1001', '209056603', 10000, 10000, '2026-04-26 12:32:51', 'IN', '01725Z22D1Z1001', 'XYRON Z552H OK-X6922 {A1A3301}', 11),
('HECIYS8OWYHJ1J1002', '169387500', 10000, 10000, '2026-04-26 12:33:03', 'IN', '01725Z22D1Z1002', 'XYRON Z552H OK-X6922 {A1A3301}', 12),
('HECIYS8OWYHJ1J1003', '169387600', 10000, 9860, '2026-04-26 12:33:09', 'USED', '01725Z22D1Z1003', 'XYRON Z552H OK-X6922 {A1A3301}', 13),
('HECIYS8OWYHJ1J1004', '128237800', 10000, 9604, '2026-04-26 12:36:13', 'USED', '01725Z22D1Z1004', 'XYRON Z552H OK-X6922 {A1A3301}', 14),
('HECIYS8OWYHJ1J1005', '189358800', 10000, 9860, '2026-04-30 03:32:33', 'USED', '01725Z22D1Z1005', 'XYRON Z552H OK-X6922 {A1A3301}', 15),
('HECIYS8OWYHJ1J111', '169390101', 10000, 9692, '2026-04-26 12:30:21', 'USED', '01725Z22D1Z1111', 'XYRON Z552H OK-X6922 {A1A3301}', 1),
('HECIYS8OWYHJ1J222', '172503000', 10000, 9636, '2026-04-26 12:30:48', 'USED', '01725Z22D1Z222', 'XYRON Z552H OK-X6922 {A1A3301}', 2),
('HECIYS8OWYHJ1J333', '169455303', 10000, 9860, '2026-04-26 12:31:05', 'USED', '01725Z22D1Z333', 'XYRON Z552H OK-X6922 {A1A3301}', 3),
('HECIYS8OWYHJ1J444', '217711301', 10000, 9860, '2026-04-26 12:31:21', 'USED', '01725Z22D1Z444', 'XYRON Z552H OK-X6922 {A1A3301}', 4),
('HECIYS8OWYHJ1J555', '221065900', 10000, 10000, '2026-04-26 12:31:29', 'IN', '01725Z22D1Z555', 'XYRON Z552H OK-X6922 {A1A3301}', 5),
('HECIYS8OWYHJ1J666', '212685700', 10000, 10000, '2026-04-26 12:31:41', 'IN', '01725Z22D1Z666', 'XYRON Z552H OK-X6922 {A1A3301}', 6),
('HECIYS8OWYHJ1J777', '159352600', 10000, 10000, '2026-04-26 12:32:06', 'IN', '01725Z22D1Z777', 'XYRON Z552H OK-X6922 {A1A3301}', 7),
('HECIYS8OWYHJ1J888', '169387700', 10000, 9860, '2026-04-26 12:32:21', 'USED', '01725Z22D1Z888', 'XYRON Z552H OK-X6922 {A1A3301}', 8),
('HECIYS8OWYHJ1J999', '189427200', 10000, 10000, '2026-04-26 12:32:31', 'IN', '01725Z22D1Z999', 'XYRON Z552H OK-X6922 {A1A3301}', 9);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_product`
--

CREATE TABLE `tbl_detail_product` (
  `id` int NOT NULL,
  `product_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serial_no` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `shift` int DEFAULT NULL,
  `line_id` int DEFAULT NULL,
  `operator` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_number` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `remarks` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `operator_remark` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('in','out') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'in',
  `location` int DEFAULT '0',
  `out_date` datetime DEFAULT NULL,
  `is_ng` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_product`
--

INSERT INTO `tbl_detail_product` (`id`, `product_code`, `serial_no`, `qty`, `shift`, `line_id`, `operator`, `ref_number`, `remarks`, `operator_remark`, `created_at`, `status`, `location`, `out_date`, `is_ng`) VALUES
(2, '1A4215700', '01725Z22D1Z001COBA', 28, 1, 7, 'operator', 'HECIYS8OWYHJ1JCOBA1', 'XYRON Z552H OK-X6922 {A1A3301}', '', '2026-04-30 09:02:27', 'out', 23, '2026-04-30 10:51:14', 0),
(3, '1A4215700', '01725Z22D1Z001COBA', 28, 1, 7, 'operator', 'HECIYS8OWYHJ1JCOBA2', 'XYRON Z552H OK-X6922 {A1A3301}', '', '2026-04-30 09:17:07', 'in', 0, NULL, 0),
(4, '1A4215700', '01725Z22D1Z001COBA', 28, 1, 7, 'operator', 'HECIYS8OWYHJ1JCOBA3', 'XYRON Z552H OK-X6922 {A1A3301}', '', '2026-04-30 09:50:37', 'in', 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_production`
--

CREATE TABLE `tbl_detail_production` (
  `id` int NOT NULL,
  `product_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serial_no` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `part_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `used_qty` int DEFAULT NULL,
  `lot_no` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `exit_meca` tinyint DEFAULT '0',
  `ref_number` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `ref_product` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_production`
--

INSERT INTO `tbl_detail_production` (`id`, `product_code`, `serial_no`, `part_code`, `used_qty`, `lot_no`, `created_at`, `exit_meca`, `ref_number`, `ref_product`) VALUES
(19, '1A4215700', '01725Z22D1Z001COBA', '169390101', 112, '01725Z22D1Z1111', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J111', 'HECIYS8OWYHJ1JCOBA1'),
(20, '1A4215700', '01725Z22D1Z001COBA', '172503000', 56, '01725Z22D1Z222', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J222', 'HECIYS8OWYHJ1JCOBA1'),
(21, '1A4215700', '01725Z22D1Z001COBA', '169455303', 28, '01725Z22D1Z333', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J333', 'HECIYS8OWYHJ1JCOBA1'),
(22, '1A4215700', '01725Z22D1Z001COBA', '217711301', 28, '01725Z22D1Z444', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J444', 'HECIYS8OWYHJ1JCOBA1'),
(23, '1A4215700', '01725Z22D1Z001COBA', '169387700', 28, '01725Z22D1Z888', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J888', 'HECIYS8OWYHJ1JCOBA1'),
(24, '1A4215700', '01725Z22D1Z001COBA', '218496702', 28, '01725Z22D1Z1000', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J1000', 'HECIYS8OWYHJ1JCOBA1'),
(25, '1A4215700', '01725Z22D1Z001COBA', '169387600', 28, '01725Z22D1Z1003', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J1003', 'HECIYS8OWYHJ1JCOBA1'),
(26, '1A4215700', '01725Z22D1Z001COBA', '128237800', 28, '01725Z22D1Z1004', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J1004', 'HECIYS8OWYHJ1JCOBA1'),
(27, '1A4215700', '01725Z22D1Z001COBA', '189358800', 28, '01725Z22D1Z1005', '2026-04-30 09:15:37', 0, 'HECIYS8OWYHJ1J1005', 'HECIYS8OWYHJ1JCOBA1'),
(37, '1A4215700', '01725Z22D1Z001COBA', '169390101', 112, '01725Z22D1Z1111', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J111', 'HECIYS8OWYHJ1JCOBA2'),
(38, '1A4215700', '01725Z22D1Z001COBA', '172503000', 56, '01725Z22D1Z222', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J222', 'HECIYS8OWYHJ1JCOBA2'),
(39, '1A4215700', '01725Z22D1Z001COBA', '169455303', 28, '01725Z22D1Z333', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J333', 'HECIYS8OWYHJ1JCOBA2'),
(40, '1A4215700', '01725Z22D1Z001COBA', '217711301', 28, '01725Z22D1Z444', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J444', 'HECIYS8OWYHJ1JCOBA2'),
(41, '1A4215700', '01725Z22D1Z001COBA', '169387700', 28, '01725Z22D1Z888', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J888', 'HECIYS8OWYHJ1JCOBA2'),
(42, '1A4215700', '01725Z22D1Z001COBA', '218496702', 28, '01725Z22D1Z1000', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J1000', 'HECIYS8OWYHJ1JCOBA2'),
(43, '1A4215700', '01725Z22D1Z001COBA', '169387600', 28, '01725Z22D1Z1003', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J1003', 'HECIYS8OWYHJ1JCOBA2'),
(44, '1A4215700', '01725Z22D1Z001COBA', '128237800', 28, '01725Z22D1Z1004', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J1004', 'HECIYS8OWYHJ1JCOBA2'),
(45, '1A4215700', '01725Z22D1Z001COBA', '189358800', 28, '01725Z22D1Z1005', '2026-04-30 09:19:57', 0, 'HECIYS8OWYHJ1J1005', 'HECIYS8OWYHJ1JCOBA2'),
(55, '1A4215700', '01725Z22D1Z001COBA', '128237800', 112, '01725Z22D1Z1004', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J1004', 'HECIYS8OWYHJ1JCOBA3'),
(56, '1A4215700', '01725Z22D1Z001COBA', '169387600', 28, '01725Z22D1Z1003', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J1003', 'HECIYS8OWYHJ1JCOBA3'),
(57, '1A4215700', '01725Z22D1Z001COBA', '169387700', 28, '01725Z22D1Z888', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J888', 'HECIYS8OWYHJ1JCOBA3'),
(58, '1A4215700', '01725Z22D1Z001COBA', '169390101', 28, '01725Z22D1Z1111', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J111', 'HECIYS8OWYHJ1JCOBA3'),
(59, '1A4215700', '01725Z22D1Z001COBA', '169455303', 28, '01725Z22D1Z333', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J333', 'HECIYS8OWYHJ1JCOBA3'),
(60, '1A4215700', '01725Z22D1Z001COBA', '172503000', 84, '01725Z22D1Z222', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J222', 'HECIYS8OWYHJ1JCOBA3'),
(61, '1A4215700', '01725Z22D1Z001COBA', '189358800', 28, '01725Z22D1Z1005', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J1005', 'HECIYS8OWYHJ1JCOBA3'),
(62, '1A4215700', '01725Z22D1Z001COBA', '217711301', 28, '01725Z22D1Z444', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J444', 'HECIYS8OWYHJ1JCOBA3'),
(63, '1A4215700', '01725Z22D1Z001COBA', '218496702', 28, '01725Z22D1Z1000', '2026-04-30 10:00:18', 0, 'HECIYS8OWYHJ1J1000', 'HECIYS8OWYHJ1JCOBA3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_production_planning`
--

CREATE TABLE `tbl_detail_production_planning` (
  `id` int NOT NULL,
  `pp_id` int NOT NULL,
  `jam` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `actual` int NOT NULL DEFAULT '0',
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_production_planning`
--

INSERT INTO `tbl_detail_production_planning` (`id`, `pp_id`, `jam`, `qty`, `actual`, `status`) VALUES
(1, 1, '07:00-08:00', 60, 0, 'planned'),
(2, 1, '08:00-09:00', 60, 0, 'planned'),
(3, 1, '09:00-10:00', 50, 0, 'planned'),
(4, 1, '10:00-11:00', 30, 0, 'planned'),
(5, 1, '11:00-12:00', 0, 0, 'planned'),
(6, 1, '12:00-13:00', 0, 0, 'planned'),
(7, 1, '13:00-14:00', 0, 0, 'planned'),
(8, 1, '14:00-15:00', 0, 0, 'planned'),
(9, 1, 'OT', 0, 0, 'planned'),
(10, 2, '07:00-08:00', 60, 0, 'planned'),
(11, 2, '08:00-09:00', 60, 0, 'planned'),
(12, 2, '09:00-10:00', 50, 0, 'planned'),
(13, 2, '10:00-11:00', 30, 0, 'planned'),
(14, 2, '11:00-12:00', 0, 0, 'planned'),
(15, 2, '12:00-13:00', 0, 0, 'planned'),
(16, 2, '13:00-14:00', 0, 0, 'planned'),
(17, 2, '14:00-15:00', 0, 0, 'planned'),
(18, 2, 'OT', 0, 0, 'planned'),
(19, 3, '15:00-16:00', 60, 0, 'planned'),
(20, 3, '16:00-17:00', 60, 0, 'planned'),
(21, 3, '17:00-18:00', 50, 0, 'planned'),
(22, 3, '18:00-19:00', 30, 0, 'planned'),
(23, 3, '19:00-20:00', 0, 0, 'planned'),
(24, 3, '20:00-21:00', 0, 0, 'planned'),
(25, 3, '21:00-22:00', 0, 0, 'planned'),
(26, 3, '22:00-23:00', 0, 0, 'planned'),
(27, 3, 'OT', 0, 0, 'planned'),
(28, 4, '15:00-16:00', 0, 0, 'planned'),
(29, 4, '16:00-17:00', 0, 0, 'planned'),
(30, 4, '17:00-18:00', 0, 0, 'planned'),
(31, 4, '18:00-19:00', 30, 0, 'planned'),
(32, 4, '19:00-20:00', 30, 0, 'planned'),
(33, 4, '20:00-21:00', 20, 0, 'planned'),
(34, 4, '21:00-22:00', 0, 0, 'planned'),
(35, 4, '22:00-23:00', 0, 0, 'planned'),
(36, 4, 'OT', 0, 0, 'planned'),
(37, 5, '23:00-00:00', 60, 0, 'planned'),
(38, 5, '00:00-01:00', 60, 0, 'planned'),
(39, 5, '01:00-02:00', 60, 0, 'planned'),
(40, 5, '02:00-03:00', 20, 0, 'planned'),
(41, 5, '03:00-04:00', 0, 0, 'planned'),
(42, 5, '04:00-05:00', 0, 0, 'planned'),
(43, 5, '05:00-06:00', 0, 0, 'planned'),
(44, 5, '06:00-07:00', 0, 0, 'planned'),
(45, 5, 'OT', 0, 0, 'planned'),
(46, 6, '23:00-00:00', 0, 0, 'planned'),
(47, 6, '00:00-01:00', 0, 0, 'planned'),
(48, 6, '01:00-02:00', 0, 0, 'planned'),
(49, 6, '02:00-03:00', 40, 0, 'planned'),
(50, 6, '03:00-04:00', 20, 0, 'planned'),
(51, 6, '04:00-05:00', 0, 0, 'planned'),
(52, 6, '05:00-06:00', 0, 0, 'planned'),
(53, 6, '06:00-07:00', 0, 0, 'planned'),
(54, 6, 'OT', 0, 0, 'planned'),
(55, 7, '07:00-08:00', 62, 62, 'planned'),
(56, 7, '08:00-09:00', 61, 22, 'planned'),
(57, 7, '09:00-10:00', 50, 0, 'planned'),
(58, 7, '10:00-11:00', 27, 0, 'planned'),
(59, 7, '11:00-12:00', 0, 0, 'planned'),
(60, 7, '12:00-13:00', 0, 0, 'planned'),
(61, 7, '13:00-14:00', 0, 0, 'planned'),
(62, 7, '14:00-15:00', 0, 0, 'planned'),
(63, 7, 'OT', 0, 0, 'planned'),
(64, 8, '07:00-08:00', 0, 0, 'planned'),
(65, 8, '08:00-09:00', 0, 0, 'planned'),
(66, 8, '09:00-10:00', 0, 0, 'planned'),
(67, 8, '10:00-11:00', 35, 0, 'planned'),
(68, 8, '11:00-12:00', 51, 0, 'planned'),
(69, 8, '12:00-13:00', 20, 0, 'planned'),
(70, 8, '13:00-14:00', 61, 0, 'planned'),
(71, 8, '14:00-15:00', 33, 0, 'planned'),
(72, 8, 'OT', 0, 0, 'planned'),
(73, 9, '07:00-08:00', 60, 0, 'planned'),
(74, 9, '08:00-09:00', 60, 0, 'planned'),
(75, 9, '09:00-10:00', 50, 0, 'planned'),
(76, 9, '10:00-11:00', 30, 0, 'planned'),
(77, 9, '11:00-12:00', 0, 0, 'planned'),
(78, 9, '12:00-13:00', 0, 0, 'planned'),
(79, 9, '13:00-14:00', 0, 0, 'planned'),
(80, 9, '14:00-15:00', 0, 0, 'planned'),
(81, 9, 'OT', 0, 0, 'planned');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_detail_products`
--

CREATE TABLE `tbl_detail_products` (
  `ref_number` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `production_date` date NOT NULL,
  `status` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lot_no` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_karyawan`
--

CREATE TABLE `tbl_karyawan` (
  `karyawan_id` int NOT NULL,
  `nip` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(200) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_karyawan`
--

INSERT INTO `tbl_karyawan` (`karyawan_id`, `nip`, `nama`, `role`, `username`) VALUES
(1, '123456789', 'Operator', 'operator', 'operator'),
(2, '987654321', 'operator3', 'operator', 'operator3'),
(10, '1', '1', 'operator', '121'),
(11, '87692js', '098ass', 'operator', '1211'),
(12, 'coba', 'coba', 'operator', 'coba');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_line`
--

CREATE TABLE `tbl_line` (
  `line_id` int NOT NULL,
  `line_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_line`
--

INSERT INTO `tbl_line` (`line_id`, `line_name`, `created_at`, `created_by`) VALUES
(5, 'K02', '2026-02-02 12:53:44', 'admin'),
(7, 'CL01', '2026-02-22 05:59:54', 'admin'),
(9, 'A02', '2026-02-20 07:35:21', 'admin'),
(10, 'K01', '2026-02-22 05:57:56', 'admin'),
(12, 'A03', '2026-02-27 08:29:24', 'admin'),
(13, '1PAPAYA', '2026-04-15 08:52:57', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_material_loss`
--

CREATE TABLE `tbl_material_loss` (
  `id` int NOT NULL,
  `part_code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lost_qty` int DEFAULT NULL,
  `old_remain` int DEFAULT NULL,
  `operator` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `reason` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `assy` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shift` int DEFAULT NULL,
  `line_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_material_loss`
--

INSERT INTO `tbl_material_loss` (`id`, `part_code`, `lost_qty`, `old_remain`, `operator`, `created_at`, `reason`, `assy`, `ref_number`, `shift`, `line_id`) VALUES
(1, '169387600', 1, 834, 'CL01', '2026-04-18 08:42:58', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J991', 0, 7),
(2, '169387600', 1, 835, 'CL01', '2026-04-18 08:45:43', 'ADJUST SUB', '', 'HECIYS8OWYHJ1J991', 0, 7),
(3, '169387600', 1, 834, 'CL01', '2026-04-18 08:46:03', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J991', 0, 7),
(4, '169387600', 1, 835, 'CL01', '2026-04-18 08:47:13', 'ADJUST SUB', '', 'HECIYS8OWYHJ1J991', 0, 7),
(5, '169387600', 1, 834, 'CL01', '2026-04-18 08:47:48', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J991', 0, 7),
(6, '169387600', 2, 835, 'CL01', '2026-04-18 08:48:39', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J991', 0, 7),
(7, '169387600', 2, 837, 'CL01', '2026-04-18 08:54:49', 'ADJUST SUB', '', 'HECIYS8OWYHJ1J991', 1, 7),
(8, '169387600', 1, 835, 'CL01', '2026-04-18 08:58:33', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J991', 1, 7),
(9, '169387600', 1, 836, 'CL01', '2026-04-18 09:00:56', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J991', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_menu`
--

CREATE TABLE `tbl_menu` (
  `menu_id` int NOT NULL,
  `menu_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `menu_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `menu_url` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `urutan` int DEFAULT '0',
  `is_active` tinyint DEFAULT '1',
  `menu_key` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_menu`
--

INSERT INTO `tbl_menu` (`menu_id`, `menu_name`, `menu_icon`, `menu_url`, `parent_id`, `urutan`, `is_active`, `menu_key`) VALUES
(1, 'Dashboard', '   <span class=\"svg-icon menu-icon\"><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <polygon points=\"0 0 24 0 24 24 0 24\" />\n                                                    <path d=\"M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z\" fill=\"#000000\" fill-rule=\"nonzero\" />\n                                                    <path d=\"M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                </g>\n                                            </svg></span>', 'pages/dashboard.php', NULL, 1, 1, 'dashboard'),
(2, 'Traceability', ' <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\General\\Search.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z\" fill=\"#000000\" fill-rule=\"nonzero\" opacity=\"0.3\" />\n                                                    <path d=\"M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z\" fill=\"#000000\" fill-rule=\"nonzero\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', NULL, NULL, 2, 1, 'traceability'),
(3, 'Part Register', '  <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Shopping\\Box3.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M20.4061385,6.73606154 C20.7672665,6.89656288 21,7.25468437 21,7.64987309 L21,16.4115967 C21,16.7747638 20.8031081,17.1093844 20.4856429,17.2857539 L12.4856429,21.7301984 C12.1836204,21.8979887 11.8163796,21.8979887 11.5143571,21.7301984 L3.51435707,17.2857539 C3.19689188,17.1093844 3,16.7747638 3,16.4115967 L3,7.64987309 C3,7.25468437 3.23273352,6.89656288 3.59386153,6.73606154 L11.5938615,3.18050598 C11.8524269,3.06558805 12.1475731,3.06558805 12.4061385,3.18050598 L20.4061385,6.73606154 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                    <polygon fill=\"#000000\" points=\"14.9671522 4.22441676 7.5999999 8.31727912 7.5999999 12.9056825 9.5999999 13.9056825 9.5999999 9.49408582 17.25507 5.24126912\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/part_register/', NULL, 3, 1, 'part_register'),
(4, 'Incoming Part', ' <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Communication\\Incoming-box.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M22,17 L22,21 C22,22.1045695 21.1045695,23 20,23 L4,23 C2.8954305,23 2,22.1045695 2,21 L2,17 L6.27924078,17 L6.82339262,18.6324555 C7.09562072,19.4491398 7.8598984,20 8.72075922,20 L15.381966,20 C16.1395101,20 16.8320364,19.5719952 17.1708204,18.8944272 L18.118034,17 L22,17 Z\" fill=\"#000000\" />\n                                                    <path d=\"M2.5625,15 L5.92654389,9.01947752 C6.2807805,8.38972356 6.94714834,8 7.66969497,8 L16.330305,8 C17.0528517,8 17.7192195,8.38972356 18.0734561,9.01947752 L21.4375,15 L18.118034,15 C17.3604899,15 16.6679636,15.4280048 16.3291796,16.1055728 L15.381966,18 L8.72075922,18 L8.17660738,16.3675445 C7.90437928,15.5508602 7.1401016,15 6.27924078,15 L2.5625,15 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                    <path d=\"M11.1288761,0.733697713 L11.1288761,2.69017121 L9.12120481,2.69017121 C8.84506244,2.69017121 8.62120481,2.91402884 8.62120481,3.19017121 L8.62120481,4.21346991 C8.62120481,4.48961229 8.84506244,4.71346991 9.12120481,4.71346991 L11.1288761,4.71346991 L11.1288761,6.66994341 C11.1288761,6.94608579 11.3527337,7.16994341 11.6288761,7.16994341 C11.7471877,7.16994341 11.8616664,7.12798964 11.951961,7.05154023 L15.4576222,4.08341738 C15.6683723,3.90498251 15.6945689,3.58948575 15.5161341,3.37873564 C15.4982803,3.35764848 15.4787093,3.33807751 15.4576222,3.32022374 L11.951961,0.352100892 C11.7412109,0.173666017 11.4257142,0.199862688 11.2472793,0.410612793 C11.1708299,0.500907473 11.1288761,0.615386087 11.1288761,0.733697713 Z\" fill=\"#000000\" fill-rule=\"nonzero\" transform=\"translate(11.959697, 3.661508) rotate(-270.000000) translate(-11.959697, -3.661508) \" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/incoming_part/', NULL, 4, 1, 'incoming_part'),
(5, 'Stok Part', '   <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Files\\File.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <polygon points=\"0 0 24 0 24 24 0 24\" />\n                                                    <path d=\"M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z\" fill=\"#000000\" fill-rule=\"nonzero\" opacity=\"0.3\" />\n                                                    <rect fill=\"#000000\" x=\"6\" y=\"11\" width=\"9\" height=\"2\" rx=\"1\" />\n                                                    <rect fill=\"#000000\" x=\"6\" y=\"15\" width=\"5\" height=\"2\" rx=\"1\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/stok_part/', NULL, 5, 1, 'stok_part'),
(6, 'Part Assy', '     <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Code\\Puzzle.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M19,11 L20,11 C21.6568542,11 23,12.3431458 23,14 C23,15.6568542 21.6568542,17 20,17 L19,17 L19,20 C19,21.1045695 18.1045695,22 17,22 L5,22 C3.8954305,22 3,21.1045695 3,20 L3,17 L5,17 C6.65685425,17 8,15.6568542 8,14 C8,12.3431458 6.65685425,11 5,11 L3,11 L3,8 C3,6.8954305 3.8954305,6 5,6 L8,6 L8,5 C8,3.34314575 9.34314575,2 11,2 C12.6568542,2 14,3.34314575 14,5 L14,6 L17,6 C18.1045695,6 19,6.8954305 19,8 L19,11 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/part_assy/', NULL, 6, 1, 'part_assy'),
(7, 'Production Planning', ' <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Code\\Time-schedule.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M10.9630156,7.5 L11.0475062,7.5 C11.3043819,7.5 11.5194647,7.69464724 11.5450248,7.95024814 L12,12.5 L15.2480695,14.3560397 C15.403857,14.4450611 15.5,14.6107328 15.5,14.7901613 L15.5,15 C15.5,15.2109164 15.3290185,15.3818979 15.1181021,15.3818979 C15.0841582,15.3818979 15.0503659,15.3773725 15.0176181,15.3684413 L10.3986612,14.1087258 C10.1672824,14.0456225 10.0132986,13.8271186 10.0316926,13.5879956 L10.4644883,7.96165175 C10.4845267,7.70115317 10.7017474,7.5 10.9630156,7.5 Z\" fill=\"#000000\" />\n                                                    <path d=\"M7.38979581,2.8349582 C8.65216735,2.29743306 10.0413491,2 11.5,2 C17.2989899,2 22,6.70101013 22,12.5 C22,18.2989899 17.2989899,23 11.5,23 C5.70101013,23 1,18.2989899 1,12.5 C1,11.5151324 1.13559454,10.5619345 1.38913364,9.65805651 L3.31481075,10.1982117 C3.10672013,10.940064 3,11.7119264 3,12.5 C3,17.1944204 6.80557963,21 11.5,21 C16.1944204,21 20,17.1944204 20,12.5 C20,7.80557963 16.1944204,4 11.5,4 C10.54876,4 9.62236069,4.15592757 8.74872191,4.45446326 L9.93948308,5.87355717 C10.0088058,5.95617272 10.0495583,6.05898805 10.05566,6.16666224 C10.0712834,6.4423623 9.86044965,6.67852665 9.5847496,6.69415008 L4.71777931,6.96995273 C4.66931162,6.97269931 4.62070229,6.96837279 4.57348157,6.95710938 C4.30487471,6.89303938 4.13906482,6.62335149 4.20313482,6.35474463 L5.33163823,1.62361064 C5.35654118,1.51920756 5.41437908,1.4255891 5.49660017,1.35659741 C5.7081375,1.17909652 6.0235153,1.2066885 6.2010162,1.41822583 L7.38979581,2.8349582 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/production_planning/', NULL, 7, 1, 'production_planning'),
(8, 'Product', '           <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Shopping\\Box2.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M4,9.67471899 L10.880262,13.6470401 C10.9543486,13.689814 11.0320333,13.7207107 11.1111111,13.740321 L11.1111111,21.4444444 L4.49070127,17.526473 C4.18655139,17.3464765 4,17.0193034 4,16.6658832 L4,9.67471899 Z M20,9.56911707 L20,16.6658832 C20,17.0193034 19.8134486,17.3464765 19.5092987,17.526473 L12.8888889,21.4444444 L12.8888889,13.6728275 C12.9050191,13.6647696 12.9210067,13.6561758 12.9368301,13.6470401 L20,9.56911707 Z\" fill=\"#000000\" />\n                                                    <path d=\"M4.21611835,7.74669402 C4.30015839,7.64056877 4.40623188,7.55087574 4.5299008,7.48500698 L11.5299008,3.75665466 C11.8237589,3.60013944 12.1762411,3.60013944 12.4700992,3.75665466 L19.4700992,7.48500698 C19.5654307,7.53578262 19.6503066,7.60071528 19.7226939,7.67641889 L12.0479413,12.1074394 C11.9974761,12.1365754 11.9509488,12.1699127 11.9085461,12.2067543 C11.8661433,12.1699127 11.819616,12.1365754 11.7691509,12.1074394 L4.21611835,7.74669402 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/product/', NULL, 8, 1, 'product'),
(9, 'Outbound Product', '<span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Navigation\\Angle-double-right.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <polygon points=\"0 0 24 0 24 24 0 24\" />\n                                                    <path d=\"M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z\" fill=\"#000000\" fill-rule=\"nonzero\" />\n                                                    <path d=\"M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z\" fill=\"#000000\" fill-rule=\"nonzero\" opacity=\"0.3\" transform=\"translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) \" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/outbound_product/', NULL, 9, 1, 'outbound_product'),
(10, 'NG Material', '    <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Navigation\\Angle-double-right.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <polygon points=\"0 0 24 0 24 24 0 24\" />\n                                                    <path d=\"M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z\" fill=\"#000000\" fill-rule=\"nonzero\" />\n                                                    <path d=\"M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z\" fill=\"#000000\" fill-rule=\"nonzero\" opacity=\"0.3\" transform=\"translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) \" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/material_report/', NULL, 10, 1, 'ng_material'),
(11, 'Settings', '  <span class=\"svg-icon menu-icon svg-icon-2x\"><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M2.56066017,10.6819805 L4.68198052,8.56066017 C5.26776695,7.97487373 6.21751442,7.97487373 6.80330086,8.56066017 L8.9246212,10.6819805 C9.51040764,11.267767 9.51040764,12.2175144 8.9246212,12.8033009 L6.80330086,14.9246212 C6.21751442,15.5104076 5.26776695,15.5104076 4.68198052,14.9246212 L2.56066017,12.8033009 C1.97487373,12.2175144 1.97487373,11.267767 2.56066017,10.6819805 Z M14.5606602,10.6819805 L16.6819805,8.56066017 C17.267767,7.97487373 18.2175144,7.97487373 18.8033009,8.56066017 L20.9246212,10.6819805 C21.5104076,11.267767 21.5104076,12.2175144 20.9246212,12.8033009 L18.8033009,14.9246212 C18.2175144,15.5104076 17.267767,15.5104076 16.6819805,14.9246212 L14.5606602,12.8033009 C13.9748737,12.2175144 13.9748737,11.267767 14.5606602,10.6819805 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                    <path d=\"M8.56066017,16.6819805 L10.6819805,14.5606602 C11.267767,13.9748737 12.2175144,13.9748737 12.8033009,14.5606602 L14.9246212,16.6819805 C15.5104076,17.267767 15.5104076,18.2175144 14.9246212,18.8033009 L12.8033009,20.9246212 C12.2175144,21.5104076 11.267767,21.5104076 10.6819805,20.9246212 L8.56066017,18.8033009 C7.97487373,18.2175144 7.97487373,17.267767 8.56066017,16.6819805 Z M8.56066017,4.68198052 L10.6819805,2.56066017 C11.267767,1.97487373 12.2175144,1.97487373 12.8033009,2.56066017 L14.9246212,4.68198052 C15.5104076,5.26776695 15.5104076,6.21751442 14.9246212,6.80330086 L12.8033009,8.9246212 C12.2175144,9.51040764 11.267767,9.51040764 10.6819805,8.9246212 L8.56066017,6.80330086 C7.97487373,6.21751442 7.97487373,5.26776695 8.56066017,4.68198052 Z\" fill=\"#000000\" />\n                                                </g>\n                                            </svg></span>', NULL, NULL, 11, 1, 'settings'),
(12, 'User', '  <span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\General\\User.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <polygon points=\"0 0 24 0 24 24 0 24\" />\n                                                    <path d=\"M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z\" fill=\"#000000\" fill-rule=\"nonzero\" opacity=\"0.3\" />\n                                                    <path d=\"M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z\" fill=\"#000000\" fill-rule=\"nonzero\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/user/', NULL, 12, 1, 'user'),
(13, 'Operator', '<span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Tools\\Tools.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n                                                <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n                                                    <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\" />\n                                                    <path d=\"M15.9497475,3.80761184 L13.0246125,6.73274681 C12.2435639,7.51379539 12.2435639,8.78012535 13.0246125,9.56117394 L14.4388261,10.9753875 C15.2198746,11.7564361 16.4862046,11.7564361 17.2672532,10.9753875 L20.1923882,8.05025253 C20.7341101,10.0447871 20.2295941,12.2556873 18.674559,13.8107223 C16.8453326,15.6399488 14.1085592,16.0155296 11.8839934,14.9444337 L6.75735931,20.0710678 C5.97631073,20.8521164 4.70998077,20.8521164 3.92893219,20.0710678 C3.1478836,19.2900192 3.1478836,18.0236893 3.92893219,17.2426407 L9.05556629,12.1160066 C7.98447038,9.89144078 8.36005124,7.15466739 10.1892777,5.32544095 C11.7443127,3.77040588 13.9552129,3.26588995 15.9497475,3.80761184 Z\" fill=\"#000000\" />\n                                                    <path d=\"M16.6568542,5.92893219 L18.0710678,7.34314575 C18.4615921,7.73367004 18.4615921,8.36683502 18.0710678,8.75735931 L16.6913928,10.1370344 C16.3008685,10.5275587 15.6677035,10.5275587 15.2771792,10.1370344 L13.8629656,8.7228208 C13.4724413,8.33229651 13.4724413,7.69913153 13.8629656,7.30860724 L15.2426407,5.92893219 C15.633165,5.5384079 16.26633,5.5384079 16.6568542,5.92893219 Z\" fill=\"#000000\" opacity=\"0.3\" />\n                                                </g>\n                                            </svg><!--end::Svg Icon--></span>', 'pages/operator/', NULL, 13, 1, 'operator'),
(15, 'Product Trace', NULL, 'pages/production_traceability/', 2, 1, 1, 'traceability'),
(16, 'Material Trace', NULL, 'pages/material_traceability/', 2, 2, 1, 'material_traceability'),
(17, 'Shift Setting', NULL, 'pages/shift/', 11, 1, 1, 'shift_setting'),
(18, 'Line Setting', NULL, 'pages/line_setting/', 11, 2, 1, 'line_setting'),
(19, 'Supplier Setting', NULL, 'pages/supplier/', 11, 3, 1, 'supplier_setting'),
(20, 'Customer Setting', NULL, 'pages/customer/', 11, 4, 1, 'customer_setting'),
(21, 'NG Type', NULL, 'pages/ng_type/', 11, 5, 1, 'ng_type'),
(22, 'Role', '<span class=\"svg-icon menu-icon\"><!--begin::Svg Icon | path:C:\\wamp64\\www\\keenthemes\\themes\\metronic\\theme\\html\\demo1\\dist/../src/media/svg/icons\\Communication\\Contact1.svg--><svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" width=\"24px\" height=\"24px\" viewBox=\"0 0 24 24\" version=\"1.1\">\n    <g stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\">\n        <rect x=\"0\" y=\"0\" width=\"24\" height=\"24\"/>\n        <circle fill=\"#000000\" opacity=\"0.3\" cx=\"12\" cy=\"12\" r=\"10\"/>\n        <path d=\"M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 L7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z\" fill=\"#000000\" opacity=\"0.3\"/>\n    </g>\n</svg><!--end::Svg Icon--></span>\n', 'pages/role/', 0, 14, 1, 'role');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_model`
--

CREATE TABLE `tbl_model` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_model`
--

INSERT INTO `tbl_model` (`id`, `name`, `part_code`, `created_by`, `created_at`) VALUES
(8, 'NAIAD 6', '1A4219900', 'admin', '2026-04-26 09:40:24'),
(10, 'NAIAD 8', '1A4215700', 'admin', '2026-04-29 20:29:56');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ng_part`
--

CREATE TABLE `tbl_ng_part` (
  `id` int NOT NULL,
  `ng_id` int NOT NULL,
  `part_code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lot_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `used_qty` int DEFAULT NULL,
  `ng_qty` int DEFAULT NULL,
  `ng_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `reason` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `ref_part` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `shift` int NOT NULL,
  `line_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ng_part`
--

INSERT INTO `tbl_ng_part` (`id`, `ng_id`, `part_code`, `lot_no`, `used_qty`, `ng_qty`, `ng_type`, `reason`, `created_at`, `ref_part`, `shift`, `line_id`) VALUES
(1, 3, '128237800', '01725Z22D1Z1004', 112, 1, '4', NULL, '2026-04-30 05:41:49', 'HECIYS8OWYHJ1J1004', 3, 7),
(2, 4, '128237800', '01725Z22D1Z1004', 112, 1, '4', NULL, '2026-04-30 09:13:46', 'HECIYS8OWYHJ1J1004', 1, 7),
(3, 5, '128237800', '01725Z22D1Z1004', 112, 1, '4', NULL, '2026-04-30 09:18:29', 'HECIYS8OWYHJ1J1004', 1, 7),
(4, 6, '128237800', '01725Z22D1Z1004', 112, 1, '4', NULL, '2026-04-30 09:59:18', 'HECIYS8OWYHJ1J1004', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ng_product`
--

CREATE TABLE `tbl_ng_product` (
  `id` int NOT NULL,
  `serial_no` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `product_code` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `line_id` int DEFAULT NULL,
  `shift` int DEFAULT NULL,
  `operator` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('EXIT_MECA','IN_MECA') COLLATE utf8mb4_general_ci DEFAULT 'EXIT_MECA',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `ref_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ng_product`
--

INSERT INTO `tbl_ng_product` (`id`, `serial_no`, `product_code`, `line_id`, `shift`, `operator`, `status`, `created_at`, `ref_number`) VALUES
(3, '01725Z22D1Z001A', '1A4215700', 7, 3, 'CL01', 'IN_MECA', '2026-04-30 05:41:49', 'HECIYS8OWYHJ1JBA'),
(4, '01725Z22D1Z001COBA', '1A4215700', 7, 1, 'CL01', 'IN_MECA', '2026-04-30 09:13:46', 'HECIYS8OWYHJ1JCOBA1'),
(5, '01725Z22D1Z001COBA', '1A4215700', 7, 1, 'CL01', 'IN_MECA', '2026-04-30 09:18:29', 'HECIYS8OWYHJ1JCOBA2'),
(6, '01725Z22D1Z001COBA', '1A4215700', 7, 1, 'CL01', 'IN_MECA', '2026-04-30 09:59:18', 'HECIYS8OWYHJ1JCOBA3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ng_type`
--

CREATE TABLE `tbl_ng_type` (
  `id` int NOT NULL,
  `ng_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ng_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_general_ci DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ng_type`
--

INSERT INTO `tbl_ng_type` (`id`, `ng_code`, `ng_name`, `status`) VALUES
(1, 'rusakJ', 'rusak', 'ACTIVE'),
(2, 'u', 'h', 'ACTIVE'),
(3, 'COB', 'YGH', 'ACTIVE'),
(4, 'RUSTY', NULL, 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ng_type_detail`
--

CREATE TABLE `tbl_ng_type_detail` (
  `id` int NOT NULL,
  `part_id` int NOT NULL,
  `type_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ng_type_detail`
--

INSERT INTO `tbl_ng_type_detail` (`id`, `part_id`, `type_id`) VALUES
(1, 1, 2),
(2, 3, 2),
(3, 1, 3),
(4, 2, 3),
(6, 10, 1),
(7, 14, 4),
(8, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_part`
--

CREATE TABLE `tbl_part` (
  `id_part` int NOT NULL,
  `part_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `supplier` int DEFAULT NULL,
  `qty` int DEFAULT '0',
  `status` enum('sp','md') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'sp',
  `status_assy` int DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_part`
--

INSERT INTO `tbl_part` (`id_part`, `part_code`, `part_name`, `supplier`, `qty`, `status`, `status_assy`, `updated`) VALUES
(1, '169390101', 'FRAME,LOWER,RIGHT', 4, 0, 'sp', 0, '2026-04-25 21:33:12'),
(2, '172503000', 'FOOT,8.5X8.5X5', 5, 0, 'sp', 0, '2026-04-25 21:33:12'),
(3, '169455303', 'HOLDER,AC INLET,LEFT,PS UNIT', 7, 0, 'sp', 0, '2026-04-25 21:33:12'),
(4, '217711301', 'CABLE,CSIC,INK EJECT', 8, 0, 'sp', 0, '2026-04-25 21:33:12'),
(5, '221065900', 'CABLE,CSIC,INK EJECT;B', 8, 0, 'sp', 0, '2026-04-25 21:33:12'),
(6, '212685700', 'FERRITE CORE,28R0629-030', 6, 0, 'sp', 0, '2026-04-25 21:33:12'),
(7, '159352600', 'DOUBLE SIDE TAPE,12X10', 2, 0, 'sp', 0, '2026-04-25 21:33:12'),
(8, '169387700', 'HOLDER,CONNECTOR,CSIC,IS', 10, 0, 'sp', 0, '2026-04-25 21:33:12'),
(9, '189427200', 'HOLDER,CONNECTOR,CSIC,IS,KP', 10, 0, 'sp', 0, '2026-04-25 21:33:12'),
(10, '218496702', 'CONNECTOR,CSIC', 9, 0, 'sp', 0, '2026-04-25 21:33:12'),
(11, '209056603', 'CONNECTOR,CSIC', 9, 0, 'sp', 0, '2026-04-25 21:33:12'),
(12, '169387500', 'HOLDER,CABLE,CSIC,INK EJECT', 3, 0, 'sp', 0, '2026-04-25 21:33:12'),
(13, '169387600', 'JOINT,TUBE', 3, 0, 'sp', 0, '2026-04-25 21:33:12'),
(14, '128237800', 'C.B.P-TITE SCREW,3X10,F/ZN-3C', 1, 0, 'sp', 0, '2026-04-25 21:33:12'),
(15, '189358800', 'HOLDER,CABLE,CSIC,INK EJECT;B', 3, 0, 'sp', 0, '2026-04-25 21:33:12'),
(16, '218362101', 'CABLE,CSIC,INK EJECT', 8, 0, 'sp', 0, '2026-04-25 21:33:12'),
(17, '221066500', 'CABLE,CSIC,INK EJECT;B', 8, 0, 'sp', 0, '2026-04-25 21:33:12'),
(19, '159352600', 'BEDA', 16, 0, 'sp', 0, '2026-04-25 22:18:01'),
(24, '1A4219900', 'HOUSING LOWER RIGHT ASSY;B;CG17;IEI', 4, 0, 'md', 1, '2026-04-26 16:40:24'),
(26, '1A4215700', 'HOUSING LOWER RIGHT ASSY;B;CK86;IEI', 4, 0, 'md', 1, '2026-04-30 03:29:56'),
(27, '169455303', 'BEDA 2', 17, 0, 'sp', 0, '2026-04-30 11:37:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_part_assy`
--

CREATE TABLE `tbl_part_assy` (
  `id_pa` int NOT NULL,
  `part_assy` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pcs',
  `remark` int NOT NULL,
  `part_id` int NOT NULL,
  `subs` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_part_assy`
--

INSERT INTO `tbl_part_assy` (`id_pa`, `part_assy`, `part_code`, `qty`, `unit`, `remark`, `part_id`, `subs`) VALUES
(198, '1A4219900', '169390101', 4, 'Pcs', 0, 1, '0'),
(199, '1A4219900', '172503000', 2, 'Pcs', 0, 2, '0'),
(200, '1A4219900', '169455303', 1, 'Pcs', 0, 3, '0'),
(201, '1A4219900', '217711301', 1, 'Pcs', 0, 4, '0'),
(202, '1A4219900', '221065900', 1, 'Pcs', 1, 5, '4'),
(203, '1A4219900', '212685700', 1, 'Pcs', 0, 6, '0'),
(204, '1A4219900', '159352600', 1, 'Pcs', 0, 7, '0'),
(205, '1A4219900', '169387700', 1, 'Pcs', 0, 8, '0'),
(206, '1A4219900', '189427200', 1, 'Pcs', 1, 9, '8'),
(207, '1A4219900', '218496702', 1, 'Pcs', 0, 10, '0'),
(208, '1A4219900', '209056603', 1, 'Pcs', 1, 11, '10'),
(209, '1A4219900', '169387500', 1, 'Pcs', 0, 12, '0'),
(210, '1A4219900', '169387600', 1, 'Pcs', 0, 13, '0'),
(211, '1A4219900', '128237800', 1, 'Pcs', 0, 14, '0'),
(226, '1A4215700', '169390101', 1, 'Pcs', 0, 1, '0'),
(227, '1A4215700', '172503000', 3, 'Pcs', 0, 2, '0'),
(228, '1A4215700', '169455303', 1, 'Pcs', 0, 3, '0'),
(229, '1A4215700', '217711301', 1, 'Pcs', 0, 4, '0'),
(230, '1A4215700', '221065900', 1, 'Pcs', 1, 5, '4'),
(231, '1A4215700', '169387700', 1, 'Pcs', 0, 8, '0'),
(232, '1A4215700', '189427200', 1, 'Pcs', 1, 9, '8'),
(233, '1A4215700', '218496702', 1, 'Pcs', 0, 10, '0'),
(234, '1A4215700', '209056603', 1, 'Pcs', 1, 11, '10'),
(235, '1A4215700', '189358800', 1, 'Pcs', 0, 15, '0'),
(236, '1A4215700', '169387500', 1, 'Pcs', 1, 12, '15'),
(237, '1A4215700', '169387600', 1, 'Pcs', 0, 13, '0'),
(238, '1A4215700', '128237800', 4, 'Pcs', 0, 14, '0');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pp_material`
--

CREATE TABLE `tbl_pp_material` (
  `id` int NOT NULL,
  `pp_id` int NOT NULL,
  `part_id` int NOT NULL,
  `part_code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` enum('MAIN','SUB') COLLATE utf8mb4_general_ci DEFAULT 'MAIN',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pp_material`
--

INSERT INTO `tbl_pp_material` (`id`, `pp_id`, `part_id`, `part_code`, `type`, `created_at`) VALUES
(1, 1, 1, '169390101', 'MAIN', '2026-04-30 03:55:05'),
(2, 1, 2, '172503000', 'MAIN', '2026-04-30 03:55:05'),
(3, 1, 3, '169455303', 'MAIN', '2026-04-30 03:55:05'),
(4, 1, 4, '217711301', 'MAIN', '2026-04-30 03:55:05'),
(5, 1, 8, '169387700', 'MAIN', '2026-04-30 03:55:05'),
(6, 1, 10, '218496702', 'MAIN', '2026-04-30 03:55:05'),
(7, 1, 15, '189358800', 'MAIN', '2026-04-30 03:55:05'),
(8, 1, 13, '169387600', 'MAIN', '2026-04-30 03:55:05'),
(9, 1, 14, '128237800', 'MAIN', '2026-04-30 03:55:05'),
(10, 2, 1, '169390101', 'MAIN', '2026-04-30 03:55:05'),
(11, 2, 2, '172503000', 'MAIN', '2026-04-30 03:55:05'),
(12, 2, 3, '169455303', 'MAIN', '2026-04-30 03:55:05'),
(13, 2, 4, '217711301', 'MAIN', '2026-04-30 03:55:05'),
(14, 2, 6, '212685700', 'MAIN', '2026-04-30 03:55:05'),
(15, 2, 7, '159352600', 'MAIN', '2026-04-30 03:55:05'),
(16, 2, 8, '169387700', 'MAIN', '2026-04-30 03:55:05'),
(17, 2, 10, '218496702', 'MAIN', '2026-04-30 03:55:05'),
(18, 2, 12, '169387500', 'MAIN', '2026-04-30 03:55:05'),
(19, 2, 13, '169387600', 'MAIN', '2026-04-30 03:55:05'),
(20, 2, 14, '128237800', 'MAIN', '2026-04-30 03:55:05'),
(21, 3, 1, '169390101', 'MAIN', '2026-04-30 03:55:05'),
(22, 3, 2, '172503000', 'MAIN', '2026-04-30 03:55:05'),
(23, 3, 3, '169455303', 'MAIN', '2026-04-30 03:55:05'),
(24, 3, 4, '217711301', 'MAIN', '2026-04-30 03:55:05'),
(25, 3, 8, '169387700', 'MAIN', '2026-04-30 03:55:05'),
(26, 3, 10, '218496702', 'MAIN', '2026-04-30 03:55:05'),
(27, 3, 15, '189358800', 'MAIN', '2026-04-30 03:55:05'),
(28, 3, 13, '169387600', 'MAIN', '2026-04-30 03:55:05'),
(29, 3, 14, '128237800', 'MAIN', '2026-04-30 03:55:05'),
(30, 4, 1, '169390101', 'MAIN', '2026-04-30 03:55:05'),
(31, 4, 2, '172503000', 'MAIN', '2026-04-30 03:55:05'),
(32, 4, 3, '169455303', 'MAIN', '2026-04-30 03:55:05'),
(33, 4, 4, '217711301', 'MAIN', '2026-04-30 03:55:05'),
(34, 4, 6, '212685700', 'MAIN', '2026-04-30 03:55:05'),
(35, 4, 7, '159352600', 'MAIN', '2026-04-30 03:55:05'),
(36, 4, 8, '169387700', 'MAIN', '2026-04-30 03:55:05'),
(37, 4, 10, '218496702', 'MAIN', '2026-04-30 03:55:05'),
(38, 4, 12, '169387500', 'MAIN', '2026-04-30 03:55:05'),
(39, 4, 13, '169387600', 'MAIN', '2026-04-30 03:55:05'),
(40, 4, 14, '128237800', 'MAIN', '2026-04-30 03:55:05'),
(41, 5, 1, '169390101', 'MAIN', '2026-04-30 03:55:05'),
(42, 5, 2, '172503000', 'MAIN', '2026-04-30 03:55:05'),
(43, 5, 3, '169455303', 'MAIN', '2026-04-30 03:55:05'),
(44, 5, 4, '217711301', 'MAIN', '2026-04-30 03:55:05'),
(45, 5, 6, '212685700', 'MAIN', '2026-04-30 03:55:05'),
(46, 5, 7, '159352600', 'MAIN', '2026-04-30 03:55:05'),
(47, 5, 8, '169387700', 'MAIN', '2026-04-30 03:55:05'),
(48, 5, 10, '218496702', 'MAIN', '2026-04-30 03:55:05'),
(49, 5, 12, '169387500', 'MAIN', '2026-04-30 03:55:05'),
(50, 5, 13, '169387600', 'MAIN', '2026-04-30 03:55:05'),
(51, 5, 14, '128237800', 'MAIN', '2026-04-30 03:55:05'),
(52, 6, 1, '169390101', 'MAIN', '2026-04-30 03:55:05'),
(53, 6, 2, '172503000', 'MAIN', '2026-04-30 03:55:05'),
(54, 6, 3, '169455303', 'MAIN', '2026-04-30 03:55:05'),
(55, 6, 4, '217711301', 'MAIN', '2026-04-30 03:55:05'),
(56, 6, 8, '169387700', 'MAIN', '2026-04-30 03:55:05'),
(57, 6, 10, '218496702', 'MAIN', '2026-04-30 03:55:05'),
(58, 6, 15, '189358800', 'MAIN', '2026-04-30 03:55:05'),
(59, 6, 13, '169387600', 'MAIN', '2026-04-30 03:55:05'),
(60, 6, 14, '128237800', 'MAIN', '2026-04-30 03:55:05'),
(61, 7, 1, '169390101', 'MAIN', '2026-04-30 08:51:54'),
(62, 7, 2, '172503000', 'MAIN', '2026-04-30 08:51:54'),
(63, 7, 3, '169455303', 'MAIN', '2026-04-30 08:51:54'),
(64, 7, 4, '217711301', 'MAIN', '2026-04-30 08:51:54'),
(65, 7, 8, '169387700', 'MAIN', '2026-04-30 08:51:54'),
(66, 7, 10, '218496702', 'MAIN', '2026-04-30 08:51:54'),
(67, 7, 15, '189358800', 'MAIN', '2026-04-30 08:51:54'),
(68, 7, 13, '169387600', 'MAIN', '2026-04-30 08:51:54'),
(69, 7, 14, '128237800', 'MAIN', '2026-04-30 08:51:54'),
(70, 8, 1, '169390101', 'MAIN', '2026-04-30 08:51:54'),
(71, 8, 2, '172503000', 'MAIN', '2026-04-30 08:51:54'),
(72, 8, 3, '169455303', 'MAIN', '2026-04-30 08:51:54'),
(73, 8, 4, '217711301', 'MAIN', '2026-04-30 08:51:54'),
(74, 8, 6, '212685700', 'MAIN', '2026-04-30 08:51:54'),
(75, 8, 7, '159352600', 'MAIN', '2026-04-30 08:51:54'),
(76, 8, 8, '169387700', 'MAIN', '2026-04-30 08:51:54'),
(77, 8, 10, '218496702', 'MAIN', '2026-04-30 08:51:54'),
(78, 8, 12, '169387500', 'MAIN', '2026-04-30 08:51:54'),
(79, 8, 13, '169387600', 'MAIN', '2026-04-30 08:51:54'),
(80, 8, 14, '128237800', 'MAIN', '2026-04-30 08:51:54'),
(81, 9, 1, '169390101', 'MAIN', '2026-04-30 08:51:54'),
(82, 9, 2, '172503000', 'MAIN', '2026-04-30 08:51:54'),
(83, 9, 3, '169455303', 'MAIN', '2026-04-30 08:51:54'),
(84, 9, 4, '217711301', 'MAIN', '2026-04-30 08:51:54'),
(85, 9, 6, '212685700', 'MAIN', '2026-04-30 08:51:54'),
(86, 9, 7, '159352600', 'MAIN', '2026-04-30 08:51:54'),
(87, 9, 8, '169387700', 'MAIN', '2026-04-30 08:51:54'),
(88, 9, 10, '218496702', 'MAIN', '2026-04-30 08:51:54'),
(89, 9, 12, '169387500', 'MAIN', '2026-04-30 08:51:54'),
(90, 9, 13, '169387600', 'MAIN', '2026-04-30 08:51:54'),
(91, 9, 14, '128237800', 'MAIN', '2026-04-30 08:51:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `part_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_production_output`
--

CREATE TABLE `tbl_production_output` (
  `id` int NOT NULL,
  `product_code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serial_no` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `operator_remark` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shift` int DEFAULT NULL,
  `line_id` int DEFAULT NULL,
  `operator` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qty` int DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `result_status` enum('good','ng') COLLATE utf8mb4_general_ci DEFAULT 'good',
  `ref_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_production_output`
--

INSERT INTO `tbl_production_output` (`id`, `product_code`, `serial_no`, `operator_remark`, `shift`, `line_id`, `operator`, `qty`, `created_at`, `result_status`, `ref_number`) VALUES
(1, '1A4215700', '01725Z22D1Z001A', '', 3, 7, 'operator', 28, '2026-04-30 05:32:50', 'good', 'HECIYS8OWYHJ1JBA'),
(2, '1A4215700', '01725Z22D1Z001COBA', '', 1, 7, 'operator', 28, '2026-04-30 09:02:27', 'good', 'HECIYS8OWYHJ1JCOBA1'),
(3, '1A4215700', '01725Z22D1Z001COBA', '', 1, 7, 'operator', 28, '2026-04-30 09:17:07', 'good', 'HECIYS8OWYHJ1JCOBA2'),
(4, '1A4215700', '01725Z22D1Z001COBA', '', 1, 7, 'operator', 28, '2026-04-30 09:50:37', 'good', 'HECIYS8OWYHJ1JCOBA3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_production_planning`
--

CREATE TABLE `tbl_production_planning` (
  `pp_id` int NOT NULL,
  `line_id` int NOT NULL,
  `product_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `shift` int NOT NULL,
  `production_date` date NOT NULL,
  `qty` int NOT NULL,
  `status` enum('planned','released','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_qty` int NOT NULL,
  `pp_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_production_planning`
--

INSERT INTO `tbl_production_planning` (`pp_id`, `line_id`, `product_code`, `shift`, `production_date`, `qty`, `status`, `total_qty`, `pp_code`) VALUES
(1, 7, '1A4215700', 1, '2026-04-29', 200, 'planned', 200, 'PP-20260429-844E'),
(2, 5, '1A4219900', 1, '2026-04-29', 200, 'planned', 200, 'PP-20260429-844E'),
(3, 7, '1A4215700', 2, '2026-04-29', 200, 'planned', 200, 'PP-20260429-844E'),
(4, 7, '1A4219900', 2, '2026-04-29', 80, 'planned', 80, 'PP-20260429-844E'),
(5, 7, '1A4219900', 3, '2026-04-29', 200, 'planned', 200, 'PP-20260429-844E'),
(6, 7, '1A4215700', 3, '2026-04-29', 60, 'planned', 60, 'PP-20260429-844E'),
(7, 7, '1A4215700', 1, '2026-04-30', 200, 'planned', 200, 'PP-20260430-9C3C'),
(8, 7, '1A4219900', 1, '2026-04-30', 200, 'planned', 200, 'PP-20260430-9C3C'),
(9, 13, '1A4219900', 1, '2026-04-30', 200, 'planned', 200, 'PP-20260430-9C3C');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_unit`
--

CREATE TABLE `tbl_product_unit` (
  `id` int NOT NULL,
  `serial_no` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `product_code` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `unit_no` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `ref_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product_unit`
--

INSERT INTO `tbl_product_unit` (`id`, `serial_no`, `product_code`, `unit_no`, `created_at`, `ref_number`) VALUES
(1, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 03:44:40', 'HECIYS8OWYHJ1JBA'),
(2, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 03:44:40', 'HECIYS8OWYHJ1JBA'),
(3, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 03:44:40', 'HECIYS8OWYHJ1JBA'),
(4, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 03:44:40', 'HECIYS8OWYHJ1JBA'),
(5, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 03:45:19', 'HECIYS8OWYHJ1JBB'),
(6, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 03:45:19', 'HECIYS8OWYHJ1JBB'),
(7, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 03:45:19', 'HECIYS8OWYHJ1JBB'),
(8, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 03:45:19', 'HECIYS8OWYHJ1JBB'),
(9, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 03:48:08', 'HECIYS8OWYHJ1JBC'),
(10, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 03:48:08', 'HECIYS8OWYHJ1JBC'),
(11, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 03:48:08', 'HECIYS8OWYHJ1JBC'),
(12, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 03:48:08', 'HECIYS8OWYHJ1JBC'),
(13, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 03:56:59', 'HECIYS8OWYHJ1JBD'),
(14, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 03:56:59', 'HECIYS8OWYHJ1JBD'),
(15, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 03:56:59', 'HECIYS8OWYHJ1JBD'),
(16, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 03:56:59', 'HECIYS8OWYHJ1JBD'),
(17, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 03:57:22', 'HECIYS8OWYHJ1JBE'),
(18, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 03:57:22', 'HECIYS8OWYHJ1JBE'),
(19, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 03:57:22', 'HECIYS8OWYHJ1JBE'),
(20, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 03:57:22', 'HECIYS8OWYHJ1JBE'),
(21, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 03:59:42', 'HECIYS8OWYHJ1JBF'),
(22, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 03:59:42', 'HECIYS8OWYHJ1JBF'),
(23, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 03:59:42', 'HECIYS8OWYHJ1JBF'),
(24, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 03:59:42', 'HECIYS8OWYHJ1JBF'),
(25, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 04:23:07', 'HECIYS8OWYHJ1JBG'),
(26, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 04:23:07', 'HECIYS8OWYHJ1JBG'),
(27, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 04:23:07', 'HECIYS8OWYHJ1JBG'),
(28, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 04:23:07', 'HECIYS8OWYHJ1JBG'),
(29, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 04:33:18', 'HECIYS8OWYHJ1JBH'),
(30, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 04:33:18', 'HECIYS8OWYHJ1JBH'),
(31, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 04:33:18', 'HECIYS8OWYHJ1JBH'),
(32, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 04:33:18', 'HECIYS8OWYHJ1JBH'),
(33, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 04:33:41', 'HECIYS8OWYHJ1JBI'),
(34, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 04:33:41', 'HECIYS8OWYHJ1JBI'),
(35, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 04:33:41', 'HECIYS8OWYHJ1JBI'),
(36, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 04:33:41', 'HECIYS8OWYHJ1JBI'),
(37, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 04:40:00', 'HECIYS8OWYHJ1JBJ'),
(38, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 04:40:00', 'HECIYS8OWYHJ1JBJ'),
(39, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 04:40:00', 'HECIYS8OWYHJ1JBJ'),
(40, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 04:40:00', 'HECIYS8OWYHJ1JBJ'),
(49, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 05:20:12', 'HECIYS8OWYHJ1JBK'),
(50, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 05:20:12', 'HECIYS8OWYHJ1JBK'),
(51, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 05:20:12', 'HECIYS8OWYHJ1JBK'),
(52, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 05:20:12', 'HECIYS8OWYHJ1JBK'),
(53, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 05:20:18', 'HECIYS8OWYHJ1JBL'),
(54, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-18 05:20:18', 'HECIYS8OWYHJ1JBL'),
(55, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-18 05:20:18', 'HECIYS8OWYHJ1JBL'),
(56, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-18 05:20:18', 'HECIYS8OWYHJ1JBL'),
(57, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-18 05:20:50', 'HECIYS8OWYHJ1JBM'),
(154, '01725Z22D1Z001A', '1A4215700', 5, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(155, '01725Z22D1Z001A', '1A4215700', 6, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(156, '01725Z22D1Z001A', '1A4215700', 7, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(157, '01725Z22D1Z001A', '1A4215700', 8, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(158, '01725Z22D1Z001A', '1A4215700', 9, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(159, '01725Z22D1Z001A', '1A4215700', 10, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(160, '01725Z22D1Z001A', '1A4215700', 11, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(161, '01725Z22D1Z001A', '1A4215700', 12, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(162, '01725Z22D1Z001A', '1A4215700', 13, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(163, '01725Z22D1Z001A', '1A4215700', 14, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(164, '01725Z22D1Z001A', '1A4215700', 15, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(165, '01725Z22D1Z001A', '1A4215700', 16, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(166, '01725Z22D1Z001A', '1A4215700', 17, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(167, '01725Z22D1Z001A', '1A4215700', 18, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(168, '01725Z22D1Z001A', '1A4215700', 19, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(169, '01725Z22D1Z001A', '1A4215700', 20, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(170, '01725Z22D1Z001A', '1A4215700', 21, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(171, '01725Z22D1Z001A', '1A4215700', 22, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(172, '01725Z22D1Z001A', '1A4215700', 23, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(173, '01725Z22D1Z001A', '1A4215700', 24, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(174, '01725Z22D1Z001A', '1A4215700', 25, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(175, '01725Z22D1Z001A', '1A4215700', 26, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(176, '01725Z22D1Z001A', '1A4215700', 27, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(177, '01725Z22D1Z001A', '1A4215700', 28, '2026-04-30 05:18:47', 'HECIYS8OWYHJ1JBA'),
(178, '01725Z22D1Z001A', '1A4215700', 5, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(179, '01725Z22D1Z001A', '1A4215700', 6, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(180, '01725Z22D1Z001A', '1A4215700', 7, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(181, '01725Z22D1Z001A', '1A4215700', 8, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(182, '01725Z22D1Z001A', '1A4215700', 9, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(183, '01725Z22D1Z001A', '1A4215700', 10, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(184, '01725Z22D1Z001A', '1A4215700', 11, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(185, '01725Z22D1Z001A', '1A4215700', 12, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(186, '01725Z22D1Z001A', '1A4215700', 13, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(187, '01725Z22D1Z001A', '1A4215700', 14, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(188, '01725Z22D1Z001A', '1A4215700', 15, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(189, '01725Z22D1Z001A', '1A4215700', 16, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(190, '01725Z22D1Z001A', '1A4215700', 17, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(191, '01725Z22D1Z001A', '1A4215700', 18, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(192, '01725Z22D1Z001A', '1A4215700', 19, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(193, '01725Z22D1Z001A', '1A4215700', 20, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(194, '01725Z22D1Z001A', '1A4215700', 21, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(195, '01725Z22D1Z001A', '1A4215700', 22, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(196, '01725Z22D1Z001A', '1A4215700', 23, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(197, '01725Z22D1Z001A', '1A4215700', 24, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(198, '01725Z22D1Z001A', '1A4215700', 25, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(199, '01725Z22D1Z001A', '1A4215700', 26, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(200, '01725Z22D1Z001A', '1A4215700', 27, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(201, '01725Z22D1Z001A', '1A4215700', 28, '2026-04-30 05:19:52', 'HECIYS8OWYHJ1JBB'),
(202, '01725Z22D1Z001COBA', '1A4215700', 1, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(203, '01725Z22D1Z001COBA', '1A4215700', 2, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(204, '01725Z22D1Z001COBA', '1A4215700', 3, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(205, '01725Z22D1Z001COBA', '1A4215700', 4, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(206, '01725Z22D1Z001COBA', '1A4215700', 5, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(207, '01725Z22D1Z001COBA', '1A4215700', 6, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(208, '01725Z22D1Z001COBA', '1A4215700', 7, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(209, '01725Z22D1Z001COBA', '1A4215700', 8, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(210, '01725Z22D1Z001COBA', '1A4215700', 9, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(211, '01725Z22D1Z001COBA', '1A4215700', 10, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(212, '01725Z22D1Z001COBA', '1A4215700', 11, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(213, '01725Z22D1Z001COBA', '1A4215700', 12, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(214, '01725Z22D1Z001COBA', '1A4215700', 13, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(215, '01725Z22D1Z001COBA', '1A4215700', 14, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(216, '01725Z22D1Z001COBA', '1A4215700', 15, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(217, '01725Z22D1Z001COBA', '1A4215700', 16, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(218, '01725Z22D1Z001COBA', '1A4215700', 17, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(219, '01725Z22D1Z001COBA', '1A4215700', 18, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(220, '01725Z22D1Z001COBA', '1A4215700', 19, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(221, '01725Z22D1Z001COBA', '1A4215700', 20, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(222, '01725Z22D1Z001COBA', '1A4215700', 21, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(223, '01725Z22D1Z001COBA', '1A4215700', 22, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(224, '01725Z22D1Z001COBA', '1A4215700', 23, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(225, '01725Z22D1Z001COBA', '1A4215700', 24, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(226, '01725Z22D1Z001COBA', '1A4215700', 25, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(227, '01725Z22D1Z001COBA', '1A4215700', 26, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(228, '01725Z22D1Z001COBA', '1A4215700', 27, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(229, '01725Z22D1Z001COBA', '1A4215700', 28, '2026-04-30 09:02:27', 'HECIYS8OWYHJ1JCOBA1'),
(230, '01725Z22D1Z001COBA', '1A4215700', 1, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(231, '01725Z22D1Z001COBA', '1A4215700', 2, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(232, '01725Z22D1Z001COBA', '1A4215700', 3, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(233, '01725Z22D1Z001COBA', '1A4215700', 4, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(234, '01725Z22D1Z001COBA', '1A4215700', 5, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(235, '01725Z22D1Z001COBA', '1A4215700', 6, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(236, '01725Z22D1Z001COBA', '1A4215700', 7, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(237, '01725Z22D1Z001COBA', '1A4215700', 8, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(238, '01725Z22D1Z001COBA', '1A4215700', 9, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(239, '01725Z22D1Z001COBA', '1A4215700', 10, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(240, '01725Z22D1Z001COBA', '1A4215700', 11, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(241, '01725Z22D1Z001COBA', '1A4215700', 12, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(242, '01725Z22D1Z001COBA', '1A4215700', 13, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(243, '01725Z22D1Z001COBA', '1A4215700', 14, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(244, '01725Z22D1Z001COBA', '1A4215700', 15, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(245, '01725Z22D1Z001COBA', '1A4215700', 16, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(246, '01725Z22D1Z001COBA', '1A4215700', 17, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(247, '01725Z22D1Z001COBA', '1A4215700', 18, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(248, '01725Z22D1Z001COBA', '1A4215700', 19, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(249, '01725Z22D1Z001COBA', '1A4215700', 20, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(250, '01725Z22D1Z001COBA', '1A4215700', 21, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(251, '01725Z22D1Z001COBA', '1A4215700', 22, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(252, '01725Z22D1Z001COBA', '1A4215700', 23, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(253, '01725Z22D1Z001COBA', '1A4215700', 24, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(254, '01725Z22D1Z001COBA', '1A4215700', 25, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(255, '01725Z22D1Z001COBA', '1A4215700', 26, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(256, '01725Z22D1Z001COBA', '1A4215700', 27, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(257, '01725Z22D1Z001COBA', '1A4215700', 28, '2026-04-30 09:17:07', 'HECIYS8OWYHJ1JCOBA2'),
(258, '01725Z22D1Z001COBA', '1A4215700', 1, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(259, '01725Z22D1Z001COBA', '1A4215700', 2, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(260, '01725Z22D1Z001COBA', '1A4215700', 3, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(261, '01725Z22D1Z001COBA', '1A4215700', 4, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(262, '01725Z22D1Z001COBA', '1A4215700', 5, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(263, '01725Z22D1Z001COBA', '1A4215700', 6, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(264, '01725Z22D1Z001COBA', '1A4215700', 7, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(265, '01725Z22D1Z001COBA', '1A4215700', 8, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(266, '01725Z22D1Z001COBA', '1A4215700', 9, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(267, '01725Z22D1Z001COBA', '1A4215700', 10, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(268, '01725Z22D1Z001COBA', '1A4215700', 11, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(269, '01725Z22D1Z001COBA', '1A4215700', 12, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(270, '01725Z22D1Z001COBA', '1A4215700', 13, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(271, '01725Z22D1Z001COBA', '1A4215700', 14, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(272, '01725Z22D1Z001COBA', '1A4215700', 15, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(273, '01725Z22D1Z001COBA', '1A4215700', 16, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(274, '01725Z22D1Z001COBA', '1A4215700', 17, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(275, '01725Z22D1Z001COBA', '1A4215700', 18, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(276, '01725Z22D1Z001COBA', '1A4215700', 19, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(277, '01725Z22D1Z001COBA', '1A4215700', 20, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(278, '01725Z22D1Z001COBA', '1A4215700', 21, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(279, '01725Z22D1Z001COBA', '1A4215700', 22, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(280, '01725Z22D1Z001COBA', '1A4215700', 23, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(281, '01725Z22D1Z001COBA', '1A4215700', 24, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(282, '01725Z22D1Z001COBA', '1A4215700', 25, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(283, '01725Z22D1Z001COBA', '1A4215700', 26, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(284, '01725Z22D1Z001COBA', '1A4215700', 27, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3'),
(285, '01725Z22D1Z001COBA', '1A4215700', 28, '2026-04-30 09:50:37', 'HECIYS8OWYHJ1JCOBA3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role`
--

CREATE TABLE `tbl_role` (
  `role_id` int NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_role`
--

INSERT INTO `tbl_role` (`role_id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Super Admin', NULL, '2026-04-22 06:08:02'),
(2, 'Operator', NULL, '2026-04-22 10:08:55'),
(3, 'Admin', NULL, '2026-04-22 18:14:23'),
(4, 'Line', NULL, '2026-04-27 04:43:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_role_menu`
--

CREATE TABLE `tbl_role_menu` (
  `id` int NOT NULL,
  `role_id` int NOT NULL,
  `menu_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_role_menu`
--

INSERT INTO `tbl_role_menu` (`id`, `role_id`, `menu_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(5, 1, 3),
(6, 1, 4),
(7, 1, 5),
(8, 1, 6),
(9, 1, 7),
(10, 1, 8),
(11, 1, 9),
(12, 1, 10),
(13, 1, 11),
(19, 1, 12),
(3, 1, 15),
(4, 1, 16),
(14, 1, 17),
(15, 1, 18),
(16, 1, 19),
(17, 1, 20),
(18, 1, 21),
(20, 1, 22),
(26, 2, 13),
(27, 3, 1),
(28, 3, 2),
(31, 3, 3),
(32, 3, 4),
(33, 3, 5),
(34, 3, 6),
(35, 3, 7),
(36, 3, 8),
(37, 3, 9),
(38, 3, 10),
(39, 3, 11),
(45, 3, 12),
(29, 3, 15),
(30, 3, 16),
(40, 3, 17),
(41, 3, 18),
(42, 3, 19),
(43, 3, 20),
(44, 3, 21),
(46, 3, 22),
(47, 4, 13);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_shift`
--

CREATE TABLE `tbl_shift` (
  `shift_id` int NOT NULL,
  `shift` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start` int NOT NULL,
  `end` int NOT NULL,
  `time_coffe` int NOT NULL,
  `duration_time` int NOT NULL,
  `break_makan` int NOT NULL,
  `duration_bm` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_shift`
--

INSERT INTO `tbl_shift` (`shift_id`, `shift`, `start`, `end`, `time_coffe`, `duration_time`, `break_makan`, `duration_bm`) VALUES
(1, '1', 7, 15, 560, 10, 710, 50),
(2, '2', 15, 23, 1030, 10, 1170, 50),
(3, '3', 23, 7, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_supplier`
--

CREATE TABLE `tbl_supplier` (
  `id_supplier` int NOT NULL,
  `name_supplier` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('supplier','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'supplier',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_supplier`
--

INSERT INTO `tbl_supplier` (`id_supplier`, `name_supplier`, `status`, `created_at`, `created_by`) VALUES
(1, 'PT. SAGA HIKARI TEKNINDO SEJATI', 'supplier', '2026-04-05 09:45:19', 'admin'),
(2, 'PT. SPACE', 'supplier', '2026-04-05 09:45:19', 'admin'),
(3, 'PT. SANSYU', 'supplier', '2026-04-05 09:45:19', 'admin'),
(4, 'PT. STI', 'supplier', '2026-04-05 09:45:19', 'admin'),
(5, 'PT. ARMSTRONG', 'supplier', '2026-04-05 09:45:19', 'admin'),
(6, 'PT. ARTRON INTERNATIONAL PTE. LTD.', 'supplier', '2026-04-05 09:45:19', 'admin'),
(7, 'PT. VS TECHNOLOGY INDONESIA', 'supplier', '2026-04-05 09:45:19', 'admin'),
(8, 'PT. TOTOKU', 'supplier', '2026-04-05 09:45:19', 'admin'),
(9, 'PT. SHINKO (PTE) LTD', 'supplier', '2026-04-05 09:45:19', 'admin'),
(10, 'PT. IKPI', 'supplier', '2026-04-05 09:45:19', 'admin'),
(13, 'PT. STI', 'customer', '2026-04-05 10:08:59', 'admin'),
(16, 'PT. ABC', 'supplier', '2026-04-07 14:07:41', 'admin'),
(17, 'PT. ABD', 'supplier', '2026-04-07 14:07:41', 'admin'),
(18, 'PT. CBA', 'supplier', '2026-04-07 14:07:41', 'admin'),
(19, 'PT. DEC', 'supplier', '2026-04-07 14:07:41', 'admin'),
(20, 'PT. ABC', 'customer', '2026-04-16 21:50:08', 'admin'),
(21, 'PT. ABD', 'customer', '2026-04-16 21:50:08', 'admin'),
(22, 'PT. CBA', 'customer', '2026-04-16 21:50:08', 'admin'),
(23, 'PT. DEC', 'customer', '2026-04-16 21:50:08', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit_material`
--

CREATE TABLE `tbl_unit_material` (
  `id` int NOT NULL,
  `unit_id` int NOT NULL,
  `part_code` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `lot_no` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `used_qty` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `ref_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int NOT NULL,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rule` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `password`, `rule`, `role_id`) VALUES
(1, 'admin', '$2y$10$wwX59l.50xDpWJAYPhgzmOAw1HFMjK7.q5AY7YgIkyNrh.oeCy6XC', 'admin', 1),
(5, 'operator', '$2y$10$wwX59l.50xDpWJAYPhgzmOAw1HFMjK7.q5AY7YgIkyNrh.oeCy6XC', 'operator', 2),
(6, 'K01', '$2y$10$LTHmyWVVqIQoLrkNHqpVVuNVEsDEmuMLqT8W9wIliLyKZcMJ5PRwq', 'line', NULL),
(7, 'CL01', '$2y$10$N4wfBh/bRA9KHn3rM5XguOzYj8CdXjl9yR3yf/ftiaXVTs9vN.zny', 'line', NULL),
(8, 'operator3', '$2y$10$EoeZLYZs3BlrGo99OrRCoeQVbUbOY7lKqQ8Ro.qIdA.ddGX87fz0W', 'operator', NULL),
(10, 'A03', '$2y$10$plHkxUZ.nj18zF2ctLO84uwql9bJTmrU2KE6t6iwNTdo.fyvyQYkm', 'line', NULL),
(11, '1PAPAYA', '$2y$10$lP4PCyBBAEJqWQEmW7.1oOvI8Yzdj1iIgzdVekjxiuPAHBGoncbwa', 'line', NULL),
(12, '121', '$2y$10$t407Bq9Hg005WZXcyNAT6On7veYCUIhADHk3k4YtiR1lFDCT6q8QW', 'operator', 2),
(13, '1211', '$2y$10$/K1PkzMYcXLFw.VVxwtbW.wyeHV7SbljTG4fEuBA7DYDwas8XWv9C', 'operator', 2),
(14, 'coba', '$2y$10$qyX6fr7K2IZBMnzpa10bYu8Op1Gaak8O1Vv1tDz3HLRb5NoFXTz/W', 'operator', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_active_material`
--
ALTER TABLE `tbl_active_material`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `part_code_2` (`part_code`,`line_id`,`lot_no`,`ref_number`) USING BTREE,
  ADD KEY `ref_number` (`ref_number`);

--
-- Indexes for table `tbl_detail_part`
--
ALTER TABLE `tbl_detail_part`
  ADD PRIMARY KEY (`ref_number`),
  ADD KEY `part_code` (`part_code`) USING BTREE,
  ADD KEY `incoming_date` (`incoming_date`),
  ADD KEY `lot_no` (`lot_no`);

--
-- Indexes for table `tbl_detail_product`
--
ALTER TABLE `tbl_detail_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_code` (`product_code`,`serial_no`,`line_id`,`ref_number`,`created_at`),
  ADD KEY `shift` (`shift`);

--
-- Indexes for table `tbl_detail_production`
--
ALTER TABLE `tbl_detail_production`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_detail_production_planning`
--
ALTER TABLE `tbl_detail_production_planning`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pp_id` (`pp_id`,`jam`);

--
-- Indexes for table `tbl_detail_products`
--
ALTER TABLE `tbl_detail_products`
  ADD KEY `product_code` (`ref_number`),
  ADD KEY `part_code` (`part_code`);

--
-- Indexes for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  ADD PRIMARY KEY (`karyawan_id`);

--
-- Indexes for table `tbl_line`
--
ALTER TABLE `tbl_line`
  ADD PRIMARY KEY (`line_id`),
  ADD KEY `id_line` (`line_id`,`line_name`);

--
-- Indexes for table `tbl_material_loss`
--
ALTER TABLE `tbl_material_loss`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `tbl_model`
--
ALTER TABLE `tbl_model`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_2` (`name`),
  ADD KEY `name` (`name`,`part_code`);

--
-- Indexes for table `tbl_ng_part`
--
ALTER TABLE `tbl_ng_part`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ng_id` (`ng_id`),
  ADD KEY `part_code` (`part_code`);

--
-- Indexes for table `tbl_ng_product`
--
ALTER TABLE `tbl_ng_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serial_no` (`serial_no`),
  ADD KEY `product_code` (`product_code`);

--
-- Indexes for table `tbl_ng_type`
--
ALTER TABLE `tbl_ng_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ng_code` (`ng_code`);

--
-- Indexes for table `tbl_ng_type_detail`
--
ALTER TABLE `tbl_ng_type_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_part`
--
ALTER TABLE `tbl_part`
  ADD PRIMARY KEY (`id_part`),
  ADD UNIQUE KEY `part_code` (`part_code`,`supplier`) USING BTREE,
  ADD KEY `qty` (`qty`),
  ADD KEY `part_name` (`part_name`),
  ADD KEY `supplier` (`supplier`),
  ADD KEY `status_assy` (`status_assy`);

--
-- Indexes for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  ADD PRIMARY KEY (`id_pa`),
  ADD UNIQUE KEY `part_assy_2` (`part_assy`,`part_code`,`id_pa`) USING BTREE,
  ADD KEY `part_assy` (`part_assy`),
  ADD KEY `part_code` (`part_code`);

--
-- Indexes for table `tbl_pp_material`
--
ALTER TABLE `tbl_pp_material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pp_id` (`pp_id`,`part_id`,`part_code`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`part_code`);

--
-- Indexes for table `tbl_production_output`
--
ALTER TABLE `tbl_production_output`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_production_planning`
--
ALTER TABLE `tbl_production_planning`
  ADD PRIMARY KEY (`pp_id`),
  ADD KEY `line_id` (`line_id`),
  ADD KEY `product_code` (`product_code`),
  ADD KEY `pp_code` (`pp_code`);

--
-- Indexes for table `tbl_product_unit`
--
ALTER TABLE `tbl_product_unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serial_no` (`serial_no`),
  ADD KEY `product_code` (`product_code`);

--
-- Indexes for table `tbl_role`
--
ALTER TABLE `tbl_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tbl_role_menu`
--
ALTER TABLE `tbl_role_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_menu` (`role_id`,`menu_id`);

--
-- Indexes for table `tbl_shift`
--
ALTER TABLE `tbl_shift`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  ADD PRIMARY KEY (`id_supplier`),
  ADD UNIQUE KEY `unique_supplier` (`name_supplier`,`status`),
  ADD KEY `name_supplier` (`name_supplier`);

--
-- Indexes for table `tbl_unit_material`
--
ALTER TABLE `tbl_unit_material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `part_code` (`part_code`),
  ADD KEY `lot_no` (`lot_no`),
  ADD KEY `unit_id_2` (`unit_id`,`part_code`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_active_material`
--
ALTER TABLE `tbl_active_material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tbl_detail_product`
--
ALTER TABLE `tbl_detail_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_detail_production`
--
ALTER TABLE `tbl_detail_production`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `tbl_detail_production_planning`
--
ALTER TABLE `tbl_detail_production_planning`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  MODIFY `karyawan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_line`
--
ALTER TABLE `tbl_line`
  MODIFY `line_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_material_loss`
--
ALTER TABLE `tbl_material_loss`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  MODIFY `menu_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tbl_model`
--
ALTER TABLE `tbl_model`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_ng_part`
--
ALTER TABLE `tbl_ng_part`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_ng_product`
--
ALTER TABLE `tbl_ng_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_ng_type`
--
ALTER TABLE `tbl_ng_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_ng_type_detail`
--
ALTER TABLE `tbl_ng_type_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_part`
--
ALTER TABLE `tbl_part`
  MODIFY `id_part` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  MODIFY `id_pa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT for table `tbl_pp_material`
--
ALTER TABLE `tbl_pp_material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `tbl_production_output`
--
ALTER TABLE `tbl_production_output`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_production_planning`
--
ALTER TABLE `tbl_production_planning`
  MODIFY `pp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_product_unit`
--
ALTER TABLE `tbl_product_unit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `tbl_role`
--
ALTER TABLE `tbl_role`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_role_menu`
--
ALTER TABLE `tbl_role_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tbl_shift`
--
ALTER TABLE `tbl_shift`
  MODIFY `shift_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  MODIFY `id_supplier` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_unit_material`
--
ALTER TABLE `tbl_unit_material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
