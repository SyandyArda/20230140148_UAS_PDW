<?php
session_start();
require_once '../config.php';

// Pastikan hanya asisten yang bisa melakukan aksi ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit();
}

// ===================================================================
// BLOK UNTUK AKSI TAMBAH PRAKTIKUM
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $kode = $_POST['kode_praktikum'];
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("INSERT INTO mata_praktikum (kode_praktikum, nama_praktikum, deskripsi) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $kode, $nama, $deskripsi);

    if ($stmt->execute()) {
        header('Location: kelola_praktikum.php?status=sukses_tambah');
    } else {
        header('Location: tambah_praktikum.php?status=gagal');
    }
    $stmt->close();
}
// ===================================================================
// BLOK UNTUK AKSI EDIT PRAKTIKUM
// ===================================================================
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id = $_POST['id'];
    $kode = $_POST['kode_praktikum'];
    $nama = $_POST['nama_praktikum'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("UPDATE mata_praktikum SET kode_praktikum = ?, nama_praktikum = ?, deskripsi = ? WHERE id = ?");
    $stmt->bind_param("sssi", $kode, $nama, $deskripsi, $id);

    if ($stmt->execute()) {
        header('Location: kelola_praktikum.php?status=sukses_edit');
    } else {
        header('Location: edit_praktikum.php?id=' . $id . '&status=gagal');
    }
    $stmt->close();
}
// ===================================================================
// BLOK UNTUK AKSI HAPUS PRAKTIKUM
// ===================================================================
elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Karena kita menggunakan ON DELETE CASCADE di database,
        // semua modul dan pendaftaran terkait akan otomatis terhapus.
        header('Location: kelola_praktikum.php?status=sukses_hapus');
    } else {
        header('Location: kelola_praktikum.php?status=gagal_hapus');
    }
    $stmt->close();
}
// ===================================================================
else {
    // Jika tidak ada aksi yang cocok, kembalikan ke halaman utama
    header('Location: kelola_praktikum.php');
}

$conn->close();
?>