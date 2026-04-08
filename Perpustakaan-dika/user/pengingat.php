<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

include '../config.php/config.php';

$reminders = [];
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0);
$query = "SELECT t.*, b.judul, b.penulis, b.penerbit FROM transaksi t LEFT JOIN buku b ON t.id_buku = b.id_buku WHERE t.id_anggota = ? AND t.status = 'dipinjam' ORDER BY t.tanggal_kembali ASC";
$stmt = mysqli_prepare($koneksi, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $reminders[] = $row;
    }
    mysqli_stmt_close($stmt);
}

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pengembalian - Perpustakaan Digital</title>
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
                <a href="pinjam_buku.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                     Pinjam Buku
                </a>
                <a href="riwayat_peminjaman.php" class="px-4 py-3 rounded-lg hover:bg-indigo-700 text-indigo-100 transition duration-200 flex items-center gap-2">
                     Riwayat Peminjaman
                </a>
                <a href="pengingat.php" class="px-4 py-3 rounded-lg bg-indigo-700 text-white font-medium transition duration-200 flex items-center gap-2">
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
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-3xl font-bold text-slate-900">Pengingat Pengembalian</h2>
                        <p class="text-slate-600 mt-2">Pantau buku yang harus dikembalikan segera.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <?php if (count($reminders) > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">#</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Judul Buku</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Tanggal Kembali</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Status</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($reminders as $index => $reminder): ?>
                                            <?php
                                                $diffDays = (int) ((strtotime($reminder['tanggal_kembali']) - strtotime($today)) / 86400);
                                                if ($diffDays < 0) {
                                                    $note = 'Telat ' . abs($diffDays) . ' hari';
                                                    $badge = 'bg-red-100 text-red-700';
                                                } elseif ($diffDays <= 3) {
                                                    $note = 'Segera dikembalikan';
                                                    $badge = 'bg-yellow-100 text-yellow-700';
                                                } else {
                                                    $note = 'Aman';
                                                    $badge = 'bg-green-100 text-green-700';
                                                }
                                        ?>
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo $index + 1; ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($reminder['judul']); ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700"><?php echo htmlspecialchars($reminder['tanggal_kembali']); ?></td>
                                            <td class="px-4 py-3 text-sm text-slate-700">
                                                <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-700 text-xs font-semibold">Dipinjam</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-700">
                                                <span class="rounded-full <?php echo $badge; ?> px-3 py-1 text-xs font-semibold"><?php echo $note; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="rounded-xl border border-slate-200 p-6 text-slate-600">
                            Tidak ada pengingat pengembalian untuk saat ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>