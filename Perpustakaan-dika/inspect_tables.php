<?php
include 'config.php/config.php';
$res = mysqli_query($koneksi, 'SHOW TABLES');
if (!$res) {
    echo 'ERROR: ' . mysqli_error($koneksi);
    exit(1);
}
while ($row = mysqli_fetch_row($res)) {
    echo $row[0] . "\n";
}
