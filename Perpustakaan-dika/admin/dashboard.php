<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config.php/config.php';

function safe_count_query($koneksi, $query) {
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return isset($row['total']) ? intval($row['total']) : 0;
    }
    return 0;
}

$total_buku = safe_count_query($koneksi, "SELECT COUNT(*) AS total FROM buku");
$total_anggota = safe_count_query($koneksi, "SELECT COUNT(*) AS total FROM anggota");
$total_pinjam = safe_count_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE status = 'dipinjam'");
$total_kembali_hari_ini = safe_count_query($koneksi, "SELECT COUNT(*) AS total FROM peminjaman WHERE tanggal_kembali = CURDATE()");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen bg-white">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-indigo-900 via-indigo-800 to-indigo-900 text-white shadow-2xl flex flex-col">
            <div class="p-6 flex items-center gap-3 border-b border-indigo-700">
                <img src="../img/logosipus.png" alt="SIPUS Logo" class="h-14 w-14 rounded-lg bg-white p-1" />
                <div>
                    <p class="font-bold text-lg">SIPUS</p>
                    <p class="text-xs text-indigo-200">Perpustakaan Digital</p>
                </div>
            </div>
            <nav class="flex-1 flex flex-col gap-1 p-4">
                <a href="dashboard.php" class="px-4 py-3 rounded-lg bg-indigo-700 hover:bg-indigo-600 text-white font-medium transition duration-200 flex items-center gap-2">
                    Dashboard
                </a>
                <a href="kelola_buku.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                    Kelola Buku
                </a>
                <a href="kelola_anggota.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                    Kelola Anggota
                </a>
                <a href="transaksi.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                    Transaksi
                </a>
            </nav>
            <div class="border-t border-indigo-700 p-4">
                <a href="../auth/logout.php" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center gap-2">
                     Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="p-8">
                <div class="max-w-6xl mx-auto">
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-4xl font-bold text-slate-900">Dashboard Admin</h1>
                        <p class="text-slate-600 mt-2">Selamat datang di halaman administrasi sistem perpustakaan digital</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition border-l-4 border-blue-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium">Total Buku</p>
                                    <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo $total_buku; ?></p>
                                </div>
                                <span class="text-4xl"></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition border-l-4 border-green-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium">Anggota Aktif</p>
                                    <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo $total_anggota; ?></p>
                                </div>
                                <span class="text-4xl"></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition border-l-4 border-purple-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium">Peminjaman Aktif</p>
                                    <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo $total_pinjam; ?></p>
                                </div>
                                <span class="text-4xl"></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition border-l-4 border-amber-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 text-sm font-medium">Pengembalian Hari Ini</p>
                                    <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo $total_kembali_hari_ini; ?></p>
                                </div>
                                <span class="text-4xl"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Aksi Cepat</h2>
                        <div class="grid md:grid-cols-2 gap-4">
                            <a href="kelola_buku.php" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                ➕ Tambah Buku Baru
                            </a>
                            <a href="kelola_anggota.php" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                ➕ Daftarkan Anggota Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>