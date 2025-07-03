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

// 3. Ambil ID Praktikum dari URL dan validasi
if (!isset($_GET['id_praktikum'])) {
    header("Location: dashboard.php");
    exit();
}
$id_praktikum = $_GET['id_praktikum'];

// Ambil info mata praktikum untuk judul
$stmt_praktikum = $conn->prepare("SELECT nama_praktikum FROM mata_praktikum WHERE id = ?");
$stmt_praktikum->bind_param("i", $id_praktikum);
$stmt_praktikum->execute();
$praktikum_info = $stmt_praktikum->get_result()->fetch_assoc();
$nama_praktikum = $praktikum_info['nama_praktikum'];


// 4. Definisi Variabel untuk Template
$pageTitle = $nama_praktikum;
$activePage = 'praktikum-saya'; // Bisa dikosongkan jika tidak ada menu khusus

// 5. Panggil Header Mahasiswa
require_once 'templates/header_mahasiswa.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($nama_praktikum); ?></h1>
    <a href="dashboard.php" class="text-blue-500 hover:underline">&larr; Kembali ke Dashboard</a>
</div>


<div class="space-y-6">
    <?php
    // Query untuk mengambil semua modul dari praktikum ini
    $stmt_modul = $conn->prepare("SELECT * FROM modul WHERE id_praktikum = ? ORDER BY id ASC");
    $stmt_modul->bind_param("i", $id_praktikum);
    $stmt_modul->execute();
    $result_modul = $stmt_modul->get_result();

    if ($result_modul && $result_modul->num_rows > 0):
        while ($modul = $result_modul->fetch_assoc()):
            $id_modul = $modul['id'];

            // Untuk setiap modul, cek apakah mahasiswa sudah mengumpulkan laporan
            $stmt_laporan = $conn->prepare("SELECT * FROM pengumpulan_laporan WHERE id_modul = ? AND id_mahasiswa = ?");
            $stmt_laporan->bind_param("ii", $id_modul, $id_mahasiswa);
            $stmt_laporan->execute();
            $laporan = $stmt_laporan->get_result()->fetch_assoc();
    ?>
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($modul['nama_modul']); ?></h3>
            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($modul['deskripsi_modul']); ?></p>

            <?php if (!empty($modul['file_materi'])): ?>
                <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" target="_blank" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md transition-colors">
                    Unduh Materi
                </a>
            <?php endif; ?>

            <hr class="my-6">

            <div>
                <h4 class="text-lg font-semibold text-gray-700 mb-3">Pengumpulan Laporan</h4>
                <?php if ($laporan): // Jika sudah ada laporan yang dikumpulkan ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                        <p class="font-bold">Sudah Mengumpulkan</p>
                        <p>Anda mengumpulkan pada: <?php echo date('d M Y, H:i', strtotime($laporan['tanggal_kumpul'])); ?></p>
                        <p class="mt-2">
                            <strong>Status Penilaian:</strong> 
                            <?php if (is_null($laporan['nilai'])): ?>
                                <span class="font-normal">Menunggu Penilaian</span>
                            <?php else: ?>
                                <span class="font-bold text-2xl"><?php echo $laporan['nilai']; ?>/100</span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($laporan['feedback'])): ?>
                             <div class="mt-2 bg-green-50 p-3 rounded">
                                <p class="font-semibold">Feedback dari Asisten:</p>
                                <p class="italic">"<?php echo htmlspecialchars($laporan['feedback']); ?>"</p>
                             </div>
                        <?php endif; ?>
                    </div>
                <?php else: // Jika belum mengumpulkan, tampilkan form upload ?>
                    <form action="proses_upload.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_modul" value="<?php echo $id_modul; ?>">
                        <input type="hidden" name="id_praktikum" value="<?php echo $id_praktikum; ?>">
                        <div>
                            <label for="file_laporan_<?php echo $id_modul; ?>" class="block mb-2 text-sm font-medium text-gray-900">Unggah file Anda (PDF/DOCX):</label>
                            <input type="file" name="file_laporan" id="file_laporan_<?php echo $id_modul; ?>" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none" required>
                        </div>
                        <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition-colors">
                            Kumpulkan Laporan
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php 
        endwhile;
    else: 
    ?>
        <p class="text-gray-500 text-center">Belum ada modul yang ditambahkan untuk praktikum ini.</p>
    <?php endif; ?>
</div>

<?php
// 6. Panggil Footer Mahasiswa
require_once 'templates/footer_mahasiswa.php';
?>