<?php
// 1. Definisi Variabel, Koneksi, dan Logika PHP
require_once '../config.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// --- Query untuk Statistik ---

// Menghitung total mata praktikum
$total_praktikum_result = $conn->query("SELECT COUNT(*) as total FROM mata_praktikum");
$total_praktikum = $total_praktikum_result->fetch_assoc()['total'];

// Menghitung total laporan yang masuk
$total_laporan_result = $conn->query("SELECT COUNT(*) as total FROM pengumpulan_laporan");
$total_laporan = $total_laporan_result->fetch_assoc()['total'];

// Menghitung laporan yang belum dinilai (kolom 'nilai' masih NULL)
$laporan_belum_dinilai_result = $conn->query("SELECT COUNT(*) as total FROM pengumpulan_laporan WHERE nilai IS NULL");
$laporan_belum_dinilai = $laporan_belum_dinilai_result->fetch_assoc()['total'];

// Mengambil 5 aktivitas laporan terbaru
$aktivitas_terbaru_query = "
    SELECT u.nama, m.nama_modul, pl.tanggal_kumpul 
    FROM pengumpulan_laporan pl
    JOIN users u ON pl.id_mahasiswa = u.id
    JOIN modul m ON pl.id_modul = m.id
    ORDER BY pl.tanggal_kumpul DESC
    LIMIT 5
";
$aktivitas_terbaru_result = $conn->query($aktivitas_terbaru_query);


// 2. Panggil Header
require_once 'templates/header.php'; 
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <a href="kelola_praktikum.php" class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4 hover:bg-gray-50 transition-colors">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Mata Praktikum</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total_praktikum; ?></p>
        </div>
    </a>

    <a href="laporan_masuk.php" class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4 hover:bg-gray-50 transition-colors">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total_laporan; ?></p>
        </div>
    </a>

    <a href="laporan_masuk.php?filter=belum_dinilai" class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4 hover:bg-gray-50 transition-colors">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $laporan_belum_dinilai; ?></p>
        </div>
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php if ($aktivitas_terbaru_result->num_rows > 0): ?>
            <?php while($aktivitas = $aktivitas_terbaru_result->fetch_assoc()): ?>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                        <?php
                            $nama_parts = explode(' ', $aktivitas['nama'], 2);
                            $inisial = '';
                            foreach ($nama_parts as $part) {
                                $inisial .= strtoupper(substr($part, 0, 1));
                            }
                        ?>
                        <span class="font-bold text-gray-500"><?php echo $inisial; ?></span>
                    </div>
                    <div>
                        <p class="text-gray-800"><strong><?php echo htmlspecialchars($aktivitas['nama']); ?></strong> mengumpulkan laporan untuk <strong><?php echo htmlspecialchars($aktivitas['nama_modul']); ?></strong></p>
                        <p class="text-sm text-gray-500"><?php echo date('d M Y, H:i', strtotime($aktivitas['tanggal_kumpul'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada aktivitas laporan.</p>
        <?php endif; ?>
    </div>
</div>


<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>