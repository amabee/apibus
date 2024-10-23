-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2024 at 06:14 AM
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
-- Database: `bus_reserve`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `bid` int(11) NOT NULL,
  `bus_name` varchar(25) NOT NULL,
  `seat_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`bid`, `bus_name`, `seat_capacity`) VALUES
(5, 'Rural Transit 101', 10);

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `pid` int(11) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`pid`, `firstname`, `lastname`, `email`, `password`) VALUES
(1, 'Johny', 'Cage', '187mobztaz@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'),
(2, 'Lilia', 'Wishville', 'wddwm@netflix.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'),
(3, 'Shin Hye', 'Park', 'psh_me@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'),
(4, 'Bit Na', 'Kang', 'dm0n_jdge@koreaboo.mail', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8'),
(5, 'Seok Ryu', 'Bae', 'sheeshable@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `payment_mode` enum('Cash','Gcash') NOT NULL,
  `passenger_type` enum('Regular','Student','Senior') NOT NULL,
  `number_of_passengers` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `driver_id` int(11) DEFAULT NULL,
  `passenger_id` int(11) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `reservation_time` date DEFAULT current_timestamp(),
  `reservation_status` enum('active','inactive','checked-in','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `trip_id`, `payment_mode`, `passenger_type`, `number_of_passengers`, `total_amount`, `created_at`, `driver_id`, `passenger_id`, `seat_number`, `reservation_time`, `reservation_status`) VALUES
(6, 5, 'Cash', 'Student', 1, 75.00, '2024-10-22 10:28:45', 3, 2, 2, '2024-10-23', 'active'),
(7, 5, 'Cash', 'Student', 1, 75.00, '2024-10-23 01:54:43', 3, 2, 1, '2024-10-23', 'checked-in'),
(8, 5, 'Gcash', 'Student', 2, 150.00, '2024-10-23 01:55:53', 3, 2, 3, '2024-10-24', 'checked-in'),
(9, 5, 'Gcash', 'Student', 2, 150.00, '2024-10-23 01:55:53', 3, 2, 4, '2024-10-24', 'checked-in'),
(10, 5, 'Cash', 'Student', 2, 150.00, '2024-10-23 01:58:41', 3, 2, 6, '2024-10-24', 'active'),
(11, 5, 'Cash', 'Student', 2, 150.00, '2024-10-23 01:58:41', 3, 2, 5, '2024-10-23', 'active'),
(12, 5, 'Cash', 'Student', 5, 375.00, '2024-10-23 03:07:16', 3, 2, 7, '2024-10-24', 'active'),
(13, 5, 'Cash', 'Student', 5, 375.00, '2024-10-23 03:07:16', 3, 2, 3, '2024-10-24', 'active'),
(14, 5, 'Cash', 'Student', 5, 375.00, '2024-10-23 03:07:16', 3, 2, 8, '2024-10-24', 'active'),
(15, 5, 'Cash', 'Student', 5, 375.00, '2024-10-23 03:07:16', 3, 2, 4, '2024-10-24', 'active'),
(16, 5, 'Cash', 'Student', 5, 375.00, '2024-10-23 03:07:16', 3, 2, 10, '2024-10-24', 'active'),
(17, 5, 'Cash', 'Student', 2, 150.00, '2024-10-23 03:08:56', 3, 2, 2, '2024-10-24', 'active'),
(18, 5, 'Cash', 'Student', 2, 150.00, '2024-10-23 03:08:56', 3, 2, 1, '2024-10-24', 'active'),
(19, 4, 'Cash', 'Student', 2, 150.00, '2024-10-23 03:09:44', 3, 2, 2, '2024-10-24', 'active'),
(20, 4, 'Cash', 'Student', 2, 150.00, '2024-10-23 03:09:44', 3, 2, 1, '2024-10-24', 'active'),
(21, 4, 'Cash', 'Student', 2, 150.00, '2024-10-23 03:13:57', 3, 2, 3, '2024-10-24', 'active'),
(22, 4, 'Cash', 'Student', 2, 150.00, '2024-10-23 03:13:57', 3, 2, 4, '2024-10-24', 'active'),
(23, 4, 'Cash', 'Student', 1, 75.00, '2024-10-23 03:48:40', 3, 2, 5, '2024-10-24', 'active'),
(24, 5, 'Cash', 'Student', 1, 75.00, '2024-10-23 03:58:42', 3, 2, 9, '2024-10-24', 'active'),
(25, 4, 'Cash', 'Student', 2, 150.00, '2024-10-23 04:04:16', 3, 2, 7, '2024-10-24', 'active'),
(26, 4, 'Cash', 'Student', 2, 150.00, '2024-10-23 04:04:16', 3, 2, 6, '2024-10-24', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tbldrivers`
--

CREATE TABLE `tbldrivers` (
  `driver_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `assigned_bus` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbldrivers`
--

INSERT INTO `tbldrivers` (`driver_id`, `firstname`, `lastname`, `email`, `password`, `address`, `assigned_bus`) VALUES
(3, 'Michael', 'Johnson', 'johnsons@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Tokyo, Japan', 5);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `tid` int(11) NOT NULL,
  `trip_name` varchar(50) NOT NULL,
  `from_loc` varchar(50) NOT NULL,
  `to_loc` varchar(50) NOT NULL,
  `departure_time` time DEFAULT NULL,
  `bus_assigned` int(11) NOT NULL,
  `fare_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`tid`, `trip_name`, `from_loc`, `to_loc`, `departure_time`, `bus_assigned`, `fare_price`) VALUES
(4, 'R1 Vice Versa', 'Cagayan', 'Tagoloan', '07:00:00', 5, 75),
(5, 'R1 Vice Versa', 'Tagoloan', 'Cagayan', '10:00:00', 5, 75);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`pid`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `passenger_id` (`passenger_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `tbldrivers`
--
ALTER TABLE `tbldrivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD KEY `assigned_bus` (`assigned_bus`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`tid`),
  ADD KEY `bus_assigned` (`bus_assigned`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bus`
--
ALTER TABLE `bus`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tbldrivers`
--
ALTER TABLE `tbldrivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `tbldrivers` (`driver_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`pid`),
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`tid`);

--
-- Constraints for table `tbldrivers`
--
ALTER TABLE `tbldrivers`
  ADD CONSTRAINT `tbldrivers_ibfk_1` FOREIGN KEY (`assigned_bus`) REFERENCES `bus` (`bid`);

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`bus_assigned`) REFERENCES `bus` (`bid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
