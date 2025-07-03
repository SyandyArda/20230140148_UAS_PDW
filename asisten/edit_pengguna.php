<?php
require_once '../config.php';

$pageTitle = 'Edit Pengguna';
$activePage = 'kelola-pengguna';
require_once 'templates/header.php';

// Pastikan ID pengguna ada di URL
if (!isset($_GET['id'])) {
    echo "ID Pengguna tidak ditemukan.";
    require_once 'templates/footer.php';
    exit();
}
$id_pengguna = $_GET['id'];

// Ambil data pengguna yang akan diedit
$stmt = $conn->prepare("SELECT nama, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Pengguna tidak ditemukan.";
    require_once 'templates/footer.php';
    exit();
}
?>

<div class="bg-white p-8 rounded-xl shadow-md max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Akun Pengguna</h1>

    <form action="proses_pengguna.php" method="POST">
        <input type="hidden" name="aksi" value="edit">
        <input type="hidden" name="id" value="<?php echo $id_pengguna; ?>">

        <div class="mb-4">
            <label for="nama" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-medium mb-2">Password Baru</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Kosongkan jika tidak diubah">
        </div>

        <div class="mb-6">
            <label for="role" class="block text-gray-700 font-medium mb-2">Role</label>
            <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="mahasiswa" <?php echo ($user['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                <option value="asisten" <?php echo ($user['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
            </select>
        </div>

        <div class="flex items-center space-x-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Simpan Perubahan</button>
            <a href="kelola_pengguna.php" class="text-gray-600 hover:text-gray-900">Batal</a>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>