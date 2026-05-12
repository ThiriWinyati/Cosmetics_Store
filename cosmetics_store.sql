-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 05:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cosmetics_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `Admin_User_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`Admin_User_ID`, `Name`, `Password`) VALUES
(2, 'Thiri', '$2y$10$sY73J02uZUX6u4hcLTGr.OF4jvqO76jmv9ZDwroaSpPxSVDisBSF.'),
(3, 'Aung', '$2y$10$rCHk.ft.vUMyTLY.E0Ia0ufU52ccRla8pqgHuvLp1JL8AKv8gDAtm'),
(4, 'Lynn', '$2y$10$dg5WPDkh.0UVlCAJ8mnxIeVqjIOVv/C5.axgpDPzuiEAPame/5VFu');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `created_at`) VALUES
(1, 'Dior', '2024-12-21 09:43:44'),
(2, 'Chanel', '2024-12-21 09:43:44'),
(3, 'M.A.C', '2024-12-21 09:43:44'),
(4, 'Maybelline', '2024-12-21 09:43:44'),
(5, 'YvesSaintLaurent', '2024-12-21 09:43:44'),
(6, 'Charlotte Tilbury', '2024-12-21 09:43:44'),
(7, 'NARS', '2024-12-21 09:43:44'),
(8, 'Bobbi Brown', '2024-12-21 09:43:44'),
(9, 'Clinique', '2024-12-21 09:43:44'),
(10, 'Estée Lauder', '2024-12-21 09:43:44'),
(11, 'L\'ORÉAL', '2024-12-21 09:43:44'),
(12, 'Revlon', '2024-12-21 09:43:44'),
(13, 'Glossier', '2024-12-21 09:43:44'),
(14, 'Rare Beauty', '2024-12-21 09:43:44'),
(15, 'Wet n Wild', '2024-12-21 09:43:44'),
(16, 'The Ordinary', '2024-12-21 09:43:44'),
(17, 'CeraVe', '2024-12-21 09:43:44'),
(18, 'Espoir', '2024-12-21 09:43:44'),
(19, 'LANEIGE', '2024-12-21 09:43:44'),
(20, 'JMsolutioon', '2024-12-21 09:43:44'),
(21, 'rom&nd', '2024-12-21 09:43:44'),
(22, '3CE', '2024-12-21 09:43:44'),
(23, 'Etude House', '2024-12-21 09:43:44'),
(24, 'Tony Moly', '2024-12-21 09:43:44'),
(25, 'Cosrx', '2024-12-21 09:43:44'),
(26, 'CLIO', '2024-12-21 09:43:44'),
(27, 'Skin1004', '2024-12-21 09:43:44'),
(28, 'HERA', '2024-12-21 09:43:44'),
(29, 'Colorgram', '2024-12-21 09:43:44'),
(30, 'SheGlam', '2024-12-21 09:43:44'),
(32, 'Judy Doll', '2025-01-27 15:47:35');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`Category_ID`, `Category_Name`) VALUES
(1, 'Foundation'),
(2, 'Cushion'),
(3, 'Concealer'),
(4, 'Bronzer'),
(5, 'Blush'),
(6, 'Highlighter'),
(7, 'Eye Liner'),
(8, 'Eye Shadow'),
(9, 'Lipstick'),
(12, 'Eyebrow'),
(16, 'Lip Care'),
(19, 'Mascara'),
(20, 'Finishing Powder');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `customer_id`, `admin_id`, `message`, `timestamp`, `admin_read`) VALUES
(2, 17, NULL, 'Hello', '2025-01-14 14:35:00', 1),
(3, 17, NULL, 'Hello', '2025-01-14 14:35:07', 1),
(4, 17, NULL, 'Hello', '2025-01-14 14:38:53', 1),
(5, 17, 0, 'Hello', '2025-01-14 14:54:38', 1),
(6, 17, 0, 'Hiii', '2025-01-14 14:54:43', 1),
(7, 17, NULL, 'hii', '2025-01-14 15:11:21', 1),
(8, 17, 0, 'terh', '2025-01-14 15:37:33', 1),
(9, 17, 0, 'rg', '2025-01-14 15:41:16', 1),
(10, 17, 0, 'efwe', '2025-01-14 15:41:43', 1),
(11, 17, 0, 'weg', '2025-01-14 15:45:22', 1),
(12, 17, 0, 's', '2025-01-14 15:48:47', 1),
(13, 17, 0, 'efg', '2025-01-14 15:53:25', 1),
(14, 17, 0, 'gae', '2025-01-14 15:57:55', 1),
(15, 17, NULL, 'hi', '2025-01-14 16:01:09', 1),
(16, 17, 2, 'juohi', '2025-01-14 22:40:17', 1),
(17, 17, NULL, 'gfwwg', '2025-01-14 23:40:38', 1),
(18, 17, NULL, 'i want to ask smth', '2025-01-15 02:22:14', 1),
(19, 17, NULL, 'fwerweg', '2025-01-15 02:25:20', 1),
(20, 17, NULL, 'fea', '2025-01-15 02:28:56', 1),
(21, 17, NULL, 'ad', '2025-01-15 02:32:19', 1),
(22, 17, NULL, 'hj', '2025-01-15 02:34:16', 1),
(23, 17, 2, 'kijn', '2025-01-15 02:39:57', 1),
(24, 17, NULL, 'rt', '2025-01-15 02:42:31', 1),
(25, 17, NULL, 'edga', '2025-01-15 02:43:06', 1),
(26, 18, NULL, 'Hello', '2025-01-26 10:41:39', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contactmessages`
--

CREATE TABLE `contactmessages` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactmessages`
--

INSERT INTO `contactmessages` (`id`, `name`, `email`, `subject`, `message`, `submission_date`, `customer_id`) VALUES
(6, 'Thiri', 'thiriwinyati@gmail.com', 'want to give feedback!', 'I am really fond of online shopping and this site is my favorite one! Hope to see many popular products from other countries ASAP.', '2025-01-27 17:15:26', 17);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `Coupon_ID` int(11) NOT NULL,
  `Coupon_Code` varchar(50) NOT NULL,
  `Discount_Percentage` decimal(5,2) NOT NULL,
  `Valid_From` timestamp NOT NULL DEFAULT current_timestamp(),
  `Valid_To` timestamp NOT NULL DEFAULT current_timestamp(),
  `Minimum_Purchase_Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`Coupon_ID`, `Coupon_Code`, `Discount_Percentage`, `Valid_From`, `Valid_To`, `Minimum_Purchase_Amount`) VALUES
(1, 'WELCOME10', 10.00, '2024-12-31 17:30:00', '2025-01-31 17:30:00', 50.00),
(2, 'SEASON20', 20.00, '2024-12-31 17:30:00', '2025-01-30 17:30:00', 100.00),
(3, 'BOGO', 50.00, '2024-12-31 17:30:00', '2025-02-28 17:30:00', 30.00),
(4, 'HOLIDAY25', 25.00, '2025-11-30 17:30:00', '2025-12-30 17:30:00', 75.00),
(5, 'LOYALTY15', 15.00, '2024-12-31 17:30:00', '2025-06-29 17:30:00', 60.00),
(6, 'NEWYEAR', 70.00, '2025-01-15 17:30:00', '2025-01-30 17:30:00', 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `Customer_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Signup_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `Phone` varchar(15) NOT NULL,
  `Address` text NOT NULL,
  `Profile_Picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`Customer_ID`, `Name`, `Email`, `Password`, `Signup_time`, `Phone`, `Address`, `Profile_Picture`) VALUES
