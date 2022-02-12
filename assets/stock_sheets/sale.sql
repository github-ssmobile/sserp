-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2022 at 06:15 AM
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
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `id_sale` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `inv_no` varchar(45) DEFAULT NULL,
  `customer_fname` varchar(200) DEFAULT NULL,
  `customer_lname` varchar(200) DEFAULT NULL,
  `customer_contact` varchar(45) DEFAULT NULL,
  `idcustomer` int(11) DEFAULT NULL,
  `customer_gst` varchar(45) DEFAULT NULL,
  `customer_address` varchar(200) DEFAULT NULL,
  `customer_pincode` int(11) DEFAULT NULL,
  `customer_idstate` int(11) DEFAULT '1',
  `idsalesperson` int(11) DEFAULT NULL,
  `basic_total` double DEFAULT '0',
  `discount_total` double DEFAULT '0',
  `final_total` double DEFAULT '0',
  `idbranch` int(11) NOT NULL DEFAULT '1',
  `corporate_sale` int(11) NOT NULL DEFAULT '0' COMMENT '0=Branch,1=Corporate',
  `gst_type` int(11) NOT NULL COMMENT '0=cgst&sgst, 1=igst',
  `remark` varchar(200) DEFAULT NULL,
  `entry_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `sales_return_by` int(11) DEFAULT NULL,
  `sales_return_date` datetime DEFAULT NULL,
  `sales_return_invid` varchar(45) DEFAULT NULL,
  `sales_return_type` int(11) DEFAULT '0' COMMENT '0=Not Returned,1=Cash,2=Replace,3=DOA Return',
  `dcprint` int(11) NOT NULL DEFAULT '0' COMMENT '0=Invoice,1=Delivery Challan ',
  `idadvance_payment_receive` int(11) DEFAULT NULL,
  `token_uid` varchar(50) DEFAULT NULL,
  `bfl_upload` int(11) DEFAULT NULL,
  `idsaletoken` int(11) DEFAULT NULL COMMENT '0=Direct,NULL=Normal,id=Token'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`id_sale`, `date`, `inv_no`, `customer_fname`, `customer_lname`, `customer_contact`, `idcustomer`, `customer_gst`, `customer_address`, `customer_pincode`, `customer_idstate`, `idsalesperson`, `basic_total`, `discount_total`, `final_total`, `idbranch`, `corporate_sale`, `gst_type`, `remark`, `entry_time`, `created_by`, `sales_return_by`, `sales_return_date`, `sales_return_invid`, `sales_return_type`, `dcprint`, `idadvance_payment_receive`, `token_uid`, `bfl_upload`, `idsaletoken`) VALUES
(364512, '2022-01-26', '21-22/SAT3/02424', 'YOGESH GHADGE', '', '9763890742', 1297781, '', '', 0, 1, 959, 2100, 150, 1950, 61, 0, 0, '', '2022-01-26 12:23:56', 613, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182115),
(364637, '2022-01-26', '21-22/SHAH/12360', 'YASIN MUJAWAR', '', '9421173033', 1295261, '', '', 0, 1, 150, 14000, 0, 14000, 63, 0, 0, '', '2022-01-26 12:53:26', 64, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182203),
(364322, '2022-01-26', '21-22/SHAH/12344', 'VISHAL', '', '7775037574', 1278895, '', '', 0, 1, 114, 37999, 6999, 31000, 63, 0, 0, '', '2022-01-26 11:47:19', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181979),
(363482, '2022-01-26', '21-22/CHI2/05337', 'VINAY SAKAT', '', '9763471518', 1270294, '', '', 0, 1, 854, 10490, 90, 10400, 12, 0, 0, '', '2022-01-26 08:39:49', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181386),
(363598, '2022-01-26', '21-22/GADHINGLAJ/00013', 'VILAS MORE', '', '8308301333', 1267546, '', '', 0, 1, 2605, 25990, 2190, 23800, 147, 0, 0, '', '2022-01-26 09:08:01', 2603, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181351),
(364128, '2022-01-26', '21-22/JAYS/03524', 'VILAS', '', '9422752511', 1266934, '', '', 0, 1, 305, 20999, 149, 20850, 23, 0, 0, '', '2022-01-26 11:06:17', 251, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364048, '2022-01-26', '21-22/SHAH/12333', 'VIKAS MOHITE', '', '9011727189', 1264140, '', '', 0, 1, 1446, 8200, 200, 8000, 63, 0, 0, '', '2022-01-26 10:48:23', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181792),
(363408, '2022-01-26', '21-22/SONG/03034', 'VIJAY INGOLE', '', '9766238937', 1259026, '', '', 0, 1, 2366, 13200, 1400, 11800, 58, 0, 0, '', '2022-01-26 08:24:49', 347, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181326),
(364273, '2022-01-26', '21-22/ISL3/03847', 'VASANT CHAUDHARI', '', '8208781250', 1252519, '', '', 0, 1, 971, 19990, 1280, 18710, 108, 0, 0, '', '2022-01-26 11:38:08', 963, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364679, '2022-01-26', '21-22/CHI2/05353', 'VAIDEHI NIKAM', '', '9766752288', 1248027, '', '', 0, 1, 854, 13490, 690, 12800, 12, 0, 0, '', '2022-01-26 13:03:55', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182231),
(363809, '2022-01-26', '21-22/KAR4/00960', 'SWAPNIL SHINDE', '', '9850248037', 1222667, '', '', 0, 1, 1374, 1000, 0, 1000, 117, 0, 0, '', '2022-01-26 09:56:00', 1315, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181621),
(364028, '2022-01-26', '21-22/KAR4/00961', 'SWAPNIL SHINDE', '', '9850248037', 1222667, '', '', 0, 1, 1374, 8200, 400, 7800, 117, 0, 0, '', '2022-01-26 10:43:22', 1315, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181772),
(363671, '2022-01-26', '21-22/RAT2/07123', 'suyog', '', '8007577877', 1218758, '', '', 0, 1, 597, 37500, 6500, 31000, 51, 0, 0, '', '2022-01-26 09:24:15', 129, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181508),
(362596, '2022-01-26', '21-22/SHIV/01765', 'SUSHILKUMAR ', '', '9767819889', 1217470, '', '', 0, 1, 1188, 10500, 0, 10500, 114, 0, 0, '', '2022-01-26 05:22:20', 1162, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180082),
(362710, '2022-01-26', '21-22/SHIV/01766', 'SUSHILKUMAR ', '', '9767819889', 1217470, '', '', 0, 1, 1188, 10500, 0, 10500, 114, 0, 0, '', '2022-01-26 05:53:19', 1162, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180084),
(362716, '2022-01-26', '21-22/SHIV/01767', 'SUSHILKUMAR ', '', '9767819889', 1217470, '', '', 0, 1, 1188, 1200, 0, 1200, 114, 0, 0, '', '2022-01-26 05:54:38', 1162, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180092),
(362718, '2022-01-26', '21-22/SHIV/01768', 'SUSHILKUMAR ', '', '9767819889', 1217470, '', '', 0, 1, 1188, 1200, 0, 1200, 114, 0, 0, '', '2022-01-26 05:55:17', 1162, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180095),
(363582, '2022-01-26', '21-22/SHAH/12307', 'SUPRIYA', '', '9503337881', 1204195, '', '', 0, 1, 236, 20300, 1800, 18500, 63, 0, 0, '', '2022-01-26 09:06:02', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181468),
(364276, '2022-01-26', '21-22/PETV/03401', 'SUHAS', '', '7350026200', 1189953, '', '', 0, 1, 200, 30000, 0, 30000, 44, 0, 0, '', '2022-01-26 11:38:14', 232, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363420, '2022-01-26', '21-22/MRJ1/02498', 'SUBAN INAMDAR', '', '9822768388', 1185205, '', '', 0, 1, 338, 29999, 1649, 28350, 36, 0, 0, '', '2022-01-26 08:27:45', 249, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181340),
(363452, '2022-01-26', '21-22/MRJ1/02499', 'SUBAN INAMDAR', '', '9822768388', 1185205, '', '', 0, 1, 338, 1649, 0, 1649, 36, 0, 0, '', '2022-01-26 08:33:24', 249, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181368),
(362669, '2022-01-26', '21-22/REM1/01150', 'SOMESH', '', '8055871759', 1179858, '', '', 0, 1, 2549, 16000, 0, 16000, 98, 0, 0, '', '2022-01-26 05:41:45', 235, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180771),
(364160, '2022-01-26', '21-22/CHI2/05346', 'siddhesh', '', '7507636906', 1173640, '', '', 0, 1, 1514, 73500, 0, 73500, 12, 0, 0, '', '2022-01-26 11:12:09', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181840),
(363489, '2022-01-26', '21-22/AKLJ/03583', 'SIDDHANT KASHID', '', '9172354739', 1173148, '', '', 0, 1, 440, 17499, 0, 17499, 1, 0, 0, '', '2022-01-26 08:42:31', 341, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181384),
(364312, '2022-01-26', '21-22/SHAH/12343', 'SHUBHAM SUTAR', '', '8149232020', 1171060, '', '', 0, 1, 160, 29700, 0, 29700, 63, 0, 0, '', '2022-01-26 11:45:27', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181992),
(363426, '2022-01-26', '21-22/MUDT/02764', 'SHREYASH PATIL', '', '9822316033', 1161375, '', '', 0, 1, 196, 2100, 100, 2000, 39, 0, 0, '', '2022-01-26 08:28:47', 231, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363902, '2022-01-26', '21-22/MIRT/05648', 'SHOHEB', '', '9359009321', 1158925, '', '', 0, 1, 1447, 13990, 540, 13450, 38, 0, 0, '', '2022-01-26 10:17:33', 227, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363127, '2022-01-26', '21-22/NAG1/03351', 'SHELAKE', '', '9881942690', 1145826, '', '', 0, 1, 2344, 31000, 1120, 29880, 40, 0, 0, 'SAMSUNG MOP- 30000 ACC MOP-120(10383608) SAM UPGRED OFFER -5000(PCS40F10DB3F)', '2022-01-26 07:29:34', 264, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181076),
(363140, '2022-01-26', '21-22/NAG1/03352', 'SHELAKE', '', '9881942690', 1145826, '', '', 0, 1, 2344, 1, 0, 1, 40, 0, 0, '', '2022-01-26 07:31:48', 264, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181081),
(364485, '2022-01-26', '21-22/BLJN/02933', 'SHEKHAR', '', '9665085893', 1145397, '', '', 0, 1, 392, 10490, 150, 10340, 8, 0, 0, 'CASH 10490/-WITH ACC CONNECT 150/-', '2022-01-26 12:18:15', 262, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364119, '2022-01-26', '21-22/NAG3/03278', 'SHARIFA SHAIKH', '', '8237601986', 1142836, '', '', 0, 1, 396, 20999, 300, 20699, 42, 0, 0, 'OK DIS FOR POWER BANK ', '2022-01-26 11:02:44', 681, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181794),
(364121, '2022-01-26', '21-22/NAG3/03279', 'SHARIFA SHAIKH', '', '8237601986', 1142836, '', '', 0, 1, 396, 20999, 0, 20999, 42, 0, 0, 'OK ', '2022-01-26 11:04:53', 681, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181795),
(364279, '2022-01-26', '21-22/NAG3/03281', 'SHARIFA SHAIKH', '', '8237601986', 1142836, '', '', 0, 1, 396, 1, 0, 1, 42, 0, 0, '', '2022-01-26 11:38:45', 681, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181929),
(364282, '2022-01-26', '21-22/NAG3/03282', 'SHARIFA SHAIKH', '', '8237601986', 1142836, '', '', 0, 1, 396, 1, 0, 1, 42, 0, 0, '', '2022-01-26 11:38:59', 681, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181936),
(364629, '2022-01-26', '21-22/NAG1/03363', 'SHAILA HADSAL', '', '9130403526', 1136769, '', '', 0, 1, 388, 19990, 150, 19840, 40, 0, 0, '', '2022-01-26 12:52:00', 264, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182004),
(363910, '2022-01-26', '21-22/SAN2/04599', 'SAYALI MOKALE', '', '9156308767', 1130931, '', '', 0, 1, 318, 30000, 0, 30000, 56, 0, 0, '', '2022-01-26 10:19:55', 252, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181525),
(363587, '2022-01-26', '21-22/KUDL/03510', 'SAYALI', '', '9403173359', 1130721, '', '', 0, 1, 769, 12000, 0, 12000, 31, 0, 0, '', '2022-01-26 09:06:29', 79, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181408),
(363915, '2022-01-26', '21-22/MIRT/05649', 'SAWPANIL PATIL', '', '7447571010', 1130453, '', '', 0, 1, 189, 13490, 990, 12500, 38, 0, 0, '', '2022-01-26 10:20:59', 227, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363880, '2022-01-26', '21-22/SHAH/12324', 'SANTYA PATANKAR', '', '8421333810', 1120622, '', '', 0, 1, 149, 21990, 1490, 20500, 63, 0, 0, '', '2022-01-26 10:13:28', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181672),
(362637, '2022-01-26', '21-22/SHI1/00237', 'SANJAY  ', '', '7758897052', 1106207, '', '', 0, 1, 2599, 15490, 790, 14700, 140, 0, 0, '', '2022-01-26 05:32:20', 1544, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180746),
(364188, '2022-01-26', '21-22/GADHINGLAJ/00015', 'SANDIP BHADAVANKAR', '', '9970072019', 1099255, '', '', 0, 1, 2605, 13200, 1200, 12000, 147, 0, 0, '', '2022-01-26 11:17:57', 2603, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181876),
(363078, '2022-01-26', '21-22/KAR4/00954', 'SANDESH PATIL', '', '9588623117', 1097259, '', '', 0, 1, 1414, 21990, 491, 21499, 117, 0, 0, '', '2022-01-26 07:18:14', 1315, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181074),
(363423, '2022-01-26', '21-22/SHAH/12298', 'SANDEEP PATIL', '', '9422581472', 1095919, '', '', 0, 1, 1236, 20991, 1490, 19501, 63, 0, 0, '', '2022-01-26 08:28:23', 701, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181344),
(363142, '2022-01-26', '21-22/SONG/03029', 'SAMDHAN GUNGE', '', '9527454335', 1089576, '', '', 0, 1, 469, 29999, 839, 29160, 58, 0, 0, '', '2022-01-26 07:32:25', 347, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181137),
(364659, '2022-01-26', '21-22/SAN3/02995', 'SAIRAAJ PRINTERS', '', '9881257280', 1084437, '', '', 0, 1, 296, 20990, 1266, 19724, 57, 0, 0, '', '2022-01-26 12:58:57', 253, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182215),
(363307, '2022-01-26', '21-22/KAVM/01334', 'SAHEBLAL TAMBOLI', '', '9421129239', 1079819, '', '', 0, 1, 376, 1500, 0, 1500, 29, 0, 0, '', '2022-01-26 08:03:03', 257, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180761),
(363299, '2022-01-26', '21-22/SHAH/12289', 'SAGAR', '', '8692836938', 1071343, '', '', 0, 1, 160, 6435, 0, 6435, 63, 0, 0, '', '2022-01-26 08:00:44', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181242),
(364308, '2022-01-26', '21-22/SAT1/04228', 'SACHIN THAKANAIK', '', '8605406001', 1066705, '', '', 0, 1, 809, 21990, 1790, 20200, 59, 0, 0, '', '2022-01-26 11:45:02', 611, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181961),
(364311, '2022-01-26', '21-22/SAT1/04229', 'SACHIN THAKANAIK', '', '8605406001', 1066705, '', '', 0, 1, 809, 21990, 1790, 20200, 59, 0, 0, '', '2022-01-26 11:45:26', 611, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181970),
(362790, '2022-01-26', '21-22/ISL2/02191', 'SACHIN NALAWADE', '', '9657819550', 1065098, '', '', 0, 1, 2588, 2100, 100, 2000, 22, 0, 0, '', '2022-01-26 06:14:45', 606, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180859),
(362907, '2022-01-26', '21-22/CHI2/05331', 'RUSHIKESH', '', '9011670854', 1052309, '', '', 0, 1, 853, 1500, 0, 1500, 12, 0, 0, '', '2022-01-26 06:41:29', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180950),
(363675, '2022-01-26', '21-22/RAT3/02133', 'ROHIT RAJENDRA JOSHI', '', '8329524054', 1047095, '', '', 0, 1, 562, 23999, 999, 23000, 52, 0, 0, '', '2022-01-26 09:25:03', 442, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181380),
(363679, '2022-01-26', '21-22/RAT3/02134', 'ROHIT RAJENDRA JOSHI', '', '8329524054', 1047095, '', '', 0, 1, 562, 1, 0, 1, 52, 0, 0, '', '2022-01-26 09:25:22', 442, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181383),
(363564, '2022-01-26', '21-22/KAGL/03607', 'ROHIT PALANGE', '', '9975245340', 1046719, '', '', 0, 1, 187, 21051, 1451, 19600, 25, 0, 0, '', '2022-01-26 09:02:06', 230, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181418),
(364416, '2022-01-26', '21-22/SAT1/04232', 'ROHIT', '', '9145452242', 1044113, '', '', 0, 1, 2543, 15490, 490, 15000, 59, 0, 0, '', '2022-01-26 12:02:57', 611, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182056),
(363816, '2022-01-26', '21-22/CHI2/05340', 'RIZWAN MATVANKAR', '', '9665315658', 1040253, '', '', 0, 1, 853, 13990, 140, 13850, 12, 0, 0, '', '2022-01-26 09:57:04', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181616),
(363692, '2022-01-26', '21-22/JATH/01653', 'RITESH MODI', '', '9890695522', 1039228, '', '', 0, 1, 487, 54900, 0, 54900, 77, 0, 0, '', '2022-01-26 09:29:14', 258, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364368, '2022-01-26', '21-22/GADH/02751', 'RAVSAHEB PATIL', '', '9673466656', 1036579, '', '', 0, 1, 171, 4800, 0, 4800, 14, 0, 0, '', '2022-01-26 11:54:57', 228, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182022),
(364047, '2022-01-26', '21-22/RAJ2/05778', 'RAVI', '', '9823946245', 1033083, '', '', 0, 1, 217, 800, 0, 800, 48, 0, 0, '', '2022-01-26 10:48:11', 127, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364054, '2022-01-26', '21-22/RAJ2/05779', 'RAVI', '', '9823946245', 1033083, '', '', 0, 1, 217, 800, 0, 800, 48, 0, 0, '', '2022-01-26 10:49:28', 127, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(362738, '2022-01-26', '21-22/SHIV/01769', 'RAMESH', '', '8530689898', 1024719, '', '', 0, 1, 1188, 8500, 0, 8500, 114, 0, 0, '', '2022-01-26 06:02:24', 1162, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364618, '2022-01-26', '21-22/SOP3/02653', 'RAJESH ', '', '9423421308', 1004848, '', '', 0, 1, 2532, 13990, 290, 13700, 67, 0, 0, '', '2022-01-26 12:48:35', 349, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363818, '2022-01-26', '21-22/GADH/02745', 'Rajendar', '', '7350257479', 1001186, '', '', 0, 1, 171, 25990, 1990, 24000, 14, 0, 0, '', '2022-01-26 09:57:41', 228, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363951, '2022-01-26', '21-22/GADH/02748', 'Rajendar', '', '7350257479', 1001186, '', '', 0, 1, 171, 1, 0, 1, 14, 0, 0, '', '2022-01-26 10:28:46', 228, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363924, '2022-01-26', '21-22/JATH/01655', 'RAJ MULLA', '', '7276211249', 994778, '', '', 0, 1, 487, 20990, 990, 20000, 77, 0, 0, '', '2022-01-26 10:21:55', 258, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363010, '2022-01-26', '21-22/SHAH/12271', 'RAJ IYER', '', '9604188181', 994585, '', '', 0, 1, 153, 34999, 0, 34999, 63, 0, 0, '', '2022-01-26 07:03:59', 64, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181030),
(364643, '2022-01-26', '21-22/SAN1/14240', 'pratap', '', '9822032365', 978817, '', '', 0, 1, 277, 37499, 7499, 30000, 55, 0, 0, '', '2022-01-26 12:55:00', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182206),
(364653, '2022-01-26', '21-22/SAN1/14241', 'pratap', '', '9822032365', 978817, '', '', 0, 1, 277, 1, 0, 1, 55, 0, 0, '', '2022-01-26 12:56:31', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182208),
(363195, '2022-01-26', '21-22/SHAH/12284', 'PRAKASH MANIK', '', '9421200235', 968204, '', '', 0, 1, 154, 2100, 100, 2000, 63, 0, 0, '', '2022-01-26 07:41:49', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181158),
(364417, '2022-01-26', '21-22/KAGL/03621', 'NINAD PATIL', '', '7057057028', 960119, '', '', 0, 1, 187, 23801, 1601, 22200, 25, 0, 0, '', '2022-01-26 12:02:58', 230, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364446, '2022-01-26', '21-22/SAN1/14225', 'BABU VASANT SAKATE', '', '7745809797', 941087, '', '', 0, 1, 1417, 19990, 1190, 18800, 55, 0, 0, '', '2022-01-26 12:10:39', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182071),
(364359, '2022-01-26', '21-22/SOP2/06619', 'B.C.BIRAJDAR', '', '8108377311', 939379, '', '', 0, 1, 491, 10490, 0, 10490, 66, 0, 0, '', '2022-01-26 11:53:47', 130, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364690, '2022-01-26', '21-22/RAJ1/03022', 'ANUJA MALI', '', '9422401427', 931369, '', '', 0, 1, 208, 18490, 990, 17500, 47, 0, 0, '', '2022-01-26 13:07:32', 97, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363354, '2022-01-26', '21-22/GADHINGLAJ/00008', 'AMOL TELI', '', '7743883758', 927571, '', '', 0, 1, 2605, 1900, 0, 1900, 147, 0, 0, '', '2022-01-26 08:13:17', 2603, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181273),
(364715, '2022-01-26', '21-22/SAN3/02997', 'AMIT PAWAR', '', '8850407720', 926659, '', '', 0, 1, 300, 8200, 500, 7700, 57, 0, 0, '', '2022-01-26 13:13:33', 253, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182248),
(364356, '2022-01-26', '21-22/KAVM/01338', 'AKSHAY MASAL', '', '8080952234', 922745, '', '', 0, 1, 376, 13990, 790, 13200, 29, 0, 0, '', '2022-01-26 11:53:33', 257, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181946),
(364708, '2022-01-26', '21-22/CHI2/05354', 'akshay', '', '9421488492', 921974, '', '', 0, 1, 2472, 17490, 990, 16500, 12, 0, 0, '', '2022-01-26 13:11:28', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182256),
(364156, '2022-01-26', '21-22/RAJ1/03013', 'AJIT ', '', '9921458136', 914484, '', '', 0, 1, 206, 10790, 290, 10500, 47, 0, 0, '', '2022-01-26 11:11:23', 97, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(362824, '2022-01-26', '21-22/ISL4/01851', 'ABHISHEK JADHAV', '', '9561145640', 903889, '', '', 0, 1, 1510, 13990, 790, 13200, 116, 0, 0, '', '2022-01-26 06:26:56', 1297, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180881),
(364570, '2022-01-26', '21-22/SAT1/04236', 'ABHISHEK JADHAV', '', '9356047747', 903885, '', '', 0, 1, 809, 13991, 691, 13300, 59, 0, 0, '', '2022-01-26 12:37:18', 611, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182153),
(363506, '2022-01-26', '21-22/SAN1/14160', 'ABHIJIT SURYAVANSHI', '', '8793421999', 902246, '', '', 0, 1, 278, 16300, 0, 16300, 55, 0, 0, '', '2022-01-26 08:49:18', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181405),
(363514, '2022-01-26', '21-22/SAN1/14162', 'ABHIJIT SURYAVANSHI', '', '8793421999', 902246, '', '', 0, 1, 278, 1, 0, 1, 55, 0, 0, '', '2022-01-26 08:50:50', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181406),
(362681, '2022-01-26', '21-22/RAJ2/05732', 'ABHIJEET ', '', '8208494450', 899774, '', '', 0, 1, 213, 9300, 0, 9300, 48, 0, 0, '', '2022-01-26 05:45:12', 127, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180773),
(363323, '2022-01-26', '21-22/RAJ2/05748', 'AARTI', '', '9175167885', 896091, '', '', 0, 1, 210, 30000, 1000, 29000, 48, 0, 0, '', '2022-01-26 08:06:36', 127, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364474, '2022-01-26', '21-22/GADHINGLAJ/00019', 'PRIYANKA BHAPKAR', '', '9922679217', 877977, '', '', 0, 1, 2605, 37500, 7500, 30000, 147, 0, 0, '', '2022-01-26 12:16:13', 2603, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181606),
(364489, '2022-01-26', '21-22/KAGL/03623', 'PRAVIN ', '', '9420317970', 872873, '', '', 0, 1, 185, 10791, 41, 10750, 25, 0, 0, '', '2022-01-26 12:19:36', 230, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363051, '2022-01-26', '21-22/SHAH/12275', 'PRAVIN', '', '9975240044', 872782, '', '', 0, 1, 154, 13991, 991, 13000, 63, 0, 0, '', '2022-01-26 07:12:05', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181045),
(363306, '2022-01-26', '21-22/JAYS/03514', 'PRADNYA CHOUGULE', '', '7738556990', 849699, '', '', 0, 1, 306, 20990, 990, 20000, 23, 0, 0, '', '2022-01-26 08:02:35', 251, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181226),
(364096, '2022-01-26', '21-22/SAN1/14193', 'POPAT KAMBLE', '', '7798501616', 845406, '', '', 0, 1, 279, 3500, 0, 3500, 55, 0, 0, '', '2022-01-26 10:59:06', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181763),
(363973, '2022-01-26', '21-22/SAN1/14184', 'POPAT KAMBLE', '', '7798501616', 845406, '', '', 0, 1, 279, 33000, 1000, 32000, 55, 0, 0, '', '2022-01-26 10:33:30', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181717),
(363490, '2022-01-26', '21-22/GADH/02741', 'OMKAR', '', '9075271023', 820248, '', '', 0, 1, 172, 12200, 0, 12200, 14, 0, 0, '', '2022-01-26 08:42:37', 228, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363285, '2022-01-26', '21-22/RANK/02232', 'OMKAR', '', '8698517682', 820027, '', '', 0, 1, 222, 37499, 7478, 30021, 49, 0, 0, '', '2022-01-26 07:57:47', 242, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181213),
(362630, '2022-01-26', '21-22/ICH3/03729', 'NISHANT ', '', '7798949333', 812930, '', '', 0, 1, 331, 18499, 0, 18499, 19, 0, 0, '', '2022-01-26 05:30:38', 246, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180706),
(362635, '2022-01-26', '21-22/ICH3/03730', 'NISHANT ', '', '7798949333', 812930, '', '', 0, 1, 331, 300, 0, 300, 19, 0, 0, '', '2022-01-26 05:31:27', 246, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180713),
(364219, '2022-01-26', '21-22/SONG/03044', 'NATHA KAMBALE', '', '7744087141', 800208, '', '', 0, 1, 1331, 9200, 0, 9200, 58, 0, 0, '', '2022-01-26 11:26:39', 347, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181920),
(363792, '2022-01-26', '21-22/VIT2/02562', 'NARAYAN JADHAV', '', '9970551730', 798619, '', '', 0, 1, 370, 13990, 890, 13100, 70, 0, 0, '', '2022-01-26 09:51:11', 256, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181577),
(364719, '2022-01-26', '21-22/SAN1/14243', 'MRUDULA BHAMBURE', '', '8421278459', 788155, '', '', 0, 1, 314, 13990, 990, 13000, 55, 0, 0, '', '2022-01-26 13:14:03', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182264),
(362609, '2022-01-26', '21-22/ICH3/03728', 'MEGHA SHINDE', '', '9021607793', 539926, '', '', 0, 1, 327, 1900, 0, 1900, 19, 0, 0, '', '2022-01-26 05:25:18', 246, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180723),
(364295, '2022-01-26', '21-22/SAT3/02419', 'manoj kamble ', '', '9665203702', 533432, '', '', 0, 1, 959, 2100, 100, 2000, 61, 0, 0, '', '2022-01-26 11:40:52', 613, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181968),
(362592, '2022-01-26', '21-22/MRJ1/02488', 'KORE', '', '8624871515', 499410, '', '', 0, 1, 337, 16490, 990, 15500, 36, 0, 0, '', '2022-01-26 05:20:46', 249, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180674),
(362918, '2022-01-26', '21-22/RAT1/04153', 'KISAN GOSWAMI', '', '9325195045', 495931, '', '', 0, 1, 1385, 13200, 550, 12650, 50, 0, 0, '', '2022-01-26 06:43:46', 443, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180959),
(362924, '2022-01-26', '21-22/BICW/02753', 'KAPDE', '', '9860002514', 483493, '', '', 0, 1, 164, 13991, 1191, 12800, 10, 0, 0, '', '2022-01-26 06:45:43', 226, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180907),
(363193, '2022-01-26', '21-22/SOP2/06602', 'JAGDISH', '', '9028544015', 465902, '', '', 0, 1, 2513, 8200, 200, 8000, 66, 0, 0, '', '2022-01-26 07:41:38', 130, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181171),
(364663, '2022-01-26', '21-22/KAN2/03196', 'IMRN KHAN', '', '9930505515', 460690, '', '', 0, 1, 1267, 12990, 640, 12350, 78, 0, 0, '', '2022-01-26 12:59:41', 439, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182223),
(362659, '2022-01-26', '21-22/VIT1/01771', 'HINGSE ', '', '9822765343', 458372, '', '', 0, 1, 353, 18200, 0, 18200, 69, 0, 0, '', '2022-01-26 05:39:20', 255, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180759),
(364215, '2022-01-26', '21-22/GADHINGLAJ/00016', 'GOKUL', '', '9860673872', 445298, '', '', 0, 1, 2605, 19200, 210, 18990, 147, 0, 0, '', '2022-01-26 11:25:49', 2603, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181461),
(362882, '2022-01-26', '21-22/RAT2/07111', 'ghag', '', '8484981117', 443386, '', '', 0, 1, 597, 15499, 499, 15000, 51, 0, 0, '', '2022-01-26 06:36:02', 129, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180931),
(363243, '2022-01-26', '21-22/SAG2/01585', 'GANESH GORHE', '', '8975907374', 437547, '', '', 0, 1, 1235, 13200, 1030, 12170, 110, 0, 0, '1% SWIPE CHARGES RECEIVED', '2022-01-26 07:50:20', 994, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181193),
(362632, '2022-01-26', '21-22/SHAH/12252', 'Ganesh', '', '9850111530', 435872, '', '', 0, 1, 160, 27500, 0, 27500, 63, 0, 0, '', '2022-01-26 05:30:43', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180737),
(364431, '2022-01-26', '21-22/MIRT/05658', 'FRENANDES', '', '7798134353', 431474, '', '', 0, 1, 1447, 1500, 0, 1500, 38, 0, 0, '', '2022-01-26 12:06:12', 677, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(363695, '2022-01-26', '21-22/NAG3/03274', 'FIROJ KHAN', '', '8329441744', 431015, '', '', 0, 1, 396, 19200, 910, 18290, 42, 0, 0, 'OK DIS FOR ACC (10462877 ) + 200 BRAND MOP DIS ', '2022-01-26 09:30:40', 681, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181395),
(364364, '2022-01-26', '21-22/MIRT/05657', 'FIROJ', '', '8329986723', 430885, '', '', 0, 1, 194, 18000, 0, 18000, 38, 0, 0, '', '2022-01-26 11:54:46', 227, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(362633, '2022-01-26', '21-22/PAND/04573', 'DR SAM BHAJI BHOSALE', '', '9860285300', 426255, '', '', 0, 1, 801, 13200, 471, 12729, 43, 0, 0, '', '2022-01-26 05:30:46', 76, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180742),
(362638, '2022-01-26', '21-22/PAND/04574', 'DR SAM BHAJI BHOSALE', '', '9860285300', 426255, '', '', 0, 1, 801, 1200, 0, 1200, 43, 0, 0, '', '2022-01-26 05:32:54', 76, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180748),
(362875, '2022-01-26', '21-22/SAN3/02965', 'DEEPAK BELVALKAR', '', '8625031205', 400697, '', '', 0, 1, 301, 20990, 1404, 19586, 57, 0, 0, '', '2022-01-26 06:34:57', 253, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 180928),
(363573, '2022-01-26', '21-22/SOP2/06607', 'CHAVAN', '', '7387304921', 388191, '', '', 0, 1, 2513, 10800, 0, 10800, 66, 0, 0, '', '2022-01-26 09:04:01', 130, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181440),
(363581, '2022-01-26', '21-22/ISL3/03838', 'CHANDRAKANT PATIL', '', '9689403064', 386952, '', '', 0, 1, 967, 10500, 0, 10500, 108, 0, 0, '', '2022-01-26 09:05:27', 963, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181459),
(363256, '2022-01-26', '21-22/PETV/03388', 'CHANDAN SALAPE', '', '8149111114', 385821, '', '', 0, 1, 945, 1600, 0, 1600, 44, 0, 0, '', '2022-01-26 07:53:32', 232, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364526, '2022-01-26', '21-22/RAJ2/05788', 'BHOSLE ', '', '7798924800', 379142, '', '', 0, 1, 219, 16500, 0, 16500, 48, 0, 0, '', '2022-01-26 12:28:06', 127, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364101, '2022-01-26', '21-22/SHAH/12337', 'BALASO KAGALKAR', '', '9404388272', 369576, '', '', 0, 1, 149, 27000, 0, 27000, 63, 0, 0, '', '2022-01-26 11:00:20', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181822),
(364122, '2022-01-26', '21-22/SHAH/12339', 'BALASO KAGALKAR', '', '9404388272', 369576, '', '', 0, 1, 149, 2700, 0, 2700, 63, 0, 0, '', '2022-01-26 11:04:57', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181844),
(363375, '2022-01-26', '21-22/CHI3/02721', 'BAJIRAV', '', '9604251534', 367924, '', '', 0, 1, 886, 1700, 0, 1700, 13, 0, 0, '', '2022-01-26 08:18:44', 446, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181297),
(363859, '2022-01-26', '21-22/SAN1/14180', 'AYAJ KOKANE', '', '9881179912', 363295, '', '', 0, 1, 2473, 16490, 990, 15500, 55, 0, 0, '', '2022-01-26 10:06:43', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181654),
(363990, '2022-01-26', '21-22/PAND/04593', 'APPASAHEB', '', '9860966148', 342581, '', '', 0, 1, 462, 16490, 215, 16275, 43, 0, 0, '', '2022-01-26 10:36:08', 76, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181730),
(363783, '2022-01-26', '21-22/ISL2/02197', 'ANURADHA SUTAR', '', '9765035115', 341652, '', '', 0, 1, 1189, 9500, 0, 9500, 22, 0, 0, '', '2022-01-26 09:48:38', 658, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364450, '2022-01-26', '21-22/SAN1/14226', 'ANITA PAWAR', '', '7875709012', 338108, '', '', 0, 1, 316, 25990, 461, 25529, 55, 0, 0, '', '2022-01-26 12:11:29', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182066),
(364453, '2022-01-26', '21-22/SAN1/14227', 'ANITA PAWAR', '', '7875709012', 338108, '', '', 0, 1, 316, 500, 0, 500, 55, 0, 0, '', '2022-01-26 12:12:12', 128, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182074),
(364334, '2022-01-26', '21-22/RANK/02239', 'ANIL JADHAV', '', '9970922728', 335226, '', '', 0, 1, 221, 2699, 0, 2699, 49, 0, 0, '', '2022-01-26 11:48:49', 242, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182009),
(364285, '2022-01-26', '21-22/RANK/02238', 'ANIL JADHAV', '', '9970922728', 335226, '', '', 0, 1, 221, 29730, 0, 29730, 49, 0, 0, '', '2022-01-26 11:39:19', 242, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181949),
(364678, '2022-01-26', '21-22/KHED/01730', 'ANIKET PATHARE', '', '9730234846', 332296, '', '', 0, 1, 981, 15499, 499, 15000, 82, 0, 0, '', '2022-01-26 13:03:35', 540, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182212),
(364242, '2022-01-26', '21-22/CHI2/05347', 'ANANT CHAVAN', '', '9689617904', 329305, '', '', 0, 1, 853, 65900, 0, 65900, 12, 0, 0, '', '2022-01-26 11:31:31', 445, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(364486, '2022-01-26', '21-22/SANG/04294', 'AMOL KHTAL', '', '9922270001', 324675, '', '', 0, 1, 416, 12990, 526, 12464, 54, 0, 0, '', '2022-01-26 12:18:35', 692, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 182095),
(363985, '2022-01-26', '21-22/SHAH/12329', 'AMOL ', '', '7262942455', 323170, '', '', 0, 1, 236, 29999, 0, 29999, 63, 0, 0, '', '2022-01-26 10:35:09', 225, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, 181652);

--
-- Triggers `sale`
--
DELIMITER $$
CREATE TRIGGER `Delete_sale` BEFORE DELETE ON `sale` FOR EACH ROW insert into deleted_sale select * from sale where sale.id_sale = OLD.id_sale
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`id_sale`),
  ADD KEY `idbranch` (`idbranch`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idstate` (`customer_idstate`),
  ADD KEY `sale_ibfk_3` (`idcustomer`),
  ADD KEY `dt` (`date`),
  ADD KEY `idsaletoken` (`idsaletoken`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `id_sale` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365918;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
