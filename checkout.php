<?php
session_start();
include 'config.php';
// Update jumlah item kalau ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['update_qty'])) {
    $product_id = (int) $_POST['product_id'];
    $new_qty = (int) $_POST['update_qty'];

    if ($new_qty > 0) {
        $_SESSION['cart'][$product_id] = $new_qty;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }

    // reload biar nilai qty baru masuk
    header("Location: checkout.php");
    exit;
}


// Ambil keranjang
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<h2 class='text-center mt-10'>Keranjang kamu kosong</h2>";
    exit;
}

$ids = implode(',', array_keys($cart));
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id IN ($ids)");

$products = [];
while ($row = mysqli_fetch_assoc($query)) {
    $row['qty'] = $cart[$row['id']];
    $row['subtotal'] = $row['harga'] * $row['qty'];
    $products[] = $row;
}

$ongkir = 10000; // contoh ongkir
$grand_total = array_sum(array_column($products, 'subtotal')) + $ongkir;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-center">Checkout</h1>

    <!-- RINGKASAN PESANAN -->
    <div class="bg-white rounded shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
        <table class="w-full mb-4">
            <thead>
                <tr class="border-b">
                    <th class="py-2 text-left">Produk</th>
                    <th class="py-2 text-center">Jumlah</th>
                    <th class="py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $produk): ?>
                <tr class="border-b">
                    <td class="py-3 flex items-center space-x-3">
                        <img src="img/<?= htmlspecialchars($produk['foto']) ?>" 
                             alt="<?= htmlspecialchars($produk['nama']) ?>" 
                             class="w-16 h-16 object-cover rounded">
                        <span><?= htmlspecialchars($produk['nama']) ?></span>
                    </td>
                    <td class="py-3 text-center">
    <div class="flex items-center justify-center space-x-2">
        <form method="post" action="checkout.php" class="flex items-center space-x-2">
            <input type="hidden" name="product_id" value="<?= $produk['id'] ?>">
            
            <!-- Tombol minus -->
            <button type="button" onclick="changeQty('minus', <?= $produk['id'] ?>)" 
                class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300">
                −
            </button>
            
            <!-- Input jumlah -->
            <input type="number" 
                   id="qty-<?= $produk['id'] ?>" 
                   name="update_qty" 
                   value="<?= $produk['qty'] ?>" 
                   min="1" 
                   max="<?= $produk['stok'] ?? 99 ?>" 
                   class="w-12 text-center border rounded">
            
            <!-- Tombol plus -->
            <button type="button" onclick="changeQty('plus', <?= $produk['id'] ?>)" 
                class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300">
                +
            </button>
        </form>
    </div>
</td>

                    <td class="py-3 text-right">
                        Rp <?= number_format($produk['subtotal'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2" class="py-3 text-left">Ongkos Kirim</td>
                    <td class="py-3 text-right">Rp <?= number_format($ongkir, 0, ',', '.') ?></td>
                </tr>
                <tr class="font-bold">
                    <td colspan="2" class="py-3">Total Bayar</td>
                    <td class="py-3 text-right text-blue-600">
                        Rp <?= number_format($grand_total, 0, ',', '.') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- FORM PENGIRIMAN -->
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Detail Pengiriman & Pembayaran</h2>
        <form action="place_order.php" method="post" class="space-y-4">
            <!-- kirim semua produk -->
            <?php foreach ($products as $produk): ?>
                <input type="hidden" name="id_produk[]" value="<?= $produk['id'] ?>">
                <input type="hidden" name="qty[]" value="<?= $produk['qty'] ?>">
                <input type="hidden" name="subtotal[]" value="<?= $produk['subtotal'] ?>">
            <?php endforeach; ?>
            <input type="hidden" name="total" value="<?= $grand_total ?>">

            <div>
                <label class="font-medium">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="font-medium">Alamat</label>
                <textarea name="alamat" required class="w-full border p-2 rounded"></textarea>
            </div>
            <div>
                <label class="font-medium">Nomor Telepon</label>
                <input type="tel" name="telepon" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="font-medium">Metode Pembayaran</label>
                <select name="metode_pembayaran" required class="w-full border p-2 rounded">
                    <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                    <option value="transfer_bank">Transfer Bank</option>
                    <option value="cod">COD</option>
                    <option value="ewallet">E-Wallet</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Konfirmasi & Checkout
            </button>
        </form>
    </div>
</div>
<script>
function changeQty(action, productId) {
    const input = document.getElementById('qty-' + productId);
    let currentQty = parseInt(input.value);
    const max = parseInt(input.max);

    if (action === 'plus' && currentQty < max) {
        input.value = currentQty + 1;
    } else if (action === 'minus' && currentQty > 1) {
        input.value = currentQty - 1;
    }
    input.form.submit(); // submit otomatis
}
</script>

</body>
</html>
