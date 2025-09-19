<?php
session_start();
include 'config.php';

// ======================= CEK USER LOGIN =======================
$user = null;
$username = '';
$profile_picture = 'img/default-profile.jpg';

if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];

    $stmt = $koneksi->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $username = $row['username'];
        $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'img/default-profile.jpg';
        $user = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tentang Kami - Donuts</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <style>
        /* Animasi */
        @keyframes bounce { 0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);} }
        @keyframes pulse { 0%,100%{transform:scale(1);}50%{transform:scale(1.1);} }
        @keyframes swing { 0%{transform:rotate(0deg);}25%{transform:rotate(5deg);}50%{transform:rotate(-5deg);}75%{transform:rotate(5deg);}100%{transform:rotate(0deg);} }

        .icon-bounce { animation: bounce 2s infinite; }
        .icon-pulse { animation: pulse 2s infinite; }
        .icon-swing { animation: swing 3s infinite; }
    </style>
</head>
<body class="bg-[#FFF8F2] text-[#6B3F2D] font-sans">

<!-- ======================= NAVBAR ======================= -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="https://i.pinimg.com/736x/7d/a5/e8/7da5e8d2640d92b429cbcfee78355263.jpg" 
                 alt="Logo Donuts" class="h-10 rounded-full">
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
    </div>
</nav>

