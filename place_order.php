<?php
session_start();
include 'config.php';

// ====================
// VALIDASI LOGIN
// ====================
if (!isset($_SESSION['id_user'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

// ====================
// AMBIL DATA KERANJANG
// ====================
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<h2 class='text-center mt-10'>Keranjang kamu kosong — tidak bisa checkout.</h2>";
    echo "<p class='text-center'><a href='index.php' class='text-blue-600 underline'>Belanja sekarang</a></p>";
    exit;
}

// ====================
// AMBIL DETAIL PRODUK
// ====================
$ids = implode(',', array_keys($cart));
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id IN ($ids)");
$products = [];
while ($row = mysqli_fetch_assoc($query)) {
    $products[$row['id']] = $row;
}

// Hitung total harga
$total = 0;
foreach ($cart as $id => $qty) {
    $total += $products[$id]['harga'] * $qty;
}

// ========================
// PROSES PLACE ORDER
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id_user'];

    // Backup pesanan untuk ditampilkan di invoice
    $pesanan = [];
    foreach ($cart as $id => $qty) {
        $pesanan[] = [
            'id_produk' => $id,
            'nama'      => $products[$id]['nama'],
            'harga'     => $products[$id]['harga'],
            'qty'       => $qty
        ];
    }

    // Simpan transaksi
    $stmt = $koneksi->prepare("INSERT INTO transaksi (id_pelanggan, tanggal, total_harga) VALUES (?, NOW(), ?)");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $id_transaksi = $stmt->insert_id;
    $stmt->close();

    // Simpan detail_transaksi
    $stmtDetail = $koneksi->prepare("INSERT INTO detail_transaksi (transaksi_id, produk_id, qty, harga_satuan) VALUES (?, ?, ?, ?)");
    foreach ($pesanan as $item) {
        $produk_id    = $item['id_produk'];
        $qty          = $item['qty'];
        $harga_satuan = $item['harga'];
        $stmtDetail->bind_param("iiii", $id_transaksi, $produk_id, $qty, $harga_satuan);
        $stmtDetail->execute();
    }
    $stmtDetail->close();

    // Kosongkan keranjang
    unset($_SESSION['cart']);

    // Ambil data transaksi + user
    $stmt2 = $koneksi->prepare("
        SELECT t.id_transaksi, t.tanggal, t.total_harga, u.username, u.email
        FROM transaksi t
        JOIN users u ON t.id_pelanggan = u.id
        WHERE t.id_transaksi = ?
    ");
    $stmt2->bind_param("i", $id_transaksi);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $transaksi = $res->fetch_assoc();
    $stmt2->close();

    if (!$transaksi) {
        die("❌ Transaksi tidak ditemukan. Pastikan data user valid.");
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Transaksi Berhasil</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-blue-50 font-sans">
    <div class="container mx-auto p-6">
        
        <!-- Logo Toko -->
        <div class="flex flex-col items-center mb-6">
            <div class="w-24 h-24 rounded-full overflow-hidden shadow-lg border-4 border-blue-200 bg-white flex items-center justify-center">
                <img src="https://i.pinimg.com/736x/7d/a5/e8/7da5e8d2640d92b429cbcfee78355263.jpg" 
                     alt="Logo Donuts" 
                     class="w-full h-full object-cover">
            </div>
            <h2 class="mt-3 text-2xl font-bold text-blue-700">Donuts</h2>
        </div>

        <div class="bg-white shadow-lg rounded-2xl p-8 max-w-2xl mx-auto border border-blue-200">
            
            <!-- Judul -->
            <h1 class="text-3xl font-bold text-blue-700 mb-2">Transaksi Berhasil 🎉</h1>
            <p class="text-gray-600 mb-6">Terima kasih sudah berbelanja di 
                <span class="font-semibold text-blue-600">Donuts</span>
            </p>

            <!-- Info Transaksi -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 text-left border border-blue-100">
                <p class="mb-2">Nomor Transaksi: <b class="text-blue-700">#<?= $transaksi['id_transaksi'] ?></b></p>
                <p class="mb-2">Tanggal: <?= $transaksi['tanggal'] ?></p>
                <p class="mb-2">Email: <b><?= htmlspecialchars($transaksi['email']) ?></b></p>
            </div>

            <!-- Ringkasan Pesanan -->
            <h2 class="text-xl font-semibold text-blue-700 mb-3">Ringkasan Pesanan</h2>
            <table class="w-full mb-6 border border-blue-200 rounded-lg overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="py-2 px-4 text-left">Produk</th>
                        <th class="py-2 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesanan as $item): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?= htmlspecialchars($item['nama']) ?> (x<?= $item['qty'] ?>)</td>
                        <td class="py-2 px-4 text-right">
                            Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="font-bold bg-blue-50">
                        <td class="py-2 px-4">Total</td>
                        <td class="py-2 px-4 text-right text-blue-700">
                            Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Tombol -->
            <div class="flex gap-4 justify-center mb-6">
                <a href="index.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 shadow">
                    Belanja Lagi
                </a>
                <a href="invoice.php?id=<?= $transaksi['id_transaksi'] ?>" target="_blank" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 shadow">
                    Cetak Invoice
                </a>
            </div>
        </div>
    </div>
    </body>
    </html>
<?php } ?>
