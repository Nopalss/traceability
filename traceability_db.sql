-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 05, 2026 at 09:33 AM
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
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_active_material`
--

INSERT INTO `tbl_active_material` (`id`, `part_code`, `lot_no`, `spq`, `remain`, `ref_number`, `line_id`, `updated_at`) VALUES
(1, '169387600', '01725Z22D1Z001C', 300, 260, 'HECIYS8OWYHJ1JBY', 7, '2026-04-05 16:15:48'),
(2, '217711301', '083259241ID0001', 999, 959, 'HEMEIN640WAJ0AYL', 7, '2026-04-05 16:15:48'),
(3, '218496702', '140261011010001', 770, 730, 'HEFHGGF55H281RNB', 7, '2026-04-05 16:15:48'),
(4, '128237800', '01725Z22D1Z1111', 900, 740, 'HECIYS8OWYHJ1J111', 7, '2026-04-05 16:15:48'),
(5, '159352600', '01725Z22D1Z222', 900, 820, 'HECIYS8OWYHJ1J222', 7, '2026-04-05 16:15:48'),
(6, '169390101', '01725Z22D1Z333', 900, 860, 'HECIYS8OWYHJ1J333', 7, '2026-04-05 16:15:48'),
(7, '172503000', '01725Z22D1Z444', 900, 780, 'HECIYS8OWYHJ1J444', 7, '2026-04-05 16:15:48'),
(8, '212685700', '01725Z22D1Z555', 900, 820, 'HECIYS8OWYHJ1J555', 7, '2026-04-05 16:15:48'),
(9, '169455303', '01725Z22D1Z666', 900, 860, 'HECIYS8OWYHJ1J666', 7, '2026-04-05 16:15:48'),
(10, '169387500', '01725Z22D1Z777', 900, 860, 'HECIYS8OWYHJ1J777', 7, '2026-04-05 16:15:48'),
(11, '169387700', '01725Z22D1Z888', 900, 860, 'HECIYS8OWYHJ1J888', 7, '2026-04-05 16:15:48');

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
('HECIYS8OWYHJ1J111', '128237800', 900, 740, '2026-04-05 13:12:18', 'IN', '01725Z22D1Z1111', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J222', '159352600', 900, 820, '2026-04-05 13:12:26', 'IN', '01725Z22D1Z222', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J333', '169390101', 900, 860, '2026-04-05 13:16:55', 'IN', '01725Z22D1Z333', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J444', '172503000', 900, 780, '2026-04-05 13:17:45', 'IN', '01725Z22D1Z444', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J555', '212685700', 900, 820, '2026-04-05 13:17:55', 'IN', '01725Z22D1Z555', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J666', '169455303', 900, 860, '2026-04-05 13:18:09', 'IN', '01725Z22D1Z666', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J777', '169387500', 900, 860, '2026-04-05 13:18:19', 'IN', '01725Z22D1Z777', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1J888', '169387700', 900, 860, '2026-04-05 13:18:28', 'IN', '01725Z22D1Z888', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBA', '169387600', 300, 300, '2026-04-05 12:40:56', 'IN', '01725Z22D1Z001C', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBAA', '169387600', 300, 300, '2026-04-05 12:42:09', 'IN', '01725Z22D1Z001G', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBB', '169387600', 300, 300, '2026-04-05 12:41:05', 'IN', '01725Z22D1Z001B', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBC', '169387600', 300, 300, '2026-04-05 12:41:34', 'IN', '01725Z22D1Z001D', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBE', '169387600', 300, 300, '2026-04-05 12:41:40', 'IN', '01725Z22D1Z001E', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBF', '169387600', 300, 300, '2026-04-05 12:41:45', 'IN', '01725Z22D1Z001F', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBG', '169387600', 300, 300, '2026-04-05 12:41:50', 'IN', '01725Z22D1Z001G', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBH', '169387600', 300, 300, '2026-04-05 12:42:19', 'IN', '01725Z22D1Z001H', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBI', '169387600', 300, 300, '2026-04-05 12:42:25', 'IN', '01725Z22D1Z001I', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBJ', '169387600', 300, 300, '2026-04-05 12:42:31', 'IN', '01725Z22D1Z001J', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBK', '169387600', 300, 300, '2026-04-05 12:42:37', 'IN', '01725Z22D1Z001K', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBL', '169387600', 300, 300, '2026-04-05 12:42:43', 'IN', '01725Z22D1Z001L', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBLL', '169387600', 300, 300, '2026-04-05 12:42:48', 'IN', '01725Z22D1Z001L', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HECIYS8OWYHJ1JBY', '169387600', 300, 260, '2026-04-05 12:06:55', 'IN', '01725Z22D1Z001C', 'XYRON Z552H OK-X6922 {A1A3301}'),
('HEFHGGF55H281RNB', '218496702', 770, 730, '2026-04-05 12:06:42', 'IN', '140261011010001', 'BY WH E4 MISDA'),
('HEMEIN640WAJ0AYL', '217711301', 999, 959, '2026-04-05 12:06:51', 'IN', '083259241ID0001', 'HEMEIN640WAJ0AYL-(REMAIN SA)E4PS3A-A12 JL-MAR'),
('HEPFF7DWWA361DPI', '169455303', 25, 25, '2026-04-05 12:07:12', 'IN', '09926109C110126', 'NAIAD;N1050/E473097/SABIC JAPAN');

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
(1, '1A4219900', '01725Z22D1Z001A', 10, 1, 7, 'operator', 'HECIYS8OWYHJ1JBA', 'XYRON Z552H OK-X6922 {A1A3301}', '', '2026-04-05 13:37:26', 'in', 0, NULL, 1),
(2, '1A4219900', '01725Z22D1Z001B', 30, 1, 7, 'operator', 'HECIYS8OWYHJ1JBB', 'XYRON Z552H OK-X6922 {A1A3301}', '', '2026-04-05 13:37:49', 'in', 0, NULL, 0);

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
(12, '1A4219900', '01725Z22D1Z001B', '128237800', 120, '01725Z22D1Z1111', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J111', 'HECIYS8OWYHJ1JBB'),
(13, '1A4219900', '01725Z22D1Z001B', '159352600', 60, '01725Z22D1Z222', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J222', 'HECIYS8OWYHJ1JBB'),
(14, '1A4219900', '01725Z22D1Z001B', '169387600', 30, '01725Z22D1Z001C', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1JBY', 'HECIYS8OWYHJ1JBB'),
(15, '1A4219900', '01725Z22D1Z001B', '169390101', 30, '01725Z22D1Z333', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J333', 'HECIYS8OWYHJ1JBB'),
(16, '1A4219900', '01725Z22D1Z001B', '172503000', 90, '01725Z22D1Z444', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J444', 'HECIYS8OWYHJ1JBB'),
(17, '1A4219900', '01725Z22D1Z001B', '212685700', 60, '01725Z22D1Z555', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J555', 'HECIYS8OWYHJ1JBB'),
(18, '1A4219900', '01725Z22D1Z001B', '169455303', 30, '01725Z22D1Z666', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J666', 'HECIYS8OWYHJ1JBB'),
(19, '1A4219900', '01725Z22D1Z001B', '169387500', 30, '01725Z22D1Z777', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J777', 'HECIYS8OWYHJ1JBB'),
(20, '1A4219900', '01725Z22D1Z001B', '217711301', 30, '083259241ID0001', '2026-04-05 13:37:49', 0, 'HEMEIN640WAJ0AYL', 'HECIYS8OWYHJ1JBB'),
(21, '1A4219900', '01725Z22D1Z001B', '218496702', 30, '140261011010001', '2026-04-05 13:37:49', 0, 'HEFHGGF55H281RNB', 'HECIYS8OWYHJ1JBB'),
(22, '1A4219900', '01725Z22D1Z001B', '169387700', 30, '01725Z22D1Z888', '2026-04-05 13:37:49', 0, 'HECIYS8OWYHJ1J888', 'HECIYS8OWYHJ1JBB'),
(23, '1A4219900', '01725Z22D1Z001A', '128237800', 40, '01725Z22D1Z1111', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J111', 'HECIYS8OWYHJ1JBA'),
(24, '1A4219900', '01725Z22D1Z001A', '159352600', 20, '01725Z22D1Z222', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J222', 'HECIYS8OWYHJ1JBA'),
(25, '1A4219900', '01725Z22D1Z001A', '169387600', 10, '01725Z22D1Z001C', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1JBY', 'HECIYS8OWYHJ1JBA'),
(26, '1A4219900', '01725Z22D1Z001A', '169390101', 10, '01725Z22D1Z333', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J333', 'HECIYS8OWYHJ1JBA'),
(27, '1A4219900', '01725Z22D1Z001A', '172503000', 30, '01725Z22D1Z444', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J444', 'HECIYS8OWYHJ1JBA'),
(28, '1A4219900', '01725Z22D1Z001A', '212685700', 20, '01725Z22D1Z555', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J555', 'HECIYS8OWYHJ1JBA'),
(29, '1A4219900', '01725Z22D1Z001A', '169455303', 10, '01725Z22D1Z666', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J666', 'HECIYS8OWYHJ1JBA'),
(30, '1A4219900', '01725Z22D1Z001A', '169387500', 10, '01725Z22D1Z777', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J777', 'HECIYS8OWYHJ1JBA'),
(31, '1A4219900', '01725Z22D1Z001A', '217711301', 10, '083259241ID0001', '2026-04-05 16:15:48', 0, 'HEMEIN640WAJ0AYL', 'HECIYS8OWYHJ1JBA'),
(32, '1A4219900', '01725Z22D1Z001A', '218496702', 10, '140261011010001', '2026-04-05 16:15:48', 0, 'HEFHGGF55H281RNB', 'HECIYS8OWYHJ1JBA'),
(33, '1A4219900', '01725Z22D1Z001A', '169387700', 10, '01725Z22D1Z888', '2026-04-05 16:15:48', 0, 'HECIYS8OWYHJ1J888', 'HECIYS8OWYHJ1JBA');

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
(10, 2, '7:00-8:00', 30, 30, 'planned'),
(11, 2, '8:00-9:00', 30, 10, 'planned'),
(12, 2, '9:00-10:00', 23, 0, 'planned'),
(13, 2, '10:00-11:00', 30, 0, 'planned'),
(14, 2, '11:00-12:00', 22, 0, 'planned'),
(15, 2, '12:00-13:00', 7, 0, 'planned'),
(16, 2, '13:00-14:00', 29, 0, 'planned'),
(17, 2, '14:00-15:00', 29, 0, 'planned'),
(18, 2, 'OT', 0, 0, 'planned');

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
(1, '128237800', 40, 740, 'A01', '2026-04-05 13:38:41', 'ADJUST ADD', '', 'HECIYS8OWYHJ1J111', 0, 7);

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
(1, 'NAIAD 6', '1A4219900', 'admin', '2026-04-05 05:53:32');

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
  `ref_part` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ng_part`
