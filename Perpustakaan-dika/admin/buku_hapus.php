<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config.php/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_buku'])) {
    header('Location: kelola_buku.php');
    exit;
}

$id = intval($_POST['id_buku']);
$error = '';

$result = mysqli_query($koneksi, "SELECT gambar FROM buku WHERE id_buku={$id} LIMIT 1");
if ($result && $row = mysqli_fetch_assoc($result)) {
    if (!empty($row['gambar']) && str_starts_with($row['gambar'], 'uploads/') && file_exists(__DIR__ . '/../' . $row['gambar'])) {
        @unlink(__DIR__ . '/../' . $row['gambar']);
    }
    mysqli_free_result($result);

    if (mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku={$id}")) {
        header('Location: kelola_buku.php?success=' . urlencode('Buku berhasil dihapus.'));
        exit;
    }
    $error = 'Gagal menghapus buku: ' . mysqli_error($koneksi);
} else {
    $error = 'Buku tidak ditemukan.';
}

header('Location: kelola_buku.php?error=' . urlencode($error));
exit;
