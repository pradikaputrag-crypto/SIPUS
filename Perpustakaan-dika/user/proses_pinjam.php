<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_buku'])) {
    header('Location: pinjam_buku.php');
    exit;
}

include '../config.php/config.php';

$id_buku = intval($_POST['id_buku']);
$id_anggota = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0);

if ($id_buku <= 0 || $id_anggota <= 0) {
    header('Location: pinjam_buku.php?error=' . urlencode('Data peminjaman tidak valid.'));
    exit;
}

mysqli_begin_transaction($koneksi);

$bookQuery = 'SELECT stok FROM buku WHERE id_buku = ? FOR UPDATE';
$stmt = mysqli_prepare($koneksi, $bookQuery);
if (!$stmt) {
    mysqli_rollback($koneksi);
    header('Location: pinjam_buku.php?error=' . urlencode('Terjadi kesalahan server.'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id_buku);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $stok);
$hasBook = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$hasBook || $stok <= 0) {
    mysqli_rollback($koneksi);
    header('Location: pinjam_buku.php?error=' . urlencode('Buku tidak tersedia untuk dipinjam.'));
    exit;
}

$tanggal_pinjam = date('Y-m-d');
$tanggal_kembali = date('Y-m-d', strtotime('+7 days'));
$status = 'dipinjam';

$insertQuery = 'INSERT INTO transaksi (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($koneksi, $insertQuery);
if (!$stmt) {
    mysqli_rollback($koneksi);
    header('Location: pinjam_buku.php?error=' . urlencode('Gagal menyimpan transaksi.'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'iisss', $id_anggota, $id_buku, $tanggal_pinjam, $tanggal_kembali, $status);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    mysqli_rollback($koneksi);
    header('Location: pinjam_buku.php?error=' . urlencode('Gagal menyimpan transaksi peminjaman.'));
    exit;
}

$updateBook = 'UPDATE buku SET stok = stok - 1 WHERE id_buku = ?';
$stmt = mysqli_prepare($koneksi, $updateBook);
if (!$stmt) {
    mysqli_rollback($koneksi);
    header('Location: pinjam_buku.php?error=' . urlencode('Gagal memperbarui stok buku.'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id_buku);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    mysqli_rollback($koneksi);
    header('Location: pinjam_buku.php?error=' . urlencode('Gagal memperbarui stok buku.'));
    exit;
}

mysqli_commit($koneksi);
header('Location: pinjam_buku.php?success=' . urlencode('Buku berhasil ditambahkan ke daftar pinjaman.'));
exit;
