-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 06:39 AM
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
-- Database: `hfh`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `userid` int(11) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_entries`
--

CREATE TABLE `form_entries` (
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `address` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `parcel_number` varchar(45) DEFAULT 'N/A',
  `zip` varchar(45) DEFAULT NULL,
  `layout` varchar(45) DEFAULT NULL,
  `assessmentStatus` enum('Needs Assessment','Needs Bidding','Archived') NOT NULL DEFAULT 'Needs Assessment',
  `username` varchar(45) DEFAULT 'unknown user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `icons`
--

CREATE TABLE `icons` (
  `id` int(11) NOT NULL,
  `iconId` varchar(255) DEFAULT NULL,
  `assignmentID` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `x_pos` int(11) NOT NULL,
  `y_pos` int(11) NOT NULL,
  `severity` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel`
--

CREATE TABLE `parcel` (
  `parcel_number` varchar(20) NOT NULL,
  `address` varchar(90) DEFAULT NULL,
  `residence_type` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- Indexes for table `form_entries`
--
ALTER TABLE `form_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `icons`
--
ALTER TABLE `icons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iconId` (`iconId`);

--
-- Indexes for table `parcel`
--
ALTER TABLE `parcel`
  ADD PRIMARY KEY (`parcel_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_entries`
--
ALTER TABLE `form_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `icons`
--
ALTER TABLE `icons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
