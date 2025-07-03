<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login atau bukan mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Mahasiswa - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-white text-2xl font-bold">SIMPRAK</span>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <?php 
                                $activeClass = 'bg-blue-700 text-white';
                                $inactiveClass = 'text-gray-200 hover:bg-blue-700 hover:text-white';
                                $currentPage = $activePage ?? '';
                            ?>
                            <a href="dashboard.php" class="<?php echo ($currentPage == 'dashboard') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Praktikum Saya</a>
                            
                            <a href="katalog.php" class="<?php echo ($currentPage == 'katalog') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Cari Praktikum</a>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <span class="text-gray-200 mr-4">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition-colors duration-300">
                            Logout
                        </a>
                    </div>
                </div>

                </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-Sekarang, tautan "Cari Praktikum" akan berfungsi dengan benar sesuai dengan struktur file Anda.