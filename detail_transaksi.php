<?php
session_start();
include 'config.php';

// CEK LOGIN ADMIN
if (!isset($_SESSION['admin'])){
    header('Location: login.php');
    exit;
}

$id_transaksi = $_GET['id'] ?? 0;

// Ambil data transaksi + user
$query = "
    SELECT t.id_transaksi, t.tanggal, t.total_harga, 
           u.username, u.email, u.hp, u.alamat
    FROM transaksi t
    JOIN users u ON t.id_pelanggan = u.id
    WHERE t.id_transaksi = '$id_transaksi'
";
$transaksi = mysqli_fetch_assoc(mysqli_query($koneksi, $query));

// Ambil detail produk
$queryDetail = "
    SELECT d.qty, d.harga_satuan, p.nama
    FROM detail_transaksi d
    JOIN produk p ON d.produk_id = p.id
    WHERE d.transaksi_id = '$id_transaksi'
";
$details = mysqli_query($koneksi, $queryDetail);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold text-blue-700 mb-4">🧾 Detail Transaksi</h1>

    <div class="bg-white shadow-lg rounded-2xl p-6 mb-6">
        <p><b>No Transaksi:</b> #<?= $transaksi['id_transaksi'] ?></p>
        <p><b>Tanggal:</b> <?= $transaksi['tanggal'] ?></p>
        <p><b>Nama:</b> <?= htmlspecialchars($transaksi['username']) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($transaksi['email']) ?></p>
        <p><b>HP:</b> <?= htmlspecialchars($transaksi['hp']) ?></p>
        <p><b>Alamat:</b> <?= htmlspecialchars($transaksi['alamat']) ?></p>
    </div>

    <div class="bg-white shadow-lg rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-blue-600 mb-4">Produk yang Dibeli</h2>
        <table class="w-full border border-gray-200">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="py-2 px-4 text-left">Produk</th>
                    <th class="py-2 px-4 text-right">Qty</th>
                    <th class="py-2 px-4 text-right">Harga Satuan</th>
                    <th class="py-2 px-4 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($details)): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
                        <td class="py-2 px-4 text-right"><?= $row['qty'] ?></td>
                        <td class="py-2 px-4 text-right">Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                        <td class="py-2 px-4 text-right">Rp <?= number_format($row['harga_satuan'] * $row['qty'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
                <tr class="font-bold bg-blue-50">
                    <td colspan="3" class="py-2 px-4 text-right">Total</td>
                    <td class="py-2 px-4 text-right text-blue-700">
                        Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="riwayat_transaksi_admin.php" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Kembali</a>
    </div>
</div>
</body>
</html>