(17, 'Chaw Nadi', 'cna@gmail.com', '$2y$10$mt4lMZovbXS5lw3YPn8k7OTGjcWhTBZioF7ew23Q3m.YQecIXIt2G', '2025-01-09 23:19:57', '0952314687', '12, A street ', ''),
(18, 'Thiri Winyati', 'thiriwinyati@gmail.com', '$2y$10$yG08w62TbNCXbpGgQDf9ju1smgRFkafYpkObYia3aJS/DY9urj8A6', '2025-01-26 07:56:02', '0952314687', '12, A street ', NULL),
(19, 'Ingyin', 'ingyin@gmail.com', '$2y$10$lfvhUvQuSa2dhk/ItkCf1uOzKyKG8o8srMRsICAYxhYJqnOP122Kq', '2025-01-27 03:39:55', '0952314687', '12, A street ', NULL),
(20, 'Lynn', 'lynn@gmail.com', '$2y$10$P2joSW9V6ujs4nP/uOWXu.5.AvTd8TI4VTba5IdJQ2FHV6x7ImJFm', '2025-01-27 04:44:33', '0952314687', '12, A street ', NULL),
(21, 'Phoo Nge', 'phoo@gmail.com', '$2y$10$TYvkMs3XIF65FP2RRiz.gOJuCvWpd5lKF5A64n78t/RqdCZ9ovx7S', '2025-01-27 10:04:46', '', '', NULL),
(22, 'Nway Thu', 'nway@gmail.com', '$2y$10$jE/mS8/OTFF/EdF6ELyxH.qe0l524myy0uoD9qQupu2JDoy/WDzTe', '2025-01-27 10:05:15', '', '', NULL),
(23, 'May Mon', 'maymon@gmail.com', '$2y$10$2VKoWOZcnoXjfHHDRfqKCOBvRuCAULolaIWUcDnnkoOosA3TgJRZ.', '2025-01-27 10:05:46', '', '', NULL),
(24, 'Lilly', 'lilly@gmail.com', '$2y$10$k105hgLIMuhX.TAOETjmyePWSgCZVnpoZaSQINgKenolpO2wxewxS', '2025-01-27 10:06:14', '', '', NULL),
(25, 'Khine Zin', 'Khinezm@gmail.com', '$2y$10$6FNq51gvEGtQz1gjzMIPyeaq/QsT2En4N8SKTTU7Gd.LagmMTcJZ6', '2025-01-27 10:06:42', '', '', NULL),
(26, 'Thet Htoo', 'thethz@gmail.com', '$2y$10$vBwi2mOcE18e8W4xCE2RFeU8JYHhwPcvU1pDYqKZKR53Qd8aKEyYi', '2025-01-27 10:07:16', '', '', NULL),
(27, 'Yoon', 'yoon@gmail.com', '$2y$10$UEdE7eYHU66LpPsav.sZ6.uybDeCxJbDJg7h7xgATLzWkc8HP1ZaO', '2025-01-27 10:07:47', '', '', NULL),
(28, 'Yoon Lae', 'yoonlae@gmail.com', '$2y$10$gtQvc1zoIpqZ9njY2CtajuY9WB96xhGQ1UaOY4pFEDypR./dYcvDe', '2025-01-27 10:08:17', '', '', NULL),
(29, 'Danny Aung', 'danny@gmail.com', '$2y$10$FzdNgEW40YfPe8meHCgX3.2cB31hcoDYXgY6Cn56tjE8PUboa/Jjq', '2025-01-27 10:08:40', '', '', NULL),
(30, 'Yoon Nadi', 'yoonnadi@gmail.com', '$2y$10$SoahBDIcOVzaxB12jdHL1ul5G1R1yGkW8mpHjGpjSy.vp.BbAq/zS', '2025-01-27 10:09:05', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `FavouritesID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `DateAdded` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favourites`
--

INSERT INTO `favourites` (`FavouritesID`, `Customer_ID`, `Product_ID`, `DateAdded`) VALUES
(40, 19, 16, '2025-01-28 10:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Order_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) DEFAULT 'Pending',
  `Shipping_Address` text DEFAULT NULL,
  `Total_Price` decimal(10,2) DEFAULT NULL,
  `cupon_id` int(11) DEFAULT NULL,
  `shipping_id` int(11) DEFAULT NULL,
  `Payment_Method_ID` int(11) DEFAULT NULL,
  `Phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`Order_ID`, `Customer_ID`, `Order_Date`, `Status`, `Shipping_Address`, `Total_Price`, `cupon_id`, `shipping_id`, `Payment_Method_ID`, `Phone`) VALUES
(1, 26, '2024-02-10 17:30:00', 'Accepted', 'Address 2', 201.82, 2, 6, 2, '+12345678674'),
(2, 23, '2024-11-29 17:30:00', 'Accepted', 'Address 28', 294.70, 6, 5, 4, '+12345678639'),
(3, 23, '2023-06-25 17:30:00', 'Cancelled', 'Address 11', 322.97, 4, 6, 4, '+12345678611'),
(4, 17, '2022-04-14 17:30:00', 'Accepted', 'Address 15', 32.84, 4, 6, 1, '+12345678733'),
(5, 24, '2022-12-08 17:30:00', 'Accepted', 'Address 72', 497.84, 0, 3, 6, '+12345678729'),
(6, 28, '2024-07-17 17:30:00', 'Pending', 'Address 24', 101.34, 0, 3, 6, '+12345678999'),
(7, 18, '2024-07-06 17:30:00', 'Accepted', 'Address 45', 52.40, 0, 1, 2, '+12345678310'),
(8, 19, '2023-01-23 17:30:00', 'Cancelled', 'Address 33', 469.49, 0, 1, 5, '+12345678496'),
(9, 25, '2023-04-01 17:30:00', 'Accepted', 'Address 55', 230.24, 0, 4, 1, '+12345678499'),
(10, 22, '2022-10-05 17:30:00', 'Pending', 'Address 12', 177.91, 0, 2, 1, '+12345678699'),
(11, 22, '2023-03-07 17:30:00', 'Cancelled', 'Address 36', 124.28, 0, 5, 4, '+12345678167'),
(12, 25, '2023-08-20 17:30:00', 'Pending', 'Address 56', 112.38, 2, 5, 6, '+12345678659'),
(13, 22, '2022-12-27 17:30:00', 'Cancelled', 'Address 15', 382.02, 0, 1, 4, '+12345678210'),
(14, 23, '2024-07-05 17:30:00', 'Cancelled', 'Address 7', 146.49, 0, 2, 5, '+12345678283'),
(15, 28, '2024-08-30 17:30:00', 'Accepted', 'Address 13', 53.33, 0, 2, 5, '+12345678734'),
(16, 21, '2022-08-12 17:30:00', 'Accepted', 'Address 97', 486.69, 6, 3, 6, '+12345678474'),
(17, 18, '2024-05-19 17:30:00', 'Pending', 'Address 30', 287.62, 0, 4, 3, '+12345678972'),
(18, 21, '2022-07-10 17:30:00', 'Pending', 'Address 83', 250.55, 0, 6, 6, '+12345678571'),
(19, 29, '2023-08-16 17:30:00', 'Accepted', 'Address 61', 93.37, 0, 4, 6, '+12345678938'),
(20, 28, '2022-11-28 17:30:00', 'Pending', 'Address 66', 231.67, 0, 4, 4, '+12345678850'),
(21, 25, '2024-12-01 17:30:00', 'Pending', 'Address 29', 310.49, 0, 4, 1, '+12345678231'),
(22, 17, '2022-05-07 17:30:00', 'Pending', 'Address 84', 476.36, 4, 6, 4, '+12345678499'),
(23, 18, '2022-01-28 17:30:00', 'Accepted', 'Address 8', 37.23, 0, 4, 1, '+12345678240'),
(24, 30, '2023-11-13 17:30:00', 'Pending', 'Address 30', 26.48, 0, 4, 1, '+12345678768'),
(25, 18, '2023-02-02 17:30:00', 'Pending', 'Address 16', 441.48, 0, 2, 3, '+12345678304'),
(26, 17, '2023-12-05 17:30:00', 'Pending', 'Address 95', 431.15, 0, 3, 5, '+12345678115'),
(27, 27, '2022-12-02 17:30:00', 'Pending', 'Address 57', 393.70, 0, 5, 2, '+12345678239'),
(28, 29, '2024-08-22 17:30:00', 'Cancelled', 'Address 39', 218.47, 0, 1, 3, '+12345678913'),
(29, 25, '2024-03-15 17:30:00', 'Cancelled', 'Address 76', 335.36, 8, 3, 6, '+12345678674'),
(30, 18, '2024-09-17 17:30:00', 'Accepted', 'Address 89', 80.42, 0, 5, 3, '+12345678488'),
(31, 17, '2023-04-08 17:30:00', 'Pending', 'Address 70', 401.74, 0, 1, 6, '+12345678384'),
(32, 30, '2024-01-06 17:30:00', 'Pending', 'Address 40', 482.64, 9, 1, 4, '+12345678550'),
(33, 23, '2023-01-05 17:30:00', 'Cancelled', 'Address 44', 439.06, 0, 2, 3, '+12345678781'),
(34, 23, '2022-08-08 17:30:00', 'Cancelled', 'Address 30', 450.92, 6, 3, 5, '+12345678655'),
(35, 22, '2022-12-05 17:30:00', 'Cancelled', 'Address 4', 45.95, 0, 1, 1, '+12345678356'),
(36, 23, '2023-08-28 17:30:00', 'Cancelled', 'Address 27', 120.94, 2, 3, 2, '+12345678340'),
(37, 24, '2022-10-01 17:30:00', 'Cancelled', 'Address 1', 126.70, 5, 2, 5, '+12345678802'),
(38, 22, '2022-04-12 17:30:00', 'Pending', 'Address 64', 120.63, 10, 1, 6, '+12345678325'),
(39, 18, '2022-03-04 17:30:00', 'Pending', 'Address 68', 399.78, 0, 5, 3, '+12345678204'),
(40, 17, '2024-01-14 17:30:00', 'Pending', 'Address 74', 122.29, 6, 6, 5, '+12345678562'),
(41, 25, '2023-11-26 17:30:00', 'Pending', 'Address 98', 414.96, 0, 4, 2, '+12345678534'),
(42, 28, '2024-10-01 17:30:00', 'Pending', 'Address 94', 315.67, 0, 4, 2, '+12345678577'),
(43, 22, '2024-01-03 17:30:00', 'Cancelled', 'Address 64', 367.84, 0, 3, 6, '+12345678127'),
(44, 24, '2024-10-27 17:30:00', 'Pending', 'Address 89', 350.18, 0, 6, 4, '+12345678772'),
(45, 29, '2022-01-19 17:30:00', 'Pending', 'Address 76', 313.82, 2, 3, 3, '+12345678746'),
(46, 25, '2023-09-05 17:30:00', 'Cancelled', 'Address 12', 184.35, 0, 6, 1, '+12345678173'),
(47, 18, '2023-01-19 17:30:00', 'Cancelled', 'Address 65', 142.93, 5, 2, 2, '+12345678207'),
(48, 20, '2022-12-30 17:30:00', 'Accepted', 'Address 30', 309.82, 5, 2, 3, '+12345678305'),
(49, 20, '2022-07-05 17:30:00', 'Accepted', 'Address 86', 49.77, 9, 6, 6, '+12345678986'),
(50, 21, '2024-05-20 17:30:00', 'Pending', 'Address 58', 325.10, 0, 1, 6, '+12345678281'),
(51, 30, '2024-03-25 17:30:00', 'Pending', 'Address 41', 436.12, 0, 2, 4, '+12345678256'),
(52, 21, '2024-12-13 17:30:00', 'Pending', 'Address 73', 478.56, 0, 5, 4, '+12345678694'),
(53, 18, '2022-08-11 17:30:00', 'Cancelled', 'Address 37', 298.76, 0, 2, 4, '+12345678133'),
(55, 21, '2023-04-03 17:30:00', 'Pending', 'Address 53', 110.79, 0, 2, 2, '+12345678921'),
(56, 29, '2022-01-04 17:30:00', 'Accepted', 'Address 48', 272.59, 3, 5, 3, '+12345678135'),
(57, 25, '2022-04-11 17:30:00', 'Cancelled', 'Address 83', 146.53, 0, 6, 2, '+12345678181'),
(58, 26, '2024-11-29 17:30:00', 'Cancelled', 'Address 31', 367.70, 0, 4, 3, '+12345678562'),
(59, 18, '2022-06-13 17:30:00', 'Pending', 'Address 30', 108.34, 0, 5, 6, '+12345678945'),
(60, 24, '2023-10-27 17:30:00', 'Cancelled', 'Address 82', 445.43, 0, 4, 3, '+12345678976'),
(61, 27, '2024-07-31 17:30:00', 'Pending', 'Address 100', 195.16, 0, 3, 5, '+12345678825'),
(62, 29, '2022-07-03 17:30:00', 'Pending', 'Address 87', 135.33, 0, 5, 5, '+12345678921'),
(63, 18, '2023-08-30 17:30:00', 'Pending', 'Address 27', 177.03, 0, 6, 4, '+12345678266'),
(64, 23, '2023-09-22 17:30:00', 'Accepted', 'Address 58', 266.08, 3, 1, 5, '+12345678726'),
(65, 22, '2024-09-02 17:30:00', 'Pending', 'Address 27', 282.35, 0, 4, 6, '+12345678487'),
(66, 30, '2023-11-04 17:30:00', 'Cancelled', 'Address 3', 430.05, 0, 5, 5, '+12345678345'),
(67, 21, '2022-02-19 17:30:00', 'Accepted', 'Address 78', 399.58, 10, 3, 5, '+12345678119'),
(69, 29, '2023-07-05 17:30:00', 'Cancelled', 'Address 43', 43.11, 0, 4, 6, '+12345678453'),
(70, 30, '2023-05-11 17:30:00', 'Cancelled', 'Address 62', 122.20, 0, 3, 2, '+12345678614'),
(71, 21, '2024-03-25 17:30:00', 'Pending', 'Address 82', 128.20, 0, 6, 6, '+12345678646'),
(72, 23, '2023-02-01 17:30:00', 'Cancelled', 'Address 83', 460.22, 0, 1, 2, '+12345678336'),
(73, 27, '2023-01-17 17:30:00', 'Pending', 'Address 48', 440.03, 0, 6, 4, '+12345678913'),
(74, 21, '2023-06-21 17:30:00', 'Pending', 'Address 42', 359.45, 0, 4, 6, '+12345678210'),
(75, 26, '2023-01-20 17:30:00', 'Accepted', 'Address 22', 254.76, 0, 6, 3, '+12345678888'),
(76, 17, '2023-03-22 17:30:00', 'Cancelled', 'Address 85', 190.35, 6, 1, 2, '+12345678646'),
(77, 21, '2023-05-14 17:30:00', 'Accepted', 'Address 22', 321.12, 0, 3, 4, '+12345678305'),
(78, 20, '2022-02-19 17:30:00', 'Pending', 'Address 45', 299.57, 0, 2, 1, '+12345678539'),
(79, 29, '2022-02-14 17:30:00', 'Cancelled', 'Address 93', 308.13, 6, 2, 2, '+12345678490'),
(80, 25, '2023-01-07 17:30:00', 'Cancelled', 'Address 34', 390.42, 0, 6, 3, '+12345678223'),
(81, 26, '2022-10-31 17:30:00', 'Pending', 'Address 1', 484.85, 0, 3, 4, '+12345678288'),
(82, 27, '2023-08-30 17:30:00', 'Cancelled', 'Address 7', 392.36, 6, 2, 4, '+12345678500'),
(83, 22, '2024-06-27 17:30:00', 'Accepted', 'Address 93', 304.79, 0, 6, 4, '+12345678953'),
(84, 27, '2022-11-19 17:30:00', 'Accepted', 'Address 37', 48.51, 0, 6, 3, '+12345678418'),
(85, 28, '2022-03-27 17:30:00', 'Pending', 'Address 71', 201.35, 0, 4, 6, '+12345678382'),
(86, 21, '2022-02-27 17:30:00', 'Cancelled', 'Address 78', 96.87, 0, 1, 4, '+12345678347'),
(87, 18, '2023-10-06 17:30:00', 'Accepted', 'Address 42', 405.41, 0, 3, 1, '+12345678689'),
(88, 21, '2022-02-22 17:30:00', 'Pending', 'Address 11', 189.82, 1, 3, 6, '+12345678650'),
(89, 27, '2023-11-09 17:30:00', 'Cancelled', 'Address 35', 380.33, 0, 5, 1, '+12345678122'),
(90, 26, '2024-06-04 17:30:00', 'Cancelled', 'Address 11', 233.07, 2, 2, 1, '+12345678791'),
(91, 22, '2022-04-21 17:30:00', 'Cancelled', 'Address 44', 246.17, 0, 4, 5, '+12345678667'),
(92, 24, '2024-02-12 17:30:00', 'Accepted', 'Address 97', 59.51, 1, 6, 1, '+12345678829'),
(93, 27, '2024-04-03 17:30:00', 'Pending', 'Address 32', 281.27, 9, 1, 3, '+12345678862'),
(94, 18, '2022-07-20 17:30:00', 'Pending', 'Address 22', 392.32, 0, 1, 6, '+12345678958'),
(95, 18, '2023-07-05 17:30:00', 'Accepted', 'Address 30', 302.75, 0, 4, 6, '+12345678996'),
(96, 20, '2024-01-29 17:30:00', 'Cancelled', 'Address 39', 492.79, 0, 3, 3, '+12345678147'),
(98, 27, '2024-06-21 17:30:00', 'Accepted', 'Address 16', 411.06, 0, 1, 1, '+12345678413'),
(99, 26, '2023-03-30 17:30:00', 'Accepted', 'Address 25', 47.00, 8, 4, 3, '+12345678781'),
(100, 26, '2024-05-24 17:30:00', 'Pending', 'Address 39', 467.45, 8, 1, 3, '+12345678526'),
(199, 19, '2025-01-27 16:34:46', 'Accepted', '12, A street ', 16.00, NULL, 290, 3, '0952314687'),
(201, 19, '2025-01-28 03:54:42', 'Pending', '12, A street ', 28.00, NULL, 292, 3, '0952314687'),
(202, 19, '2025-01-28 03:56:16', 'Pending', '12, A street ', 12.50, NULL, 293, 3, '0952314687'),
(203, 19, '2025-01-28 03:59:27', 'Pending', '12, A street ', 12.50, NULL, 294, 3, '0952314687'),
(204, 19, '2025-01-28 04:02:38', 'Pending', '12, A street ', 0.00, NULL, 295, 3, '0952314687'),
(205, 19, '2025-01-28 04:02:55', 'Pending', '12, A street ', 12.50, NULL, 296, 3, '0952314687'),
(206, 19, '2025-01-28 04:03:57', 'Pending', '12, A street ', 25.00, NULL, 297, 3, '0952314687');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `Order_Item_ID` int(11) NOT NULL,
  `Order_ID` int(11) DEFAULT NULL,
  `Product_ID` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `Unit_Price` decimal(10,2) NOT NULL,
  `Subtotal` decimal(10,2) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `shade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`Order_Item_ID`, `Order_ID`, `Product_ID`, `Quantity`, `Unit_Price`, `Subtotal`, `brand_id`, `shade_id`) VALUES
(1, 1, 11, 2, 19.88, 39.76, 3, 1),
(2, 1, 23, 9, 11.06, 99.54, 8, 2),
(3, 1, 27, 10, 71.10, 711.00, 3, 5),
(4, 1, 25, 1, 59.34, 59.34, 9, 11),
(5, 1, 18, 1, 74.31, 74.31, 2, 9),
(6, 2, 26, 8, 93.94, 751.52, 4, 2),
(7, 2, 20, 5, 91.22, 456.10, 8, 16),
(8, 2, 30, 8, 90.87, 726.96, 2, 20),
(9, 2, 17, 6, 51.17, 307.02, 8, 9),
(10, 3, 27, 4, 89.23, 356.92, 3, 1),
(11, 4, 11, 4, 34.68, 138.72, 4, 12),
(12, 4, 16, 5, 57.33, 286.65, 6, 7),
(13, 4, 27, 9, 18.39, 165.51, 10, 16),
(14, 4, 17, 6, 35.49, 212.94, 5, 7),
(15, 4, 14, 5, 19.52, 97.60, 5, 14),
(16, 5, 11, 3, 83.32, 249.96, 1, 3),
(18, 5, 11, 5, 24.20, 121.00, 3, 15),
(19, 6, 27, 1, 71.55, 71.55, 5, 20),
(20, 6, 11, 10, 57.20, 572.00, 4, 20),
(21, 6, 24, 10, 55.26, 552.60, 5, 10),
(22, 7, 22, 5, 21.94, 109.70, 8, 19),
(23, 7, 18, 9, 79.02, 711.18, 3, 15),
(24, 7, 15, 9, 26.14, 235.26, 4, 19),
(26, 8, 16, 9, 24.82, 223.38, 5, 12),
(27, 8, 30, 1, 12.27, 12.27, 4, 14),
(28, 9, 11, 4, 43.03, 172.12, 4, 14),
(29, 9, 15, 8, 73.41, 587.28, 10, 8),
(31, 10, 18, 7, 42.15, 295.05, 4, 20),
(32, 11, 23, 1, 17.22, 17.22, 5, 11),
(33, 12, 24, 1, 26.83, 26.83, 3, 7),
(34, 12, 23, 6, 8.64, 51.84, 7, 16),
(35, 13, 11, 5, 58.92, 294.60, 7, 16),
(36, 13, 14, 10, 34.96, 349.60, 3, 10),
(37, 13, 20, 5, 53.88, 269.40, 6, 10),
(38, 13, 15, 7, 67.88, 475.16, 5, 6),
(39, 13, 22, 3, 60.86, 182.58, 6, 13),
(40, 14, 19, 1, 38.69, 38.69, 4, 13),
(41, 14, 17, 10, 86.30, 863.00, 7, 14),
(42, 14, 21, 4, 85.56, 342.24, 10, 18),
(43, 15, 24, 8, 99.10, 792.80, 3, 8),
(44, 16, 21, 10, 12.45, 124.50, 9, 3),
(45, 16, 16, 8, 66.18, 529.44, 2, 18),
(46, 16, 25, 7, 22.08, 154.56, 9, 12),
(47, 16, 19, 1, 89.24, 89.24, 7, 19),
(48, 17, 16, 2, 20.99, 41.98, 5, 2),
(49, 17, 20, 2, 61.13, 122.26, 3, 20),
(50, 18, 11, 2, 93.25, 186.50, 5, 15),
(51, 19, 15, 10, 79.27, 792.70, 3, 16),
(52, 20, 17, 7, 57.07, 399.49, 1, 18),
(53, 21, 15, 6, 19.92, 119.52, 6, 2),
(54, 21, 27, 5, 93.41, 467.05, 10, 6),
(55, 21, 24, 1, 78.88, 78.88, 5, 12),
(56, 21, 20, 4, 10.33, 41.32, 10, 10),
(57, 21, 24, 6, 23.62, 141.72, 9, 10),
(58, 22, 20, 8, 61.08, 488.64, 5, 17),
(59, 22, 11, 5, 46.35, 231.75, 5, 7),
(60, 23, 22, 10, 36.87, 368.70, 1, 12),
(61, 24, 23, 7, 16.54, 115.78, 1, 5),
(62, 24, 16, 8, 6.94, 55.52, 9, 2),
(63, 24, 18, 1, 49.23, 49.23, 5, 13),
(64, 25, 21, 9, 93.96, 845.64, 3, 5),
(65, 25, 11, 9, 99.61, 896.49, 9, 4),
(66, 25, 23, 8, 7.22, 57.76, 5, 14),
(67, 26, 11, 10, 74.12, 741.20, 4, 20),
(68, 26, 30, 3, 66.03, 198.09, 1, 20),
(69, 27, 26, 2, 28.15, 56.30, 1, 12),
(70, 27, 16, 7, 49.92, 349.44, 10, 12),
(71, 27, 25, 2, 95.60, 191.20, 8, 11),
(72, 27, 19, 5, 32.31, 161.55, 5, 9),
(73, 27, 27, 3, 81.12, 243.36, 3, 14),
(74, 28, 22, 10, 30.49, 304.90, 7, 11),
(75, 28, 24, 3, 35.67, 107.01, 3, 17),
(76, 28, 11, 1, 30.70, 30.70, 3, 6),
(77, 28, 16, 3, 87.71, 263.13, 2, 19),
(78, 29, 27, 2, 45.84, 91.68, 3, 2),
(79, 29, 11, 3, 63.34, 190.02, 6, 17),
(80, 29, 28, 9, 71.41, 642.69, 10, 2),
(81, 29, 30, 5, 60.80, 304.00, 7, 17),
(82, 29, 11, 4, 84.40, 337.60, 9, 18),
(83, 30, 24, 2, 25.60, 51.20, 3, 12),
(84, 30, 28, 6, 93.01, 558.06, 10, 2),
(85, 30, 17, 4, 17.91, 71.64, 3, 16),
(86, 30, 14, 5, 6.44, 32.20, 2, 5),
(87, 30, 30, 3, 81.63, 244.89, 5, 12),
(88, 31, 26, 4, 74.50, 298.00, 10, 8),
(89, 31, 16, 6, 44.52, 267.12, 5, 14),
(90, 31, 20, 3, 99.59, 298.77, 2, 4),
(91, 32, 24, 7, 26.43, 185.01, 9, 15),
(92, 32, 11, 5, 47.87, 239.35, 5, 12),
(93, 33, 25, 10, 18.90, 189.00, 7, 4),
(94, 33, 30, 3, 70.45, 211.35, 4, 12),
(95, 34, 18, 8, 72.84, 582.72, 5, 17),
(96, 34, 15, 4, 60.40, 241.60, 1, 18),
(97, 34, 22, 8, 68.92, 551.36, 1, 14),
(98, 34, 28, 4, 61.74, 246.96, 8, 19),
(99, 34, 22, 7, 37.91, 265.37, 8, 6),
(100, 35, 21, 8, 64.32, 514.56, 5, 9),
(101, 35, 23, 2, 45.37, 90.74, 7, 15),
(102, 35, 26, 10, 80.27, 802.70, 10, 13),
(103, 36, 20, 6, 15.13, 90.78, 4, 14),
(104, 36, 25, 10, 46.10, 461.00, 8, 3),
(105, 37, 28, 8, 89.68, 717.44, 4, 18),
(106, 37, 23, 4, 27.36, 109.44, 3, 12),
(107, 37, 16, 7, 55.73, 390.11, 3, 4),
(108, 37, 11, 4, 88.53, 354.12, 4, 10),
(109, 38, 30, 10, 81.88, 818.80, 3, 19),
(110, 38, 11, 5, 74.54, 372.70, 1, 16),
(111, 39, 16, 5, 17.99, 89.95, 9, 17),
(112, 40, 15, 3, 72.05, 216.15, 6, 4),
(113, 40, 30, 6, 11.70, 70.20, 2, 4),
(114, 40, 28, 5, 51.77, 258.85, 5, 4),
(115, 40, 15, 2, 28.75, 57.50, 10, 19),
(116, 40, 23, 2, 68.11, 136.22, 2, 12),
(117, 41, 14, 3, 27.52, 82.56, 3, 20),
(119, 42, 26, 9, 8.28, 74.52, 2, 5),
(120, 42, 19, 4, 53.98, 215.92, 3, 18),
(121, 43, 27, 2, 97.60, 195.20, 9, 20),
(122, 43, 11, 9, 77.29, 695.61, 8, 6),
(123, 44, 18, 4, 62.47, 249.88, 10, 6),
(124, 44, 28, 1, 67.89, 67.89, 4, 18),
(125, 44, 20, 1, 96.61, 96.61, 7, 20),
(126, 45, 26, 2, 86.69, 173.38, 3, 15),
(127, 45, 18, 6, 76.23, 457.38, 8, 13),
(129, 45, 24, 10, 86.82, 868.20, 3, 15),
(131, 46, 27, 2, 31.10, 62.20, 3, 6),
(132, 46, 14, 8, 45.21, 361.68, 7, 7),
(133, 47, 15, 4, 44.59, 178.36, 5, 13),
(134, 47, 11, 8, 50.21, 401.68, 2, 14),
(135, 47, 21, 3, 47.02, 141.06, 6, 8),
(136, 47, 24, 6, 32.88, 197.28, 3, 11),
(137, 48, 28, 6, 16.12, 96.72, 1, 6),
(138, 48, 21, 6, 36.29, 217.74, 10, 7),
(139, 48, 25, 4, 95.20, 380.80, 9, 12),
(140, 49, 25, 7, 89.32, 625.24, 8, 11),
(141, 50, 26, 7, 89.37, 625.59, 8, 3),
(142, 50, 24, 6, 48.69, 292.14, 10, 1),
(143, 50, 18, 10, 14.94, 149.40, 8, 8),
(144, 51, 17, 6, 10.42, 62.52, 9, 9),
(145, 51, 18, 9, 5.88, 52.92, 3, 8),
(146, 51, 30, 10, 7.73, 77.30, 10, 9),
(147, 51, 18, 4, 87.26, 349.04, 4, 19),
(148, 51, 30, 1, 52.07, 52.07, 5, 18),
(149, 52, 18, 5, 44.77, 223.85, 3, 10),
(150, 53, 14, 5, 38.42, 192.10, 4, 10),
(151, 53, 23, 5, 27.44, 137.20, 10, 6),
(155, 55, 23, 2, 79.35, 158.70, 8, 9),
(156, 55, 23, 2, 87.81, 175.62, 10, 19),
(157, 56, 23, 7, 98.71, 690.97, 4, 8),
(158, 56, 16, 9, 87.86, 790.74, 2, 8),
(160, 57, 24, 6, 75.72, 454.32, 10, 1),
(161, 57, 21, 10, 71.01, 710.10, 4, 18),
(162, 58, 23, 6, 54.16, 324.96, 5, 8),
(163, 58, 25, 5, 25.45, 127.25, 1, 3),
(164, 58, 19, 9, 92.55, 832.95, 10, 19),
(165, 58, 14, 10, 85.98, 859.80, 7, 20),
(166, 59, 22, 2, 78.26, 156.52, 5, 18),
(169, 60, 22, 9, 81.05, 729.45, 8, 2),
(170, 60, 25, 5, 53.49, 267.45, 10, 18),
(171, 60, 20, 7, 72.52, 507.64, 10, 5),
(172, 60, 21, 5, 92.03, 460.15, 7, 15),
(173, 61, 21, 5, 49.47, 247.35, 6, 9),
(174, 61, 11, 10, 61.32, 613.20, 7, 6),
(175, 61, 20, 2, 46.58, 93.16, 8, 18),
(176, 61, 21, 5, 37.37, 186.85, 5, 2),
(178, 62, 22, 7, 67.82, 474.74, 6, 4),
(179, 62, 23, 2, 35.55, 71.10, 3, 5),
(180, 63, 19, 2, 69.51, 139.02, 10, 8),
(181, 63, 11, 6, 13.14, 78.84, 6, 5),
(182, 63, 15, 7, 45.19, 316.33, 8, 8),
(183, 63, 20, 3, 98.25, 294.75, 2, 13),
(184, 63, 25, 7, 90.58, 634.06, 3, 6),
(185, 64, 23, 7, 87.49, 612.43, 8, 15),
(186, 64, 26, 2, 84.12, 168.24, 2, 13),
(187, 64, 14, 4, 69.69, 278.76, 6, 4),
(188, 65, 14, 2, 34.75, 69.50, 1, 9),
(189, 65, 20, 3, 21.77, 65.31, 6, 17),
(190, 65, 25, 5, 86.48, 432.40, 1, 15),
(191, 66, 15, 5, 93.92, 469.60, 6, 6),
(192, 66, 15, 1, 14.15, 14.15, 6, 4),
(193, 66, 16, 1, 97.13, 97.13, 7, 4),
(202, 69, 19, 9, 38.41, 345.69, 5, 9),
(208, 0, 0, 0, 0.00, 0.00, 0, 0),
(210, 71, 17, 8, 96.05, 768.40, 7, 5),
(211, 71, 19, 8, 92.90, 743.20, 8, 6),
(212, 72, 16, 4, 53.72, 214.88, 3, 20),
(213, 72, 17, 2, 80.51, 161.02, 6, 3),
(214, 72, 27, 5, 95.36, 476.80, 1, 11),
(215, 72, 17, 9, 59.40, 534.60, 10, 20),
(216, 72, 28, 3, 39.93, 119.79, 8, 6),
(217, 73, 20, 7, 53.68, 375.76, 6, 16),
(218, 73, 20, 5, 58.89, 294.45, 3, 6),
(219, 73, 19, 1, 59.57, 59.57, 10, 9),
(220, 74, 28, 7, 39.56, 276.92, 8, 5),
(221, 74, 26, 7, 47.55, 332.85, 10, 1),
(222, 74, 24, 1, 60.66, 60.66, 7, 19),
(223, 75, 23, 2, 13.11, 26.22, 10, 19),
(224, 75, 20, 2, 27.00, 54.00, 7, 20),
(225, 76, 26, 6, 34.61, 207.66, 1, 12),
(226, 76, 16, 8, 33.86, 270.88, 5, 8),
(227, 76, 23, 4, 8.57, 34.28, 9, 5),
(228, 76, 22, 10, 46.20, 462.00, 10, 16),
(229, 76, 27, 5, 20.97, 104.85, 9, 20),
(230, 77, 24, 4, 58.33, 233.32, 7, 8),
(231, 77, 25, 9, 46.77, 420.93, 5, 2),
(232, 77, 28, 2, 31.30, 62.60, 1, 6),
(233, 77, 11, 4, 6.32, 25.28, 1, 10),
(234, 78, 30, 1, 51.35, 51.35, 9, 5),
(235, 78, 11, 7, 8.71, 60.97, 3, 7),
(236, 78, 16, 7, 86.72, 607.04, 9, 15),
(237, 78, 23, 5, 37.87, 189.35, 9, 13),
(238, 79, 22, 8, 94.05, 752.40, 6, 5),
(239, 79, 16, 5, 67.13, 335.65, 4, 9),
(240, 79, 16, 9, 99.87, 898.83, 1, 15),
(241, 80, 17, 6, 47.36, 284.16, 9, 20),
(242, 80, 20, 10, 82.48, 824.80, 5, 10),
(243, 81, 20, 1, 42.78, 42.78, 7, 11),
(244, 82, 30, 7, 22.52, 157.64, 7, 14),
(245, 82, 24, 1, 36.55, 36.55, 5, 2),
(246, 83, 28, 2, 44.52, 89.04, 1, 17),
(247, 83, 18, 1, 92.41, 92.41, 1, 4),
(248, 84, 30, 10, 65.62, 656.20, 6, 19),
(249, 84, 17, 10, 40.16, 401.60, 6, 11),
(251, 84, 23, 7, 78.14, 546.98, 4, 10),
(252, 84, 11, 10, 49.39, 493.90, 5, 20),
(253, 85, 20, 6, 37.47, 224.82, 3, 5),
(254, 85, 28, 3, 72.18, 216.54, 1, 11),
(256, 85, 17, 5, 98.90, 494.50, 1, 5),
(257, 86, 21, 1, 84.50, 84.50, 5, 16),
(258, 86, 19, 9, 51.32, 461.88, 1, 7),
(259, 86, 11, 2, 62.03, 124.06, 7, 2),
(260, 86, 17, 2, 69.84, 139.68, 10, 8),
(261, 86, 26, 10, 35.81, 358.10, 8, 14),
(262, 87, 18, 4, 78.41, 313.64, 10, 10),
(263, 87, 18, 10, 71.49, 714.90, 8, 4),
(264, 88, 22, 9, 84.95, 764.55, 1, 1),
(265, 88, 16, 10, 48.12, 481.20, 2, 6),
(266, 88, 24, 5, 99.68, 498.40, 8, 12),
(267, 88, 21, 6, 27.90, 167.40, 1, 1),
(268, 89, 11, 2, 27.75, 55.50, 8, 13),
(269, 90, 18, 4, 21.67, 86.68, 3, 19),
(270, 90, 18, 5, 26.73, 133.65, 4, 10),
(271, 90, 22, 5, 79.34, 396.70, 7, 3),
(272, 90, 22, 3, 10.25, 30.75, 5, 18),
(273, 90, 27, 3, 82.82, 248.46, 9, 6),
(275, 92, 20, 10, 72.84, 728.40, 7, 13),
(276, 92, 30, 9, 97.24, 875.16, 6, 17),
(277, 92, 27, 8, 23.98, 191.84, 2, 2),
(278, 92, 15, 1, 83.76, 83.76, 6, 3),
(279, 92, 17, 1, 37.37, 37.37, 4, 14),
(280, 93, 27, 2, 22.78, 45.56, 10, 3),
(281, 93, 16, 6, 61.86, 371.16, 9, 5),
(282, 93, 25, 2, 80.10, 160.20, 7, 4),
(283, 93, 22, 1, 99.92, 99.92, 9, 18),
(284, 93, 11, 2, 38.76, 77.52, 2, 17),
(285, 94, 23, 5, 87.71, 438.55, 2, 11),
(286, 94, 23, 5, 58.46, 292.30, 4, 11),
(288, 94, 25, 8, 31.90, 255.20, 5, 2),
(289, 94, 27, 2, 58.08, 116.16, 5, 18),
(290, 95, 14, 6, 40.21, 241.26, 10, 16),
(291, 95, 21, 2, 87.89, 175.78, 4, 15),
(292, 96, 27, 7, 22.71, 158.97, 10, 2),
(293, 96, 26, 7, 87.34, 611.38, 6, 7),
(294, 96, 25, 4, 55.15, 220.60, 1, 2),
(295, 96, 26, 4, 99.94, 399.76, 1, 7),
(296, 96, 26, 2, 96.45, 192.90, 7, 10),
(298, 98, 22, 8, 77.93, 623.44, 7, 16),
(299, 98, 25, 1, 82.83, 82.83, 5, 18),
(300, 98, 16, 6, 32.45, 194.70, 1, 18),
(302, 98, 21, 10, 70.55, 705.50, 3, 5),
(303, 99, 27, 9, 75.48, 679.32, 4, 18),
(304, 99, 27, 2, 46.14, 92.28, 4, 10),
(305, 99, 14, 9, 96.88, 871.92, 2, 10),
(307, 199, 16, 1, 16.00, 16.00, 21, 44),
(309, 201, 14, 1, 14.00, 14.00, 21, 36),
(310, 201, 14, 1, 14.00, 14.00, 21, 38),
(311, 202, 18, 1, 12.50, 12.50, 21, 57),
(312, 203, 18, 1, 12.50, 12.50, 21, 58),
(313, 205, 18, 1, 12.50, 12.50, 21, 58),
(314, 206, 18, 2, 12.50, 25.00, 21, 58);

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `Payment_Method_ID` int(11) NOT NULL,
  `Method_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`Payment_Method_ID`, `Method_Name`) VALUES
(1, 'Credit/Debit Card'),
(2, 'PayPal'),
(3, 'Cash on Delivery'),
(4, 'KBZPay'),
(5, 'WavePay'),
(6, 'AYA Pay');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `Product_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Category_ID` int(11) DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Admin_User_ID` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_latest` tinyint(1) DEFAULT 0,
  `Image_Path` varchar(255) DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`Product_ID`, `Name`, `Category_ID`, `Price`, `Admin_User_ID`, `brand_id`, `Description`, `created_at`, `is_latest`, `Image_Path`, `is_popular`) VALUES
(11, 'Blur Fudge Tint', 9, 14.00, 2, 21, 'Fudge spreading on your lips! It\'s a completely matte finish without any glow.· Fudge spreading on your lips.· Matte gradation smudges easily and smoothly.· It spreads out without clumping, and the fudge · spreads out easily.· It\'s a completely matte finish without any glow· Finish with getting blurry the more you spread it.', '2025-01-25 13:14:34', 1, '../uploads/products/blur_fudge.jpg', 1),
(14, 'Glasting Color Gloss', 9, 14.00, 2, 21, '💄Plump and juicy glow just like an angel ring on your lips- Angel ring glow filling in and smoothing out the lip lines as if being sugar coated- Plump and voluminous glow that no other tint has ever had- Plump and juicy finish from any angle due to high refractive oil💄Vivid and transparent color on your lips- Pure and clear on first swatch, getting bold and vivid once layered- Transparent and vibrant color chart that could not be found in other glosses💄Not too watery or sticky, perfect texture on your lips- Smooth and gliding texture due to high-viscosity wax and moisturizing essential oil- Lightweight and comfortable, but stays put on the lips', '2025-01-25 22:27:05', 1, '../uploads/products/glasting.webp', 1),
(15, 'Zero Cushion', 2, 24.00, 2, 21, 'A semi-matte cushion with Air-Light System offers burden-less, flawless base makeup without the need for concealer and touch-up!<br /><br />Cyclopentasiloxane, Titanium Dioxide (CI 77891), Butylene Glycol, Dicaprylyl Carbonate, Phenyl Trimethicone, Butylene Glycol Dicaprylate/Dicaprate, Polypropylsilsesquioxane, Niacinamide, Pentylene Glycol, Trimethylsiloxysilicate, Isododecane, PEG-10 Dimethicone, Magnesium Sulfate, Disteardimonium Hectorite, Methyl Methacrylate Crosspolymer, Triethoxycaprylylsilane, Stearic Acid, Ethylhexylglycerin, Adenosine, Disodium EDTA, Phenoxyethanol, Citronellol, Geraniol, Water, Lauryl Glucoside, Aluminum Hydroxide, Iron Oxide Yellow (CI 7492), PEG-10 Tris(Trimethylsiloxy)silylethyl Dimethicone, Fragrance(Parfum), Purified Water, Black Oxide of Iron (CI 7499), Aluminium Hydroxide, Iron Oxides (CI 77491), Fragrance, Red Oxide of Iron (CI 77491), Black Oxide of Iron (CI 77499), Iron Oxides (CI 77499), Lauryl PEG-10 Tris(Trimethylsiloxy)silylethyl Dimethicone, Iron Oxide Yellow (CI 77492), Tocopherol, Iron Oxides (CI 77492), Lauryl PEG-10 Trissilylethyl Dimethicone', '2025-01-25 22:51:48', 0, '../uploads/products/zerocusion.webp', 1),
(16, 'Bare Water Cushion', 2, 16.00, 2, 21, '💙Fresh moisture without any glitter.<br />💙Light and fresh water base.<br />💙Moisture replenishment as soon as applied, comfort without tightness.<br />💙Adheres without stickiness and stays fresh all day long.<br /><br />01.PORCELAIN 17: Bright ivory color with a hint of pink, perfect for bright skin tones<br /><br />02.PURE 21: A natural pink ivory color without yellow undertones. Perfect for those who have used No.21 before<br /><br />03.NATURAL 21: Neutral ivory shade that is neither red nor yellow. Recommended for those with a shade #21 who are looking for a natural skin tone rather than a brightened look<br /><br />04.BEIGE 23: A calm natural beige color. Perfect for those who find the standard shade #21 on the market to be too bright<br /><br />05.SAND 25: Healthy, warm neutral beige tone. Suitable for those who want to achieve a healthy and warm toned skin', '2025-01-25 23:04:22', 1, '../uploads/products/watercushion.webp', 1),
(17, 'Han All Flat Brow', 12, 12.00, 3, 21, 'Draw natural-looking brows with flat shaped lead! Formulas with coating film and resists sweat and smudging.  ·10g ·1.5mm pencil, pre-loaded powder, and spoolie-·brush in one tool ·Formula with coating film and oil resists sweat and ·smudging ·Available in six shades for every skin tone and hair color   01. Use the built-in spoolie-brush to straighten the eyebrow. 02. Use edge of blade to shape and define the eyebrow. 03. Fill the sparse area with rectangular lead. Blend and groom the eyebrow with the built-in spoolie-brush for polished finish.', '2025-01-25 23:12:45', 0, '../uploads/products/flatbrow.webp', 1),
(18, 'Han All Sharp Brow', 12, 12.50, 3, 21, 'A three-pronged attack for gorgeous eyebrows! Design detailed strokes with a 1.5mm tip and shading without flattening your eyebrows texture. Comes with a powder brush tip to minimize product fallout.01. Use the built-in spoolie-brush to straighten the eyebrow.02. Use 1.5 mm pencil to shape and define the eyebrow.03. Fill the sparse area with the sponge-tip pre-loaded with the powder.04. Blend and groom the eyebrow with the built-in spoolie-brush for polished finish.', '2025-01-25 23:28:27', 1, '../uploads/products/brow.webp', 1),
(19, 'Twinkle Pen Liner', 7, 10.00, 2, 21, 'Shimmery and sparkling daily glitter that you can put anywhere you want! Super fine pen brush offers soft and easy application as well. Easy for beginners to try makeups on inner corner and lower eyelids . Glides on smoothly without skipping', '2025-01-25 23:39:51', 1, '../uploads/products/4_58e8de17-a378-4bf6-9cf8-83bc7e2930b6.webp', 1),
(20, 'Han All Fix Mascara 7g', 19, 15.00, 3, 21, 'A formulation designed to achieve oil and water resistance and no clumping or smudging. rom&nd Han All Fix Mascara delivers sharp styling with long-lasting hold and all-day definition.Gives a long-lasting finish without clumping and smudgingCreates sharp-looking lashes without worries of droopingEasy to wipe off with eye makeup remover and leaves no irritation to the delicate eye areaThe curling fixation lasts long without being sagged100 survey members show the satisfaction on its curling fastening powerA “SMART PROOF” prevents the smudges by water and oil', '2025-01-25 23:48:18', 1, '../uploads/products/mascara.webp', 1),
(21, 'Bare Layer Palette', 8, 25.00, 2, 21, 'rom&nd\'s New Bare Layer Palette is fruit-inspired vegan palette features seven matte and glittery formulations for eye and cheek makeup. it is formulated with ultra-fine powders enables even color blending without any caking.It also offers both warm-tone and cool-tone color collections to create natural makeup that fits you well. The bare matte shadows blur fine lines with outstanding adherence for long-lasting makeup. it refined and glowy glitters help create clear and sparkling looks.#01 Apricot Mood: Refreshing peachy palette for warm tone#02 Strawberry Mood: Lovely strawberry palette for cool tone', '2025-01-25 23:54:09', 1, '../uploads/products/eyeshadow.webp', 1),
(23, 'Better Than Palette Secret Garden', 8, 26.00, 2, 21, 'rom&nd\'s Secret Garden eyeshadow palettes provide remarkable contrast between dark and light shades for improved layerability. In #Mahogany Garden, you get an impressive spectrum of honey golds ranging from light beige to deep umber in matte and glittery finishes.7.5g·Ultra-fine particles with rom&nd’s matted formula·Dewy glitters with long-lasting formula·The wide color spectrum for all skin tones·Each palette includes 10 colors·Contains a mix of matte and glittered shades01. Apply it with your fingertips as per the shadow order.02. Complete shadowing outer corner of eyes or lash line with sharped and detailed brush.03. Tap the applied area with your fingertips and press it with the glitters in order to make it completely fixed.', '2025-01-26 00:40:31', 1, '../uploads/products/palette.webp', 1),
(24, 'Dior Addict Lip Glow Oil - 6ml', 16, 40.00, 3, 1, 'The Dior Addict Lip Glow lip balm is available as a glossy lip oil that deeply protects and enhances the lips, lastingly bringing out their natural color.\r\n\r\nGenuine lip care infused with cherry oil, Dior Addict Lip Glow Oil instantly nourishes, protects, softens and revitalizes the lips.\r\n\r\n1. Can be worn on its own for a glossy, mirror-shine effect.\r\n2. Can be worn as a primer by removing excess product before applying lipstick, for smoothed and hydrated lips.\r\n3. Can be worn over the corresponding shade of Dior Addict Lip Glow to play with the color and shine.', '2025-01-26 00:55:29', 1, '../uploads/products/lip.jpg', 0),
(25, 'Dior Addict Lip Tint', 9, 45.00, 2, 1, 'Dior Addict Lip Tint is the first no-transfer Dior lip tint with 12h* wear that hydrates lips for 24h.**Composed with 95%*** natural-origin ingredients and infused with Cherry Oil, this Dior lip tint fuses with the lips for a comfortable bare-lip sensation. It is also characterized by a semi-matte color finish.The Dior Addict Lip Tint applicator delivers precise and optimal application of the lip tint, for an even makeup finish.* Instrumental test on 11 subjects.** Instrumental test on 25 subjects.*** Value calculated based on ISO standards 16128-1 and 16128-2. Percentage of water included. The remaining 5% contribute to the formula’s performance, sensory appeal and stability.', '2025-01-26 01:04:47', 1, '../uploads/products/lip.webp', 1),
(26, 'Rouge Dior 3.5g', 9, 49.00, 2, 1, 'Rouge Dior, the Dior makeup icon, reveals a wide range of shades with 3 unique¹ finishes. The longwear lipstick offers 24 hours of comfort² thanks to its formula infused with hydrating floral lip care and a texture that leaves lips feeling soft. The lipstick is housed in an elegant case with a contemporary design.\r\n\r\nLips appear plump and smooth, embellished with a soft, matte effect for the velvet finish, a bright, creamy effect for the satin finish and a blurring and smoothing matte effect for the veil finish.\r\n\r\n1. Apply the Rouge Dior balm as a hydrating base. Use a tissue to remove any excess product before applying makeup to the lips.\r\n2. Line the lips with Rouge Dior Contour.\r\n3. For an even and sophisticated makeup result, apply Rouge Dior from the center of the lips outwards.', '2025-01-26 01:14:46', 1, '../uploads/products/dior.jpg', 0),
(27, 'Dior Forever Skin Glow', 1, 52.00, 3, 1, 'Dior Forever Skin Glow is the radiant foundation by Dior that offers the complexion high perfection with 24h* wear, without touch-ups, and acts like a serum to reveal all of the skin\'s natural luminosity. The complexion is more even and smoothed.Its formula composed of an 86%** skincare base infused with floral extracts helps to hydrate the skin intensely, let it breathe and durably improve its quality. Formulated to resist even hot and humid conditions, this fluid foundation reveals a radiant makeup finish from morning to night.In just 1 week, the skin is visibly more hydrated and plumped.**In just 1 month, the skin is more beautiful and more', '2025-01-26 01:25:21', 1, '../uploads/products/Anya_Yara_1850x2000.webp', 1),
(28, 'Dior Forever Couture Perfect Cushion - 14g', 2, 56.00, 2, 1, 'Dior takes long-wear high perfection to the next level with an improved formula in an especially radiant and spectacular luminous matte finish with Dior Forever Couture Perfect Cushion. For the first time in a Dior cushion foundation, 24h* of high seduction combined with 24h** of hydration for sensational comfort and freshness that last all day long.Dior Forever Couture Perfect Cushion is housed in a unique slim on-the-go case that offers even greater couture style.The case is refillable to reduce your environmental footprint.Used alone in the morning, Dior Forever Couture Perfect Cushion ensures a fresh and flawless makeup result.Used with Dior Forever foundation, it delivers +45% hydration* for sensational comfort all day long.It is also ideal for touch-ups to refresh the complexion at any time of day.* Instrumental test after 24 hours on 10 women for Dior Forever.', '2025-01-26 01:39:50', 1, '../uploads/products/dior (1).webp', 1),
(30, 'CLIO Sharp So Simple Waterproof Pencil Liner (Reformulated)', 7, 11.00, 2, 26, 'Made with sliding film former that can adhere around eyes like a film so that it does not get smudged by water, sweat, and sebum.\r\n\r\n1. Gently draw a line from corner of eyes to end of eyes.\r\n2. Draw a line under fat under eyes and then blend to create contour.\r\n3. Apply creamy ivory shade on fat under eyes for volumizing effect.\r\n\r\n', '2025-01-27 11:45:12', 0, '../uploads/products/f279b55a-55b7-4fdc-a40f-d8ee7fa5085e.webp', 0),
(31, 'rom&nd Zero Matte Lipstick', 9, 14.00, 2, 21, '·The chicest lipstick that you’ll carry everywhere and want to use every time·This light as a feather, soft as butter lipstick gently glides on your lips without flaking, drying, or caking·All gorgeous colors instantly give you a chic, runway-worthy look·Intense payoff with a velvety, matte finish·An ultra-light, ultra-adhesive formula·Lightweight & real matte texture', '2025-01-27 21:25:42', 0, '../uploads/products/zeromattelipstick_nude.webp', 1),
(32, 'LES BEIGES BRONZING CREAM', 4, 48.00, 2, 2, 'CREAM-GEL BRONZER FOR A HEALTHY SUN-KISSED GLOW', '2025-01-30 08:17:40', 1, '../uploads/products/chanel_ma2022_24_0050_1_rgbtif_jpeg-haute-definition-LD-571x740.jpg', 0),
(33, 'LES BEIGES WATER-FRESH TINT', 1, 55.00, NULL, 2, 'WATER-FRESH TINT WITH MICRO-DROPLET PIGMENTS. BARE SKIN EFFECT. NATURAL AND LUMINOUS HEALTHY GLOW.', '2025-01-30 08:22:17', 0, '../uploads/products/9533150232606.webp', 0),
(35, 'ROUGE COCO BAUME – SHINE', 9, 36.00, 3, 2, 'HYDRATING BEAUTIFYING TINTED LIP BALM – BUILDABLE COLOUR\r\n\r\nApply the lip balm straight from the case in a single swipe.\r\n\r\nROUGE COCO BAUME can be worn on its own or in combination with other shades to either illuminate or intensify your makeup look.\r\n\r\nFor a more diffused look, use your finger to blur the edges.\r\n\r\nChoose the intensity of your lip colour:\r\n\r\nOne swipe for a subtle hint of colour.\r\n\r\nThree swipes for coverage that is nearly as intense as a lipstick.', '2025-01-30 08:31:30', 1, '../uploads/products/1732802135042-onepdpeditopushdesktopmobile01974x1298px2jpg_1299x974.webp', 0),
(36, 'JEUX DE LUMIÈRES', 8, 75.00, 2, 2, 'MULTI-USE EYESHADOW AND HIGHLIGHTER PALETTE\r\n\r\nCombine, blend or layer the shades together to create a variety of effects. The powders can be used to illuminate the face with a soft glow. Apply before or after blush, or layer for a luminous effect.', '2025-01-30 08:37:29', 1, '../uploads/products/9556751974430.webp', 0),
(37, 'M·A·CXIMAL SILKY MATTE LIPSTICK', 9, 25.00, 4, 2, 'A silky matte lipstick that delivers 12 hours of full-coverage colour and eight hours of moisture.', '2025-01-30 08:43:06', 1, '../uploads/products/2000736770_170999881_06.avif', 0),
(38, 'CANDY GLOW TINTED BUTTER BALM', 9, 40.00, 3, 5, 'Soft shine balm with a smooth buttery feel for a healthy flush of color.\r\n\r\nGet all day care in a rich balm with a soft tint of color. Formulated with maracuja oil and shea butter, Candy Glow Balm adds immediate moisture for smoother looking lips over time. Ph adjusting pigments reveal a custom shade for each person across all skin tones. The lightweight, clean formula also acts as a lip primer without smudging.\r\n\r\nUp to 24 hours of deep lip care\r\nInstantly, lips feel hydrated and moisturized.\r\nAfter 2 weeks of use, lip folds appear smoother and lips have improved texture.\r\nClean formula with up to 98% natural origin ingredients. Paraben-free, sulfate-free.\r\n*Consumer test of 116 women over 14 days', '2025-01-30 08:49:51', 1, '../uploads/products/ysl-candy-glow-tinted-butter-balm-44b-view.jpg', 0),
(40, 'Powder Blush', 5, 34.00, 2, 7, 'Up to 16-hour wear\r\nTrue-color payoff\r\nComfortable, weightless feel\r\nSilky, blendable application\r\nBlurs imperfections\r\nVegan formula\r\n0.17 oz / 4.8 g', '2025-01-30 09:03:55', 1, '../uploads/products/999NAC0000192_3.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `shade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `shade_id`) VALUES
(28, 11, '../uploads/products/blur_fudge1.webp', 14),
(29, 11, '../uploads/products/blur_fudge2.webp', 15),
(30, 11, '../uploads/products/blur_fudge3.webp', 16),
(31, 11, '../uploads/products/blur_fudge4.webp', 17),
(32, 11, '../uploads/products/blur_fudge5.webp', 18),
(33, 11, '../uploads/products/blur_fudge6.webp', 19),
(34, 11, '../uploads/products/blur_fudge7.webp', 20),
(35, 11, '../uploads/products/blur_fudge8.webp', 21),
(36, 11, '../uploads/products/blur_fudge9.webp', 22),
(37, 11, '../uploads/products/blur_fudge10.webp', 23),
(38, 11, '../uploads/products/blur_fudge11.webp', 24),
(49, 14, '../uploads/products/glasting1.webp', 34),
(50, 14, '../uploads/products/glasting2.webp', 35),
(51, 14, '../uploads/products/glasting3.webp', 36),
(52, 14, '../uploads/products/glasting4.webp', 37),
(53, 14, '../uploads/products/glasting5.webp', 38),
(54, 14, '../uploads/products/glasting6.webp', 39),
(55, 15, '../uploads/products/zerocusion1.webp', 40),
(56, 15, '../uploads/products/zerocusion2.webp', 41),
(57, 15, '../uploads/products/zerocusion3.webp', 42),
(58, 16, '../uploads/products/watercushion1.webp', 43),
(59, 16, '../uploads/products/watercushion2.webp', 44),
(60, 16, '../uploads/products/watercushion3.webp', 45),
(61, 16, '../uploads/products/watercushion4.webp', 46),
(62, 17, '../uploads/products/flatbrow1.webp', 47),
(63, 17, '../uploads/products/flatbrow2.webp', 48),
(64, 17, '../uploads/products/flatbrow3.webp', 49),
(65, 17, '../uploads/products/flatbrow4.webp', 50),
(66, 17, '../uploads/products/flatbrow5.webp', 51),
(67, 17, '../uploads/products/flatbrow6.webp', 52),
(68, 18, '../uploads/products/c1_classic_gray_01.webp', 53),
(69, 18, '../uploads/products/c2_grace_taupe_01.webp', 54),
(70, 18, '../uploads/products/c3_modern_beige_01.webp', 55),
(71, 18, '../uploads/products/w1_gentle_brown_01.webp', 56),
(72, 18, '../uploads/products/w2_mild_woody_01.webp', 57),
(73, 18, '../uploads/products/w3_merry_blondy_01.webp', 58),
(74, 19, '../uploads/products/liner1.webp', 59),
(75, 19, '../uploads/products/liner2.webp', 60),
(76, 19, '../uploads/products/liner3.webp', 61),
(77, 19, '../uploads/products/liner4.webp', 62),
(78, 19, '../uploads/products/liner5.webp', 63),
(79, 20, '../uploads/products/01_long_black_01.webp', 64),
(80, 20, '../uploads/products/02_long_hazel_01.webp', 65),
(81, 20, '../uploads/products/03_long_ash_01.webp', 66),
(82, 20, '../uploads/products/04_volume_black_01.webp', 67),
(83, 21, '../uploads/products/eyeshadow1.webp', 68),
(84, 21, '../uploads/products/eyeshadow2.webp', 69),
(91, 23, '../uploads/products/1_pampas_garden_00.webp', 76),
(92, 23, '../uploads/products/2_mahogany_garden_00.webp', 77),
(93, 23, '../uploads/products/3_rosebud_garden_00.webp', 78),
(94, 23, '../uploads/products/4_dusty_fog_garden_00.webp', 79),
(95, 23, '../uploads/products/05_shade-_-shadow-garden_00.webp', 80),
(96, 23, '../uploads/products/6_peony-nude-garden_00.webp', 81),
(97, 24, '../uploads/products/lip1.jpg', 82),
(98, 24, '../uploads/products/pink.webp', 83),
(99, 24, '../uploads/products/clear.jpg', 84),
(100, 24, '../uploads/products/cherry.webp', 85),
(101, 24, '../uploads/products/rosewood.jpg', 86),
(102, 25, '../uploads/products/351.webp', 87),
(103, 25, '../uploads/products/761.webp', 88),
(104, 25, '../uploads/products/541.webp', 89),
(105, 25, '../uploads/products/651.webp', 90),
(106, 25, '../uploads/products/491.webp', 91),
(107, 26, '../uploads/products/277.jpg', 92),
(108, 26, '../uploads/products/840.jpg', 93),
(109, 26, '../uploads/products/221.webp', 94),
(110, 26, '../uploads/products/773.jpg', 95),
(111, 26, '../uploads/products/866.webp', 96),
(112, 26, '../uploads/products/824.webp', 97),
(113, 26, '../uploads/products/678.jpg', 98),
(114, 26, '../uploads/products/343.webp', 99),
(115, 27, '../uploads/products/2n.webp', 100),
(116, 27, '../uploads/products/2w.jpg', 101),
(117, 27, '../uploads/products/1w.jpg', 102),
(118, 27, '../uploads/products/1n.webp', 103),
(119, 27, '../uploads/products/35n.jpg', 104),
(120, 28, '../uploads/products/00.webp', 105),
(121, 28, '../uploads/products/1cr.jpg', 106),
(122, 28, '../uploads/products/1n.jpg', 107),
(123, 28, '../uploads/products/1w.webp', 108),
(124, 28, '../uploads/products/2n.jpg', 109),
(125, 28, '../uploads/products/3n.jpg', 110),
(133, 30, '../uploads/products/clio1.webp', 118),
(134, 30, '../uploads/products/clio2.webp', 119),
(135, 30, '../uploads/products/clio3.webp', 120),
(136, 30, '../uploads/products/clio4.webp', 121),
(137, 30, '../uploads/products/clio5.webp', 122),
(138, 30, '../uploads/products/clio6.webp', 123),
(139, 31, '../uploads/products/01_dusty_pink_01.webp', 124),
(140, 31, '../uploads/products/03_silhouette_01.webp', 125),
(141, 31, '../uploads/products/08_adorable_01.webp', 126),
(142, 31, '../uploads/products/10_pink_sand_01.webp', 127),
(143, 31, '../uploads/products/11_sunlight_01.webp', 128),
(144, 31, '../uploads/products/12_something_01.webp', 129),
(145, 31, '../uploads/products/14_sweet_p_01.webp', 130),
(146, 31, '../uploads/products/20_red_dive_01.webp', 131),
(147, 32, '../uploads/products/chanel_bronzer.avif', 132),
(148, 32, '../uploads/products/395.avif', 133),
(149, 32, '../uploads/products/395.avif', 134),
(150, 33, '../uploads/products/light.avif', 135),
(151, 33, '../uploads/products/Mlight.avif', 136),
(152, 33, '../uploads/products/Medium.avif', 137),
(153, 33, '../uploads/products/medium plus.avif', 138),
(157, 35, '../uploads/products/rouge-coco-baume-shine-hydrating-beautifying-tinted-lip-balm-buildable-colour-752-honey-bliss-0-1oz--packshot-default-171752-9551633874974 (1).avif', 142),
(158, 35, '../uploads/products/rouge-coco-baume-shine-hydrating-beautifying-tinted-lip-balm-buildable-colour-754-tender-peach-0-1oz--packshot-default-171754-9551632433182.avif', 143),
(159, 35, '../uploads/products/rouge-coco-baume-shine-hydrating-beautifying-tinted-lip-balm-buildable-colour-756-cherry-burst-0-1oz--packshot-default-171756-9551637315614.avif', 144),
(160, 35, '../uploads/products/rouge-coco-baume-shine-hydrating-beautifying-tinted-lip-balm-buildable-colour-758-blushing-pink-0-1oz--packshot-default-171758-9551633252382.avif', 145),
(161, 36, '../uploads/products/jeux-de-lumieres-multi-use-eyeshadow-and-highlighter-palette-0-42oz--packshot-default-151968-9551634825246.avif', 146),
(162, 37, '../uploads/products/mac_sku_NY9N12_1x1_0.avif', 147),
(163, 37, '../uploads/products/mac_sku_NY9N24_1x1_0.avif', 148),
(164, 37, '../uploads/products/mac_sku_NY9N37_1x1_0.avif', 149),
(165, 37, '../uploads/products/mac_sku_NY9NHW_1x1_0.avif', 150),
(166, 37, '../uploads/products/mac_sku_NY9NP6_1x1_0.avif', 151),
(167, 37, '../uploads/products/mac_sku_NY9NP8_1x1_0.avif', 152),
(168, 38, '../uploads/products/3614274128260.jpg', 153),
(169, 38, '../uploads/products/3614274128277.jpg', 154),
(170, 38, '../uploads/products/3614274128307.webp', 155),
(171, 38, '../uploads/products/3614274128307.webp', 156),
(173, 40, '../uploads/products/NARS_SP25_Blush_PDPCrop_Soldier_Swatch_AllureBeautySeal_Orgasm_GLBL.webp', 158),
(174, 40, '../uploads/products/NARS_SP25_Blush_PDPCrop_Soldier_Swatch_AllureBeautySeal_OrgasmEdge_GLBL.webp', 159),
(175, 40, '../uploads/products/NARS_SP25_Blush_PDPCrop_Soldier_Swatch_AllureBeautySeal_DeepThroat_GLBL.webp', 160),
(176, 40, '../uploads/products/NARS_SP25_Blush_PDPCrop_Soldier_Swatch_AllureBeautySeal_Thrill_GLBL.webp', 161);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `Review_ID` int(11) NOT NULL,
  `Product_ID` int(11) DEFAULT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL CHECK (`Rating` >= 1 and `Rating` <= 5),
  `Review_Text` text DEFAULT NULL,
  `Review_Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`Review_ID`, `Product_ID`, `Customer_ID`, `Rating`, `Review_Text`, `Review_Date`) VALUES
(1, 27, 28, 4, 'Terrible, wouldn\'t buy again.', '2024-08-25 11:57:17'),
(2, 27, 17, 2, 'Good quality, but could be improved.', '2024-06-16 21:41:07'),
(3, 30, 22, 4, 'Very satisfied, would buy again.', '2022-07-03 12:15:50'),
(4, 27, 21, 5, 'Terrible, wouldn\'t buy again.', '2024-06-11 20:30:52'),
(5, 30, 22, 5, 'Great product, highly recommend!', '2023-08-08 05:22:20'),
(6, 19, 17, 4, 'I bought one with Rosy Sparkle color. It\'s so pretty and I really love it!', '2025-01-26 21:20:54'),
(7, 23, 28, 3, 'Not as expected, but still decent.', '2024-01-30 00:29:21'),
(8, 30, 28, 5, 'Not as expected, but still decent.', '2022-07-03 22:45:43'),
(9, 19, 19, 5, 'At a reasonable price!!!', '2025-01-27 04:40:33'),
(11, 19, 25, 5, 'Terrible, wouldn\'t buy again.', '2024-10-06 18:07:13'),
(12, 19, 20, 4, 'Bought it for my friend. She really loves it!', '2025-01-27 04:46:57'),
(13, 22, 27, 2, 'Great product, highly recommend!', '2024-01-06 12:22:20'),
(14, 17, 26, 3, 'Good quality, but could be improved.', '2023-10-21 12:06:06'),
(15, 22, 20, 3, 'Not as expected, but still decent.', '2023-02-13 16:02:28'),
(16, 28, 29, 2, 'Very satisfied, would buy again.', '2023-01-07 01:10:10'),
(17, 11, 22, 5, 'Not as expected, but still decent.', '2023-12-15 04:48:19'),
(18, 18, 29, 2, 'Very satisfied, would buy again.', '2024-01-20 11:04:32'),
(19, 15, 21, 1, 'Terrible, wouldn\'t buy again.', '2024-04-08 11:37:52'),
(20, 30, 27, 1, 'Not as expected, but still decent.', '2023-12-09 00:40:55'),
(21, 15, 19, 5, 'Great product, highly recommend!', '2022-09-14 04:21:36'),
(22, 30, 20, 1, 'Good quality, but could be improved.', '2023-09-14 01:48:58'),
(23, 24, 24, 1, 'Good quality, but could be improved.', '2022-06-21 16:09:14'),
(24, 25, 20, 1, 'Terrible, wouldn\'t buy again.', '2023-07-24 23:57:24'),
(25, 16, 26, 1, 'Good quality, but could be improved.', '2023-08-20 14:59:59'),
(27, 25, 18, 1, 'Great product, highly recommend!', '2023-08-18 03:04:03'),
(28, 25, 27, 5, 'Terrible, wouldn\'t buy again.', '2022-01-26 12:02:13'),
(29, 14, 30, 2, 'Good quality, but could be improved.', '2024-06-27 19:03:14'),
(30, 16, 25, 4, 'Great product, highly recommend!', '2023-06-23 15:29:30'),
(31, 24, 18, 3, 'Good quality, but could be improved.', '2022-05-26 10:56:06'),
(32, 24, 30, 3, 'Great product, highly recommend!', '2023-01-01 02:16:06'),
(33, 21, 22, 5, 'Great product, highly recommend!', '2023-10-27 18:02:30'),
(34, 25, 20, 5, 'Great product, highly recommend!', '2022-10-11 19:22:20'),
(36, 15, 28, 4, 'Great product, highly recommend!', '2022-02-18 21:53:53'),
(37, 25, 29, 4, 'Terrible, wouldn\'t buy again.', '2023-07-29 13:28:37'),
(38, 24, 20, 1, 'Not as expected, but still decent.', '2022-10-23 12:45:04'),
(39, 17, 17, 1, 'Great product, highly recommend!', '2023-03-30 02:37:12'),
(40, 11, 24, 2, 'Good quality, but could be improved.', '2023-05-25 02:52:59'),
(41, 16, 20, 4, 'Good quality, but could be improved.', '2024-11-07 01:09:29'),
(42, 19, 20, 1, 'Great product, highly recommend!', '2023-01-28 15:27:09'),
(43, 24, 25, 1, 'Not as expected, but still decent.', '2022-09-07 11:54:53'),
(44, 21, 25, 4, 'Terrible, wouldn\'t buy again.', '2024-01-05 21:57:20'),
(45, 11, 29, 5, 'Great product, highly recommend!', '2024-02-15 22:26:39'),
(46, 28, 21, 5, 'Great product, highly recommend!', '2024-05-13 22:02:34'),
(47, 11, 20, 3, 'Good quality, but could be improved.', '2023-06-04 13:57:50'),
(48, 15, 30, 1, 'Very satisfied, would buy again.', '2024-10-21 16:38:14'),
(49, 19, 22, 3, 'Great product, highly recommend!', '2024-08-01 23:15:41'),
(50, 18, 24, 2, 'Terrible, wouldn\'t buy again.', '2022-06-14 20:28:55'),
(51, 25, 23, 2, 'Good quality, but could be improved.', '2024-03-20 03:37:54'),
(52, 14, 26, 5, 'Terrible, wouldn\'t buy again.', '2023-06-08 15:55:22'),
(53, 26, 25, 1, 'Great product, highly recommend!', '2024-10-18 21:17:46'),
(54, 14, 29, 1, 'Terrible, wouldn\'t buy again.', '2022-11-15 19:58:26'),
(56, 20, 18, 4, 'Good quality, but could be improved.', '2022-06-11 15:16:30'),
(57, 26, 29, 3, 'Good quality, but could be improved.', '2022-05-31 09:21:55'),
(58, 28, 23, 1, 'Good quality, but could be improved.', '2024-09-18 17:59:08'),
(59, 25, 26, 2, 'Not as expected, but still decent.', '2023-11-07 03:02:05'),
(61, 22, 29, 2, 'Not as expected, but still decent.', '2023-11-28 17:46:13'),
(62, 26, 20, 4, 'Very satisfied, would buy again.', '2024-07-05 14:15:30'),
(63, 14, 28, 2, 'Good quality, but could be improved.', '2023-02-18 14:13:09'),
(64, 16, 24, 2, 'Terrible, wouldn\'t buy again.', '2024-08-18 06:20:54'),
(65, 28, 21, 1, 'Good quality, but could be improved.', '2025-01-18 10:00:49'),
(66, 17, 28, 3, 'Terrible, wouldn\'t buy again.', '2023-10-14 18:17:13'),
(67, 25, 25, 4, 'Great product, highly recommend!', '2022-03-08 20:52:16'),
(68, 14, 29, 3, 'Terrible, wouldn\'t buy again.', '2023-02-10 05:05:25'),
(69, 25, 21, 2, 'Very satisfied, would buy again.', '2023-03-17 09:52:07'),
(70, 16, 19, 3, 'Great product, highly recommend!', '2022-08-19 17:48:13'),
(71, 19, 18, 5, 'Very satisfied, would buy again.', '2022-08-29 19:56:21'),
(72, 25, 28, 3, 'Not as expected, but still decent.', '2024-04-16 09:44:48'),
(73, 26, 30, 1, 'Great product, highly recommend!', '2023-09-02 10:14:34'),
(74, 27, 30, 2, 'Good quality, but could be improved.', '2022-08-15 17:55:26'),
(75, 19, 30, 3, 'Not as expected, but still decent.', '2021-12-31 21:17:38'),
(76, 21, 30, 2, 'Great product, highly recommend!', '2022-04-11 05:49:31'),
(77, 23, 24, 4, 'Good quality, but could be improved.', '2024-06-26 20:12:28'),
(78, 16, 24, 3, 'Good quality, but could be improved.', '2023-05-09 01:12:21'),
(79, 11, 30, 5, 'Great product, highly recommend!', '2022-12-16 20:01:55'),
(80, 19, 21, 4, 'Not as expected, but still decent.', '2022-06-23 07:39:58'),
(81, 28, 29, 5, 'Great product, highly recommend!', '2022-03-15 03:39:34'),
(82, 11, 27, 1, 'Good quality, but could be improved.', '2022-05-14 02:39:16'),
(83, 26, 18, 4, 'Terrible, wouldn\'t buy again.', '2025-01-14 02:00:07'),
(84, 18, 28, 1, 'Great product, highly recommend!', '2024-12-12 09:36:54'),
(85, 19, 20, 4, 'Not as expected, but still decent.', '2023-01-20 01:00:20'),
(86, 18, 20, 5, 'Very satisfied, would buy again.', '2023-05-01 23:43:01'),
(87, 19, 30, 5, 'Great product, highly recommend!', '2023-03-13 09:38:35'),
(88, 18, 30, 2, 'Not as expected, but still decent.', '2022-01-25 03:12:47'),
(89, 22, 24, 4, 'Not as expected, but still decent.', '2024-04-19 23:01:42'),
(90, 30, 28, 1, 'Good quality, but could be improved.', '2024-04-25 18:02:59'),
(91, 27, 20, 2, 'Great product, highly recommend!', '2023-10-16 06:31:28'),
(92, 14, 26, 1, 'Very satisfied, would buy again.', '2024-06-23 22:39:15'),
(93, 28, 28, 4, 'Great product, highly recommend!', '2024-07-06 05:08:29'),
(94, 22, 27, 5, 'Great product, highly recommend!', '2024-11-15 13:38:27'),
(95, 19, 29, 1, 'Terrible, wouldn\'t buy again.', '2023-01-24 06:27:43'),
(96, 22, 23, 4, 'Terrible, wouldn\'t buy again.', '2023-10-23 10:22:51'),
(97, 20, 24, 1, 'Good quality, but could be improved.', '2025-01-04 17:43:06'),
(98, 21, 24, 2, 'Not as expected, but still decent.', '2024-02-25 07:34:47'),
(99, 25, 26, 2, 'Very satisfied, would buy again.', '2022-03-16 22:57:42'),
(100, 27, 21, 1, 'Good quality, but could be improved.', '2022-05-23 08:58:53');

-- --------------------------------------------------------

--
-- Table structure for table `shades`
--

CREATE TABLE `shades` (
  `shade_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `shade_name` varchar(255) NOT NULL,
  `Quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shades`
