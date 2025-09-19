<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $qty = (int)$_POST['qty'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // tambahkan qty, bukan menimpa
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }

    $_SESSION['success'] = "Produk berhasil ditambahkan ke keranjang!";
    header("Location: keranjang.php");
    exit;
}
