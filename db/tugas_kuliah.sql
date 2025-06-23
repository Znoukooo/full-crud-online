-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jun 2025 pada 17.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tugas_kuliah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `id` int(11) NOT NULL,
  `kode_matkul` varchar(10) DEFAULT NULL,
  `nama_matkul` varchar(100) DEFAULT NULL,
  `sks` varchar(11) NOT NULL,
  `dosen_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`id`, `kode_matkul`, `nama_matkul`, `sks`, `dosen_id`) VALUES
(1, '22PAM002', 'Bahasa Indonesia', '2', 16),
(2, '12TPLK009', 'Sistem Operasi', '3', NULL),
(3, '21PAM003', 'ALgoritma dan Pemrograman', '3', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumpulan`
--

CREATE TABLE `pengumpulan` (
  `id` int(11) NOT NULL,
  `tugas_id` int(11) DEFAULT NULL,
  `mahasiswa_id` int(11) DEFAULT NULL,
  `file_jawaban` varchar(255) DEFAULT NULL,
  `tanggal_kumpul` datetime DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas`
--

CREATE TABLE `tugas` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `file_tugas` varchar(255) DEFAULT NULL,
  `matkul_id` int(11) DEFAULT NULL,
  `dosen_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','dosen','mahasiswa') DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `email`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', 'muzhaffarkhaisanramzi@gmail.com\r\n'),
(2, '241011400049', 'unpam#400049', 'mahasiswa', 'KHAISAN RAMZI MUZHAFFAR', 'khaisanramzimuzhaffar12466@gmail.com\r\n'),
(3, '251012675627', 'unpam#675627', 'mahasiswa', 'REHAN RAHARDY', 'rehanrahardy2@gmail.com'),
(5, '201011159559', 'dosen#159559', 'dosen', 'Eka Puspita, S.Pd., M.H', 'ekapuspita88@gmail.com'),
(6, '251012100001', 'unpam#100001', 'mahasiswa', 'Ahmad Ramadhan', 'ahmad.ramadhan@unpam.ac.id'),
(7, '251012100002', 'unpam#100002', 'mahasiswa', 'Siti Aisyah', 'siti.aisyah@unpam.ac.id'),
(8, '251012100003', 'unpam#100003', 'mahasiswa', 'Budi Santoso', 'budi.santoso@unpam.ac.id'),
(10, '251012100005', 'unpam#100005', 'mahasiswa', 'Rizki Maulana', 'rizki.maulana@unpam.ac.id'),
(11, '251012100006', 'unpam#100006', 'mahasiswa', 'Nurhaliza Zahra', 'nurhaliza.zahra@unpam.ac.id'),
(12, '251012100007', 'unpam#100007', 'mahasiswa', 'Fajar Hidayat', 'fajar.hidayat@unpam.ac.id'),
(13, '251012100008', 'unpam#100008', 'mahasiswa', 'Intan Permata', 'intan.permata@unpam.ac.id'),
(14, '251012100009', 'unpam#100009', 'mahasiswa', 'Galih Pratama', 'galih.pratama@unpam.ac.id'),
(15, '251012100010', 'unpam#100010', 'mahasiswa', 'Laila Anjani', 'laila.anjani@unpam.ac.id'),
(16, '201011100001', 'dosen#100001', 'dosen', 'Dr. Hasan Basri', 'hasan.basri@unpam.ac.id'),
(17, '201011100002', 'dosen#100002', 'dosen', 'Maya Sari, M.Kom', 'maya.sari@unpam.ac.id'),
(18, '201011100003', 'dosen#100003', 'dosen', 'Ir. Joko Widodo', 'joko.widodo@unpam.ac.id'),
(19, '201011100004', 'dosen#100004', 'dosen', 'Ayu Lestari, S.T', 'ayu.lestari@unpam.ac.id'),
(20, '201011100005', 'dosen#100005', 'dosen', 'Fahmi Rizal, M.T', 'fahmi.rizal@unpam.ac.id'),
(21, '201011554549', 'dosen#554549', 'dosen', 'Ferdy, S.Kom', 'ferdy@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indeks untuk tabel `pengumpulan`
--
ALTER TABLE `pengumpulan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_id` (`tugas_id`),
  ADD KEY `mahasiswa_id` (`mahasiswa_id`);

--
-- Indeks untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matkul_id` (`matkul_id`),
  ADD KEY `dosen_id` (`dosen_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pengumpulan`
--
ALTER TABLE `pengumpulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD CONSTRAINT `mata_kuliah_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `pengumpulan`
--
ALTER TABLE `pengumpulan`
  ADD CONSTRAINT `pengumpulan_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`),
  ADD CONSTRAINT `pengumpulan_ibfk_2` FOREIGN KEY (`mahasiswa_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`matkul_id`) REFERENCES `mata_kuliah` (`id`),
  ADD CONSTRAINT `tugas_ibfk_2` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
