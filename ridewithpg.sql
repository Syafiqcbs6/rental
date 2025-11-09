-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 06:20 PM
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
-- Database: `ridewithpg`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `car_id`, `start_date`, `end_date`, `total_price`, `status`, `payment_proof`) VALUES
(9, 3, 5, '2025-11-10', '2025-11-13', 480.00, '', NULL),
(10, 3, 4, '2025-11-01', '2025-11-01', 300.00, '', NULL),
(11, 3, 4, '2025-11-11', '2025-11-20', 3000.00, 'rejected', NULL),
(12, 3, 5, '2025-11-14', '2025-11-15', 240.00, '', NULL),
(13, 3, 4, '2025-11-21', '2025-11-22', 600.00, 'rejected', NULL),
(14, 3, 5, '2025-11-19', '2025-11-20', 240.00, 'rejected', NULL),
(15, 12, 4, '2025-11-09', '2025-11-10', 600.00, 'rejected', NULL),
(16, 12, 5, '2025-11-04', '2025-11-05', 240.00, 'approved', NULL),
(17, 12, 3, '2025-11-18', '2025-11-20', 450.00, 'approved', NULL),
(18, 12, 4, '2025-11-05', '2025-11-07', 900.00, 'approved', NULL),
(19, 12, 5, '2025-11-06', '2025-11-08', 360.00, 'approved', NULL),
(20, 12, 8, '2025-11-11', '2025-11-14', 1200.00, 'approved', NULL),
(21, 15, 7, '2025-11-03', '2025-11-11', 1350.00, 'rejected', NULL),
(22, 15, 8, '2025-11-15', '2025-11-18', 1200.00, 'rejected', NULL),
(23, 15, 7, '2025-11-13', '2025-11-15', 450.00, 'rejected', NULL),
(24, 12, 4, '2025-11-25', '2025-11-25', 300.00, 'approved', NULL),
(25, 12, 4, '2025-11-28', '2025-11-28', 300.00, 'pending', NULL),
(26, 12, 7, '2025-11-19', '2025-11-19', 150.00, 'pending', NULL),
(27, 12, 9, '2025-11-12', '2025-11-12', 300.00, 'pending', NULL),
(28, 12, 9, '2025-11-17', '2025-11-17', 300.00, 'pending', NULL),
(29, 12, 9, '2025-11-19', '2025-11-19', 300.00, '', '????\0JFIF\0\0\0\0\0\0??\0?\0	( %!1!%)+...383-7(-.+\n\n\n\r+%%--------------------------------------------+---+-??\0\0?,\"\0??\0\0\0\0\0\0\0\0\0\0\0\0\0\0??\0F\0	\0\0\0!1AQa\"q?2B??????#CR'),
(30, 12, 5, '2025-11-28', '2025-11-28', 120.00, '', 'payment_30_1762363295.jpeg'),
(31, 12, 7, '2025-11-12', '2025-11-12', 150.00, 'pending', 'payment_31_1762363684.jpeg'),
(32, 12, 4, '2025-11-26', '2025-11-26', 300.00, 'Approved', 'payment_32_1762364137.jpeg'),
(33, 12, 9, '2025-11-20', '2025-11-20', 300.00, 'Rejected', NULL),
(34, 3, 9, '2025-11-21', '2025-11-21', 300.00, 'pending', 'payment_34_1762365922.jpeg'),
(35, 12, 9, '2025-11-06', '2025-11-06', 300.00, 'pending', 'payment_35_1762367500.jpeg'),
(36, 12, 7, '2025-11-18', '2025-11-18', 150.00, 'pending', 'payment_36_1762367562.jpeg'),
(37, 12, 7, '0000-00-00', '0000-00-00', 150.00, 'pending', 'payment_37_1762367576.jpeg'),
(38, 12, 7, '2025-11-17', '2025-11-17', 150.00, 'pending', 'payment_38_1762367606.jpeg'),
(39, 12, 5, '2025-11-24', '2025-11-26', 360.00, 'Approved', 'payment_39_1762517166.jpeg'),
(40, 3, 4, '2025-11-23', '2025-11-24', 600.00, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price_per_day` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `availability_status` varchar(20) NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `model`, `brand`, `price_per_day`, `image`, `availability_status`) VALUES
(2, 'VELLFIRE', 'TOYOTA', 250.00, 'vellfire.jpg', 'Available'),
(3, 'MODEL s', 'TESLA', 150.00, 'model s.png', 'Available'),
(4, 'SEAL', 'BYD', 120.00, '1761743377_ev.jpg', 'Available'),
(5, 'M4 COMPETITION', 'BMW', 1500.00, 'm4.jpg', 'Available'),
(7, 'MYVI', 'PERODUA', 150.00, 'perodua-myvi-front-angle-low-view-485010.jpg', 'Available'),
(8, 'AMG GT', 'MERCEDES', 300.00, '1762110678_amg.jpeg', 'Not Available'),
(9, 'RAPTOR 4X4', 'FORD', 450.00, '4x4.jpg', 'Available');
(10, 'X70', 'PROTON', 110.00, 'x70.jpg', 'Available');
(11, 'TYPE R', 'HONDA', 245.00, 'type r.jpg', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_pic` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `profile_pic`) VALUES
(1, 'admin', NULL, 'admin123', 'admin', NULL),
(3, 'admin', 'admin@ridewithpg.com', '0192023a7bbd73250516f069df18b500', 'admin', NULL),
(4, 'mengmeng023', 'waqifhafid@gmail.com', '$2y$10$nobNRZZPTdZrb/yQN/DA8.e2h15LLmIPjedWUN2wWkNaKffIZtqRi', 'user', NULL),
(5, 'hinamasayaalia', 'lsdknclsn@fklnef', 'b1eb52b37747fb8c60c52dcb84f4684d', 'user', NULL),
(9, 'alamakalamak', 'alamak@alkmdsa', '3dac3c564cbbb033ca9a9eca9905cfc8', 'user', NULL),
(10, 'alamakalama', 'alama@alkmdsa', 'c9262ce0bda58f535c6c279276a13803', 'user', NULL),
(11, 'amiramir', 'amiramir123@ogdf', 'c415397d9382f8d187825c098d3385ea', 'user', NULL),
(12, 'mengmeng', 'mengmeng@mengmeng', '3060c3bd94c8ed8d310b7e82c73ae49e', 'user', NULL),
(13, 'anep', 'anep@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'user', NULL),
(14, 'nepnep', 'nepnep@gmail.com', '5c44d3ed7462245f57b37f8fe2a3d5de', 'user', NULL),
(15, 'wafiqkamarulzaman', 'kamarulzaman@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_bookings_car` (`car_id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`),
  ADD CONSTRAINT `fk_bookings_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
