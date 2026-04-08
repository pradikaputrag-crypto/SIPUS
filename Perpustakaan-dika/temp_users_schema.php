<?php
$koneksi=mysqli_connect('localhost','root','','perpustakaan');
if(!$koneksi){echo 'KONEKSI GAGAL'; exit(1);}
$res=mysqli_query($koneksi,'SHOW COLUMNS FROM users');
if(!$res){echo 'ERROR: '.mysqli_error($koneksi); exit(1);}
while($row=mysqli_fetch_assoc($res)){
    echo $row['Field'].'|'.$row['Type'].'|'.$row['Null'].'|'.$row['Key'].'\n';
}
