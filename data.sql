-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 05, 2022 at 07:50 PM
-- Server version: 8.0.28
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PWeb`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `infected` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'hors ligne',
  `longitude` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `infected`, `status`, `longitude`, `latitude`) VALUES
(1, 'Tania', '5b2d4484498235e80d61a233a7c04991', 'tania.olivia69@yahoo.com', 'no', 'hors ligne', '6.1904981', '48.6857813'),
(2, 'Matthew', '4a258d930b7d3409982d727ddbb4ba88', 'matthew.olivian@yahoo.com', 'yes', 'hors ligne', '14.7966236', '50.9007241'),
(3, 'Michelle', '2ec490229423f4a6879d555a81bd6e4a', 'michelle.olivia@yahoo.com', 'yes', 'hors ligne', '2.3229092', '48.8685678'),
(4, 'Evelyn', 'fa6a91ef9baa242de0b354a212e8cf82', 'evelyn.amelia@yahoo.com', 'no', 'hors ligne', '2.320687', '50.222546'),
(5, 'Tsuki', '52ebf4f9694107b25c3b56dc723c2df5', 'tsuki.olivia@yahoo.com', 'yes', 'hors ligne', '-0.350964', '47.071929'),
(6, 'Black', '128ecf542a35ac5270a87dc740918404', 'black.olivian@yahoo.com', 'yes', 'hors ligne', '3.080317', '50.375816'),
(7, 'Shereen', '155d1a1c98b0f860a26b80ff6ab941fe', 'shereen.amelia@yahoo.com', 'yes', 'hors ligne', '-0.851981', '51.193031'),
(8, 'Bella', '49b10fbde180f30ecd23a4155ecc5a6f', 'bella.fransiska@yahoo.com', 'yes', 'hors ligne', '3.548071', '44.264801'),
(9, 'Alvionna', '5aa9114de7d21806f68693601b5842d9', 'alvionna.hanson@yahoo.com', 'no', 'hors ligne', '-2.291251', '48.160839');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_unq` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
