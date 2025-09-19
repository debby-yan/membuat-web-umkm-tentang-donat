<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'website';

$koneksi = mysqli_connect($host, $username, $password, $dbname);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