<!-- ======================= HERO SECTION ======================= -->
<section class="bg-gradient-to-r from-[#DBEAFE] to-[#EFF6FF] py-16" data-aos="fade-down" data-aos-duration="1000">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-extrabold text-[#2563EB]">Tentang Donuts</h1>
        <p class="mt-4 text-lg text-[#6B3F2D]">Hadir dengan cita rasa manis istimewa & bahan berkualitas 🍩</p>
    </div>
</section>

<!-- ======================= ABOUT ======================= -->
<section class="py-20 bg-[#F0F9FF]">
    <div class="container mx-auto px-6 md:px-12 lg:px-20">
        <div class="flex flex-col md:flex-row items-center gap-10">
            <div class="md:w-1/2" data-aos="fade-right" data-aos-duration="1000">
                <img src="img/wlp donut.jpg.jpg" alt="Tentang Donuts" class="rounded-2xl shadow-lg hover:scale-105 transition-transform duration-300">
            </div>
            <div class="md:w-1/2 text-center md:text-left" data-aos="fade-left" data-aos-duration="1000">
                <h2 class="text-3xl md:text-4xl font-bold text-[#1E40AF] mb-4">Tentang Kami</h2>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Selamat datang di <span class="font-semibold text-[#2563EB]">Donuts</span>!  
                    Kami adalah UMKM lokal yang menghadirkan donat lezat dengan bahan berkualitas, 
                    resep autentik, dan penuh cinta. Setiap gigitan adalah perpaduan sempurna rasa manis 
                    dan lembut, dibuat segar setiap hari untuk kamu yang istimewa.
                </p>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Kami percaya, makanan bukan hanya sekadar pengisi perut, tetapi juga penghangat hati 
                    dan penghubung cerita. Yuk, rasakan kelezatan donat kami dan dukung usaha lokal!
                </p>
                <p class="text-gray-700 leading-relaxed mb-6">
                    Saat ini, kami menghadirkan dua varian unggulan:  
                    <span class="font-semibold text-[#2563EB]">Donat Kentang</span> dengan tekstur lembut, padat namun empuk, 
                    dan <span class="font-semibold text-[#2563EB]">Donat Madu</span> yang manis alami dan harum.  
                    Dua varian ini siap memanjakan lidahmu dengan rasa istimewa!
                </p>
                <a href="produk.php" 
                   class="inline-block px-6 py-3 bg-[#3B82F6] text-white font-semibold rounded-full shadow-md transition-transform duration-300 transform hover:scale-105 hover:shadow-xl hover:animate-bounce">
                    Lihat Produk Kami 🍩
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ======================= CERITA KAMI ======================= -->
<section class="py-16 bg-gradient-to-b from-[#DBEAFE] to-[#EFF6FF]" data-aos="fade-up" data-aos-duration="1000">
    <div class="container mx-auto px-6">
        <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 text-center md:text-left">
            <h2 class="text-4xl font-bold text-[#2563EB] mb-6">🍩 Cerita Kami</h2>
            <p class="text-gray-700 leading-relaxed text-lg mb-4">
                Berawal dari kecintaan kami terhadap donat, 
                <span class="font-semibold text-[#2563EB]">Donuts</span> lahir di dapur kecil di Cirebon 
                dengan tekad menghadirkan rasa yang berbeda. Setiap orang merasakan donat yang bukan hanya enak, 
                tetapi juga dibuat dengan <span class="font-semibold text-[#1E40AF]">bahan alami</span> dan penuh kasih.
            </p>
            <p class="text-gray-700 leading-relaxed text-lg">
                Setiap gigitan adalah cerita tentang kehangatan, ketulusan, 
                dan keinginan kami untuk selalu memberikan yang terbaik. 
                Donat bukan sekadar makanan, melainkan cara untuk menghadirkan 
                <span class="font-semibold text-[#1E40AF]">kebahagiaan kecil</span> di setiap momen Anda.
            </p>
        </div>
    </div>
</section>

<!-- ======================= VISI & MISI ======================= -->
<section class="py-16 container mx-auto px-6 grid md:grid-cols-2 gap-10">
    <div class="bg-white rounded-3xl p-8 shadow-lg border border-blue-100 hover:shadow-2xl transition transform hover:-translate-y-2" data-aos="fade-right" data-aos-duration="800">
        <h3 class="text-3xl font-bold text-[#2563EB] mb-4 flex items-center gap-2">🎯 Visi</h3>
        <p class="text-gray-700 leading-relaxed">
            Menjadi <span class="font-semibold text-[#1E40AF]">UMKM Donat</span> pilihan utama 
            yang menghadirkan kebahagiaan & kehangatan melalui 
            <span class="text-[#3B82F6]">Donat Kentang</span> dan 
            <span class="text-[#3B82F6]">Donat Madu</span> ke seluruh Indonesia.
        </p>
    </div>

    <div class="bg-white rounded-3xl p-8 shadow-lg border border-blue-100 hover:shadow-2xl transition transform hover:-translate-y-2" data-aos="fade-left" data-aos-duration="800">
        <h3 class="text-3xl font-bold text-[#2563EB] mb-4 flex items-center gap-2">💡 Misi</h3>
        <ul class="list-disc list-inside space-y-2 text-gray-700 leading-relaxed">
            <li>Menghadirkan donat sehat dengan bahan alami dan segar</li>
            <li>Menyajikan varian spesial: <span class="text-[#3B82F6] font-semibold">Donat Kentang</span> & <span class="text-[#3B82F6] font-semibold">Donat Madu</span></li>
            <li>Mendukung pertumbuhan <span class="text-[#1E40AF] font-semibold">UMKM lokal</span> dengan kualitas terbaik</li>
            <li>Memberikan pelayanan ramah dan penuh kehangatan</li>
        </ul>
    </div>
</section>

<!-- ======================= KENAPA PILIH DONUTS ======================= -->
<section class="py-12 container mx-auto px-4">
    <h2 class="text-3xl font-bold text-center text-[#2563EB] mb-8" data-aos="fade-up" data-aos-duration="800">Kenapa Pilih Donuts?</h2>
    <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-3xl shadow-lg text-center hover:shadow-2xl transition transform hover:-translate-y-2" data-aos="zoom-in" data-aos-delay="100">
            <i class="fas fa-utensils text-4xl text-[#3B82F6] mb-3 icon-bounce"></i>
            <h4 class="font-bold text-[#1E40AF] mb-2">Varian Spesial</h4>
            <p class="text-gray-600">Donat Kentang lembut & Donat Madu manis alami, bikin nagih di setiap gigitan.</p>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-lg text-center hover:shadow-2xl transition transform hover:-translate-y-2" data-aos="zoom-in" data-aos-delay="200">
            <i class="fas fa-seedling text-4xl text-[#60A5FA] mb-3 icon-pulse"></i>
            <h4 class="font-bold text-[#1E40AF] mb-2">Bahan Berkualitas</h4>
            <p class="text-gray-600">Dari bahan segar tanpa pengawet, menjaga rasa otentik dan sehat.</p>
        </div>
        <div class="bg-white p-6 rounded-3xl shadow-lg text-center hover:shadow-2xl transition transform hover:-translate-y-2" data-aos="zoom-in" data-aos-delay="300">
            <i class="fas fa-smile-beam text-4xl text-[#93C5FD] mb-3 icon-swing"></i>
            <h4 class="font-bold text-[#1E40AF] mb-2">Bahagia Setiap Gigitan</h4>
            <p class="text-gray-600">Manis, lembut, penuh cinta — sempurna untuk momen istimewamu.</p>
        </div>
    </div>
</section>

<!-- ======================= CTA ======================= -->
<section class="bg-gradient-to-r from-[#2563EB] to-[#1D4ED8] py-12 text-white text-center" data-aos="fade-up" data-aos-duration="1000">
    <h2 class="text-3xl font-bold mb-4">Siap Menikmati Donuts Yummy?</h2>
    <p class="mb-6">Pesan sekarang dan rasakan kelezatannya di rumah Anda!</p>
    <a href="produk.php" 
       class="bg-white text-[#2563EB] px-6 py-3 rounded-full font-bold shadow-lg transition-transform duration-300 transform hover:scale-105 hover:shadow-2xl hover:bg-[#EFF6FF] hover:animate-bounce">
        Pesan Sekarang
    </a>
</section>

<!-- ======================= FOOTER ======================= -->
<footer class="bg-[#1E3A8A] text-white py-6 text-center">
    <p>© 2025 Donuts. All rights reserved.</p>
</footer>

<script>
    AOS.init();
</script>
</body>
</html>
