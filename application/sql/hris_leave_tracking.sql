-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 10:04 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `depedmis_hris`
--

-- --------------------------------------------------------

--
-- Table structure for table `hris_leave_tracking`
--

CREATE TABLE `hris_leave_tracking` (
  `trackingID` int(11) NOT NULL,
  `leaveID` int(11) NOT NULL,
  `IDNumber` varchar(20) NOT NULL,
  `actionTaken` varchar(50) NOT NULL,
  `responsibleUser` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hris_leave_tracking`
--

INSERT INTO `hris_leave_tracking` (`trackingID`, `leaveID`, `IDNumber`, `actionTaken`, `responsibleUser`, `position`, `timestamp`) VALUES
(1, 82, '1301260', 'Evaluated', 'Seeyouinthedark', 'Human Resource Admin', '2025-03-19 08:51:38'),
(2, 79, '1301260', 'Approved', '  ', 'asds', '2025-03-19 08:54:26'),
(3, 82, '1301260', 'Approved', 'asdsv2', 'asds', '2025-03-19 09:02:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hris_leave_tracking`
--
ALTER TABLE `hris_leave_tracking`
  ADD PRIMARY KEY (`trackingID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hris_leave_tracking`
--
ALTER TABLE `hris_leave_tracking`
  MODIFY `trackingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
