-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 08:18 AM
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
-- Database: `dilnicky_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admini`
--

CREATE TABLE `admini` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `admini`
--

INSERT INTO `admini` (`id_admin`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$kP6/MI8f5wJdIP9jcbzVj.vxX.skcNz3GfEgf3LgX2wMmm.12U63e'),
(2, 'admin2', 'admin456'),
(3, 'admin3', '$2y$10$4Xn.O66dfKaNFtGvj3asZ.0wrvdDaJyIti7GhUR8iln/Kmhy6Xs/q');

-- --------------------------------------------------------

--
-- Table structure for table `deti`
--

CREATE TABLE `deti` (
  `id_dite` int(11) NOT NULL,
  `jmeno_dite` varchar(255) NOT NULL,
  `id_rodic` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `deti`
--

INSERT INTO `deti` (`id_dite`, `jmeno_dite`, `id_rodic`) VALUES
(1, 'Horacio Bartoš', 1),
(2, 'Jordán Bartoš', 1),
(3, 'Ferdinand Uhlíř', 2),
(4, 'Ludvík Starší', 3),
(5, 'Cílovníci Dálava', 4),
(6, 'Cílovníci Dálava', 5),
(7, 'Albert Lichý', 6),
(8, 'sdsdsd', 7);

-- --------------------------------------------------------

--
-- Table structure for table `dilny`
--

CREATE TABLE `dilny` (
  `id_dilna` int(11) NOT NULL,
  `nazev_dilna` varchar(255) NOT NULL,
  `vedouci` varchar(255) NOT NULL,
  `kapacita` int(11) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `datum_konani` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `dilny`
--

INSERT INTO `dilny` (`id_dilna`, `nazev_dilna`, `vedouci`, `kapacita`, `cena`, `datum_konani`) VALUES
(1, 'Malování na hedvábí', 'Jana Nováková', 10, 250.00, '2024-09-15 10:00:00'),
(3, 'Fortnite pro seniory', 'Jaroslav Uhlíř', 20, 120.00, '2026-10-02 18:45:00'),
(4, '2 mista', 'já', 2, 20.00, '3035-12-20 23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `prihlasky`
--

CREATE TABLE `prihlasky` (
  `id_prihlaska` int(11) NOT NULL,
  `id_dite` int(11) NOT NULL,
  `id_dilna` int(11) NOT NULL,
  `datum_prihlaseni` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `prihlasky`
--

INSERT INTO `prihlasky` (`id_prihlaska`, `id_dite`, `id_dilna`, `datum_prihlaseni`) VALUES
(1, 1, 1, '2025-05-28 06:50:06'),
(2, 2, 1, '2025-05-28 06:50:39'),
(4, 4, 3, '2025-05-28 10:54:43'),
(5, 1, 4, '2025-05-28 10:55:39'),
(6, 1, 3, '2025-05-28 10:56:10'),
(7, 5, 4, '2025-05-28 10:57:52'),
(8, 5, 3, '2025-05-28 11:05:29'),
(9, 5, 1, '2025-05-28 11:05:40'),
(10, 6, 3, '2025-05-28 11:06:02'),
(12, 7, 3, '2025-05-28 11:20:38'),
(13, 8, 1, '2025-05-29 13:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `rodice`
--

CREATE TABLE `rodice` (
  `id_rodic` int(11) NOT NULL,
  `jmeno_rodic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Dumping data for table `rodice`
--

INSERT INTO `rodice` (`id_rodic`, `jmeno_rodic`) VALUES
(1, 'Pavel Bartoš'),
(2, 'Jaroslav Uhlíř'),
(3, 'Bartoloměj Starší'),
(4, 'Akat Blýskavice'),
(5, 'fortnite epicgames'),
(6, 'Alžběta Lichá'),
(7, 'fjff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admini`
--
ALTER TABLE `admini`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `deti`
--
ALTER TABLE `deti`
  ADD PRIMARY KEY (`id_dite`),
  ADD KEY `id_rodic` (`id_rodic`);

--
-- Indexes for table `dilny`
--
ALTER TABLE `dilny`
  ADD PRIMARY KEY (`id_dilna`);

--
-- Indexes for table `prihlasky`
--
ALTER TABLE `prihlasky`
  ADD PRIMARY KEY (`id_prihlaska`),
  ADD UNIQUE KEY `unikatni_prihlaska` (`id_dite`,`id_dilna`),
  ADD KEY `id_dilna` (`id_dilna`);

--
-- Indexes for table `rodice`
--
ALTER TABLE `rodice`
  ADD PRIMARY KEY (`id_rodic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admini`
--
ALTER TABLE `admini`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deti`
--
ALTER TABLE `deti`
  MODIFY `id_dite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dilny`
--
ALTER TABLE `dilny`
  MODIFY `id_dilna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prihlasky`
--
ALTER TABLE `prihlasky`
  MODIFY `id_prihlaska` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `rodice`
--
ALTER TABLE `rodice`
  MODIFY `id_rodic` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `deti`
--
ALTER TABLE `deti`
  ADD CONSTRAINT `deti_ibfk_1` FOREIGN KEY (`id_rodic`) REFERENCES `rodice` (`id_rodic`) ON DELETE CASCADE;

--
-- Constraints for table `prihlasky`
--
ALTER TABLE `prihlasky`
  ADD CONSTRAINT `prihlasky_ibfk_1` FOREIGN KEY (`id_dite`) REFERENCES `deti` (`id_dite`) ON DELETE CASCADE,
  ADD CONSTRAINT `prihlasky_ibfk_2` FOREIGN KEY (`id_dilna`) REFERENCES `dilny` (`id_dilna`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
