<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id_praktikum'])) {
    header('Location: kelola_praktikum.php');
    exit();
}
$id_praktikum = $_GET['id_praktikum'];

$stmt_praktikum = $conn->prepare("SELECT nama_praktikum FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$result_praktikum = $stmt_praktikum->get_result();
$praktikum = $result_praktikum->fetch_assoc();
$nama_praktikum = $praktikum['nama_praktikum'] ?? 'Tidak Ditemukan';

$pageTitle = 'Kelola Modul';
$activePage = 'manajemen-praktikum';
require_once 'templates/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-2">Mengelola Modul untuk: <?php echo htmlspecialchars($nama_praktikum); ?></h1>
<a href="kelola_praktikum.php" class="text-blue-500 hover:underline mb-6 inline-block">&larr; Kembali ke Daftar Praktikum</a>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tambah Modul Baru</h2>
    
    <form action="proses_modul.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="aksi" value="tambah">
        <input type="hidden" name="id_praktikum" value="<?php echo $id_praktikum; ?>">
        
        <div class="mb-4">
            <label for="nama_modul" class="block text-gray-700 font-medium mb-2">Nama Modul</label>
            <input type="text" id="nama_modul" name="nama_modul" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="deskripsi_modul" class="block text-gray-700 font-medium mb-2">Deskripsi (Opsional)</label>
            <textarea id="deskripsi_modul" name="deskripsi_modul" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
        </div>

        <div class="mb-4">
            <label for="file_materi" class="block text-gray-700 font-medium mb-2">File Materi (Opsional)</label>
            <input type="file" id="file_materi" name="file_materi" accept=".pdf,.docx,.zip" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer">
        </div>
        
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Tambah Modul</button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Modul</h2>
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nama Modul</th>
                    <th class="px-4 py-2 text-left">Deskripsi</th> <th class="px-4 py-2 text-left">File Materi</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // DIUBAH: Query mengambil kolom deskripsi_modul
                $stmt_modul = $conn->prepare("SELECT id, nama_modul, deskripsi_modul, file_materi FROM modul WHERE id_praktikum = ? ORDER BY id ASC");
                $stmt_modul->bind_param("i", $id_praktikum);
                $stmt_modul->execute();
                $result_modul = $stmt_modul->get_result();
                if ($result_modul->num_rows > 0):
                    while ($modul = $result_modul->fetch_assoc()):
                ?>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium"><?php echo htmlspecialchars($modul['nama_modul']); ?></td>
                    <td class="px-4 py-2 text-gray-600"><?php echo htmlspecialchars($modul['deskripsi_modul']); ?></td> <td class="px-4 py-2">
                        <?php if (!empty($modul['file_materi'])): ?>
                            <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank" class="text-blue-500 hover:underline">Unduh</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2">
                        <a href="edit_modul.php?id_modul=<?php echo $modul['id']; ?>" class="text-green-500 hover:underline">Edit</a>
                        <a href="proses_modul.php?aksi=hapus&id_modul=<?php echo $modul['id']; ?>&id_praktikum=<?php echo $id_praktikum; ?>" class="text-red-500 hover:underline ml-2" onclick="return confirm('Anda yakin ingin menghapus modul ini?')">Hapus</a>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Belum ada modul yang ditambahkan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>