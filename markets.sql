-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 24, 2026 at 08:03 AM
-- Server version: 8.0.39-cll-lve
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smm2355_typescript`
--

-- --------------------------------------------------------

--
-- Table structure for table `markets`
--

CREATE TABLE `markets` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_id` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=Enable,0=Disable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `markets`
--

INSERT INTO `markets` (`id`, `name`, `currency_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 'USD', 1, 1, '2026-01-19 23:11:43', '2026-01-19 23:11:43'),
(4, 'EUR / USD', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(5, 'GBP / USD', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(6, 'USD / JPY', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(7, 'AUD / USD', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(8, 'USD / CAD', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(9, 'USD / CHF', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(10, 'NZD / USD', 1, 1, '2026-01-21 19:07:25', '2026-01-21 19:07:25'),
(11, 'Gold (XAU / USD)', 1, 1, '2026-01-21 19:07:45', '2026-01-21 19:07:45'),
(12, 'Silver (XAG / USD)', 1, 1, '2026-01-21 19:07:45', '2026-01-21 19:07:45'),
(13, 'Platinum (XPT / USD)', 1, 1, '2026-01-21 19:07:45', '2026-01-21 19:07:45'),
(14, 'Palladium (XPD / USD)', 1, 1, '2026-01-21 19:07:45', '2026-01-21 19:07:45'),
(15, 'WTI Crude Oil', 1, 1, '2026-01-21 19:07:58', '2026-01-21 19:07:58'),
(16, 'Brent Crude Oil', 1, 1, '2026-01-21 19:07:58', '2026-01-21 19:07:58'),
(17, 'Natural Gas', 1, 1, '2026-01-21 19:07:58', '2026-01-21 19:07:58'),
(18, 'Apple (AAPL)', 1, 1, '2026-01-21 19:08:17', '2026-01-21 19:08:17'),
(19, 'Tesla (TSLA)', 1, 1, '2026-01-21 19:08:17', '2026-01-21 19:08:17'),
(20, 'Amazon (AMZN)', 1, 1, '2026-01-21 19:08:17', '2026-01-21 19:08:17'),
(21, 'Microsoft (MSFT)', 1, 1, '2026-01-21 19:08:17', '2026-01-21 19:08:17'),
(22, 'NVIDIA (NVDA)', 1, 1, '2026-01-21 19:08:17', '2026-01-21 19:08:17'),
(23, 'S&P 500 (SPX)', 1, 1, '2026-01-21 19:08:32', '2026-01-21 19:08:32'),
(24, 'NASDAQ 100 (NDX)', 1, 1, '2026-01-21 19:08:32', '2026-01-21 19:08:32'),
(25, 'Dow Jones (DJI)', 1, 1, '2026-01-21 19:08:32', '2026-01-21 19:08:32'),
(26, 'DAX', 1, 1, '2026-01-21 19:08:32', '2026-01-21 19:08:32'),
(27, 'FTSE 100', 1, 1, '2026-01-21 19:08:32', '2026-01-21 19:08:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `markets`
--
ALTER TABLE `markets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `markets_name_unique` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `markets`
--
ALTER TABLE `markets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
