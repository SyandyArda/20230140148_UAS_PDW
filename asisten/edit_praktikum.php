<?php
require_once '../config.php';

$pageTitle = 'Edit Praktikum';
$activePage = 'manajemen-praktikum';
require_once 'templates/header.php';

// Pastikan ID praktikum ada di URL
if (!isset($_GET['id'])) {
    header('Location: kelola_praktikum.php');
    exit();
}
$id_praktikum = $_GET['id'];

// Ambil data praktikum yang akan diedit
$stmt = $conn->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt->bind_param("i", $id_praktikum);
$stmt->execute();
$result = $stmt->get_result();
$praktikum = $result->fetch_assoc();

if (!$praktikum) {
    echo "Praktikum tidak ditemukan.";
    require_once 'templates/footer.php';
    exit();
}
?>

<div class="bg-white p-8 rounded-xl shadow-md max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Mata Praktikum</h1>

    <form action="proses_praktikum.php" method="POST">
        <input type="hidden" name="aksi" value="edit">
        <input type="hidden" name="id" value="<?php echo $id_praktikum; ?>">

        <div class="mb-4">
            <label for="kode_praktikum" class="block text-gray-700 font-medium mb-2">Kode Praktikum</label>
            <input type="text" id="kode_praktikum" name="kode_praktikum" value="<?php echo htmlspecialchars($praktikum['kode_praktikum']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700 font-medium mb-2">Nama Mata Praktikum</label>
            <input type="text" id="nama_praktikum" name="nama_praktikum" value="<?php echo htmlspecialchars($praktikum['nama_praktikum']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-6">
            <label for="deskripsi" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($praktikum['deskripsi']); ?></textarea>
        </div>

        <div class="flex items-center space-x-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Simpan Perubahan</button>
            <a href="kelola_praktikum.php" class="text-gray-600 hover:text-gray-900">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>