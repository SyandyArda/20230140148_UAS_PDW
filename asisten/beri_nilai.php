<?php
// 1. Definisi Variabel & Logika
require_once '../config.php';

$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan-masuk'; // Tetap aktif di menu Laporan Masuk

// Ambil id_laporan dari URL
if (!isset($_GET['id_laporan'])) {
    header('Location: laporan_masuk.php');
    exit();
}
$id_laporan = $_GET['id_laporan'];

// Query untuk mengambil detail laporan spesifik
$query = "
    SELECT 
        u.nama as nama_mahasiswa,
        mp.nama_praktikum,
        m.nama_modul,
        pl.file_laporan
    FROM pengumpulan_laporan pl
    JOIN users u ON pl.id_mahasiswa = u.id
    JOIN modul m ON pl.id_modul = m.id
    JOIN mata_praktikum mp ON m.id_praktikum = mp.id
    WHERE pl.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_laporan);
$stmt->execute();
$result = $stmt->get_result();
$laporan = $result->fetch_assoc();

if (!$laporan) {
    echo "Laporan tidak ditemukan.";
    exit();
}

// 2. Panggil Header
require_once 'templates/header.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-semibold">Detail Laporan</h2>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Mahasiswa</p>
                <p class="font-medium"><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Mata Praktikum</p>
                <p class="font-medium"><?php echo htmlspecialchars($laporan['nama_praktikum']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Modul</p>
                <p class="font-medium"><?php echo htmlspecialchars($laporan['nama_modul']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">File Laporan</p>
                <a href="../uploads/laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" target="_blank" class="text-blue-500 hover:underline">Unduh Laporan</a>
            </div>
        </div>
    </div>
    
    <h2 class="text-xl font-semibold mb-4">Form Penilaian</h2>
    <form action="proses_penilaian.php" method="POST">
        <input type="hidden" name="id_laporan" value="<?php echo $id_laporan; ?>">
        
        <div class="mb-4">
            <label for="nilai" class="block text-gray-700 font-medium mb-2">Nilai (0-100)</label>
            <input type="number" id="nilai" name="nilai" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        
        <div class="mb-6">
            <label for="feedback" class="block text-gray-700 font-medium mb-2">Feedback (Opsional)</label>
            <textarea id="feedback" name="feedback" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>
        
        <div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">Simpan Penilaian</button>
        </div>
    </form>
</div>

<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>