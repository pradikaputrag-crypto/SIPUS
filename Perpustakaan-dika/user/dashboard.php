<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config.php/config.php';

$activeLoans = 0;
$dueSoon = 0;
$availableBooks = 0;
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0);

$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM transaksi WHERE id_anggota = ? AND status = 'dipinjam'");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $activeLoans);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM transaksi WHERE id_anggota = ? AND status = 'dipinjam' AND tanggal_kembali BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $dueSoon);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

$result = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM buku WHERE stok > 0");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $availableBooks = intval($row['total']);
    mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Perpustakaan Digital</title>
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
                <a href="pinjam_buku.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                     Pinjam Buku
                </a>
                <a href="riwayat_peminjaman.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                     Riwayat Peminjaman
                </a>
                <a href="pengingat.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                     Pengingat Pengembalian
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
            <div class="p-8 h-full flex flex-col">
                <div class="max-w-6xl w-full mx-auto flex-1 flex flex-col">
                    <!-- Header -->
                    <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
                        <h2 class="text-3xl font-bold text-slate-900">Dashboard User</h2>
                        <p class="text-slate-600 mt-2">Kelola peminjaman buku Anda dengan mudah</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-md border-l-4 border-blue-500 p-6 hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 font-medium mb-1">Buku Dipinjam</p>
                                    <p class="text-3xl font-bold text-blue-600"><?php echo intval($activeLoans); ?></p>
                                </div>
                                <span class="text-3xl"></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-md border-l-4 border-amber-500 p-6 hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 font-medium mb-1">Jatuh Tempo</p>
                                    <p class="text-3xl font-bold text-amber-600"><?php echo intval($dueSoon); ?></p>
                                </div>
                                <span class="text-3xl"></span>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-md border-l-4 border-green-500 p-6 hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-slate-600 font-medium mb-1">Buku Tersedia</p>
                                    <p class="text-3xl font-bold text-green-600"><?php echo intval($availableBooks); ?></p>
                                </div>
                                <span class="text-3xl"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Aksi Cepat</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <a href="pinjam_buku.php" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                 Pinjam Buku Baru
                            </a>
                            <a href="riwayat_peminjaman.php" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 inline-flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                 Riwayat & Pengembalian
                            </a>
                        </div>
                    </div>

                    <!-- Buku yang Sedang Dipinjam -->
                    
                </div>
            </div>
        </main>
    </div>

</body>
</html>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>