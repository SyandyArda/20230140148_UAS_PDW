<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header('Location: ../login.php');
    exit();
}
require_once '../config.php';

// Pastikan ID modul ada di URL
if (!isset($_GET['id_modul'])) {
    echo "ID Modul tidak ditemukan.";
    exit();
}
$id_modul = $_GET['id_modul'];

// Ambil data modul yang akan diedit
$stmt = $conn->prepare("SELECT * FROM modul WHERE id = ?");
$stmt->bind_param("i", $id_modul);
$stmt->execute();
$result = $stmt->get_result();
$modul = $result->fetch_assoc();

if (!$modul) {
    echo "Modul tidak ditemukan.";
    exit();
}

// Menyiapkan variabel untuk template
$pageTitle = 'Edit Modul';
$activePage = 'manajemen-praktikum'; // Menandakan menu sidebar yang aktif

// Memanggil template header
require_once 'templates/header.php';
?>

<div class="bg-white p-8 rounded-xl shadow-md max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Modul</h1>
    <p class="text-gray-600 mb-6">Anda sedang mengubah modul: <strong><?php echo htmlspecialchars($modul['nama_modul']); ?></strong></p>

    <form action="proses_modul.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="aksi" value="edit">
        <input type="hidden" name="id_modul" value="<?php echo $modul['id']; ?>">
        <input type="hidden" name="id_praktikum" value="<?php echo $modul['id_praktikum']; ?>">
        <input type="hidden" name="file_lama" value="<?php echo htmlspecialchars($modul['file_materi']); ?>">

        <div class="mb-4">
            <label for="nama_modul" class="block text-gray-700 font-medium mb-2">Nama Modul</label>
            <input type="text" id="nama_modul" name="nama_modul" value="<?php echo htmlspecialchars($modul['nama_modul']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="mb-4">
            <label for="deskripsi_modul" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
            <textarea id="deskripsi_modul" name="deskripsi_modul" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($modul['deskripsi_modul']); ?></textarea>
        </div>

        <div class="mb-6">
            <label for="file_materi" class="block text-gray-700 font-medium mb-2">File Materi</label>
            <div class="bg-gray-50 p-3 rounded-md border">
                <p class="text-sm text-gray-500">File saat ini: 
                    <strong class="text-gray-700">
                    <?php 
                        if(!empty($modul['file_materi'])) {
                            echo htmlspecialchars($modul['file_materi']);
                        } else {
                            echo "Tidak ada file.";
                        }
                    ?>
                    </strong>
                </p>
            </div>
            <small class="text-gray-500 mt-1 block">Kosongkan input di bawah jika tidak ingin mengubah file materi.</small>
            <input type="file" id="file_materi" name="file_materi" accept=".pdf,.docx,.zip" class="mt-2 block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none">
        </div>
        
        <div class="flex items-center space-x-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">Update Modul</button>
            <a href="kelola_modul.php?id_praktikum=<?php echo $modul['id_praktikum']; ?>" class="text-gray-600 hover:text-gray-900">Batal</a>
        </div>
    </form>
</div>

<?php
// Memanggil template footer
require_once 'templates/footer.php';
?>