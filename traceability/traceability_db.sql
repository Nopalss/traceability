-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 30, 2026 at 06:09 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ng_type_detail`
--

CREATE TABLE `tbl_ng_type_detail` (
  `id` int NOT NULL,
  `part_id` int NOT NULL,
  `type_id` int NOT NULL
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_detail_product`
--
ALTER TABLE `tbl_detail_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_detail_production`
--
ALTER TABLE `tbl_detail_production`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_detail_production_planning`
--
ALTER TABLE `tbl_detail_production_planning`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_karyawan`
--
ALTER TABLE `tbl_karyawan`
  MODIFY `karyawan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_line`
--
ALTER TABLE `tbl_line`
  MODIFY `line_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_material_loss`
--
ALTER TABLE `tbl_material_loss`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_menu`
--
ALTER TABLE `tbl_menu`
  MODIFY `menu_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_model`
--
ALTER TABLE `tbl_model`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT for table `tbl_ng_type`
--
ALTER TABLE `tbl_ng_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_ng_type_detail`
--
ALTER TABLE `tbl_ng_type_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_production_output`
--
ALTER TABLE `tbl_production_output`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_production_planning`
--
ALTER TABLE `tbl_production_planning`
  MODIFY `pp_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_product_unit`
--
ALTER TABLE `tbl_product_unit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
