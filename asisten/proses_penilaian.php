<?php
session_start();
require_once '../config.php';

// Pastikan hanya asisten yang bisa mengakses dan data dikirim via POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit();
}

// Ambil data dari form
$id_laporan = $_POST['id_laporan'];
$nilai = $_POST['nilai'];
$feedback = $_POST['feedback'];

// Validasi sederhana
if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
    die("Nilai tidak valid.");
}

// Update database dengan nilai dan feedback baru
$stmt = $conn->prepare("UPDATE pengumpulan_laporan SET nilai = ?, feedback = ? WHERE id = ?");
$stmt->bind_param("isi", $nilai, $feedback, $id_laporan);

if ($stmt->execute()) {
    // Jika berhasil, kembalikan ke halaman daftar laporan
    header("Location: laporan_masuk.php?status=sukses_nilai");
    exit();
} else {
    // Jika gagal
    echo "Error saat menyimpan penilaian: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>