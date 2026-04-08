<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config.php/config.php';

function sanitize_input($koneksi, $value) {
    return mysqli_real_escape_string($koneksi, trim($value));
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: kelola_anggota.php?error=' . urlencode('ID anggota tidak valid.'));
    exit;
}

$result = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota={$id} LIMIT 1");
if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: kelola_anggota.php?error=' . urlencode('Anggota tidak ditemukan.'));
    exit;
}

$member = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize_input($koneksi, $_POST['nama'] ?? '');
    $kelas = sanitize_input($koneksi, $_POST['kelas'] ?? '');
    $alamat = sanitize_input($koneksi, $_POST['alamat'] ?? '');

    if ($nama === '' || $kelas === '' || $alamat === '') {
        $error = 'Semua kolom wajib diisi.';
    }

    if (empty($error)) {
        $query = "UPDATE anggota SET nama='{$nama}', kelas='{$kelas}', alamat='{$alamat}' WHERE id_anggota={$id}";
        if (mysqli_query($koneksi, $query)) {
            header('Location: kelola_anggota.php?success=' . urlencode('Anggota berhasil diperbarui.'));
            exit;
        }
        $error = 'Gagal memperbarui anggota: ' . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - SIPUS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100">

<div class="flex min-h-screen">

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
    <main class="flex-1 p-8">
        <div class="max-w-4xl mx-auto">

            <div class="bg-white rounded-xl border shadow-sm p-8">

                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-slate-800">Sunting Anggota</h2>
                    <p class="text-sm text-slate-500">Perbarui data anggota</p>
                </div>

                <!-- Error -->
                <?php if ($error): ?>
                    <div class="mb-4 rounded-md border border-red-200 bg-red-100 p-3 text-sm text-red-700">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="anggota_edit.php?id=<?php echo intval($id); ?>" method="post" class="space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Nama</label>
                        <input type="text" name="nama"
                            value="<?php echo htmlspecialchars($_POST['nama'] ?? $member['nama']); ?>"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Kelas</label>
                        <input type="text" name="kelas"
                            value="<?php echo htmlspecialchars($_POST['kelas'] ?? $member['kelas']); ?>"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Alamat</label>
                        <textarea name="alamat" rows="4"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required><?php echo htmlspecialchars($_POST['alamat'] ?? $member['alamat']); ?></textarea>
                    </div>

                    <div>
                        <button type="submit"
                            class="bg-indigo-600 text-white px-5 py-2.5 rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </main>

</div>

</body>
</html>