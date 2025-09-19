<?php
session_start();
include 'config.php';

$user = null;

// Ambil data user kalau sudah login
if (isset($_SESSION['id_user'])) {
    $id = $_SESSION['id_user'];
    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
    $user = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donuts</title>
    <meta name="description" content="Temukan produk terbaik dengan harga terbaik di ShopHub">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .carousel-slide { transition: opacity 0.5s ease-in-out; }
        .carousel-slide.active { opacity: 1; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .category-tab.active { border-bottom: 3px solid #3b82f6; color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-50 font-sans">

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

            <!-- Profil / Login -->
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
    </div>
</nav>




        <!-- </a>
        <a href="logout.php" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
            Logout
        </a>
   -->
        <!-- Tombol login & daftar kalau belum login -->
        <!-- <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-300">
            Masuk
        </a>
        <a href="register.php" class="border border-blue-600 text-blue-600 hover:bg-blue-50 px-4 py-2 rounded-md font-medium transition duration-300">
            Daftar
        </a> -->

</div>

        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t pb-4">
            <div class="container mx-auto px-4 flex flex-col space-y-3">
                <a href="#" class="text-gray-700 hover:text-blue-600 py-2">Beranda</a>
                <a href="#products" class="text-gray-700 hover:text-blue-600 py-2">Produk</a>
                <a href="#" class="text-gray-700 hover:text-blue-600 py-2">Tentang Kami</a>
                <a href="#" class="text-gray-700 hover:text-blue-600 py-2">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Carousel -->
    <section class="relative overflow-hidden">
        <div class="carousel-container relative h-96 md:h-[32rem]">
            <!-- Slide 1 -->
            <div class="carousel-slide active absolute inset-0 w-full h-full flex items-center bg-gradient-to-r from-blue-50 to-indigo-100">
                <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Donat baru,mood baru!</h1>
                        <p class="text-lg text-gray-600 mb-6">>Diskon sampe 30%, buruan sebelum ludes! 🔥🍩</p>
                      <button onclick="window.location.href='produk.php'" 
    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium text-lg transition duration-300">
    Lihat Produk
</button>

                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <img src="https://i.pinimg.com/736x/d7/7b/2f/d77b2f668aaafb4c3ba8db970d36b4f8.jpg"  alt="" class="max-h-80">
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="carousel-slide absolute inset-0 w-full h-full flex items-center bg-gradient-to-r from-pink-50 to-purple-100 opacity-0">
                <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                      <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Produk Terbaru Kami</h1>
                      <p class="text-lg text-gray-600 mb-6">Dapatkan produk terbaru dengan desain trendi dan harga terbaik khusus untuk Anda.</p>
                    <button href="produk.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium text-lg transition duration-300">
                      Belanja Sekarang
                      </button>

                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <img src="https://i.pinimg.com/1200x/63/d6/97/63d6979df9b0a8106593162a4e52381b.jpg" alt="" class="max-h-80">
                    </div>
                </div>
            </div>
            
        <!-- Carousel Controls -->
        <button id="prev-btn" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 p-2 rounded-full shadow-md">
            <i class="fas fa-chevron-left text-blue-600 text-xl"></i>
        </button>
        <button id="next-btn" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-100 p-2 rounded-full shadow-md">
            <i class="fas fa-chevron-right text-blue-600 text-xl"></i>
        </button>
        
        <!-- Carousel Indicators -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <span class="carousel-indicator w-3 h-3 rounded-full bg-white bg-opacity-70 cursor-pointer active"></span>
            <span class="carousel-indicator w-3 h-3 rounded-full bg-white bg-opacity-70 cursor-pointer"></span>
            <span class="carousel-indicator w-3 h-3 rounded-full bg-white bg-opacity-70 cursor-pointer"></span>
        </div>
    </section>

   <!-- Featured Categories -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-2">Kategori</h2>
        <p class="text-gray-600 text-center max-w-2xl mx-auto mb-8">
            Pilih donat favoritmu sesuai kategori
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
            <!-- Donat Kentang -->
            <a href="kategori.php?kategori=donat-kentang" class="category-card group flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition duration-300">
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-yellow-200 transition duration-300">
                    <i class="fas fa-bread-slice text-yellow-600 text-3xl"></i>
                </div>
                <span class="font-medium text-gray-700">Donat Kentang</span>
            </a>

            <!-- Donat Madu -->
            <a href="kategori.php?kategori=donat-madu" class="category-card group flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition duration-300">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-amber-200 transition duration-300">
                    <i class="fas fa-cookie text-amber-600 text-3xl"></i>
                </div>
                <span class="font-medium text-gray-700">Donat Madu</span>
            </a>
        </div>
    </div>
</section>
           <section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-[#6B3F2D] mb-8">Produk Unggulan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            include 'config.php';
            $hasil = mysqli_query($koneksi, "SELECT * FROM produk");
            while($data = mysqli_fetch_array($hasil)) {
            ?>
            <!-- Product Card -->
            <div class="product-card bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg transition">
                <!-- Gambar -->
                <div class="w-full h-40 flex justify-center items-center overflow-hidden mb-4">
                    <img src="img/<?= $data['foto'] ?>" alt="<?= $data['nama'] ?>" class="h-full object-contain">
                </div>

                <!-- Nama Produk -->
                <h3 class="text-lg font-semibold text-[#6B3F2D] mb-1"><?= $data['nama'] ?></h3>

                <!-- Harga -->
                <p class="text-base font-bold text-blue-600 mb-2">
                    Rp <?= number_format($data['harga'], 0, ',', '.') ?>
                </p>

                <!-- Deskripsi -->
                <p class="text-sm text-gray-600 mb-4 line-clamp-2"><?= $data['deskripsi'] ?></p>

                <!-- Tombol -->
                <a href="detail.php?id=<?= $data['id'] ?>" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-full hover:bg-blue-700 transition duration-300">
                    Lihat Detail
                </a>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Pengiriman Cepat</h3>
                    <p class="text-gray-600">Pengiriman cepat dan dapat dilacak ke seluruh wilayah Indonesia</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Pembayaran Aman</h3>
                    <p class="text-gray-600">Sistem pembayaran yang aman dengan berbagai pilihan metode</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Tim customer service siap membantu Anda kapan saja</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimoni -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-[#000000] mb-8">Testimoni</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Debby -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-gray-600 mb-4">"Donatnya enak banget, lembut, dan toppingnya melimpah. Pasti bakal beli lagi!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">D</div>
                    <span class="ml-3 font-semibold text-[#000000]">Debby</span>
                </div>
            </div>
            <!-- Sela -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-gray-600 mb-4">"Pengiriman cepat, donat masih fresh. Rasanya bikin nagih!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center font-bold text-pink-600">S</div>
                    <span class="ml-3 font-semibold text-[#000000]">Sela</span>
                </div>
            </div>
            <!-- Nisa -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                <p class="text-gray-600 mb-4">"Harga terjangkau, rasa premium. Cocok buat oleh-oleh dan cemilan keluarga."</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-600">N</div>
                    <span class="ml-3 font-semibold text-[#000000]">Nisa</span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Jam Operasional Section -->
<section class="py-20 bg-white relative overflow-hidden">
  <!-- Decorative Elements -->
  <div class="absolute top-10 left-10 w-20 h-20 bg-orange-200 rounded-full opacity-50 animate-float"></div>
  <div class="absolute bottom-20 right-20 w-16 h-16 bg-yellow-200 rounded-full opacity-40 animate-float-delayed"></div>
  <div class="absolute top-1/2 left-1/4 w-12 h-12 bg-orange-300 rounded-full opacity-30 animate-pulse"></div>

<div class="container mx-auto px-6 relative z-10">
  <!-- Title with Steam Effect -->
  <div class="text-center mb-16">
    <div class="inline-block relative">
      <h2 class="text-5xl font-bold text-blue-700 mb-4 animate-fade-in-up">
        <i class="fas fa-clock text-4xl mr-3"></i>
        Jam Operasional
      </h2>
      <div class="absolute -top-2 -right-2 text-2xl animate-bounce">
        <i class="fas fa-fire text-blue-400"></i>
      </div>
    </div>
    <p class="text-lg text-blue-600 font-medium">Siap melayani Anda setiap hari!</p>
  </div>

<!-- Operating Hours Cards (Compact Version) -->
<div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
  
  <!-- Jam Buka Card -->
  <div class="group">
    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl border-l-4 border-green-400 relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-green-100 to-transparent rounded-full -mr-12 -mt-12"></div>
      
      <!-- Icon Area -->
      <div class="relative z-10 text-center mb-4">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-400 to-green-600 rounded-xl shadow-lg mb-2 group-hover:rotate-12 transition-transform duration-300">
          <i class="fas fa-sun text-2xl text-white"></i>
        </div>
        <h3 class="text-xl font-bold text-green-600 mb-1">Jam Buka</h3>
        <div class="w-12 h-1 bg-gradient-to-r from-green-400 to-green-600 mx-auto rounded-full"></div>
      </div>
      
      <!-- Time Display -->
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-800 mb-1">07:00</div>
        <div class="text-sm text-gray-600 font-medium">WIB</div>
        <div class="mt-2 text-xs text-green-600 font-medium">
          <i class="fas fa-coffee mr-1"></i>
          Sarapan Dimsum!
        </div>
      </div>
    </div>
  </div>

  <!-- Jam Tutup Card -->
  <div class="group">
    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl border-l-4 border-red-400 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-red-100 to-transparent rounded-full -mr-12 -mt-12"></div>
      
      <div class="relative z-10 text-center mb-4">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-red-400 to-red-600 rounded-xl shadow-lg mb-2 group-hover:rotate-12 transition-transform duration-300">
          <i class="fas fa-moon text-2xl text-white"></i>
        </div>
        <h3 class="text-xl font-bold text-red-600 mb-1">Jam Tutup</h3>
        <div class="w-12 h-1 bg-gradient-to-r from-red-400 to-red-600 mx-auto rounded-full"></div>
      </div>
      
      <div class="text-center">
        <div class="text-2xl font-bold text-gray-800 mb-1">20:00</div>
        <div class="text-sm text-gray-600 font-medium">WIB</div>
        <div class="mt-2 text-xs text-red-600 font-medium">
          <i class="fas fa-utensils mr-1"></i>
          Makan Malam!
        </div>
      </div>
    </div>
  </div>

  <!-- Lokasi Card -->
  <div class="group">
    <div class="bg-white rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl border-l-4 border-blue-400 relative overflow-hidden">
      <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-100 to-transparent rounded-full -mr-12 -mt-12"></div>
      
      <div class="relative z-10 text-center mb-4">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl shadow-lg mb-2 group-hover:rotate-12 transition-transform duration-300">
          <i class="fas fa-map-marker-alt text-2xl text-white"></i>
        </div>
        <h3 class="text-xl font-bold text-blue-600 mb-1">Lokasi Kami</h3>
        <div class="w-12 h-1 bg-gradient-to-r from-blue-400 to-blue-600 mx-auto rounded-full"></div>
      </div>
      
      <div class="text-center">
        <div class="text-sm font-bold text-gray-800 mb-1">Jl. jenral sudirman no. 49</div>
        <div class="text-xs text-gray-600 font-medium mb-2">Kota Kuliner</div>
        <div class="mt-2 text-xs text-blue-600 font-medium">
          <i class="fas fa-directions mr-1"></i>
          Google Maps Ready!
        </div>
      </div>
    </div>
  </div>

</div>

    <!-- Additional Info Banner -->
    <div class="mt-16 text-center">
      <div class="inline-block bg-white rounded-full px-8 py-4 shadow-lg border-2 border-orange-200">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-sm font-medium text-gray-700">Buka Setiap Hari</span>
          </div>
          <div class="w-px h-6 bg-gray-300"></div>
          <div class="flex items-center space-x-2">
            <i class="fas fa-phone text-orange-500"></i>
            <span class="text-sm font-medium text-gray-700">Call: (089) 6677-57755</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

    <!-- Call to Action -->
    <section class="py-16 bg-blue-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Bergabunglah dengan Komunitas Kami</h2>
            <p class="text-lg mb-8 max-w-2xl mx-auto">Dapatkan penawaran eksklusif dan update produk terbaru langsung ke email Anda</p>
            <div class="max-w-md mx-auto flex">
                <input type="email" placeholder="Alamat email Anda" class="flex-grow px-4 py-3 rounded-l-md focus:outline-none text-gray-800">
                <button class="bg-blue-800 hover:bg-blue-900 px-6 py-3 rounded-r-md font-medium transition duration-300">
                    Berlangganan
                </button>
            </div>
        </div>
    </section>
    <footer class="bg-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/a22df96e-7d8a-4063-a946-3da598b3038f.png" alt="ShopHub logo - minimalist shopping cart icon with blue gradient" class="h-10">
                        <span class="text-xl font-bold ml-2">Donuts</span>
                    </div>
                    <p class="text-gray-400 mb-4">donat disini selain berkualitas tapi memiliki rasa yang sangat enak.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-instagram"></i>
                        </a>
                         <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
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
                            <span>Jl. pramuka. kec,harjamukti kab,cirebon</span>
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
                <p>© 2025 donuts. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.carousel-indicator');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    let currentIndex = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            slide.style.opacity = "0";
            if (i === index) {
                slide.classList.add('active');
                slide.style.opacity = "1";
            }
        });

        indicators.forEach((dot, i) => {
            dot.classList.remove('active', 'bg-blue-600');
            dot.classList.add('bg-white');
            if (i === index) {
                dot.classList.add('active', 'bg-blue-600');
            }
        });

        currentIndex = index;
    }

    function nextSlide() {
        let newIndex = (currentIndex + 1) % slides.length;
        showSlide(newIndex);
    }

    function prevSlide() {
        let newIndex = (currentIndex - 1 + slides.length) % slides.length;
        showSlide(newIndex);
    }

    // Tombol Next/Prev
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);

    // Klik indikator
    indicators.forEach((dot, i) => {
        dot.addEventListener('click', () => showSlide(i));
    });

    // Auto slide tiap 5 detik
    setInterval(nextSlide, 5000);

    // Tampilkan slide pertama
    showSlide(0);
</script>

    <script>

        
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Carousel Functionality
        const carouselSlides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.carousel-indicator');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        let currentSlide = 0;
        
        function showSlide(index) {
            // Hide all slides
            carouselSlides.forEach(slide => {
                slide.classList.remove('active');
                slide.style.opacity = '0';
            });
            
            // Remove active class from all indicators
            indicators.forEach(indicator => {
                indicator.classList.remove('active');
            });
            
            // Show current slide
            carouselSlides[index].classList.add('active');
            setTimeout(() => {
                carouselSlides[index].style.opacity = '1';
            }, 10);
            
            // Set active indicator
            indicators[index].classList.add('active');
            currentSlide = index;
        }
        
        // Next slide
        function nextSlide() {
            currentSlide = (currentSlide + 1) % carouselSlides.length;
            showSlide(currentSlide);
        }
        
        // Previous slide
        function prevSlide() {
            currentSlide = (currentSlide - 1 + carouselSlides.length) % carouselSlides.length;
            showSlide(currentSlide);
        }
        
        // Event listeners
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);
        
        // Set up indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                showSlide(index);
            });
        });
        
        // Auto-advance slides every 5 seconds
        setInterval(nextSlide, 5000);
        
        // Category Tabs
        const categoryTabs = document.querySelectorAll('.category-tab');
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                categoryTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            });
        });
    </script>
</body>
</html>

