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

$uploadDir = __DIR__ . '/../uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$error = '';
$id = intval($_GET['id'] ?? 0);
$book = null;

if ($id <= 0) {
    header('Location: kelola_buku.php?error=' . urlencode('ID buku tidak valid.'));
    exit;
}

$result = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku={$id} LIMIT 1");
if ($result && $book = mysqli_fetch_assoc($result)) {
    mysqli_free_result($result);
} else {
    header('Location: kelola_buku.php?error=' . urlencode('Buku tidak ditemukan.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = sanitize_input($koneksi, $_POST['judul'] ?? '');
    $penulis = sanitize_input($koneksi, $_POST['penulis'] ?? '');
    $penerbit = sanitize_input($koneksi, $_POST['penerbit'] ?? '');
    $tahun = intval($_POST['tahun'] ?? 0);
    $stok = intval($_POST['stok'] ?? 0);
    $gambarUrl = sanitize_input($koneksi, $_POST['gambar_url'] ?? '');
    $oldImage = sanitize_input($koneksi, $_POST['old_gambar'] ?? $book['gambar']);

    if ($judul === '' || $penulis === '' || $penerbit === '' || $tahun <= 0 || $stok < 0) {
        $error = 'Semua kolom wajib diisi dengan benar.';
    }

    $imagePath = $oldImage;
    if (empty($error)) {
        if (!empty($_FILES['gambar']['name']) && is_uploaded_file($_FILES['gambar']['tmp_name'])) {
            $filename = basename($_FILES['gambar']['name']);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($extension, $allowedExtensions, true)) {
                $newName = uniqid('book_', true) . '.' . $extension;
                $targetPath = $uploadDir . '/' . $newName;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
                    $imagePath = 'uploads/' . $newName;
                    if (!empty($oldImage) && str_starts_with($oldImage, 'uploads/') && file_exists(__DIR__ . '/../' . $oldImage)) {
                        @unlink(__DIR__ . '/../' . $oldImage);
                    }
                } else {
                    $error = 'Gagal mengunggah gambar buku.';
                }
            } else {
                $error = 'Ekstensi gambar tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.';
            }
        } elseif (!empty($gambarUrl) && $gambarUrl !== $oldImage) {
            if (!empty($oldImage) && str_starts_with($oldImage, 'uploads/') && file_exists(__DIR__ . '/../' . $oldImage)) {
                @unlink(__DIR__ . '/../' . $oldImage);
            }
            $imagePath = $gambarUrl;
        }
    }

    if (empty($error)) {
        $query = "UPDATE buku SET judul='{$judul}', gambar='" . mysqli_real_escape_string($koneksi, $imagePath) . "', penulis='{$penulis}', penerbit='{$penerbit}', tahun={$tahun}, stok={$stok} WHERE id_buku={$id}";
        if (mysqli_query($koneksi, $query)) {
            header('Location: kelola_buku.php?success=' . urlencode('Buku berhasil diperbarui.'));
            exit;
        }
        $error = 'Gagal memperbarui buku: ' . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - SIPUS</title>
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
        <div class="max-w-5xl mx-auto">

            <div class="bg-white rounded-xl border shadow-sm p-8">

                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-slate-800">Sunting Buku</h2>
                    <span class="text-sm text-slate-500">
                        Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>
                    </span>
                </div>

                <!-- Error -->
                <?php if ($error): ?>
                    <div class="mb-6 rounded-md border border-red-200 bg-red-100 p-3 text-sm text-red-700">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="buku_edit.php?id=<?php echo intval($id); ?>" method="post" enctype="multipart/form-data" class="space-y-6">

                    <input type="hidden" name="id_buku" value="<?php echo intval($id); ?>">
                    <input type="hidden" name="old_gambar" value="<?php echo htmlspecialchars($book['gambar']); ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Judul</label>
                            <input type="text" name="judul"
                                value="<?php echo htmlspecialchars($_POST['judul'] ?? $book['judul']); ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Penulis</label>
                            <input type="text" name="penulis"
                                value="<?php echo htmlspecialchars($_POST['penulis'] ?? $book['penulis']); ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Penerbit</label>
                            <input type="text" name="penerbit"
                                value="<?php echo htmlspecialchars($_POST['penerbit'] ?? $book['penerbit']); ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Tahun</label>
                            <input type="number" name="tahun"
                                value="<?php echo htmlspecialchars($_POST['tahun'] ?? $book['tahun']); ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Stok</label>
                            <input type="number" name="stok"
                                value="<?php echo htmlspecialchars($_POST['stok'] ?? $book['stok']); ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Gambar Buku</label>
                            <input type="file" name="gambar" accept="image/*"
                                class="w-full text-sm border border-slate-300 rounded-md file:mr-3 file:px-3 file:py-2 file:border-0 file:bg-indigo-600 file:text-white file:rounded-md hover:file:bg-indigo-700">
                            <p class="text-xs text-slate-400 mt-1">
                                Upload gambar baru atau gunakan URL
                            </p>

                            <?php if (!empty($book['gambar'])): ?>
                                <div class="mt-3">
                                    <p class="text-xs text-slate-500 mb-1">Gambar saat ini:</p>
                                    <img src="<?php echo htmlspecialchars($book['gambar']); ?>"
                                         class="h-24 w-20 object-cover rounded-md border">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 mb-1">URL Gambar</label>
                            <input type="text" name="gambar_url"
                                value="<?php echo htmlspecialchars($_POST['gambar_url'] ?? $book['gambar']); ?>"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

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