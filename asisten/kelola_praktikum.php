<?php
// 1. Definisi Variabel & Logika
session_start();
require_once '../config.php';

// Pastikan hanya asisten yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Manajemen Praktikum';
$activePage = 'manajemen-praktikum';

// 2. Panggil Header
require_once 'templates/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Manajemen Mata Praktikum</h1>
    <a href="tambah_praktikum.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">
        + Tambah Praktikum Baru
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Kode</th>
                    <th class="px-4 py-2 text-left">Nama Mata Praktikum</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM mata_praktikum ORDER BY kode_praktikum ASC");
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr class="border-b">
                    <td class="px-4 py-2"><?php echo htmlspecialchars($row['kode_praktikum']); ?></td>
                    <td class="px-4 py-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                    <td class="px-4 py-2">
                        <a href="kelola_modul.php?id_praktikum=<?php echo $row['id']; ?>" class="text-blue-500 hover:underline font-semibold">Kelola Modul</a>
                        <span class="text-gray-300 mx-1">|</span>
                        <a href="edit_praktikum.php?id=<?php echo $row['id']; ?>" class="text-green-500 hover:underline">Edit</a>
                        <span class="text-gray-300 mx-1">|</span>
                        <a href="proses_praktikum.php?aksi=hapus&id=<?php echo $row['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Anda yakin ingin menghapus praktikum ini? Semua modul terkait juga akan terhapus.')">Hapus</a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else: 
                ?>
                <tr>
                    <td colspan="3" class="text-center px-4 py-2">Belum ada data mata praktikum.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>