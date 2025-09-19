<?php
session_start();

// Simpan info role dulu sebelum dihapus
$isAdmin = isset($_SESSION['admin']);

// Hapus semua session
session_unset();
session_destroy();

// Redirect sesuai role
if ($isAdmin) {
    header("Location: login.php"); // admin → login
} else {
    header("Location: index.php"); // pelanggan → homepage
}
exit();