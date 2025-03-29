-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2025 at 08:03 PM
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
-- Database: `invoice`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(255) DEFAULT NULL,
  `activity_description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `activity_type`, `activity_description`, `timestamp`) VALUES
(1, 1, 'login', 'User logged in successfully.', '2025-03-24 18:45:56'),
(2, 1, 'login', 'User logged in successfully.', '2025-03-24 18:50:14'),
(3, 1, 'login', 'User logged in successfully.', '2025-03-24 18:50:24'),
(4, 1, 'login', 'User logged in successfully.', '2025-03-24 18:56:59'),
(5, 1, 'logout', 'User logged out successfully.', '2025-03-24 18:57:06'),
(6, 1, 'login', 'User logged in successfully.', '2025-03-24 18:57:08'),
(7, 1, 'logout', 'User logged out successfully.', '2025-03-24 18:57:24'),
(8, 1, 'login', 'User logged in successfully.', '2025-03-24 18:58:59'),
(9, 1, 'logout', 'User logged out successfully.', '2025-03-24 19:01:16'),
(10, 1, 'login', 'User logged in successfully.', '2025-03-24 19:01:18');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `package` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `modem` varchar(255) NOT NULL,
  `installation_date` date NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `package`, `price`, `modem`, `installation_date`, `status`) VALUES
(126, 'YANI MLANGSEN', '7Mbps', 110000.00, 'F44 GLOBAL', '0000-00-00', 'Aktif'),
(127, 'MBAK MAH', '7Mbps', 110000.00, 'F66', '0000-00-00', 'Aktif'),
(128, 'INDAH PURNAMA CENDANA', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(129, 'DEK ISNA', '10Mbps', 165000.00, 'F66', '0000-00-00', 'Aktif'),
(130, 'RENI BERAN', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(131, 'NYOMAN', '7Mbps', 100000.00, 'F66', '0000-00-00', 'Aktif'),
(132, 'KANG NO PETIL', '7Mbps', 100000.00, 'F44 GLOBAL', '0000-00-00', 'Aktif'),
(133, 'MBAK GIK', '7Mbps', 100000.00, 'F44 GLOBAL', '0000-00-00', 'Aktif'),
(134, 'MAS RONI', '7Mbps', 110000.00, 'F44 GLOBAL', '0000-00-00', 'Aktif'),
(135, 'BUDI', '15Mbps', 0.00, 'F66', '0000-00-00', 'Aktif'),
(136, 'PURWANTO TNI', '15Mbps', 185000.00, 'F66', '0000-00-00', 'Aktif'),
(137, 'PAK AMIN CENDANA', '7Mbps', 110000.00, 'F44 GLOBAL', '0000-00-00', 'Aktif'),
(138, 'ATIK', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(139, 'TUTUT', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Tidak Aktif'),
(140, 'RICHARD', '7Mbps', 110000.00, 'MYLINK', '0000-00-00', 'Aktif'),
(141, 'SAMAN', '7Mbps', 100000.00, 'MYLINK', '0000-00-00', 'Aktif'),
(142, 'MAS EDI (MBAK RISA)', '7Mbps', 110000.00, 'MYLINK', '0000-00-00', 'Aktif'),
(143, 'BU IM (PAK AMIN)', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(144, 'Mbak Sis (mbah Sojo)', '15Mbps', 185000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(145, 'ABAH ALI', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(146, 'MENIK MLANGESEN', '15Mbps', 185000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(147, 'MAS BENDO KOPI', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(148, 'MBA IKA', '15Mbps', 185000.00, 'ZTE GM220', '0000-00-00', 'Aktif'),
(149, 'PAK AMIK CENDANA', '7Mbps', 110000.00, 'ZTE GM220', '0000-00-00', 'Aktif'),
(150, 'MUNIR', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Tidak Aktif'),
(151, 'AFIT BU TRIS', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(152, 'VERA-PASMI', '15Mbps', 185000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(153, 'EDO', '7Mbps', 110000.00, 'MYLINK', '0000-00-00', 'Aktif'),
(154, 'KUKUH BUNDER', '20Mbps', 220000.00, 'ZIMMLINK', '0000-00-00', 'Aktif'),
(155, 'JUNI BERAN', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(156, 'PAK RW (DWI DARKO)', '15Mbps', 185000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(157, 'BU DYAH', '7Mbps', 110000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(158, 'MAS ANWAR', '15Mbps', 185000.00, 'HUAWEI', '0000-00-00', 'Aktif'),
(159, 'RIO', '15Mbps', 185000.00, 'ZIMMLINK', '0000-00-00', 'Aktif'),
(160, 'MURTI', '7Mbps', 110000.00, 'ZIMMLINK', '0000-00-00', 'Aktif'),
(161, 'PRI + NAR', '10Mbps', 165000.00, 'ZIMMLINK', '0000-00-00', 'Aktif'),
(162, 'LEK MEN BENGKEL (LINDA)', '7Mbps', 110000.00, 'ZIMMLINK', '0000-00-00', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE `paket` (
  `id` int(11) NOT NULL,
  `nama_paket` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paket`
