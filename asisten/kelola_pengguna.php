<?php
// 1. Definisi Variabel & Logika
require_once '../config.php';

$pageTitle = 'Kelola Pengguna';
$activePage = 'kelola-pengguna';

// 2. Panggil Header
require_once 'templates/header.php';

// DIUBAH: Mengganti 'username' menjadi 'email' sesuai struktur database Anda
$query = "SELECT id, nama, email, role FROM users ORDER BY role, nama";
$result = $conn->query($query);
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Kelola Akun Pengguna</h1>
    <a href="tambah_pengguna.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">
        + Tambah Pengguna
    </a>
</div>


<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nama Lengkap</th>
                    <th class="px-4 py-2 text-left">Email</th> <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['email']); ?></td> <td class="px-4 py-2">
                            <?php if ($row['role'] == 'asisten'): ?>
                                <span class="bg-blue-200 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Asisten</span>
                            <?php else: ?>
                                <span class="bg-green-200 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Mahasiswa</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2">
                            <a href="edit_pengguna.php?id=<?php echo $row['id']; ?>" class="text-green-500 hover:underline">Edit</a>
                            
                            <?php if ($_SESSION['user_id'] != $row['id']): ?>
                                | <a href="proses_pengguna.php?aksi=hapus&id=<?php echo $row['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center px-4 py-2">Belum ada pengguna yang terdaftar.</td>
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