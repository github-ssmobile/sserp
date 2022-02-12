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
-- Table structure for table `branch_channel_partner_details`
--

CREATE TABLE `branch_channel_partner_details` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `owner_name` varchar(50) DEFAULT NULL,
  `owner_age` int(2) DEFAULT NULL,
  `owner_occupation` varchar(20) DEFAULT NULL,
  `owner_pan` varchar(8) DEFAULT NULL,
  `owner_adhar` varchar(12) DEFAULT NULL,
  `owner_email` varchar(50) DEFAULT NULL,
  `owner_address` varchar(200) DEFAULT NULL,
  `shop_address` varchar(200) DEFAULT NULL,
  `deposit_amt` double DEFAULT NULL,
  `owner_gst` varchar(20) DEFAULT NULL,
  `agreement_doc` varchar(100) DEFAULT NULL,
  `owner_bank_name` varchar(100) DEFAULT NULL,
  `owner_bank_accno` varchar(20) DEFAULT NULL,
  `owner_bank_ifsc` varchar(10) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(1) DEFAULT NULL,
  `created_by` varchar(10) DEFAULT NULL,
  `deposit_rec_amt` double DEFAULT NULL,
  `deposit_rec_date` date DEFAULT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `receive_status` varchar(1) NOT NULL,
  `pan_doc` varchar(200) DEFAULT NULL,
  `adhar_doc` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branch_channel_partner_details`
--

INSERT INTO `branch_channel_partner_details` (`id`, `branch_id`, `owner_name`, `owner_age`, `owner_occupation`, `owner_pan`, `owner_adhar`, `owner_email`, `owner_address`, `shop_address`, `deposit_amt`, `owner_gst`, `agreement_doc`, `owner_bank_name`, `owner_bank_accno`, `owner_bank_ifsc`, `created_date`, `status`, `created_by`, `deposit_rec_amt`, `deposit_rec_date`, `trans_id`, `remark`, `receive_status`, `pan_doc`, `adhar_doc`) VALUES
(7, 122, 'Kaustubh Rajendra Metha', 28, '9762779999', 'BQFPM433', '475925933379', 'kmetha14@gmail.com', 'Supriya Appartment flat no. 23, Ashok nagar Baramati 416102', NULL, 800000, '', 'uploads/branch_info/122/cpagreement_122.pdf', 'HDFC BANK', '50100295181960', 'HDFC000208', '2022-01-21 11:15:44', NULL, '120', 800000, '2022-01-19', 'MB19150955818T32118007/MB20120959802T38275379', '0000000158515302,0000000394012807', '1', 'uploads/branch_info/122/cp_pan_doc_122.jfif', 'uploads/branch_info/122/cp_adhar_doc_122.jfif'),
(8, 237, 'Yash Ramdas Kurade', 24, '7774851954', 'FMJPK244', '978169137081', 'yash7774851954@gmail.com', 'AAI KALBHAIRI GADHINGLAJ 416502', NULL, 700000, '', 'uploads/branch_info/237/cpagreement_237.pdf', 'Bank of Baroda', '35760100012565', 'BARB0GADHI', '2022-01-21 11:12:54', NULL, '120', 700000, '2022-01-18', 'FDRLR520220118006', 'FDRLR52022011800663001', '1', 'uploads/branch_info/237/cp_pan_doc_237.jfif', 'uploads/branch_info/237/cp_adhar_doc_237.jfif'),
(9, 238, 'SAGAR DILIP GAIKWAD', 33, '9881436648', 'AOIPG626', '764476132617', 'moraya.kwd@gmail.com', 'Shivpratithan nagar ,near raily hospital at post kurudwadi tal-madha dist solapur .\r\n', NULL, 600000, '27AOIPG6261R1ZA', 'uploads/branch_info/238/cpagreement_748251213.pdf', 'BANK OF MAHARASHTRA ', '60218932928', 'MAHB000195', '2022-01-29 05:45:54', NULL, '774', 600000, '2022-01-14', 'MAHBH22014149481', 'MAHBH22014149481 ', '1', 'uploads/branch_info/238/cp_pan_doc_238.jpeg', 'uploads/branch_info/238/cp_adhar_doc_238.jpeg'),
(10, 230, 'Mr. TANAJI WAMAN SINNARKAR', 37, '9850374747', 'BAPPS351', '951829789181', 'Vaishnavi_idea@yahoo.com', 'Saykheda Road, Sinnarkar Vasti, Ozar Mig, Ojhar Township, Nashik-422007', NULL, 600000, '', NULL, 'STATE BANK OF INDIA', '30235366341', 'SBIN000119', '2022-01-28 13:13:10', NULL, '125', 600000, '2022-01-13', 'MAHGR52022011300000484', 'MAHGR52022011300000484', '1', 'uploads/branch_info/230/cp_pan_doc_1270158577.pdf', 'uploads/branch_info/230/cp_adhar_doc_604035411.pdf'),
(11, 231, 'Mr. VISHAL VASANT NIKAM', 27, '9404804835', 'ARBPN824', '986424591319', 'vnikam94@gmail.com', 'Indraprastha Colony, Satana, Baglan, Nashik, Satana-423301', NULL, 600000, '', NULL, 'STATE BANK OF INDIA', '11372629411', 'SBIN000047', '2022-01-28 13:12:41', NULL, '125', 600000, '2022-01-27', 'SBIN222024468001/  SBINR52022012763793505', 'as per satana- nasik cp depoSBINR52022012763793', '1', 'uploads/branch_info/231/cp_pan_doc_1870069563.pdf', 'uploads/branch_info/231/cp_adhar_doc_1703929891.pdf'),
(14, 242, 'Mr. AKSHAY ASHOK OSTWAL', 31, '9370066200', 'ABIPO166', '696718709195', 'ostwal.akshay@gmail.com', 'S No. 1571/23, Old Agra Road, Near Upkar Theatre, Malegaon, Dist Nashik-423203', NULL, 800000, '', NULL, 'UNION BANK OF INDIA', '38782010017703', 'UBIN053878', '2022-01-28 13:13:22', NULL, '125', 800000, '2022-01-19', 'UBINH22019004128/ 000270351940', '000000027035, ubinr220220119010041281940', '1', 'uploads/branch_info/242/cp_pan_doc_1672984293.pdf', 'uploads/branch_info/242/cp_adhar_doc_206109569.pdf'),
(15, 147, 'SHYAM BHAHUSAHEB MASKE', 28, '9765501125', 'CWZPM860', '744437612528', 'shyammaske123@gmail.com', 'AT SAROLA LATUR 413531', NULL, 700000, '', 'uploads/branch_info/147/cpagreement_557151824.pdf', 'HDFC BANK', '50100199720410', 'HDFC000036', '2022-02-04 13:22:25', NULL, '122', 700000, '2022-02-02', 'CH NO 000017', 'DEPOSIT RECEIVED', '1', 'uploads/branch_info/147/cp_pan_doc_974656475.jpeg', 'uploads/branch_info/147/cp_adhar_doc_1399040149.jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch_channel_partner_details`
--
ALTER TABLE `branch_channel_partner_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch_channel_partner_details`
--
ALTER TABLE `branch_channel_partner_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
