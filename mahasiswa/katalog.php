<?php
// 1. Set variabel untuk template dan panggil session/config
session_start();
require_once '../config.php';

$pageTitle = 'Katalog Praktikum';
$activePage = 'katalog'; // 'katalog' akan menandai link "Cari Praktikum" sebagai aktif

// 2. Panggil header yang konsisten dengan halaman mahasiswa lainnya
require_once 'templates/header_mahasiswa.php';
?>

<h2 class="text-3xl font-bold text-center text-gray-800 mb-4">Katalog Mata Praktikum</h2>
<p class="text-center text-gray-600 mb-8">Temukan dan daftarkan diri Anda pada praktikum yang tersedia.</p>

<?php if(isset($_GET['status'])): ?>
    <div class="max-w-3xl mx-auto mb-6">
        <?php if($_GET['status'] == 'sukses'): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p class="font-bold">Pendaftaran Berhasil!</p>
                <p>Anda sekarang terdaftar di praktikum. Silakan cek di halaman 'Praktikum Saya'.</p>
            </div>
        <?php elseif($_GET['status'] == 'terdaftar'): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">Informasi</p>
                <p>Anda sudah terdaftar di praktikum tersebut.</p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php
    $result = $conn->query("SELECT * FROM mata_praktikum ORDER BY nama_praktikum ASC");
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
            <div class="flex-grow">
                <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
            </div>
            
            <a href="proses_pendaftaran.php?id_praktikum=<?php echo $row['id']; ?>" class="mt-4 bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors w-full text-center inline-block">
                Daftar ke Praktikum Ini
            </a>
        </div>
    <?php 
        endwhile;
    else:
    ?>
        <div class="col-span-full bg-white text-center p-12 rounded-xl shadow-md">
            <p class="text-gray-500 text-lg">Saat ini belum ada mata praktikum yang dibuka.</p>
            <p class="text-gray-400 mt-2">Silakan cek kembali nanti.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// 3. Panggil footer yang konsisten
require_once 'templates/footer_mahasiswa.php';
?>