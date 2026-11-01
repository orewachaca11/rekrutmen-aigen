-- Database: rekrutmen_aigen
-- Sistem Informasi Rekrutmen dan Uji Kompetensi Calon Karyawan Bootcamp IT
-- PT. Aigen Global Teknologi Tangerang

CREATE DATABASE IF NOT EXISTS `rekrutmen_aigen` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rekrutmen_aigen`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for `pengguna`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengguna`;
CREATE TABLE `pengguna` (
  `id_pengguna` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('Admin','Penguji') NOT NULL,
  PRIMARY KEY (`id_pengguna`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `nama`, `kategori`) VALUES
('ID0001', 'admin', 'admin123', 'Raden Muhammad Prakoso', 'Admin'),
('ID0002', 'penguji', 'penguji123', 'Tim Penguji Bootcamp IT', 'Penguji'),
('ID0003', 'daffa', 'daffa123', 'Muchammad Daffa Syaidan', 'Admin'),
('ID0004', 'firman', 'firman123', 'Muhammad Firmansyah', 'Admin');

-- --------------------------------------------------------
-- Table structure for `kriteria`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kriteria`;
CREATE TABLE `kriteria` (
  `kode_kriteria` varchar(10) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `bobot` decimal(5,2) NOT NULL,
  `tipe` enum('Benefit','Cost') NOT NULL,
  PRIMARY KEY (`kode_kriteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kriteria` (`kode_kriteria`, `nama_kriteria`, `bobot`, `tipe`) VALUES
('C1', 'Skor Hackerrank Frontend', 0.10, 'Benefit'),
('C2', 'Skor Hackerrank Backend', 0.10, 'Benefit'),
('C3', 'Usia', 0.15, 'Benefit'),
('C4', 'Pendidikan Terakhir', 0.20, 'Benefit'),
('C5', 'Lama Pengalaman Kerja', 0.20, 'Benefit'),
('C6', 'Waktu Tempuh ke Perusahaan', 0.05, 'Cost'),
('C7', 'Hasil Tes Tertulis', 0.20, 'Benefit');

-- --------------------------------------------------------
-- Table structure for `subkriteria`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `subkriteria`;
CREATE TABLE `subkriteria` (
  `id_sub` int(11) NOT NULL AUTO_INCREMENT,
  `kode_kriteria` varchar(10) NOT NULL,
  `pilihan_kriteria` varchar(100) NOT NULL,
  `nilai` int(11) NOT NULL,
  PRIMARY KEY (`id_sub`),
  KEY `kode_kriteria` (`kode_kriteria`),
  CONSTRAINT `fk_subkriteria_kriteria` FOREIGN KEY (`kode_kriteria`) REFERENCES `kriteria` (`kode_kriteria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `subkriteria` (`kode_kriteria`, `pilihan_kriteria`, `nilai`) VALUES
('C1', '50 - 60', 1),
('C1', '75 - 85', 2),
('C1', '90 - 95', 3),
('C1', '100', 4),
('C2', '50 - 60', 1),
('C2', '75 - 85', 2),
('C2', '90 - 95', 3),
('C2', '100', 4),
('C3', '< 19th & > 30th', 1),
('C3', '19th - 21th', 2),
('C3', '22th - 24th', 3),
('C3', '25th - 30th', 4),
('C4', 'SMP', 1),
('C4', 'SMA/SMK/MA', 2),
('C4', 'D3', 3),
('C4', 'S1', 4),
('C5', '< 1th', 1),
('C5', '1th - 2th', 2),
('C5', '3th - 5th', 3),
('C5', '> 5th', 4),
('C6', '> 90 menit', 1),
('C6', '61 menit - 90 menit', 2),
('C6', '30 menit - 60 menit', 3),
('C6', '< 30 menit', 4),
('C7', '< 12 soal', 1),
('C7', '12 soal - 15 soal', 2),
('C7', '16 soal - 19 soal', 3),
('C7', '20 soal', 4);

-- --------------------------------------------------------
-- Table structure for `peserta`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `peserta`;
CREATE TABLE `peserta` (
  `id_peserta` varchar(20) NOT NULL,
  `nama_peserta` varchar(100) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') NOT NULL,
  `no_telepon` varchar(25) NOT NULL,
  PRIMARY KEY (`id_peserta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `peserta` (`id_peserta`, `nama_peserta`, `jurusan`, `alamat`, `tanggal_lahir`, `jenis_kelamin`, `no_telepon`) VALUES
('P001', 'Budi Santoso', 'Teknik Informatika', 'Jl. Sudirman No. 12, Tangerang', '1998-05-14', 'Laki-Laki', '081234567801'),
('P002', 'Dewi Rahayu', 'Sistem Informasi', 'Jl. Merdeka No. 45, Jakarta Barat', '2001-08-20', 'Perempuan', '081234567802'),
('P003', 'Hadi Pratama', 'Teknik Informatika', 'Jl. BSD Raya Barat No. 8, Tangerang Selatan', '1997-03-10', 'Laki-Laki', '081234567803'),
('P004', 'Nita Anggraini', 'Teknik Komputer', 'Jl. Pahlawan No. 19, Ciputat', '1996-11-25', 'Perempuan', '081234567804'),
('P005', 'Rizky Saputra', 'Sistem Informasi', 'Jl. Cempaka Putih No. 5, Jakarta Pusat', '2002-01-15', 'Laki-Laki', '081234567805'),
('P006', 'Siti Aminah', 'Rekayasa Perangkat Lunak', 'Jl. Anggrek No. 22, Tangerang Selatan', '2001-07-09', 'Perempuan', '081234567806'),
('P007', 'Andi Susilo', 'Teknik Informatika', 'Jl. Melati No. 88, Bintaro', '2000-09-17', 'Laki-Laki', '081234567807'),
('P008', 'Maya Sari', 'Ilmu Komputer', 'Jl. Teratai No. 4, Pamulang', '2004-02-18', 'Perempuan', '081234567808'),
('P009', 'Joko Santoso', 'Teknik Informatika', 'Jl. Mawar No. 16, Ciputat Timur', '2002-12-05', 'Laki-Laki', '081234567809'),
('P010', 'Yulia Putri', 'Sistem Informasi', 'Jl. Kenanga No. 31, Jakarta Selatan', '1999-06-22', 'Perempuan', '081234567810'),
('P011', 'Fajar Wibowo', 'Teknik Informatika', 'Jl. Flamboyan No. 9, Serpong', '1999-04-11', 'Laki-Laki', '081234567811'),
('P012', 'Rina Fitriani', 'Teknik Komputer', 'Jl. Boulevard BSD No. 50, Serpong', '1999-10-30', 'Perempuan', '081234567812'),
('P013', 'Adi Nugroho', 'Rekayasa Perangkat Lunak', 'Jl. Raya Ciater No. 14, Serpong', '2004-05-19', 'Laki-Laki', '081234567813'),
('P014', 'Eka Wahyuni', 'Sistem Informasi', 'Jl. Pajajaran No. 23, Tangerang', '2002-03-08', 'Perempuan', '081234567814'),
('P015', 'Irfan Kusuma', 'Teknik Informatika', 'Jl. Alam Sutera No. 101, Tangerang Selatan', '1997-09-15', 'Laki-Laki', '081234567815'),
('P016', 'Anisa Lestari', 'Ilmu Komputer', 'Jl. Graha Raya No. 40, Serpong Utara', '1996-08-14', 'Perempuan', '081234567816'),
('P017', 'Dito Kurniawan', 'Teknik Informatika', 'Jl. Gading Serpong No. 77, Tangerang', '2001-12-28', 'Laki-Laki', '081234567817'),
('P018', 'Retno Utami', 'Sistem Informasi', 'Jl. WR Supratman No. 6, Ciputat', '1999-07-04', 'Perempuan', '081234567818'),
('P019', 'Ari Wijaya', 'Teknik Komputer', 'Jl. Legok No. 15, Tangerang', '2000-02-17', 'Laki-Laki', '081234567819'),
('P020', 'Rini Setiawan', 'Teknik Informatika', 'Jl. Pondok Aren No. 18, Tangerang Selatan', '1996-04-20', 'Perempuan', '081234567820'),
('P021', 'Yoga Prasetyo', 'Rekayasa Perangkat Lunak', 'Jl. Rawa Buntu No. 29, Serpong', '2002-05-03', 'Laki-Laki', '081234567821'),
('P022', 'Ria Permata', 'Sistem Informasi', 'Jl. Veteran No. 8, Jakarta Selatan', '2002-11-12', 'Perempuan', '081234567822'),
('P023', 'Agus Hermawan', 'Teknik Informatika', 'Jl. Cirendeu Raya No. 34, Tangerang Selatan', '2001-01-29', 'Laki-Laki', '081234567823'),
('P024', 'Lina Hartati', 'Teknik Informatika', 'Jl. Boulevard Gading No. 12, Serpong', '1998-03-16', 'Perempuan', '081234567824'),
('P025', 'Agung Purnomo', 'Sistem Informasi', 'Jl. Taman Tekno No. 55, Serpong', '2001-06-25', 'Laki-Laki', '081234567825'),
('P026', 'Sari Indah', 'Ilmu Komputer', 'Jl. Ciater Raya No. 80, Tangerang Selatan', '1997-10-18', 'Perempuan', '081234567826'),
('P027', 'Dodi Susanto', 'Teknik Informatika', 'Jl. Siliwangi No. 42, Pamulang', '1998-12-03', 'Laki-Laki', '081234567827'),
('P028', 'Rani Puspita', 'Rekayasa Perangkat Lunak', 'Jl. Surya Kencana No. 7, Pamulang', '2002-08-07', 'Perempuan', '081234567828'),
('P029', 'Dani Firmansyah', 'Teknik Komputer', 'Jl. KH Hasyim Ashari No. 91, Tangerang', '2001-09-02', 'Laki-Laki', '081234567829'),
('P030', 'Nova Cahaya', 'Sistem Informasi', 'Jl. Raden Saleh No. 33, Karang Tengah', '2002-07-21', 'Perempuan', '081234567830');

-- --------------------------------------------------------
-- Table structure for `penilaian`
-- (Nilai skala 1 - 4 sesuai Tabel 4.4 Dokumen)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `penilaian`;
CREATE TABLE `penilaian` (
  `id_penilaian` int(11) NOT NULL AUTO_INCREMENT,
  `id_peserta` varchar(20) NOT NULL,
  `c1` int(11) NOT NULL,
  `c2` int(11) NOT NULL,
  `c3` int(11) NOT NULL,
  `c4` int(11) NOT NULL,
  `c5` int(11) NOT NULL,
  `c6` int(11) NOT NULL,
  `c7` int(11) NOT NULL,
  PRIMARY KEY (`id_penilaian`),
  UNIQUE KEY `uq_penilaian_peserta` (`id_peserta`),
  CONSTRAINT `fk_penilaian_peserta` FOREIGN KEY (`id_peserta`) REFERENCES `peserta` (`id_peserta`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `penilaian` (`id_peserta`, `c1`, `c2`, `c3`, `c4`, `c5`, `c6`, `c7`) VALUES
('P001', 4, 3, 1, 2, 1, 1, 2),
('P002', 3, 2, 2, 1, 1, 4, 1),
('P003', 4, 4, 4, 2, 4, 2, 2),
('P004', 4, 2, 4, 2, 4, 3, 2),
('P005', 3, 2, 2, 2, 1, 4, 2),
('P006', 2, 1, 2, 2, 2, 3, 3),
('P007', 1, 2, 2, 2, 3, 4, 3),
('P008', 2, 2, 1, 2, 1, 4, 2),
('P009', 2, 2, 2, 2, 3, 4, 2),
('P010', 2, 2, 3, 2, 1, 4, 3),
('P011', 4, 3, 3, 2, 3, 4, 2),
('P012', 4, 2, 3, 2, 2, 4, 3),
('P013', 3, 2, 1, 2, 1, 2, 2),
('P014', 2, 2, 2, 2, 1, 4, 2),
('P015', 3, 3, 4, 2, 4, 1, 3),
('P016', 4, 1, 4, 2, 4, 4, 2),
('P017', 4, 2, 2, 2, 1, 4, 3),
('P018', 3, 2, 3, 2, 3, 4, 3),
('P019', 2, 2, 3, 2, 3, 4, 2),
('P020', 2, 2, 4, 4, 3, 3, 3),
('P021', 2, 2, 2, 2, 3, 2, 3),
('P022', 1, 2, 2, 2, 1, 4, 1),
('P023', 3, 2, 2, 2, 1, 3, 2),
('P024', 4, 4, 3, 4, 2, 2, 3),
('P025', 3, 2, 2, 2, 2, 3, 3),
('P026', 3, 2, 4, 2, 3, 4, 2),
('P027', 3, 3, 3, 2, 3, 4, 2),
('P028', 2, 1, 2, 2, 1, 4, 1),
('P029', 2, 3, 2, 2, 2, 3, 2),
('P030', 2, 2, 2, 2, 2, 3, 1);

-- --------------------------------------------------------
-- Table structure for `hasil_seleksi`
-- (Hasil ranking tersimpan sesuai Tabel 4.7 Dokumen)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hasil_seleksi`;
CREATE TABLE `hasil_seleksi` (
  `id_hasil` int(11) NOT NULL AUTO_INCREMENT,
  `id_peserta` varchar(20) NOT NULL,
  `total_nilai` decimal(6,4) NOT NULL,
  `ranking` int(11) NOT NULL,
  `status` enum('Lolos','Tidak Lolos') NOT NULL,
  `tanggal_perhitungan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hasil`),
  UNIQUE KEY `uq_hasil_peserta` (`id_peserta`),
  CONSTRAINT `fk_hasil_peserta` FOREIGN KEY (`id_peserta`) REFERENCES `peserta` (`id_peserta`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `hasil_seleksi` (`id_peserta`, `total_nilai`, `ranking`, `status`) VALUES
('P015', 0.8500, 1, 'Lolos'),
('P024', 0.8375, 2, 'Lolos'),
('P020', 0.8167, 3, 'Lolos'),
('P003', 0.7958, 4, 'Lolos'),
('P004', 0.7500, 5, 'Lolos'),
('P016', 0.7208, 6, 'Lolos'),
('P018', 0.7000, 7, 'Lolos'),
('P011', 0.6833, 8, 'Tidak Lolos'),
('P012', 0.6750, 9, 'Tidak Lolos'),
('P026', 0.6708, 10, 'Tidak Lolos'),
('P027', 0.6583, 11, 'Tidak Lolos'),
('P025', 0.6167, 12, 'Tidak Lolos'),
('P007', 0.6125, 13, 'Tidak Lolos'),
('P019', 0.6083, 14, 'Tidak Lolos'),
('P017', 0.5875, 15, 'Tidak Lolos'),
('P010', 0.5750, 16, 'Tidak Lolos'),
('P021', 0.5750, 17, 'Tidak Lolos'),
('P009', 0.5708, 18, 'Tidak Lolos'),
('P006', 0.5667, 19, 'Tidak Lolos'),
('P029', 0.5500, 20, 'Tidak Lolos'),
('P001', 0.5458, 21, 'Tidak Lolos'),
('P023', 0.5000, 22, 'Tidak Lolos'),
('P005', 0.4958, 23, 'Tidak Lolos'),
('P013', 0.4708, 24, 'Tidak Lolos'),
('P014', 0.4708, 25, 'Tidak Lolos'),
('P030', 0.4583, 26, 'Tidak Lolos'),
('P008', 0.4333, 27, 'Tidak Lolos'),
('P002', 0.3792, 28, 'Tidak Lolos'),
('P022', 0.3792, 29, 'Tidak Lolos'),
('P028', 0.3792, 30, 'Tidak Lolos');

SET FOREIGN_KEY_CHECKS = 1;
