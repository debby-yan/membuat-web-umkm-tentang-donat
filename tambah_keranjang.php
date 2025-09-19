<?php
session_start();
include 'config.php';

// Ambil data dari permintaan AJAX
if (isset($_POST['id'])) { // <-- Ubah dari 'id_produk' menjadi 'id'
    $id_produk = intval($_POST['id']);
    $qty = 1; // Asumsi default quantity 1 saat ditambahkan dari halaman produk

    // Jika cart belum ada, buat array kosong
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Kalau produk sudah ada di keranjang → tambahkan qty
    if (isset($_SESSION['cart'][$id_produk])) {
        $_SESSION['cart'][$id_produk] += $qty;
    } else {
        $_SESSION['cart'][$id_produk] = $qty;
    }

    // Kirim respons JSON yang menunjukkan keberhasilan
    // Ini akan ditangkap oleh fungsi callback AJAX di produk.php
    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!']);
    exit; // Penting untuk menghentikan eksekusi setelah mengirim respons JSON
}

// Jika tidak ada ID produk yang diterima, kirim respons error
echo json_encode(['status' => 'error', 'message' => 'ID produk tidak ditemukan.']);
exit;
?>