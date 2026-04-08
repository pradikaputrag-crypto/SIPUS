<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config.php/config.php';

$error = '';
$transactions = [];

$query = "SELECT t.*, a.nama AS nama_anggota, b.judul AS judul_buku
          FROM transaksi t
          LEFT JOIN anggota a ON t.id_anggota = a.id_anggota
          LEFT JOIN buku b ON t.id_buku = b.id_buku
          ORDER BY t.id_transaksi DESC";
$result = mysqli_query($koneksi, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
    mysqli_free_result($result);
} else {
    $error = 'Gagal memuat data transaksi: ' . mysqli_error($koneksi);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Admin Perpustakaan</title>
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
                    <p class="text-xs text-indigo-200">Admin Portal</p>
                </div>
            </div>
            <nav class="flex-1 flex flex-col gap-1 p-4">
                <a href="dashboard.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                    Dashboard
                </a>
                <a href="kelola_buku.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                    Kelola Buku
                </a>
                <a href="kelola_anggota.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                    Kelola Anggota
                </a>
                <a href="transaksi.php" class="px-4 py-3 rounded-lg bg-indigo-700 text-white font-medium transition duration-200 flex items-center gap-2">
                    Transaksi
                </a>
            </nav>
            <div class="border-t border-indigo-700 p-4">
                <a href="../auth/logout.php" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center gap-2">
                         Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-auto">
            <div class="p-8 h-full flex flex-col">
                <div class="max-w-6xl w-full mx-auto flex-1 flex flex-col">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-3xl font-bold text-slate-900">Daftar Transaksi</h2>
                        <p class="text-slate-600 mt-2">Menampilkan semua transaksi peminjaman dan pengembalian buku.</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="bg-red-100 text-red-700 border border-red-200 rounded-lg p-4 mb-6">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-xl shadow-lg p-8 flex-1 flex flex-col">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-900">Transaksi</h3>
                                <p class="text-slate-600">Total transaksi: <?php echo count($transactions); ?></p>
                            </div>
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <a href="transaksi.php" class="bg-indigo-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                    Refresh Data
                                </a>
                            </div>
                        </div>

                        <?php if (count($transactions) > 0): ?>
                            <div class="overflow-x-auto flex-1">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">#</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">ID Transaksi</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Anggota</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Buku</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Tanggal Pinjam</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Tanggal Kembali</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($transactions as $index => $transaction): ?>
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo $index + 1; ?></td>
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($transaction['id_transaksi']); ?></td>
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($transaction['nama_anggota'] ?: 'ID ' . $transaction['id_anggota']); ?></td>
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($transaction['judul_buku'] ?: 'ID ' . $transaction['id_buku']); ?></td>
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($transaction['tanggal_pinjam']); ?></td>
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($transaction['tanggal_kembali']); ?></td>
                                                <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($transaction['status']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="rounded-xl border border-slate-200 p-6 text-slate-600 text-center flex-1 flex items-center justify-center">
                                Tidak ada data transaksi yang dapat ditampilkan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
