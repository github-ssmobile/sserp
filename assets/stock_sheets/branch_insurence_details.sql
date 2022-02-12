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
-- Table structure for table `branch_insurence_details`
--

CREATE TABLE `branch_insurence_details` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `insurence_type` enum('1','2','3','4') DEFAULT NULL COMMENT '''1''=>office,''2''=>''vechile'',''3''=>''godown'',''4''=>''employee''',
  `insurence_start_date` date DEFAULT NULL,
  `insurence_end_date` date DEFAULT NULL,
  `policy_no` varchar(20) DEFAULT NULL,
  `furni_fixtures_amt` double DEFAULT NULL,
  `stock_amt` double DEFAULT NULL,
  `elec_equip_amt` double DEFAULT NULL,
  `section_3A_amt` double DEFAULT NULL,
  `section_3B_amt` double DEFAULT NULL,
  `section_3C_amt` double DEFAULT NULL,
  `plate_glass_amt` double DEFAULT NULL,
  `neon_glowsign_amt` double DEFAULT NULL,
  `baggage_amt` double DEFAULT NULL,
  `section_10A_amt` double DEFAULT NULL,
  `buis_interuption_amt` double DEFAULT NULL,
  `total_sum_insured` double DEFAULT NULL,
  `premium_amt` double DEFAULT NULL,
  `gst_amt` double DEFAULT NULL,
  `total_premium_amt` double DEFAULT NULL,
  `insurence_co_name` varchar(200) DEFAULT NULL,
  `hypothicated_with` varchar(200) DEFAULT NULL,
  `insurence_doc` varchar(200) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch_insurence_details`
--
ALTER TABLE `branch_insurence_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch_insurence_details`
--
ALTER TABLE `branch_insurence_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