--

INSERT INTO `paket` (`id`, `nama_paket`, `harga`, `keterangan`) VALUES
(6, '10Mbps', 165000.00, 'nonPPN'),
(7, '7Mbps Promo', 100000.00, 'promo'),
(8, '7Mbps ', 110000.00, ''),
(9, '15Mbps', 165000.00, ''),
(10, '20Mbps', 220000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `customer_id`, `amount`, `payment_date`, `payment_method`) VALUES
(11, 160, 110000.00, '2025-03-20', 'Cash'),
(12, 162, 110000.00, '2025-03-20', 'Transfer'),
(13, 159, 185000.00, '2025-03-20', 'Cash'),
(14, 158, 185000.00, '2025-03-20', 'Transfer'),
(15, 156, 185000.00, '2025-03-20', 'Transfer'),
(16, 155, 110000.00, '2025-03-20', 'Cash'),
(17, 154, 220000.00, '2025-03-20', 'Cash'),
(18, 149, 110000.00, '2025-03-20', 'Cash'),
(19, 147, 110000.00, '2025-03-20', 'Cash'),
(20, 146, 185000.00, '2025-03-20', 'Transfer'),
(21, 144, 185000.00, '2025-03-20', 'Transfer'),
(22, 143, 110000.00, '2025-03-20', 'Cash'),
(23, 142, 110000.00, '2025-03-20', 'Transfer'),
(24, 141, 100000.00, '2025-03-20', 'Cash'),
(25, 140, 110000.00, '2025-02-27', 'Transfer'),
(26, 138, 110000.00, '2025-03-20', 'Transfer'),
(27, 137, 110000.00, '2025-03-03', 'Transfer'),
(28, 136, 185000.00, '2025-03-20', 'Cash'),
(29, 134, 110000.00, '2025-03-16', 'Cash'),
(31, 131, 100000.00, '2025-03-10', 'Cash'),
(32, 129, 165000.00, '2025-03-16', 'Cash'),
(33, 128, 110000.00, '2025-03-16', 'Cash'),
(34, 127, 110000.00, '2025-03-18', 'Transfer'),
(35, 126, 110000.00, '2025-03-15', 'Cash'),
(36, 135, 0.00, '2025-03-21', 'Cash'),
(37, 139, 110000.00, '2025-03-21', 'Transfer'),
(38, 132, 100000.00, '2025-03-20', 'Cash'),
(39, 130, 110000.00, '2025-03-22', 'Cash'),
(40, 148, 185000.00, '2025-03-23', 'Cash'),
(42, 152, 185000.00, '2025-03-24', 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`) VALUES
(1, 'fatkhur', '$2y$10$Yxhpk9hey12mLxOIoJLzIeRWuTA00W5ir9pA.rvHc9fzidaeqTqqG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_index` (`user_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `paket`
--
ALTER TABLE `paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
