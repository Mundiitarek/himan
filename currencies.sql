-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 24, 2026 at 08:04 AM
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
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=Crypto,2=Fiat',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sign` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT '0.00000000' COMMENT 'only for fiat currency',
  `ranking` int NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=Enable,0=Disable',
  `highlighted_coin` tinyint(1) NOT NULL DEFAULT '0',
  `p2p_sn` int NOT NULL DEFAULT '0',
  `last_update` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `type`, `name`, `sign`, `symbol`, `image`, `rate`, `ranking`, `status`, `highlighted_coin`, `p2p_sn`, `last_update`, `created_at`, `updated_at`) VALUES
(1, 2, 'US Dollar', '$', 'USD', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(2, 2, 'Euro', '€', 'EUR', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(3, 2, 'British Pound', '£', 'GBP', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(4, 2, 'Japanese Yen', '¥', 'JPY', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(5, 2, 'Australian Dollar', 'A$', 'AUD', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(6, 2, 'Canadian Dollar', 'C$', 'CAD', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(7, 2, 'Swiss Franc', 'CHF', 'CHF', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(8, 2, 'New Zealand Dollar', 'NZ$', 'NZD', NULL, 1.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(9, 1, 'Bitcoin', '₿', 'BTC', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(10, 1, 'Ethereum', 'Ξ', 'ETH', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(11, 1, 'Tether', '₮', 'USDT', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(12, 1, 'Binance Coin', 'BNB', 'BNB', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-19 22:41:15', '2026-01-19 22:41:15'),
(13, 1, 'Solana', NULL, 'SOL', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-21 19:03:12', '2026-01-21 19:03:12'),
(14, 1, 'Ripple', NULL, 'XRP', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-21 19:03:12', '2026-01-21 19:03:12'),
(15, 1, 'Cardano', NULL, 'ADA', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-21 19:03:12', '2026-01-21 19:03:12'),
(16, 1, 'Dogecoin', NULL, 'DOGE', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-21 19:03:12', '2026-01-21 19:03:12'),
(29, 2, 'Chinese Yuan', NULL, 'CNY', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(30, 2, 'Hong Kong Dollar', NULL, 'HKD', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(31, 2, 'Singapore Dollar', NULL, 'SGD', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(32, 2, 'Indian Rupee', NULL, 'INR', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(33, 2, 'Mexican Peso', NULL, 'MXN', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(34, 2, 'South African Rand', NULL, 'ZAR', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(35, 2, 'Turkish Lira', NULL, 'TRY', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(36, 2, 'Gold', NULL, 'XAU', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(37, 2, 'Silver', NULL, 'XAG', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(38, 2, 'Platinum', NULL, 'XPT', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(39, 2, 'Palladium', NULL, 'XPD', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(40, 2, 'Copper', NULL, 'XCU', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(41, 2, 'WTI Crude Oil', NULL, 'USOIL', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(42, 2, 'Brent Crude Oil', NULL, 'UKOIL', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(43, 2, 'Natural Gas', NULL, 'NATGAS', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(44, 2, 'Apple', NULL, 'AAPL', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(45, 2, 'Tesla', NULL, 'TSLA', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(46, 2, 'Alphabet', NULL, 'GOOGL', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(47, 2, 'Amazon', NULL, 'AMZN', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(48, 2, 'Microsoft', NULL, 'MSFT', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(49, 2, 'Meta', NULL, 'META', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(50, 2, 'NVIDIA', NULL, 'NVDA', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(51, 2, 'AMD', NULL, 'AMD', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(52, 2, 'Netflix', NULL, 'NFLX', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(53, 2, 'Disney', NULL, 'DIS', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(54, 2, 'S&P 500', NULL, 'SPX', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(55, 2, 'NASDAQ 100', NULL, 'NDX', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(56, 2, 'Dow Jones', NULL, 'DJI', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(57, 2, 'Russell 2000', NULL, 'RUT', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(58, 2, 'VIX', NULL, 'VIX', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(59, 2, 'FTSE 100', NULL, 'FTSE', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(60, 2, 'DAX', NULL, 'DAX', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(61, 2, 'CAC 40', NULL, 'CAC', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(62, 2, 'Nikkei 225', NULL, 'NIKKEI', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(63, 2, 'Hang Seng', NULL, 'HSI', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16'),
(64, 2, 'Shanghai Composite', NULL, 'SSE', NULL, 0.00000000, 0, 1, 0, 0, 0, '2026-01-22 00:52:16', '2026-01-22 00:52:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_name_unique` (`name`),
  ADD UNIQUE KEY `currencies_symbol_unique` (`symbol`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
