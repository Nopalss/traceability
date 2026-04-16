-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 15, 2026 at 07:11 AM
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
-- Table structure for table `tbl_model`
--

CREATE TABLE `tbl_model` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `part_code` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `created_by` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `rule` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `username`, `password`, `rule`) VALUES
(1, 'admin', '$2y$10$wwX59l.50xDpWJAYPhgzmOAw1HFMjK7.q5AY7YgIkyNrh.oeCy6XC', 'admin'),
(5, 'operator', '$2y$10$wwX59l.50xDpWJAYPhgzmOAw1HFMjK7.q5AY7YgIkyNrh.oeCy6XC', 'operator'),
(6, 'K01', '$2y$10$LTHmyWVVqIQoLrkNHqpVVuNVEsDEmuMLqT8W9wIliLyKZcMJ5PRwq', 'line'),
(7, 'CL01', '$2y$10$N4wfBh/bRA9KHn3rM5XguOzYj8CdXjl9yR3yf/ftiaXVTs9vN.zny', 'line'),
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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `karyawan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `tbl_model`
--
ALTER TABLE `tbl_model`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `id_part` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_part_assy`
--
ALTER TABLE `tbl_part_assy`
  MODIFY `id_pa` int NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `tbl_shift`
--
ALTER TABLE `tbl_shift`
  MODIFY `shift_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  MODIFY `id_supplier` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_unit_material`
--
ALTER TABLE `tbl_unit_material`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
