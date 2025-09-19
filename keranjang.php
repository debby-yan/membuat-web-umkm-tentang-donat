<?php
session_start();
include 'config.php';

// Update jumlah item jika ada POST update qty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update qty
    if (isset($_POST['update_qty']) && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $new_qty = intval($_POST['update_qty']);
        if ($new_qty > 0) {
            $_SESSION['cart'][$product_id] = $new_qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
        header('Location: keranjang.php');
        exit;    
    }

    // Hapus item
    if (isset($_POST['remove_id'])) {
        $remove_id = $_POST['remove_id'];
        unset($_SESSION['cart'][$remove_id]);
        header('Location: keranjang.php');
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Keranjang Kosong</title>
        <script src='https://cdn.tailwindcss.com'></script>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'/>
    </head>
    <body class='flex items-center justify-center min-h-screen bg-gray-100'>
        <div class='bg-white shadow-lg rounded-xl p-10 text-center'>
            <i class='fa-solid fa-cart-shopping text-6xl text-blue-500 mb-4 animate-bounce'></i>
            <h2 class='text-2xl font-bold text-gray-800 mb-2'>Keranjangmu Kosong</h2>
            <p class='text-gray-500 mb-6'>Yuk, mulai belanja sekarang!</p>
            <a href='index.php' class='bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl shadow hover:opacity-90 transition'>
                Belanja Sekarang
            </a>
        </div>
    </body>
    </html>
    ";
    exit;
}

// Ambil produk berdasarkan id di keranjang
$ids = implode(',', array_keys($cart));
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id IN ($ids)");

$products = [];
while ($row = mysqli_fetch_assoc($query)) {
    $products[$row['id']] = $row;
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body class="bg-gray-100 font-sans">
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-extrabold mb-8 text-center text-blue-600">🛒 Keranjang Belanja</h1>

    <div class="overflow-x-auto bg-white shadow-lg rounded-xl">
        <table class="min-w-full">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="py-3 px-6 text-left">Produk</th>
                    <th class="py-3 px-6 text-center">Harga</th>
                    <th class="py-3 px-6 text-center">Jumlah</th>
                    <th class="py-3 px-6 text-center">Subtotal</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
            <?php foreach ($cart as $id => $qty):
                $product = $products[$id];
                $subtotal = $product['harga'] * $qty;
                $total += $subtotal;
            ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-4 px-6 flex items-center space-x-4">
                        <img src="img/<?= $product['foto'] ?>" alt="<?= $product['nama'] ?>" class="w-16 h-16 object-contain rounded-lg shadow">
                        <span class="font-medium"><?= $product['nama'] ?></span>
                    </td>
                    <td class="py-4 px-6 text-center">Rp <?= number_format($product['harga'], 0, ',', '.') ?></td>
                    <td class="py-4 px-6 text-center">
                        <form method="post" class="inline-flex items-center space-x-1">
                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            <button type="button" onclick="changeQty('minus', <?= $id ?>)" class="bg-gray-200 px-2 rounded hover:bg-gray-300">−</button>
                            <input type="number" name="update_qty" id="qty-<?= $id ?>" value="<?= $qty ?>" min="1" max="<?= $product['stok'] ?>" class="w-12 text-center border rounded" onchange="this.form.submit()">
                            <button type="button" onclick="changeQty('plus', <?= $id ?>)" class="bg-gray-200 px-2 rounded hover:bg-gray-300">+</button>
                        </form>
                    </td>
                    <td class="py-4 px-6 text-center font-semibold text-blue-600">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    <td class="py-4 px-6 text-center">
                        <form method="post" onsubmit="return confirm('Hapus produk ini dari keranjang?')" class="inline">
                            <input type="hidden" name="remove_id" value="<?= $id ?>">
                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
                <tr class="bg-gray-100 font-bold text-lg">
                    <td colspan="3" class="py-4 px-6 text-right">Total</td>
                    <td class="py-4 px-6 text-center text-blue-700">Rp <?= number_format($total, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-between">
        <a href="index.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-xl shadow hover:bg-gray-400 transition">⬅ Lanjut Belanja</a>
        <a href="checkout.php" class="bg-blue-600 text-white px-6 py-3 rounded-xl shadow hover:bg-blue-700 transition">Checkout ➡</a>
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
    input.form.submit();
}
</script>
</body>
</html>
