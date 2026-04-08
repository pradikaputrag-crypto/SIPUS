<?php
include 'config.php/config.php';
$res = mysqli_query($koneksi, 'SHOW COLUMNS FROM peminjaman');
if (!$res) {
    echo 'ERROR: ' . mysqli_error($koneksi);
    exit(1);
}
while ($col = mysqli_fetch_assoc($res)) {
    echo $col['Field'] . ' ' . $col['Type'] . "\n";
}
