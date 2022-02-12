-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2022 at 07:31 AM
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
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `id_branch` int(11) NOT NULL,
  `is_warehouse` int(11) NOT NULL,
  `idwarehouse` int(11) NOT NULL,
  `branch_name` varchar(50) NOT NULL,
  `branch_code` varchar(10) NOT NULL,
  `branch_gstno` varchar(200) DEFAULT NULL,
  `branch_address` varchar(200) NOT NULL,
  `branch_pincode` int(11) NOT NULL,
  `branch_state_name` varchar(100) NOT NULL,
  `branch_district` varchar(100) DEFAULT NULL,
  `branch_city` varchar(100) NOT NULL,
  `idstate` int(11) DEFAULT NULL,
  `branch_email` varchar(100) DEFAULT NULL,
  `branch_contact_person` varchar(100) NOT NULL,
  `branch_contact` varchar(20) NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `idcompany` int(11) NOT NULL,
  `idzone` int(11) NOT NULL,
  `idroute` int(11) NOT NULL,
  `idprinthead` int(11) DEFAULT NULL,
  `idbranchcategory` int(11) NOT NULL,
  `active` int(11) DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `po_approval` int(11) NOT NULL DEFAULT '0',
  `branch_timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `branch_lmb` int(11) DEFAULT NULL,
  `branch_lmt` datetime DEFAULT CURRENT_TIMESTAMP,
  `invoice_no` int(11) NOT NULL DEFAULT '0',
  `sales_return_invoice_no` int(11) DEFAULT '0',
  `inter_state_sale` int(11) NOT NULL,
  `purchase_invoice` int(11) NOT NULL,
  `bfl_store_id` varchar(200) DEFAULT '54429',
  `apple_store_id` int(11) DEFAULT '9',
  `p_direct_billing` int(11) NOT NULL DEFAULT '1',
  `token_billing` int(11) NOT NULL DEFAULT '1',
  `online_billing` int(11) NOT NULL DEFAULT '1',
  `web_billing` int(11) NOT NULL DEFAULT '1',
  `idpartner_type` int(20) DEFAULT NULL,
  `branch_dc_no` int(11) NOT NULL DEFAULT '0',
  `petti_cash_balance` double DEFAULT '0',
  `expense_allowed` int(11) NOT NULL DEFAULT '1' COMMENT '1=allowed,0=not allowed',
  `allow_purchase_direct_inward` int(11) DEFAULT '0',
  `acc_branch_id` int(20) DEFAULT NULL,
  `hrms_branch_id` int(20) DEFAULT NULL,
  `is_billing` int(11) DEFAULT NULL COMMENT '0=Disable,1=WebOnly,2=AppOnly(SaleToken),3=AppOnly(Direct)4=AppOnlyBoth,5=WebAppBoth,6=WebApp(SaleToken)7=WebApp(Direct)',
  `credit_limit` double DEFAULT '0',
  `credit_days` int(20) DEFAULT '0',
  `idservice_executive` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`id_branch`, `is_warehouse`, `idwarehouse`, `branch_name`, `branch_code`, `branch_gstno`, `branch_address`, `branch_pincode`, `branch_state_name`, `branch_district`, `branch_city`, `idstate`, `branch_email`, `branch_contact_person`, `branch_contact`, `latitude`, `longitude`, `idcompany`, `idzone`, `idroute`, `idprinthead`, `idbranchcategory`, `active`, `created_by`, `po_approval`, `branch_timestamp`, `branch_lmb`, `branch_lmt`, `invoice_no`, `sales_return_invoice_no`, `inter_state_sale`, `purchase_invoice`, `bfl_store_id`, `apple_store_id`, `p_direct_billing`, `token_billing`, `online_billing`, `web_billing`, `idpartner_type`, `branch_dc_no`, `petti_cash_balance`, `expense_allowed`, `allow_purchase_direct_inward`, `acc_branch_id`, `hrms_branch_id`, `is_billing`, `credit_limit`, `credit_days`, `idservice_executive`) VALUES
(1, 0, 7, 'AKLUJ', 'AKLJ', '27AAXCS2330R1ZH', 'INDAPUR - PANDHARPUR RD.,CTS NO.2258/3,PRANALI SHOPPING CENTRE, SHOP NO.3, AKLUJ - 413101  ', 413101, 'Maharashtra', 'Solapur', 'Madha', 1, 'akluj@ssmobile.com', 'Akluj Manager', '8956433811', 17.888748, 75.018883, 1, 4, 4, 1, 1, 1, NULL, 0, NULL, 32, '2021-12-07 11:53:54', 3781, 18, 0, 0, '54429', 2221556, 0, 1, 1, 0, 2, 0, NULL, 1, 0, 11, 48, 0, 0, 0, 1288),
(7, 1, 0, 'GANDHINAGAR Warehouse', 'GDRW', '27AAXCS2330R1ZH', 'M.No.16,11/f-13, Ahuja building, Near Guru Nanak petrol pump, Mudslinging occupied, Gandhinagar - 416122    ', 416122, 'Maharashtra', 'Kolhapur', 'Karveer', 1, 'gnnr@ss.comm', 'Aprina chavan', '9890080206', 74.3030102, 16.7077082, 1, 12, 12, 1, 0, 1, NULL, 0, '2020-05-16 19:28:08', 32, '2021-12-22 11:13:32', 242, 111, 0, 0, '54429', 9, 0, 1, 1, 1, NULL, 0, 11143, 1, 0, NULL, NULL, 0, 50000000, 365, NULL),
(8, 0, 7, 'BALAJI NAGAR', 'BLJN', '27AAXCS2330R1ZH', 'C.S.NO.30/1, PLOT NO.9,SHOP NO.1&2, GROUND FLOOR,GURUPRASAD BUILDING,OPP.SBI, BALAJI NAGAR CHOWK,DHANKAWADI, PUNE - 411043   ', 411043, 'Maharashtra', 'Mumbai', 'NA', 1, 'balajinagar@ssmobile.com', 'Balaji Nagar Manager', '9767737475 ', 18.4655572, 73.8580118, 1, 3, 3, 1, 1, 1, 32, 0, '2020-05-17 15:13:06', 32, '2021-12-07 11:30:20', 3063, 9, 0, 0, '54429', 2221568, 0, 1, 1, 0, 2, 0, 18000, 1, 0, 61, 49, 0, 100000, 50, 1287),
(9, 0, 7, 'BARSHI', 'BARS', '27AAXCS2330R1ZH', 'C.S.NO.4305, GROUND FLOOR, SOMWAR PETH, BARSHI - 413401  ', 413401, 'Maharashtra', 'Solapur', 'Barshi', 1, 'barshi@ssmobile.com', 'Barshi Manager', '8956433813', 18.234219, 75.690809, 1, 15, 15, 1, 2, 1, 32, 0, '2020-05-30 15:19:02', 32, '2021-12-07 11:54:11', 2321, 5, 0, 0, '54429', 2472710, 0, 1, 1, 0, 2, 0, 176, 1, 0, 59, 43, 0, 60000, 50, 1288),
(10, 0, 7, 'BINDU CHOWK', 'BICW', '27AAXCS2330R1ZH', 'BUSINESS POINT COMPLEX, 1555/3, C WARD, SHIVAJI ROAD, NR.PADMA TAWLKIES, BINDU CHOWK KOLHAPUR.- 416012  ', 416012, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'ssbinduchowk@ssmobile.com', 'Binduchowk Manager', '9158227373', 16.6979475, 74.2272211, 1, 1, 1, 1, 1, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2021-12-07 11:16:00', 2918, 8, 0, 0, '54429', 2221551, 0, 1, 1, 0, 1, 0, 20130, 1, 0, 30, 2, 0, 100000, 50, NULL),
(11, 0, 7, 'CHIPLUN 1', 'CHI1', '27AAXCS2330R1ZH', 'PRIME CENTRE, CHINCH NAKA, OPP.SBI,GALA NO.1, CHIPLUN - 415605 ', 415605, 'Maharashtra', 'Ratnagiri', 'Chiplun', 1, 'chiplun@ssmobile.com', 'Chiplun 1 Manager', '7066037536', 17.529933, 73.517005, 1, 11, 11, 1, 1, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2021-12-07 11:34:28', 4319, 19, 0, 0, '54429', 2221552, 0, 1, 1, 0, 2, 0, 22000, 1, 0, 19, 18, 0, 100000, 50, NULL),
(12, 0, 7, 'CHIPLUN 2', 'CHI2', '27AAXCS2330R1ZH', 'GALA NO.34, SUVARNA MANDIR COMPLEX, KARAD ROAD,CHIPLUN - 415605  ', 415605, 'Maharashtra', 'Ratnagiri', 'Chiplun', 1, 'chiplun2@ssmobile.com', 'Chiplun 2 Manager', '9860798075', 17.528852, 73.519358, 1, 11, 11, 1, 4, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2021-12-07 11:34:45', 5630, 29, 0, 0, '54429', 2399356, 0, 1, 1, 0, 2, 0, 40270, 1, 0, 34, 24, 0, 150000, 50, NULL),
(13, 0, 7, 'CHIPLUN 3', 'CHI3', '27AAXCS2330R1ZH', 'Near Bank Of Maharashtra, A\\P Kherdi, Chiplun - 415604 ', 415604, 'Maharashtra', 'Ratnagiri', 'Chiplun', 1, 'kherdi@ssmobile.com', 'Chiplun 3 Manager', '9890236231', 17.5205039, 73.5447996, 1, 11, 11, 1, 2, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2021-12-07 11:38:58', 2854, 14, 0, 0, '54429', 2685025, 0, 1, 1, 0, 2, 0, 18950, 1, 0, 46, 39, 0, 60000, 50, NULL),
(14, 0, 7, 'GADHINGLAJ 1', 'GADH', '27AAXCS2330R1ZH', 'Opposite Bus Stand, Main Road, Gadhinglaj. Pin - 416502', 416502, 'Maharashtra', 'Kolhapur', 'Gadhinglaj', 1, 'gadhinglaj@ssmobile.com', 'Gadhinglaj Manager', '7030293434', 16.225492, 74.351172, 1, 13, 13, 1, 2, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2022-02-01 10:37:03', 2845, 5, 0, 0, '54429', 2221558, 0, 1, 1, 0, 2, 0, 41950, 1, 0, 26, 6, 0, 100000, 50, NULL),
(15, 0, 7, 'GANDHINAGAR', 'GNDH', '27AAXCS2330R1ZH', 'M.NO.1611/F-13, GROUND FLOOR, MAIN ROAD, GADMUDSHINGI OCCUPIED GANDHINAGAR - 416122', 416122, 'Maharashtra', 'Kolhapur', 'Karveer', 1, 'gandhinagar@ssmobile.com', 'Gandhinagar Manager', '9730290123', 16.705477, 74.296484, 1, 13, 13, 1, 4, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2021-12-07 11:16:42', 3592, 14, 0, 0, '54429', 2239262, 0, 1, 1, 0, 2, 0, 28320, 1, 0, 7, 4, 0, 150000, 50, NULL),
(16, 0, 7, 'HADAPSAR', 'HADP', '27AAXCS2330R1ZH', 'C.S.NO.84/1+2+3A+9+10D/3+4+5, VAIBHAV COMMERCIAL COMPLEX,GROUND FLOOR,SHOP NO.13, HADAPSAR,TAL.- HAVELI,DIST. - PUNE - 411028  ', 411028, 'Maharashtra', 'Pune', 'Pune City', 1, 'hadapsar@ssmobile.com', 'GHadapsar Manager', '8956020098', 18.501589, 73.929943, 1, 3, 3, 1, 2, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2021-12-07 11:30:40', 1660, 11, 0, 0, '54429', 2221541, 0, 1, 1, 0, 1, 0, 19480, 1, 0, 56, 46, 0, 0, 50, 1287),
(17, 0, 7, 'ICHALKARANJI 1', 'ICH1', '27AAXCS2330R1ZH', '5/99, NR.VYANKATRAO HIGH SCHOOL, OPP.DR. BHIDE, ICHALKARANJI - 416115 ', 416115, 'Maharashtra', 'Kolhapur', 'Hatkanangle', 1, 'sscommunication.ichalkaranji@ssmobile.com', 'Ichalkaranji 1 Manager', '9096309993', 16.683561, 74.465428, 1, 14, 14, 1, 3, 1, NULL, 1, '2020-05-16 19:28:08', 32, '2022-02-01 10:40:50', 5654, 15, 0, 0, '54429', 2221553, 0, 1, 1, 0, 1, 0, 64250, 1, 0, 8, 7, 0, 150000, 50, NULL),
(18, 1, 0, 'MUMBAI Warehouse', 'MUMW', '27AAXCS2330R1ZH', ' Unit No.12,Second Floor, Harmony Mall, Goregaon Link Road, Bhagat Singh Nagar No. 1, Goregaon West, Mumbai 400104 ', 400104, 'Maharashtra', 'Mumbai', 'Goregaon West', 1, 'mumbai@ss.com', 'Mansi Thakar', '8850030280', 18.5245649, 73.7228812, 1, 12, 12, 1, 0, 1, 32, 1, '2020-06-20 17:54:06', 32, '2020-11-24 16:52:38', 44, 2, 0, 0, '54429', 9, 0, 1, 1, 1, NULL, 0, 5000, 1, 0, NULL, NULL, 0, 50000000, 365, NULL),
(19, 0, 7, 'ICHALKARANJI 3', 'ICH3', '27AAXCS2330R1ZH', 'WARD NO.16, PROP.NO.162002035, OLD PROP.NO.16/1537,NEAR BHAGYAREKHA TALKIES, MAIN ROAD,ICHALKARANJI - 416115  ', 416115, 'Maharashtra', 'Kolhapur', 'Hatkanangle', 1, 'ichalkaranji3@ssmobile.com', 'Ichalkaranji 3 Manager', '8956020106', 16.689671, 74.456904, 1, 2, 2, 1, 1, 1, 32, 0, '2020-06-27 11:40:43', 32, '2022-02-01 10:41:46', 3960, 14, 0, 0, '54429', 2472711, 0, 1, 1, 0, 1, 0, 15280, 1, 0, 81, 69, 0, 100000, 50, NULL),
(20, 0, 7, 'INDAPUR', 'INDA', '27AAXCS2330R1ZH', 'PROPERTY No.w7z1003251(old 895/78),1 ST FLOOR, INDAPUR, DIST. - PUNE - 413106   ', 413106, 'Maharashtra', 'Pune', 'Indapur', 1, 'indapur@sscommunication.co.in', 'Indapur Manager', '8956931118', 18.11713, 75.025912, 1, 4, 4, 1, 5, 0, 32, 0, '2020-06-27 11:44:48', 32, '2021-11-06 11:58:08', 207, 2, 0, 0, '54429', 2399386, 0, 1, 1, 0, 2, 0, 5000, 1, 0, 0, 0, 0, 30000, 50, NULL),
(21, 0, 7, 'ISLAMPUR 1', 'ISL1', '27AAXCS2330R1ZH', 'R.S.NO.72 / 1, SHRI RAM KRISHNA HARI BUILDING, GROUND FLOOR,OPP.PUSHKAR SANSKRITIK BHAVAN, ISLAMPUR, TAL-WALWA, DIST. - SANGLI - 415409  ', 415409, 'Maharashtra', 'Sangli', 'Walva', 1, 'islampur@sscommunication.co.in', 'Islampur 1 Manager', '9168117979', 17.04731, 74.2581029, 1, 16, 16, 1, 2, 0, 32, 0, '2020-06-27 12:18:37', 32, '2021-11-10 12:23:54', 205, 0, 0, 0, '54429', 2221542, 0, 1, 1, 0, 2, 0, 6600, 1, 0, 1, 1, 0, 60000, 50, NULL),
(22, 0, 7, 'ISLAMPUR 2', 'ISL2', '27AAXCS2330R1ZH', 'Shri Ram Krishna Hari Building, Ground Floor, Opp. Pushkar Sanskritik Bhavan Behind ST Stand Collage Road.', 415409, 'Maharashtra', 'Sangli', 'Walva', 1, 'islampur2@ssmobile.com', 'Islampur 2 Manager', '8956020107', 17.047011, 74.261253, 1, 16, 16, 1, 2, 1, 32, 0, '2020-06-27 12:19:48', 32, '2021-12-07 11:26:14', 2311, 6, 0, 0, '54429', 2685022, 0, 1, 1, 0, 2, 0, 5500, 1, 0, 50, 45, 0, 60000, 50, NULL),
(23, 0, 7, 'JAYSINGPUR', 'JAYS', '27AAXCS2330R1ZH', 'KRANTI CHOWK, NEAR OLD COURT, SANGLI-KOLHAPUR ROAD, JAYSINGPUR.- 416101  ', 416101, 'Maharashtra', 'Kolhapur', 'Hatkanangale', 1, 'jaysingpur@ssmobile.com', 'Jaysingpur Manager', '8806476262', 16.779854, 74.55611, 1, 14, 14, 1, 1, 1, 32, 0, '2020-06-27 12:20:57', 32, '2021-12-07 11:16:58', 3712, 20, 0, 0, '54429', 2221559, 0, 1, 1, 0, 2, 0, 7000, 1, 0, 22, 12, 0, 100000, 50, NULL),
(24, 0, 7, 'J M ROAD', 'JMRO', '27AAXCS2330R1ZH', 'CTS NO.418,MITTAL CHAMBERS,SHOP NO.3/4/5, J.M.ROAD, PUNE - 411004 ', 411004, 'Maharashtra', 'Pune', 'Pune City', 1, 'jmroad.pune@ssmobile.com', 'JM Road Manager', '7028821400', 18.525493, 73.849936, 1, 3, 3, 1, 8, 1, 32, 0, '2020-06-27 12:23:05', 32, '2021-12-21 15:16:21', 6696, 32, 0, 0, '54429', 2472706, 0, 1, 1, 0, 1, 0, 67050, 1, 0, 57, 73, 0, 300000, 100, 1287),
(25, 0, 7, 'KAGAL', 'KAGL', '27AAXCS2330R1ZH', 'Shop No. 4/5 ,Ground floor, Near Bhuyekar Petrol Pump, Near Bank Of Maharashtra, Kagal - 416216  ', 416216, 'Maharashtra', 'Kolhapur', 'Kagal', 1, 'kagal@ssmobile.com', 'Kagal Manager', '9607115858', 16.5800646, 74.3133108, 1, 13, 13, 1, 1, 1, 32, 0, '2020-06-27 12:33:09', 32, '2021-12-07 11:19:09', 3816, 12, 0, 0, '54429', 2221560, 0, 1, 1, 0, 2, 0, 0, 1, 0, 51, 35, 0, 100000, 50, NULL),
(26, 0, 7, 'KANKAVALI 1', 'KAN1', '27AAXCS2330R1ZH', 'S.NO.207 A, H.NO.43, SHOP NO.3, MUDRA, KANKAVALI - 416602 ', 416602, 'Maharashtra', 'Sindhudurg', 'Kankavli', 1, 'kankavli@ssmobile.com', 'Kankavli Manager', '7030934427', 16.267894, 73.708042, 1, 6, 6, 1, 2, 1, 32, 0, '2020-06-27 12:34:23', 32, '2021-12-07 11:41:29', 2588, 5, 0, 0, '54429', 2221561, 0, 1, 1, 0, 2, 0, 31400, 1, 0, 40, 28, 0, 50000, 50, NULL),
(27, 0, 7, 'KARAD 1', 'KAR1', '27AAXCS2330R1ZH', 'PLOT NO.469, B/3, SHANIWAR PETH, KARAD - 415110 ', 415110, 'Maharashtra', 'Satara', 'Karad', 1, 'sscommunication.karad@ssmobile.com', 'Karad 1 Manager', '9168430022', 17.276246, 74.180992, 1, 16, 16, 1, 3, 1, 32, 0, '2020-06-27 12:36:46', 32, '2021-12-22 11:32:18', 4182, 12, 0, 0, '54429', 2221554, 0, 1, 1, 0, 1, 0, 4810, 1, 0, 9, 27, 0, 150000, 50, NULL),
(28, 0, 7, 'KARAD 2', 'KAR2', '27AAXCS2330R1ZH', '37, INDU COMPLEX,C/O.SHYAM SALES, SHANIWAR PETH , KARAD. - 415110 ', 415110, 'Maharashtra', 'Satara', 'Karad', 1, 'karad2@sscommunication.co.in', 'Karad 2 Manager', '7030296767', 17.28553, 74.180075, 1, 16, 16, 1, 2, 0, 32, 0, '2020-06-27 12:52:51', 32, '2021-11-06 11:57:56', 69, 0, 0, 0, '54429', 2399370, 0, 1, 1, 0, 2, 0, 4000, 1, 0, 0, 0, 0, 60000, 50, NULL),
(29, 0, 7, 'KAWATHE MAHNKAL', 'KAVM', '27AAXCS2330R1ZH', 'C.S NO./GAT NO.1418 /1, PLOT NO.132 & 133, GAJANAN PLAZA,GROUND FLOOR, GALA NO.2,KAVATHEMAHANKAL,DIST-SANGLI - 416405  ', 416405, 'Maharashtra', 'Sangli', 'K Mahankal', 1, 'kavathemahankal@ssmobile.com', 'Kavte Mahankal Manager', '8484966245', 17.007412, 74.862451, 1, 14, 14, 1, 5, 1, 32, 0, '2020-06-27 13:03:52', 32, '2021-12-07 11:27:54', 1384, 2, 0, 0, '54429', 2399371, 0, 1, 1, 0, 2, 0, 4500, 1, 0, 77, 66, 0, 30000, 50, NULL),
(30, 0, 7, 'KOREGAON', 'KORE', '27AAXCS2330R1ZH', 'NEAR HUTATMA SMARAK,OPP.STATE BANK OF INDIA, MAIN ROAD,KOREGAON, DIST. - SATARA - 415501  ', 415501, 'Maharashtra', 'Satara', 'Koregaon', 1, 'koregaon@ssmobile.com', 'Koregaon Manager', '8956931115', 17.700801, 74.161969, 1, 5, 5, 1, 2, 1, 32, 0, '2020-06-27 13:05:22', 32, '2021-12-07 11:45:32', 1677, 7, 0, 0, '54429', 3422693, 0, 1, 1, 0, 2, 0, 6000, 1, 0, 72, 60, 0, 60000, 50, 1286),
(31, 0, 7, 'KUDAL', 'KUDL', '27AAXCS2330R1ZH', '3519(10-11), GROUND FLOOR, CHINTAMANI PLAZA, KUDAL, SINDHUDURG - 416520', 416520, 'Maharashtra', 'Sindhudurg', 'Kudal', 1, 'kudal@ssmobile.com', 'Kudal Manager', '8956931120', 16.005301, 73.68688, 1, 6, 6, 1, 1, 1, 32, 0, '2020-06-27 13:06:52', 32, '2021-12-07 11:42:26', 3644, 18, 0, 0, '54429', 2399395, 0, 1, 1, 0, 2, 0, 40000, 1, 0, 75, 64, 0, 100000, 50, NULL),
(32, 0, 7, 'LATUR 1', 'LAT1', '27AAXCS2330R1ZH', 'MADHU - MIRA COMPLEX, SHOP NO. 8,9,10,15,16 & 17, SHIVAJI NAGAR, LATUR - 413512 ', 413512, 'Maharashtra', 'Latur', 'Latur', 1, 'latur@ssmobile.com', 'Latur Manager', '8956797888', 18.397517, 76.566768, 1, 15, 15, 1, 2, 1, 32, 0, '2020-06-27 13:09:03', 32, '2022-02-02 16:21:47', 2151, 14, 0, 0, '54429', 2385247, 0, 1, 1, 0, 2, 0, 5850, 1, 0, 79, 67, 0, 100000, 50, 1288),
(33, 0, 7, 'LATUR 2', 'LAT2', '27AAXCS2330R1ZH', 'KEDAR MOBILE, SHOP NO.1, GANDHI MARKET, CHAIN SUKH ROAD,LATUR - 413512', 413512, 'Maharashtra', 'Latur', 'Latur', 1, 'latur2@ssmobile.com', 'Latur 2 Manager', '9028208208', 18.401034, 76.581263, 1, 15, 15, 1, 2, 1, 32, 0, '2020-06-27 13:11:10', 32, '2021-12-22 11:32:05', 2417, 2, 0, 0, '54429', 2850212, 0, 1, 1, 0, 2, 0, 4500, 1, 0, 85, 75, 0, 100000, 50, 1288),
(34, 0, 7, 'MANCHAR', 'MNCH', '27AAXCS2330R1ZH', 'SHOP NO.1, VITTHAL SMUTI BUILDING,MANCHAR, TAL.-AMBEGAON, DIST.- PUNE   ', 410503, 'Maharashtra', 'Pune', 'Ambegaon', 1, 'manchar@ssmobile.com', 'Manchar Manager', '9960465965', 19.003438, 73.943378, 1, 9, 9, 1, 7, 1, 32, 0, '2020-06-27 13:12:18', 32, '2021-12-21 15:18:39', 990, 2, 0, 0, '54429', 2850210, 0, 1, 1, 0, 2, 0, 0, 1, 0, 86, 77, 0, 30000, 50, 1287),
(35, 0, 7, 'MARGAO 1', 'MAR1', '30AAXCS2330R1ZU', 'Shop No.G/7 & G/8, Gajanan Commercial Complex, Ground Floor,Opp. Nanutel, Margao, Goa - 403601     ', 403601, 'Goa', 'South Goa', 'Salcete', 7, 'goa1@ssmobile.com', 'Margao Manager', '8956797878', 15.27784, 73.958674, 2, 6, 6, 1, 3, 1, 32, 0, '2020-06-27 13:21:24', 32, '2021-12-07 11:48:54', 3703, 17, 0, 0, '54429', 3152245, 0, 1, 1, 0, 1, 0, 6787, 1, 1, 78, 68, 0, 150000, 50, NULL),
(36, 0, 7, 'MIRAJ 1', 'MRJ1', '27AAXCS2330R1ZH', 'C.S.NO.5122 / B, NEAR POLICE STATION, MIRAJ - 416410  ', 416410, 'Maharashtra', 'Sangli', 'Miraj', 1, 'sscommunication.miraj@ssmobile.com', 'Miraj 1 Manager', '9168127676', 16.821602, 74.649581, 1, 14, 14, 1, 2, 1, 32, 0, '2020-06-27 13:23:17', 32, '2021-12-07 11:24:53', 2638, 8, 0, 0, '54429', 2221562, 0, 1, 1, 0, 1, 0, NULL, 1, 0, 39, 30, 0, 60000, 50, NULL),
(37, 0, 7, 'MIRAJ 2', 'MRJ2', '27AAXCS2330R1ZH', 'C.S.NO.5875(G), UPPER GROUND FLOOR OF SHOPPING CENTRE,SHOP NO.19 & 20, MIRAJ - 416410  ', 416410, 'Maharashtra', 'Sangli', 'Miraj', 1, 'miraj2@ssmobile.com', 'Ramesh Mali', '8767223780', 16.820429, 74.649792, 1, 2, 2, 1, 2, 1, 32, 0, '2020-06-27 13:25:03', 32, '2021-12-07 11:25:21', 2090, 6, 0, 0, '54429', 2472705, 0, 1, 1, 0, 1, 0, 38960, 1, 0, 80, 74, 0, 60000, 50, NULL),
(38, 0, 7, 'MIRAJKAR TIKATI', 'MIRT', '27AAXCS2330R1ZH', 'GALA NO.16,28,15,29, MAHILA SEVA SANKUL, OPP.BALGOPAL TALIM MANDAL, MANGALWAR PETH, KOLHAPUR - 416012 ', 416012, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'sscommunication.mirajkartickti@ssmobile.com', 'Mirajkar Tikati Manager', '9881349996', 16.693536, 74.225579, 1, 13, 13, 1, 3, 1, 32, 0, '2020-06-27 13:26:39', 32, '2022-02-01 10:38:33', 5909, 32, 0, 0, '54429', 2221563, 0, 1, 1, 0, 1, 0, 28790, 1, 0, 4, 10, 0, 150000, 50, NULL),
(39, 0, 7, 'MUDHAL TITTA', 'MUDT', '27AAXCS2330R1ZH', 'GAT NO.183, UPPER GROUND FLOOR, SHOP NO.1 & 2, MUDHAL TITTA, TAL- KAGAL, DIST. - KOLHAPUR - 416209', 416209, 'Maharashtra', 'Kolhapur', 'Bhudargad', 1, 'mudhaltitta@ssmobile.com', 'Mudhal Titta Manager', '8956931110', 16.407517, 74.142864, 1, 13, 13, 1, 2, 1, 32, 0, '2020-06-27 13:28:14', 32, '2022-02-01 10:37:48', 2882, 13, 0, 0, '54429', 2399374, 0, 1, 1, 0, 2, 0, 7130, 1, 0, 69, 59, 0, 60000, 50, NULL),
(40, 0, 7, 'NAGAR 1', 'NAG1', '27AAXCS2330R1ZH', 'Sai Palace, Shop No. 10-11, Opposite Akashwani, Professor Chowk. Savedi, Ahmednagar - 414003 ', 414003, 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', 1, 'nagar@ssmobile.com', 'Nagar 1 Manager', '9607113636', 19.117104, 74.735771, 1, 10, 10, 1, 1, 1, 32, 0, '2020-06-27 13:32:37', 32, '2021-11-30 12:51:14', 3551, 3, 0, 0, '54429', 2221547, 0, 1, 1, 0, 1, 0, NULL, 1, 0, 52, 36, 0, 100000, 50, NULL),
(41, 0, 7, 'NAGAR 2', 'NAG2', '27AAXCS2330R1ZH', 'SHOP NO.1, CHANDRALOK APPT., DELHI GATE, GUNDU BAZAR , AHMEDNAGAR - 414001 ', 414001, 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', 1, 'ssnagar2@ssmobile.com', 'Nagar 2 Manager', '9890761616', 19.098194, 74.732458, 1, 10, 10, 1, 7, 1, 32, 0, '2020-06-27 13:44:38', 32, '2021-12-21 15:19:02', 1267, 2, 0, 0, '54429', 2399340, 0, 1, 1, 0, 2, 0, 20000, 1, 0, 54, 38, 0, 50000, 50, NULL),
(42, 0, 7, 'NAGAR 3', 'NAG3', '27AAXCS2330R1ZH', 'Plot No.93 out of Plot No.1 & 2,\'NAMOHA COMPOUND\',Ahmednagar - 414001 ', 414001, 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', 1, 'nagar3@ssmobile.com', 'Nagar 3 Manager', '9607113838', 19.084487, 74.734599, 1, 10, 10, 1, 4, 1, 32, 0, '2020-06-27 13:45:44', 32, '2021-12-07 11:59:43', 3450, 11, 0, 0, '54429', 2399341, 0, 1, 1, 0, 1, 0, 7000, 1, 0, 76, 65, 0, 150000, 50, NULL),
(43, 0, 7, 'PANDHARPUR', 'PAND', '27AAXCS2330R1ZH', 'C.S.NO.4041/A 1, WARD NO.7,BHADULE CHOWK, PANDHARPUR - 413304 ', 413304, 'Maharashtra', 'Solapur', 'Pandharpur', 1, 'pandharpur@ssmobile.com', 'Pandharpur Manager', '9075456999', 17.679074, 75.331753, 1, 4, 4, 1, 4, 1, 32, 0, '2020-06-27 13:47:26', 32, '2021-12-07 11:53:11', 4865, 19, 0, 0, '54429', 2221565, 0, 1, 1, 0, 2, 0, 2150, 1, 0, 29, 44, 0, 150000, 50, 1288),
(44, 0, 7, 'PETH VADGAON', 'PETV', '27AAXCS2330R1ZH', 'PADMA ROAD, NEAR JAY BHAVANI PATH SANSTHA, PETH VADGAON. - 416112 ', 416112, 'Maharashtra', 'Kolhapur', 'Hatkanangle', 1, 'pethvadgaon@ssmobile.com', 'Peth Vadgaon Manager', '8806734949', 16.8357033, 74.312423, 1, 13, 13, 1, 1, 1, 32, 0, '2020-06-27 13:49:14', 32, '2022-02-01 10:39:01', 3578, 8, 0, 0, '54429', 2221566, 0, 1, 1, 0, 2, 0, 5210, 1, 0, 21, 9, 0, 180000, 50, NULL),
(45, 0, 7, 'PHALTAN 1', 'PHL1', '27AAXCS2330R1ZH', 'SAI PLAZA , DEd CHOWK RING ROAD , LAXMI NAGAR , PHALTAN DIST. - SATARA - 415015 ', 415015, 'Maharashtra', 'Satara', 'Satara', 1, 'phaltan1@ssmobile.com', 'Phaltan 1 Manager', '7028781500', 17.984226, 74.435147, 1, 5, 5, 1, 1, 1, 32, 0, '2020-06-27 13:55:16', 32, '2021-12-07 11:45:16', 3324, 15, 0, 0, '54429', 2685024, 0, 1, 1, 0, 2, 0, 7601, 1, 0, 27, 71, 0, 100000, 365, 1286),
(46, 0, 7, 'PIMPRI', 'PIMP', '27AAXCS2330R1ZH', 'SHOP NO. 315, GROUND FLOOR+1, NEAR SAI MANDIR, SAI CHOWK, PIMPARI-CAMP, PUNE - 411017 ', 411017, 'Maharashtra', 'Pune', 'Pune City', 1, 'pimpri@ssmobile.com', 'Pimpri Manager', '9545454541', 18.620403, 73.803626, 1, 9, 9, 1, 3, 1, 32, 0, '2020-06-27 13:59:58', 32, '2021-12-07 11:29:39', 4960, 19, 0, 0, '54429', 2239261, 0, 1, 1, 0, 2, 0, 7510, 1, 0, 68, 58, 0, 200000, 50, 1287),
(47, 0, 7, 'RAJARAMPURI 1', 'RAJ1', '27AAXCS2330R1ZH', '2018/KH/20, PRABHAVATI APPT.,4TH LANE, RAJARAMPURI, KOLHAPUR - 416008 ', 416008, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'sscommunication.rajarampuri@ssmobile.com', 'Rajarampuri 1 Manager', '8411965001', 16.6965278, 74.244083, 1, 1, 1, 1, 1, 1, 32, 0, '2020-06-27 14:07:05', 32, '2021-12-07 11:15:30', 3179, 25, 0, 0, '54429', 3087919, 0, 1, 1, 0, 1, 0, NULL, 1, 0, 5, 5, 0, 100000, 50, NULL),
(48, 0, 7, 'RAJARAMPURI 2', 'RAJ2', '27AAXCS2330R1ZH', 'C.S.NO.1115/B/2,SHOP.NO.5,LOWER G.FLR.,\'TATHASTU CORNER\'SHOP NO.5,OPP.RAILWAY GAT NO.2, FIVE BUNGLOW AREA, \'E\' WARD, SHAHUPURI, KOLHAPUR - 416001', 416001, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'rajarampuri2@ssmobile.com', 'Rajarampuri 2 Manager', '8956797877', 16.701164, 74.241723, 1, 1, 1, 1, 8, 1, 32, 0, '2020-06-27 14:08:22', 32, '2021-12-21 15:16:03', 6075, 30, 0, 0, '54429', 2221545, 0, 1, 1, 0, 1, 0, 0, 1, 0, 63, 54, 0, 500000, 50, 0),
(49, 0, 7, 'RANKALA', 'RANK', '27AAXCS2330R1ZH', 'R.S.NO.1234/3 & 1316/1, SHOP NO.16 A, PLOT NO.1, WATERFRONT, NR.D MART, RANKALA, KOLHAPUR - 416012 ', 416012, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'rankala@ssmobile.com', 'Rankala Manager', '8806962277', 16.693359, 74.208536, 1, 13, 13, 1, 2, 1, 32, 0, '2020-06-27 14:13:58', 32, '2022-02-01 10:38:46', 2340, 8, 0, 0, '54429', 2399373, 0, 1, 1, 0, 2, 0, 5500, 1, 0, 36, 3, 0, 400000, 50, NULL),
(50, 0, 7, 'RATNAGIRI 1', 'RAT1', '27AAXCS2330R1ZH', 'S.N.372, B, C.S.NO.175/B, SHOP NO.3, GROUND FLOOR,SAMRAT SHOPPING CENTER,OPP.MARUTI MANDIR, RATNAGIRI ', 415612, 'Maharashtra', 'Ratnagiri', 'Ratnagiri', 1, 'ratnagiri1@ssmobile.com', 'Ratnagiri 1 Manager', '9022178373', 16.9902777, 73.3099408, 1, 11, 11, 1, 4, 1, 32, 0, '2020-06-27 14:22:05', 32, '2021-12-22 11:31:28', 4372, 17, 0, 0, '54429', 2685026, 0, 1, 1, 0, 2, 0, 56450, 1, 0, 83, 21, 0, 100000, 50, NULL),
(51, 0, 7, 'RATNAGIRI 2', 'RAT2', '27AAXCS2330R1ZH', 'SHOP NO.15 & 16,NAVKAR PLAZA, MARUTI MANDIR, RATNAGIRI - 415612  ', 415612, 'Maharashtra', 'Ratnagiri', 'Ratnagiri', 1, 'ratnagiri2@ssmobile.com', 'Ratnagiri 2 Manager', '8390455123', 16.990286, 73.313139, 1, 11, 11, 1, 3, 1, 32, 0, '2020-06-27 14:24:27', 32, '2021-12-07 11:33:40', 7426, 31, 0, 0, '54429', 2399392, 0, 1, 1, 0, 2, 0, 6700, 1, 0, 37, 16, 0, 200000, 50, NULL),
(52, 0, 7, 'RATNAGIRI 3', 'RAT3', '27AAXCS2330R1ZH', 'SANKESHWAR ARCADE, ATHAWADI BAZAR, GROUND FLOOR, SHOP NO.36, RATNAGIRI. ', 415612, 'Maharashtra', 'Ratnagiri', 'Ratnagiri', 1, 'ratnagiri3@ssmobile.com', 'Ratnagiri 3 Manager', '8390433123', 16.992323, 73.292278, 1, 11, 11, 1, 2, 1, 32, 0, '2020-06-27 14:32:00', 32, '2021-12-07 11:34:03', 2224, 10, 0, 0, '54429', 2472707, 0, 1, 1, 0, 2, 0, 13520, 1, 0, 65, 55, 0, 60000, 50, NULL),
(53, 0, 7, 'SADASHIV PETH', 'SADP', '27AAXCS2330R1ZH', 'SHOP NO.5,GANESH SADAN, SURVEY NO.1164, SADASHIV PETH, PUNE - 411030. ', 411030, 'Maharashtra', 'Pune', 'Pune City', 1, 'sadashivpeth@ssmobile.com', 'Sadashiv Peth Manager', '9890762626', 18.5110833, 73.8459722, 1, 3, 3, 1, 1, 1, 32, 0, '2020-06-27 14:33:15', 32, '2021-12-07 11:29:59', 2445, 11, 0, 0, '54429', 2221546, 0, 1, 1, 0, 1, 0, 15000, 1, 0, 62, 53, 0, 100000, 50, 1287),
(54, 0, 7, 'SANGAMNER', 'SANG', '27AAXCS2330R1ZH', 'SURVEY NO.151/139/1,RAJPAL CLOTH STORE,SHOP NO.1/2/3 & 4, SANGAMNER, DIST - NAGAR - 422605 ', 422605, 'Maharashtra', 'Ahmed Nagar', 'Sangamner', 1, 'sangamner@ssmobile.com', 'Sangamner Manager', '8956931114', 19.5725794, 74.2093346, 1, 10, 10, 1, 4, 1, 32, 0, '2020-06-27 14:34:46', 32, '2021-12-07 12:00:27', 4522, 10, 0, 0, '54429', 2399393, 0, 1, 1, 0, 2, 0, 6100, 1, 0, 71, 57, 0, 150000, 50, NULL),
(55, 0, 7, 'SANGLI 1', 'SAN1', '27AAXCS2330R1ZH', 'C.S.NO.404/3, SHIV MERIDIAN,GALA NO.12,13,14, KHANBHAG, SANGLI - 416416', 416416, 'Maharashtra', 'Sangli', 'Tasgaon', 1, 'sscommunication.sangli@ssmobile.com', 'Sangli 1 Manager', '9975799222', 16.85935, 74.57264, 1, 2, 2, 1, 6, 1, 32, 0, '2020-06-27 14:36:09', 32, '2022-01-13 18:06:59', 14891, 50, 0, 0, '54429', 2221548, 0, 1, 1, 0, 1, 0, 19000, 1, 0, 10, 11, 0, 50000000, 365, NULL),
(56, 0, 7, 'SANGLI 2', 'SAN2', '27AAXCS2330R1ZH', 'B,1 &2, SHIV MERIAN APPT.,M.G. ROAD, SANGLI - 416410 ', 416410, 'Maharashtra', 'Sangli', 'Miraj', 1, 'sangli2@ssmobile.com', 'Sangli 2 Manager', '7030893377', 16.85972, 74.572714, 1, 14, 14, 1, 4, 1, 32, 0, '2020-06-27 14:38:50', 32, '2021-12-07 11:23:49', 4830, 11, 0, 0, '54429', 2399394, 0, 1, 1, 0, 1, 0, 30700, 1, 0, 24, 13, 0, 150000, 50, NULL),
(57, 0, 7, 'SANGLI 3', 'SAN3', '27AAXCS2330R1ZH', 'C.S.NO.1056/4, SHOP NO. 2 & 3, GAONBHAG, SANGLI - 415410 ', 415410, 'Maharashtra', 'Sangli', 'Shirala', 1, 'sangli3@ssmobile.com', 'Sangli 3 Manager', '8956020104', 16.855165, 74.562999, 1, 2, 2, 1, 1, 1, 32, 0, '2020-06-27 14:40:09', 32, '2021-12-07 11:24:15', 3166, 10, 0, 0, '54429', 2239260, 0, 1, 1, 0, 2, 0, 940, 1, 0, 55, 42, 0, 100000, 50, NULL),
(58, 0, 7, 'SANGOLA', 'SONG', '27AAXCS2330R1ZH', 'Shop no. 9, Rajaram complex, Ground floor, Sangola, Solapur - 413307 ', 413307, 'Maharashtra', 'Solapur', 'Sangola', 1, 'sangola@ssmobile.com', 'Sangola Manager', '9960846262', 17.4382304, 75.1893718, 1, 4, 4, 1, 2, 1, 32, 0, '2020-06-27 14:41:24', 32, '2021-12-07 11:53:26', 3201, 30, 0, 0, '54429', 2221570, 0, 1, 1, 0, 2, 0, 4650, 1, 0, 53, 50, 0, 60000, 50, 1288),
(59, 0, 7, 'SATARA 1', 'SAT1', '27AAXCS2330R1ZH', 'PUSHPDATTA APPT.,SHOP NO.5,6,7, PL.NO.3,SARVEY NO.481 A,BHUVIKAS PETROL PUMP,SADAR BAZAR,SATARA - 415001 ', 415001, 'Maharashtra', 'Satara', 'Satara', 1, 'satara@ssmobile.com', 'Satara 1 Manager', '9975699222', 17.697202, 74.004741, 1, 5, 5, 1, 3, 1, 32, 0, '2020-06-27 14:42:44', 32, '2021-12-07 11:43:31', 4421, 19, 0, 0, '54429', 2221549, 0, 1, 1, 0, 1, 0, 5540, 1, 0, 16, 29, 0, 250000, 100, 1286),
(60, 0, 7, 'SATARA Z P CHOWK', 'SZPC', '27AAXCS2330R1ZH', 'Shop no- 5, Lucky Plaza, Z P Chowk, Satara 415001', 415001, 'Maharashtra', 'Satara', 'Satara', 1, 'satara2@ssmobile.com', 'Satara 2 Manager', '7030934425', 17.686874, 74.0167, 1, 5, 5, 1, 2, 1, 32, 0, '2020-06-27 14:44:06', 1546, '2022-02-03 16:21:28', 2416, 8, 0, 0, '54429', 2472708, 0, 1, 1, 0, 2, 0, 7095, 1, 0, 38, 22, 0, 100000, 50, 1286),
(61, 0, 7, 'SATARA POWAI NAKA', 'SAT3', '27AAXCS2330R1ZH', 'SHOP NO. 1A, VITTHAL LEELA COMPLEX, OPP.IDBI BANK, POWAI NAKA, SATARA - 415001 ', 415001, 'Maharashtra', 'Satara', 'Satara', 1, 'satara3.powainaka@ssmobile.com', 'Satara 3 Manager', '9607872727', 17.686947, 74.004859, 1, 5, 5, 1, 1, 1, 32, 0, '2020-06-27 14:49:33', 32, '2021-12-07 11:44:10', 2544, 12, 0, 0, '54429', 2685023, 0, 1, 1, 0, 2, 0, 8000, 1, 0, 45, 72, 0, 100000, 50, 1286),
(62, 0, 7, 'SAWANTWADI', 'SAWT', '27AAXCS2330R1ZH', 'S.N. 85/1/ A, GROUND FLOOR, PATIL TOWERS,GAVALI TITHA,NR.S.T.STAND, SAWANTWADI - 416510 ', 416510, 'Maharashtra', 'Sindhudurg', 'Sawantwadi', 1, 'sawantwadi@ssmobile.com', 'Sawantwadi Manager', '7066755656', 15.910949, 73.822515, 1, 6, 6, 1, 2, 1, 32, 0, '2020-06-27 14:52:01', 32, '2021-12-07 11:42:45', 2256, 14, 0, 0, '54429', 2221571, 0, 1, 1, 0, 2, 0, 5500, 1, 0, 49, 40, 0, 60000, 50, NULL),
(63, 0, 7, 'SHAHUPURI', 'SHAH', '27AAXCS2330R1ZH', 'RATIKAMAL COMPLEX, 399 E WARD, SHAHUPURI, KOLHAPUR - 416001', 416001, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'sscommunication.shahupuri@ssmobile.com', 'Shahupuri Manager', '8380095094', 16.7042753, 74.2333574, 1, 1, 1, 1, 6, 1, 32, 0, '2020-06-27 14:53:23', 32, '2021-12-07 11:14:19', 12913, 165, 0, 0, '54429', 2221538, 0, 1, 1, 0, 1, 0, 39000, 1, 1, 3, 31, 0, 50000000, 365, NULL),
(64, 0, 7, 'SHIRUR', 'SHIR', '27AAXCS2330R1ZH', 'C.S.NO.160, HOUSE NO. D3Z - 1000062, GROUND FLOOR, A/P. & TAL. - SHIRUR, DIST. - PUNE - 412210 ', 412210, 'Maharashtra', 'Pune', 'Shirur', 1, 'shirur@ssmobile.com', 'Shirur Manager', '8956587323', 18.827669, 74.374177, 1, 3, 3, 1, 1, 1, 32, 0, '2020-06-27 14:55:09', 32, '2022-02-07 10:18:49', 2165, 11, 0, 0, '54429', 2399387, 0, 1, 1, 0, 2, 0, 4125, 1, 0, 73, 62, 0, 100000, 50, NULL),
(65, 0, 7, 'SOLAPUR 1', 'SOP1', '27AAXCS2330R1ZH', 'SHOP NO.4,GR.FLOOR, KHANCHAND MARKET.,H.NO.97/7,GOLDKING PETH, SOLAPUR - 416005 ', 413007, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'solapur@ssmobile.com', 'Solapur 1 Manager', '7030934424', 17.676794, 75.899783, 1, 4, 4, 1, 4, 1, 32, 0, '2020-06-27 14:57:43', 32, '2021-12-07 11:51:49', 4663, 18, 0, 0, '54429', 2221543, 0, 1, 1, 0, 2, 0, 6700, 1, 0, 28, 23, 0, 150000, 50, 1288),
(66, 0, 7, 'SOLAPUR 2', 'SOP2', '27AAXCS2330R1ZH', 'PLOT NO.11/14, SHOP NO. 1 & 2, RAILWAY LINES, CTS NO.840/2/2 F,SOLAPUR - 413001', 413001, 'Maharashtra', 'Solapur', 'Solapur North', 1, 'solapur2@ssmobile.com', 'Solapur 2 Manager', '8956483554', 17.663009, 75.9056595, 1, 4, 4, 1, 3, 1, 32, 0, '2020-06-27 15:00:22', 32, '2021-12-07 11:51:32', 6999, 25, 0, 0, '54429', 2221539, 0, 1, 1, 0, 2, 0, 1420, 1, 0, 58, 51, 0, 250000, 50, 1288),
(67, 0, 7, 'SOLAPUR 3', 'SOP3', '27AAXCS2330R1ZH', 'C.S.NO.744/A, HOUSE NO.697,SALGAR COMPLEX,GROUND FLOOR, SHOP NO. G-4,SOUTH KASABA PETH, SOLAPUR - 413007', 413007, 'Maharashtra', 'Solapur', 'Solapur North', 1, 'solapur3@ssmobile.com', 'Solapur 3 Manager', '8956020095', 17.677673, 75.901686, 1, 4, 4, 1, 2, 1, 32, 0, '2020-06-27 15:32:03', 32, '2021-12-07 11:52:41', 2826, 7, 0, 0, '54429', 2472709, 0, 1, 1, 0, 2, 0, 5500, 1, 0, 82, 70, 0, 50000, 50, 1288),
(68, 0, 7, 'SOLAPUR 4', 'SOP4', '27AAXCS2330R1ZH', 'SHOP NO.2 & 6, DEGAONKAR SANKUL, OPP.SUDHIR GAS AGENCY,JOD BASVANNA CHOWK,SAKHAR PETH,SOLAPUR - 413005', 413005, 'Maharashtra', 'Solapur', 'Solapur North', 1, 'solapur4@ssmobile.com', 'Solapur 4 Manager', '8767646797', 17.673305, 75.918197, 1, 4, 4, 1, 1, 1, 32, 0, '2020-06-27 15:48:59', 32, '2021-12-07 11:52:16', 3357, 15, 0, 0, '54429', 2850211, 0, 1, 1, 0, 2, 0, 3455, 1, 0, 84, 76, 0, 100000, 50, 1288),
(69, 0, 7, 'VITA 1', 'VIT1', '27AAXCS2330R1ZH', 'PANDURANG COMPLEX,NR.HDFC BANK,428/1/2, KARAD ROAD, VITA, SANGLI - 415311 ', 415311, 'Maharashtra', 'Sangli', 'Kadegaon', 1, 'ssvita@ssmobile.com', 'Vita 1 Manager', '9168739988', 17.273371, 74.535217, 1, 16, 16, 1, 2, 1, 32, 0, '2020-06-27 15:58:35', 32, '2021-12-07 11:27:20', 1855, 1, 0, 0, '54429', 2221573, 0, 1, 1, 0, 2, 0, 0, 1, 0, 25, 15, 0, 60000, 50, NULL),
(70, 0, 7, 'VITA 2', 'VIT2', '27AAXCS2330R1ZH', 'C.S.NO.1013, LAKADE PLAZA, 1ST FLOOR, SHOP NO. 1 & 2, VITA, TAL.-KHANAPUR, DIST-SANGLI - 415311 ', 415311, 'Maharashtra', 'Sangli', 'Kadegaon', 1, 'vita2@ssmobile.com', 'Vita 2 Manager', '8956931116', 17.275702, 74.538083, 1, 16, 16, 1, 2, 1, 32, 0, '2020-06-27 16:10:06', 32, '2021-12-22 11:32:33', 2688, 4, 0, 0, '54429', 2399412, 0, 1, 1, 0, 2, 0, 28500, 1, 0, 67, 56, 0, 100000, 50, NULL),
(71, 0, 7, 'WAI', 'WAI1', '27AAXCS2330R1ZH', 'C.S.NO.976/978/17, GROUND FLOOR, WAI, TAL. - WAI, DIST. - SATARA - 412803 ', 412803, 'Maharashtra', 'Satara', 'Wai', 1, 'wai@ssmobile.com', 'Wai Manager', '8956931117', 17.95267, 73.888805, 1, 5, 5, 1, 2, 1, 32, 0, '2020-06-27 16:12:34', 32, '2021-12-07 11:45:54', 2016, 10, 0, 0, '54429', 2399416, 0, 1, 1, 0, 2, 0, 6000, 1, 0, 74, 63, 0, 60000, 50, 1286),
(72, 0, 7, 'AKKALKOT', 'AKKL', '27AAXCS2330R1ZH', '739, Near Central Bank, Station Road,  Akkalkot ', 413216, 'Maharashtra', 'Solapur', 'Akkalkot', 1, 'akkalkot@ssmobile.com', 'Akkalkot Manager', '9309117647', 17.525207, 76.205319, 1, 4, 4, 1, 7, 1, 32, 0, '2020-11-23 16:45:03', 32, '2021-12-21 15:17:55', 1916, 5, 0, 0, '54429', 3232292, 0, 1, 1, 0, 2, 0, 5000, 1, 0, 110, 111, 0, 60000, 50, 1288),
(73, 0, 7, 'TULJAPUR', 'TULP', '27AAXCS2330R1ZH', 'Near Tulja Bhavani Temple,Tuljapur', 413601, 'Maharashtra', 'Osmanabad', 'Tuljapur', 1, 'tuljapur@ssmobile.com', 'Tuljapur Manager', '8010946262', 18.009341, 76.071067, 1, 15, 15, 1, 2, 1, 32, 0, '2020-11-23 16:55:32', 32, '2022-02-02 16:13:22', 1885, 5, 0, 0, '54429', 3232291, 0, 1, 1, 0, 2, 0, 4500, 1, 0, 109, 105, 0, 60000, 50, 1288),
(74, 0, 7, 'BHOSARI', 'BHSR', '27AAXCS2330R1ZH', 'A7, OPP OM HOSPITAL, SHRIRAM COLONY, ALANDI ROAD, BHOSARI', 411039, 'Maharashtra', 'Pune', 'Pune City', 1, 'bhosari@ssmobile.com', 'BHOSARI MANAGER', '8010793393', 18.623808, 73.853505, 1, 9, 9, 1, 2, 1, 32, 0, '2020-11-23 17:44:17', 32, '2021-12-07 11:30:55', 1423, 8, 0, 0, '54429', 3087917, 0, 1, 1, 0, 2, 0, 1510, 1, 0, 98, 97, 0, 60000, 50, 1287),
(75, 0, 7, 'HUPARI', 'HPRI', '27AAXCS2330R1ZH', 'MILKAT NO.348/B, SHIVAJI NAGAR,NR.LAKSHMIDEVI GIRLS HIGH SCHOOL RENDAL ROAD,HUPARI', 416203, 'Maharashtra', 'Kolhapur', 'Hatkanangale', 1, 'hupari@ssmobile.com', 'HUPARI MANAGER', '8180968101', 16.619502, 74.406318, 1, 13, 13, 1, 5, 0, 32, 0, '2020-11-23 17:49:08', 32, '2022-01-27 13:49:23', 743, 4, 0, 0, '54429', 2850206, 0, 1, 1, 0, 2, 0, 5000, 1, 0, 89, 81, 0, 30000, 50, NULL),
(76, 0, 7, 'ICHALKARANJI 2', 'ICH2', '27AAXCS2330R1ZH', 'PLOT NO.155,GALA NO. B 101,CENTER ONE, ICHALKARANJI', 416115, 'Maharashtra', 'Kolhapur', 'Hatkanangle', 1, 'ichalkaranji2@ssmobile.com', 'ICHALKRANJI 2 MANAGER', '8390344123', 16.693042, 74.450003, 1, 2, 2, 1, 7, 1, 32, 0, '2020-11-23 17:54:24', 32, '2021-12-21 15:17:39', 1775, 4, 0, 0, '54429', 2850208, 0, 1, 1, 0, 1, 0, 6950, 1, 0, 35, 8, 0, 60000, 50, NULL),
(77, 0, 7, 'JATH', 'JATH', '27AAXCS2330R1ZH', 'Mangalwar peth, Banali chowk, Near State Bank, Jath ', 416404, 'Maharashtra', 'Sangli', 'Jath', 1, 'jat@ssmobile.com', 'JATH MANAGER', '8956069211', 17.048491, 75.220268, 1, 14, 14, 1, 2, 1, 32, 0, '2020-11-23 18:01:03', 32, '2021-12-07 11:28:31', 1735, 7, 0, 0, '54429', 2685021, 0, 1, 1, 0, 2, 0, 19450, 1, 0, 97, 93, 0, 60000, 50, NULL),
(78, 0, 7, 'KANKAVALI 2', 'KAN2', '27AAXCS2330R1ZH', 'DP road,Bandu Harne Building ,Shop No. 2&3 Kankavli 416602,Sindhudurg', 416602, 'Maharashtra', 'Sindhudurg', 'Kankavli', 1, 'kankavli2@ssmobile.com', 'KANKAVALI2 MANAGER', '8956069213', 16.265058, 73.70992, 1, 6, 6, 1, 1, 1, 32, 0, '2020-11-23 18:05:07', 32, '2021-12-07 11:41:11', 3340, 12, 0, 0, '54429', 3087920, 0, 1, 1, 0, 2, 0, 4905, 1, 0, 94, 94, 0, 100000, 50, NULL),
(79, 0, 7, 'KARAD 3', 'KRD3', '27AAXCS2330R1ZH', 'SHOP NO-6,7& 8, GR.FLR,KRISHNA KRUPA SURVEY NO.34/13+14 SHANIWAR PETH, OPP. ST STAND, KARAD. DIST-SATARA MAHARASHTRA', 415110, 'Maharashtra', 'Satara', 'Karad', 1, 'karad3@ssmobile.com', 'KARAD 3 MANAGER', '9322670990', 17.283922, 74.182362, 1, 16, 16, 1, 1, 1, 32, 0, '2020-11-23 18:08:05', 32, '2021-12-07 11:44:55', 2746, 8, 0, 0, '54429', 3047687, 0, 1, 1, 0, 2, 0, 5132, 1, 0, 91, 82, 0, 100000, 50, NULL),
(80, 0, 7, 'TASGAON 2', 'TASG', '27AAXCS2330R1ZH', 'Ganesh Hits, Gala no 1, Opp Ganesh Mandir, Near petrol pump, Tasgaon', 416312, 'Maharashtra', 'Sangli', 'Tasgaon', 1, 'tasgaon2@ssmobile.com', 'TASGAON MANAGER', '9511699200', 17.034423, 74.603935, 1, 2, 2, 1, 5, 1, 32, 0, '2020-11-23 18:17:29', 32, '2021-12-07 11:28:12', 1159, 2, 0, 0, '54429', 2850205, 0, 1, 1, 0, 2, 0, 5000, 1, 0, 95, 14, 0, 30000, 50, NULL),
(81, 0, 7, 'VADUJ', 'vadu', '27AAXCS2330R1ZH', 'Vaduj - Above Mongenes, \r\nShivaji Chowk Vaduj', 415506, 'Maharashtra', 'Satara', 'Khatav', 1, 'vaduj@ssmobile.com', 'VADUJ MANAGER', '9922873645 ', 17.594668, 74.451094, 1, 5, 5, 1, 2, 1, 32, 0, '2020-11-23 18:20:33', 32, '2021-12-07 11:46:09', 2041, 7, 0, 0, '54429', 3232289, 0, 1, 1, 0, 2, 0, 5000, 1, 0, 48, 98, 0, 0, 0, NULL),
(82, 0, 7, 'KHED', 'KHED', '27AAXCS2330R1ZH', 'SIDDHI PATHARJAI BUILDING KHED GALA NO. 02,IN FRONT OF POLICE STATION,415709', 415709, 'Maharashtra', 'Ratnagiri', 'Khed (rtg)', 1, 'khed@ssmobile.com', 'KHED MANAGER', '8668798668', 17.7196762, 73.395661, 1, 11, 11, 1, 2, 1, 32, 0, '2020-11-23 18:24:19', 32, '2021-12-07 11:40:37', 1811, 6, 0, 0, '54429', 3232293, 0, 1, 1, 0, 2, 0, 20450, 1, 0, 103, 104, 0, 60000, 50, NULL),
(83, 0, 18, 'GOREGAON West', 'GOWE', '27AAXCS2330R1ZH', 'Shop No.5 & 6 Hiren Shopping Centre S.V Road ', 400104, 'Maharashtra', 'Mumbai', 'Goregaon West', 1, 'goregaon.west@ssmobile.com', 'Goregaon Manager', '9321700408', 19.1626697, 72.8458855, 1, 8, 8, 1, 1, 1, 32, 0, '2020-11-23 18:28:35', 32, '2021-12-07 12:08:19', 2103, 14, 0, 0, '54429', 3152232, 0, 1, 1, 0, 1, 0, 24960, 1, 0, 106, 112, 0, 50000000, 50, NULL),
(84, 0, 18, 'MALAD West', 'MALW', '27AAXCS2330R1ZH', 'Shop No.3 Habib Park Co Op Society Ltd Opp Jogeshwari Railway Station Jogeshwari West Mumbai', 400064, 'Maharashtra', 'Mumbai', 'Malad West', 1, 'malad.west@ssmobile.com', 'Malad manager', '9930309230', 0, 0, 1, 8, 8, 1, 2, 0, 32, 0, '2020-11-23 18:31:29', 32, '2021-12-03 11:10:14', 45, 0, 0, 0, '54429', 3152238, 0, 1, 1, 0, 1, 0, 2710, 1, 0, 0, 0, 0, 50000000, 365, NULL),
(85, 0, 18, 'MIRA ROAD East', 'MIRE', '27AAXCS2330R1ZH', 'A-SH NO-11 ASHA DEEP OPP. MAHESH INDL AREA ', 401107, 'Maharashtra', 'Thane', 'Thane', 1, 'mira_road.east@ssmobile.com', 'MIRA ROAD MANAGER', '9321696771', 19.2822417, 72.8759774, 1, 8, 8, 1, 2, 1, 32, 0, '2020-11-23 18:34:30', 32, '2021-12-07 12:10:23', 1129, 8, 0, 0, '54429', 3152240, 0, 1, 1, 0, 1, 0, 21900, 1, 0, 107, 119, 0, 50000000, 365, NULL),
(86, 0, 18, 'OSHIWARA', 'OSHI', '27AAXCS2330R1ZH', 'Shop no 5 & 6 building no.26 mhada residential complex ', 400053, 'Maharashtra', 'Mumbai', 'Mumbai', 1, 'oshiwara@ssmobile.com', 'OSHIWARA MANAGER', '9324960060', 19.1512697, 72.8315285, 1, 8, 8, 1, 2, 1, 32, 0, '2020-11-23 18:37:00', 32, '2021-12-07 12:10:41', 969, 10, 0, 0, '54429', 3152241, 0, 1, 1, 0, 1, 0, 11450, 1, 0, 108, 121, 0, 50000000, 365, NULL),
(87, 0, 7, 'VASCO', 'VSCO', '30AAXCS2330R1ZU', 'Shop No 1 & 2,Severina Centre, Swatantra Path, Near IOC Laxmi petrol Pump', 403802, 'Goa', 'South Goa', 'Mormugao', 7, 'vasco@ssmobile.com', 'VASCO MANAGER', '7058793261', 15.399701, 73.822138, 2, 6, 6, 1, 1, 1, 32, 0, '2020-11-23 18:42:16', 32, '2021-12-07 11:50:32', 2498, 13, 0, 0, '54429', 3232288, 0, 1, 1, 0, 2, 0, 6320, 1, 0, 96, 95, 0, 100000, 50, NULL),
(88, 0, 7, 'PONDA', 'PNDA', '30AAXCS2330R1ZU', 'Shop No. B1 & B4 Upper Ground Floor,', 403401, 'Goa', 'South Goa', 'Ponda', 7, 'ponda@ssmobile.com', 'PONDA MANAGER', '7083387727 ', 15.4017303, 74.0062337, 2, 6, 6, 1, 1, 1, 32, 0, '2020-11-23 18:44:50', 32, '2021-12-07 11:50:52', 2742, 7, 0, 0, '54429', 3232290, 0, 1, 1, 0, 2, 0, 14450, 1, 0, 102, 102, 0, 100000, 50, NULL),
(89, 0, 7, 'PANAJI', 'PANJ', '30AAXCS2330R1ZU', 'Ground floor , Shop no 7 & 8 - Kamat Nagar Apartment, Off MG Road , Opp Hotel Marva - Panjim - Goa - 403001', 403001, 'Goa', 'North Goa', 'Tiswadi', 7, 'panaji1@ssmobile.com', 'PANJIM MANAGER', '7262895151', 15.497301, 73.823213, 2, 6, 6, 1, 1, 1, 32, 0, '2020-11-23 18:48:47', 32, '2021-12-07 11:49:17', 2248, 6, 0, 0, '54429', 2850209, 0, 1, 1, 0, 2, 0, -14000, 1, 0, 87, 78, 0, 0, 0, NULL),
(90, 0, 7, 'MARGAO 2', 'MAR2', '30AAXCS2330R1ZU', 'SHOP NO.G-2,GROUND FLOOR,GOPIKA APPTS. MARGAO,GOA  ', 403601, 'Goa', 'South Goa', 'Salcete', 7, 'margao2@sscommunication.co.in', 'MARGAO 2 MANAGER', '7385061449', 15.271524, 73.956276, 2, 6, 6, 1, 2, 0, 32, 0, '2020-11-23 18:51:55', 32, '2021-11-06 11:57:27', 0, 0, 0, 0, '54429', 2969268, 0, 1, 1, 0, 2, 0, 8800, 1, 0, 0, 0, 0, 0, 50, NULL),
(91, 0, 142, 'NASHIK 1', 'NAK1', '27AAXCS2330R1ZH', '(College Road) - Shop No 1, Vasant chhaya, Near Vijon Hospital, Collage Road, Nashik  422005', 422005, 'Maharashtra', 'Nashik', 'Nashik', 1, 'nashik1@ssmobile.com', 'NASHIK 1 MANAGER', '8087052421', 20.005238, 73.766998, 1, 7, 7, 1, 4, 1, 32, 0, '2020-11-23 18:57:26', 32, '2021-12-07 12:04:58', 2822, 16, 0, 0, '54429', 3422694, 0, 1, 1, 0, 1, 0, 31690, 1, 1, 99, 99, 0, 100000, 50, NULL),
(92, 0, 142, 'NASHIK 2', 'NAS2', '27AAXCS2330R1ZH', '(MG Road) - Shop No 3/4, Shilpa Hotel Building, Opp. Yashwant Vyayam Shala, MG Road, Nashik - 422001', 422001, 'Maharashtra', 'Nashik', 'Nashik', 1, 'nashik2@ssmobile.com', 'NASHIL 2 MANAGER', '8087052422', 20.00436734, 73.78650683, 1, 7, 7, 1, 2, 1, 32, 0, '2020-11-23 18:58:41', 32, '2021-12-07 12:05:16', 2377, 8, 0, 0, '54429', 3422694, 0, 1, 1, 0, 1, 0, 24820, 1, 0, 100, 100, 0, 60000, 50, NULL),
(93, 0, 142, 'NASHIK 3', 'NAS3', '27AAXCS2330R1ZH', '(Untwadi) - Shop No.1, Vraj Bhoomi Apartment, behind City Center Mall, Near Kankariyas Jewellers, Untwadi, Nashik ', 422009, 'Maharashtra', 'Nashik', 'Nashik', 1, 'nashik3@ssmobile.com', 'NASHIK 3 MANAGER', '8087052423', 19.98770579, 73.75886164, 1, 7, 7, 1, 2, 1, 32, 0, '2020-11-23 19:00:04', 32, '2021-12-07 12:05:31', 2001, 6, 0, 0, '54429', 3422694, 0, 1, 1, 0, 1, 0, 45350, 1, 0, 101, 101, 0, 60000, 50, NULL),
(94, 0, 7, 'PALUS', 'PLUS', '27AAXCS2330R1ZH', 'Karad Tasgaon Road,Near Mansingh Bank , Palus . 416310', 416310, 'Maharashtra', 'Sangli', 'Palus', 1, 'palus@ssmobile.com', 'PALUS MANAGER', '7620601234', 17.093777, 74.457922, 1, 14, 14, 1, 2, 1, 32, 0, '2020-11-23 19:03:32', 32, '2021-12-07 11:28:47', 3249, 6, 0, 0, '54429', 3087918, 0, 1, 1, 0, 2, 0, 8450, 1, 0, 90, 83, 0, 60000, 50, NULL),
(95, 0, 7, 'RAHURI', 'RAHU', '27AAXCS2330R1ZH', 'Navi peth,college road in front of bhagirathi bai kanya vidyalaya rahuri,ahmednagar', 413705, 'Maharashtra', 'Ahmed Nagar', 'Rahuri', 1, 'rahuri@ssmobile.com', 'RAHURI MANAGER', '9284174587', 19.391438, 74.650196, 1, 10, 10, 1, 1, 1, 32, 0, '2020-11-23 19:05:16', 32, '2021-12-07 12:01:19', 2759, 4, 0, 0, '54429', 3312681, 0, 1, 1, 0, 2, 0, 5150, 1, 0, 119, 103, 0, 100000, 50, NULL),
(96, 0, 7, 'SWARGATE 1', 'SRGT', '27AAXCS2330R1ZH', 'C.S.NO.961, THORAT BUILDING,SHOP OF GROUND FLOOR & HALF PART OF 2ND SHOP,SHUKRAWAR PETH,RASHTRABHUSHAN CHOWK,SWARGATE,PUNE.', 411002, 'Maharashtra', 'Pune', 'Pune City', 1, 'swargate@ssmobile.com', 'SWARGATE MANAGER', '7709160169', 18.5031256, 73.8588277, 1, 3, 3, 1, 2, 1, 32, 0, '2020-11-23 19:12:54', 32, '2022-02-09 19:02:53', 2353, 10, 0, 0, '54429', 2850207, 0, 1, 1, 0, 1, 0, 11450, 1, 0, 92, 90, 0, 60000, 50, 1287),
(97, 0, 7, 'SHRIRAMPUR', 'SHRM', '27AAXCS2330R1ZH', 'KUNAL COMPLEX,SHOP NO.2 & 3,UPPER GROUND FLOOR,SHIVAJI ROAD', 413709, 'Maharashtra', 'Ahmed Nagar', 'Shrirampur', 1, 'shrirampur@ssmobile.com', 'SHRIAMPUR MANAGER', '9860989444', 19.616684, 74.656357, 1, 10, 10, 1, 2, 1, 32, 0, '2020-11-23 19:16:36', 32, '2021-12-07 12:00:53', 2227, 6, 0, 0, '54429', 3087916, 0, 1, 1, 0, 2, 0, 5000, 1, 0, 93, 80, 0, 60000, 50, NULL),
(98, 0, 7, 'REALME Kolhapur', 'REM1', '27AAXCS2330R1ZH', 'C.S.NO.1090, SHOP UNIT NO.27 & 28, GROUND FLOOR,CHATRAPATI SHIVAJI STADIUM, E WARD, SHAHUPURI,KOLHAPUR ', 416001, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'realme.kolhapur1@ssmobile.com', 'REALME MANAGER', '8600666111', 16.6990221, 74.2420614, 1, 12, 12, 2, 5, 1, 32, 0, '2020-11-23 19:18:58', 32, '2021-03-12 12:34:29', 1220, 6, 0, 0, '54429', 9, 0, 1, 1, 0, 1, 0, 5000, 1, 0, 104, 127, 0, 50000000, 365, NULL),
(99, 0, 18, 'THAKUR VILLAGE- kandivali E', 'THAV', '27AAXCS2330R1ZH', 'Shop No. 7 & 8 Shree Ganesh Angan Society, Opp Thakur Collage , Thakur Village, Kandivali East, Mumbai', 400101, 'Maharashtra', 'Mumbai', 'Kandivali East', 1, 'kandivali.east@ssmobile.com', 'Kandivali MANAGER', '7715977187', 19.2073917, 72.8726641, 1, 8, 8, 1, 4, 1, 32, 0, '2020-11-24 15:47:45', 32, '2021-12-07 12:07:29', 3135, 23, 0, 0, '54429', 3152230, 0, 1, 1, 0, 1, 0, 28150, 1, 0, 113, 125, 0, 50000000, 365, NULL),
(100, 0, 18, 'JOGESHWARI 1', 'JOG1', '27AAXCS2330R1ZH', 'Shop No.3 Habib Park Co Op Society Ltd Opp Jogeshwari Railway Station Jogeshwari West Mumbai', 400102, 'Maharashtra', 'Mumbai', 'Jogeshwari West', 1, 'jogeshwari02.west@ssmobile.com', 'MANAGER', '8600666111', 0, 0, 1, 8, 8, 1, 2, 1, 32, 0, '2020-11-27 16:08:00', 32, '2021-12-20 12:33:01', 117, 1, 0, 0, '54429', 3152234, 0, 1, 1, 0, 1, 0, NULL, 1, 0, 0, 0, 0, 50000000, 365, NULL),
(101, 0, 18, 'RAM MANDIR WEST', 'RAMW', '27AAXCS2330R1ZH', 'Shop No.1 & 2 Sairam Apartments, Ram Mandir Road, Goregaon West, Mumbai', 400067, 'Maharashtra', 'Mumbai', 'Kandivali West', 1, 'ram_mandir.west@ssmobile.com', 'MANAGER', '9321679919', 19.1508687, 72.8495777, 1, 8, 8, 1, 2, 1, 32, 0, '2020-11-27 16:10:26', 32, '2021-12-07 12:08:47', 1257, 10, 0, 0, '54429', 3152233, 0, 1, 1, 0, 1, 0, 11450, 1, 0, 115, 122, 0, 50000000, 365, NULL),
(102, 0, 18, 'BHAYANDER WEST', 'BHAW', ' 27AAXCS2330R1ZH', '9 & 10 SAROJ PLAZA, 150 FT. ROAD, NEAR MAXUS MALL BHAYANDER WEST, THANE- 401101', 401101, 'MAHARASHTRA', 'Thane', 'Thane', 1, 'bhayandar.west@ssmobile.com', 'BHAVANDER WEST MANAGER', '9321682678', 19.2949053, 72.848095, 1, 8, 8, 1, 1, 1, 32, 0, '2021-01-14 12:51:10', 32, '2021-12-07 12:07:14', 1611, 17, 0, 0, '54429', 3152229, 0, 1, 1, 0, 1, 0, 2860, 1, 1, 118, 113, 0, 50000000, 365, NULL),
(103, 0, 18, 'CHARKOP - kandivali W', 'CHAR', '27AAXCS2330R1ZH', 'Shop No.38 & 44 Gr Floor Kesar Residency C.H.S Ltd Charkop Kandivali West MUMBAI- 400067', 400067, 'MAHARASHTRA', 'Mumbai', 'Kandivlai West', 1, 'kandivali.west@ssmobile.com', 'MANAGER', '9321690901', 19.215355, 72.8291827, 1, 8, 8, 1, 1, 1, 32, 0, '2021-01-14 12:53:37', 32, '2022-02-01 10:33:18', 1885, 10, 0, 0, '54429', 3152231, 0, 1, 1, 0, 1, 0, 18700, 1, 0, 114, 114, 0, 50000000, 365, NULL),
(105, 0, 18, 'JOGESHWARI 2', 'JOG2', '27AAXCS2330R1ZH', 'Shop No.12 Gr Floor Abba Residency Opp Jogeshwari Railway station Jogeshwari West MumbaI', 400102, 'MAHARASHTRA', 'Mumbai', 'Jogeshwari West', 1, 'jogeshwari02.west@ssmobile.com', 'v', '9867887630', 19.1366408, 72.8485454, 1, 8, 8, 1, 2, 1, 32, 0, '2021-01-14 13:03:58', 32, '2021-11-12 19:03:05', 2079, 28, 0, 0, '54429', 3152235, 0, 1, 1, 0, 1, 0, 4200, 1, 0, 112, 116, 0, 50000000, 365, NULL),
(106, 0, 18, 'VASAI WEST', 'VASW', '27AAXCS2330R1ZH', 'Shop no - 01, Mukesh Apartment,Station Road Opposite Bassein Catholic Bank , Pandit Dindayal Nagar,Vasai West - ', 401202, 'MAHARASHTRA', 'Thane', 'Bassein', 1, 'vasai.west@ssmobile.com', '1', '9307603807', 19.3794703, 72.8281505, 1, 8, 8, 1, 2, 1, 32, 0, '2021-01-14 13:06:29', 32, '2021-12-07 12:09:49', 927, 8, 0, 0, '54429', 3152239, 0, 1, 1, 0, 1, 0, 10450, 1, 0, 116, 123, 0, 50000000, 365, NULL),
(107, 0, 18, 'VIRAR WEST', 'VIRW', '27AAXCS2330R1ZH', 'SHOP NO.3 & 4 GAURI BHAVAN, PARIJAT CO. OP. HSG SO, GAOTHAN, VIRAR (W) THANE-401303', 401303, 'MAHARASHTRA', 'Thane', 'Vasai', 1, 'virar.west@ssmobile.com', '1', '7499213336', 19.4528042, 72.8114651, 1, 8, 8, 1, 2, 1, 32, 0, '2021-01-14 13:09:43', 32, '2021-12-07 12:09:29', 1875, 6, 0, 0, '54429', 3152236, 0, 1, 1, 0, 1, 0, 7000, 1, 0, 117, 124, 0, 50000000, 365, NULL),
(108, 0, 7, 'ISLAMPUR 3', 'ISL3', '27AAXCS2330R1ZH', 'Appo  S T Stand \r\nSidhanath Sankul ,Falle Building \r\nTal. Valva\r\nDist. Sangli 415409', 415409, 'Maharashtra', 'Sangli', 'Walva', 1, 'islampur3@ssmobile.com', 'Islampur 3 Manager', '8888997333', 17.0453295, 74.2608708, 1, 16, 16, 1, 1, 1, 32, 0, '2021-02-20 15:39:11', 32, '2021-12-07 11:26:35', 4005, 27, 0, 0, '54429', 3386120, 0, 1, 1, 0, 2, 0, 0, 1, 0, 120, 131, 0, 100000, 50, NULL),
(110, 0, 7, 'SANGAMNER 2', 'SAG2', '27AAXCS2330R1ZH', 'Next to Laxmikamal Gas Agency,Opp. HDFC Bank.Link Road Sangamner.\r\n', 422605, 'Maharashtra', 'Ahmed Nagar', 'Sangamner', 1, 'sangamner2@ssmobile.com', 'N', '8623018699 ', 19.5708781, 74.2104134, 1, 10, 10, 1, 2, 1, 32, 0, '2021-02-23 15:19:41', 32, '2022-02-02 16:13:00', 1643, 1, 0, 0, '54429', 3386123, 0, 1, 1, 0, 2, 0, 0, 1, 0, 121, 130, 0, 60000, 50, NULL),
(111, 0, 7, 'WAKAD', 'WAKD', '27AAXCS2330R1ZH', 'E building shop no 1/2\r\nG O Square Menkar Chowk\r\nWaked pune ', 411057, 'Maharashtra', 'Pune', 'Pune City', 1, 'wakad@ssmobile.com', 'Store manager', '7058700546', 18.5903564, 73.7708461, 1, 9, 9, 1, 2, 1, 32, 0, '2021-02-27 11:59:55', 32, '2021-12-22 11:31:12', 1852, 9, 0, 0, '54429', 3386122, 0, 1, 1, 0, 2, 0, 0, 1, 0, 122, 132, 0, 0, 0, 1287),
(112, 0, 7, 'MANGALWEDHA', 'MAGW', '27AAXCS2330R1ZH', '295 Damaji chowk,near S T stand.Mangalwedha', 413305, 'Maharashtra', 'Solapur', 'Mangalvedha', 1, 'mangalwedha@ssmobile.com', 'Manager', '9112353516', 17.515361, 75.447815, 1, 4, 4, 1, 5, 1, 32, 0, '2021-03-05 10:47:34', 32, '2021-12-07 11:55:13', 782, 2, 0, 0, '54429', 3386121, 0, 1, 1, 0, 2, 0, 0, 1, 0, 123, 133, 0, 0, 0, 1288),
(113, 0, 142, 'SINNAR', 'SINA', '27AAXCS2330R1ZH', 'Opp. Mahatma Phule Statue, Near sinnar ST Stand, Sinnar, Nashik - 422103', 422103, 'Maharashtra', 'Nashik', 'Sinnar', 1, 'sinnar@ssmobile.com', 'Manager', '8087054003', 19.8433815, 73.9946885, 1, 7, 7, 1, 2, 1, 32, 0, '2021-03-30 16:24:33', 32, '2021-12-07 12:06:07', 2074, 2, 0, 0, '54429', 3422694, 0, 1, 1, 0, 2, 0, 0, 1, 0, 125, 135, 0, 60000, 50, NULL),
(114, 0, 7, 'SHIVAJI ROAD', 'SHIV', '27AAXCS2330R1ZH', 'PADMA TAWLKIES, BINDU CHOWK KOLHAPUR.', 416012, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'shivajiroad.kop@ssmobile.com', 'Binduchowk Manager', '8329326600', 0, 0, 1, 1, 1, 1, 2, 1, 32, 0, '2021-04-06 12:33:05', 32, '2021-12-07 11:20:19', 1896, 7, 0, 0, '54429', 3422695, 0, 1, 1, 0, 2, 0, 0, 1, 0, 124, 0, 0, 60000, 50, NULL),
(115, 0, 7, 'KOPARGAON', 'KOPG', '27AAXCS2330R1ZH', 'C.T.S.NO.2061, K CITY CENTER, SHOP NO. 4 & 5, GROUND FLOOR, KOPERGAO, TAL - KOPERGAO, DIST - AHEMADNAGAR - 423601', 423601, 'Maharashtra', 'Ahmed Nagar', 'Kopergaon', 1, 'kopargaon@ssmobile.com', 'MANAGER', '9021227001', 19.8833318, 74.4759088, 1, 10, 10, 1, 2, 1, 32, 0, '2021-06-09 15:04:45', 32, '2022-02-08 17:41:11', 1449, 4, 0, 0, '54429', 3422693, 0, 1, 1, 0, 2, 0, 0, 1, 1, 126, 136, 0, 60000, 50, NULL),
(116, 0, 7, 'ISLAMPUR 4', 'ISL4', '27AAXCS2330R1ZH', 'Shop no 4&5 mankeshwar plaza,Peth Sangli Road, Ap Islampur', 415409, 'Maharashtra', 'Sangli', 'Walva', 1, 'islampur4@ssmobile.com', 'Manager', '7276286003', 17.0469077, 74.2579746, 1, 16, 16, 1, 2, 1, 32, 0, '2021-06-28 11:23:23', 32, '2021-12-07 11:27:05', 1978, 13, 0, 0, '54429', 3471233, 1, 1, 1, 0, 2, 0, 0, 1, 1, 127, 134, NULL, 100000, 50, NULL),
(117, 0, 7, 'KARAD 4', 'KAR4', '27AAXCS2330R1ZH', 'CHAWADI CHOWK, NEAR SAMSUNG CAFÉ, GURUWAR PETH, KARAD - 415110', 415110, 'Maharashtra', 'Satara', 'Karad', 1, '0', 'manager', '7030296767', 0, 0, 1, 16, 16, 1, 2, 1, 32, 0, '2021-07-01 11:56:32', 32, '2022-02-08 17:38:58', 1054, 4, 0, 0, '54429', 3471234, 0, 1, 1, 0, 2, 0, 0, 1, 0, 128, 0, NULL, 60000, 50, NULL),
(118, 0, 7, 'BAGAL CHOWK', 'BGCK', '27AAXCS2330R1ZH', '1064/1, E ward Ameya chembers, Bagal Chouk Klhapur', 416001, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'bagal.chowk@ssmobile.com', 'Irfan Kurane', '9850849394', 0, 0, 1, 1, 1, 1, 2, 1, 32, 0, '2021-07-05 17:08:24', 32, '2021-12-07 11:21:03', 1336, 6, 0, 0, '54429', 3422696, 0, 1, 1, 0, 2, 0, 0, 1, 0, 129, 0, NULL, 60000, 50, NULL),
(120, 0, 7, 'SHEVGAON', 'SEGN', '27AAXCS2330R1ZH', 'Opp Nagnath Bharde Mangal  Karyalay Miri Road  Tal-Shevgaon 414502,Dist Ahmednagar.', 414502, 'Maharashtra', 'Ahmed Nagar', 'Shevgaon', 1, '0', 'Binduchowk Manager', '9921535333', 0, 0, 1, 10, 10, 1, 2, 1, 32, 0, '2021-07-27 22:17:57', 32, '2021-12-07 12:02:07', 1226, 2, 0, 0, '54429', 9, 0, 1, 1, 0, 2, 0, 0, 1, 1, 130, 0, NULL, 60000, 50, NULL),
(121, 0, 142, 'NASHIK 4', 'NAK4', '27AAXCS2330R1ZH', 'Shop No. 7, 8, 9, DEHBURZ Complex, Opp. Lahoti Peteol Pump, Bitco Chowk, Nashik - 422101', 422101, 'Maharashtra', 'Nashik', 'Nashik', 1, 'nashik4@ssmobile.com', 'NASHIK 4 MANAGER', '8087054009', 1, 1, 1, 7, 7, 1, 2, 1, 32, 0, '2021-08-18 13:59:38', 32, '2021-12-22 11:30:43', 866, 4, 0, 0, '54429', 3471235, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0, 0, NULL, 100000, 50, NULL);
INSERT INTO `branch` (`id_branch`, `is_warehouse`, `idwarehouse`, `branch_name`, `branch_code`, `branch_gstno`, `branch_address`, `branch_pincode`, `branch_state_name`, `branch_district`, `branch_city`, `idstate`, `branch_email`, `branch_contact_person`, `branch_contact`, `latitude`, `longitude`, `idcompany`, `idzone`, `idroute`, `idprinthead`, `idbranchcategory`, `active`, `created_by`, `po_approval`, `branch_timestamp`, `branch_lmb`, `branch_lmt`, `invoice_no`, `sales_return_invoice_no`, `inter_state_sale`, `purchase_invoice`, `bfl_store_id`, `apple_store_id`, `p_direct_billing`, `token_billing`, `online_billing`, `web_billing`, `idpartner_type`, `branch_dc_no`, `petti_cash_balance`, `expense_allowed`, `allow_purchase_direct_inward`, `acc_branch_id`, `hrms_branch_id`, `is_billing`, `credit_limit`, `credit_days`, `idservice_executive`) VALUES
(122, 0, 7, 'BANDA', 'BAND', '27AAXCS2330R1ZH', 'Market road, gandhi chowk,Besides,dr Swar dental clinic,banda,sawantwadi,Sindhudurg,', 416510, 'Maharashtra', 'Sindhudurg', 'Sawantwadi', 1, 'ssmobile.com', 'Manager', '7066854949', 1, 1, 1, 6, 6, 1, 5, 1, 32, 0, '2021-08-18 14:16:19', 32, '2021-12-07 11:51:15', 322, 1, 0, 0, '54429', 3509017, 0, 1, 1, 0, 2, 0, 0, 1, 0, 0, 0, NULL, 30000, 50, NULL),
(123, 0, 7, 'RAHURI FACTORY', 'RAFA', '27AAXCS2330R1ZH', 'Lodha gadi wale,Near Jayashri Hotel.', 413706, 'Maharashtra', 'Ahmed Nagar', 'Rahuri', 1, 'rahuri.factory@ssmobile.com', 'Manager', '9970272828', 1, 1, 1, 10, 10, 1, 7, 1, 32, 0, '2021-09-01 15:30:24', 32, '2021-12-21 15:19:18', 677, 0, 0, 0, '54429', 3517945, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(124, 0, 7, 'MOHOL', 'MOHO', '27AAXCS2330R1ZH', 'Shop No 6, Nagar Parished Shopping complex \r\nMain Road mohol.', 413213, 'Maharashtra', 'Solapur', 'Mohol', 1, 'mohol@ssmobile.com', 'Manager', '8552987777', 1, 1, 1, 4, 4, 1, 7, 1, 32, 0, '2021-09-01 15:54:44', 32, '2021-12-21 15:18:07', 859, 2, 0, 0, '54429', 3529773, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 11, NULL, 30000, 59, 1288),
(125, 0, 7, 'BAMBAVADE', 'BAMB', '27AAXCS2330R1ZH', 'Shop no.1,Opp. S.T.Stand,Narkar Complex, Bambavde, Tal-Shahuwadi,Kolhapur-416213', 416213, 'Maharashtra', 'Kolhapur', 'Shahuwadi', 1, 'bambavade@ssmobile.com', 'Manager', '9359420002', 1, 1, 1, 1, 1, 1, 7, 1, 32, 0, '2021-09-08 10:51:40', 32, '2021-12-21 15:17:04', 878, 2, 0, 0, '54429', 3509018, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(126, 0, 7, 'UMBRAJ', 'UMBR', '27AAXCS2330R1ZH', 'MATOSHRI BILDING,SARVE NO-200/2,JUNA POST ROD SHRI-KRUSHN MANDIR JAVL  UMBRAJ', 415109, 'Maharashtra', 'Satara', 'Karad', 1, 'umbraj@ssmobile.com', 'Manager', '9583937777', 1, 1, 1, 16, 16, 1, 5, 1, 32, 0, '2021-09-14 15:11:59', 32, '2022-02-02 16:14:35', 374, 1, 0, 0, '54429', 3509022, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(127, 0, 7, 'GARGOTI', 'GARO', '27AAXCS2330R1ZH', 'OPP.BARDESKAR PETROL PUMP, GARGOTI, TAL. - GARGOTI, DIST.- KOLHAPUR.', 416209, 'Maharashtra', 'Kolhapur', 'Bhudargad', 1, 'gargoti@ssmobile.com', 'Manager', '8237387451', 1, 1, 1, 13, 13, 1, 5, 1, 32, 0, '2021-09-16 16:39:11', 32, '2022-02-01 10:38:07', 374, 2, 0, 0, '54429', 3509020, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(128, 0, 7, 'SOLAPUR 5', 'SOL5', '27AAXCS2330R1ZH', 'Shop No. 2 & 3 Rukmini Shoping Complex, 7717/1 B, St.Serve No. 51/8B, North Sadar Bazar, Solapur - 413006', 413006, 'Maharashtra', 'Solapur', 'North  Solapur', 1, 'solapur5@ssmovile.com', 'Manager', '8459962513', 1, 1, 1, 4, 4, 1, 2, 1, 32, 0, '2021-09-16 16:42:42', 32, '2022-02-08 17:36:49', 826, 4, 0, 0, '54429', 3517943, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 120000, 30, 1288),
(129, 0, 7, 'PANDHARPUR 2', 'PAN2', '27AAXCS2330R1ZH', 'PANDHARPUR', 413304, 'Maharashtra', 'Solapur', 'Pandharpur', 1, 'pandharpur2@ssmobile.com', 'Manager', '8600666111', 1, 1, 1, 4, 4, 1, 2, 0, 32, 0, '2021-09-16 16:45:04', 32, '2022-02-01 11:43:47', 2, 0, 0, 0, '54429', 3509021, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 60000, 50, 1288),
(130, 0, 7, 'MHASWAD', 'MSWD', '27AAXCS2330R1ZH', 'Jagtap - Khasbage Heights,Satara - Pandhrpur Road,Near Chandani Chowk,Mhaswd,Tal-Man,Dist-Satara - 415509', 415509, 'Maharashtra', 'Satara', 'Man', 1, 'mhaswad@ssmoile.com', 'Manager', '9403067628', 1, 1, 1, 5, 5, 1, 5, 1, 32, 0, '2021-09-16 16:48:57', 32, '2022-02-08 17:37:24', 391, 3, 0, 0, '54429', 3509026, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(131, 0, 7, 'MALVAN', 'MAVN', '27AAXCS2330R1ZH', 'Shop No. 543/3 , Bharad Naka , Malvan ,  Dist. Sindhudurg - 416606', 416606, 'Maharashtra', 'Sindhudurg', 'Malvan', 1, 'malvan@ssmobile.com', 'Manager', '8468988280', 1, 1, 1, 6, 5, 1, 7, 1, 32, 0, '2021-09-16 16:56:31', 32, '2022-02-08 17:37:56', 816, 3, 0, 0, '54429', 3509042, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(132, 0, 142, 'NIPHAD', 'NIPH', '27AAXCS2330R1ZH', 'Gat No. 361, Milkat No 1571/A, GROUND FLOOR, SHOP NO. 1, A/p Niphad, Ugaon Road, Tal. Niphad. Dist Nashik - 422303', 422303, 'Maharashtra', 'Nashik', 'Niphad', 1, 'niphad@ssmobile.com', 'Manager', '8087052093', 1, 1, 1, 7, 7, 1, 2, 1, 32, 0, '2021-09-16 17:00:46', 32, '2022-02-08 17:38:26', 455, 2, 0, 0, '54429', 3517942, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 60000, 50, NULL),
(133, 0, 7, 'CHIKHALI', 'CHIK', '27AAXCS2330R1ZH', 'Shop No.1 & 2 Dehu Alandi Road, Chikhali Gav, Near Corporator Kundan Gaikwad Office Chikhali Pune.', 412062, 'Maharashtra', 'Pune', 'Pune City', 1, 'chikhali@ssmobile.com', 'Manager', '7770008989', 1, 1, 1, 9, 9, 1, 2, 1, 32, 0, '2021-09-16 17:04:08', 32, '2021-12-07 11:32:34', 820, 0, 0, 0, '54429', 3509019, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 0, NULL, 60000, 50, 1287),
(134, 0, 7, 'NARAYANGAON', 'NARA', '27AAXCS2330R1ZH', 'NARAYANGAON', 410504, 'Maharashtra', 'Pune', 'Junnar', 1, 'narayangaon@ssmobile.com', 'Manager', '7738614555', 1, 1, 1, 9, 9, 1, 7, 1, 32, 0, '2021-09-16 17:10:32', 32, '2021-12-21 15:18:51', 416, 0, 0, 0, '54429', 3509025, 0, 1, 1, 0, 2, 0, 0, 1, 0, 6, 6, NULL, 30000, 50, NULL),
(135, 0, 7, 'ASHTA', 'ASTA', '27AAXCS2330R1ZH', 'ASHTA', 466116, 'Maharashtra', 'SANGLI', 'Ashta', 1, 'ashta@ssmobile.com', 'Manager', '8600666111', 1, 1, 1, 14, 14, 1, 7, 1, 32, 0, '2021-09-16 17:13:23', 32, '2021-12-21 15:19:37', 206, 0, 0, 0, '54429', 3529778, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 30000, 50, NULL),
(136, 0, 142, 'AURANGABAD 1', 'AUR1', '27AAXCS2330R1ZH', 'Block No. 4 , ATC Cannaught Place, Town Center, Cidco, Aurangabad - 431001.', 431001, 'Maharashtra', 'Aurangabad', 'Aurangabad', 1, 'aurangabad1@ssmobile.com', 'Managar', '8087052094', 1, 1, 1, 17, 17, 1, 1, 1, 32, 0, '2021-09-16 17:19:50', 32, '2021-12-22 11:32:47', 827, 1, 0, 0, '54429', 3509023, 0, 1, 1, 0, 1, 0, 0, 1, 0, 2, 2, NULL, 50000000, 365, NULL),
(137, 0, 142, 'AURANGABAD 2', 'AUR2', '27AAXCS2330R1ZH', 'Shop No. 3 ,City Marvel, Nirala Bazar, Opposite City Bank, Aurangabad 431001.', 431005, 'Maharashtra', 'Aurangabad', 'Aurangabad', 1, 'aurangabad2@ssmobile.com', 'Manager', '8087052095', 1, 1, 1, 17, 17, 1, 1, 1, 32, 0, '2021-09-16 17:21:09', 32, '2021-12-22 11:32:57', 824, 0, 0, 0, '54429', 3509024, 0, 1, 1, 0, 2, 0, 0, 1, 1, 2, 5, NULL, 50000000, 365, NULL),
(138, 0, 7, 'GIRISH SALES', 'GISL', '27AAXCS2330R1ZH', 'Trade Center\r\n334-E, Upper Ground Floor, Dattawad Renaissance, Railway Station Road, near Trade Centre, New Shahupuri, Kolhapur, Maharashtra ', 416001, 'Maharashtra', 'Kolhapur', 'Karvir', 1, 'S@gmail.com', 'Manager', '8600666111', 1, 1, 1, 1, 1, 1, 5, 0, 32, 0, '2021-10-05 17:09:47', 32, '2022-02-01 11:44:01', 0, 0, 0, 0, '54429', 9, 0, 1, 1, 0, 1, 0, 0, 1, 1, 1, 1, NULL, 0, 0, NULL),
(140, 0, 7, 'SHIRALA', 'SHI1', '27AAXCS2330R1ZH', 'LAXMI CHOUK, SOMWAR PETH,SHIRALA - 415408', 415408, 'Maharashtra', 'Sangli', 'Shirala', 1, 'shirala@ssmobile.com', 'manager', '9890024082', 1, 1, 1, 16, 16, 1, 2, 1, 32, 0, '2021-10-05 17:32:32', 32, '2022-02-02 16:13:56', 280, 2, 0, 0, '54429', 3517944, 0, 1, 1, 0, 2, 0, 0, 1, 1, 1, 2, NULL, 0, 0, NULL),
(141, 1, 0, 'Ingram Warehouse', 'INGW', '27AAXCS2330R1ZH', 'C/O BRIGHTPOINT INDIA PVT.LTD, NO.D-5,SHREE RAJLXMI LOGISTICS PARK, VADAPE,BHIWANDI,THANE', 421308, 'Maharashtra', 'Thane', 'Bhiwandi', 1, 'ingram@ssmobile.com', 'Ghanshyam', '8408889889', 1, 1, 1, 12, 12, 1, 0, 1, 32, 1, '2021-10-11 17:21:39', 32, '2021-10-11 17:21:39', 0, 0, 0, 0, '54429', 9, 0, 1, 1, 0, NULL, 0, 0, 1, 0, NULL, NULL, NULL, 0, 0, NULL),
(142, 1, 0, 'NASHIK Warehouse', '', '27AAXCS2330R1ZH', 'Untwadi, Shop No.1, Vraj Bhoomi Apartment,\r\nBehind city center mall, near kankriya jwellers\r\nuntwadi Nashik .', 422008, 'Maharashtra', 'Nashik', 'Nashik', NULL, 'nashikwarehouse@ssmobile.com', '8087052423', '8087052423', 1, 1, 1, 12, 12, 1, 0, 1, 32, 1, '2021-10-28 18:20:15', 32, '2022-01-17 13:49:03', 0, 0, 0, 0, '54429', 9, 0, 1, 1, 0, NULL, 0, 0, 1, 0, NULL, NULL, NULL, 0, 0, NULL),
(143, 0, 7, 'KURUNDWAD', 'KURW', '27AAXCS2330R1ZH', 'Navbag road ap mane hospital kurundwad,shirol,kolhapur', 416104, 'Maharashtra', 'Kolhapur', 'Shirol', 1, 'kurundwad@ssmobile.com', 'Manager', '8805289696', 1, 1, 1, 14, 14, 1, 7, 1, 32, 0, '2021-11-27 13:31:30', 32, '2021-12-30 16:13:46', 260, 0, 0, 0, '54429', 3529779, 0, 1, 1, 0, 2, 0, 0, 1, 1, 1, 1, NULL, 0, 0, NULL),
(144, 0, 7, 'LATUR 3', 'LAT3', '27AAXCS2330R1ZH', '1 No Chowk BARSHI Road Tal- LATUR , Dist - LATUR.', 413516, 'Maharashtra', 'Osmanabad', 'NA', 1, 'latur3@ssmobile.com', 'Manager', '7020701295', 1, 1, 1, 15, 15, 1, 2, 1, 32, 0, '2022-01-07 19:00:56', 32, '2022-01-18 13:31:09', 93, 1, 0, 0, '54429', 9, 0, 1, 1, 0, 2, 0, 0, 1, 0, 12, 12, NULL, 30000, 30, NULL),
(145, 0, 7, 'BARAMATI', 'BAM1', '27AAXCS2330R1ZH', 'Shop No.3/4 Pravin Plaza,Cinema Road,Baramati', 413102, 'Maharashtra', 'Pune', 'Baramati', 1, 'baramati@ssmobile.com', '0000000000', '8600666111', 1, 1, 1, 5, 5, 1, 1, 1, 32, 0, '2022-01-18 11:05:32', 32, '2022-02-01 11:44:39', 27, 0, 0, 0, '54429', 9, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 0, 0, NULL),
(147, 0, 7, 'GADHINGLAJ 2', 'GADHINGLAJ', '27AAXCS2330R1ZH', 'GALA NO.1109/1,MAIN ROAD,OPP.MODERN BAKERY, GADHINGLAJ - 416502', 416502, 'Maharashtra', 'Kolhapur', 'Gadhinglaj', 1, 'gadhinglaj2@ssmobile.com', 'Kishore', '8600666111', 0, 0, 1, 13, 13, 1, 2, 1, NULL, 0, '2022-01-21 16:51:30', 32, '2022-02-01 10:37:23', 103, 0, 0, 0, '54429', 9, 0, 1, 1, 0, 2, 0, 0, 1, 0, 154, 0, NULL, 0, 0, NULL),
(148, 0, 7, 'KURUDWADI ', 'KURD', '27AAXCS2330R1ZH', '27 NAVI PETH KURUDWADI , TAL  MADHA , DIST SOLAPUR 413208', 413208, 'Maharashtra', 'Solapur', 'Madha', 1, 'kurudwadi@gmail.com', '8600666111', '123456', 1, 1, 1, 4, 4, 1, 7, 0, NULL, 0, '2022-01-29 11:44:24', 32, '2022-02-01 10:29:18', 0, 0, 0, 0, '54429', 9, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 0, 0, NULL),
(149, 0, 142, 'OZAR', 'OZR1', '27AAXCS2330R1ZH', 'Gat No. 1986/2/C, Saikheda Phata, Ozar (Mig), Tal. Niphad. Dist. Nashik-422007', 422007, 'Maharashtra', 'Nashik', 'Nashik', 1, 'ozar@ssmobile.com', 'Manager', '1', 1, 1, 1, 7, 7, 1, 2, 1, NULL, 0, '2022-01-29 11:44:40', 32, '2022-02-01 10:54:12', 0, 0, 0, 0, '54429', 9, 0, 1, 1, 0, 2, 0, 0, 1, 0, 1, 1, NULL, 0, 0, NULL),
(150, 0, 142, 'SATANA', 'STNA', '27AAXCS2330R1ZH', '2/414, Taharabad Road, Tal. Satana, Dist. Nashik - 423301', 423301, 'Maharashtra', 'Nashik', 'Satana', 1, 'satana@ssmobile.com', '125', '8600666111', 1, 1, 1, 7, 7, 1, 2, 1, NULL, 0, '2022-01-29 11:44:53', 32, '2022-02-01 10:55:58', 0, 0, 0, 0, '54429', 9, 1, 1, 1, 0, 2, 0, 0, 1, 0, 2, 2, NULL, 0, 0, NULL),
(151, 0, 142, 'MALEGAON', 'MGN1', '27AAXCS2330R1ZH', 'Shop No.13,Panjarapol Shopping Center, Near Shivaji Maharaj Putala,Malegaon-423203.', 423203, 'Maharashtra', 'Nashik', 'Malegaon', 1, 'malegaon@ssmobile.com', '125', '123456', 1, 1, 1, 7, 7, 1, 1, 1, NULL, 0, '2022-01-29 11:45:38', 32, '2022-02-01 10:55:13', 0, 0, 0, 0, '54429', 9, 1, 1, 1, 0, 2, 0, 0, 1, 0, 2, 2, NULL, 0, 0, NULL),
(152, 0, 0, 'SHAHUPURI 2', 'SHAHUPURI ', NULL, 'shahupuri basant bahar talkies,ratnakar bang bhandar,kolhapur', 416002, 'Maharashtra', 'Kolhapur', 'Karvir', NULL, NULL, '120', '123456', NULL, NULL, 0, 0, 0, NULL, 2, 0, NULL, 0, '2022-02-05 17:05:25', NULL, '2022-02-05 17:05:25', 0, 0, 0, 0, '54429', 9, 1, 1, 1, 1, 2, 0, 0, 1, 0, NULL, NULL, NULL, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `branch_category`
--

CREATE TABLE `branch_category` (
  `id_branch_category` int(11) NOT NULL,
  `branch_category_name` varchar(100) NOT NULL,
  `active` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `bcategory_timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `bcategory_lmb` int(11) DEFAULT NULL,
  `bcategory_lmt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `branch_category`
--

INSERT INTO `branch_category` (`id_branch_category`, `branch_category_name`, `active`, `created_by`, `bcategory_timestamp`, `bcategory_lmb`, `bcategory_lmt`) VALUES
(1, 'GOLD', 1, 32, '2020-05-17 14:22:16', 32, '2020-12-29 16:42:58'),
(2, 'SILVER', 1, 32, '2020-05-17 14:33:38', 32, '2020-12-29 16:42:50'),
(3, 'DIAMOND', 1, 32, '2020-05-17 14:47:28', 32, '2021-03-06 10:38:14'),
(4, 'PLATINUM', 1, 32, '2020-06-25 16:12:02', 32, '2020-12-29 16:42:25'),
(5, 'SS MINI', 1, 32, '2020-06-25 16:12:18', 32, '2020-12-29 16:42:10'),
(6, 'SIGNATURE', 1, 32, '2020-12-29 16:41:28', 32, '2020-12-29 16:41:28'),
(7, 'Mini Plus', 1, 32, '2021-12-21 15:14:16', 32, '2021-12-21 15:14:16'),
(8, 'Diamond Plus', 1, 32, '2021-12-21 15:14:42', 32, '2021-12-21 15:14:42'),
(9, 'Signature Plus', 1, 32, '2021-12-21 15:15:34', 32, '2021-12-21 15:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `id_zone` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `idwarehouse` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `zone_timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `zone_lmb` int(11) NOT NULL,
  `zone_lmt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`id_zone`, `zone_name`, `idwarehouse`, `active`, `created_by`, `zone_timestamp`, `zone_lmb`, `zone_lmt`) VALUES
(1, 'ZONE 1', 7, 1, 32, '2020-05-17 14:22:16', 32, '2020-12-29 16:20:24'),
(2, 'ZONE 2', 7, 1, 32, '2020-06-25 15:39:05', 32, '2020-12-29 16:20:58'),
(3, 'ZONE 3', 7, 1, 32, '2020-06-25 15:39:58', 32, '2020-12-29 16:21:13'),
(4, 'ZONE 4', 7, 1, 32, '2020-06-25 15:40:16', 32, '2020-12-29 16:21:21'),
(5, 'ZONE 5', 7, 1, 32, '2020-06-25 15:40:32', 32, '2020-12-29 16:21:28'),
(6, 'ZONE 6', 7, 1, 32, '2020-06-25 15:40:43', 32, '2020-12-29 16:21:35'),
(7, 'ZONE 7', 7, 1, 32, '2020-06-25 15:41:15', 32, '2020-12-29 16:21:42'),
(8, 'ZONE 8', 7, 1, 32, '2020-06-25 15:41:35', 32, '2020-12-29 16:21:48'),
(9, 'ZONE 9', 18, 1, 32, '2020-11-23 17:10:07', 32, '2020-12-29 16:21:54'),
(10, 'ZONE 10', 7, 1, 32, '2020-12-29 16:22:11', 32, '2020-12-29 16:22:11'),
(11, 'ZONE 11', 7, 1, 32, '2021-03-03 19:01:50', 32, '2021-03-03 19:01:50'),
(12, 'OTHER', 7, 1, 32, '2021-03-12 12:33:33', 32, '2021-03-12 12:33:33'),
(13, 'ZONE 12', 7, 1, 32, '2021-03-15 16:32:05', 32, '2021-03-15 16:32:05'),
(14, 'ZONE 13', 7, 1, 32, '2021-03-15 16:32:13', 32, '2021-03-15 16:32:13'),
(15, 'ZONE 14', 7, 1, 32, '2021-03-15 16:32:20', 32, '2021-03-15 16:32:20'),
(16, 'ZONE 15', 7, 1, 32, '2021-03-15 16:32:26', 32, '2021-03-15 16:32:26'),
(17, 'ZONE 16', 7, 1, 32, '2021-09-16 17:17:07', 32, '2021-09-16 17:17:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`id_branch`),
  ADD UNIQUE KEY `branch_code` (`branch_code`),
  ADD KEY `branch_ibfk_1` (`idstate`);

--
-- Indexes for table `branch_category`
--
ALTER TABLE `branch_category`
  ADD PRIMARY KEY (`id_branch_category`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`id_zone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `id_branch` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `branch_category`
--
ALTER TABLE `branch_category`
  MODIFY `id_branch_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `id_zone` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branch`
--
ALTER TABLE `branch`
  ADD CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`idstate`) REFERENCES `state` (`id_state`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
