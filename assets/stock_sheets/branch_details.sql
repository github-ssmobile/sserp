-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2022 at 10:43 AM
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
-- Table structure for table `branch_details`
--

CREATE TABLE `branch_details` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `mbb_provider` varchar(100) DEFAULT NULL,
  `mbb_accno` int(11) DEFAULT NULL,
  `mbb_acname` varchar(100) DEFAULT NULL,
  `mbb_accadd` varchar(200) DEFAULT NULL,
  `mbb_telno` int(11) DEFAULT NULL,
  `mbb_gstno` varchar(20) DEFAULT NULL,
  `mbb_plandetails` varchar(200) DEFAULT NULL,
  `mbb_planamount` double DEFAULT NULL,
  `ele_provider` varchar(100) DEFAULT NULL,
  `ele_custno` varchar(100) DEFAULT NULL,
  `ele_custadd` varchar(200) DEFAULT NULL,
  `ele_billingunit` varchar(50) DEFAULT NULL,
  `ele_telno` int(11) DEFAULT NULL,
  `ele_meterno` varchar(100) DEFAULT NULL,
  `ele_gstno` varchar(20) DEFAULT NULL,
  `ele_last_billing_unit` int(11) DEFAULT NULL,
  `ele_las_billing_month` date DEFAULT NULL,
  `ins_provider` varchar(100) DEFAULT NULL,
  `ins_provadd` varchar(200) DEFAULT NULL,
  `ins_provtelno` int(11) DEFAULT NULL,
  `ins_provemail` varchar(50) DEFAULT NULL,
  `ins_provgstno` varchar(20) DEFAULT NULL,
  `ins_doc` varchar(150) DEFAULT NULL,
  `ren_ownname` varchar(100) DEFAULT NULL,
  `ren_ownadd` varchar(200) DEFAULT NULL,
  `ren_owntelno` int(11) DEFAULT NULL,
  `ren_ownicardno` varchar(20) DEFAULT NULL,
  `ren_agrstartdate` date DEFAULT NULL,
  `ren_agrenddate` date DEFAULT NULL,
  `ren_ownbank` varchar(50) DEFAULT NULL,
  `ren_ownaccno` int(11) DEFAULT NULL,
  `ren_ownbankifsc` varchar(20) DEFAULT NULL,
  `ren_doc` varchar(100) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `mbb_permonthamt` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branch_details`
--

INSERT INTO `branch_details` (`id`, `branch_id`, `mbb_provider`, `mbb_accno`, `mbb_acname`, `mbb_accadd`, `mbb_telno`, `mbb_gstno`, `mbb_plandetails`, `mbb_planamount`, `ele_provider`, `ele_custno`, `ele_custadd`, `ele_billingunit`, `ele_telno`, `ele_meterno`, `ele_gstno`, `ele_last_billing_unit`, `ele_las_billing_month`, `ins_provider`, `ins_provadd`, `ins_provtelno`, `ins_provemail`, `ins_provgstno`, `ins_doc`, `ren_ownname`, `ren_ownadd`, `ren_owntelno`, `ren_ownicardno`, `ren_agrstartdate`, `ren_agrenddate`, `ren_ownbank`, `ren_ownaccno`, `ren_ownbankifsc`, `ren_doc`, `created_date`, `created_by`, `mbb_permonthamt`) VALUES
(7, 122, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'HARI OM MULTVISION CO PVT LTD', '2147483647', NULL, '5851', NULL, '2147483647', NULL, 949, '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-12 07:03:12', NULL, NULL),
(8, 229, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RAHUL SHYAM DEDGAONKAR', '2147483647', NULL, '4200', NULL, '2147483647', NULL, 410, '2021-09-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-13 10:50:14', NULL, NULL),
(9, 235, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Shri Darshansing Indarsing Bindra', '2147483647', NULL, '0914', NULL, '2147483647', NULL, 7353, '2021-11-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-20 05:22:08', NULL, NULL),
(10, 237, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Shri Noormahamad Kadar Khalif', '2147483647', NULL, '1121', NULL, '2147483647', NULL, 67032, '2021-11-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-20 10:25:46', NULL, NULL),
(11, 238, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MSEB', '2147483647', NULL, '1368', NULL, '2147483647', NULL, 10203, '2022-01-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-24 13:44:28', NULL, NULL),
(12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MSEB', '2147483647', NULL, '1368', NULL, '2147483647', NULL, 10203, '2022-01-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-24 13:45:10', NULL, NULL),
(13, 231, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Maharashtra State Electricity Distribution Company Limited', '054010079601', NULL, '5479', NULL, '05376025398', NULL, 49, '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-28 06:58:19', NULL, NULL),
(14, 230, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Maharashtra State Electricity Distribution Company Limited', '073020089811', NULL, '0469', NULL, '06110296442', NULL, 99, '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-27 12:57:58', NULL, NULL),
(15, 242, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Maharashtra State Electricity Distribution Company Limited', '065510418471', NULL, '0426', NULL, '05516056308', NULL, 148, '0000-00-00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-01-28 10:52:46', NULL, NULL),
(16, 243, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'MSEB', '3256987', NULL, '100', NULL, '58745896', NULL, 80, '2022-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-02-01 10:02:57', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch_details`
--
ALTER TABLE `branch_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch_details`
--
ALTER TABLE `branch_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
