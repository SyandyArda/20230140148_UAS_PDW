<?php
session_start();
require_once '../config.php';

// Pastikan hanya asisten yang bisa melakukan aksi ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    die("Akses ditolak.");
}

// ===================================================================
// BLOK UNTUK AKSI TAMBAH PENGGUNA (BARU)
// ===================================================================
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Keamanan: Hash password sebelum disimpan ke database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
    
    if ($stmt->execute()) {
        header("Location: kelola_pengguna.php?status=sukses_tambah");
    } else {
        echo "Error saat menambah pengguna: " . $stmt->error;
    }
    $stmt->close();
}
// ===================================================================
// BLOK UNTUK AKSI EDIT PENGGUNA
// ===================================================================
elseif (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nama, $email, $hashed_password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $email, $role, $id);
    }

    if ($stmt->execute()) {
        header("Location: kelola_pengguna.php?status=sukses_edit");
    } else {
        echo "Error saat update: " . $stmt->error;
    }
    $stmt->close();
}
// ===================================================================
// BLOK UNTUK AKSI HAPUS PENGGUNA
// ===================================================================
elseif (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_to_delete = $_GET['id'];

    if ($id_to_delete == $_SESSION['user_id']) {
        die("Anda tidak bisa menghapus akun Anda sendiri.");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_to_delete);

    if ($stmt->execute()) {
        header("Location: kelola_pengguna.php?status=sukses_hapus");
    } else {
        echo "Error saat menghapus: " . $stmt->error;
    }
    $stmt->close();
}
// ===================================================================
else {
    header("Location: kelola_pengguna.php");
}

$conn->close();
?>