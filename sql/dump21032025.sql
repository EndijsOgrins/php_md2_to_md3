-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 21, 2025 at 03:44 PM
-- Server version: 5.7.36
-- PHP Version: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rally`
--

-- --------------------------------------------------------

--
-- Table structure for table `sacensibas`
--

CREATE TABLE `sacensibas` (
  `id` int(11) NOT NULL,
  `nosaukums` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `norises_vieta` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `datums_no` date NOT NULL,
  `datums_lidz` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `sacensibas`
--

INSERT INTO `sacensibas` (`id`, `nosaukums`, `norises_vieta`, `datums_no`, `datums_lidz`) VALUES
(2, '2025 Ziemas čempionāts', 'Latvija, Sigulda', '2025-02-10', '2025-02-12'),
(3, '2024 Baltijas kauss', 'Lietuva, Viļņa', '2024-09-15', '2024-09-18'),
(4, '2024 Zemgales rallijs', 'Latvija, Jelgava', '2024-05-20', '2024-05-22'),
(5, '2023 Latvijas čempionāts', 'Latvija, Rīga', '2023-07-01', '2023-07-04'),
(6, '2023 Baltijas kauss', 'Igaunija, Tallina', '2023-08-20', '2023-08-22'),
(15, '2025 Latvijas rallijs', 'Latvija, Cēsis', '2025-06-01', '2025-06-04'),
(16, '2028 Rallijs Kurzeme', 'Saldus, Ošakalni', '2028-06-06', '2028-06-08');

-- --------------------------------------------------------

--
-- Table structure for table `sacensibas_sponsori`
--

CREATE TABLE `sacensibas_sponsori` (
  `id` int(11) NOT NULL,
  `sacensibas_id` int(11) NOT NULL,
  `sponsora_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `sacensibas_sponsori`
--

INSERT INTO `sacensibas_sponsori` (`id`, `sacensibas_id`, `sponsora_id`) VALUES
(5, 2, 1),
(6, 2, 4),
(7, 2, 5),
(8, 2, 6),
(57, 2, 15),
(9, 3, 1),
(10, 3, 2),
(11, 3, 6),
(12, 4, 3),
(13, 4, 4),
(58, 4, 5),
(15, 5, 1),
(16, 5, 3),
(17, 5, 5),
(18, 6, 2),
(19, 6, 4),
(20, 6, 6),
(53, 6, 15),
(60, 15, 5),
(59, 15, 6),
(63, 15, 15),
(62, 15, 16),
(61, 15, 18),
(64, 16, 1),
(66, 16, 3),
(68, 16, 6),
(67, 16, 16),
(65, 16, 18);

-- --------------------------------------------------------

--
-- Table structure for table `sponsori`
--

CREATE TABLE `sponsori` (
  `id` int(11) NOT NULL,
  `kompanijas_nosaukums` varchar(255) COLLATE utf8mb4_latvian_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_latvian_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_latvian_ci DEFAULT NULL,
  `talrunis` varchar(20) COLLATE utf8mb4_latvian_ci DEFAULT NULL,
  `piezimes` text COLLATE utf8mb4_latvian_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_latvian_ci;

--
-- Dumping data for table `sponsori`
--

INSERT INTO `sponsori` (`id`, `kompanijas_nosaukums`, `url`, `logo`, `talrunis`, `piezimes`) VALUES
(1, 'Latvijas Balzams', 'https://amberlb.lv', 'lb_logo.png', '+371 67000001', 'Galvenais sponsors'),
(2, 'Circle K', 'https://circlek.lv', 'ck_logo.png', '+371 67000002', 'Degvielas partners'),
(3, 'LMT', 'https://lmt.lv', 'lmt_logo.png', '+371 67000003', 'Komunikāciju partners'),
(4, 'Michelin', 'https://michelin.com', 'michelin_logo.png', '+371 67000004', 'Riepu sponsors'),
(5, 'Red Bull', 'https://redbull.com', 'redbull_logo.png', '+371 67000005', 'Enerģijas dzēriena sponsors'),
(6, 'Castrol', 'https://www.castrol.com', 'castrol_logo.png', '+371 67000006', 'Eļļas sponsors'),
(7, 'Delfi', 'https://delfi.lv', 'delfi_logo.png', '+371 67000007', 'Mediju partners'),
(15, 'Coca Cola', 'https://www.coca-cola.com', 'coca_cola.png', NULL, ''),
(16, 'MikroTik', 'https://mikrotik.com/', 'MikroTik_Logo.jpg', NULL, ''),
(18, 'Sixt', 'https://www.sixt.com/', 'sixt_rent_car.jpg', NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sacensibas`
--
ALTER TABLE `sacensibas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sacensibas_sponsori`
--
ALTER TABLE `sacensibas_sponsori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_relationship` (`sacensibas_id`,`sponsora_id`),
  ADD KEY `sponsora_id` (`sponsora_id`);

--
-- Indexes for table `sponsori`
--
ALTER TABLE `sponsori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kompanijas_nosaukums` (`kompanijas_nosaukums`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sacensibas`
--
ALTER TABLE `sacensibas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sacensibas_sponsori`
--
ALTER TABLE `sacensibas_sponsori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `sponsori`
--
ALTER TABLE `sponsori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sacensibas_sponsori`
--
ALTER TABLE `sacensibas_sponsori`
  ADD CONSTRAINT `sacensibas_sponsori_ibfk_1` FOREIGN KEY (`sacensibas_id`) REFERENCES `sacensibas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sacensibas_sponsori_ibfk_2` FOREIGN KEY (`sponsora_id`) REFERENCES `sponsori` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
