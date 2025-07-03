<?php
$pageTitle = 'Tambah Praktikum';
$activePage = 'manajemen-praktikum';
require_once 'templates/header.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Tambah Mata Praktikum Baru</h1>

<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <form action="proses_praktikum.php" method="POST">
        <input type="hidden" name="aksi" value="tambah">
        <div class="mb-4">
            <label for="kode_praktikum" class="block text-gray-700 font-medium mb-2">Kode Praktikum</label>
            <input type="text" id="kode_praktikum" name="kode_praktikum" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>
        <div class="mb-4">
            <label for="nama_praktikum" class="block text-gray-700 font-medium mb-2">Nama Mata Praktikum</label>
            <input type="text" id="nama_praktikum" name="nama_praktikum" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
        </div>
        <div class="mb-6">
            <label for="deskripsi" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
        </div>
        <div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Simpan</button>
        </div>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>