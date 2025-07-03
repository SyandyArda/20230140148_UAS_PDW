<?php
// Menampilkan semua error untuk debugging jika terjadi masalah
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config.php';

// Pastikan hanya asisten yang bisa melakukan aksi ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die("Akses ditolak.");
}

// ===================================================================
// BLOK UNTUK AKSI TAMBAH MODUL
// ===================================================================
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    // Pastikan folder untuk upload ada
    $upload_dir = '../uploads/materi/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $id_praktikum = $_POST['id_praktikum'];
    $nama_modul = $_POST['nama_modul'];
    // Mengambil data deskripsi dari form
    $deskripsi_modul = $_POST['deskripsi_modul'] ?? '';
    $file_path = '';

    // Logika Upload File
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == UPLOAD_ERR_OK) {
        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $target_file)) {
            $file_path = $file_name;
        } else {
            die("Gagal memindahkan file yang diunggah. Cek perizinan folder.");
        }
    }

    // Simpan data ke database (termasuk deskripsi)
    $stmt = $conn->prepare("INSERT INTO modul (id_praktikum, nama_modul, deskripsi_modul, file_materi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_praktikum, $nama_modul, $deskripsi_modul, $file_path);
    
    if ($stmt->execute()) {
        header('Location: kelola_modul.php?id_praktikum=' . $id_praktikum);
        exit();
    } else {
        die("Error saat menyimpan ke database: " . $stmt->error);
    }
    $stmt->close();
} 
// ===================================================================
// BLOK UNTUK AKSI EDIT MODUL
// ===================================================================
elseif (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id_modul = $_POST['id_modul'];
    $id_praktikum = $_POST['id_praktikum'];
    $nama_modul = $_POST['nama_modul'];
    // Mengambil data deskripsi dari form edit
    $deskripsi_modul = $_POST['deskripsi_modul'] ?? '';
    $file_lama = $_POST['file_lama'];
    $file_path = $file_lama;

    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/materi/';
        if (!empty($file_lama) && file_exists($upload_dir . $file_lama)) {
            unlink($upload_dir . $file_lama);
        }

        $file_name = time() . '_' . basename($_FILES['file_materi']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $target_file)) {
            $file_path = $file_name;
        } else {
            echo "Gagal mengunggah file baru.";
            exit();
        }
    }

    // Update data di database (termasuk deskripsi)
    $stmt = $conn->prepare("UPDATE modul SET nama_modul = ?, deskripsi_modul = ?, file_materi = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nama_modul, $deskripsi_modul, $file_path, $id_modul);
    
    if ($stmt->execute()) {
        header('Location: kelola_modul.php?id_praktikum=' . $id_praktikum);
        exit();
    } else {
        echo "Error saat update: " . $stmt->error;
    }
    $stmt->close();
} 
// ===================================================================
// BLOK UNTUK AKSI HAPUS MODUL
// ===================================================================
elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_modul = $_GET['id_modul'];
    $id_praktikum = $_GET['id_praktikum'];

    $stmt_select = $conn->prepare("SELECT file_materi FROM modul WHERE id = ?");
    $stmt_select->bind_param("i", $id_modul);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $modul = $result->fetch_assoc();

    if ($modul && !empty($modul['file_materi'])) {
        $file_to_delete = '../uploads/materi/' . $modul['file_materi'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }
    $stmt_select->close();

    $stmt_delete = $conn->prepare("DELETE FROM modul WHERE id = ?");
    $stmt_delete->bind_param("i", $id_modul);
    if ($stmt_delete->execute()) {
        header('Location: kelola_modul.php?id_praktikum=' . $id_praktikum);
        exit();
    } else {
        echo "Gagal menghapus modul: " . $stmt_delete->error;
    }
    $stmt_delete->close();
}

$conn->close();
?>