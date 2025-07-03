<?php
session_start();
// DIUBAH: Path ke config.php diperbaiki untuk naik satu level direktori
require_once '../config.php';

// 1. Cek Keamanan: Pastikan yang mengakses adalah mahasiswa yang sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    // DIUBAH: Path ke login.php diperbaiki
    header("Location: ../login.php");
    exit();
}

// 2. Ambil Data yang Diperlukan
if (!isset($_GET['id_praktikum'])) {
    // Path ke katalog.php sudah benar karena berada di folder yang sama
    header("Location: katalog.php");
    exit();
}
$id_mahasiswa = $_SESSION['user_id'];
$id_praktikum = $_GET['id_praktikum'];

// 3. Cek Agar Mahasiswa Tidak Mendaftar Dua Kali di Praktikum yang Sama
$check_stmt = $conn->prepare("SELECT id FROM pendaftaran_praktikum WHERE id_mahasiswa = ? AND id_praktikum = ?");
$check_stmt->bind_param("ii", $id_mahasiswa, $id_praktikum);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Jika sudah terdaftar, kembalikan ke katalog dengan notifikasi 'terdaftar'
    header("Location: katalog.php?status=terdaftar");
    exit();
}
$check_stmt->close();


// 4. Jika Belum Terdaftar, Masukkan Data Pendaftaran ke Database
$insert_stmt = $conn->prepare("INSERT INTO pendaftaran_praktikum (id_mahasiswa, id_praktikum) VALUES (?, ?)");
$insert_stmt->bind_param("ii", $id_mahasiswa, $id_praktikum);

if ($insert_stmt->execute()) {
    // Jika pendaftaran berhasil, kembalikan ke katalog dengan notifikasi 'sukses'
    header("Location: katalog.php?status=sukses");
    exit();
} else {
    // Jika ada error saat query
    echo "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
}

$insert_stmt->close();
$conn->close();
?>