--

INSERT INTO `shades` (`shade_id`, `product_id`, `shade_name`, `Quantity`) VALUES
(14, 11, 'Pomeloco', 1),
(15, 11, 'Rosiental', 1),
(16, 11, 'Musky', 1),
(17, 11, 'Radwood', 1),
(18, 11, 'Bibi Candy', 1),
(19, 11, 'Mauvish', 0),
(20, 11, 'Cool Rose Up', 1),
(21, 11, 'Current Jam', 1),
(22, 11, 'Coral Jubilee', 1),
(23, 11, 'Fudge Red', 1),
(24, 11, 'Fuchsia Vibe', 1),
(34, 14, 'Peony Ballet', 13),
(35, 14, 'Nutty Vague', 15),
(36, 14, 'Rose Finch', 18),
(37, 14, 'Grapy Way', 0),
(38, 14, 'Dim Mauve', 17),
(39, 14, 'Deepen Moor', 7),
(40, 15, 'Pure', 20),
(41, 15, 'Natural', 15),
(42, 15, 'Beige', 18),
(43, 16, 'Porcelain 17', 18),
(44, 16, 'Pure 21', 24),
(45, 16, 'Beige 23', 28),
(46, 16, 'Sand 25', 30),
(47, 17, 'W1 Gentle Brown', 20),
(48, 17, 'W2 Mild Woody', 25),
(49, 17, 'W3 Merry Blondy', 25),
(50, 17, 'C1 Classic Gray', 30),
(51, 17, 'C2 Grace Taupe', 25),
(52, 17, 'C3 Modern Beige', 24),
(53, 18, 'C1 Classic Gray', 30),
(54, 18, 'C2 Grace Taupe', 30),
(55, 18, 'C3 Modern Beige', 24),
(56, 18, 'W1 Gentle Brown', 28),
(57, 18, 'W2 Mild Woody', 25),
(58, 18, 'W3 Merry Blondy', 32),
(59, 19, '01 Silver Flake', 19),
(60, 19, '02 Golden Wave', 21),
(61, 19, '03 Rosy Sparkle', 19),
(62, 19, '04 Midnight Ash', 24),
(63, 19, '05 Sunset Hazel', 21),
(64, 20, 'L01 Long Black', 25),
(65, 20, 'L02 Long Ash', 28),
(66, 20, 'L03 Long Hazel', 29),
(67, 20, 'V01 Volume Black', 32),
(68, 21, '01 Apricot Mood', 22),
(69, 21, '02 Strawberry Mood', 25),
(76, 23, '01 Pampas Garden', 25),
(77, 23, '02 Mahogany Garden', 24),
(78, 23, '03 Rosebud Garden', 22),
(79, 23, '04 Dusty Fog Garden', 26),
(80, 23, '05 Shade & Shadow Garden', 28),
(81, 23, '06 Peony Nude Garden', 27),
(82, 24, '007 Raspberry', 20),
(83, 24, '001 Pink', 22),
(84, 24, '000 Universal Clear', 24),
(85, 24, '015 Cherry', 26),
(86, 24, '012 Rosewood', 24),
(87, 25, '351 Natural Nude', 22),
(88, 25, '761 Natural Fuschia', 24),
(89, 25, '541 Natural Sienna', 19),
(90, 25, '651 Natural Rose', 20),
(91, 25, '491 Natural Rosewood', 21),
(92, 26, '277 Osée satiny finish ', 20),
(93, 26, '840 Rayonnante velvet finish', 22),
(94, 26, '221 Frou-frou velvet finish', 24),
(95, 26, '773 Bonheur velvet finish', 21),
(96, 26, '866 Together velvet finish', 25),
(97, 26, '824 Saint Germain velvet finish', 24),
(98, 26, '678 Culte satiny finish', 12),
(99, 26, '343 Panarea satin finish', 23),
(100, 27, '2N Neutral', 16),
(101, 27, '2W Warm', 18),
(102, 27, '1W Warm', 20),
(103, 27, '1N Neutral', 15),
(104, 27, '3,5N Neutral', 14),
(105, 28, '00 Neutral', 22),
(106, 28, '1CR Cool Rosy', 20),
(107, 28, '1N Neutral', 19),
(108, 28, '1W Warm', 16),
(109, 28, '2N Neutral', 25),
(110, 28, '3N Neutral', 24),
(118, 30, 'Black', 16),
(119, 30, 'Brown', 15),
(120, 30, 'Black brown', 20),
(121, 30, 'Dark brown', 19),
(122, 30, 'Ash brown', 17),
(123, 30, 'Creamy Ivory', 18),
(124, 31, '01 Dusty Pink', 20),
(125, 31, '03 Silhouette', 22),
(126, 31, '08 Adorable', 21),
(127, 31, '10 Pink Sand', 25),
(128, 31, '11 Sunlight', 24),
(129, 31, '12 Something', 20),
(130, 31, '14 Sweet P', 22),
(131, 31, '20 Red Dive', 24),
(132, 32, '390 - SOLEIL TAN BRONZE', 20),
(133, 32, '392 - SOLEIL TAN MEDIUM BRONZE', 22),
(134, 32, '395 - SOLEIL TAN DEEP BRONZE', 21),
(135, 33, 'Light', 20),
(136, 33, 'Medium Light', 22),
(137, 33, 'Medium', 25),
(138, 33, 'Medium Plus', 22),
(142, 35, '752 - HONEY BLISS', 20),
(143, 35, '754 - TENDER PEACH', 22),
(144, 35, '756 - CHERRY BURST', 24),
(145, 35, '758 - BLUSHING PINK', 25),
(146, 36, 'EYESHADOW AND HIGHLIGHTER PALETTE', 26),
(147, 37, 'Velvet Teddy', 20),
(148, 37, 'Lipstick Snob', 22),
(149, 37, 'No Coralation', 24),
(150, 37, 'Get the Hint?', 21),
(151, 37, 'Sweet Deal', 25),
(152, 37, 'You Wouldn\'t Get It', 23),
(153, 38, '1B - Pink Sunrise', 20),
(154, 38, '5B - Nude Crush', 22),
(155, 38, '44B- Nude Lavalliere', 25),
(156, 38, '3B - Rosewood Blush', 23),
(158, 40, '777 Orgasm', 25),
(159, 40, '778 Orgasm Edge', 22),
(160, 40, '237 Deep Throat', 24),
(161, 40, '950 Thrill', 26);

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `Shipping_ID` int(11) NOT NULL,
  `Order_ID` int(11) DEFAULT NULL,
  `Shipping_Status` varchar(50) DEFAULT 'Processing',
  `Shipping_Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Shipping_Method_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`Shipping_ID`, `Order_ID`, `Shipping_Status`, `Shipping_Date`, `Shipping_Method_ID`) VALUES
(290, 199, 'Delivered', '2025-01-27 16:49:41', 2),
(292, 201, 'Processing', '2025-01-27 17:30:00', 2),
(293, 202, 'Processing', '2025-01-27 17:30:00', 2),
(294, 203, 'Pending', '2025-01-28 03:59:27', 2),
(295, 204, 'Pending', '2025-01-28 04:02:38', 2),
(296, 205, 'Pending', '2025-01-28 04:02:55', 2),
(297, 206, 'Processing', '2025-01-27 17:30:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `shippingmethods`
--

CREATE TABLE `shippingmethods` (
  `Shipping_Method_ID` int(11) NOT NULL,
  `Shipping_Method` varchar(100) NOT NULL,
  `DeliveryTime` varchar(100) DEFAULT NULL,
  `Cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shippingmethods`
--

INSERT INTO `shippingmethods` (`Shipping_Method_ID`, `Shipping_Method`, `DeliveryTime`, `Cost`) VALUES
(1, 'Standard Shipping', '5-7 business days', 5.00),
(2, 'Express Shipping', '1-2 business days', 15.00),
(3, 'Overnight Shipping', '1 business day', 25.00),
(4, 'Free Shipping', '5-7 business days', 0.00),
(5, 'Same-Day Delivery', 'Same day (if ordered before 12 PM)', 20.00),
(6, 'Pick Up at Store', 'Same day or scheduled pickup', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `Cart_ID` int(11) NOT NULL,
  `Customer_ID` int(11) DEFAULT NULL,
  `Product_ID` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `shade_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`Admin_User_ID`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`Category_ID`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contactmessages`
--
ALTER TABLE `contactmessages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer_contact` (`customer_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`Coupon_ID`),
  ADD UNIQUE KEY `Coupon_Code` (`Coupon_Code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`Customer_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`FavouritesID`),
  ADD KEY `FK_Customer` (`Customer_ID`),
  ADD KEY `FK_Product` (`Product_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Order_ID`),
  ADD KEY `fk_customer` (`Customer_ID`),
  ADD KEY `fk_cupon_id` (`cupon_id`),
  ADD KEY `fk_shipping_id` (`shipping_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`Order_Item_ID`),
  ADD KEY `Order_ID` (`Order_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `fk_order_items_brand_id` (`brand_id`),
  ADD KEY `shade_id` (`shade_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`Payment_Method_ID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`Product_ID`),
  ADD KEY `fk_category` (`Category_ID`),
  ADD KEY `fk_products_admin` (`Admin_User_ID`),
  ADD KEY `fk_products_brand_id` (`brand_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_shade_id` (`shade_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`Review_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`);

--
-- Indexes for table `shades`
--
ALTER TABLE `shades`
  ADD PRIMARY KEY (`shade_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`Shipping_ID`),
  ADD KEY `Order_ID` (`Order_ID`),
  ADD KEY `FK_ShippingMethod` (`Shipping_Method_ID`);

--
-- Indexes for table `shippingmethods`
--
ALTER TABLE `shippingmethods`
  ADD PRIMARY KEY (`Shipping_Method_ID`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`Cart_ID`),
  ADD KEY `Customer_ID` (`Customer_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `Admin_User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `Category_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `contactmessages`
--
ALTER TABLE `contactmessages`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `Coupon_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `Customer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `favourites`
--
ALTER TABLE `favourites`
  MODIFY `FavouritesID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `Order_Item_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=315;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `Payment_Method_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `Product_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `Review_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `shades`
--
ALTER TABLE `shades`
  MODIFY `shade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `Shipping_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `shippingmethods`
--
ALTER TABLE `shippingmethods`
  MODIFY `Shipping_Method_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `Cart_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contactmessages`
--
ALTER TABLE `contactmessages`
  ADD CONSTRAINT `fk_customer_contact` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`Customer_ID`);

--
-- Constraints for table `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `FK_Customer` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`Customer_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Product` FOREIGN KEY (`Product_ID`) REFERENCES `products` (`Product_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_cupon_id` FOREIGN KEY (`cupon_id`) REFERENCES `coupons` (`Coupon_ID`),
  ADD CONSTRAINT `fk_shipping_id` FOREIGN KEY (`shipping_id`) REFERENCES `shipping` (`Shipping_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`Customer_ID`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `products` (`Product_ID`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`shade_id`) REFERENCES `shades` (`shade_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`Category_ID`) REFERENCES `categories` (`Category_ID`),
  ADD CONSTRAINT `fk_products_admin` FOREIGN KEY (`Admin_User_ID`) REFERENCES `admin_users` (`Admin_User_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_shade_id` FOREIGN KEY (`shade_id`) REFERENCES `shades` (`shade_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`Product_ID`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `products` (`Product_ID`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`Customer_ID`);

--
-- Constraints for table `shades`
--
ALTER TABLE `shades`
  ADD CONSTRAINT `shades_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`Product_ID`) ON DELETE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `FK_ShippingMethod` FOREIGN KEY (`Shipping_Method_ID`) REFERENCES `shippingmethods` (`Shipping_Method_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`Customer_ID`) REFERENCES `customers` (`Customer_ID`),
  ADD CONSTRAINT `shopping_cart_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `products` (`Product_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
