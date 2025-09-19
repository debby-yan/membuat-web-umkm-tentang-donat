<?php
session_start();
include 'config.php';

// ======================= CEK USER LOGIN =======================
$user = null;

if (isset($_SESSION['id_user'])) {
    $id = $_SESSION['id_user'];
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
    $user = mysqli_fetch_assoc($res);
}

// Ambil data user jika login dengan user_id
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmtUser = $koneksi->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();
    $resUser = $stmtUser->get_result();
    $user = $resUser->fetch_assoc();
    $stmtUser->close();
}

// ======================= AMBIL SEMUA KATEGORI =======================
$mapKategori = [];
$kategoriQuery = $koneksi->query("SELECT id_kategori, nama_kategori FROM kategori");
while ($row = $kategoriQuery->fetch_assoc()) {
    $mapKategori[$row['id_kategori']] = $row['nama_kategori'];
}

// ======================= AMBIL PARAMETER KATEGORI =======================
$kategoriId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ======================= QUERY PRODUK =======================
if ($kategoriId > 0 && isset($mapKategori[$kategoriId])) {
    $kategoriDb = $mapKategori[$kategoriId];

    $stmt = $koneksi->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
        WHERE p.id_kategori = ?
    ");
    $stmt->bind_param("i", $kategoriId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Jika tidak ada parameter kategori, tampilkan semua produk
    $kategoriDb = "Semua Produk";
    $result = $koneksi->query("
        SELECT p.*, k.nama_kategori 
        FROM produk p
        JOIN kategori k ON p.id_kategori = k.id_kategori
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori: <?= htmlspecialchars($kategoriDb) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<!-- Navigation Bar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="https://i.pinimg.com/736x/7d/a5/e8/7da5e8d2640d92b429cbcfee78355263.jpg" alt="Logo" class="h-10">
            <span class="text-xl font-bold text-blue-600">Donuts</span>
        </div>

        <!-- Menu Links -->
        <div class="hidden md:flex space-x-6">
            <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
            <a href="produk.php" class="text-gray-700 hover:text-blue-600 font-medium">Produk</a>
            <a href="kategori.php" class="text-gray-700 hover:text-blue-600 font-medium">Kategori</a>
            <a href="tentang.php" class="text-gray-700 hover:text-blue-600 font-medium">Tentang Kami</a>
            <a href="kontak.php" class="text-gray-700 hover:text-blue-600 font-medium">Kontak</a>
            <?php foreach ($mapKategori as $id => $nama): ?>
                <a href="kategori.php?id=<?= $id ?>" 
                   class="text-gray-700 hover:text-blue-600 font-medium">
                    <?= htmlspecialchars($nama) ?>
                </a>
            <?php endforeach; ?>
        </div>

<!-- User & Cart -->
<div class="flex items-center space-x-3">
    <a href="keranjang.php" class="relative inline-block">
        <i class="fa-solid fa-cart-shopping text-2xl text-gray-700"></i>
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php $cart_count = array_sum($_SESSION['cart']); ?>
            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                <?= $cart_count ?>
            </span>
        <?php endif; ?>
    </a>
 <!-- User / Login -->
            <?php if (!empty($user)): ?>
                <a href="profile.php" class="flex items-center space-x-2 hover:text-blue-600">
                    <img src="img/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>" 
                         alt="Profile" class="h-8 w-8 rounded-full object-cover">
                    <span><?= htmlspecialchars($user['username']) ?></span>
                </a>
            <?php else: ?>
                <a href="login.php" class="hover:text-blue-600">
                    <i class="fas fa-user"></i> Login  
                </a>
            <?php endif; ?>
</div>


    </nav>

<!-- Konten -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <!-- Judul -->
        <h2 class="text-2xl md:text-3xl font-bold text-center text-[#6B3F2D] mb-8">
            Kategori: <?= htmlspecialchars($kategoriDb) ?>
        </h2>

        <!-- Grid Produk -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition p-4 flex flex-col items-center">
                        <img src="img/<?= htmlspecialchars($row['foto']) ?>" 
                             alt="<?= htmlspecialchars($row['nama']) ?>" 
                             class="w-40 h-40 object-contain mb-4">
                        <div class="text-center">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <?= htmlspecialchars($row['nama']) ?>
                            </h2>
                            <p class="text-gray-700 mb-2">
                                Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </p>
                            <a href="detail.php?id=<?= $row['id'] ?>" 
                               class="mt-3 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center col-span-4 text-gray-500">Produk tidak ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-white mt-12 border-t">
    <div class="container mx-auto px-4 py-6 text-center text-gray-600">
        &copy; <?= date('Y') ?> DonatKu. Semua Hak Dilindungi.
    </div>
</footer>

</body>
</html>
