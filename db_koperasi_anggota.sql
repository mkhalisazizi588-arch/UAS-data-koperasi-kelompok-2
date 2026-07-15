-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 05:26 AM
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
-- Database: `db_koperasi_anggota`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `nomor_anggota` varchar(30) NOT NULL,
  `nama_anggota` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `tanggal_daftar` date NOT NULL,
  `status_anggota` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nomor_anggota`, `nama_anggota`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_hp`, `pekerjaan`, `tanggal_daftar`, `status_anggota`, `created_at`) VALUES
(1, 'KOP-2026-001', 'Ahmad Fauzi', 'L', 'Banda Aceh', '1995-03-12', 'Banda Aceh', '081234567001', 'Wiraswasta', '2026-01-10', 'aktif', '2026-07-01 01:25:43'),
(2, 'KOP-2026-002', 'Siti Rahmah', 'P', 'Aceh Besar', '1998-07-22', 'Aceh Besar', '081234567002', 'Guru', '2026-01-12', 'aktif', '2026-07-01 01:25:43'),
(3, 'KOP-2026-003', 'M. Ikhsan', 'L', 'Pidie', '1993-11-05', 'Pidie', '081234567003', 'Petani', '2026-01-15', 'aktif', '2026-07-01 01:25:43'),
(4, 'KOP-2026-004', 'Nur Aisyah', 'P', 'Bireuen', '1996-09-18', 'Bireuen', '081234567004', 'Pedagang', '2026-01-20', 'aktif', '2026-07-01 01:25:43'),
(5, 'KOP-2026-005', 'Rizky Maulana', 'L', 'Lhokseumawe', '1994-12-01', 'Lhokseumawe', '081234567005', 'Pegawai Swasta', '2026-01-25', 'aktif', '2026-07-01 01:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `simpanan`
--

CREATE TABLE `simpanan` (
  `id_simpanan` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `tanggal_simpanan` date NOT NULL,
  `jenis_simpanan` enum('pokok','wajib','sukarela') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simpanan`
--

INSERT INTO `simpanan` (`id_simpanan`, `id_anggota`, `tanggal_simpanan`, `jenis_simpanan`, `jumlah`, `keterangan`, `created_by`, `created_at`) VALUES
(1, 1, '2026-07-01', 'pokok', 100000.00, 'Simpanan pokok awal', 1, '2026-07-01 01:25:43'),
(2, 1, '2026-07-01', 'wajib', 50000.00, 'Simpanan wajib bulan Juli', 1, '2026-07-01 01:25:43'),
(3, 2, '2026-07-01', 'pokok', 100000.00, 'Simpanan pokok awal', 1, '2026-07-01 01:25:43'),
(4, 2, '2026-07-01', 'sukarela', 150000.00, 'Simpanan sukarela', 1, '2026-07-01 01:25:43'),
(5, 3, '2026-07-01', 'wajib', 50000.00, 'Simpanan wajib bulan Juli', 1, '2026-07-01 01:25:43'),
(6, 4, '2026-07-01', 'pokok', 100000.00, 'Simpanan pokok awal', 1, '2026-07-01 01:25:43'),
(7, 5, '2026-07-01', 'sukarela', 200000.00, 'Simpanan sukarela', 1, '2026-07-01 01:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','operator') DEFAULT 'operator',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password_hash`, `role`, `status`, `created_at`) VALUES
(1, 'Administrator Koperasi', 'admin', 'password_hash_diisi_backend', 'admin', 'aktif', '2026-07-01 01:25:43'),
(2, 'Operator Koperasi', 'operator', 'password_hash_diisi_backend', 'operator', 'aktif', '2026-07-01 01:25:43');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_laporan_simpanan`
-- (See below for the actual view)
--
CREATE TABLE `view_laporan_simpanan` (
`id_anggota` int(11)
,`nomor_anggota` varchar(30)
,`nama_anggota` varchar(100)
,`no_hp` varchar(20)
,`status_anggota` enum('aktif','nonaktif')
,`total_simpanan_pokok` decimal(37,2)
,`total_simpanan_wajib` decimal(37,2)
,`total_simpanan_sukarela` decimal(37,2)
,`total_seluruh_simpanan` decimal(37,2)
);

-- --------------------------------------------------------

--
-- Structure for view `view_laporan_simpanan`
--
DROP TABLE IF EXISTS `view_laporan_simpanan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_laporan_simpanan`  AS SELECT `a`.`id_anggota` AS `id_anggota`, `a`.`nomor_anggota` AS `nomor_anggota`, `a`.`nama_anggota` AS `nama_anggota`, `a`.`no_hp` AS `no_hp`, `a`.`status_anggota` AS `status_anggota`, sum(case when `s`.`jenis_simpanan` = 'pokok' then `s`.`jumlah` else 0 end) AS `total_simpanan_pokok`, sum(case when `s`.`jenis_simpanan` = 'wajib' then `s`.`jumlah` else 0 end) AS `total_simpanan_wajib`, sum(case when `s`.`jenis_simpanan` = 'sukarela' then `s`.`jumlah` else 0 end) AS `total_simpanan_sukarela`, sum(`s`.`jumlah`) AS `total_seluruh_simpanan` FROM (`anggota` `a` left join `simpanan` `s` on(`a`.`id_anggota` = `s`.`id_anggota`)) GROUP BY `a`.`id_anggota`, `a`.`nomor_anggota`, `a`.`nama_anggota`, `a`.`no_hp`, `a`.`status_anggota` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `nomor_anggota` (`nomor_anggota`);

--
-- Indexes for table `simpanan`
--
ALTER TABLE `simpanan`
  ADD PRIMARY KEY (`id_simpanan`),
  ADD KEY `fk_simpanan_anggota` (`id_anggota`),
  ADD KEY `fk_simpanan_user` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `simpanan`
--
ALTER TABLE `simpanan`
  MODIFY `id_simpanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `simpanan`
--
ALTER TABLE `simpanan`
  ADD CONSTRAINT `fk_simpanan_anggota` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_simpanan_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
