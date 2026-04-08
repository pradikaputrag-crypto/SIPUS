<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

include '../config.php/config.php';

$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

$availableBooks = [];
$query = "SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC";
$result = mysqli_query($koneksi, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $availableBooks[] = $row;
    }
    mysqli_free_result($result);
} else {
    $error = 'Gagal memuat daftar buku: ' . mysqli_error($koneksi);
}

$activeLoans = 0;
$dueSoon = 0;
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0);

$borrowedQuery = "SELECT COUNT(*) AS total FROM transaksi WHERE id_anggota = ? AND status = 'dipinjam'";
$stmt = mysqli_prepare($koneksi, $borrowedQuery);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $activeLoans);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

$dueSoonQuery = "SELECT COUNT(*) AS total FROM transaksi WHERE id_anggota = ? AND status = 'dipinjam' AND tanggal_kembali BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
$stmt = mysqli_prepare($koneksi, $dueSoonQuery);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $dueSoon);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Buku - Perpustakaan Digital</title>
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
                <a href="dashboard.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                     Dashboard
                </a>
                <a href="pinjam_buku.php" class="px-4 py-3 rounded-lg bg-indigo-700 text-white font-medium transition duration-200 flex items-center gap-2">
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

        <main class="flex-1 overflow-auto">
            <div class="p-8 h-full">
                <div class="max-w-6xl w-full mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                    <h2 class="text-3xl font-bold text-slate-900">Pinjam Buku</h2>
                    <p class="text-slate-600 mt-2">Pilih buku tersedia untuk dipinjam dan atur tanggal kembali.</p>
                </div>

                <?php if ($success): ?>
                    <div class="bg-green-100 text-green-800 border border-green-200 rounded-lg p-4 mb-6">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-100 text-red-700 border border-red-200 rounded-lg p-4 mb-6">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                        <p class="text-slate-600 font-semibold">Peminjaman Aktif</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo intval($activeLoans); ?></p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                        <p class="text-slate-600 font-semibold">Pengembalian dalam 3 hari</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo intval($dueSoon); ?></p>
                    </div>
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <p class="text-slate-600 font-semibold">Buku Tersedia</p>
                        <p class="text-3xl font-bold text-slate-900 mt-2"><?php echo count($availableBooks); ?></p>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">Daftar Buku Tersedia</h3>
                            <p class="text-slate-600">Silakan pinjam buku yang stoknya tersedia.</p>
                        </div>
                        <a href="riwayat_peminjaman.php" class="bg-indigo-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">Lihat Riwayat</a>
                    </div>

                    <?php if (count($availableBooks) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Judul</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Penulis</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Penerbit</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Stok</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($availableBooks as $book): ?>
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($book['judul']); ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($book['penulis']); ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($book['penerbit']); ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo intval($book['stok']); ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700">
                                                <form action="proses_pinjam.php" method="post" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                    <input type="hidden" name="id_buku" value="<?php echo intval($book['id_buku']); ?>">
                                                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition">Pinjam</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="rounded-xl border border-slate-200 p-6 text-slate-600">
                            Tidak ada buku tersedia saat ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>