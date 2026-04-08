<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_transaksi'])) {
    header('Location: riwayat_peminjaman.php');
    exit;
}

include '../config.php/config.php';

$id_transaksi = intval($_POST['id_transaksi']);
$id_anggota = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : (isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0);

if ($id_transaksi <= 0 || $id_anggota <= 0) {
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Data transaksi tidak valid.'));
    exit;
}

mysqli_begin_transaction($koneksi);

$query = 'SELECT id_buku, status FROM transaksi WHERE id_transaksi = ? AND id_anggota = ? FOR UPDATE';
$stmt = mysqli_prepare($koneksi, $query);
if (!$stmt) {
    mysqli_rollback($koneksi);
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Terjadi kesalahan server.'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'ii', $id_transaksi, $id_anggota);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_buku, $status);
$hasTrans = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$hasTrans || $status !== 'dipinjam') {
    mysqli_rollback($koneksi);
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Transaksi tidak ditemukan atau sudah dikembalikan.'));
    exit;
}

$tanggal_kembali = date('Y-m-d');
$updateTrans = 'UPDATE transaksi SET status = ?, tanggal_kembali = ? WHERE id_transaksi = ?';
$stmt = mysqli_prepare($koneksi, $updateTrans);
if (!$stmt) {
    mysqli_rollback($koneksi);
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Gagal memperbarui transaksi.'));
    exit;
}

$statusReturned = 'dikembalikan';
mysqli_stmt_bind_param($stmt, 'ssi', $statusReturned, $tanggal_kembali, $id_transaksi);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    mysqli_rollback($koneksi);
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Gagal memperbarui status pengembalian.'));
    exit;
}

$updateBook = 'UPDATE buku SET stok = stok + 1 WHERE id_buku = ?';
$stmt = mysqli_prepare($koneksi, $updateBook);
if (!$stmt) {
    mysqli_rollback($koneksi);
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Gagal memperbarui stok buku.'));
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id_buku);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (!$ok) {
    mysqli_rollback($koneksi);
    header('Location: riwayat_peminjaman.php?error=' . urlencode('Gagal memperbarui stok buku.'));
    exit;
}

mysqli_commit($koneksi);
header('Location: riwayat_peminjaman.php?success=' . urlencode('Buku berhasil dikembalikan.'));
exit;