--

INSERT INTO `tbl_ng_part` (`id`, `ng_id`, `part_code`, `lot_no`, `used_qty`, `ng_qty`, `ng_type`, `reason`, `created_at`, `ref_part`) VALUES
(1, 1, '128237800', '01725Z22D1Z1111', 40, 40, 'rusak', NULL, '2026-04-05 13:38:32', 'HECIYS8OWYHJ1J111'),
(2, 2, '128237800', '01725Z22D1Z1111', 40, 40, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J111'),
(3, 2, '159352600', '01725Z22D1Z222', 20, 20, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J222'),
(4, 2, '169387600', '01725Z22D1Z001C', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1JBY'),
(5, 2, '169390101', '01725Z22D1Z333', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J333'),
(6, 2, '172503000', '01725Z22D1Z444', 30, 30, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J444'),
(7, 2, '212685700', '01725Z22D1Z555', 20, 20, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J555'),
(8, 2, '169455303', '01725Z22D1Z666', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J666'),
(9, 2, '169387500', '01725Z22D1Z777', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J777'),
(10, 2, '217711301', '083259241ID0001', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HEMEIN640WAJ0AYL'),
(11, 2, '218496702', '140261011010001', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HEFHGGF55H281RNB'),
(12, 2, '169387700', '01725Z22D1Z888', 10, 10, 'rusak', NULL, '2026-04-05 16:16:04', 'HECIYS8OWYHJ1J888');

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
(1, '01725Z22D1Z001A', '1A4219900', 7, 1, 'A01', 'IN_MECA', '2026-04-05 13:38:31', 'HECIYS8OWYHJ1JBA'),
(2, '01725Z22D1Z001A', '1A4219900', 7, 2, 'A01', 'EXIT_MECA', '2026-04-05 16:16:04', 'HECIYS8OWYHJ1JBA');

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
(1, 'rusak', 'rusak', 'ACTIVE');

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
(1, '128237800', 'C.B.P-TITE SCREW,3X10,F/ZN-3C', 1, 0, 'sp', 0, '2026-04-05 11:07:28'),
(2, '159352600', 'DOUBLE SIDE TAPE,12X10', 2, 0, 'sp', 0, '2026-04-05 11:07:28'),
(3, '169387600', 'JOINT,TUBE', 3, 0, 'sp', 0, '2026-04-05 11:07:28'),
(4, '169390101', 'FRAME,LOWER,RIGHT', 4, 0, 'sp', 0, '2026-04-05 11:07:28'),
(5, '172503000', 'FOOT,8.5X8.5X5', 5, 0, 'sp', 0, '2026-04-05 11:07:28'),
(6, '212685700', 'FERRITE CORE,28R0629-030', 6, 0, 'sp', 0, '2026-04-05 11:07:28'),
(7, '169455303', 'HOLDER,AC INLET,LEFT,PS UNIT', 7, 0, 'sp', 0, '2026-04-05 11:07:28'),
(8, '169387500', 'HOLDER,CABLE,CSIC,INK EJECT', 3, 0, 'sp', 0, '2026-04-05 11:07:28'),
(9, '217711301', 'CABLE,CSIC,INK EJECT', 8, 0, 'sp', 0, '2026-04-05 11:07:28'),
(10, '218496702', 'CONNECTOR,CSIC', 9, 0, 'sp', 0, '2026-04-05 11:07:28'),
(11, '169387700', 'HOLDER,CONNECTOR,CSIC,IS', 10, 0, 'sp', 0, '2026-04-05 11:07:28'),
(12, '189358800', 'HOLDER,CABLE,CSIC,INK EJECT;B', 3, 0, 'sp', 0, '2026-04-05 11:07:28'),
(13, '1A4219900', 'HOUSING LOWER RIGHT ASSY;B;CG17;IEI', 4, 0, 'md', 1, '2026-04-05 12:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_part_assy`
--

CREATE TABLE `tbl_part_assy` (
  `id_pa` int NOT NULL,
  `part_assy` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pcs'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_part_assy`
--

INSERT INTO `tbl_part_assy` (`id_pa`, `part_assy`, `part_code`, `qty`, `unit`) VALUES
(1, '1A4219900', '128237800', 4, 'Pcs'),
(2, '1A4219900', '159352600', 2, 'Pcs'),
(3, '1A4219900', '169387600', 1, 'Pcs'),
(4, '1A4219900', '169390101', 1, 'Pcs'),
(5, '1A4219900', '172503000', 3, 'Pcs'),
(6, '1A4219900', '212685700', 2, 'Pcs'),
(7, '1A4219900', '169455303', 1, 'Pcs'),
(8, '1A4219900', '169387500', 1, 'Pcs'),
(9, '1A4219900', '217711301', 1, 'Pcs'),
(10, '1A4219900', '218496702', 1, 'Pcs'),
(11, '1A4219900', '169387700', 1, 'Pcs');

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
(1, '1A4219900', '01725Z22D1Z001A', '', 1, 7, 'operator', 10, '2026-04-05 13:37:26', 'good', 'HECIYS8OWYHJ1JBA'),
(2, '1A4219900', '01725Z22D1Z001B', '', 1, 7, 'operator', 30, '2026-04-05 13:37:49', 'good', 'HECIYS8OWYHJ1JBB');

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
(2, 7, '1A4219900', 1, '2026-04-05', 200, 'planned', 200, 'PP-20260406-4B79');

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
(1, '01725Z22D1Z001A', '1A4219900', 1, '2026-04-05 13:37:26', 'HECIYS8OWYHJ1JBA'),
(2, '01725Z22D1Z001A', '1A4219900', 2, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(3, '01725Z22D1Z001A', '1A4219900', 3, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(4, '01725Z22D1Z001A', '1A4219900', 4, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(5, '01725Z22D1Z001A', '1A4219900', 5, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(6, '01725Z22D1Z001A', '1A4219900', 6, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(7, '01725Z22D1Z001A', '1A4219900', 7, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(8, '01725Z22D1Z001A', '1A4219900', 8, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(9, '01725Z22D1Z001A', '1A4219900', 9, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(10, '01725Z22D1Z001A', '1A4219900', 10, '2026-04-05 13:37:27', 'HECIYS8OWYHJ1JBA'),
(11, '01725Z22D1Z001B', '1A4219900', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(12, '01725Z22D1Z001B', '1A4219900', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(13, '01725Z22D1Z001B', '1A4219900', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(14, '01725Z22D1Z001B', '1A4219900', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(15, '01725Z22D1Z001B', '1A4219900', 5, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(16, '01725Z22D1Z001B', '1A4219900', 6, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(17, '01725Z22D1Z001B', '1A4219900', 7, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(18, '01725Z22D1Z001B', '1A4219900', 8, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(19, '01725Z22D1Z001B', '1A4219900', 9, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(20, '01725Z22D1Z001B', '1A4219900', 10, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(21, '01725Z22D1Z001B', '1A4219900', 11, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(22, '01725Z22D1Z001B', '1A4219900', 12, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(23, '01725Z22D1Z001B', '1A4219900', 13, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(24, '01725Z22D1Z001B', '1A4219900', 14, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(25, '01725Z22D1Z001B', '1A4219900', 15, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(26, '01725Z22D1Z001B', '1A4219900', 16, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(27, '01725Z22D1Z001B', '1A4219900', 17, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(28, '01725Z22D1Z001B', '1A4219900', 18, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(29, '01725Z22D1Z001B', '1A4219900', 19, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(30, '01725Z22D1Z001B', '1A4219900', 20, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(31, '01725Z22D1Z001B', '1A4219900', 21, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(32, '01725Z22D1Z001B', '1A4219900', 22, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(33, '01725Z22D1Z001B', '1A4219900', 23, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(34, '01725Z22D1Z001B', '1A4219900', 24, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(35, '01725Z22D1Z001B', '1A4219900', 25, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(36, '01725Z22D1Z001B', '1A4219900', 26, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(37, '01725Z22D1Z001B', '1A4219900', 27, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(38, '01725Z22D1Z001B', '1A4219900', 28, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(39, '01725Z22D1Z001B', '1A4219900', 29, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB'),
(40, '01725Z22D1Z001B', '1A4219900', 30, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBB');

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
(3, '2', 15, 23, 0, 0, 0, 0),
(4, '1', 7, 15, 540, 15, 705, 60),
(5, '3', 23, 7, 0, 0, 0, 0);

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
(13, 'PT. STI', 'customer', '2026-04-05 10:08:59', 'admin');

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

--
-- Dumping data for table `tbl_unit_material`
--

INSERT INTO `tbl_unit_material` (`id`, `unit_id`, `part_code`, `lot_no`, `used_qty`, `created_at`, `ref_number`) VALUES
(111, 11, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(112, 12, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(113, 13, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(114, 14, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(115, 15, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(116, 16, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(117, 17, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(118, 18, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(119, 19, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(120, 20, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(121, 21, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(122, 22, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(123, 23, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(124, 24, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(125, 25, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(126, 26, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(127, 27, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(128, 28, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(129, 29, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(130, 30, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(131, 31, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(132, 32, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(133, 33, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(134, 34, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(135, 35, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(136, 36, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(137, 37, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(138, 38, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(139, 39, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(140, 40, '128237800', '01725Z22D1Z1111', 4, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J111'),
(141, 11, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(142, 12, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(143, 13, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(144, 14, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(145, 15, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(146, 16, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(147, 17, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(148, 18, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(149, 19, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(150, 20, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(151, 21, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(152, 22, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(153, 23, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(154, 24, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(155, 25, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(156, 26, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(157, 27, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(158, 28, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(159, 29, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(160, 30, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(161, 31, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(162, 32, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(163, 33, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(164, 34, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(165, 35, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(166, 36, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(167, 37, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(168, 38, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(169, 39, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(170, 40, '159352600', '01725Z22D1Z222', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J222'),
(171, 11, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(172, 12, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(173, 13, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(174, 14, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(175, 15, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(176, 16, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(177, 17, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(178, 18, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(179, 19, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(180, 20, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(181, 21, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(182, 22, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(183, 23, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(184, 24, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(185, 25, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(186, 26, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(187, 27, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(188, 28, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(189, 29, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(190, 30, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(191, 31, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(192, 32, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(193, 33, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(194, 34, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(195, 35, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(196, 36, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(197, 37, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(198, 38, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(199, 39, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(200, 40, '169387600', '01725Z22D1Z001C', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1JBY'),
(201, 11, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(202, 12, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(203, 13, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(204, 14, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(205, 15, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(206, 16, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(207, 17, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(208, 18, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(209, 19, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(210, 20, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(211, 21, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(212, 22, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(213, 23, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(214, 24, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(215, 25, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(216, 26, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(217, 27, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(218, 28, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(219, 29, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(220, 30, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(221, 31, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(222, 32, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(223, 33, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(224, 34, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(225, 35, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(226, 36, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(227, 37, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(228, 38, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(229, 39, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(230, 40, '169390101', '01725Z22D1Z333', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J333'),
(231, 11, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(232, 12, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(233, 13, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(234, 14, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(235, 15, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(236, 16, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(237, 17, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(238, 18, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(239, 19, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(240, 20, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(241, 21, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(242, 22, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(243, 23, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(244, 24, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(245, 25, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(246, 26, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(247, 27, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(248, 28, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(249, 29, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(250, 30, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(251, 31, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(252, 32, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(253, 33, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(254, 34, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(255, 35, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(256, 36, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(257, 37, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(258, 38, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(259, 39, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(260, 40, '172503000', '01725Z22D1Z444', 3, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J444'),
(261, 11, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(262, 12, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(263, 13, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(264, 14, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(265, 15, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(266, 16, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(267, 17, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(268, 18, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(269, 19, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(270, 20, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(271, 21, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(272, 22, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(273, 23, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(274, 24, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(275, 25, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(276, 26, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(277, 27, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(278, 28, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(279, 29, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(280, 30, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(281, 31, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(282, 32, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(283, 33, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(284, 34, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(285, 35, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(286, 36, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(287, 37, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(288, 38, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(289, 39, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(290, 40, '212685700', '01725Z22D1Z555', 2, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J555'),
(291, 11, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(292, 12, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(293, 13, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(294, 14, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(295, 15, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(296, 16, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(297, 17, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(298, 18, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(299, 19, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(300, 20, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(301, 21, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(302, 22, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(303, 23, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(304, 24, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(305, 25, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(306, 26, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(307, 27, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(308, 28, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(309, 29, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(310, 30, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(311, 31, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(312, 32, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(313, 33, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(314, 34, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(315, 35, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(316, 36, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(317, 37, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(318, 38, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(319, 39, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(320, 40, '169455303', '01725Z22D1Z666', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J666'),
(321, 11, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(322, 12, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(323, 13, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(324, 14, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(325, 15, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(326, 16, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(327, 17, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(328, 18, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(329, 19, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(330, 20, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(331, 21, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(332, 22, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(333, 23, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(334, 24, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(335, 25, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(336, 26, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(337, 27, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(338, 28, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(339, 29, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(340, 30, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(341, 31, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(342, 32, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(343, 33, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(344, 34, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(345, 35, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(346, 36, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(347, 37, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(348, 38, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(349, 39, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(350, 40, '169387500', '01725Z22D1Z777', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J777'),
(351, 11, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(352, 12, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(353, 13, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(354, 14, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(355, 15, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(356, 16, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(357, 17, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(358, 18, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(359, 19, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(360, 20, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(361, 21, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(362, 22, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(363, 23, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(364, 24, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(365, 25, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(366, 26, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(367, 27, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(368, 28, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(369, 29, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(370, 30, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(371, 31, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(372, 32, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(373, 33, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(374, 34, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(375, 35, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(376, 36, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(377, 37, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(378, 38, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(379, 39, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(380, 40, '217711301', '083259241ID0001', 1, '2026-04-05 13:37:49', 'HEMEIN640WAJ0AYL'),
(381, 11, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(382, 12, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(383, 13, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(384, 14, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(385, 15, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(386, 16, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(387, 17, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(388, 18, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(389, 19, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(390, 20, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(391, 21, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(392, 22, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(393, 23, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(394, 24, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(395, 25, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(396, 26, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(397, 27, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(398, 28, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(399, 29, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(400, 30, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(401, 31, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(402, 32, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(403, 33, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(404, 34, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(405, 35, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(406, 36, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(407, 37, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(408, 38, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(409, 39, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(410, 40, '218496702', '140261011010001', 1, '2026-04-05 13:37:49', 'HEFHGGF55H281RNB'),
(411, 11, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(412, 12, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(413, 13, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(414, 14, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(415, 15, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(416, 16, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(417, 17, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(418, 18, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(419, 19, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(420, 20, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(421, 21, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(422, 22, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(423, 23, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(424, 24, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(425, 25, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(426, 26, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(427, 27, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(428, 28, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(429, 29, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(430, 30, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(431, 31, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(432, 32, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(433, 33, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(434, 34, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(435, 35, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(436, 36, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(437, 37, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(438, 38, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(439, 39, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888'),
(440, 40, '169387700', '01725Z22D1Z888', 1, '2026-04-05 13:37:49', 'HECIYS8OWYHJ1J888');

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
-- Indexes for table `tbl_part`
--
ALTER TABLE `tbl_part`
  ADD PRIMARY KEY (`id_part`),
  ADD UNIQUE KEY `part_code` (`part_code`),
  ADD UNIQUE KEY `idx_part_name_upper` ((upper(trim(`part_name`)))),
  ADD KEY `qty` (`qty`),
  ADD KEY `part_name` (`part_name`),
  ADD KEY `supplier` (`supplier`),
  ADD KEY `status_assy` (`status_assy`);

--
-- Indexes for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  ADD PRIMARY KEY (`id_pa`),
  ADD UNIQUE KEY `part_assy_2` (`part_assy`,`part_code`),
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
-- Indexes for table `tbl_product_unit`
--
ALTER TABLE `tbl_product_unit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_no_2` (`serial_no`,`unit_no`,`ref_number`) USING BTREE,
  ADD KEY `serial_no` (`serial_no`),
  ADD KEY `product_code` (`product_code`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_detail_product`
--
ALTER TABLE `tbl_detail_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_detail_production`
--
ALTER TABLE `tbl_detail_production`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tbl_detail_production_planning`
--
ALTER TABLE `tbl_detail_production_planning`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_model`
--
ALTER TABLE `tbl_model`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_ng_part`
--
ALTER TABLE `tbl_ng_part`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_ng_product`
--
ALTER TABLE `tbl_ng_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_ng_type`
--
ALTER TABLE `tbl_ng_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_part`
--
ALTER TABLE `tbl_part`
  MODIFY `id_part` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  MODIFY `id_pa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_production_output`
--
ALTER TABLE `tbl_production_output`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_production_planning`
--
ALTER TABLE `tbl_production_planning`
  MODIFY `pp_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_product_unit`
--
ALTER TABLE `tbl_product_unit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tbl_shift`
--
ALTER TABLE `tbl_shift`
  MODIFY `shift_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  MODIFY `id_supplier` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_unit_material`
--
ALTER TABLE `tbl_unit_material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=441;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
