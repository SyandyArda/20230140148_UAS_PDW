<?php
session_start();
require_once '../config.php';

// 1. Cek Keamanan: Pastikan yang mengakses adalah mahasiswa yang sudah login dan metodenya POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

// 2. Ambil data dari form
if (isset($_POST['id_modul'], $_POST['id_praktikum'])) {
    $id_modul = $_POST['id_modul'];
    $id_praktikum = $_POST['id_praktikum']; // Digunakan untuk redirect kembali
    $id_mahasiswa = $_SESSION['user_id'];
    $file_path = '';

    // 3. Logika untuk memproses file yang diunggah
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/laporan/';
        
        // Buat nama file unik untuk mencegah tumpang tindih
        // Format: idmahasiswa_idmodul_waktu_namaasli.ekstensi
        $file_extension = pathinfo($_FILES['file_laporan']['name'], PATHINFO_EXTENSION);
        $file_name = $id_mahasiswa . '_' . $id_modul . '_' . time() . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;

        // Pindahkan file dari lokasi sementara ke folder tujuan
        if (move_uploaded_file($_FILES['file_laporan']['tmp_name'], $target_file)) {
            $file_path = $file_name; // Simpan nama file unik ini ke database
        } else {
            die("Gagal memindahkan file yang diunggah.");
        }
    } else {
        die("Error saat mengunggah file atau tidak ada file yang dipilih.");
    }

    // 4. Simpan data pengumpulan ke database
    if (!empty($file_path)) {
        $stmt = $conn->prepare("INSERT INTO pengumpulan_laporan (id_modul, id_mahasiswa, file_laporan) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_modul, $id_mahasiswa, $file_path);
        
        if ($stmt->execute()) {
            // Jika berhasil, kembalikan ke halaman detail praktikum
            header('Location: detail_praktikum.php?id_praktikum=' . $id_praktikum . '&status=upload_sukses');
            exit();
        } else {
            echo "Error saat menyimpan data ke database: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    // Jika data POST tidak lengkap
    header('Location: dashboard.php');
    exit();
}

$conn->close();
?>