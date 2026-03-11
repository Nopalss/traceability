-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 07, 2026 at 03:32 AM
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
  `ref_number` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_active_material`
--

INSERT INTO `tbl_active_material` (`id`, `part_code`, `lot_no`, `spq`, `remain`, `ref_number`, `updated_at`) VALUES
(15, '189427200', '02925Z24A240865', 300, 296, 'HE6I8BC7IRG3TE3M', '2026-03-05 15:26:24'),
(16, '169387500', '01726108D1Z001B', 300, 296, 'HEPRQ4A2NOHJ2MWA', '2026-03-05 15:26:24'),
(17, '169387600', '01725Z22D1Z001C', 300, 296, 'HECIYS8OWYHJ1JBY', '2026-03-05 15:26:24'),
(18, '217711301', '083259241ID0001', 999, 1893, 'HEMEIN640WAJ0AYB', '2026-02-27 14:59:52');

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
  `remarks` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_part`
--

INSERT INTO `tbl_detail_part` (`ref_number`, `part_code`, `qty`, `remain`, `incoming_date`, `status`, `lot_no`, `remarks`) VALUES
('HE6I8BC7IRG3TE3A', '189427200', 300, 300, '2026-02-21 09:55:46', 'IN', '02925Z24A24086A', 'POM Kepital/NX-20 Natural'),
('HE6I8BC7IRG3TE3B', '189427200', 300, 300, '2026-02-21 09:55:52', 'IN', '02925Z24A24086B', 'POM Kepital/NX-20 Natural'),
('HE6I8BC7IRG3TE3C', '189427200', 300, 300, '2026-02-21 09:55:59', 'IN', '02925Z24A24086C', 'POM Kepital/NX-20 Natural'),
('HE6I8BC7IRG3TE3D', '189427200', 300, 300, '2026-02-21 09:56:06', 'IN', '02925Z24A24086D', 'POM Kepital/NX-20 Natural'),
('HE6I8BC7IRG3TE3E', '189427200', 300, 300, '2026-02-21 09:56:13', 'IN', '02925Z24A24086E', 'POM Kepital/NX-20 Natural'),
('HE6I8BC7IRG3TE3F', '189427200', 300, 300, '2026-02-21 09:56:26', 'IN', '02925Z24A24086F', 'POM Kepital/NX-20 Natural'),
('HE6I8BC7IRG3TE3M', '189427200', 300, 296, '2026-02-21 09:55:40', 'REPLACED', '02925Z24A240865', 'POM Kepital/NX-20 Natural'),
('HECIYS8OWYHJ1JB1', '169387600', 300, 300, '2026-02-27 12:55:40', 'IN', '01725Z22D1Z0011', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JB2', '169387600', 300, 300, '2026-02-27 12:56:23', 'IN', '01725Z22D1Z0012', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBA', '169387600', 300, 300, '2026-02-21 09:53:10', 'IN', '01725Z22D1Z001A', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBC', '169387600', 300, 300, '2026-02-21 09:53:27', 'IN', '01725Z22D1Z001C', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBD', '169387600', 300, 300, '2026-02-21 09:53:35', 'IN', '01725Z22D1Z001D', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBE', '169387600', 300, 300, '2026-02-21 09:53:42', 'IN', '01725Z22D1Z001E', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBF', '169387600', 300, 300, '2026-02-21 09:53:55', 'IN', '01725Z22D1Z001F', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBY', '169387600', 300, 296, '2026-02-21 09:47:17', 'REPLACED', '01725Z22D1Z001C', 'XYRON'),
('HEMEIN640WAJ0AYA', '217711301', 999, 999, '2026-02-21 09:54:29', 'IN', '083259241ID000A', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEMEIN640WAJ0AYB', '217711301', 999, 915, '2026-02-21 09:54:40', 'IN', '083259241ID000B', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEMEIN640WAJ0AYC', '217711301', 999, 999, '2026-02-21 09:54:50', 'IN', '083259241ID000C', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEMEIN640WAJ0AYD', '217711301', 999, 999, '2026-02-21 09:55:02', 'IN', '083259241ID000D', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEMEIN640WAJ0AYE', '217711301', 999, 999, '2026-02-21 09:55:10', 'IN', '083259241ID000E', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEMEIN640WAJ0AYF', '217711301', 999, 999, '2026-02-21 09:55:19', 'IN', '083259241ID000F', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEMEIN640WAJ0AYL', '217711301', 999, 978, '2026-02-21 09:54:12', 'IN', '083259241ID0001', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEPFF7DWWA361DPI', '169455303', 25, 25, '2026-02-27 14:30:17', 'IN', '09926109C110126', 'NAIAD;N1050/E473097/SABIC JAPAN'),
('HEPRQ4A2NOHJ2MWA', '169387500', 300, 296, '2026-02-21 09:45:25', 'REPLACED', '01726108D1Z001B', 'ABS TOYOLAC 500-322 NATURAL'),
('HEPRQ4A2NOHJ2MWB', '169387500', 300, 300, '2026-02-21 09:46:11', 'IN', '01726108D1Z001A', 'ABS TOYOLAC 500-322 NATURAL'),
('HEPRQ4A2NOHJ2MWC', '169387500', 300, 300, '2026-02-21 09:46:38', 'IN', '01726108D1Z001C', 'ABS TOYOLAC 500-322 NATURAL'),
('HEPRQ4A2NOHJ2MWD', '169387500', 300, 300, '2026-02-21 09:46:48', 'IN', '01726108D1Z001D', 'ABS TOYOLAC 500-322 NATURAL'),
('HEPRQ4A2NOHJ2MWE', '169387500', 300, 300, '2026-02-21 09:46:56', 'IN', '01726108D1Z001E', 'ABS TOYOLAC 500-322 NATURAL'),
('HEPRQ4A2NOHJ2MWF', '169387500', 300, 300, '2026-02-21 09:47:06', 'IN', '01726108D1Z001F', 'ABS TOYOLAC 500-322 NATURAL'),
('HG4F95SEFP1AV976', '174617201', 30, 30, '2026-02-27 14:28:40', 'IN', '06426225D010000', 'LOUVRE;PS PC2065 SJM3889S9 EBCK');

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
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('in','out') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'in',
  `location` int DEFAULT '0',
  `out_date` datetime DEFAULT NULL,
  `is_ng` tinyint DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_product`
--

INSERT INTO `tbl_detail_product` (`id`, `product_code`, `serial_no`, `qty`, `shift`, `line_id`, `operator`, `ref_number`, `remarks`, `created_at`, `status`, `location`, `out_date`, `is_ng`) VALUES
(2, '217711301', '083259241ID0001', 1, 1, 7, 'admin', 'HEMEIN640WAJ0AYL', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR', '2026-02-21 10:13:08', 'out', 1, '2026-02-27 09:49:01', 0),
(3, '217711301', '083259241ID0002', 1, 1, 7, 'admin', 'HEMEIN640WAJ0AYC', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR', '2026-02-21 10:16:30', 'in', NULL, NULL, 0),
(13, '169387500', '01726108D1Z001B', 1, 1, 7, 'operator', 'HEPRQ4A2NOHJ2MWA', 'ABS TOYOLAC 500-322 NATURAL', '2026-02-27 12:32:59', 'in', 0, NULL, 0),
(14, '169387500', '01726108D1Z001A', 2, 1, 7, 'operator', 'HEPRQ4A2NOHJ2MWA', 'ABS TOYOLAC 500-322 NATURAL', '2026-02-27 12:33:57', 'in', 0, NULL, 0),
(15, '169387500', '01726108D1Z0011', 1, 1, 7, 'operator', 'HEPRQ4A2NOHJ2MW1', 'ABS TOYOLAC 500-322 NATURAL', '2026-02-27 14:57:22', 'out', 2, '2026-02-27 15:23:05', 0),
(16, '169387500', '01726108D1Z0012', 1, 1, 7, 'operator', 'HEPRQ4A2NOHJ2MW2', 'ABS TOYOLAC 500-322 NATURAL', '2026-02-27 14:57:45', 'in', 0, NULL, 0),
(18, '169387500', '01726108D1Z0020', 10, 1, 7, 'operator', 'HEPRQ4A2NOHJ2MW19', 'ABS TOYOLAC 500-322 NATURAL', '2026-02-27 14:59:52', 'in', 0, NULL, 0),
(19, '217711301', '083259241ID0100', 1, 2, 7, 'operator', 'HEMEIN640WAJ0AYZ', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR', '2026-03-05 15:25:17', 'in', 0, NULL, 0),
(20, '217711301', '083259241ID101', 1, 2, 7, 'operator', 'HEMEIN640WAJ0AZZ', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR', '2026-03-05 15:26:24', 'in', 0, NULL, 0);

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
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_detail_production`
--

INSERT INTO `tbl_detail_production` (`id`, `product_code`, `serial_no`, `part_code`, `used_qty`, `lot_no`, `created_at`) VALUES
(1, '217711301', '083259241ID0001', '169387600', 1, '01725Z22D1Z001C', '2026-02-21 10:13:08'),
(2, '217711301', '083259241ID0001', '189427200', 1, '02925Z24A240865', '2026-02-21 10:13:08'),
(3, '217711301', '083259241ID0001', '169387500', 1, '01726108D1Z001B', '2026-02-21 10:13:08'),
(4, '217711301', '083259241ID0002', '169387600', 1, '01725Z22D1Z001C', '2026-02-21 10:16:30'),
(5, '217711301', '083259241ID0002', '189427200', 1, '02925Z24A240865', '2026-02-21 10:16:30'),
(6, '217711301', '083259241ID0002', '169387500', 1, '01726108D1Z001B', '2026-02-21 10:16:30'),
(9, '169387500', '01726108D1Z001B', '217711301', 7, '083259241ID0001', '2026-02-27 12:32:59'),
(10, '169387500', '01726108D1Z001A', '217711301', 14, '083259241ID0001', '2026-02-27 12:33:57'),
(11, '169387500', '01726108D1Z0011', '217711301', 7, '083259241ID0001', '2026-02-27 14:57:22'),
(12, '169387500', '01726108D1Z0012', '217711301', 7, '083259241ID0001', '2026-02-27 14:57:45'),
(13, '169387500', '01726108D1Z0020', '217711301', 70, '083259241ID0001', '2026-02-27 14:59:52'),
(14, '217711301', '083259241ID0100', '169387600', 1, '01725Z22D1Z001C', '2026-03-05 15:25:17'),
(15, '217711301', '083259241ID0100', '189427200', 1, '02925Z24A240865', '2026-03-05 15:25:17'),
(16, '217711301', '083259241ID0100', '169387500', 1, '01726108D1Z001B', '2026-03-05 15:25:17'),
(17, '217711301', '083259241ID101', '169387600', 1, '01725Z22D1Z001C', '2026-03-05 15:26:24'),
(18, '217711301', '083259241ID101', '189427200', 1, '02925Z24A240865', '2026-03-05 15:26:24'),
(19, '217711301', '083259241ID101', '169387500', 1, '01726108D1Z001B', '2026-03-05 15:26:24');

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
(47, 140, '7:00-8:00', 2, 2, 'planned'),
(48, 140, '8:00-9:00', 2, 0, 'planned'),
(49, 140, '9:00-10:00', 3, 0, 'planned'),
(50, 140, '10:00-11:00', 3, 0, 'planned'),
(51, 140, '11:00-12:00', 3, 0, 'planned'),
(52, 140, '12:00-13:00', 0, 0, 'planned'),
(53, 140, '13:00-14:00', 0, 0, 'planned'),
(54, 140, '14:00-15:00', 0, 0, 'planned'),
(55, 140, '15:00-16:00', 0, 0, 'planned'),
(56, 140, 'OT', 0, 0, 'planned'),
(57, 141, '7:00-8:00', 0, 0, 'planned'),
(58, 141, '8:00-9:00', 0, 0, 'planned'),
(59, 141, '9:00-10:00', 0, 0, 'planned'),
(60, 141, '10:00-11:00', 0, 0, 'planned'),
(61, 141, '11:00-12:00', 0, 0, 'planned'),
(62, 141, '12:00-13:00', 0, 0, 'planned'),
(63, 141, '13:00-14:00', 2, 0, 'planned'),
(64, 141, '14:00-15:00', 6, 0, 'planned'),
(65, 141, '15:00-16:00', 3, 0, 'planned'),
(66, 141, 'OT', 2, 0, 'planned'),
(67, 142, '19:00-20:00', 2, 0, 'planned'),
(68, 142, '20:00-21:00', 2, 0, 'planned'),
(69, 142, '21:00-22:00', 3, 0, 'planned'),
(70, 142, '22:00-23:00', 2, 0, 'planned'),
(71, 142, '23:00-0:00', 2, 0, 'planned'),
(72, 142, '0:00-1:00', 2, 0, 'planned'),
(73, 142, '1:00-2:00', 0, 0, 'planned'),
(74, 142, '2:00-3:00', 0, 0, 'planned'),
(75, 142, '3:00-4:00', 0, 0, 'planned'),
(76, 142, '4:00-5:00', 0, 0, 'planned'),
(77, 142, '5:00-6:00', 0, 0, 'planned'),
(78, 142, '6:00-7:00', 0, 0, 'planned'),
(79, 142, 'OT', 0, 0, 'planned'),
(80, 143, '19:00-20:00', 0, 0, 'planned'),
(81, 143, '20:00-21:00', 0, 0, 'planned'),
(82, 143, '21:00-22:00', 0, 0, 'planned'),
(83, 143, '22:00-23:00', 0, 0, 'planned'),
(84, 143, '23:00-0:00', 0, 0, 'planned'),
(85, 143, '0:00-1:00', 0, 0, 'planned'),
(86, 143, '1:00-2:00', 0, 0, 'planned'),
(87, 143, '2:00-3:00', 0, 0, 'planned'),
(88, 143, '3:00-4:00', 0, 0, 'planned'),
(89, 143, '4:00-5:00', 3, 0, 'planned'),
(90, 143, '5:00-6:00', 1, 0, 'planned'),
(91, 143, '6:00-7:00', 2, 0, 'planned'),
(92, 143, 'OT', 2, 0, 'planned'),
(93, 144, '7:00-8:00', 4, 0, 'planned'),
(94, 144, '8:00-9:00', 8, 0, 'planned'),
(95, 144, '9:00-10:00', 8, 0, 'planned'),
(96, 144, '10:00-11:00', 8, 0, 'planned'),
(97, 144, '11:00-12:00', 8, 0, 'planned'),
(98, 144, '12:00-13:00', 0, 0, 'planned'),
(99, 144, '13:00-14:00', 0, 0, 'planned'),
(100, 144, '14:00-15:00', 0, 0, 'planned'),
(101, 144, '15:00-16:00', 0, 0, 'planned'),
(102, 144, 'OT', 0, 0, 'planned'),
(103, 145, '7:00-8:00', 0, 0, 'planned'),
(104, 145, '8:00-9:00', 0, 0, 'planned'),
(105, 145, '9:00-10:00', 0, 0, 'planned'),
(106, 145, '10:00-11:00', 0, 0, 'planned'),
(107, 145, '11:00-12:00', 0, 0, 'planned'),
(108, 145, '12:00-13:00', 0, 0, 'planned'),
(109, 145, '13:00-14:00', 9, 0, 'planned'),
(110, 145, '14:00-15:00', 9, 0, 'planned'),
(111, 145, '15:00-16:00', 9, 0, 'planned'),
(112, 145, 'OT', 9, 0, 'planned'),
(113, 146, '19:00-20:00', 8, 0, 'planned'),
(114, 146, '20:00-21:00', 8, 0, 'planned'),
(115, 146, '21:00-22:00', 8, 0, 'planned'),
(116, 146, '22:00-23:00', 8, 0, 'planned'),
(117, 146, '23:00-0:00', 8, 0, 'planned'),
(118, 146, '0:00-1:00', 0, 0, 'planned'),
(119, 146, '1:00-2:00', 0, 0, 'planned'),
(120, 146, '2:00-3:00', 0, 0, 'planned'),
(121, 146, '3:00-4:00', 0, 0, 'planned'),
(122, 146, '4:00-5:00', 0, 0, 'planned'),
(123, 146, '5:00-6:00', 0, 0, 'planned'),
(124, 146, '6:00-7:00', 0, 0, 'planned'),
(125, 146, 'OT', 0, 0, 'planned'),
(126, 147, '19:00-20:00', 99, 0, 'planned'),
(127, 147, '20:00-21:00', 9, 0, 'planned'),
(128, 147, '21:00-22:00', 9, 0, 'planned'),
(129, 147, '22:00-23:00', 9, 0, 'planned'),
(130, 147, '23:00-0:00', 0, 0, 'planned'),
(131, 147, '0:00-1:00', 0, 0, 'planned'),
(132, 147, '1:00-2:00', 0, 0, 'planned'),
(133, 147, '2:00-3:00', 0, 0, 'planned'),
(134, 147, '3:00-4:00', 0, 0, 'planned'),
(135, 147, '4:00-5:00', 0, 0, 'planned'),
(136, 147, '5:00-6:00', 0, 0, 'planned'),
(137, 147, '6:00-7:00', 0, 0, 'planned'),
(138, 147, 'OT', 0, 0, 'planned'),
(162, 150, '7:00-8:00', 90, 0, 'planned'),
(163, 150, '8:00-9:00', 0, 0, 'planned'),
(164, 150, '9:00-10:00', 0, 0, 'planned'),
(165, 150, '10:00-11:00', 0, 0, 'planned'),
(166, 150, '11:00-12:00', 0, 0, 'planned'),
(167, 150, '12:00-13:00', 0, 0, 'planned'),
(168, 150, '13:00-14:00', 0, 0, 'planned'),
(169, 150, '14:00-15:00', 0, 0, 'planned'),
(170, 150, '15:00-16:00', 0, 0, 'planned'),
(171, 150, 'OT', 0, 0, 'planned'),
(172, 151, '19:00-20:00', 99, 0, 'planned'),
(173, 151, '20:00-21:00', 0, 0, 'planned'),
(174, 151, '21:00-22:00', 0, 0, 'planned'),
(175, 151, '22:00-23:00', 0, 0, 'planned'),
(176, 151, '23:00-0:00', 0, 0, 'planned'),
(177, 151, '0:00-1:00', 0, 0, 'planned'),
(178, 151, '1:00-2:00', 0, 0, 'planned'),
(179, 151, '2:00-3:00', 0, 0, 'planned'),
(180, 151, '3:00-4:00', 0, 0, 'planned'),
(181, 151, '4:00-5:00', 0, 0, 'planned'),
(182, 151, '5:00-6:00', 9, 0, 'planned'),
(183, 151, '6:00-7:00', 0, 0, 'planned'),
(184, 151, 'OT', 0, 0, 'planned'),
(218, 155, '7:00-8:00', 2, 0, 'planned'),
(219, 155, '8:00-9:00', 5, 0, 'planned'),
(220, 155, '9:00-10:00', 5, 0, 'planned'),
(221, 155, '10:00-11:00', 5, 0, 'planned'),
(222, 155, '11:00-12:00', 5, 0, 'planned'),
(223, 155, '12:00-13:00', 0, 0, 'planned'),
(224, 155, '13:00-14:00', 0, 0, 'planned'),
(225, 155, '14:00-15:00', 0, 0, 'planned'),
(226, 155, '15:00-16:00', 0, 0, 'planned'),
(227, 155, 'OT', 0, 0, 'planned'),
(228, 156, '7:00-8:00', 0, 0, 'planned'),
(229, 156, '8:00-9:00', 0, 0, 'planned'),
(230, 156, '9:00-10:00', 0, 0, 'planned'),
(231, 156, '10:00-11:00', 0, 0, 'planned'),
(232, 156, '11:00-12:00', 0, 0, 'planned'),
(233, 156, '12:00-13:00', 0, 0, 'planned'),
(234, 156, '13:00-14:00', 2, 2, 'planned'),
(235, 156, '14:00-15:00', 6, 1, 'planned'),
(236, 156, '15:00-16:00', 6, 0, 'planned'),
(237, 156, 'OT', 4, 0, 'planned'),
(238, 157, '19:00-20:00', 2, 0, 'planned'),
(239, 157, '20:00-21:00', 3, 0, 'planned'),
(240, 157, '21:00-22:00', 4, 0, 'planned'),
(241, 157, '22:00-23:00', 0, 0, 'planned'),
(242, 157, '23:00-0:00', 4, 0, 'planned'),
(243, 157, '0:00-1:00', 0, 0, 'planned'),
(244, 157, '1:00-2:00', 0, 0, 'planned'),
(245, 157, '2:00-3:00', 0, 0, 'planned'),
(246, 157, '3:00-4:00', 0, 0, 'planned'),
(247, 157, '4:00-5:00', 0, 0, 'planned'),
(248, 157, '5:00-6:00', 0, 0, 'planned'),
(249, 157, '6:00-7:00', 0, 0, 'planned'),
(250, 157, 'OT', 0, 0, 'planned'),
(313, 164, '7:00-8:00', 1, 0, 'planned'),
(314, 164, '8:00-9:00', 1, 0, 'planned'),
(315, 164, '9:00-10:00', 1, 0, 'planned'),
(316, 164, '10:00-11:00', 1, 0, 'planned'),
(317, 164, '11:00-12:00', 1, 0, 'planned'),
(318, 164, '12:00-13:00', 1, 0, 'planned'),
(319, 164, '13:00-14:00', 0, 0, 'planned'),
(320, 164, '14:00-15:00', 0, 0, 'planned'),
(321, 164, 'OT', 0, 0, 'planned'),
(322, 165, '7:00-8:00', 1, 0, 'planned'),
(323, 165, '8:00-9:00', 1, 0, 'planned'),
(324, 165, '9:00-10:00', 1, 0, 'planned'),
(325, 165, '10:00-11:00', 0, 0, 'planned'),
(326, 165, '11:00-12:00', 1, 0, 'planned'),
(327, 165, '12:00-13:00', 1, 0, 'planned'),
(328, 165, '13:00-14:00', 1, 0, 'planned'),
(329, 165, '14:00-15:00', 0, 0, 'planned'),
(330, 165, 'OT', 0, 0, 'planned'),
(331, 166, '19:00-20:00', 1, 1, 'planned'),
(332, 166, '20:00-21:00', 1, 1, 'planned'),
(333, 166, '21:00-22:00', 1, 0, 'planned'),
(334, 166, '22:00-23:00', 1, 0, 'planned'),
(335, 166, '23:00-0:00', 1, 0, 'planned'),
(336, 166, '0:00-1:00', 1, 0, 'planned'),
(337, 166, '1:00-2:00', 1, 0, 'planned'),
(338, 166, '2:00-3:00', 1, 0, 'planned'),
(339, 166, '3:00-4:00', 1, 0, 'planned'),
(340, 166, '4:00-5:00', 1, 0, 'planned'),
(341, 166, '5:00-6:00', 0, 0, 'planned'),
(342, 166, '6:00-7:00', 0, 0, 'planned'),
(343, 166, 'OT', 0, 0, 'planned');

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
  `no_hp` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(200) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_karyawan`
--

INSERT INTO `tbl_karyawan` (`karyawan_id`, `nip`, `nama`, `no_hp`, `role`, `username`) VALUES
(1, '123456789', 'Operator', '08132861823', 'operator', 'operator'),
(2, '987654321', 'operator3', '081382712821', 'operator', 'operator3');

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
(7, 'A01', '2026-02-22 05:59:54', 'admin'),
(9, 'A02', '2026-02-20 07:35:21', 'admin'),
(10, 'K01', '2026-02-22 05:57:56', 'admin'),
(12, 'A03', '2026-02-27 08:29:24', 'admin');

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
(1, '169387500', 253, 253, 'admin', '2026-02-20 10:54:13', 'Replace material', '218496702', 'HEPRQ4A2NOHJ2MWA', 1, 7),
(2, '169387500', 253, 253, 'admin', '2026-02-20 11:00:12', 'Replace material', '217711301', 'HEPRQ4A2NOHJ2MWA', 1, 7),
(3, '169387500', 600, 600, 'admin', '2026-02-20 11:00:38', 'Replace material', '218496702', 'HEPRQ4A2NOHJ2MWA', 1, 7),
(4, '169387500', 600, 600, 'admin', '2026-02-20 11:00:50', 'Replace material', '218496702', 'HEPRQ4A2NOHJ2MWA', 1, 7),
(5, '169387600', 253, 253, 'admin', '2026-02-20 11:04:58', 'Replace material', '217711301', 'HECIYS8OWYHJ1JBY', 1, 7),
(6, '189427200', 553, 553, 'admin', '2026-02-20 11:05:10', 'Replace material', '217711301', 'HE6I8BC7IRG3TE3M', 1, 7),
(7, '189427200', 291, 291, 'admin', '2026-02-21 09:57:39', 'Replace material', '218496702', 'HE6I8BC7IRG3TE3M', 1, 7),
(8, '169387500', 291, 291, 'admin', '2026-02-21 09:57:57', 'Replace material', '218496702', 'HEPRQ4A2NOHJ2MWA', 1, 7),
(9, '169387600', 291, 291, 'admin', '2026-02-21 09:58:18', 'Replace material', '217711301', 'HECIYS8OWYHJ1JBY', 1, 7);

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
  `ng_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_assy` int DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_part`
--

INSERT INTO `tbl_part` (`id_part`, `part_code`, `part_name`, `supplier`, `qty`, `status`, `status_assy`, `updated`) VALUES
(21, '217711301', 'CABLE, CSIC, INK EJECT', 1, 0, NULL, 1, '2026-02-16 10:39:28'),
(22, '169387600', 'JOINT TUBE', 2, 0, NULL, 0, '2026-02-16 10:40:01'),
(23, '189427200', 'HOLDER', 2, 0, NULL, 0, '2026-02-16 10:40:26'),
(24, '218496702', 'CONNECTOR CSIC', 1, 0, NULL, 1, '2026-02-16 10:40:59'),
(25, '169390101', 'FRAME LOWER RIGHT', 1, 0, NULL, 0, '2026-02-16 10:41:38'),
(26, '169387500', 'HOLDER CABLE CSIC INK EJECT', 1, 0, NULL, 1, '2026-02-16 10:42:17'),
(27, '12464544', 'CONNECTOR CSIC', 1, 0, NULL, 0, '2026-02-20 13:45:56'),
(28, '12345678910', 'Connector', 1, 0, NULL, 0, '2026-02-27 14:23:15'),
(29, '174617201', 'Housing, Front, Right', 1, 0, NULL, 0, '2026-02-27 14:28:23'),
(30, '169455303', 'HOLDER AC INLET LEFT PS UNIT', 1, 0, NULL, 1, '2026-02-27 14:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_part_assy`
--

CREATE TABLE `tbl_part_assy` (
  `id_pa` int NOT NULL,
  `part_assy` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_part_assy`
--

INSERT INTO `tbl_part_assy` (`id_pa`, `part_assy`, `part_code`, `qty`) VALUES
(37, '1010', '1111', 2),
(38, '1010', '4444', 2),
(39, '1010', '333', 2),
(49, '1111', '4444', 4),
(50, '1111', '65432', 5),
(51, '217711301', '169387600', 1),
(52, '217711301', '189427200', 1),
(53, '217711301', '169387500', 1),
(54, '218496702', '169387500', 1),
(55, '218496702', '189427200', 1),
(56, '169387500', '217711301', 7),
(57, '169455303', '169390101', 2),
(58, '169455303', '169387500', 2);

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
  `shift` int DEFAULT NULL,
  `line_id` int DEFAULT NULL,
  `operator` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qty` int DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `result_status` enum('good','ng') COLLATE utf8mb4_general_ci DEFAULT 'good'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_production_output`
--

INSERT INTO `tbl_production_output` (`id`, `product_code`, `serial_no`, `shift`, `line_id`, `operator`, `qty`, `created_at`, `result_status`) VALUES
(34, '217711301', '083259241ID0050', 2, 7, 'admin', 9, '2026-02-20 20:14:25', 'good'),
(36, '217711301', '083259241ID0001', 1, 7, 'admin', 1, '2026-02-21 10:13:08', 'good'),
(37, '217711301', '083259241ID0002', 1, 7, 'admin', 1, '2026-02-21 10:16:30', 'good'),
(47, '169387500', '01726108D1Z001B', 1, 7, 'operator', 1, '2026-02-27 12:32:59', 'good'),
(48, '169387500', '01726108D1Z001A', 1, 7, 'operator', 2, '2026-02-27 12:33:57', 'good'),
(49, '169387500', '01726108D1Z0011', 1, 7, 'operator', 1, '2026-02-27 14:57:22', 'good'),
(50, '169387500', '01726108D1Z0012', 1, 7, 'operator', 1, '2026-02-27 14:57:45', 'good'),
(52, '169387500', '01726108D1Z0020', 1, 7, 'operator', 10, '2026-02-27 14:59:52', 'good'),
(53, '217711301', '083259241ID0100', 2, 7, 'operator', 1, '2026-03-05 15:25:17', 'good'),
(54, '217711301', '083259241ID101', 2, 7, 'operator', 1, '2026-03-05 15:26:24', 'good');

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
(128, 7, '217711301', 1, '2026-02-19', 10, 'planned', 10, 'PP-20260218-37D5'),
(129, 7, '218496702', 1, '2026-02-19', 8, 'planned', 8, 'PP-20260218-37D5'),
(130, 7, '218496702', 2, '2026-02-19', 7, 'planned', 7, 'PP-20260218-37D5'),
(131, 7, '217711301', 2, '2026-02-19', 9, 'planned', 9, 'PP-20260218-37D5'),
(140, 7, '217711301', 1, '2026-02-21', 13, 'planned', 13, 'PP-20260220-1CF0'),
(141, 7, '218496702', 1, '2026-02-21', 13, 'planned', 13, 'PP-20260220-1CF0'),
(142, 7, '218496702', 2, '2026-02-21', 13, 'planned', 13, 'PP-20260220-1CF0'),
(143, 7, '217711301', 2, '2026-02-21', 8, 'planned', 8, 'PP-20260220-1CF0'),
(144, 7, '169387500', 1, '2026-02-23', 36, 'planned', 36, 'PP-20260223-32B3'),
(145, 7, '217711301', 1, '2026-02-23', 36, 'planned', 36, 'PP-20260223-32B3'),
(146, 7, '169387500', 2, '2026-02-23', 40, 'planned', 40, 'PP-20260223-32B3'),
(147, 7, '217711301', 2, '2026-02-23', 126, 'planned', 126, 'PP-20260223-32B3'),
(150, 7, '217711301', 1, '2026-02-26', 90, 'planned', 90, 'PP-20260226-8693'),
(151, 7, '217711301', 2, '2026-02-26', 108, 'planned', 108, 'PP-20260226-8693'),
(155, 7, '217711301', 1, '2026-02-27', 22, 'planned', 22, 'PP-20260227-0353'),
(156, 7, '169387500', 1, '2026-02-27', 18, 'planned', 18, 'PP-20260227-0353'),
(157, 7, '169387500', 2, '2026-02-27', 13, 'planned', 13, 'PP-20260227-0353'),
(164, 7, '169387500', 1, '2026-03-05', 6, 'planned', 6, 'PP-20260305-C110'),
(165, 7, '217711301', 1, '2026-03-05', 6, 'planned', 6, 'PP-20260305-C110'),
(166, 7, '217711301', 2, '2026-03-05', 10, 'planned', 10, 'PP-20260305-C110');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_shift`
--

CREATE TABLE `tbl_shift` (
  `shift_id` int NOT NULL,
  `shift` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start` int NOT NULL,
  `end` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_shift`
--

INSERT INTO `tbl_shift` (`shift_id`, `shift`, `start`, `end`) VALUES
(3, '2', 19, 7),
(4, '1', 7, 15);

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
(1, 'PT. ABC', 'supplier', '2026-02-08 15:03:11', NULL),
(2, 'PT. DEk', 'supplier', '2026-02-08 15:03:11', NULL),
(4, 'PT abc', 'supplier', '2026-03-04 12:18:36', 'admin'),
(7, 'PT. ABC', 'customer', '2026-03-04 12:29:49', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int NOT NULL,
  `username` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rule` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `password`, `rule`) VALUES
(1, 'admin', '$2y$10$wwX59l.50xDpWJAYPhgzmOAw1HFMjK7.q5AY7YgIkyNrh.oeCy6XC', 'admin'),
(5, 'operator', '$2y$10$wwX59l.50xDpWJAYPhgzmOAw1HFMjK7.q5AY7YgIkyNrh.oeCy6XC', 'operator'),
(6, 'K01', '$2y$10$LTHmyWVVqIQoLrkNHqpVVuNVEsDEmuMLqT8W9wIliLyKZcMJ5PRwq', 'line'),
(7, 'A01', '$2y$10$IKEox5atcZamIOQWmkrBLupJP6SqEDGtNMuuzyRMyJK1PaLi.IO6i', 'line'),
(8, 'operator3', '$2y$10$EoeZLYZs3BlrGo99OrRCoeQVbUbOY7lKqQ8Ro.qIdA.ddGX87fz0W', 'operator'),
(10, 'A03', '$2y$10$plHkxUZ.nj18zF2ctLO84uwql9bJTmrU2KE6t6iwNTdo.fyvyQYkm', 'line');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_active_material`
--
ALTER TABLE `tbl_active_material`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_detail_part`
--
ALTER TABLE `tbl_detail_part`
  ADD PRIMARY KEY (`ref_number`),
  ADD KEY `part_code` (`part_code`) USING BTREE;

--
-- Indexes for table `tbl_detail_product`
--
ALTER TABLE `tbl_detail_product`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `tbl_part`
--
ALTER TABLE `tbl_part`
  ADD PRIMARY KEY (`id_part`),
  ADD UNIQUE KEY `part_code` (`part_code`),
  ADD KEY `qty` (`qty`),
  ADD KEY `part_name` (`part_name`),
  ADD KEY `supplier` (`supplier`),
  ADD KEY `status_assy` (`status_assy`);

--
-- Indexes for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  ADD PRIMARY KEY (`id_pa`),
  ADD KEY `part_assy` (`part_assy`),
  ADD KEY `part_code` (`part_code`);

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
-- Indexes for table `tbl_shift`
--
ALTER TABLE `tbl_shift`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  ADD PRIMARY KEY (`id_supplier`),
  ADD KEY `name_supplier` (`name_supplier`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbl_detail_product`
--
ALTER TABLE `tbl_detail_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_detail_production`
--
ALTER TABLE `tbl_detail_production`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_detail_production_planning`
--
ALTER TABLE `tbl_detail_production_planning`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=344;

--
-- AUTO_INCREMENT for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  MODIFY `karyawan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_line`
--
ALTER TABLE `tbl_line`
  MODIFY `line_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_material_loss`
--
ALTER TABLE `tbl_material_loss`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_ng_part`
--
ALTER TABLE `tbl_ng_part`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_ng_product`
--
ALTER TABLE `tbl_ng_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_part`
--
ALTER TABLE `tbl_part`
  MODIFY `id_part` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  MODIFY `id_pa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tbl_production_output`
--
ALTER TABLE `tbl_production_output`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `tbl_production_planning`
--
ALTER TABLE `tbl_production_planning`
  MODIFY `pp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- AUTO_INCREMENT for table `tbl_shift`
--
ALTER TABLE `tbl_shift`
  MODIFY `shift_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  MODIFY `id_supplier` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
