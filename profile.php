<?php
session_start();
include 'config.php';

if (empty($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    echo "Data user tidak ditemukan!";
    exit();
}

// Hitung total transaksi
$stmt2 = $koneksi->prepare("SELECT COUNT(*) AS total FROM transaksi WHERE id_pelanggan = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$totalTransaksi = $result2->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
  --primary: #2563eb;
  --primary-light: #93c5fd;
  --dark: #1e293b;
  --light: #f8fafc;
  --success: #3b82f6;
  --danger: #ef4444;
  --border: 1px solid rgba(0, 0, 0, 0.08);
  --shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
  font-family: 'Poppins', sans-serif;
  background: #ffffff;
  color: var(--dark);
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 20px;
}

.wrapper {
  display: flex;
  background: white;
  border-radius: 16px;
  box-shadow: var(--shadow);
  overflow: hidden;
  width: 100%;
  max-width: 1100px;
  animation: fadeIn 0.6s ease;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background: #e0f2fe;
  padding: 25px 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sidebar a {
  text-decoration: none;
  color: var(--dark);
  font-weight: 500;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  border-radius: 10px;
  transition: all 0.3s ease;
}

.sidebar a i { width: 20px; text-align: center; color: var(--primary); }
.sidebar a:hover { background: var(--primary-light); color: white; transform: translateX(4px); }
.sidebar a:hover i { color: #fff; }
.sidebar a.active { background: var(--primary); color: white; }
.sidebar a.active i { color: #fff; }

/* Konten */
.content { flex: 1; padding: 40px 30px; text-align: center; }

.profile-avatar {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  border: 4px solid var(--primary);
  margin: 0 auto 20px;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: transform 0.3s ease;
}
.profile-avatar:hover { transform: scale(1.05); }
.profile-avatar img { width: 100%; height: 100%; object-fit: cover; }

h2 { font-size: 24px; font-weight: 600; margin-bottom: 8px; color: var(--primary); }
p { font-size: 15px; color: #555; margin-bottom: 8px; }

.info-card {
  background: #f9fafb;
  border: var(--border);
  border-radius: 12px;
  padding: 20px;
  margin: 20px auto;
  width: 100%;
  max-width: 500px;
  box-shadow: var(--shadow);
  text-align: left;
}

.info-card p {
  margin: 10px 0;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.info-card b { color: var(--dark); }
.info-card i { color: var(--primary); }

.stat-box {
  background: #dbeafe;
  border-radius: 12px;
  padding: 15px;
  margin: 15px auto;
  max-width: 300px;
  box-shadow: var(--shadow);
}
.stat-box p { margin: 0; font-weight: 600; color: var(--primary); }

.btn {
  padding: 10px 20px;
  background: linear-gradient(135deg, var(--primary), var(--success));
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  margin: 10px 5px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn:hover { background: linear-gradient(135deg, #1d4ed8, var(--primary)); transform: translateY(-2px); }
.btn-logout { background: var(--danger); }
.btn-logout:hover { background: #dc2626; }

footer { margin-top: 30px; font-size: 12px; color: #888; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

</head>
<body>

<div class="wrapper">
  
<!-- Sidebar -->
<div class="sidebar">
  <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali</a>
  <a href="riwayat_transaksi.php"><i class="fas fa-shopping-cart"></i>Riwayat Transaksi Pesanan</a>
  <a href="profile.php"><i class="fas fa-user"></i> Informasi Akun</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

<!-- Konten Profil -->
<div class="content">
  <div class="profile-avatar">
    <img src="img/<?= !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default-profile.jpg' ?>?v=<?= time() ?>" 
         alt="Foto Profil <?= htmlspecialchars($user['nama']) ?>">
  </div>

  <h2>Selamat Datang, <?= htmlspecialchars($user['nama']) ?></h2>
  <p>Anda bisa mengatur akun Anda dan melihat pesanan Anda di sini</p>

  <!-- Info Card -->
  <div class="info-card">
    <p><i class="fas fa-user"></i> <b>Nama Lengkap:</b> <?= htmlspecialchars($user['nama'] ?? '-') ?></p>
    <p><i class="fas fa-envelope"></i> <b>Email:</b> <?= htmlspecialchars($user['email'] ?? '-') ?></p>
    <p><i class="fas fa-phone"></i> <b>No. Telepon:</b> <?= htmlspecialchars($user['hp'] ?? '-') ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <b>Alamat:</b> <?= htmlspecialchars($user['alamat'] ?? '-') ?></p>
  </div>

  <!-- Statistik transaksi -->
  <div class="stat-box">
    <p><i class="fas fa-receipt"></i> Total Transaksi: <?= $totalTransaksi ?></p>
  </div>

  <!-- Tombol aksi -->
  <div>
    <button class="btn" onclick="window.location.href='edit_profil.php'">
      <i class="fas fa-pencil-alt"></i> Edit Profil
    </button>
    <button class="btn btn-logout" onclick="window.location.href='logout.php'">
      <i class="fas fa-sign-out-alt"></i> Logout
    </button>
  </div>
</div>
</div>

</body>
</html>
