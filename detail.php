<?php
session_start();
include 'config.php';

// ======================= AMBIL DATA PRODUK =======================
$id = $_GET['id'] ?? 0;
$id = intval($id);

$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    die("Produk tidak ditemukan!");
}

$show_alert = false; // flag untuk SweetAlert

// ======================= PROSES FORM =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah = max(1, intval($_POST['jumlah']));

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Tambah ke keranjang
    if (isset($_POST['keranjang'])) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $jumlah;
        } else {
            $_SESSION['cart'][$id] = $jumlah;
        }
        $show_alert = true; 
    }

    // Beli sekarang
    if (isset($_POST['beli_sekarang'])) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $jumlah;
        } else {
            $_SESSION['cart'][$id] = $jumlah;
        }
        header("Location: checkout.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk - <?= htmlspecialchars($produk['nama']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-10">
        <div class="bg-white shadow-md rounded-lg p-6 md:flex md:space-x-8">
            
            <!-- Gambar Produk -->
            <div class="md:w-1/2 mb-6 md:mb-0">
                <img src="img/<?= htmlspecialchars($produk['foto']) ?>" 
                     alt="<?= htmlspecialchars($produk['nama']) ?>" 
                     class="rounded-lg w-full h-96 object-contain bg-gray-50">
            </div>

            <!-- Detail Produk -->
            <div class="md:w-1/2">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <?= htmlspecialchars($produk['nama']) ?>
                </h1>
                <p class="text-2xl text-blue-600 font-semibold mb-2">
                    Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                </p>
                <p class="text-sm text-gray-600 mb-4">
                    <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
                </p>
                <p class="text-md text-green-600 font-medium mb-2">
                    Stok tersedia: <?= intval($produk['stok']) ?> item
                </p>

                <!-- Form -->
                <!-- Form -->
<form method="post">
    <!-- Jumlah -->
    <div class="flex items-center space-x-3 mb-6">
        <span class="text-gray-700 font-medium">Jumlah:</span>
        <div class="flex items-center border rounded overflow-hidden">
            <button type="button" id="minus" class="bg-gray-200 px-3 py-1 text-lg font-bold">−</button>
            <input type="number" id="qty" name="jumlah" value="1" 
                   min="1" max="<?= intval($produk['stok']) ?>" 
                   class="w-12 text-center border-x outline-none">
            <button type="button" id="plus" class="bg-gray-200 px-3 py-1 text-lg font-bold">+</button>
        </div>
    </div>

    <!-- Hidden input (dari kode lama) -->
    <input type="hidden" name="hidden_nama" value="<?= htmlspecialchars($produk['nama']) ?>">
    <input type="hidden" name="hidden_harga" value="<?= intval($produk['harga']) ?>">
    <input type="hidden" name="hidden_foto" value="<?= htmlspecialchars($produk['foto']) ?>">

    <!-- Tombol -->
    <div class="flex items-center gap-2 mt-3">
        <button type="submit" name="keranjang" 
                class="bg-orange-500 text-white px-4 py-2 rounded-lg">
            + Keranjang
        </button>
        <button type="submit" name="beli_sekarang" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            Beli Sekarang
        </button>
        <a href="index.php" 
           class="bg-gray-400 text-white px-4 py-2 rounded-lg">
           Kembali
        </a>
    </div>
</form>

            </div>
        </div>
    </div>

    <script>
        // Plus Minus Qty
        const qtyInput = document.getElementById("qty");
        const plusBtn = document.getElementById("plus");
        const minusBtn = document.getElementById("minus");
        const maxQty = parseInt(qtyInput.max);

        plusBtn.addEventListener("click", () => {
            let current = parseInt(qtyInput.value);
            if (current < maxQty) qtyInput.value = current + 1;
        });

        minusBtn.addEventListener("click", () => {
            let current = parseInt(qtyInput.value);
            if (current > 1) qtyInput.value = current - 1;
        });

        // SweetAlert jika sukses tambah ke keranjang
        <?php if ($show_alert): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Produk berhasil ditambahkan ke keranjang.',
            showConfirmButton: false,
            timer: 1500
        });
        <?php endif; ?>
    </script>
</body>
</html>
