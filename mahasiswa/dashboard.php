<?php
// 1. Panggil file konfigurasi dan mulai session
session_start();
require_once '../config.php';

// 2. Cek Keamanan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}
$id_mahasiswa = $_SESSION['user_id'];

// 3. Definisi Variabel untuk Template
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// 4. Logika untuk mengambil data statistik
// Menghitung jumlah praktikum yang diikuti
$stmt_praktikum = $conn->prepare("SELECT COUNT(*) as total FROM pendaftaran_praktikum WHERE id_mahasiswa = ?");
$stmt_praktikum->bind_param("i", $id_mahasiswa);
$stmt_praktikum->execute();
$jumlah_praktikum = $stmt_praktikum->get_result()->fetch_assoc()['total'];

// Menghitung tugas yang sudah dinilai
$stmt_selesai = $conn->prepare("SELECT COUNT(*) as total FROM pengumpulan_laporan WHERE id_mahasiswa = ? AND nilai IS NOT NULL");
$stmt_selesai->bind_param("i", $id_mahasiswa);
$stmt_selesai->execute();
$tugas_selesai = $stmt_selesai->get_result()->fetch_assoc()['total'];

// Menghitung tugas yang sudah dikumpul tapi belum dinilai
$stmt_menunggu = $conn->prepare("SELECT COUNT(*) as total FROM pengumpulan_laporan WHERE id_mahasiswa = ? AND nilai IS NULL");
$stmt_menunggu->bind_param("i", $id_mahasiswa);
$stmt_menunggu->execute();
$tugas_menunggu = $stmt_menunggu->get_result()->fetch_assoc()['total'];


// 5. Panggil Header Mahasiswa
require_once 'templates/header_mahasiswa.php'; 
?>


<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?php echo $jumlah_praktikum; ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?php echo $tugas_selesai; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai Dinilai</div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?php echo $tugas_menunggu; ?></div>
        <div class="mt-2 text-lg text-gray-600">Menunggu Penilaian</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Praktikum Saya</h3>
    <div class="space-y-4">
        <?php
        // Query untuk mengambil daftar praktikum yang diikuti
        $query_list = "
            SELECT mp.id, mp.nama_praktikum, mp.deskripsi
            FROM pendaftaran_praktikum pp
            JOIN mata_praktikum mp ON pp.id_praktikum = mp.id
            WHERE pp.id_mahasiswa = ?
            ORDER BY mp.nama_praktikum ASC
        ";
        $stmt_list = $conn->prepare($query_list);
        $stmt_list->bind_param("i", $id_mahasiswa);
        $stmt_list->execute();
        $result_list = $stmt_list->get_result();
        
        if ($result_list && $result_list->num_rows > 0):
            while ($row = $result_list->fetch_assoc()):
        ?>
            <div class="border rounded-lg p-4 flex items-center justify-between hover:bg-gray-50">
                <div>
                    <h4 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h4>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
                <a href="detail_praktikum.php?id_praktikum=<?php echo $row['id']; ?>" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-colors whitespace-nowrap">
                    Lihat Detail
                </a>
            </div>
        <?php 
            endwhile;
        else: 
        ?>
            <div class="text-center py-4">
                <p class="text-gray-500">Anda belum terdaftar di praktikum manapun.</p>
                <a href="katalog.php" class="mt-2 inline-block text-blue-500 hover:underline">Lihat Katalog Praktikum</a>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>