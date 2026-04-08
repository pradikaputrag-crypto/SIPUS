<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config.php/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_anggota'])) {
    header('Location: kelola_anggota.php');
    exit;
}

$id = intval($_POST['id_anggota']);

if ($id <= 0) {
    header('Location: kelola_anggota.php?error=' . urlencode('ID anggota tidak valid.'));
    exit;
}

if (mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota={$id}")) {
    header('Location: kelola_anggota.php?success=' . urlencode('Anggota berhasil dihapus.'));
    exit;
}

header('Location: kelola_anggota.php?error=' . urlencode('Gagal menghapus anggota: ' . mysqli_error($koneksi)));
exit;
