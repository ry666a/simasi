-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 17, 2026 at 10:30 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gm_simasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `mp_edging`
--

CREATE TABLE `mp_edging` (
  `id` int(11) NOT NULL,
  `exact_code` varchar(20) NOT NULL,
  `id_produk_komponen_detail` int(11) NOT NULL,
  `edging_panjang` int(11) NOT NULL DEFAULT 0,
  `edging_lebar` int(11) NOT NULL DEFAULT 0,
  `groving_panjang` int(11) NOT NULL DEFAULT 0,
  `groving_lebar` int(11) NOT NULL DEFAULT 0,
  `edging_groving_panjang` int(11) NOT NULL DEFAULT 0,
  `edging_groving_lebar` int(11) NOT NULL DEFAULT 0,
  `total_meter_komponen` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `total_ml_j600` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `total_ml_j800` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `waktu_proses_j600` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `waktu_proses_j800` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `create_at` date NOT NULL,
  `id_admin` int(11) NOT NULL,
  `update_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mp_edging`
--

INSERT INTO `mp_edging` (`id`, `exact_code`, `id_produk_komponen_detail`, `edging_panjang`, `edging_lebar`, `groving_panjang`, `groving_lebar`, `edging_groving_panjang`, `edging_groving_lebar`, `total_meter_komponen`, `total_ml_j600`, `total_ml_j800`, `waktu_proses_j600`, `waktu_proses_j800`, `create_at`, `id_admin`, `update_at`) VALUES
(1, '11-1300330-101182008', 105, 1, 0, 0, 0, 0, 0, '0.8000', '1.4000', '1.6000', '0.1077', '0.0800', '2026-04-15', 1, '2026-04-15 08:10:05'),
(2, '11-1300330-101182008', 105, 0, 2, 0, 0, 0, 0, '0.7920', '1.9920', '2.3920', '0.1532', '0.1200', '2026-04-15', 1, '2026-04-15 08:10:05'),
(3, '11-1300330-101182008', 106, 1, 0, 0, 0, 0, 0, '0.7720', '1.3720', '1.5720', '0.1055', '0.0790', '2026-04-15', 1, '2026-04-15 08:10:05'),
(4, '11-1300330-101182008', 11118, 1, 0, 0, 0, 0, 0, '1.2000', '1.8000', '2.0000', '0.1385', '0.1000', '2026-04-15', 1, '2026-04-15 08:10:05'),
(5, '11-1300330-101182008', 11119, 1, 0, 0, 0, 0, 0, '1.2000', '1.8000', '2.0000', '0.1385', '0.1000', '2026-04-15', 1, '2026-04-15 08:10:05'),
(6, '11-1300330-101182008', 110, 1, 0, 0, 0, 0, 0, '1.1080', '1.7080', '1.9080', '0.1314', '0.0950', '2026-04-15', 1, '2026-04-15 08:10:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mp_edging`
--
ALTER TABLE `mp_edging`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_produk_komponen_detail` (`id_produk_komponen_detail`,`edging_panjang`,`edging_lebar`,`groving_panjang`,`groving_lebar`,`edging_groving_panjang`,`edging_groving_lebar`,`total_meter_komponen`,`total_ml_j600`,`total_ml_j800`,`waktu_proses_j600`,`waktu_proses_j800`,`exact_code`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mp_edging`
--
ALTER TABLE `mp_edging`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
