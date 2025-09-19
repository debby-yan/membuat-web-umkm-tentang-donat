<?php
include 'config.php';
session_start();

// Default data user
$user = null;
$username = '';
$profile_picture = 'img/default-profile.jpg';

// Jika user sudah login
if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];

    // Ambil data user dari database
    $stmt = $koneksi->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user = $row;
        $username = $row['username'];
        $profile_picture = $row['profile_picture'] ?: 'img/default-profile.jpg';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Donuts</title>
    <meta name="description" content="Temukan produk donat terbaik dengan harga terjangkau di Donuts">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">

<!-- Navigation Bar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="https://i.pinimg.com/736x/7d/a5/e8/7da5e8d2640d92b429cbcfee78355263.jpg" 
                 alt="Logo Donuts" 
                 class="h-10 rounded-full">
            <span class="text-xl font-bold text-blue-600">Donuts</span>
        </div>

        <!-- Menu Links -->
        <div class="hidden md:flex space-x-6">
            <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
            <a href="produk.php" class="text-gray-700 hover:text-blue-600 font-medium">Produk</a>
            <a href="kategori.php" class="text-gray-700 hover:text-blue-600 font-medium">Kategori</a>
            <a href="tentang.php" class="text-gray-700 hover:text-blue-600 font-medium">Tentang Kami</a>
            <a href="kontak.php" class="text-gray-700 hover:text-blue-600 font-medium">Kontak</a>
        </div>

        <!-- Right Side -->
        <div class="flex items-center space-x-3">
            
            <!-- Keranjang -->
            <a href="keranjang.php" class="relative inline-block">
                <i class="fa-solid fa-cart-shopping text-2xl text-gray-700"></i>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php $cart_count = array_sum($_SESSION['cart']); ?>
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-0.5">
                        <?= $cart_count ?>
                    </span>
                <?php endif; ?>
            </a>

          <?php if (!empty($user)): ?>
    <!-- Profil kalau login -->
    <a href="profile.php" class="flex items-center space-x-2 hover:text-blue-600">
        <img src="img/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>" 
             alt="Profile" 
             class="h-8 w-8 rounded-full object-cover">
        <span><?= htmlspecialchars($user['username']) ?></span>
    </a>
<?php else: ?>
    <!-- Tombol login kalau belum login -->
    <a href="login.php" class="hover:text-blue-600">
        <i class="fas fa-user"></i> Login  
    </a>
<?php endif; ?>

        </div>
    </div>
</nav>

<!-- Product Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-[#6B3F2D] mb-8">Produk Kami</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            $hasil = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC");
            while($data = mysqli_fetch_array($hasil)) {
            ?>
            <!-- Product Card -->
            <div class="product-card bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg transition">
                <!-- Gambar -->
                <div class="w-full h-40 flex justify-center items-center overflow-hidden mb-4">
                    <img src="img/<?= htmlspecialchars($data['foto']) ?>" 
                         alt="<?= htmlspecialchars($data['nama']) ?>" 
                         class="h-full object-contain">
                </div>

                <!-- Nama Produk -->
                <h3 class="text-lg font-semibold text-[#6B3F2D] mb-1"><?= htmlspecialchars($data['nama']) ?></h3>

                <!-- Harga -->
                <p class="text-base font-bold text-blue-600 mb-2">
                    Rp <?= number_format($data['harga'], 0, ',', '.') ?>
                </p>

                <!-- Deskripsi -->
                <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?= htmlspecialchars($data['deskripsi']) ?></p>
<!-- Tombol Aksi -->
<div class="flex justify-center space-x-3 mt-3">
    <!-- Tombol Lihat Detail -->
    <a href="detail.php?id=<?= $data['id'] ?>" class="flex items-center justify-center w-9 h-9 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition">
       <i class="fa fa-eye"></i>
    </a>

    <!-- Tombol Add to Cart -->
    <button type="button" class="add-to-cart flex items-center justify-center w-9 h-9 rounded-full bg-pink-100 text-pink-600 hover:bg-pink-200 transition"
        data-id="<?= $data['id'] ?>">
        <i class="fa fa-cart-plus"></i>
    </button>
</div>
  </div>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-white pt-12 pb-6">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center mb-4">
                    <img src="https://i.pinimg.com/736x/7d/a5/e8/7da5e8d2640d92b429cbcfee78355263.jpg" alt="logo" class="h-10">
                    <span class="text-xl font-bold ml-2">Donuts</span>
                </div>
                <p class="text-gray-400 mb-4">Donat di sini selain berkualitas juga memiliki rasa yang sangat enak.</p>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-4">Tautan Cepat</h3>
                <ul class="space-y-2">
                    <li><a href="tentang.php" class="text-gray-400 hover:text-white">Tentang Kami</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Blog</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-4">Pelanggan</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Akun Saya</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Pesanan</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Pengembalian</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Bantuan</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-4">Hubungi Kami</h3>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2"></i>
                        <span>Jl. Pramuka, Kec. Harjamukti, Kab. Cirebon</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-phone-alt mr-2"></i>
                        <span>+62 896 677 57755</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>donat@gmail.com</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400">
            <p>© <?= date('Y') ?> Donuts. Semua hak dilindungi.</p>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $(".add-to-cart").click(function(){
        let id = $(this).data("id");

        $.post("tambah_keranjang.php", {id:id}, function(response){
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Produk berhasil ditambahkan ke keranjang!',
                showCancelButton: true,
                confirmButtonText: 'Lihat Keranjang',
                cancelButtonText: 'Lanjut Belanja'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "keranjang.php";
                }
            });
        });
    });
});
</script>

</body>
</html>
