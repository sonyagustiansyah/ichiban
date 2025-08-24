-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 03:22 PM
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
(1, '2025-08-08', 'Dimensi Motor', 'Margonda Ry No. 026', 'Pancoran Mas', '2025-08-24 11:52:53'),
(2, '2025-08-08', 'Depok Sukses Motor', 'Margonda Ry Auto Part No. 041-049', 'Pancoran Mas', '2025-08-24 11:53:43'),
(3, '2025-08-08', 'Azis Motor', 'Margonda Ry No. 020 Lt. 1 No. 004', 'Pancoran Mas', '2025-08-24 11:53:58'),
(4, '2025-08-11', 'Sentosa Motor', 'Jl. Tole Iskandar No.59, Sentra Otomotif Pasar Segar Blok KGB-1 No.9-10', 'Pancoran Mas', '2025-08-24 11:54:17'),
(5, '2025-08-11', 'Perdana Jaya Motor', 'Jl. Raya Tole Iskandar Blok KAB 1/34 Pasar Segar, Pancoran Mas', 'Pancoran Mas', '2025-08-24 11:54:31'),
(6, '2025-08-11', 'Alindha Motor', 'Tole Iskandar Komp. Adhi Karya No. Kav 61', 'Pancoran Mas', '2025-08-24 11:54:44');

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
(1, 'DM', 'Dimensi Motor', 'Asen', 'Margonda Ry No. 026', '021-7720-5382', 'Jawa Barat', 'Pancoran Mas', 'Depok', NULL, 'class 4', NULL, NULL),
(2, 'DSM', 'Depok Sukses Motor', 'Paijo', 'Margonda Ry Auto Part No. 041-049', '021-7721-2324', 'Jawa Barat', 'Pancoran Mas', 'Depok', NULL, 'class 4', NULL, NULL),
(3, 'AM', 'Azis Motor', NULL, 'Margonda Ry No. 020 Lt. 1 No. 004', '021-7720-2366', 'Jawa Barat', 'Pancoran Mas', 'Depok', NULL, 'class 4', NULL, NULL),
(4, 'SM', 'Sentosa Motor', NULL, 'Jl. Tole Iskandar No.59, Sentra Otomotif Pasar Segar Blok KGB-1 No.9-10', '0812-6023-9716', 'Jawa Barat', 'Pancoran Mas', 'Depok', NULL, 'class 4', NULL, NULL),
(5, 'PJM', 'Perdana Jaya Motor', NULL, 'Jl. Raya Tole Iskandar Blok KAB 1/34 Pasar Segar, Pancoran Mas', '021-2932-3099', 'Jawa Barat', 'Pancoran Mas', 'Depok', NULL, 'class 4', NULL, NULL),
(6, 'ALM', 'Alindha Motor', 'Yuri/Mono', 'Tole Iskandar Komp. Adhi Karya No. Kav 61', '021-7713248', 'Jawa Barat', 'Pancoran Mas', 'Depok', NULL, 'class 4', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ichiban_orders`
--

CREATE TABLE `ichiban_orders` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kode` varchar(50) NOT NULL,
  `nama_toko` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `nama_sales` varchar(100) DEFAULT NULL,
  `cop_class` enum('Class 1','Class 2','Class 3','Class 4','Class 5','Class 6') NOT NULL,
  `diskon` decimal(5,2) DEFAULT NULL,
  `top_day` int(11) DEFAULT NULL,
  `order_set` int(11) DEFAULT NULL,
  `supply_set` int(11) DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_diterima` date DEFAULT NULL,
  `ar_deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ichiban_orders`
--

INSERT INTO `ichiban_orders` (`id`, `tanggal`, `kode`, `nama_toko`, `alamat`, `area`, `nama_sales`, `cop_class`, `diskon`, `top_day`, `order_set`, `supply_set`, `tanggal_kirim`, `tanggal_diterima`, `ar_deadline`, `created_at`) VALUES
(1, '2025-08-08', 'DM', 'Dimensi Motor', 'Margonda Ry No. 026', 'Pancoran Mas', 'Thomas Edison', 'Class 4', 15.00, 45, 100, 1000, '2025-08-08', '2025-08-11', '2025-09-22', '2025-08-24 12:26:28'),
(2, '2025-08-08', 'DSM', 'Depok Sukses Motor', 'Margonda Ry Auto Part No. 041-049', 'Pancoran Mas', 'Thomas Edison', 'Class 4', 15.00, 45, 100, 900, '2025-08-08', '2025-08-11', '2025-09-22', '2025-08-24 12:29:21'),
(3, '2025-08-11', 'SM', 'Sentosa Motor', 'Jl. Tole Iskandar No.59, Sentra Otomotif Pasar Segar Blok KGB-1 No.9-10', 'Pancoran Mas', 'Thomas Edison', 'Class 4', 15.00, 45, 100, 800, '2025-08-11', '2025-08-12', '2025-09-25', '2025-08-24 12:31:58'),
(4, '2025-08-11', 'PJM', 'Perdana Jaya Motor', 'Jl. Raya Tole Iskandar Blok KAB 1/34 Pasar Segar, Pancoran Mas', 'Pancoran Mas', 'Thomas Edison', 'Class 4', 15.00, 45, 100, 700, '2025-08-11', '2025-08-12', '2025-09-25', '2025-08-24 12:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `stock_card_ichiban`
--

CREATE TABLE `stock_card_ichiban` (
  `id` int(11) NOT NULL,
  `kode_ichiban` varchar(100) NOT NULL,
  `nomor_oem` varchar(100) DEFAULT NULL,
  `merk_mobil` varchar(100) DEFAULT NULL,
  `nama_mobil` varchar(100) DEFAULT NULL,
  `model_mobil` varchar(100) DEFAULT NULL,
  `posisi` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `set` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_card_ichiban`
--

INSERT INTO `stock_card_ichiban` (`id`, `kode_ichiban`, `nomor_oem`, `merk_mobil`, `nama_mobil`, `model_mobil`, `posisi`, `qty`, `set`, `created_at`) VALUES
(1, 'SL-TY0044', '48820-B0010', 'Toyota', 'Avanza', 'New Avanza', 'Front R/L', 2, 0, '2025-08-24 12:43:33'),
(2, 'SL-TY0044', '48820-B0010', 'Toyota', 'Daihatsu Xenia', 'New Xenia', 'Front R/L', 2, 0, '2025-08-24 12:44:29'),
(3, 'SL-TY0065', '48820-26051', 'Toyota', 'Kijang Innova', 'Innova Reborn', 'Front R/L', 2, 0, '2025-08-24 12:47:36'),
(4, 'SL-TY0065', '48820-26051', 'Toyota', 'Hilux Vigo SC', 'New Hilux Revo SC', 'Front R/L', 2, 0, '2025-08-24 12:49:27'),
(5, 'SL-TY0065', '48820-26051', 'Toyota', 'Hiace Commuter', 'Hiace Commuter', 'Front R/L', 2, 0, '2025-08-24 12:50:36'),
(6, 'SL-TY0068', '48820-B0020', 'Toyota', 'Rush', 'Daihatsu Terios', 'Front R/L', 2, 0, '2025-08-24 12:52:02');

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
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timestamp`
--

INSERT INTO `timestamp` (`id`, `tanggal`, `nama_pos`, `tipe`, `area`, `status`, `tujuan`, `order`, `qty`, `keterangan`, `foto`, `created_at`) VALUES
(1, '2025-08-08', 'Dimensi Motor', 'POS', 'Pancoran Mas', 'visit wajib', 'visit', 'Ichiban', 100, '', 'uploads/img_68aaffa6db1bf0.37139455.jpeg', '2025-08-24 12:03:50'),
(2, '2025-08-08', 'Depok Sukses Motor', 'POS', 'Pancoran Mas', 'visit wajib', 'visit', 'Ichiban', 100, '', 'uploads/img_68ab00495c30a1.57458409.jpeg', '2025-08-24 12:06:33'),
(3, '2025-08-08', 'Azis Motor', 'POS', 'Pancoran Mas', 'visit wajib', 'visit', '', 0, 'Outlet sudah tutup dan Game Over', 'uploads/img_68ab0087b7fea3.85177015.jpeg', '2025-08-24 12:07:35'),
(4, '2025-08-11', 'Sentosa Motor', 'POS', 'Pancoran Mas', 'visit wajib', 'visit', 'Ichiban', 100, '', 'uploads/img_68ab00e4d2b052.02961172.jpeg', '2025-08-24 12:09:08'),
(5, '2025-08-11', 'Perdana Jaya Motor', 'POS', 'Pancoran Mas', 'visit wajib', 'visit', 'Ichiban', 100, '', 'uploads/img_68ab01e88bba65.22105574.jpeg', '2025-08-24 12:13:28'),
(6, '2025-08-11', 'Alindha Motor', 'POS', 'Pancoran Mas', 'visit wajib', 'visit', '', 0, 'Outlet berubah jadi bermain kaca film', 'uploads/img_68ab02150635a3.93348898.jpeg', '2025-08-24 12:14:13');

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
-- Indexes for table `ichiban_orders`
--
ALTER TABLE `ichiban_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_card_ichiban`
--
ALTER TABLE `stock_card_ichiban`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `data_ichiban`
--
ALTER TABLE `data_ichiban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ichiban_orders`
--
ALTER TABLE `ichiban_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stock_card_ichiban`
--
ALTER TABLE `stock_card_ichiban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `timestamp`
--
ALTER TABLE `timestamp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
