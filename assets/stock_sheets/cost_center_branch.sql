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
-- Table structure for table `cost_center_branch`
--

CREATE TABLE `cost_center_branch` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `branch_address` varchar(200) DEFAULT NULL,
  `branch_category` int(2) DEFAULT NULL,
  `branch_landmark` varchar(100) DEFAULT NULL,
  `branch_pincode` varchar(6) DEFAULT NULL,
  `branch_state` varchar(50) DEFAULT NULL,
  `branch_district` varchar(50) DEFAULT NULL,
  `branch_city` varchar(50) DEFAULT NULL,
  `branch_contact_person` varchar(100) DEFAULT NULL,
  `branch_contact` varchar(10) DEFAULT NULL,
  `branch_status` varchar(1) DEFAULT '0',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `branch_partener_type` int(2) DEFAULT NULL,
  `original_branch_id` int(11) DEFAULT NULL,
  `interior_vendor` int(11) DEFAULT NULL,
  `shopact_doc` varchar(200) NOT NULL,
  `gstcert_doc` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cost_center_branch`
--

INSERT INTO `cost_center_branch` (`branch_id`, `branch_name`, `branch_address`, `branch_category`, `branch_landmark`, `branch_pincode`, `branch_state`, `branch_district`, `branch_city`, `branch_contact_person`, `branch_contact`, `branch_status`, `created_date`, `created_by`, `branch_partener_type`, `original_branch_id`, `interior_vendor`, `shopact_doc`, `gstcert_doc`) VALUES
(122, 'BARAMATI', 'Shop No. 3/4 Pravin Plaza, Cinema Road, Baramati', 1, 'Near Mota Collection', '413102', 'Maharashtra', 'Pune', 'Baramati', '120', NULL, '1', '2022-01-27 08:16:04', 766, 2, 145, 95, '', ''),
(123, 'BALAJI NAGAR', 'C.S.NO.30/1, PLOT NO.9,SHOP NO.1&2, GROUND FLOOR,GURUPRASAD BUILDING,OPP.SBI, BALAJI NAGAR CHOWK,DHANKAWADI, PUNE - 400043   ', 1, NULL, '400043', 'Maharashtra', 'Mumbai', 'NA', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 8, NULL, '', ''),
(124, 'BARSHI', 'C.S.NO.4305, GROUND FLOOR, SOMWAR PETH, BARSHI - 413401  ', 2, NULL, '413401', 'Maharashtra', 'Solapur', 'Barshi', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 9, NULL, '', ''),
(125, 'BINDU CHOWK', 'BUSINESS POINT COMPLEX, 1555/3, C WARD, SHIVAJI ROAD, NR.PADMA TAWLKIES, BINDU CHOWK KOLHAPUR.- 416012  ', 1, NULL, '416012', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 10, NULL, '', ''),
(126, 'CHIPLUN 1', 'PRIME CENTRE, CHINCH NAKA, OPP.SBI,GALA NO.1, CHIPLUN - 415605 ', 1, NULL, '415605', 'Maharashtra', 'Ratnagiri', 'Chiplun', '32', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 11, NULL, '', ''),
(127, 'CHIPLUN 2', 'GALA NO.34, SUVARNA MANDIR COMPLEX, KARAD ROAD,CHIPLUN - 415605  ', 1, NULL, '415605', 'Maharashtra', 'Ratnagiri', 'Chiplun', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 12, NULL, '', ''),
(128, 'CHIPLUN 3', 'Near Bank Of Maharashtra, AP Kherdi, Chiplun - 415604 ', 2, NULL, '415604', 'Maharashtra', 'Ratnagiri', 'Chiplun', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 13, NULL, '', ''),
(129, 'GADHINGLAJ 1', 'GALA NO.1109/1,MAIN ROAD,OPP.MODERN BAKERY, GADHINGLAJ - 416502', 2, NULL, '416502', 'Maharashtra', 'Kolhapur', 'Gadhinglaj', '120', NULL, '1', '2022-01-25 05:39:06', NULL, 2, 14, NULL, '', ''),
(130, 'GANDHINAGAR', 'M.NO.1611/F-13, GROUND FLOOR, MAIN ROAD, GADMUDSHINGI OCCUPIED GANDHINAGAR - 416122', 1, NULL, '416122', 'Maharashtra', 'Kolhapur', 'Karveer', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 15, NULL, '', ''),
(131, 'HADAPSAR', ' C.S.NO.84/1+2+3A+9+10D/3+4+5, VAIBHAV COMMERCIAL COMPLEX,GROUND FLOOR,SHOP NO.13, HADAPSAR,TAL.- HAVELI,DIST. - PUNE - 411028  ', 2, NULL, '411028', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 16, NULL, '', ''),
(132, 'ICHALKARANJI 1', '5/99, NR.VYANKATRAO HIGH SCHOOL, OPP.DR. BHIDE, ICHALKARANJI - 416115 ', 4, NULL, '416115', 'Maharashtra', 'Kolhapur', 'Hatkanangle', '120', NULL, '1', '2022-01-28 09:04:22', NULL, 1, 17, NULL, '', ''),
(134, 'ICHALKARANJI 3', 'WARD NO.16, PROP.NO.162002035, OLD PROP.NO.16/1537,NEAR BHAGYAREKHA TALKIES, MAIN ROAD,ICHALKARANJI - 416115  ', 1, NULL, '416115', 'Maharashtra', 'Kolhapur', 'Hatkanangle', '120', NULL, '1', '2022-01-28 09:04:30', NULL, 1, 19, NULL, '', ''),
(135, 'INDAPUR', ' PROPERTY No.w7z1003251(old 895/78),1 ST FLOOR, INDAPUR, DIST. - PUNE - 413106   ', 5, NULL, '413106', 'Maharashtra', 'Pune', 'Indapur', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 20, NULL, '', ''),
(136, 'ISLAMPUR 1', 'R.S.NO.72 / 1, SHRI RAM KRISHNA HARI BUILDING, GROUND FLOOR,OPP.PUSHKAR SANSKRITIK BHAVAN, ISLAMPUR, TAL-WALWA, DIST. - SANGLI - 415409  ', 2, NULL, '415409', 'Maharashtra', 'Sangli', 'Walva', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 21, NULL, '', ''),
(137, 'ISLAMPUR 2', 'SHOP NO.2296/172,NR.MANKESHWAR TALKIES,PETH, SANGLI ROAD, ISLAMPUR - 415409  ', 2, NULL, '415409', 'Maharashtra', 'Sangli', 'Walva', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 22, NULL, '', ''),
(138, 'JAYSINGPUR', 'KRANTI CHOWK, NEAR OLD COURT, SANGLI-KOLHAPUR ROAD, JAYSINGPUR.- 416101  ', 1, NULL, '416101', 'Maharashtra', 'Kolhapur', 'Hatkanangale', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 23, NULL, '', ''),
(139, 'J M ROAD', 'CTS NO.418,MITTAL CHAMBERS,SHOP NO.3/4/5, J.M.ROAD, PUNE - 411004 ', 3, NULL, '411004', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 24, NULL, '', ''),
(140, 'KAGAL', 'Shop No. 4/5 ,Ground floor, Near Bhuyekar Petrol Pump, Near Bank Of Maharashtra, Kagal - 416216  ', 2, NULL, '416216', 'Maharashtra', 'Kolhapur', 'Kagal', '120', NULL, '1', '2022-01-28 09:04:49', NULL, 2, 25, NULL, '', ''),
(141, 'KANKAVALI 1', 'S.NO.207 A, H.NO.43, SHOP NO.3, MUDRA, KANKAVALI - 416602 ', 2, NULL, '416602', 'Maharashtra', 'Sindhudurg', 'Kankavli', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 26, NULL, '', ''),
(142, 'KARAD 1', 'PLOT NO.469, B/3, SHANIWAR PETH, KARAD - 415110 ', 4, NULL, '415110', 'Maharashtra', 'Satara', 'Karad', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 27, NULL, '', ''),
(143, 'KARAD 2', '37, INDU COMPLEX,C/O.SHYAM SALES, SHANIWAR PETH , KARAD. - 415110 ', 2, NULL, '415110', 'Maharashtra', 'Satara', 'Karad', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 28, NULL, '', ''),
(144, 'KAWATHE MAHNKAL', 'C.S NO./GAT NO.1418 /1, PLOT NO.132 & 133, GAJANAN PLAZA,GROUND FLOOR, GALA NO.2,KAVATHEMAHANKAL,DIST-SANGLI - 416405  ', 5, NULL, '416405', 'Maharashtra', 'Sangli', 'K Mahankal', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 29, NULL, '', ''),
(145, 'KOREGAON', 'NEAR HUTATMA SMARAK,OPP.STATE BANK OF INDIA, MAIN ROAD,KOREGAON, DIST. - SATARA - 415501  ', 2, NULL, '415501', 'Maharashtra', 'Satara', 'Koregaon', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 30, NULL, '', ''),
(146, 'KUDAL', '3519(10-11), GROUND FLOOR, CHINTAMANI PLAZA, KUDAL, SINDHUDURG - 416520', 1, NULL, '416520', 'Maharashtra', 'Sindhudurg', 'Kudal', '32', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 31, NULL, '', ''),
(147, 'LATUR 1', 'MADHU - MIRA COMPLEX, SHOP NO. 8,9,10,15,16 & 17, SHIVAJI NAGAR, LATUR - 413512 ', 1, NULL, '413512', 'Maharashtra', 'Latur', 'Latur', '122', NULL, '1', '2022-02-02 10:50:33', NULL, 2, 32, NULL, '', ''),
(148, 'LATUR 2', 'KEDAR MOBILE, SHOP NO.1, GANDHI MARKET, CHAIN SUKH ROAD,LATUR - 413512', 1, NULL, '413512', 'Maharashtra', 'Latur', 'Latur', '122', NULL, '1', '2022-02-02 10:50:27', NULL, 2, 33, NULL, '', ''),
(149, 'MANCHAR', 'SHOP NO.1, VITTHAL SMUTI BUILDING,MANCHAR, TAL.-AMBEGAON, DIST.- PUNE   ', 5, NULL, '410503', 'Maharashtra', 'Pune', 'Ambegaon', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 34, NULL, '', ''),
(150, 'MARGAO 1', 'Shop No.G/7 & G/8, Gajanan Commercial Complex, Ground Floor,Opp. Nanutel, Margao, Goa - 403601     ', 4, NULL, '403601', 'Goa', 'South Goa', 'Salcete', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 35, NULL, '', ''),
(151, 'MIRAJ 1', 'C.S.NO.5122 / B, NEAR POLICE STATION, MIRAJ - 416410  ', 2, NULL, '416410', 'Maharashtra', 'Sangli', 'Miraj', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 36, NULL, '', ''),
(152, 'MIRAJ 2', 'C.S.NO.5875(G), UPPER GROUND FLOOR OF SHOPPING CENTRE,SHOP NO.19 & 20, MIRAJ - 416410  ', 2, NULL, '416410', 'Maharashtra', 'Sangli', 'Miraj', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 37, NULL, '', ''),
(153, 'MIRAJKAR TIKATI', 'GALA NO.16,28,15,29, MAHILA SEVA SANKUL, OPP.BALGOPAL TALIM MANDAL, MANGALWAR PETH, KOLHAPUR - 416012 ', 4, NULL, '416012', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 38, NULL, '', ''),
(154, 'MUDHAL TITTA', 'GAT NO.183, UPPER GROUND FLOOR, SHOP NO.1 & 2, MUDHAL TITTA, TAL- KAGAL, DIST. - KOLHAPUR - 416209', 2, NULL, '416209', 'Maharashtra', 'Kolhapur', 'Bhudargad', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 39, NULL, '', ''),
(155, 'NAGAR 1', 'Sai Palace, Shop No. 10-11, Opposite Akashwani, Professor Chowk. Savedi, Ahmednagar - 414003 ', 1, NULL, '414003', 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 40, NULL, '', ''),
(156, 'NAGAR 2', 'SHOP NO.1, CHANDRALOK APPT., DELHI GATE, GUNDU BAZAR , AHMEDNAGAR - 414001 ', 5, NULL, '414001', 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 41, NULL, '', ''),
(157, 'NAGAR 3', 'Plot No.93 out of Plot No.1 & 2,\'NAMOHA COMPOUND\',Ahmednagar - 414001 ', 4, NULL, '414001', 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 42, NULL, '', ''),
(158, 'PANDHARPUR', 'C.S.NO.4041/A 1, WARD NO.7,BHADULE CHOWK, PANDHARPUR - 413304 ', 1, NULL, '413304', 'Maharashtra', 'Solapur', 'Pandharpur', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 43, NULL, '', ''),
(159, 'PETH VADGAON', 'PADMA ROAD, NEAR JAY BHAVANI PATH SANSTHA, PETH VADGAON. - 416112 ', 1, NULL, '416112', 'Maharashtra', 'Kolhapur', 'Hatkanangle', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 44, NULL, '', ''),
(160, 'PHALTAN 1', 'SAI PLAZA , DEd CHOWK RING ROAD , LAXMI NAGAR , PHALTAN DIST. - SATARA - 415015 ', 1, NULL, '415015', 'Maharashtra', 'Satara', 'Satara', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 45, NULL, '', ''),
(161, 'PIMPRI', 'SHOP NO. 315, GROUND FLOOR+1, NEAR SAI MANDIR, SAI CHOWK, PIMPARI-CAMP, PUNE - 411017 ', 3, NULL, '411017', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 46, NULL, '', ''),
(162, 'RAJARAMPURI 1', '2018/KH/20, PRABHAVATI APPT.,4TH LANE, RAJARAMPURI, KOLHAPUR - 416008 ', 1, NULL, '416008', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 47, NULL, '', ''),
(163, 'RAJARAMPURI 2', 'C.S.NO.1115/B/2,SHOP.NO.5,LOWER G.FLR.,\'TATHASTU CORNER\'SHOP NO.5,OPP.RAILWAY GAT NO.2, FIVE BUNGLOW AREA, \'E\' WARD, SHAHUPURI, KOLHAPUR - 416001', 3, NULL, '416001', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '2022-01-28 09:05:23', NULL, 1, 48, NULL, '', ''),
(164, 'RANKALA', 'R.S.NO.1234/3 & 1316/1, SHOP NO.16 A, PLOT NO.1, WATERFRONT, NR.D MART, RANKALA, KOLHAPUR - 416012 ', 2, NULL, '416012', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 49, NULL, '', ''),
(165, 'RATNAGIRI 1', 'S.N.372, B, C.S.NO.175/B, SHOP NO.3, GROUND FLOOR,SAMRAT SHOPPING CENTER,OPP.MARUTI MANDIR, RATNAGIRI ', 1, NULL, '415612', 'Maharashtra', 'Ratnagiri', 'Ratnagiri', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 50, NULL, '', ''),
(166, 'RATNAGIRI 2', 'SHOP NO.15 & 16,NAVKAR PLAZA, MARUTI MANDIR, RATNAGIRI - 415612  ', 4, NULL, '415612', 'Maharashtra', 'Ratnagiri', 'Ratnagiri', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 51, NULL, '', ''),
(167, 'RATNAGIRI 3', 'SANKESHWAR ARCADE, ATHAWADI BAZAR, GROUND FLOOR, SHOP NO.36, RATNAGIRI. ', 5, NULL, '415612', 'Maharashtra', 'Ratnagiri', 'Ratnagiri', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 52, NULL, '', ''),
(168, 'SADASHIV PETH', 'SHOP NO.5,GANESH SADAN, SURVEY NO.1164, SADASHIV PETH, PUNE - 411030. ', 1, NULL, '411030', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 53, NULL, '', ''),
(169, 'SANGAMNER', 'SURVEY NO.151/139/1,RAJPAL CLOTH STORE,SHOP NO.1/2/3 & 4, SANGAMNER, DIST - NAGAR - 422605 ', 1, '', '422605', 'Maharashtra', 'Ahmed Nagar', 'Sangamner', '125', NULL, '1', '2022-01-15 11:53:35', 941, 2, 54, 95, '', ''),
(170, 'SANGLI 1', 'C.S.NO.404/3, SHIV MERIDIAN,GALA NO.12,13,14, KHANBHAG, KUPWAD, SANGLI - 416410 ', 1, NULL, '416410', 'Maharashtra', 'Sangli', 'Miraj', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 55, NULL, '', ''),
(171, 'SANGLI 2', 'B,1 &2, SHIV MERIAN APPT.,M.G. ROAD, SANGLI - 416410 ', 4, NULL, '416410', 'Maharashtra', 'Sangli', 'Miraj', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 56, NULL, '', ''),
(172, 'SANGLI 3', 'C.S.NO.1056/4, SHOP NO. 2 & 3, GAONBHAG, SANGLI - 415410 ', 2, NULL, '415410', 'Maharashtra', 'Sangli', 'Shirala', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 57, NULL, '', ''),
(173, 'SANGOLA', 'Shop no. 9, Rajaram complex, Ground floor, Sangola, Solapur - 413307 ', 2, NULL, '413307', 'Maharashtra', 'Solapur', 'Sangola', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 58, NULL, '', ''),
(174, 'SATARA 1', 'PUSHPDATTA APPT.,SHOP NO.5,6,7, PL.NO.3,SARVEY NO.481 A,BHUVIKAS PETROL PUMP,SADAR BAZAR,SATARA - 415001 ', 3, NULL, '415001', 'Maharashtra', 'Satara', 'Satara', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 59, NULL, '', ''),
(175, 'SATARA 2', 'GALA NO.34, GOVIND PLAZA, SADAR BAZAR, 523/A/1/7, SATARA - 415002 ', 1, NULL, '415002', 'Maharashtra', 'Satara', 'Satara', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 60, NULL, '', ''),
(176, 'SATARA POWAI NAKA', 'SHOP NO. 1A, VITTHAL LEELA COMPLEX, OPP.IDBI BANK, POWAI NAKA, SATARA - 415001 ', 1, NULL, '415001', 'Maharashtra', 'Satara', 'Satara', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 61, NULL, '', ''),
(177, 'SAWANTWADI', 'S.N. 85/1/ A, GROUND FLOOR, PATIL TOWERS,GAVALI TITHA,NR.S.T.STAND, SAWANTWADI - 416510 ', 2, NULL, '416510', 'Maharashtra', 'Sindhudurg', 'Sawantwadi', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 62, NULL, '', ''),
(178, 'SHAHUPURI', 'RATIKAMAL COMPLEX, 399 E WARD, SHAHUPURI, KOLHAPUR - 416001', 6, NULL, '416001', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 63, NULL, '', ''),
(179, 'SHIRUR', 'C.S.NO.160, HOUSE NO. D3Z - 1000062, GROUND FLOOR, A/P. & TAL. - SHIRUR, DIST. - PUNE - 412210 ', 2, NULL, '412210', 'Maharashtra', 'Pune', 'Shirur', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 64, NULL, '', ''),
(180, 'SOLAPUR 1', 'SHOP NO.4,GR.FLOOR, KHANCHAND MARKET.,H.NO.97/7,GOLDKING PETH, SOLAPUR - 416005 ', 4, NULL, '416005', 'Maharashtra', 'Kolhapur', 'Karvir', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 65, NULL, '', ''),
(181, 'SOLAPUR 2', 'PLOT NO.11/14, SHOP NO. 1 & 2, RAILWAY LINES, CTS NO.840/2/2 F,SOLAPUR - 413001', 3, NULL, '413001', 'Maharashtra', 'Solapur', 'Solapur North', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 66, NULL, '', ''),
(182, 'SOLAPUR 3', 'C.S.NO.744/A, HOUSE NO.697,SALGAR COMPLEX,GROUND FLOOR, SHOP NO. G-4,SOUTH KASABA PETH, SOLAPUR - 413007', 2, NULL, '413007', 'Maharashtra', 'Solapur', 'Solapur North', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 67, NULL, '', ''),
(183, 'SOLAPUR 4', 'SHOP NO.2 & 6, DEGAONKAR SANKUL, OPP.SUDHIR GAS AGENCY,JOD BASVANNA CHOWK,SAKHAR PETH,SOLAPUR - 413005', 1, NULL, '413005', 'Maharashtra', 'Solapur', 'Solapur North', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 68, NULL, '', ''),
(184, 'VITA 1', 'PANDURANG COMPLEX,NR.HDFC BANK,428/1/2, KARAD ROAD, VITA, SANGLI - 415311 ', 2, NULL, '415311', 'Maharashtra', 'Sangli', 'Kadegaon', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 69, NULL, '', ''),
(185, 'VITA 2', 'C.S.NO.1013, LAKADE PLAZA, 1ST FLOOR, SHOP NO. 1 & 2, VITA, TAL.-KHANAPUR, DIST-SANGLI - 415311 ', 1, NULL, '415311', 'Maharashtra', 'Sangli', 'Kadegaon', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 70, NULL, '', ''),
(186, 'WAI', 'C.S.NO.976/978/17, GROUND FLOOR, WAI, TAL. - WAI, DIST. - SATARA - 412803 ', 2, NULL, '412803', 'Maharashtra', 'Satara', 'Wai', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 71, NULL, '', ''),
(187, 'AKKALKOT', '739, Near Central Bank, Station Road,  Akkalkot ', 2, NULL, '413216', 'Maharashtra', 'Solapur', 'Akkalkot', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 72, NULL, '', ''),
(188, 'TULJAPUR', 'Near Tulja Bhavani Temple, Tuljapur', 2, NULL, '413601', 'Maharashtra', 'Osmanabad', 'Tuljapur', '122', NULL, '1', '2022-02-02 10:49:50', NULL, 2, 73, NULL, '', ''),
(189, 'BHOSARI', 'A7, OPP OM HOSPITAL, SHRIRAM COLONY, ALANDI ROAD, BHOSARI', 2, NULL, '411039', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 74, NULL, '', ''),
(190, 'HUPARI', 'MILKAT NO.348/B, SHIVAJI NAGAR,NR.LAKSHMIDEVI GIRLS HIGH SCHOOL RENDAL ROAD,HUPARI', 5, NULL, '416203', 'Maharashtra', 'Kolhapur', 'Hatkanangale', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 75, NULL, '', ''),
(191, 'ICHALKARANJI 2', 'PLOT NO.155,GALA NO. B 101,CENTER ONE, ICHALKARANJI', 5, NULL, '416115', 'Maharashtra', 'Kolhapur', 'Hatkanangle', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 76, NULL, '', ''),
(192, 'JATH', 'Mangalwar peth, Banali chowk, Near State Bank, Jath ', 2, NULL, '416404', 'Maharashtra', 'Sangli', 'Jath', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 77, NULL, '', ''),
(193, 'KANKAVALI 2', 'DP road,Bandu Harne Building ,Shop No. 2&3 Kankavli 416602,Sindhudurg', 1, NULL, '416602', 'Maharashtra', 'Sindhudurg', 'Kankavli', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 78, NULL, '', ''),
(194, 'KARAD 3', 'SHOP NO-6,7& 8, GR.FLR,KRISHNA KRUPA SURVEY NO.34/13+14 SHANIWAR PETH, OPP. ST STAND, KARAD. DIST-SATARA MAHARASHTRA', 1, NULL, '415110', 'Maharashtra', 'Satara', 'Karad', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 79, NULL, '', ''),
(195, 'TASGAON 2', 'Ganesh Hits, Gala no 1, Opp Ganesh Mandir, Near petrol pump, Tasgaon', 5, NULL, '416312', 'Maharashtra', 'Sangli', 'Tasgaon', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 80, NULL, '', ''),
(196, 'VADUJ', 'Vaduj - Above Mongenes,  Shivaji Chowk Vaduj', 2, NULL, '415506', 'Maharashtra', 'Satara', 'Khatav', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 81, NULL, '', ''),
(197, 'KHED', 'SIDDHI PATHARJAI BUILDING KHED GALA NO. 02,IN FRONT OF POLICE STATION,415709', 5, NULL, '415709', 'Maharashtra', 'Ratnagiri', 'Khed (rtg)', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 82, NULL, '', ''),
(198, 'GOREGAON WEST', 'Shop No.5 & 6 Hiren Shopping Centre S.V Road ', 4, NULL, '400104', 'Maharashtra', 'Mumbai', 'Goregaon West', '1', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 83, NULL, '', ''),
(199, 'MALAD WEST', 'Shop No 5, Parasrampuria Chembur, Anand Road ', 2, NULL, '400064', 'Maharashtra', 'Mumbai', 'Malad West', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 84, NULL, '', ''),
(200, 'MIRA ROAD EAST', 'A-SH NO-11 ASHA DEEP OPP. MAHESH INDL AREA ', 2, NULL, '401107', 'Maharashtra', 'Thane', 'Thane', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 85, NULL, '', ''),
(201, 'OSHIWARA', 'Shop no 5 & 6 building no.26 mhada residential complex ', 1, NULL, '400053', 'Maharashtra', 'Mumbai', 'Mumbai', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 86, NULL, '', ''),
(202, 'VASCO', 'Shop No 1 & 2,Severina Centre, Swatantra Path, Near IOC Laxmi petrol Pump', 2, NULL, '403802', 'Goa', 'South Goa', 'Mormugao', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 87, NULL, '', ''),
(203, 'PONDA', 'Shop No. B1 & B4 Upper Ground Floor,', 2, NULL, '403401', 'Goa', 'South Goa', 'Ponda', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 88, NULL, '', ''),
(204, 'PANAJI', 'Ground floor , Shop no 7 & 8 - Kamat Nagar Apartment, Off MG Road , Opp Hotel Marva - Panjim - Goa - 403001', 1, NULL, '403001', 'Goa', 'North Goa', 'Tiswadi', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 89, NULL, '', ''),
(205, 'MARGAO 2', 'SHOP NO.G-2,GROUND FLOOR,GOPIKA APPTS. MARGAO,GOA  ', 2, NULL, '403601', 'Goa', 'South Goa', 'Salcete', '121', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 90, NULL, '', ''),
(206, 'NASHIK 1', '(College Road) - Shop No 1, Vasant chhaya, Near Vijon Hospital, Collage Road, Nashik  422005', 1, NULL, '422005', 'Maharashtra', 'Nashik', 'Nashik', '32', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 91, NULL, '', ''),
(207, 'NASHIK 2', '(MG Road) - Shop No 3/4, Shilpa Hotel Building, Opp. Yashwant Vyayam Shala, MG Road, Nashik - 422001', 2, NULL, '422001', 'Maharashtra', 'Nashik', 'Nashik', '32', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 92, NULL, '', ''),
(208, 'NASHIK 3', '(Untwadi) - Shop No.1, Vraj Bhoomi Apartment, behind City Center Mall, Near Kankariyas Jewellers, Untwadi, Nashik ', 2, NULL, '422009', 'Maharashtra', 'Nashik', 'Nashik', '32', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 93, NULL, '', ''),
(209, 'PALUS', 'Karad Tasgaon Road,Near Mansingh Bank , Palus . 416310', 2, NULL, '416310', 'Maharashtra', 'Sangli', 'Palus', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 94, NULL, '', ''),
(210, 'RAHURI', 'Navi peth,college road in front of bhagirathi bai kanya vidyalaya rahuri,ahmednagar', 2, NULL, '413705', 'Maharashtra', 'Ahmed Nagar', 'Rahuri', '125', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 95, NULL, '', ''),
(211, 'SWARGATE 1', 'C.S.NO.961, THORAT BUILDING,SHOP OF GROUND FLOOR & HALF PART OF 2ND SHOP,SHUKRAWAR PETH,RASHTRABHUSHAN CHOWK,SWARGATE,PUNE.', 2, NULL, '411002', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 96, NULL, '', ''),
(212, 'SHRIRAMPUR', 'C.S.NO.517,PLOT NO.799,\"KUNAL COMPLEX\",SHOP NO.2 & 3,UPPER GROUND FLOOR,SHIVAJI ROAD,SHRIRAMPUR', 2, NULL, '413709', 'Maharashtra', 'Ahmed Nagar', 'Shrirampur', '125', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 97, NULL, '', ''),
(213, 'REALME KOLHAPUR', 'C.S.NO.1090, SHOP UNIT NO.27 & 28, GROUND FLOOR,CHATRAPATI SHIVAJI STADIUM, E WARD, SHAHUPURI,KOLHAPUR ', 5, NULL, '416001', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 98, NULL, '', ''),
(214, 'THAKUR VILLAGE- KANDIVALI E', 'Shop No. 7 & 8 Shree Ganesh Angan Society, Opp Thakur Collage , Thakur Village, Kandivali East, Mumbai', 1, NULL, '400101', 'Maharashtra', 'Mumbai', 'Kandivali East', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 99, NULL, '', ''),
(215, 'JOGESHWARI 1', 'Shop No.3 Habib Park Co Op Society Ltd Opp Jogeshwari Railway Station Jogeshwari West Mumbai', 2, NULL, '400102', 'Maharashtra', 'Mumbai', 'Jogeshwari West', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 100, NULL, '', ''),
(216, 'RAM MANDIR WEST', 'Shop No.1 & 2 Sairam Apartments, Ram Mandir Road, Goregaon West, Mumbai', 2, NULL, '400067', 'Maharashtra', 'Mumbai', 'Kandivali West', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 101, NULL, '', ''),
(217, 'BHAYANDER WEST', '9 & 10 SAROJ PLAZA, 150 FT. ROAD, NEAR MAXUS MALL BHAYANDER WEST, THANE- 401101', 1, NULL, '401101', 'MAHARASHTRA', 'Thane', 'Thane', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 102, NULL, '', ''),
(218, 'CHARKOP - KANDIVALI W', 'Shop No.38 & 44 Gr Floor Kesar Residency C.H.S Ltd Charkop Kandivali West MUMBAI- 400067', 2, NULL, '400067', 'MAHARASHTRA', 'Mumbai', 'Kandivlai West', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 103, NULL, '', ''),
(219, 'JOGESHWARI 2', 'Shop No.12 Gr Floor Abba Residency Opp Jogeshwari Railway station Jogeshwari West MumbaI', 2, NULL, '400102', 'MAHARASHTRA', 'Mumbai', 'Jogeshwari West', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 105, NULL, '', ''),
(220, 'VASAI WEST', 'Shop no - 01, Mukesh Apartment,Station Road Opposite Bassein Catholic Bank , Pandit Dindayal Nagar,Vasai West - ', 2, NULL, '401202', 'MAHARASHTRA', 'Thane', 'Bassein', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 106, NULL, '', ''),
(221, 'VIRAR WEST', 'SHOP NO.3 & 4 GAURI BHAVAN, PARIJAT CO. OP. HSG SO, GAOTHAN, VIRAR (W) THANE-401303', 2, NULL, '401303', 'MAHARASHTRA', 'Thane', 'Vasai', '126', NULL, '1', '0000-00-00 00:00:00', NULL, 1, 107, NULL, '', ''),
(222, 'ISLAMPUR 3', 'Appo  S T Stand  Sidhanath Sankul ,Falle Building  Tal. Valva Dist. Sangli 415409', 1, NULL, '415409', 'Maharashtra', 'Sangli', 'Walva', '105', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 108, NULL, '', ''),
(223, 'SANGAMNER 2', 'Next to Laxmikamal Gas Agency,Opp. HDFC Bank.Link Road Sangamner  ', 2, NULL, '422605', 'Maharashtra', 'Ahmed Nagar', 'Sangamner', '125', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 110, NULL, '', ''),
(224, 'WAKAD', 'E building shop no 1/2 G O Square Menkar Chowk Waked pune ', 1, NULL, '411057', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 111, NULL, '', ''),
(225, 'MANGALWEDHA', '295 Damaji chowk,near S T stand.Mangalwedha', 2, NULL, '413305', 'Maharashtra', 'Solapur', 'Mangalvedha', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 112, NULL, '', ''),
(226, 'SINNAR', 'Opp. Mahatma Phule Statue, Near sinnar ST Stand, Sinnar, Nashik - 422103', 2, NULL, '422103', 'Maharashtra', 'Nashik', 'Sinnar', '873', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 113, NULL, '', ''),
(227, 'SHIVAJI ROAD', 'BUSINESS POINT COMPLEX, 1555/3, C WARD, SHIVAJI ROAD, NR.PADMA TAWLKIES, BINDU CHOWK KOLHAPUR.- 416012', 1, NULL, '416012', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 114, NULL, '', ''),
(228, 'AKLUJ', ' INDAPUR - PANDHARPUR RD.,CTS NO.2258/3,PRANALI SHOPPING CENTRE, SHOP NO.3, AKLUJ - 413101  ', 2, NULL, '413101', 'Maharashtra', 'Solapur', 'Madha', '122', NULL, '1', '0000-00-00 00:00:00', NULL, 2, 1, NULL, '', ''),
(229, 'NAGAR - 4', 'Shop No 3,Sai corner Building,Ground floor ,Chitale Road Ahmednagar-414001', 2, 'Near police chowki', '414001', 'Maharashtra', 'Ahmed Nagar', 'Ahmednagar', '123', NULL, '0', '2022-01-13 10:21:02', 123, 2, NULL, 96, '', ''),
(230, 'OZAR', 'Gat No. 1986/2/C, Saikheda Phata, Ozar (Mig), Tal. Niphad. Dist. Nashik-422007', 2, 'Saikheda Phata, Near Kureshi Dairy, Ozar', '422007', 'Maharashtra', 'Nashik', 'Nashik', '125', NULL, '1', '2022-01-29 06:14:40', 125, 2, 149, 96, '', ''),
(231, 'Satana', '2/414, Taharabad Road, Tal. Satana, Dist. Nashik - 423301', 2, 'Taharabad Road - Satana', '423301', 'Maharashtra', 'Nashik', 'Satana', '125', NULL, '1', '2022-01-29 06:14:53', 125, 2, 150, 96, '', ''),
(232, 'SHEVGAON', 'Opp Nagnath Bharde Mangal  Karyalay Miri Road  Tal-Shevgaon 414502,Dist Ahmednagar.', 2, NULL, '414502', 'Maharashtra', 'Ahmed Nagar', 'Shevgaon', '123', NULL, '1', '2022-01-15 12:01:11', NULL, 2, 120, NULL, '', ''),
(233, 'CHIKHALI', 'Shop No.1 & 2 Dehu Alandi Road, Chikhali Gav, Near Corporator Kundan Gaikwad Office Chikhali Pune.', 2, NULL, '412062', 'Maharashtra', 'Pune', 'Pune City', '123', NULL, '1', '2022-01-15 12:01:08', NULL, 2, 133, NULL, '', ''),
(234, 'NARAYANGAON', 'NARAYANGAON', 7, NULL, '410504', 'Maharashtra', 'Pune', 'Junnar', '123', NULL, '1', '2022-01-15 11:59:59', NULL, 2, 134, NULL, '', ''),
(235, 'Satara 4', 'Shop no- 5, Lucky Plaza, Z P Chowk, Satara 415001\r\n', 2, 'Zilla Parishad \r\n', '415001', 'Maharashtra', 'Satara', 'Satara', '120', NULL, '0', '2022-01-17 08:28:10', 120, 2, NULL, 95, '', ''),
(236, 'RAJAPUR ', 'AP- RAJAPUR DOSANI PLAZA ,BAJARPETH  \r\nTAL- RAJAPUR, DIST - RATNAGIRI ', 5, 'DOSANI PLAZA BAJARPETH', '416702', 'Maharashtra', 'Ratnagiri', 'Rajapur', '121', NULL, '0', '2022-01-18 14:51:09', 762, 2, NULL, 95, '', ''),
(237, 'GADHINGLAJ 2', 'GALA NO.1109/1,MAIN ROAD,OPP.MODERN BAKERY, GADHINGLAJ - 416502', 2, 'OPP MODERN FOOD', '416502', 'Maharashtra', 'Kolhapur', 'Gadhinglaj', '120', NULL, '1', '2022-01-21 11:21:30', 120, 2, 147, 95, '', ''),
(238, 'KURUDWADI ', '27 NAVI PETH KURUDWADI , TAL  MADHA , DIST SOLAPUR 413208', 7, 'NAGAR PALIKA', '413208', 'Maharashtra', 'Solapur', 'Madha', '122', NULL, '1', '2022-01-29 06:14:24', 774, 2, 148, 95, '', ''),
(239, 'MHASWAD', 'mhaswad', 1, NULL, '415509', 'Maharashtra', 'SATARA', 'SATARA', '120', NULL, '1', '2022-01-28 09:04:30', NULL, 1, 130, NULL, '', ''),
(240, 'GARGOTI', 'GARGOTI', 1, NULL, '416209', 'Maharashtra', 'KOLHAPUR', 'GARGOTI', '120', NULL, '1', '2022-01-28 09:04:30', NULL, 1, 127, NULL, '', ''),
(241, 'BAMBAVADE', 'BAMBAVADE', 1, NULL, '416213', 'Maharashtra', 'KOLHAPUR', 'SHAHUWADI', '120', NULL, '1', '2022-01-28 09:04:30', NULL, 1, 125, NULL, '', ''),
(242, 'malegaon 1', 'Shop No. 13, Panjarapol Shopping Center, Near Shivaji Maharaj Putala, Malegaon-423203.', 1, 'Near Shivaji Maharaj Putala, Malegaon', '423203', 'Maharashtra', 'Nashik', 'Malegaon', '125', NULL, '1', '2022-01-29 06:15:38', 125, 2, 151, 96, '', ''),
(243, 'PACHGAON', 'Pachgaon', 1, 'Power colony', '416013', 'Maharashtra', 'Kolhapur', 'Karvir', '120', NULL, '0', '2022-02-01 09:49:36', 120, 2, NULL, 95, '', ''),
(244, 'LATUR 3', '1 No Chowk BARSHI Road Tal- LATUR , Dist - LATUR.', 1, NULL, '413516', 'Maharashtra', 'Osmanabad', 'NA', '122', NULL, '1', '2022-02-02 10:54:20', NULL, 2, 144, NULL, '', ''),
(245, 'MANGALWEDHA', '295 Damaji chowk,near S T stand.Mangalwedha', 1, NULL, '413305', 'Maharashtra', 'SOLAPUR', 'Mangalvedha', '122', NULL, '1', '2022-02-02 10:54:20', NULL, 2, 112, NULL, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cost_center_branch`
--
ALTER TABLE `cost_center_branch`
  ADD PRIMARY KEY (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cost_center_branch`
--
ALTER TABLE `cost_center_branch`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
