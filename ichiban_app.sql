-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2025 at 07:53 PM
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
-- Database: `ichiban_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_visit`
--

CREATE TABLE `daily_visit` (
  `id` int(11) NOT NULL,
  `tanggal_visit` date NOT NULL,
  `nama_pos` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `area` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_visit`
--

INSERT INTO `daily_visit` (`id`, `tanggal_visit`, `nama_pos`, `alamat`, `area`, `created_at`) VALUES
(2, '2025-08-21', 'Sinar Jaya Motor (SJM001)', 'Bogor Raya Km 43 No. 31', 'Cibinong', '2025-08-21 16:19:38'),
(3, '2025-08-21', 'Sinar Jaya Motor (SJM002)', 'Abd. Latif No. 009', 'Serang', '2025-08-21 16:20:17');

-- --------------------------------------------------------

--
-- Table structure for table `data_ichiban`
--

CREATE TABLE `data_ichiban` (
  `id` int(11) NOT NULL,
  `kode` varchar(20) DEFAULT NULL,
  `nama_pos` varchar(100) DEFAULT NULL,
  `nama_pic` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `kota_kabupaten` varchar(100) DEFAULT NULL,
  `salesman` varchar(100) DEFAULT NULL,
  `cop` enum('class 1','class 2','class 3','class 4','class 5','class 6') DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `ar` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_ichiban`
--

INSERT INTO `data_ichiban` (`id`, `kode`, `nama_pos`, `nama_pic`, `alamat`, `no_telp`, `region`, `area`, `kota_kabupaten`, `salesman`, `cop`, `tanggal`, `ar`) VALUES
(1, '', 'Sinar Jaya Motor (SJM001)', 'Acung', 'Bogor Raya Km 43 No. 31', '02187910178', 'Jawa Barat', 'Cibinong', 'Bogor', '', 'class 4', NULL, NULL),
(2, '', 'Sinar Jaya Motor (SJM002)', '', 'Abd. Latif No. 009', '02549192568', 'Banten', 'Serang', 'Serang', '', 'class 4', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timestamp`
--

CREATE TABLE `timestamp` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_pos` varchar(100) NOT NULL,
  `tipe` enum('NOV','AO','NO','NOO','POS') NOT NULL,
  `area` varchar(100) NOT NULL,
  `status` enum('visit tambahan','visit wajib') NOT NULL,
  `tujuan` enum('visit','visit susulan') NOT NULL,
  `order` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timestamp`
--

INSERT INTO `timestamp` (`id`, `tanggal`, `nama_pos`, `tipe`, `area`, `status`, `tujuan`, `order`, `qty`, `keterangan`, `created_at`) VALUES
(1, '2025-08-21', 'Sinar Jaya Motor (SJM001)', 'AO', 'Cibinong', 'visit wajib', 'visit', 'Ichiban', 30, '', '2025-08-21 17:35:38'),
(2, '2025-08-21', 'Sinar Jaya Motor (SJM002)', 'AO', 'Serang', 'visit wajib', 'visit', 'Ichiban', 50, '', '2025-08-21 17:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$YxJjJ3FeUfveYfwGoZAz1OOPvKAG.hktsxm3o6inrguW8DVCBDepe', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_visit`
--
ALTER TABLE `daily_visit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_ichiban`
--
ALTER TABLE `data_ichiban`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timestamp`
--
ALTER TABLE `timestamp`
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
-- AUTO_INCREMENT for table `daily_visit`
--
ALTER TABLE `daily_visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_ichiban`
--
ALTER TABLE `data_ichiban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timestamp`
--
ALTER TABLE `timestamp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
