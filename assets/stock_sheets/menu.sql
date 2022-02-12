-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2021 at 06:22 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `menu` varchar(200) DEFAULT NULL,
  `font` varchar(45) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `menu`, `font`, `url`, `level`, `active`) VALUES
(1, 'Menus', 'mdi mdi-menu', '', NULL, 1),
(2, 'Catalogue', 'mdi mdi-tag', '', NULL, 1),
(3, 'Users', 'mdi mdi-account-edit', 'Master/user_details', NULL, 1),
(4, 'Roles', 'mdi mdi-wrench', 'Master/role_details', NULL, 1),
(5, 'Pricing', 'mdi mdi-currency-inr', '', NULL, 1),
(6, 'Master', 'mdi mdi-settings-box', '', NULL, 1),
(7, 'Purchase', 'mdi mdi-cart', 'Purchase', NULL, 1),
(8, 'Allocation', 'mdi mdi-clipboard-flow', '', NULL, 1),
(9, 'Purchase Return', 'mdi mdi-keyboard-return', 'Purchase_return', NULL, 1),
(10, 'Stock', 'mdi mdi-trello', '', NULL, 1),
(11, 'Sale', 'mdi mdi-margin', 'Sale', NULL, 1),
(12, 'Outward', 'mdi mdi-barcode-scan', 'Outward/ready_to_outward', NULL, 1),
(13, 'Sales Return', 'mdi mdi-repeat', 'Sales_return', NULL, 1),
(14, 'Report', 'mdi mdi-clipboard-text', '', NULL, 1),
(15, 'Stock Shipments', 'mdi mdi-bus-side', '', NULL, 1),
(16, 'Placement Norms', 'mdi mdi-cellphone-iphone', '', NULL, 1),
(17, 'Stock Transfer', 'mdi mdi-hexagon-multiple', '', NULL, 1),
(18, 'Stock Request', 'mdi mdi-human-greeting', '', NULL, 1),
(19, 'Cash Payment', 'mdi mdi-cash-multiple', '', NULL, 1),
(20, 'Payment Reconciliation', 'mdi mdi-cash-multiple', 'Reconciliation/payment_reconciliation', NULL, 1),
(21, 'Audit', 'mdi mdi-barcode-scan', '', NULL, 1),
(22, 'Branch Wallet', 'mdi mdi-bank', '', NULL, 1),
(23, 'Expense', 'mdi mdi-forum', '', NULL, 1),
(25, 'Bank Reconciliation', 'mdi mdi-bank', '', NULL, 1),
(26, 'Cash Reports', 'mdi mdi-history', '', NULL, 1),
(27, 'Opening Stock', 'mdi mdi-checkbox-multiple-blank-circle', '', NULL, 1),
(28, 'Corrections', 'mdi mdi-table-edit', '', NULL, 1),
(29, 'Apple DMS Report', 'mdi mdi-disqus', 'Master/apple_dms_report', NULL, 0),
(30, 'Old ERP', 'mdi mdi-table-edit', '', NULL, 1),
(31, 'Service', 'mdi mdi-cellphone-iphone', '', NULL, 1),
(33, 'Help Line', 'mdi mdi-help-circle', '', NULL, 1),
(34, 'GST Report', 'mdi mdi-hexagon-multiple', '', NULL, 1),
(35, 'Tally Report', 'mdi mdi-forum', '', NULL, 1),
(36, 'Ageing', 'mdi mdi-hexagon-multiple', '', NULL, 1),
(37, 'Daily Stock Report', 'mdi mdi-cloud-download', 'Stock/stock_value_report', NULL, 1),
(38, 'Target Setup', 'mdi mdi-checkbox-multiple-blank-circle', 'Target/branch_target_setup', NULL, 1),
(39, 'Focus Model', 'mdi mdi-forum', 'Stock/focus_model', NULL, 1),
(40, 'Achivment Report', 'mdi mdi-checkbox-multiple-marked', 'Target/mtd_acheivement_report', NULL, 1),
(41, 'Finance Scheme', 'mdi mdi-repeat', 'Finance_scheme/finance_scheme', NULL, 1),
(42, 'Target Slab', 'mdi mdi-barcode-scan', 'Target/target_slabs_setup', NULL, 1),
(43, 'Stock Shipment', 'mdi mdi-forum', 'Report/warehouse_branch_shipment_report', NULL, 1),
(44, 'Insurance Reconciliation', 'mdi mdi-checkerboard', '', NULL, 1),
(45, 'Costing', 'mdi mdi-settings-box', 'Costing/branch_cost_header', NULL, 1),
(46, 'DOA', 'mdi mdi-keyboard-caps', '', NULL, 1),
(47, 'Configurations', 'mdi mdi-settings-box', '', NULL, 1),
(48, 'Apple Reports', 'mdi mdi-menu', 'Master/apple_webgdv_report', NULL, 1),
(49, 'Incentive Policy', 'mdi mdi-menu', 'Incentive_policy/policy_setup_report', NULL, 1),
(50, 'Ingram Micro', 'mdi mdi-cart', '', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
