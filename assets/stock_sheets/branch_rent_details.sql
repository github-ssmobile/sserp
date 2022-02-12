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
-- Table structure for table `branch_rent_details`
--

CREATE TABLE `branch_rent_details` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `owner_name` varchar(50) DEFAULT NULL,
  `owner_age` int(2) DEFAULT NULL,
  `owner_occupation` varchar(20) DEFAULT NULL,
  `owner_pan` varchar(12) DEFAULT NULL,
  `owner_adhar` varchar(15) DEFAULT NULL,
  `owner_email` varchar(50) DEFAULT NULL,
  `owner_contact` varchar(100) DEFAULT NULL,
  `owner_address` varchar(200) DEFAULT NULL,
  `shop_address` varchar(200) DEFAULT NULL,
  `shop_measurement` varchar(5) DEFAULT NULL,
  `deposit_amt` int(11) DEFAULT NULL,
  `rent_tenure` int(11) DEFAULT NULL,
  `rent_free_period` int(11) DEFAULT NULL,
  `rent_free_start_date` date DEFAULT NULL,
  `rent_free_end_date` date DEFAULT NULL,
  `rent_start_date` date DEFAULT NULL,
  `rent_end_date` date DEFAULT NULL,
  `lock_in_period` int(11) DEFAULT NULL,
  `termination_notice_period` int(11) DEFAULT NULL,
  `rent_amount` double DEFAULT NULL,
  `rent_incr_ratio` varchar(100) DEFAULT NULL,
  `owner_gst` varchar(20) DEFAULT NULL,
  `rent_doc` varchar(100) DEFAULT NULL,
  `owner_bank_name` varchar(100) DEFAULT NULL,
  `owner_bank_accno` varchar(20) DEFAULT NULL,
  `owner_bank_ifsc` varchar(15) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(1) DEFAULT NULL,
  `created_by` varchar(10) DEFAULT NULL,
  `deposit_paid_amt` double DEFAULT NULL,
  `deposit_paid_date` date DEFAULT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `pan_doc` varchar(100) DEFAULT NULL,
  `adhar_doc` varchar(100) DEFAULT NULL,
  `property_doc` varchar(100) DEFAULT NULL,
  `electricity_doc` varchar(100) DEFAULT NULL,
  `deposit_status` varchar(1) NOT NULL,
  `aminiti_doc` varchar(250) NOT NULL,
  `legal_approve` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branch_rent_details`
--

INSERT INTO `branch_rent_details` (`id`, `branch_id`, `owner_name`, `owner_age`, `owner_occupation`, `owner_pan`, `owner_adhar`, `owner_email`, `owner_contact`, `owner_address`, `shop_address`, `shop_measurement`, `deposit_amt`, `rent_tenure`, `rent_free_period`, `rent_free_start_date`, `rent_free_end_date`, `rent_start_date`, `rent_end_date`, `lock_in_period`, `termination_notice_period`, `rent_amount`, `rent_incr_ratio`, `owner_gst`, `rent_doc`, `owner_bank_name`, `owner_bank_accno`, `owner_bank_ifsc`, `created_date`, `status`, `created_by`, `deposit_paid_amt`, `deposit_paid_date`, `trans_id`, `remark`, `pan_doc`, `adhar_doc`, `property_doc`, `electricity_doc`, `deposit_status`, `aminiti_doc`, `legal_approve`) VALUES
(12, 122, 'Ganesh Balasaheb Shinde', 40, 'Farmer', 'AUCPS481', '945210566911', 'GANESHSHINDE@GMAIL.COM', '2147483647', 'Gavthan, Kanheri, Tal- Baramati', 'Shop No. 3/4 Pravin Plaza, Cinema Road, Baramati', '475', 1000000, 5, 0, '0000-00-00', '0000-00-00', '0000-00-00', '2027-01-02', 36, 3, 25000, '', '', 'uploads/branch_info/122/rentdoc_1221.pdf', 'Union Bank Of India', '417502010004815', 'UVIN054175', '2022-02-01 13:06:34', NULL, '32', 1000000, '2022-01-06', '0', 'NEW SHOP DEPOSIT PAID', 'uploads/branch_info/122/rentpandoc_1616030736.jpeg', 'uploads/branch_info/122/renadhartdoc_778694881.jpeg', 'uploads/branch_info/122/rentpropdoc_1812845821.jpg', 'uploads/branch_info/122/renteledoc_910740383.pdf', '1', '', '1'),
(13, 229, 'RAHUL SHAM DEDGAONKAR', 47, 'BUSINESS', 'AAJPD506', '713528983304', 'rahul.dedgaonkar@gmail.com', '9850515253', 'At-13,Laxmi Nandanwan colony ,Burudgaon Road Ahmednagar - 414001', 'Shop No 3,Sai corner Building,Ground floor ,Chitale Road Ahmednagar-414001', '310 s', 100000, 5, 30, '2022-01-01', '2022-03-01', '2022-01-02', '2027-01-02', 3, 2, 20000, '', '', 'uploads/branch_info/229/rentdoc_2291.pdf', 'HDFC BANK', '50100373150943', 'HDFC000018', '2022-02-01 13:23:47', NULL, '32', 100000, '2022-01-14', '170968172', 'NEW SHOP DEPOSIT PAID', 'uploads/branch_info/229/rentpandoc_1264100802.jpeg', 'uploads/branch_info/229/renadhartdoc_1947735514.jpeg', 'uploads/branch_info/229/rentpropdoc_1763740217.jpeg', 'uploads/branch_info/229/renteledoc_1901181804.docx', '1', '', '1'),
(15, 235, 'Suchita Mahesh Nikam', 32, 'House wife', 'BRDPN691', '350556348117', 'maheshjnikam81@gmail.com', '2147483647', 'A/p- Saygoan,  Tal- Jawali, Dist- Satara\r\n', 'Shop no- 5, Lucky Plaza, Z P Chowk, Satara 415001\r\n', '315  ', 100000, 5, 0, '2022-01-19', '2022-01-19', '2022-02-01', '2027-02-01', 3, 3, 20000, '', '', 'uploads/branch_info/235/rentdoc_1072550152.docx', 'Union Bank Of India', '579302010004842', 'UBIN057128', '2022-02-02 04:47:35', NULL, '2547', 100000, '2022-12-01', '0', 'SHOP DEPOSIT PAID', 'uploads/branch_info/235/rentpandoc_33193355.pdf', 'uploads/branch_info/235/renadhartdoc_357282748.pdf', 'uploads/branch_info/235/rentpropdoc_1679733835.pdf', 'uploads/branch_info/235/renteledoc_1416863865.pdf', '1', '', '1'),
(16, 237, 'RAMDAS SHIVAJI KURADE', 50, 'BUISENESS', 'BIMPK497', '910035419491', 'yash7774851954@gmail.com', '2147483647', '2045 Aai, Kalbhairi Gadhinglaj 416502', 'GALA NO.1109/1,MAIN ROAD,OPP.MODERN BAKERY, GADHINGLAJ - 416502', '828 ', 500000, 5, 1, '2022-01-31', '2022-01-31', '2022-02-01', '2027-02-01', 2, 2, 35000, '', '', 'uploads/branch_info/237/rentdoc_237.pdf', 'Bank of Baroda', '35760500000458', 'BARB0GADHI', '2022-02-02 04:48:16', NULL, '2547', 500000, '2022-01-21', 'N021221801842290', 'SHOP DEPOSIT PAID', 'uploads/branch_info/237/rentpandoc_1470353685.jpeg', 'uploads/branch_info/237/renadhartdoc_1355656007.jpeg', 'uploads/branch_info/237/rentpropdoc_1053829630.jpeg', 'uploads/branch_info/237/renteledoc_1278806147.jpeg', '1', '', '1'),
(17, 238, 'JAYSINGH NARAYAN PATIL', 80, 'RETRIED ', 'BVNPP930', '563446854370', 'shubham.patil@gmail.com', '9890145517', 'UJANI, HOLE KHURD MAUJE UJANI, TAL- MADHA DIST SOLAPUR 413210.', '27 NAVI PETH KURUDWADI , TAL  MADHA , DIST SOLAPUR 413208', '275', 200000, 5, 28, '2022-02-01', '2022-02-28', '2022-03-01', '2027-03-01', 60, 2, 17000, '', '', 'uploads/branch_info/238/rentdoc_238.pdf', 'BANK OF INDIA', '073110110001454', 'BKID000073', '2022-02-02 07:58:54', NULL, '2547', 200000, '2022-01-29', 'N029221810934605', 'DEPOSIT PAY', 'uploads/branch_info/238/rentpandoc_1632104755.jpeg', 'uploads/branch_info/238/renadhartdoc_608017249.jpeg', 'uploads/branch_info/238/rentpropdoc_1197149911.jpeg', 'uploads/branch_info/238/renteledoc_894621303.jpeg', '1', '', '1'),
(22, 231, 'Mrs. SUNANDA VASANT NIKAM', 49, 'House Wife', 'CQAPN8761K', '4229 9356 4492', 'vnikam94@gmail.com', '9404804835', 'Jalaram Enterprises, TDA Road, Satana, Nashik - 423301', '2/414, Taharabad Road, Tal. Satana, Dist. Nashik - 423301', '400', 0, 5, 60, '0000-00-00', '0000-00-00', '2022-03-01', '2027-01-03', 0, 60, 25000, '', '', 'uploads/branch_info/231/rentdoc_231.pdf', 'HDFC BANK LTD', '50100479778428', 'HDFC0002142', '2022-02-04 13:19:27', NULL, '2547', 0, '0000-00-00', '0', 'DEPOSIT- 0', 'uploads/branch_info/231/rentpandoc_1297843286.jpeg', 'uploads/branch_info/231/renadhartdoc_2104788272.jpeg', 'uploads/branch_info/231/rentpropdoc_1981513029.pdf', 'uploads/branch_info/231/renteledoc_737370652.pdf', '1', '', '1'),
(23, 230, 'Mr. GOVIND WAMANRAO SINNARKAR', 51, 'Business', 'APBPS1355N', '398205574070', 'Vaishnavi_idea@yahoo.com', '9850689191', 'Waman Niwas, Opp. Maharashtra Gramin Bank, Saikheda Phata, Ozar-Mig, Nashik - 422207', 'Gat No. 1986/2/C, Saikheda Phata, Ozar (Mig), Tal. Niphad. Dist. Nashik-422007', '325', 0, 5, 60, '0000-00-00', '0000-00-00', '2022-03-01', '2027-01-03', 0, 60, 20000, '', '', 'uploads/branch_info/230/rentdoc_230.pdf', 'IDBI BANK LTD', '1913104000040488', 'IBKL0001913', '2022-02-04 13:19:09', NULL, '2547', 0, '0000-00-00', '0', 'DEPOSIT- 0', 'uploads/branch_info/230/rentpandoc_1146535404.pdf', 'uploads/branch_info/230/renadhartdoc_390732474.pdf', 'uploads/branch_info/230/rentpropdoc_1429509215.pdf', 'uploads/branch_info/230/renteledoc_363427379.pdf', '1', '', '1'),
(24, 230, 'Smt. VIMAL WAMANRAO SINNARKAR', 70, 'House Wife', 'OSZPS1664F', '743897207917', 'Vaishnavi_idea@yahoo.com', '9850374747', 'Waman Niwas, Opp. Maharashtra Gramin Bank, Saikheda Phata, Ozar-Mig, Nashik - 422207', 'Gat No. 1986/2/C, Saikheda Phata, Ozar (Mig), Tal. Niphad. Dist. Nashik-422007', '325', 0, 5, 60, '0000-00-00', '0000-00-00', '2022-03-01', '2027-01-03', 0, 60, 20000, '', '', 'uploads/branch_info/230/rentdoc_230.pdf', 'IDBI BANK LTD', '1913104000040488', 'IBKL0001913', '2022-02-04 13:19:09', NULL, '32', 0, '0000-00-00', '0', 'DEPOSIT- 0', 'uploads/branch_info/243/rentpandoc_1874377214.pdf', 'uploads/branch_info/243/renadhartdoc_1196338812.pdf', 'uploads/branch_info/243/rentpropdoc_2091770977.pdf', 'uploads/branch_info/243/renteledoc_1616568297.pdf', '1', '', NULL),
(25, 230, 'Mr. TANAJI WAMAN SINNARKAR', 37, 'Business', 'BAPPS3516K', '951829789181', 'Vaishnavi_idea@yahoo.com', '9850374747', 'Waman Niwas, Opp. Maharashtra Gramin Bank, Saikheda Phata, Ozar-Mig, Nashik - 422207', 'Gat No. 1986/2/C, Saikheda Phata, Ozar (Mig), Tal. Niphad. Dist. Nashik-422007', '325', 0, 5, 60, '0000-00-00', '0000-00-00', '2022-03-01', '2027-01-03', 0, 60, 20000, '', '', 'uploads/branch_info/230/rentdoc_230.pdf', 'IDBI BANK LTD', '1913104000040488', 'IBKL0001913', '2022-02-04 13:19:09', NULL, '2547', 0, '0000-00-00', '0', 'DEPOSIT- 0', 'uploads/branch_info/230/rentpandoc_1664977587.pdf', 'uploads/branch_info/230/renadhartdoc_1473886773.pdf', 'uploads/branch_info/230/rentpropdoc_266540531.pdf', 'uploads/branch_info/230/renteledoc_827767805.pdf', '1', '', '1'),
(26, 242, 'Mr. AKSHAY ASHOK OSTWAL', 31, 'Business', 'ABIPO1667J', '696718709195', 'ostwal.akshay@gmail.com', '9370066200', 'Shop No 13, Panjarapol Shopping Center, Near Chatrapati Shivaji Maharaj Putala, Malegaon, Nashik - 423203', 'Shop No. 13, Panjarapol Shopping Center, Near Shivaji Maharaj Putala, Malegaon-423203.', '441', 1, 5, 60, '0000-00-00', '0000-00-00', '2022-03-01', '2027-01-03', 0, 60, 40000, '', '', 'uploads/branch_info/242/rentdoc_342060236.pdf', 'UNION BANK OF INDIA', '387802010017703', 'UBIN0538787', '2022-02-04 13:19:56', NULL, '2547', 0, '0000-00-00', '0', 'DEPOSIT - 0', 'uploads/branch_info/242/rentpandoc_1566437657.pdf', 'uploads/branch_info/242/renadhartdoc_911329917.pdf', 'uploads/branch_info/242/rentpropdoc_226245146.pdf', 'uploads/branch_info/242/renteledoc_408491423.pdf', '1', '', '1'),
(27, 243, 'ROHIT PATIL', 29, 'FARMER', 'jfcmof', '728549131944', 'rohit.patil2@ssmoile.com', '7030065757', 'Pachgaon', 'Pachgaon', '500', 100000, 2, 31, '2022-01-01', '2022-01-31', '2022-02-01', '2024-02-01', 24, 24, 20000, '', '', 'uploads/branch_info/243/rentdoc_1269786330.pdf', 'HDFC BANK', '50100250148256', 'HDFC0000164', '2022-02-01 10:07:16', NULL, '2547', NULL, NULL, NULL, NULL, 'uploads/branch_info/243/rentpandoc_1874377214.pdf', 'uploads/branch_info/243/renadhartdoc_1196338812.pdf', 'uploads/branch_info/243/rentpropdoc_2091770977.pdf', 'uploads/branch_info/243/renteledoc_1616568297.pdf', '', '', '1'),
(29, 243, 'sd', 20, 'sdf', 'sdf', 'sdf', 'sdf@gmail.com', '9665409053', 'sdf', 'Pachgaon', '500', 100000, 2, 31, '2022-01-01', '2022-01-31', '2022-02-01', '2024-02-01', 24, 24, 20000, '', '', NULL, 'HDFC BANK', '50100250148256', 'HDFC0000164', '2022-02-02 05:11:58', NULL, '2547', NULL, NULL, NULL, NULL, 'uploads/branch_info/243/rentpandoc_185504921.pdf', NULL, NULL, NULL, '', '', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch_rent_details`
--
ALTER TABLE `branch_rent_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch_rent_details`
--
ALTER TABLE `branch_rent_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
