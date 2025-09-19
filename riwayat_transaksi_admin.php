<?php
session_start();
include 'config.php';

// ====================
// CEK LOGIN ADMIN
// ====================
if (!isset($_SESSION['admin'])){
    header('Location: login.php');
    exit;
}

// ====================
// AMBIL SEMUA TRANSAKSI
// ====================
$query = "
    SELECT 
        t.id_transaksi, 
        t.tanggal, 
        t.total_harga, 
        u.username, 
        u.email, 
        u.hp, 
        u.alamat
    FROM transaksi t
    JOIN users u ON t.id_pelanggan = u.id
    ORDER BY t.tanggal DESC
";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="dashboard_admin.php" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-full shadow-md transition flex items-center justify-center"
               title="Kembali ke Dashboard">
                ⬅
            </a>
            <h1 class="text-3xl font-bold text-blue-700">📑 Riwayat Transaksi Semua User</h1>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-lg rounded-2xl p-6 overflow-x-auto">
            <table class="w-full border border-gray-200">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-2 px-4 text-left">No</th>
                        <th class="py-2 px-4 text-left">Tanggal</th>
                        <th class="py-2 px-4 text-left">Username</th>
                        <th class="py-2 px-4 text-left">Email</th>
                        <th class="py-2 px-4 text-left">HP</th>
                        <th class="py-2 px-4 text-left">Alamat</th>
                        <th class="py-2 px-4 text-right">Total</th>
                        <th class="py-2 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($result) > 0): 
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="border-b hover:bg-blue-50">
                                <td class="py-2 px-4"><?= $no++ ?></td>
                                <td class="py-2 px-4"><?= $row['tanggal'] ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['hp']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['alamat']) ?></td>
                                <td class="py-2 px-4 text-right text-blue-700 font-semibold">
                                    Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <a href="detail_transaksi.php?id=<?= $row['id_transaksi'] ?>" 
                                       class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Detail</a>
                                    <a href="hapus_transaksi.php?id=<?= $row['id_transaksi'] ?>" 
                                       onclick="return confirm('Yakin mau hapus transaksi ini?')" 
                                       class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr>
                            <td colspan="8" class="py-4 text-center text-gray-500">
                                Tidak ada transaksi.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
