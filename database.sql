--
-- Struktur tabel untuk `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur tabel untuk `mata_praktikum`
--
CREATE TABLE `mata_praktikum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_praktikum` varchar(20) NOT NULL,
  `nama_praktikum` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_praktikum` (`kode_praktikum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur tabel untuk `modul`
--
CREATE TABLE `modul` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_praktikum` int(11) NOT NULL,
  `nama_modul` varchar(100) NOT NULL,
  `deskripsi_modul` text DEFAULT NULL,
  `file_materi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_praktikum` (`id_praktikum`),
  CONSTRAINT `modul_ibfk_1` FOREIGN KEY (`id_praktikum`) REFERENCES `mata_praktikum` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur tabel untuk `pendaftaran_praktikum`
--
CREATE TABLE `pendaftaran_praktikum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mahasiswa` int(11) NOT NULL,
  `id_praktikum` int(11) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `pendaftaran_unik` (`id_mahasiswa`,`id_praktikum`),
  KEY `id_praktikum` (`id_praktikum`),
  CONSTRAINT `pendaftaran_praktikum_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pendaftaran_praktikum_ibfk_2` FOREIGN KEY (`id_praktikum`) REFERENCES `mata_praktikum` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur tabel untuk `pengumpulan_laporan`
--
CREATE TABLE `pengumpulan_laporan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_modul` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `file_laporan` varchar(255) NOT NULL,
  `nilai` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `tanggal_kumpul` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_modul` (`id_modul`),
  KEY `id_mahasiswa` (`id_mahasiswa`),
  CONSTRAINT `pengumpulan_laporan_ibfk_1` FOREIGN KEY (`id_modul`) REFERENCES `modul` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengumpulan_laporan_ibfk_2` FOREIGN KEY (`id_mahasiswa`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

