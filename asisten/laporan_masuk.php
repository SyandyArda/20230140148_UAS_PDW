<?php
// 1. Definisi Variabel & Logika
require_once '../config.php';

$pageTitle = 'Laporan Masuk';
$activePage = 'laporan-masuk'; // Untuk active state di sidebar

// 2. Panggil Header
require_once 'templates/header.php';

// --- LOGIKA FILTER ---
// Ambil nilai filter dari URL (jika ada)
$filter_status = $_GET['status'] ?? '';
$filter_mahasiswa = $_GET['mahasiswa'] ?? '';
$filter_modul = $_GET['modul'] ?? '';

// Bangun query dasar
$query = "
    SELECT 
        pl.id as id_laporan,
        u.nama as nama_mahasiswa,
        mp.nama_praktikum,
        m.nama_modul,
        pl.file_laporan,
        pl.tanggal_kumpul,
        pl.nilai
    FROM pengumpulan_laporan pl
    JOIN users u ON pl.id_mahasiswa = u.id
    JOIN modul m ON pl.id_modul = m.id
    JOIN mata_praktikum mp ON m.id_praktikum = mp.id
";

// Tambahkan kondisi WHERE secara dinamis berdasarkan filter
$where_clauses = [];
$bind_params = [];
$bind_types = '';

if ($filter_status === 'belum_dinilai') {
    $where_clauses[] = "pl.nilai IS NULL";
} elseif ($filter_status === 'sudah_dinilai') {
    $where_clauses[] = "pl.nilai IS NOT NULL";
}

if (!empty($filter_mahasiswa)) {
    $where_clauses[] = "pl.id_mahasiswa = ?";
    $bind_params[] = $filter_mahasiswa;
    $bind_types .= 'i';
}

if (!empty($filter_modul)) {
    $where_clauses[] = "pl.id_modul = ?";
    $bind_params[] = $filter_modul;
    $bind_types .= 'i';
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY pl.tanggal_kumpul DESC";

// Eksekusi query dengan prepared statement
$stmt = $conn->prepare($query);
if (!empty($bind_params)) {
    $stmt->bind_param($bind_types, ...$bind_params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Laporan Masuk</h1>
</div>

<div class="bg-white p-4 rounded-lg shadow-md mb-6">
    <form action="laporan_masuk.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua</option>
                <option value="belum_dinilai" <?php echo ($filter_status == 'belum_dinilai') ? 'selected' : ''; ?>>Belum Dinilai</option>
                <option value="sudah_dinilai" <?php echo ($filter_status == 'sudah_dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
            </select>
        </div>
        <div>
            <label for="mahasiswa" class="block text-sm font-medium text-gray-700">Mahasiswa</label>
            <select name="mahasiswa" id="mahasiswa" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua</option>
                <?php
                    $mahasiswa_result = $conn->query("SELECT DISTINCT u.id, u.nama FROM users u JOIN pengumpulan_laporan pl ON u.id = pl.id_mahasiswa ORDER BY u.nama");
                    while($mhs = $mahasiswa_result->fetch_assoc()) {
                        $selected = ($filter_mahasiswa == $mhs['id']) ? 'selected' : '';
                        echo "<option value='{$mhs['id']}' {$selected}>" . htmlspecialchars($mhs['nama']) . "</option>";
                    }
                ?>
            </select>
        </div>
        <div>
            <label for="modul" class="block text-sm font-medium text-gray-700">Modul</label>
            <select name="modul" id="modul" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Semua</option>
                 <?php
                    $modul_result = $conn->query("SELECT id, nama_modul FROM modul ORDER BY nama_modul");
                    while($mod = $modul_result->fetch_assoc()) {
                        $selected = ($filter_modul == $mod['id']) ? 'selected' : '';
                        echo "<option value='{$mod['id']}' {$selected}>" . htmlspecialchars($mod['nama_modul']) . "</option>";
                    }
                ?>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Filter</button>
            <a href="laporan_masuk.php" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Mahasiswa</th>
                    <th class="px-4 py-2 text-left">Praktikum</th>
                    <th class="px-4 py-2 text-left">Modul</th>
                    <th class="px-4 py-2 text-left">Waktu Kumpul</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['nama_modul']); ?></td>
                        <td class="px-4 py-2"><?php echo date('d M Y, H:i', strtotime($row['tanggal_kumpul'])); ?></td>
                        <td class="px-4 py-2">
                            <?php if (is_null($row['nilai'])): ?>
                                <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Belum Dinilai</span>
                            <?php else: ?>
                                <span class="bg-green-200 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Sudah Dinilai (<?php echo $row['nilai']; ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2">
                            <a href="../uploads/laporan/<?php echo htmlspecialchars($row['file_laporan']); ?>" target="_blank" class="text-blue-500 hover:underline">Unduh</a>
                            <?php if (is_null($row['nilai'])): ?>
                                | <a href="beri_nilai.php?id_laporan=<?php echo $row['id_laporan']; ?>" class="text-green-500 hover:underline">Beri Nilai</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center px-4 py-2">Tidak ada laporan yang cocok dengan filter.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// 3. Panggil Footer
require_once 'templates/footer.php';
?>